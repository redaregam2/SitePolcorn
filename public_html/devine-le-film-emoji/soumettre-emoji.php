<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion BDD
session_start();
$config = require __DIR__ . '/../../config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Config
$genres = ['Drame', 'Comédie', 'Action', 'SF', 'Horreur', 'Animation', 'Documentaire', 'Fantastique', 'Thriller'];
$difficulties = ['Facile','Normal','Expert'];
$uploadDir    = __DIR__ . '/../uploads/';

$error = '';

// Soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $e1 = trim($_POST['emoji1'] ?? '');
  $e2 = trim($_POST['emoji2'] ?? '');
  $e3 = trim($_POST['emoji3'] ?? '');
  $title = trim($_POST['title'] ?? '');
  $genre = $_POST['genre'] ?? '';
  $diff  = $_POST['difficulty'] ?? '';
  $aliases = array_filter(array_map('trim', $_POST['aliases'] ?? []));

  // Vérifications
  if (!$e1 || !$e2 || !$e3 || !$title || !$genre || !$diff) {
    $error = "Veuillez remplir tous les champs.";
  }
  elseif (empty($_FILES['poster']['name'])) {
    $error = "Veuillez ajouter une image.";
  }
  else {
    $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('emo_') . '.' . $ext;
    $uploadPath = $uploadDir . $newFilename;

    if (move_uploaded_file($_FILES['poster']['tmp_name'], $uploadPath)) {
      // Sauvegarde en base de données
      $stmt = $db->prepare("INSERT INTO suggestions (type, emojis, answer, poster, genre, difficulty, aliases, created_at)
                            VALUES ('emoji', :emojis, :answer, :poster, :genre, :difficulty, :aliases, NOW())");
      $stmt->execute([
        'emojis'     => json_encode([$e1, $e2, $e3], JSON_UNESCAPED_UNICODE),
        'answer'     => $title,
        'poster'     => 'uploads/' . $newFilename,
        'genre'      => $genre,
        'difficulty' => $diff,
        'aliases'    => implode(',', $aliases)
      ]);

      header('Location: soumettre-emoji.php?success=1');
      exit;
    }
    else {
      $error = "Erreur lors de l’upload de l’image.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Proposer un film (Émoji)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/emoji-admin.css">
    <link rel="stylesheet" href="/css/background.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body><div class="gradient-background">
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
    /* animation fade */

  }
.admin-container {
    margin-top: 120px;
}
 
</style>
<div class="admin-container">
  <h1>🎬 Proposer un jeu – Émojis</h1>

  <?php if (isset($_GET['success'])): ?>
    <div class="success">Merci pour votre contribution !</div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <section class="add-form">
    <h2>Proposer votre film</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="emoji1" placeholder="Émoji 1" required maxlength="2">
      <input type="text" name="emoji2" placeholder="Émoji 2" required maxlength="2">
      <input type="text" name="emoji3" placeholder="Émoji 3" required maxlength="2">
      <input type="text" name="title" placeholder="Titre du film" required>

      <select name="genre" required>
        <option value="">-- Genre --</option>
        <?php foreach ($genres as $g): ?>
          <option value="<?= $g ?>"><?= $g ?></option>
        <?php endforeach; ?>
      </select>

      <select name="difficulty" required>
        <option value="">-- Difficulté --</option>
        <?php foreach ($difficulties as $d): ?>
          <option value="<?= $d ?>"><?= $d ?></option>
        <?php endforeach; ?>
      </select>

      <div id="add-aliases-container">
        <input type="text" name="aliases[]" placeholder="Alias (optionnel)">
      </div>
      <button type="button" id="add-alias-btn" class="validate-btn">+ Ajouter un alias</button>

      <input type="file" name="poster" accept="image/*" required>

      <button type="submit" class="validate-btn">Soumettre</button>
    </form>
  </section>
</div>
</div>
<script>
document.getElementById('add-alias-btn').addEventListener('click', () => {
  const container = document.getElementById('add-aliases-container');
  const input = document.createElement('input');
  input.type = 'text';
  input.name = 'aliases[]';
  input.placeholder = 'Alias (optionnel)';
  container.appendChild(input);
});
</script>
<script src="/js/background.js"></script>
</body>
</html>
