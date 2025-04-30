<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Leaderboard – Devine le film aux émojis</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- 1️⃣ Import de Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <!-- 2️⃣ Styles leaderboard & background -->
  <link rel="stylesheet" href="css/leaderboard.css">
  <link rel="stylesheet" href="/css/background.css">
</head>
<body>
  <!-- Fond animé commun -->
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

  }

 
</style>
    <!-- Titre du leaderboard -->
    <div class="leaderboard">
      <h1>
        <img src="/images/cup1.png" class="ico-cup" alt="Trophée">
        Meilleurs joueurs Émojis
      </h1>
      <ol id="leaderboard-list">
        <!-- Les <li> seront injectés par JS -->
      </ol>
    </div>

    <!-- Bouton Retour aux jeux -->
    <button id="back-to-games" class="back-btn">
      Retour aux jeux
    </button>
  </div>

  <!-- 3️⃣ Récupération et affichage des scores -->
<script>
// 1. Demande le pseudo de l'utilisateur connecté
fetch('/api/current_user.php')
  .then(r => r.json())
  .then(user => {
    const currentPseudo = user.pseudo || '';

    fetch('/api/leaderboard.php?game=devine_emoji')
      .then(r => r.json())
      .then(board => {
        const ol = document.getElementById('leaderboard-list');
        ol.innerHTML = board.map((u, i) => {
          const isCurrent = u.pseudo === currentPseudo;
          return `
            <li class="${isCurrent ? 'current-user' : ''}">
              <mark>
                ${u.pseudo}
                ${isCurrent ? '<span class="badge-moi">Moi</span>' : ''}
              </mark>
              <small>${u.score}</small>
            </li>
          `;
        }).join('');
      });
  })
  .catch(() => {
    document.getElementById('leaderboard-list').innerHTML =
      '<li>Impossible de charger le classement</li>';
  });

document.getElementById('back-to-games')
  .addEventListener('click', () => {
    window.location.href = 'https://polcorn.com/mes-jeux.php';
  });
</script>




  <!-- 4️⃣ Script du background animé -->
  <script src="/js/background.js"></script>
</body>
</html>
