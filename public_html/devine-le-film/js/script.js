document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/user_status.php')
  .then(r => r.json())
  .then(data => {
    userStatus = data;
  })
  .catch(() => {
    userStatus = { logged_in: false };
  });

  const startScreen     = document.getElementById('start-screen');
  const startBtn        = document.getElementById('start-btn');
  const gameContainer   = document.querySelector('.game-container');
  const posterWrapper   = document.getElementById('poster-wrapper');
  const posterImg       = document.getElementById('movie-poster');
  const form            = document.getElementById('guess-form');
  const input           = document.getElementById('movie-input');
  const timerDisplay    = document.getElementById('timer');
  const resultContainer = document.querySelector('.result-container');
  const resultsList     = document.getElementById('results');
  const scoreDisplay    = document.getElementById('score');
  const restartBtn      = document.getElementById('restart-btn');
  const pseudoForm      = document.getElementById('pseudo-form');
 const saveScoreBtn    = document.getElementById('save-score-btn');
const leaderboardBtn  = document.getElementById('leaderboard-btn');
  let lastScore = 0;
let userStatus = { logged_in: false, pseudo: null, user_id: null };

  let posters = [], currentIndex = 0, results = [];
  let timerInterval, timerTimeout, endTimestamp;
const TIME_PER_GAME_MS = 10 * 1000; // 10 secondes en millisecondes


  // Démarrage
  startBtn.addEventListener('click', () => {
    startBtn.classList.add('fade-zoom');
    setTimeout(() => {
      startScreen.classList.add('hidden');
      gameContainer.classList.remove('hidden');
      loadPoster();
    }, 500);
  });

  // Soumission
  form.addEventListener('submit', e => {
    e.preventDefault();
    
    
    
    input.blur();              // ⬅️ retire le focus activement
  input.style.display = 'none';  // ⬅️ cache l'input
    checkAnswer();
  });
  restartBtn.addEventListener('click', () => location.reload());

  // Chargement JSON
  fetch('films.json')
  .then(r => r.json())
  .then(data => {
    posters = shuffleArray(data).slice(0, 10);
    startBtn.disabled = false; // rendre jouable après chargement
  })
  .catch(err => {
    console.error(err);
    alert('Erreur de chargement des affiches.');
  });

// Fonction de mélange (shuffle)
function shuffleArray(array) {
  return array.sort(() => Math.random() - 0.5);
}


function loadPoster() {
  clearInterval(timerInterval);
  clearTimeout(timerTimeout);

  posterImg.src = posters[currentIndex].before;

  // remet l'input après un petit délai (pour laisser le clavier se fermer)
  setTimeout(() => {
    input.style.display = 'block';
    input.value = '';
    input.focus();
  }, 150); // ⬅️ délai court mais suffisant pour fermer le clavier

  startTimer();
}


  function checkAnswer(timeout = false) {
    clearInterval(timerInterval);
    clearTimeout(timerTimeout);

    const userAns = input.value.trim().toLowerCase();
    const item    = posters[currentIndex];
 
window.scrollTo({ top: 0, behavior: 'smooth' });

    const correct = item.answer.trim().toLowerCase();

    // récupère aliases s'ils existent, sinon tableau vide
    const aliases = Array.isArray(item.aliases)
      ? item.aliases.map(a => a.trim().toLowerCase())
      : [];

    let status, points = 0;
// calcule le temps restant en ms
const remMs = Math.max(0, endTimestamp - Date.now());
// valide si exact match sur answer ou un alias
if (!timeout && (userAns === correct || aliases.includes(userAns))) {
  status = '✅';
  // points proportionnels au temps restant
  points = Math.ceil((remMs / TIME_PER_GAME_MS) * 100);
} else {
  status = '❌';
  points = 0;
}


    results.push({
      status,
      posterAfter:  item.after,
      posterBefore: item.before,
      answer:       item.answer,
      userGuess:    userAns,
      points
    });

    currentIndex++;
    if (currentIndex < posters.length) {
      loadPoster();
    } else {
      showResults();
    }
  }

  function startTimer() {
  // calcule le timestamp de fin
  endTimestamp = Date.now() + TIME_PER_GAME_MS;
  // affichage immédiat
  updateTimer();
  clearInterval(timerInterval);
  clearTimeout(timerTimeout);
  // rafraîchissement toutes les 50 ms pour centièmes
  timerInterval = setInterval(updateTimer, 50);
  // timeout au bout de TIME_PER_GAME_MS
  timerTimeout  = setTimeout(() => checkAnswer(true), TIME_PER_GAME_MS);

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


      
function showResults() {
    
    document.querySelectorAll('.homemade-slider').forEach(slider => {
  const overlay = slider.querySelector('.slider-overlay');
  const after   = slider.querySelector('.slider-after');

  slider.addEventListener('mousemove', (e) => {
    const rect = slider.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
    after.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
  });

  slider.addEventListener('mouseleave', () => {
    after.style.clipPath = `inset(0 100% 0 0)`;
  });
});

  // Affiche le header seulement maintenant
  const header = document.querySelector('header');
  if (header) {
    header.classList.remove('hidden-header'); // ← Ajoute une classe pour l'afficher joliment
header.classList.add('visible');

  }

  // 1) Cacher le jeu et afficher les résultats
  gameContainer.classList.add('hidden');
  resultContainer.classList.remove('hidden');

  // 2) Calcul du score et affichage
  const correctCount = results.filter(r => r.status === '✅').length;
  const totalPoints  = results.reduce((sum, r) => sum + r.points, 0);
  scoreDisplay.innerHTML = `
  Score : ${correctCount} / ${results.length} — Points : ${totalPoints}
  <br>
  <span id="best-score"></span>
`;

if (userStatus.logged_in) {
  fetch('/api/get_best_score.php?game=devine_affiche')
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        const bestScoreSpan = document.getElementById('best-score');
        bestScoreSpan.innerHTML = `Votre meilleur score : <strong>${data.best}</strong> pts`;
      }
    })
    .catch(console.error);
}


  // ==== à placer DANS showResults(), juste après le calcul de score/pagination ====
fetch('/api/record_session.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: new URLSearchParams({
    game:        'devine_affiche',      // ex. 'devine_affiche' ou 'devine_emoji'
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
      <h3>${n} / ${tot} — ${r.answer} <span class="status-icon">✅</span></h3>
      <img src="${r.posterAfter}" alt="${r.answer}">
      <p class="points">${r.points} pts</p>
    </div>`;
}
 else {
      return `
        <div class="result-item">
          <h3>${n} / ${tot} — ${r.userGuess} <span class="status-icon">❌</span></h3>
          <img src="${r.posterBefore}" alt="Affiche sans titre">
          <p class="points">0 pt</p>
        </div>`;
    }
  }).join('');

  // 🔁 Ensuite on initialise les sliders
  document.querySelectorAll('.homemade-slider').forEach(slider => {
    const overlay = slider.querySelector('.slider-overlay');
    const after = slider.querySelector('.slider-after');

    slider.addEventListener('mousemove', (e) => {
      const rect = slider.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
      after.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
    });

    slider.addEventListener('mouseleave', () => {
      after.style.clipPath = `inset(0 100% 0 0)`;
    });
  });

  // 4) Affiche formulaire pseudo + bouton leaderboard
  leaderboardBtn.classList.remove('hidden');
leaderboardBtn.addEventListener('click', () => {
  window.location.href = '/devine-le-film/leaderboard.php';
}, { once: true });

// Si connecté → auto-save du score
if (userStatus.logged_in) {
  fetch('/api/record_score.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: new URLSearchParams({
    game: 'devine_affiche', // ou devine_emoji
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
  initComparison();

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


  // Track quotidienne
  fetch('/admin/update_stats.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'game=devine_affiche'
  }).catch(() => {});
});
function initComparison() {
  const containers = document.querySelectorAll('.cd-image-container');

  containers.forEach(container => {
    const handle = container.querySelector('.cd-handle');
    const resize = container.querySelector('.cd-resize-img');

    let dragging = false;

    handle.addEventListener('mousedown', e => {
      e.preventDefault();
      dragging = true;
      handle.classList.add('draggable');
    });

    document.addEventListener('mouseup', () => {
      dragging = false;
      handle.classList.remove('draggable');
    });

    container.addEventListener('mousemove', e => {
      if (!dragging) return;

      const containerOffset = container.getBoundingClientRect().left;
      let left = e.pageX - containerOffset;
      left = Math.max(0, Math.min(left, container.offsetWidth));
      updateSlider(left, container, resize, handle);
    });

    // Position initiale au centre
    updateSlider(container.offsetWidth / 2, container, resize, handle);
  });
}

function updateSlider(x, container, resize, handle) {
  resize.style.width = `${x}px`;
  handle.style.left = `${x}px`;
}

