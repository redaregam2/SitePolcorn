<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user'])) {
  header('Location:/'); exit;
}

// Infos user
$user = array_merge(
  ['id'=>null,'email'=>'','name'=>'','picture'=>'','pseudo'=>null],
  $_SESSION['user']
);

// Connexion BD
// Connexion BD
$db = new PDO(
    'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8',
    'u714302964_reda','Inzoumouda123*',
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);

// 🔥 AJOUTER CE BLOC ICI 🔥
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pseudo'])) {
  $p = trim($_POST['pseudo']);
  if ($p !== '') {
    $stmt = $db->prepare("UPDATE users SET pseudo=:p WHERE id=:id");
    $stmt->execute(['p' => $p, 'id' => $user['id']]);
    $user['pseudo'] = $p;
    $_SESSION['user']['pseudo'] = $p;
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
  }
}

// 1) Récupère **tous** les achievements (pour la progression)
$allAchStmt = $db->query("SELECT * FROM achievements");
$allAch = $allAchStmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Récupère **ce que l’utilisateur a débloqué**, en incluant le game_scope
$uaStmt = $db->prepare("\n  SELECT a.id,a.game_scope,a.name,a.description,a.icon,ua.unlocked_at,a.threshold_type,a.threshold_value\n  FROM user_achievements ua\n  JOIN achievements a ON a.id = ua.achievement_id\n  WHERE ua.user_id = :uid\n");
$uaStmt->execute(['uid'=>$user['id']]);
$unlocked = $uaStmt->fetchAll(PDO::FETCH_ASSOC);

// 3) Calcul de la progression “count” (100 parties jouées…) par scope
$progress = [];
foreach($allAch as $a){
  if($a['threshold_type'] !== 'count') continue;
  $cntStmt = $db->prepare("\n    SELECT COUNT(*) FROM user_game_sessions\n    WHERE user_id=:uid AND game_scope=:scope\n  ");
  $cntStmt->execute(['uid'=>$user['id'],'scope'=>$a['game_scope']]);
  $count = (int)$cntStmt->fetchColumn();
  $progress[$a['game_scope']][$a['id']] = [
    'done'  => $count,
    'total' => $a['threshold_value']
  ];
}

// 4) Regroupement des unlocked par scope
$unlockedByScope = [];
foreach($unlocked as $a){
  $unlockedByScope[$a['game_scope']][] = $a;
}

// Légendes pour chaque scope
$scopeLabels = [
  'devine_affiche' => 'Devine le film sans le titre',
  'devine_emoji'   => 'Devine le film aux émojis'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta charset="UTF-8">
  <title>Mon compte – POLCORN</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="/css/account.css">
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

    <main class="account-container">
      <h1>Mon compte</h1>

      <div class="profile">
        <?php
          $avatar = $user['picture'] ?: '/images/default-avatar.png';
        ?>
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar">
        <div class="info">
          <?php if($user['name']): ?>
            <p><strong>Nom :</strong> <?= htmlspecialchars($user['name']) ?></p>
          <?php endif; ?>
          <p><strong>E-mail :</strong> <?= htmlspecialchars($user['email']) ?></p>
          <?php if(!$user['pseudo']): ?>
            <form method="POST">
              <label for="pseudo">Choisissez votre pseudo :</label>
              <input type="text" name="pseudo" id="pseudo" required>
              <button type="submit">Enregistrer</button>
            </form>
          <?php else: ?>
            <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php foreach($scopeLabels as $scope=>$label): ?>
      <section class="achievements">
        <h2><?= $label ?> – Trophées</h2>
        <ul class="achievements-list">
          <?php
            $scopeAch = array_filter($allAch, fn($a) => $a['game_scope']==$scope);
            $unlockedIds = array_column($unlockedByScope[$scope] ?? [], 'id');
            foreach($scopeAch as $a):
              $isUnlocked = in_array($a['id'], $unlockedIds, true);
              $cls = $isUnlocked ? 'unlocked' : 'locked';
          ?>
          <li class="<?= $cls ?>">
            <span class="icon"><?= htmlspecialchars($a['icon']) ?></span>
            <strong><?= htmlspecialchars($a['name']) ?></strong><br>
            <small><?= htmlspecialchars($a['description']) ?></small>
            <?php if($a['threshold_type']==='count'): ?>
              <?php
                $done  = $progress[$scope][$a['id']]['done'] ?? 0;
                $total = $progress[$scope][$a['id']]['total'];
              ?>
              <div class="progress-wrapper">
                <progress value="<?= $done ?>" max="<?= $total ?>"></progress>
                <span><?= $done ?>/<?= $total ?></span>
              </div>
            <?php endif; ?>
            <?php if($isUnlocked):
              $ua = array_values(array_filter(
                $unlockedByScope[$scope], fn($u)=> $u['id']==$a['id']
              ))[0];
            ?>
            <br><em>Débloqué le <?= date('d/m/Y H:i',strtotime($ua['unlocked_at'])) ?></em>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>

    </main>
  </div>
  <script src="/js/background.js"></script>
</body>
</html>
