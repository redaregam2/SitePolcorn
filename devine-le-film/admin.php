<?php
session_start();

// Définir le mot de passe
if (!defined('ADMIN_PASSWORD')) {
    define('ADMIN_PASSWORD', '1234');
}

// Listes de genres et niveaux
$genres = ['Drame','Comédie','Action','SF','Horreur','Animation','Documentaire'];
$difficulties = ['Facile','Normal','Expert'];

// Authentification
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isset($_POST['password']) && $_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        // Formulaire de connexion
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Connexion Admin</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="css/admin-style.css">
        </head>
        <body class="login-page">
            <div class="admin-container">
                <h2>Connexion Admin</h2>
                <form method="POST">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <button type="submit">Se connecter</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Chargement des données
$filmsFile = 'films.json';
$films = file_exists($filmsFile)
    ? json_decode(file_get_contents($filmsFile), true)
    : [];

// Traitement des actions POST
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $index  = isset($_POST['index']) ? (int)$_POST['index'] : null;

    // Récupération et nettoyage des alias soumis
    $rawAliases = $_POST['aliases'] ?? [];
    $aliases = array_filter(array_map('trim', $rawAliases), function($a){
      return $a !== '';
    });

    // Ajouter un film
    if ($action === 'add') {
        $extB = pathinfo($_FILES['before']['name'], PATHINFO_EXTENSION);
        $extA = pathinfo($_FILES['after']['name'],  PATHINFO_EXTENSION);
        $beforeName = uniqid() . '_before.' . $extB;
        $afterName  = uniqid() . '_after.'  . $extA;

        move_uploaded_file($_FILES['before']['tmp_name'], 'uploads/' . $beforeName);
        move_uploaded_file($_FILES['after']['tmp_name'],  'uploads/' . $afterName);

        $films[] = [
            'before'     => 'uploads/' . $beforeName,
            'after'      => 'uploads/'  . $afterName,
            'answer'     => trim($_POST['answer']),
            'genre'      => $_POST['genre'],
            'difficulty' => $_POST['difficulty'],
            'aliases'    => $aliases
        ];
        file_put_contents($filmsFile, json_encode($films, JSON_PRETTY_PRINT));
        header("Location: admin.php");
        exit();
    }

    // Modifier un film existant
    if ($action === 'edit' && $index !== null && isset($films[$index])) {
        // Mettre à jour champs texte
        $films[$index]['answer']     = trim($_POST['answer']);
        $films[$index]['genre']      = $_POST['genre'];
        $films[$index]['difficulty'] = $_POST['difficulty'];
        // Mettre à jour les alias
        $films[$index]['aliases']    = $aliases;
        // Si nouvelles images uploadées, remplacer
        if (!empty($_FILES['before']['tmp_name'])) {
            @unlink($films[$index]['before']);
            $ext = pathinfo($_FILES['before']['name'], PATHINFO_EXTENSION);
            $bn = uniqid() . '_before.' . $ext;
            move_uploaded_file($_FILES['before']['tmp_name'], 'uploads/' . $bn);
            $films[$index]['before'] = 'uploads/' . $bn;
        }
        if (!empty($_FILES['after']['tmp_name'])) {
            @unlink($films[$index]['after']);
            $ext = pathinfo($_FILES['after']['name'], PATHINFO_EXTENSION);
            $an = uniqid() . '_after.' . $ext;
            move_uploaded_file($_FILES['after']['tmp_name'], 'uploads/' . $an);
            $films[$index]['after'] = 'uploads/' . $an;
        }
        file_put_contents($filmsFile, json_encode($films, JSON_PRETTY_PRINT));
        header("Location: admin.php");
        exit();
    }

    // Supprimer un film
    if ($action === 'delete' && $index !== null && isset($films[$index])) {
        @unlink($films[$index]['before']);
        @unlink($films[$index]['after']);
        array_splice($films, $index, 1);
        file_put_contents($filmsFile, json_encode($films, JSON_PRETTY_PRINT));
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Gestion Films</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<div class="admin-container fade">
    <h1>Gestion des Films</h1>

    <!-- Formulaire d'ajout -->
    <h2>Ajouter un Film</h2>
    <form method="POST" enctype="multipart/form-data" class="add-form">
        <input type="hidden" name="action" value="add">

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
        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste et édition -->
    <h2>Films existants</h2>
    <div class="films-list">
      <?php foreach ($films as $i => $film): ?>
        <div class="film-card fade">
          <h3>
            <?= htmlspecialchars($film['answer']) ?>
            <small>(<?= htmlspecialchars($film['genre']) ?> / <?= htmlspecialchars($film['difficulty']) ?>)</small>
          </h3>

          <?php if (!empty($film['aliases'])): ?>
            <p><em>Alias acceptés :</em>
              <?= implode(', ', array_map('htmlspecialchars', $film['aliases'])) ?>
            </p>
          <?php endif; ?>

          <div class="film-buttons">
            <button onclick="openViewPopup(
              '<?= $film['before'] ?>',
              '<?= $film['after'] ?>'
            )">Voir</button>
            <button onclick="openEdit(<?= $i ?>)">Modifier</button>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="index" value="<?= $i ?>">
              <button type="submit" class="delete-btn">Supprimer</button>
            </form>
          </div>

          <!-- Formulaire de modification (caché) -->
          <form id="edit-form-<?= $i ?>" class="edit-form hidden" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="index"  value="<?= $i ?>">

            <input type="text" name="answer"     value="<?= htmlspecialchars($film['answer']) ?>" required>
            <select name="genre" required>
              <?php foreach($genres as $g): ?>
                <option value="<?= $g ?>" <?= $film['genre']===$g?'selected':'' ?>>
                  <?= $g ?>
                </option>
              <?php endforeach; ?>
            </select>
            <select name="difficulty" required>
              <?php foreach($difficulties as $d): ?>
                <option value="<?= $d ?>" <?= $film['difficulty']===$d?'selected':'' ?>>
                  <?= $d ?>
                </option>
              <?php endforeach; ?>
            </select>

            <!-- Aliases existants et nouveaux -->
            <div id="edit-aliases-container-<?= $i ?>">
              <?php if (!empty($film['aliases'])): ?>
                <?php foreach($film['aliases'] as $alias): ?>
                  <input type="text"
                         name="aliases[]"
                         value="<?= htmlspecialchars($alias) ?>"
                         placeholder="Alias (optionnel)">
                <?php endforeach; ?>
              <?php else: ?>
                <input type="text" name="aliases[]" placeholder="Alias (optionnel)">
              <?php endif; ?>
            </div>
            <button type="button"
                    class="add-alias-btn-edit"
                    data-index="<?= $i ?>">
              + Ajouter un alias
            </button>

            <label>Changer affiche sans titre :</label>
            <input type="file" name="before" accept="image/*">
            <label>Changer affiche avec titre :</label>
            <input type="file" name="after"  accept="image/*">

            <button type="submit">Enregistrer</button>
            <button type="button" onclick="closeEdit(<?= $i ?>)">Annuler</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
</div>

<!-- Pop-up de prévisualisation -->
<div id="view-popup" class="popup hidden">
  <div class="popup-content fade">
    <span class="close" onclick="closeViewPopup()">&times;</span>
    <h2>Affiches</h2>
    <div class="popup-images">
      <img id="before-img" src="" alt="Avant">
      <img id="after-img"  src="" alt="Après">
    </div>
  </div>
</div>

<script>
// Visionnage
function openViewPopup(before, after) {
  document.getElementById('before-img').src = before;
  document.getElementById('after-img').src  = after;
  document.getElementById('view-popup').classList.remove('hidden');
}
function closeViewPopup() {
  document.getElementById('view-popup').classList.add('hidden');
}

// Édition
function openEdit(i) {
  document.getElementById(`edit-form-${i}`).classList.remove('hidden');
}
function closeEdit(i) {
  document.getElementById(`edit-form-${i}`).classList.add('hidden');
}

// Ajout d’alias dans le formulaire d’ajout
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

// Ajout d’alias dans chaque formulaire de modif
document.querySelectorAll('.add-alias-btn-edit')
  .forEach(btn => btn.addEventListener('click', () => {
    const idx = btn.dataset.index;
    const cont = document.getElementById(`edit-aliases-container-${idx}`);
    const inp  = document.createElement('input');
    inp.type  = 'text';
    inp.name  = 'aliases[]';
    inp.placeholder = 'Alias (optionnel)';
    inp.style.marginTop = '8px';
    cont.appendChild(inp);
}));
</script>
</body>
</html>
