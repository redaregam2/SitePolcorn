<?php
// public_html/soumettre-affiche.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion BDD
$db = new PDO(
  'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8',
  'u714302964_reda','Inzoumouda123*',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);

// Variables d'erreur/succès
$error = '';
$success = '';

// Soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $answer    = trim($_POST['answer'] ?? '');
  $genre     = trim($_POST['genre'] ?? '');
  $difficulty= trim($_POST['difficulty'] ?? '');
  $aliases   = trim($_POST['aliases'] ?? '');

  // Fichiers
  $before = $_FILES['before'] ?? null;
  $after  = $_FILES['after'] ?? null;

  if ($answer === '' || !$before || !$after) {
    $error = "Veuillez remplir tous les champs requis.";
  } else {
    // Créer le dossier uploads/ si nécessaire
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];

    function saveUploadedFile($file, $prefix) {
      global $allowed, $uploadDir;
      if (!in_array($file['type'], $allowed)) return false;
      $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      $filename = uniqid($prefix . '_') . '.' . $ext;
      $dest = $uploadDir . $filename;
      return move_uploaded_file($file['tmp_name'], $dest) ? 'uploads/' . $filename : false;
    }

    $beforePath = saveUploadedFile($before, 'before');
    $afterPath  = saveUploadedFile($after,  'after');

    if (!$beforePath || !$afterPath) {
      $error = "Erreur lors de l'upload des fichiers.";
    } else {
      $stmt = $db->prepare("INSERT INTO suggestions (type, before, after, answer, genre, difficulty, aliases) VALUES ('affiche', :before, :after, :answer, :genre, :difficulty, :aliases)");
      $stmt->execute([
        'before'     => $beforePath,
        'after'      => $afterPath,
        'answer'     => $answer,
        'genre'      => $genre,
        'difficulty' => $difficulty,
        'aliases'    => $aliases
      ]);
      $success = "Merci ! Votre suggestion a été envoyée avec succès.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soumettre une affiche – POLCORN</title>
  <link rel="stylesheet" href="/css/account.css">
  <style>
    form {
      max-width: 600px;
      margin: 40px auto;
      background: #1e1e1e;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(90,200,250,0.2);
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    label { font-weight: 600; }
    input, select, textarea {
      padding: 10px;
      border: 1px solid #444;
      border-radius: 6px;
      background: #2c2c2c;
      color: #e1e1e1;
    }
    button {
      padding: 12px;
      background: #5ac8fa;
      border: none;
      border-radius: 6px;
      color: #121212;
      font-weight: 700;
      cursor: pointer;
    }
    .message {
      max-width: 600px;
      margin: 20px auto;
      text-align: center;
    }
    .error { color: #f44336; }
    .success { color: #1de9b6; }
  </style>
</head>
<body>
  <div class="message">
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <label>Image avant (sans titre) *</label>
    <input type="file" name="before" accept="image/*" required>

    <label>Image après (avec titre) *</label>
    <input type="file" name="after" accept="image/*" required>

    <label>Réponse (titre du film) *</label>
    <input type="text" name="answer" required>

    <label>Genre</label>
    <input type="text" name="genre">

    <label>Difficulté</label>
    <select name="difficulty">
      <option value="Facile">Facile</option>
      <option value="Normal">Normal</option>
      <option value="Difficile">Difficile</option>
    </select>

    <label>Alias (séparés par des virgules)</label>
    <input type="text" name="aliases">

    <button type="submit">Envoyer la suggestion</button>
  </form>
</body>
</html>
