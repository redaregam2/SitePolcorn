<?php?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Classement Général – POLCORN</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="devine-le-film/css/leaderboard.css">
  <link rel="stylesheet" href="css/background.css">
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
    <?php include __DIR__.'/header.php'; ?>
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
.current-user mark {
  background: #ffd700;
  color: #222;
}
.badge-moi {
  background: #222;
  color: #ffd700;
  border-radius: 8px;
  padding: 2px 8px;
  margin-left: 8px;
  font-size: 0.8em;
  font-weight: bold;
}

      }
    </style>

    <div class="leaderboard">
      <h1>
        <img src="/images/cup1.png" class="ico-cup" alt="Trophée">
        Classement Général <br> Saison 1
      </h1>
      <ol id="leaderboard-list">
        <!-- JS injecte ici -->
      </ol>
    </div>

    <button id="back-to-games" class="back-btn">
      Retour aux jeux
    </button>
  </div>

<script>
fetch('/api/current_user.php')
  .then(r => r.json())
  .then(user => {
    const currentPseudo = user.pseudo || '';

    fetch('/api/leaderboard_general.php')
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
              <small>
                Affiche : <b>${u.affiche}</b> |
                Emoji : <b>${u.emoji}</b> |
                Infini : <b>${u.infini}</b> |
                <br><span style="color:#ffd700">Total : <b>${u.total}</b></span>
              </small>
            </li>
          `;
        }).join('');
      })
      .catch(() => {
        document.getElementById('leaderboard-list').innerHTML =
          '<li>Impossible de charger le classement</li>';
      });
  })
  .catch(() => {
    document.getElementById('leaderboard-list').innerHTML =
      '<li>Impossible de charger le classement</li>';
  });

document.getElementById('back-to-games')
  .addEventListener('click', () => {
    window.location.href = '/mes-jeux.php';
  });
</script>

  <script src="/js/background.js"></script>
</body>
</html>