// /js/infini-game.js

// Ce jeu utilise à la fois emoji_games.json et films.json
document.addEventListener('DOMContentLoaded', () => {
  const timerEl = document.getElementById('timer');
  let games = [], currentGame = null;
  let combo = 1, streak = 0, score = 0;
  let userStatus = { logged_in: false, pseudo: null, user_id: null };

  const MAX_ERRORS = 3;
  let errors = 0;

  const TIME_PER_GAME_MS = 10000;

  let history = [];
  let usedEmojiIndices = new Set();
  let usedAfficheIndices = new Set();

  const startScreen   = document.getElementById('start-screen');
  const startBtn      = document.getElementById('start-btn');
  const gameContainer = document.getElementById('game-container');
  const emojiDisplay  = document.getElementById('emoji-display');
  const posterWrapper = document.getElementById('poster-wrapper');
  const posterImg     = document.getElementById('movie-poster');
  const input         = document.getElementById('guess-input');
  const form          = document.getElementById('guess-form');
  const comboDisplay  = document.getElementById('combo-display');
  const hearts = document.querySelectorAll('.heart');
  const streakDisplay = document.getElementById('streak-display');
  const resultContainer = document.querySelector('.result-container');
  const resultsList   = document.getElementById('results');
  const scoreDisplay  = document.getElementById('score');
  const restartBtn    = document.getElementById('restart-btn');

  const pseudoForm = document.createElement('div');
  pseudoForm.id = 'pseudo-form';


  const leaderboardBtn = document.createElement('button');
  leaderboardBtn.textContent = 'Voir le classement';
  leaderboardBtn.className = 'restart-btn';
  leaderboardBtn.id = 'leaderboard-btn';
  leaderboardBtn.addEventListener('click', () => {
    window.location.href = 'https://polcorn.com/DevineInfini/leaderboard-infini.php';
  });


  const toastContainer = document.createElement('div');
  toastContainer.id = 'toast-container';
  toastContainer.style.position = 'fixed';
  toastContainer.style.top = '80px';
  toastContainer.style.right = '20px';
  toastContainer.style.zIndex = '1000';
  document.body.appendChild(toastContainer);

  fetch('/api/user_status.php')
    .then(r => r.json())
    .then(data => userStatus = data)
    .catch(() => userStatus = { logged_in: false });

  startBtn.disabled = true;

  let emojiData = [];
  let afficheData = [];

  Promise.all([
    fetch('/devine-le-film-emoji/emoji_games.json').then(r => r.ok ? r.json() : Promise.reject(`emoji_games.json → ${r.status}`)),
    fetch('/devine-le-film/films.json').then(r => r.ok ? r.json() : Promise.reject(`films.json → ${r.status}`))
  ]).then(([emoji, affiche]) => {
    emojiData = emoji;
    afficheData = affiche;
    startBtn.disabled = false;
  }).catch(err => alert('Erreur chargement JSON : ' + err));

  startBtn.addEventListener('click', () => {
    // Enregistrer le lancement du jeu
    fetch('/admin/update_stats.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'game=devine_infini'
    }).catch(() => {});

    startScreen.style.display = 'none';
    startScreen.classList.add('hidden');
    gameContainer.classList.remove('hidden');
    loadNext();
  });

  form.addEventListener('submit', e => {
    e.preventDefault();
    checkAnswer(false);
  });

  restartBtn.addEventListener('click', () => location.reload());

  function loadNext() {
    const type = Math.random() < 0.5 ? 'emoji' : 'affiche';

    if (type === 'emoji') {
      if (usedEmojiIndices.size === emojiData.length) usedEmojiIndices.clear();
      let index;
      do index = Math.floor(Math.random() * emojiData.length);
      while (usedEmojiIndices.has(index));
      usedEmojiIndices.add(index);

      currentGame = structuredClone(emojiData[index]);
      currentGame.type = 'emoji';
      emojiDisplay.classList.remove('hidden');
      posterWrapper.classList.add('hidden');
      emojiDisplay.textContent = currentGame.emojis.join(' ');
    } else {
      if (usedAfficheIndices.size === afficheData.length) usedAfficheIndices.clear();
      let index;
      do index = Math.floor(Math.random() * afficheData.length);
      while (usedAfficheIndices.has(index));
      usedAfficheIndices.add(index);

      currentGame = structuredClone(afficheData[index]);
      currentGame.type = 'affiche';
      emojiDisplay.classList.add('hidden');
      posterWrapper.classList.remove('hidden');
      posterImg.src = '/devine-le-film/' + currentGame.before;
    }

    input.value = '';
    input.focus();
    startTimer();
  }

  let timerFrame = null;
  let startTime = null;
  let timerRunning = false;

  function startTimer() {
    cancelAnimationFrame(timerFrame);
    startTime = performance.now();
    timerRunning = true;

    function tick(now) {
      const elapsed = now - startTime;
      const remaining = TIME_PER_GAME_MS - elapsed;

      if (!timerRunning) return;

      if (remaining <= 0) {
        timerEl.textContent = "0.00";
        timerRunning = false;
        checkAnswer(true);
        return;
      }

      const seconds = Math.floor(remaining / 1000);
      const centiseconds = Math.floor((remaining % 1000) / 10);
      timerEl.textContent = `${seconds}.${centiseconds.toString().padStart(2, '0')}`;

      const ratio = remaining / TIME_PER_GAME_MS;
      timerEl.style.color = ratio >= 0.7 ? '#1de9b6' : ratio >= 0.4 ? '#ffb74d' : '#f44336';

      timerFrame = requestAnimationFrame(tick);
    }

    requestAnimationFrame(tick);
  }

  function normalize(str) {
  return str
    .toLowerCase()
    .normalize('NFD') // décompose les accents
    .replace(/[\u0300-\u036f]/g, '') // supprime les accents
    .replace(/\s+/g, ''); // supprime tous les espaces
}


function checkAnswer(timeout = false) {
  if (!timerRunning && !timeout) return;
  timerRunning = false;
  cancelAnimationFrame(timerFrame);

  const guess = normalize(input.value);
  const correct = normalize(currentGame.answer);
  const aliases = Array.isArray(currentGame.aliases) ? currentGame.aliases.map(a => normalize(a)) : [];

  const isCorrect = !timeout && (guess === correct || aliases.includes(guess));
  input.blur();

  // Calcul de la durée de la réponse
  const responseDuration = performance.now() - startTime;

  let basePts = 0;
  if (isCorrect) {
    const rem = TIME_PER_GAME_MS - responseDuration;
    basePts = Math.ceil((rem / TIME_PER_GAME_MS) * 100);
  }
  let bonusPts = isCorrect ? 10 : 0; // Toujours +10, combo ou pas
  let totalPts = isCorrect ? basePts + bonusPts : 0;

  history.push({
    ...currentGame,
    isCorrect,
    userGuess: guess,
    points: totalPts,
    status: isCorrect ? '✅' : '❌',
    responseDuration // Ajout de la durée de la réponse
  });

  if (isCorrect) {
    score += totalPts;
    combo++;
    streak++;
    showFeedback(true);
    showToast(`+${basePts} base +${bonusPts} combo`, totalPts);

    // Vérifiez si le combo atteint 10 et envoyez une requête au backend
    if (combo === 10) {
      fetch('/api/record_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          game: 'devine_infini',
          score,
          correct: history.filter(h => h.isCorrect).length,
          duration_ms: Math.min(...history.map(r => r.responseDuration)), // Durée minimale pour "Réponse éclair"
          currentCombo: combo // Vérifiez que cette ligne est présente
        })
      })
        .then(r => r.text()) // Changez .json() en .text() pour voir la réponse brute
        .then(data => {
          console.log('Réponse brute du backend :', data); // Affiche la réponse brute
          const json = JSON.parse(data); // Convertissez en JSON après avoir vérifié la réponse
          if (json.new_achievements && json.new_achievements.length) {
            json.new_achievements.forEach(a => {
              showAchievementNotif(a.name, a.description);
            });
          }
        })
        .catch(err => console.error('Erreur lors de la requête :', err));
    }
  } else {
    errors++;
    combo = 1; // Réinitialisation du combo en cas d'erreur
    streak = 0;
    showFeedback(false);
  }

  updateDisplays();

  if (errors >= MAX_ERRORS) {
    setTimeout(showResults, 500);
  } else {
    setTimeout(loadNext, 600);
  }
}

  function updateDisplays() {
    comboDisplay.textContent = `Combo x${combo}`;
    comboDisplay.classList.toggle('hidden', combo <= 1);
    hearts.forEach((heart, i) => heart.classList.toggle('lost', i < errors));
  }

  function showFeedback(correct) {
    const el = correct ? gameContainer : input;
    if (!correct) {
      gameContainer.classList.add('shake-error');
      gameContainer.style.boxShadow = '0 0 20px rgba(255, 0, 0, 0.5)';
      setTimeout(() => {
        gameContainer.classList.remove('shake-error');
        gameContainer.style.boxShadow = '';
      }, 500);
    }
    el.classList.add(correct ? 'correct' : 'wrong');
    setTimeout(() => el.classList.remove('correct', 'wrong'), 500);
  }

  function showToast(text, pts) {
    const toast = document.createElement('div');
    toast.textContent = text;
    toast.style.padding = '10px 20px';
    toast.style.marginBottom = '10px';
    toast.style.borderRadius = '8px';
    toast.style.fontWeight = 'bold';
    toast.style.color = '#fff';
    toast.style.backgroundColor = pts > 70 ? '#4caf50' : pts > 30 ? '#ff9800' : '#f44336';
    toast.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.transform = 'translateX(100%)';

    toastContainer.appendChild(toast);
    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(0)';
    });

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 300);
    }, 1500);
  }

  function showResults() {
    const header = document.querySelector('header');
    if (header) header.classList.add('visible');
    gameContainer.classList.add('hidden');
    resultContainer.classList.remove('hidden');
    scoreDisplay.textContent = `Votre score : ${score} pts`;


// —— insérer les boutons AVANT la liste des manches ——
const resultsListEl = document.getElementById('results');
if (userStatus.logged_in) {
  // si connecté, seul "Voir le classement"
  resultContainer.insertBefore(leaderboardBtn, resultsListEl);
} else {
  // sinon, seul le formulaire de connexion
  resultContainer.insertBefore(leaderboardBtn, resultsListEl);
  resultContainer.insertBefore(pseudoForm, resultsListEl);
}


resultsList.innerHTML = history.map((r, i) => {
  const n = i + 1, tot = history.length;
  if (r.type === 'emoji') {
    if (r.status === '✅') {
      return `
        <div class="result-item">
          <div class="emojis">${r.emojis.join(' ')}</div>
          <h3>${n} / ${tot} — ${r.answer} <span class="status-icon">✅</span></h3>
          <img src="https://polcorn.com/devine-le-film-emoji/${r.poster}" alt="${r.answer}">
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
  } else {
    if (r.status === '✅') {
      return `
        <div class="result-item">
          <h3>${n} / ${tot} — ${r.answer} <span class="status-icon">✅</span></h3>
          <img src="/devine-le-film/${r.after}" alt="${r.answer}">
          <p class="points">${r.points} pts</p>
        </div>`;
    } else {
      return `
        <div class="result-item">
          <h3>${n} / ${tot} — ${r.userGuess} <span class="status-icon">❌</span></h3>
          <img src="/devine-le-film/${r.after}" alt="Affiche floutée"   style="filter: blur(8px); pointer-events: none; user-drag: none; user-select: none;" 
  draggable="false" 
  oncontextmenu="return false;">
          <p class="points">0 pt</p>
        </div>`;
    }
  }
}).join('');

    if (userStatus.logged_in) {
      fetch('/api/record_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ game: 'devine_infini', score })
      }).catch(console.error);

      fetch('/api/record_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          game: 'devine_infini',
          score,
          correct: history.filter(h => h.isCorrect).length,
          duration_ms: Math.min(...history.map(r => r.responseDuration)), // Durée minimale pour "Réponse éclair"
          currentCombo: combo
        })
      })
        .then(r => r.json())
        .then(data => {
          console.log('Données envoyées au backend :', {
            game: 'devine_infini',
            score,
            correct: history.filter(h => h.isCorrect).length,
            duration_ms: Math.min(...history.map(r => r.responseDuration)),
            currentCombo: combo
          });
          if (data.new_achievements && data.new_achievements.length) {
            data.new_achievements.forEach(a => {
              showAchievementNotif(a.name, a.description);
            });
          }
        })
        .catch(console.error);
    } else {
      pseudoForm.innerHTML = `
        <p>Connectez-vous pour enregistrer votre score :</p>
        <button id="open-login-popup" class="account-btn">Se connecter / S'inscrire</button>`;
      document.getElementById('open-login-popup').addEventListener('click', () => {
        const loginPopupTrigger = document.getElementById('auth-btn');
        loginPopupTrigger ? loginPopupTrigger.click() : alert('Erreur : impossible d’ouvrir la connexion.');
      });
    }
  }

  const style = document.createElement('style');
  style.textContent = `
    @keyframes shakeError {
      10%, 90% { transform: translateX(-2px); }
      20%, 80% { transform: translateX(4px); }
      30%, 50%, 70% { transform: translateX(-6px); }
      40%, 60% { transform: translateX(6px); }
    }
    .shake-error { animation: shakeError 0.5s; }
  `;
  document.head.appendChild(style);

});
