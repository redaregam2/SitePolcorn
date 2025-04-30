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
  </div>

  <script src="/DevineInfini/js/infini-game.js"></script>
  <script src="/js/background.js"></script>
</body>
</html>
