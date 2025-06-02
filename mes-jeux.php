<?php
// mes-jeux.php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes jeux – POLCORN</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="css/mes-jeux.css">
  <link rel="stylesheet" href="/css/background.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
  <div class="gradient-background">
    <div class="gradient-sphere sphere-1"></div>
    <div class="gradient-sphere sphere-2"></div>
    <div class="gradient-sphere sphere-3"></div>
    <div class="glow"></div>
    <div class="grid-overlay"></div>
    <div class="noise-overlay"></div>
    <div class="particles-container" id="particles-container"></div>
  </div>

  <div class="site-content">
    <?php include __DIR__ . '/header.php'; ?>
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

  }

 
</style>

    <main class="main-content">
     
<section class="submit-section saison">
  <div class="saison-flex">
    <div class="gauche">
      <h2>🎬 <strong>Nouvelle Saison Polcorn Games</strong> !</h2>
      <p>
        Participe à notre <strong>nouvelle saison</strong> de jeux pour remporter <strong>2 places de cinéma</strong> !<br>
        À la fin de la saison, le <strong>joueur en tête du classement</strong> et un <strong>joueur aléatoire</strong> seront récompensés.
      </p>
      <div class="submit-buttons">
        <a href="https://polcorn.com/leaderboard-general.php" class="submit-btn">Voir classement Saison 1</a>
      </div>
    </div>
    <div class="droite">
      <img src="images/tick.png" alt="tick" />
    </div>
  </div>
</section>
      <h1>Les jeux</h1>
      <div class="games-container">
        <div class="game-card">
          <img src="images/thumb-emoji.png" alt="Devine le film aux émojis" class="game-thumb">
          <h2>Devine le film aux émojis</h2>
          <a href="/devine-le-film-emoji/" class="play-btn">Jouer</a>
        </div>
        <div class="game-card">
          <img src="images/thumb-infini.png" alt="Devine le film – Mode Infini" class="game-thumb">
          <h2>Mode Infini</h2>
          <a href="/DevineInfini/" class="play-btn">Jouer</a>
          </div>
        <div class="game-card">
          <img src="images/thumb-posters.png" alt="Devine le film sans le titre" class="game-thumb">
          <h2>Devine le film sans le titre</h2>
          <a href="/devine-le-film/" class="play-btn">Jouer</a>
        </div>
        
      </div>
      <section class="submit-section">
  <h2>Vous avez un film à proposer ?</h2>
  <p>Partagez vos idées et aidez-nous à enrichir les jeux POLCORN !</p>
  <div class="submit-buttons">
    <a href="/devine-le-film-emoji/soumettre-emoji.php" class="submit-btn">Proposer pour la version Émojis</a>
    <a href="/devine-le-film/soumettre.php" class="submit-btn">Proposer pour la version Affiches</a>
  </div>
</section>

    </main>
  </div>

  <script src="/js/background.js"></script>
</body>
</html>
