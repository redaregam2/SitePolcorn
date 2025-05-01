<?php
// admin/suggestions.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();



// Connexion DB
$config = require __DIR__ . '/../../config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Traitement des actions (valider/refuser)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) $_POST['id'];

  // Récupérer la suggestion
  $stmt = $db->prepare("SELECT * FROM suggestions WHERE id = :id");
  $stmt->execute(['id' => $id]);
  $sugg = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($sugg) {
    if ($_POST['action'] === 'valider') {
      if ($sugg['type'] === 'affiche') {
        // Ajouter dans films.json
        $jsonPath = __DIR__ . '/../devine-le-film/films.json';
        $films = json_decode(file_get_contents($jsonPath), true);

        $films[] = [
          'before' => $sugg['before'],
          'after' => $sugg['after'],
          'answer' => $sugg['answer'],
          'genre' => $sugg['genre'],
          'difficulty' => $sugg['difficulty'],
          'aliases' => array_filter(array_map('trim', explode(',', $sugg['aliases'] ?? '')))
        ];

        file_put_contents($jsonPath, json_encode($films, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
      }
      if ($sugg['type'] === 'emoji') {
        // Ajouter dans emoji_games.json
        $jsonPath = __DIR__ . '/../devine-le-film-emoji/emoji_games.json';
        $games = json_decode(file_get_contents($jsonPath), true);

        $games[] = [
          'emojis'     => json_decode($sugg['emojis'], true),
          'answer'     => $sugg['answer'],
          'poster'     => $sugg['poster'],
          'genre'      => $sugg['genre'],
          'difficulty' => $sugg['difficulty'],
          'aliases'    => array_filter(array_map('trim', explode(',', $sugg['aliases'] ?? '')))
        ];

        file_put_contents($jsonPath, json_encode($games, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
      }
      // Ensuite supprimer la suggestion de la base
      $db->prepare("DELETE FROM suggestions WHERE id = :id")->execute(['id' => $id]);
    }

    if ($_POST['action'] === 'refuser') {
      // Juste supprimer
      $db->prepare("DELETE FROM suggestions WHERE id = :id")->execute(['id' => $id]);
    }
  }

  header('Location: suggestions.php');
  exit;
}

// Charger toutes les suggestions
$stmt = $db->query("SELECT * FROM suggestions ORDER BY created_at DESC");
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modération des suggestions</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: Poppins, sans-serif; background: #121212; color: #fff; padding: 2rem; }
    .suggestion { border: 1px solid #5ac8fa; padding: 1rem; margin-bottom: 2rem; border-radius: 10px; background: #1e1e1e; }
    img { max-height: 200px; margin: 10px 5px; }
    .actions form { display: inline-block; margin-right: 1rem; }
    h2 { color: #5ac8fa; }
    .emoji-list { font-size: 2rem; margin: 10px 0; }
  </style>
</head>
<body>
  <h1>Suggestions à modérer</h1>

  <?php
    $affiches = array_filter($suggestions, fn($s) => $s['type'] === 'affiche');
    $emojis   = array_filter($suggestions, fn($s) => $s['type'] === 'emoji');
  ?>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">

    <!-- Suggestions AFFICHES -->
    <div>
      <h2>🎬 Affiches sans titre</h2>
      <?php if (empty($affiches)): ?>
        <p>Aucune suggestion.</p>
      <?php else: ?>
        <?php foreach($affiches as $s): ?>
          <div class="suggestion">
            <h3><?= htmlspecialchars($s['answer']) ?> (<?= htmlspecialchars($s['difficulty']) ?>)</h3>
            <p><strong>Genre :</strong> <?= htmlspecialchars($s['genre']) ?></p>
            <p><strong>Alias :</strong> <?= htmlspecialchars($s['aliases']) ?></p>
            <div>
              <img src="/<?= htmlspecialchars($s['before']) ?>" alt="Before">
              <img src="/<?= htmlspecialchars($s['after']) ?>" alt="After">
            </div>
            <div class="actions">
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button name="action" value="valider">✅ Valider</button>
              </form>
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button name="action" value="refuser">❌ Refuser</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Suggestions EMOJIS -->
    <div>
      <h2>🎯 Émojis</h2>
      <?php if (empty($emojis)): ?>
        <p>Aucune suggestion.</p>
      <?php else: ?>
        <?php foreach($emojis as $s): ?>
          <div class="suggestion">
            <h3><?= htmlspecialchars($s['answer']) ?> (<?= htmlspecialchars($s['difficulty']) ?>)</h3>
            <p><strong>Genre :</strong> <?= htmlspecialchars($s['genre']) ?></p>
            <p><strong>Alias :</strong> <?= htmlspecialchars($s['aliases']) ?></p>
            <div class="emoji-list">
              <?php foreach(json_decode($s['emojis'], true) as $emoji): ?>
                <?= htmlspecialchars($emoji) ?>
              <?php endforeach; ?>
            </div>
            <img src="/<?= htmlspecialchars($s['poster']) ?>" alt="Poster">

            <div class="actions">
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button name="action" value="valider">✅ Valider</button>
              </form>
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button name="action" value="refuser">❌ Refuser</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

</body>

</html>
