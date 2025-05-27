document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/user_status.php')
  .then(r => r.json())
  .then(data => {
    userStatus = data;
  })
  .catch(() => {
    userStatus = { logged_in: false };
  });

  // — Références DOM —
  const startScreen     = document.getElementById('start-screen');
  const startBtn        = document.getElementById('start-btn');
  const gameContainer   = document.querySelector('.game-container');
  const resultContainer = document.getElementById('result-container');
  const restartBtn      = document.getElementById('restart-btn');

  const timerDisplay    = document.getElementById('timer');
  const emojiDisplay    = document.getElementById('emoji-display');
  const form            = document.getElementById('guess-form');
  const input           = document.getElementById('guess-input');

  const scoreDisplay    = document.getElementById('score-display');
  const resultsList     = document.getElementById('results');
  const pseudoForm      = document.getElementById('pseudo-form');
  const saveScoreBtn    = document.getElementById('save-score-btn');
  const leaderboardBtn  = document.getElementById('leaderboard-btn');

  // — Variables de jeu —
  let userStatus = { logged_in: false, pseudo: null, user_id: null };

  let games = [], currentIndex = 0, results = [];
  let timerInterval, timerTimeout, endTimestamp;
  const TIME_PER_GAME_MS = 10 * 1000; // 10 secondes en millisecondes

  // désactive jusqu'à chargement
  startBtn.disabled = true;
  fetch('emoji_games.json')
  .then(r => r.json())
  .then(data => {
    games = shuffleArray(data).slice(0, 10);
    startBtn.disabled = false; // rendre jouable après chargement
  })
  .catch(err => {
    console.error(err);
    alert('Erreur de chargement des jeux.');
  });

// Fonction de mélange (shuffle)
function shuffleArray(array) {
  return array.sort(() => Math.random() - 0.5);
}


  // — Démarrage du jeu —
  startBtn.addEventListener('click', () => {
  // Cache bien le header quand on commence à jouer
const header = document.querySelector('header');
if (header) {
  header.classList.remove('visible');
}


  // tracking quotidien
  fetch('/admin/update_stats.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'game=devine_emoji'
  }).catch(() => {});

  startScreen.classList.add('hidden');
  gameContainer.classList.remove('hidden');
  loadGame();
});


  // Soumission / Rejouer
  form.addEventListener('submit', e => {
    e.preventDefault();
    submitAnswer(false);
  });
  restartBtn.addEventListener('click', () => location.reload());

  function loadGame() {
    const g = games[currentIndex];
    emojiDisplay.textContent = g.emojis.join(' ');
    input.value = '';
    input.focus();
    startTimer();
  }

  function startTimer() {
    // calcule le timestamp de fin
    endTimestamp = Date.now() + TIME_PER_GAME_MS;
    updateTimer();
    clearInterval(timerInterval);
    clearTimeout(timerTimeout);
    // rafraîchissement toutes les 50 ms pour centièmes
    timerInterval = setInterval(updateTimer, 50);
    timerTimeout  = setTimeout(() => submitAnswer(true), TIME_PER_GAME_MS);
  }

  function updateTimer() {
    const rem = Math.max(0, endTimestamp - Date.now());
    const s   = Math.floor(rem / 1000);
    const cs  = Math.floor((rem % 1000) / 10);
    // affiche "S.CC"
    timerDisplay.textContent = `${s}.${cs.toString().padStart(2,'0')}`;
    // change la couleur selon le pourcentage restant
    const frac = rem / TIME_PER_GAME_MS;
    if      (frac >= 0.7) timerDisplay.style.color = '#1de9b6';
    else if (frac >= 0.4) timerDisplay.style.color = '#ffb74d';
    else                  timerDisplay.style.color = '#f44336';
  }


function normalize(str) {
  return str
    .toLowerCase()
    .normalize('NFD') // décompose les accents
    .replace(/[\u0300-\u036f]/g, '') // supprime les accents
    .replace(/\s+/g, ''); // supprime tous les espaces
}
  
  function submitAnswer(timeout) {
    clearInterval(timerInterval);
    clearTimeout(timerTimeout);

    const remMs     = Math.max(0, endTimestamp - Date.now());
    const g         = games[currentIndex];
const userGuess = normalize(input.value);
const correct   = normalize(g.answer);
const aliases   = Array.isArray(g.aliases)
  ? g.aliases.map(a => normalize(a))
  : [];

    let status, points = 0;
    if (!timeout && (userGuess === correct || aliases.includes(userGuess))) {
      status = '✅';
      // points proportionnels au temps restant
      points = Math.ceil((remMs / TIME_PER_GAME_MS) * 100);
    } else {
      status = '❌';
      points = 0;
    }

    results.push({
      emojis:    g.emojis,
      answer:    g.answer,
      poster:    g.poster,
      userGuess,
      status,
      points
    });

    currentIndex++;
    if (currentIndex < games.length) {
      loadGame();
    } else {
      showResults();
    }
  }


      
function showResults() {
const header = document.querySelector('header');
  if (header) {
    header.classList.remove('hidden-header'); // ← Ajoute une classe pour l'afficher joliment
header.classList.add('visible');

  }

  // (le reste de ta fonction showResults reste pareil)




  // 1) Cacher le jeu et afficher les résultats
  gameContainer.classList.add('hidden');
  resultContainer.classList.remove('hidden');

  // 2) Calcul du score et affichage
  const correctCount = results.filter(r => r.status === '✅').length;
  const totalPoints  = results.reduce((sum, r) => sum + r.points, 0);
  scoreDisplay.textContent =
    `Score : ${correctCount} / ${results.length} — Points : ${totalPoints}`;
    
    if (userStatus.logged_in) {
  fetch('/api/get_best_score.php?game=devine_emoji') // ou devine_emoji
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        const bestScore = document.createElement('p');
        bestScore.innerHTML = `Votre meilleur score : <strong>${data.best}</strong> pts`;
        scoreDisplay.appendChild(bestScore);
      }
    })
    .catch(console.error);
}

  // ==== à placer DANS showResults(), juste après le calcul de score/pagination ====
fetch('/api/record_session.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: new URLSearchParams({
    game:        'devine_emoji',      // ex. 'devine_affiche' ou 'devine_emoji'
    score:       totalPoints,
    correct:     correctCount,
    duration_ms: TIME_PER_GAME_MS - (endTimestamp - Date.now())
  })
})
  .then(r => r.json())
  .then(data => {
    if (data.new_achievements && data.new_achievements.length) {
      // Crée un petit encart au début de resultContainer
      const box = document.createElement('div');
      box.className = 'result-new-achievement';
      box.innerHTML = `
        <p><strong>Nouvel achievement débloqué ! 🎉</strong></p>
        <button id="view-achievements-btn">Voir les achievements</button>
      `;
      // Insère avant la liste des résultats
      resultContainer.insertBefore(box, resultsList);

      // écouteur pour rediriger
      box
        .querySelector('#view-achievements-btn')
        .addEventListener('click', () => {
          window.location.href = '/mon_compte.php';
        });
    }
  })
  .catch(console.error);
// ==== fin de la modif ====

  // 3) Listing des manches
  resultsList.innerHTML = results.map((r, i) => {
    const n = i + 1, tot = results.length;
    if (r.status === '✅') {
      return `
        <div class="result-item">
          <div class="emojis">${r.emojis.join(' ')}</div>
          <h3>${n} / ${tot} — ${r.answer} <span class="status-icon">✅</span></h3>
          <img src="${r.poster}" alt="${r.answer}">
          <p class="points">${r.points} pts</p>
        </div>`;
    } else {
      return `
        <div class="result-item">
          <div class="emojis">${r.emojis.join(' ')}</div>
          <h3>${n} / ${tot} — ${r.userGuess} <span class="status-icon">❌</span></h3>
          <img src="https://polcorn.com/devine-le-film-emoji/${r.poster}" alt="Affiche floutée"   style="filter: blur(8px); pointer-events: none; user-drag: none; user-select: none;" 
  draggable="false" 
  oncontextmenu="return false;">
          <p class="points">0 pt</p>
        </div>`;
    }
  }).join('');

  // 4) Affiche formulaire pseudo + bouton leaderboard
  leaderboardBtn.classList.remove('hidden');
leaderboardBtn.addEventListener('click', () => {
  window.location.href = '/devine-le-film-emoji/leaderboard.php';
}, { once: true });

// Si connecté → auto-save du score
if (userStatus.logged_in) {
  fetch('/api/record_score.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: new URLSearchParams({
    game: 'devine_emoji', // ou devine_emoji
    score: totalPoints,
    correct: correctCount,
    duration_ms: TIME_PER_GAME_MS - (endTimestamp - Date.now())
  })
})

  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      // Optionnel : afficher un message genre "Score enregistré !"
    }
  })
  .catch(console.error);
} else {
// Pas connecté → propose d'ouvrir la pop-up de connexion
pseudoForm.classList.remove('hidden');
pseudoForm.innerHTML = `
  <p>Connectez-vous pour enregistrer votre score :</p>
  <button id="open-login-popup" class="account-btn">Se connecter</button>
`;

document.getElementById('open-login-popup').addEventListener('click', () => {
  const loginPopupTrigger = document.getElementById('auth-btn');
  if (loginPopupTrigger) {
    loginPopupTrigger.click();
  } else {
    alert('Erreur : impossible d’ouvrir la connexion.');
  }
});


}


  // 6) Gestion des achievements
  const achievements = [
    {
      code: 'fast_answer',
      name: 'Réponse éclair',
      desc: 'Trouvez un film en moins de 3 secondes',
      check: () => results.some(r => r.points >= 70)
    },
    {
      code: 'perfect_score',
      name: 'Score parfait',
      desc: 'Obtenez un 10/10',
      check: () => correctCount === results.length
    },
    {
      code: '500_points',
      name: '500+ points',
      desc: 'Cumulez au moins 500 points',
      check: () => totalPoints >= 500
    }
  ];

  achievements.forEach(a => {
    if (a.check() && !localStorage.getItem(a.code)) {
      localStorage.setItem(a.code, '1');
      showAchievementNotif(a.name, a.desc);
    }
  });
}

// Petite fonction utilitaire pour afficher une notification
function showAchievementNotif(title, description) {
  const notif = document.createElement('div');
  notif.className = 'achievement-notif';
  notif.innerHTML = `<strong>${title}</strong><p>${description}</p>`;
  document.body.appendChild(notif);

  // Animation CSS via classe
  setTimeout(() => notif.classList.add('visible'), 50);
  // Disparition après 3 s + fade out
  setTimeout(() => notif.classList.remove('visible'), 3050);
  setTimeout(() => notif.remove(), 3500);
}

});
