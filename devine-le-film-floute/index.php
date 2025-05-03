<!-- filepath: c:\Users\pcsur\Documents\SitePolcorn\public_html\devine-le-film-floute\index.php -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devine le film flouté</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/devine-le-film-floute/css/style.css">
  <link rel="stylesheet" href="/css/background.css">
</head>
<body>
  <div class="gradient-background">
    <div class="gradient-sphere sphere-1"></div>
    <div class="gradient-sphere sphere-2"></div>
    <div class="gradient-sphere sphere-3"></div>
    <div class="glow"></div>
    <div class="grid-overlay"></div>
    <div class="noise-overlay"></div>
  </div>

  <div class="site-content">
    <?php include __DIR__ . '/../header.php'; ?>

    <!-- Écran de démarrage -->
    <div id="start-screen" class="start-screen">
      <h1>Devine le film flouté</h1>
      <div class="game-rules">
        <h2>Règles du jeu</h2>
        <ul>
          <li>Trouvez le titre du film en anglais ou en français.</li>
          <li>Chaque film trouvé rapporte 50 points.</li>
          <li>Bonus de 10 points pour chaque film trouvé consécutivement.</li>
          <li>Utiliser l'aide coûte des points.</li>
        </ul>
      </div>
      <button id="start-btn" class="start-btn">Commencer</button>
    </div>

    <!-- Zone de jeu -->
    <div id="game-container" class="game-container hidden">
      <div id="timer" class="timer">60.00</div>
      <div id="poster-wrapper" class="poster-wrapper">
        <img id="movie-poster" src="" alt="Affiche du film">
        <div id="toast-container" class="toast-container"></div>
      </div>
      <form id="guess-form" class="guess-form">
        <input type="text" id="movie-input" placeholder="Quel est ce film ?" autocomplete="off" required>
        <button type="submit" id="submit-btn" class="validate-btn">Valider</button>
      </form>
      <button id="help-btn" class="help-btn">❓</button>
      <div id="hint-container" class="hint-container hidden"></div>
    </div>

    <!-- Écran de résultats -->
    <div id="result-container" class="hidden">
      <h2>Résultats</h2>
      <p id="score-display"></p>
      <div id="results"></div>
      <button id="restart-btn">Rejouer</button>
    </div>
  </div>

  <script src="/devine-le-film-floute/js/script.js"></script>
  
</body>
</html>