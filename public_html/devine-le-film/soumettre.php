<?php
// devine-le-film/soumettre.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de configuration
$config = require __DIR__ . '/../../config.php';

// Initialiser PDO
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Gestion des erreurs
    if (preg_match('#^(localhost|127\.0\.0\.1)#', $_SERVER['HTTP_HOST'] ?? php_uname('n'))) {
        die("Erreur de connexion à la base (local) : " . $e->getMessage());
    } else {
        error_log("DB prod error: " . $e->getMessage());
        die("Impossible de se connecter à la base de données.");
    }
}

// Genres et Difficultés
$genres = ['Drame', 'Comédie', 'Action', 'SF', 'Horreur', 'Animation', 'Documentaire'];
$difficulties = ['Facile', 'Normal', 'Expert'];

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer     = trim($_POST['answer']);
    $genre      = $_POST['genre'];
    $difficulty = $_POST['difficulty'];
    $aliases    = array_filter(array_map('trim', $_POST['aliases'] ?? []));

    if (!empty($answer) && isset($_FILES['before']) && isset($_FILES['after'])) {
        // Upload
        $extB = pathinfo($_FILES['before']['name'], PATHINFO_EXTENSION);
        $extA = pathinfo($_FILES['after']['name'], PATHINFO_EXTENSION);
        $beforeName = uniqid() . '_before.' . $extB;
        $afterName  = uniqid() . '_after.' . $extA;

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ok1 = move_uploaded_file($_FILES['before']['tmp_name'], $uploadDir . $beforeName);
        $ok2 = move_uploaded_file($_FILES['after']['tmp_name'],  $uploadDir . $afterName);

        if ($ok1 && $ok2) {
            // Enregistre la suggestion
            $stmt = $pdo->prepare("
                INSERT INTO suggestions (type, `before`, `after`, answer, genre, difficulty, aliases)
                VALUES ('affiche', :before, :after, :answer, :genre, :difficulty, :aliases)
            ");
            $stmt->execute([
                'before'     => 'uploads/' . $beforeName,
                'after'      => 'uploads/' . $afterName,
                'answer'     => $answer,
                'genre'      => $genre,
                'difficulty' => $difficulty,
                'aliases'    => implode(',', $aliases)
            ]);
            $message = "✅ Merci, votre affiche a été soumise pour validation !";
        } else {
            $message = "❌ Erreur lors de l'upload des fichiers.";
        }
    } else {
        $message = "❌ Merci de remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Soumettre une affiche – POLCORN</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/admin-style.css">
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
<div class="admin-container fade">
    <h1>Proposer une affiche</h1>

    <?php if ($message): ?>
      <p style="color: <?= strpos($message, '✅') !== false ? 'lightgreen' : 'red' ?>; font-weight: bold;">
        <?= $message ?>
      </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="add-form">
        <input type="text" name="answer" placeholder="Nom du film" required>

        <select name="genre" required>
          <option value="">-- Genre --</option>
          <?php foreach($genres as $g): ?>
            <option value="<?= $g ?>"><?= $g ?></option>
          <?php endforeach; ?>
        </select>

        <select name="difficulty" required>
          <option value="">-- Difficulté --</option>
          <?php foreach($difficulties as $d): ?>
            <option value="<?= $d ?>"><?= $d ?></option>
          <?php endforeach; ?>
        </select>

        <!-- Aliases -->
        <div id="aliases-container">
          <input type="text" name="aliases[]" placeholder="Alias (optionnel)">
        </div>
        <button type="button" id="add-alias-btn" class="validate-btn">+ Ajouter un alias</button>

        <label>Affiche sans titre :</label>
        <input type="file" name="before" accept="image/*" required>

        <label>Affiche avec titre :</label>
        <input type="file" name="after" accept="image/*" required>

        <button type="submit">Soumettre</button>
    </form>
</div>
</div>
<script>
// Ajouter des champs d'alias dynamiques
document.getElementById('add-alias-btn')
  .addEventListener('click', () => {
    const cont = document.getElementById('aliases-container');
    const inp  = document.createElement('input');
    inp.type  = 'text';
    inp.name  = 'aliases[]';
    inp.placeholder = 'Alias (optionnel)';
    inp.style.marginTop = '8px';
    cont.appendChild(inp);
  });
</script>
<script src="/js/background.js"></script>
</body>
</html>
