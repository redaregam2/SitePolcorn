<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mode Infini</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="stylesheet" href="css/infini-game.css">
  <link rel="stylesheet" href="/css/background.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

  <!-- Fond visuel -->
  <div class="gradient-background">
    <div class="gradient-sphere sphere-1"></div>
    <div class="gradient-sphere sphere-2"></div>
    <div class="gradient-sphere sphere-3"></div>
    <div class="glow"></div>
    <div class="grid-overlay"></div>
    <div class="noise-overlay"></div>
    <div class="particles-container" id="particles-container"></div>
  </div>

  <?php include __DIR__.'/../header.php'; ?>
  <style>
    header {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      z-index: 1000;
      background: rgba(18,18,18,0.7);
      backdrop-filter: blur(8px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.5s ease;
    }
    header.visible {
      opacity: 1;
      pointer-events: auto;
    }
    .result-container {
    margin-top: 120px;
}
  </style>

  <div class="site-content">

    <!-- Start screen -->
    <div id="start-screen" class="start-screen">
      <div class="game-rules">
        <h2>Mode Infini</h2>
        <ul>
          <li>🎬 Films aléatoires (emoji ou affiche)</li>
          <li>⚡ Enchaînez les bonnes réponses</li>
          <li>❤️ 3 erreurs max</li>
          <li>🔥 Faites des combos pour marquer plus !</li>
        </ul>
      </div>
      <button type="button" id="start-btn" class="start-btn">Commencer</button>

      <div class="music-player duplicate-player">
  <div class="controls">
    <i id="prev-btn-2" class="fas fa-backward"></i>
    <i id="play-pause-btn-2" class="fas fa-play"></i>
    <i id="next-btn-2" class="fas fa-forward"></i>
  </div>
  <div class="volume-controls">
    <i id="mute-btn-2" class="fas fa-volume-up"></i>
    <input id="volume-slider-2" type="range" min="0" max="1" step="0.1" value="0.5">
  </div>
  <p class="track-info">🎵 <span id="track-title-2">Ser4PH1M - Highway 1984</span></p>
</div>

    </div>

    <!-- Zone de jeu -->
    <div id="game-container" class="game-container hidden">
        <div id="timer" class="timer">10.00</div>

      <div id="combo-display" class="combo-display hidden">Combo x1</div>
      <div id="hearts" class="hearts">
  <span class="heart">❤️</span>
  <span class="heart">❤️</span>
  <span class="heart">❤️</span>
</div>

      <div id="emoji-display" class="emoji-display hidden"></div>
      <div id="poster-wrapper" class="poster-wrapper hidden">
        <img id="movie-poster" src="" alt="Affiche du film">
      </div>
      <form id="guess-form" class="guess-form">
        <input type="text" id="guess-input" placeholder="Quel est ce film ?" autocomplete="off" required>
        <button type="submit">Valider</button>
      </form>
    </div>

    <!-- Zone de résultat -->
    <div class="result-container hidden">
      <h2 id="score"></h2>
      <div id="results"></div>
      <button id="restart-btn" class="restart-btn">Rejouer</button>
    </div>
    <div class="music-player">
  <div class="controls">
    <i id="prev-btn" class="fas fa-backward"></i>
    <i id="play-pause-btn" class="fas fa-play"></i>
    <i id="next-btn" class="fas fa-forward"></i>
  </div>
  <div class="volume-controls">
    <i id="mute-btn" class="fas fa-volume-up"></i>
    <input id="volume-slider" type="range" min="0" max="1" step="0.1" value="0.5">
  </div>
  <p class="track-info">🎵 <span id="track-title">Ser4PH1M - Highway 1984</span></p>
</div>
  </div>

  <script src="/DevineInfini/js/infini-game.js"></script>
  <script src="/js/background.js"></script>
</body>
</html>
<script>
  // Liste des morceaux
  const tracks = [
    { title: "Ser4PH1M - Highway 1984", src: "/musique/ser4ph1n.wav" },
    { title: "Ser4PH1M - Neon Dreams", src: "/musique/ser4phin.wav" }
  ];

  let currentTrackIndex = 0;
  const audio = new Audio(tracks[currentTrackIndex].src);
  audio.loop = false; // Pas de boucle automatique
  audio.volume = 0.5; // Volume initial à 50%

  // Sélectionner les éléments pour les deux lecteurs
  const playPauseBtns = [document.getElementById('play-pause-btn'), document.getElementById('play-pause-btn-2')];
  const prevBtns = [document.getElementById('prev-btn'), document.getElementById('prev-btn-2')];
  const nextBtns = [document.getElementById('next-btn'), document.getElementById('next-btn-2')];
  const muteBtns = [document.getElementById('mute-btn'), document.getElementById('mute-btn-2')];
  const volumeSliders = [document.getElementById('volume-slider'), document.getElementById('volume-slider-2')];
  const trackTitles = [document.getElementById('track-title'), document.getElementById('track-title-2')];

  // Mettre à jour le titre du morceau
  function updateTrackInfo() {
    trackTitles.forEach(title => {
      title.textContent = tracks[currentTrackIndex].title;
    });
  }

  // Lecture/Pause
  function togglePlayPause() {
    if (audio.paused) {
      audio.play().then(() => {
        playPauseBtns.forEach(btn => {
          btn.classList.remove('fa-play');
          btn.classList.add('fa-pause');
        });
      }).catch(() => {
        console.warn("Lecture automatique bloquée par le navigateur.");
      });
    } else {
      audio.pause();
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-pause');
        btn.classList.add('fa-play');
      });
    }
  }

  playPauseBtns.forEach(btn => btn.addEventListener('click', togglePlayPause));

  // Changer de morceau (précédent)
  prevBtns.forEach(btn => btn.addEventListener('click', () => {
    currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
    audio.src = tracks[currentTrackIndex].src;
    audio.play().then(() => {
      updateTrackInfo();
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-play');
        btn.classList.add('fa-pause');
      });
    });
  }));

  // Changer de morceau (suivant)
  nextBtns.forEach(btn => btn.addEventListener('click', () => {
    currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
    audio.src = tracks[currentTrackIndex].src;
    audio.play().then(() => {
      updateTrackInfo();
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-play');
        btn.classList.add('fa-pause');
      });
    });
  }));

  // Mute/Unmute
  muteBtns.forEach(btn => btn.addEventListener('click', () => {
    audio.muted = !audio.muted;
    muteBtns.forEach(muteBtn => {
      muteBtn.classList.toggle('fa-volume-up');
      muteBtn.classList.toggle('fa-volume-mute');
    });
  }));

  // Ajuster le volume
  volumeSliders.forEach(slider => slider.addEventListener('input', (e) => {
    audio.volume = e.target.value;
    volumeSliders.forEach(s => s.value = e.target.value); // Synchroniser les jauges
  }));

  // Lecture automatique au chargement
  window.addEventListener('load', () => {
    // Synchroniser les jauges de volume avec le volume initial
    volumeSliders.forEach(slider => {
      slider.value = audio.volume;
    });

    audio.play().then(() => {
      updateTrackInfo();
      // Mettre à jour les icônes des boutons Play/Pause
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-play');
        btn.classList.add('fa-pause');
      });
    }).catch(() => {
      console.warn("Lecture automatique bloquée par le navigateur.");
      // Si la lecture automatique est bloquée, afficher l'icône Play
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-pause');
        btn.classList.add('fa-play');
      });
    });
  });
</script>