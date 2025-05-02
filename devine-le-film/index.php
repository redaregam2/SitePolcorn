<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devine le film</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/index-style.css">
  <link rel="stylesheet" href="/css/background.css">
<script type="module" src="https://cdn.jsdelivr.net/npm/img-comparison-slider@8/dist/index.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/img-comparison-slider@8/dist/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>

  <!-- 1) Fond animé commun -->
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
    /* animation fade */
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s ease;
  }

  header.visible {
    opacity: 1;
    pointer-events: auto;
  }
</style>


   <div class="site-content">


  <!-- Écran de démarrage -->
  <div id="start-screen" class="start-screen">
      <div class="game-rules">
  <h2>Règles du jeu</h2>
  <ul>
    <li>🎬 10 films choisis au hasard</li>
    <li>⏱️ 10 secondes pour chaque film</li>
    <li>💯 Marquez le meilleur score possible</li>
  </ul>
</div>

    <button id="start-btn" class="start-btn">Commencer</button>


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

  <!-- Jeu -->
  <div class="game-container hidden">
    <div id="timer" class="timer">10.00</div>
    <div class="poster-wrapper" id="poster-wrapper">
      <img id="movie-poster" src="" alt="Affiche du film">
    </div>
    <form id="guess-form" class="guess-form">
      <input type="text" id="movie-input" placeholder="Quel est ce film ?" autocomplete="off" required>
      <button type="submit" id="submit-btn" class="validate-btn">Valider</button>
    </form>
    
  </div>

  <!-- Résultats -->
<div class="result-container hidden">
  <h2 id="score"></h2>
  <!-- Formulaire pseudo -->
  <div id="pseudo-form" class="pseudo-form hidden">

  </div>

<button id="leaderboard-btn" class="validate-btn" onclick="window.location.href='/devine-le-film/leaderboard.php'">
  Voir le leaderboard
</button>

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
  <script src="js/script.js"></script>
  <script src="/js/background.js"></script>
<script>
  // Liste des morceaux
  const tracks = [
    { title: "Ser4PH1M - Highway 1984", src: "/musique/ser4ph1n.wav" },
    { title: "Ser4PH1M - Neon Dreams", src: "/musique/ser4phin.wav" }
  ];

  let currentTrackIndex = 0;

  // Créer un AudioContext pour gérer le volume
  const audioContext = new (window.AudioContext || window.webkitAudioContext)();
  const gainNode = audioContext.createGain();
  const audio = new Audio(tracks[currentTrackIndex].src);
  const track = audioContext.createMediaElementSource(audio);
  track.connect(gainNode).connect(audioContext.destination);

  audio.loop = false; // Pas de boucle automatique
  gainNode.gain.value = 0.5; // Volume initial à 50%

  // Sélectionner les éléments pour le lecteur
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
      audioContext.resume(); // Nécessaire pour activer l'AudioContext sur iOS
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

  // Ajuster le volume via le GainNode
  volumeSliders.forEach(slider => slider.addEventListener('input', (e) => {
    gainNode.gain.value = e.target.value;
    volumeSliders.forEach(s => s.value = e.target.value); // Synchroniser les jauges
  }));

  // Lecture automatique au chargement
  window.addEventListener('load', () => {
    volumeSliders.forEach(slider => {
      slider.value = gainNode.gain.value; // Synchroniser la jauge de volume
    });

    audio.play().then(() => {
      updateTrackInfo();
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-play');
        btn.classList.add('fa-pause');
      });
    }).catch(() => {
      console.warn("Lecture automatique bloquée par le navigateur.");
      playPauseBtns.forEach(btn => {
        btn.classList.remove('fa-pause');
        btn.classList.add('fa-play');
      });
    });
  });
</script>

</body>
</html>

