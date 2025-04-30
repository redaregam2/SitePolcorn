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

</div>
  <script src="js/script.js"></script>
  <script src="/js/background.js"></script>

</body>
</html>

