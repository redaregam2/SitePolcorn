<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devine le film aux émojis</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/emoji-game.css">
    <link rel="stylesheet" href="/css/background.css">
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


  <!-- START SCREEN -->
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

  <!-- GAME -->
  <div class="game-container hidden">
    <div id="timer" class="timer">10.00</div>
    <div id="emoji-display" class="emoji-display"></div>
    <form id="guess-form" class="guess-form">
      <input type="text" id="guess-input" placeholder="Quel est ce film ?" required autocomplete="off">
      <button type="submit">Valider</button>
    </form>
  </div>

<!-- RESULTATS -->
<div id="result-container" class="result-container hidden">
  <h2>Résultats</h2>
  <p id="score-display"></p>

  <!-- Formulaire pseudo -->
  <div id="pseudo-form" class="pseudo-form hidden">

  </div>

  <!-- Bouton Leaderboard (caché par défaut) -->
  <button id="leaderboard-btn" class="back-btn hidden">
    Voir le leaderboard
  </button>

  <!-- Liste des manches -->
  <div id="results" class="results-list"></div>

  <!-- Rejouer -->
  <button id="restart-btn">Rejouer</button>
</div>


</div>
  <script src="js/emoji-game.js"></script>
    <script src="/js/background.js"></script>
</body>
</html>