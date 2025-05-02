<?php
$config = require __DIR__ . '/../../config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// CONFIG
$uploadDir    = __DIR__ . '/uploads/';
$jsonFile     = __DIR__ . '/emoji_games.json';
$games        = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$genres = ['Drame', 'Comédie', 'Action', 'SF', 'Horreur', 'Animation', 'Documentaire', 'Fantastique', 'Thriller'];
$difficulties = ['Facile','Normal','Expert'];

// SUPPRESSION
if (isset($_GET['delete'])) {
    $i = (int)$_GET['delete'];
    if (isset($games[$i])) {
        @unlink($uploadDir . basename($games[$i]['poster']));
        array_splice($games, $i, 1);
        file_put_contents($jsonFile, json_encode($games, JSON_PRETTY_PRINT));
    }
    header('Location: emoji_admin.php');
    exit;
}

// TRAITEMENT DU FORMULAIRE (ADD ou EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $index  = isset($_POST['index']) ? (int)$_POST['index'] : null;

    // Récupère et filtre les aliases depuis POST
    if ($action === 'add') {
        $rawAliases = $_POST['aliases'] ?? [];
    } elseif ($action === 'edit' && $index !== null) {
        $rawAliases = $_POST['aliases'][$index] ?? [];
    } else {
        $rawAliases = [];
    }
    $aliases = array_filter(array_map('trim', $rawAliases), fn($a) => $a !== '');

    // Toujours commun
    $e1    = trim($_POST['emoji1']);
    $e2    = trim($_POST['emoji2']);
    $e3    = trim($_POST['emoji3']);
    $title = trim($_POST['title']);
    $genre = $_POST['genre']      ?? '';
    $diff  = $_POST['difficulty'] ?? '';

    // Validation minimale
    if (!in_array($genre, $genres) || !in_array($diff, $difficulties)) {
        $error = "Genre ou difficulté invalide.";
    }
    else {
        // Pour ADD : on exige un poster. Pour EDIT : poster est optionnel.
        $needPoster = ($action === 'add');
        $hasPoster  = !empty($_FILES['poster']['name'])
                        && in_array($_FILES['poster']['type'], ['image/jpeg','image/png','image/webp']);
        if ($needPoster && !$hasPoster) {
            $error = "Choisissez une image jpg/png/webp.";
        }
    }

    // Si pas d’erreur, on peut traiter
    if (empty($error)) {
        // Fonction de déplacement de fichier
        $savePoster = function($tmp, $orig) use($uploadDir) {
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $fn  = uniqid('emo_') . ".$ext";
            move_uploaded_file($tmp, $uploadDir . $fn);
            return "uploads/$fn";
        };

        if ($action === 'add') {
            // ajoute une nouvelle entrée
            $posterPath = $savePoster($_FILES['poster']['tmp_name'], $_FILES['poster']['name']);
            $games[] = [
                'emojis'     => [$e1, $e2, $e3],
                'answer'     => $title,
                'poster'     => $posterPath,
                'aliases'    => $aliases,
                'genre'      => $genre,
                'difficulty' => $diff
            ];
        }
        elseif ($action === 'edit' && isset($games[$index])) {
            // met à jour l’existante
            $g = &$games[$index];
            $g['emojis']     = [$e1, $e2, $e3];
            $g['answer']     = $title;
            $g['aliases']    = $aliases;
            $g['genre']      = $genre;
            $g['difficulty'] = $diff;
            // si on a uploadé une nouvelle affiche, on remplace l’ancienne
            if (!empty($_FILES['poster']['tmp_name'])) {
                @unlink($g['poster']);
                $g['poster'] = $savePoster($_FILES['poster']['tmp_name'], $_FILES['poster']['name']);
            }
        }

        // Sauvegarde finale
        file_put_contents($jsonFile, json_encode($games, JSON_PRETTY_PRINT));
        header('Location: emoji_admin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin – Devine le film aux émojis</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/emoji-admin.css">
</head>
<body>
  <div class="admin-container">
    <h1>Admin – 🎬 Devine le film aux émojis</h1>
    <?php if(!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif ?>

    <!-- === FORMULAIRE D’AJOUT === -->
    <section class="add-form">
      <h2>Ajouter un jeu</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">

        <input type="text"   name="emoji1"     placeholder="Émoji 1" required maxlength="2">
        <input type="text"   name="emoji2"     placeholder="Émoji 2" required maxlength="2">
        <input type="text"   name="emoji3"     placeholder="Émoji 3" required maxlength="2">
        <input type="text"   name="title"      placeholder="Titre du film" required>

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

        <!-- Aliases dynamiques -->
        <div id="add-aliases-container">
          <input type="text" name="aliases[]" placeholder="Alias (optionnel)">
        </div>
        <button type="button" id="add-alias-btn" class="validate-btn">+ Ajouter un alias</button>

        <input type="file" name="poster" accept="image/*" required>
        <button type="submit">Ajouter</button>
      </form>
    </section>

    <!-- === LISTE DES JEUX === -->
    <section class="films-list">
      <h2>Jeux existants</h2>
      <?php if(empty($games)): ?>
        <p>Aucun jeu pour l’instant.</p>
      <?php else: ?>
        <?php foreach($games as $i => $g): ?>
          <div class="film-card fade">
            <h3>
              <?= htmlspecialchars($g['answer']) ?>
              <small>(<?= $g['genre'] ?> / <?= $g['difficulty'] ?>)</small>
            </h3>
            <?php if(!empty($g['aliases'])): ?>
              <p>Aliases : <?= implode(', ', array_map('htmlspecialchars',$g['aliases'])) ?></p>
            <?php endif ?>
            <div class="film-buttons">
              <button class="view-btn"
                      data-poster="<?= htmlspecialchars($g['poster']) ?>"
                      data-emojis="<?= htmlspecialchars(implode(' ',$g['emojis'])) ?>">
                Voir
              </button>
              <button class="edit-btn" onclick="openEdit(<?= $i ?>)">Modifier</button>
              <button class="delete-btn"
                      onclick="if(confirm('Supprimer ?')) location.href='?delete=<?= $i ?>'">
                Supprimer
              </button>
            </div>

            <!-- === FORMULAIRE D’ÉDITION === -->
            <form id="edit-form-<?= $i ?>"
                  class="edit-form hidden"
                  method="POST"
                  enctype="multipart/form-data">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="index"  value="<?= $i ?>">

              <input type="text" name="emoji1" value="<?= htmlspecialchars($g['emojis'][0]) ?>" required maxlength="2">
              <input type="text" name="emoji2" value="<?= htmlspecialchars($g['emojis'][1]) ?>" required maxlength="2">
              <input type="text" name="emoji3" value="<?= htmlspecialchars($g['emojis'][2]) ?>" required maxlength="2">
              <input type="text" name="title"  value="<?= htmlspecialchars($g['answer']) ?>" required>

              <select name="genre" required>
                <?php foreach($genres as $gn): ?>
                  <option value="<?= $gn ?>" <?= $g['genre']=== $gn?'selected':'' ?>>
                    <?= $gn ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <select name="difficulty" required>
                <?php foreach($difficulties as $df): ?>
                  <option value="<?= $df ?>" <?= $g['difficulty'] === $df?'selected':'' ?>>
                    <?= $df ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <!-- aliases existants pré-remplis -->
              <div id="edit-aliases-container-<?= $i ?>">
                <?php foreach($g['aliases'] as $al): ?>
                  <input type="text"
                         name="aliases[<?= $i ?>][]"
                         value="<?= htmlspecialchars($al) ?>">
                <?php endforeach; ?>
                <!-- un champ vide pour ajouter -->
                <input type="text"
                       name="aliases[<?= $i ?>][]"
                       placeholder="Alias (optionnel)">
              </div>
              <button type="button"
                      class="add-alias-btn"
                      data-index="<?= $i ?>">
                + Ajouter un alias
              </button>

              <label>Changer l’affiche :</label>
              <input type="file" name="poster" accept="image/*">

              <button type="submit">Enregistrer</button>
              <button type="button" onclick="closeEdit(<?= $i ?>)">Annuler</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>

  <!-- POP-UP DE PRÉVISU -->
  <div id="popup" class="popup hidden">
    <div class="popup-content fade">
      <span class="close" id="popup-close">&times;</span>
      <div id="popup-emojis" class="popup-emojis" style="font-size:2em; margin-bottom:10px;"></div>
      <img id="popup-poster" src="" alt="Affiche du film">
    </div>
  </div>

  <script>
    // ajouter un alias dans le formulaire d’ajout
    document.getElementById('add-alias-btn').onclick = () => {
      const cont = document.getElementById('add-aliases-container');
      const inp  = document.createElement('input');
      inp.name   = 'aliases[]';
      inp.placeholder = 'Alias (optionnel)';
      cont.appendChild(inp);
    };

    // ouvrir/fermer le popup “Voir”
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.onclick = () => {
        document.getElementById('popup-poster').src   = btn.dataset.poster;
        document.getElementById('popup-emojis').textContent = btn.dataset.emojis;
        document.getElementById('popup').classList.remove('hidden');
      };
    });
    document.getElementById('popup-close').onclick = () =>
      document.getElementById('popup').classList.add('hidden');
    document.getElementById('popup').onclick = e => {
      if (e.target === e.currentTarget) e.currentTarget.classList.add('hidden');
    };

    // ouvrir/fermer formulaire d’édition
    function openEdit(i) {
      document.getElementById(`edit-form-${i}`).classList.remove('hidden');
    }
    function closeEdit(i) {
      document.getElementById(`edit-form-${i}`).classList.add('hidden');
    }

    // ajouter un alias dans un formulaire d’édition
    document.querySelectorAll('.add-alias-btn').forEach(btn => {
      btn.onclick = () => {
        const i   = btn.dataset.index;
        const cont = document.getElementById(`edit-aliases-container-${i}`);
        const inp  = document.createElement('input');
        inp.name   = `aliases[${i}][]`;
        inp.placeholder = 'Alias (optionnel)';
        cont.appendChild(inp);
      };
    });
  </script>
</body>
</html>
