<?php
header('Content-Type: application/json');

$lbFile = __DIR__ . '/leaderboard.json';
// 1) Initialise si nécessaire
if (!file_exists($lbFile)) {
    file_put_contents($lbFile, json_encode([], JSON_PRETTY_PRINT));
}

// 2) Récupère POST
$pseudo = trim($_POST['pseudo']   ?? '');
$game   = $_POST['game']          ?? '';
$score  = intval($_POST['score']  ?? -1);

// 3) Valide les paramètres
if ($game !== 'devine_affiche' || !$pseudo || $score < 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Paramètres invalides']);
    exit;
}

// 4) Charge l’ancien leaderboard
$board = json_decode(file_get_contents($lbFile), true);

// 5) Cherche si ce pseudo existe déjà
$existingIndex = null;
foreach ($board as $i => $entry) {
    if ($entry['pseudo'] === $pseudo) {
        $existingIndex = $i;
        break;
    }
}

// 6) Si le joueur existait et que son ancien score est >= nouveau, on ne fait rien
if ($existingIndex !== null && $board[$existingIndex]['score'] >= $score) {
    // Renvoie ok=false pour signaler qu'il n'y a pas eu de mise à jour
    echo json_encode(['ok'=>false,'error'=>'Score non amélioré, ancienne valeur conservée']);
    exit;
}

// 7) Sinon, on supprime l’ancienne entrée (si elle existait)
if ($existingIndex !== null) {
    array_splice($board, $existingIndex, 1);
}

// 8) On ajoute la nouvelle entrée
$board[] = [
    'pseudo' => $pseudo,
    'score'  => $score,
    'date'   => date('c')
];

// 9) Trie par score décroissant et limite à 50
usort($board, fn($a,$b) => $b['score'] - $a['score']);
$board = array_slice($board, 0, 50);

// 10) Sauvegarde et renvoie ok
file_put_contents($lbFile, json_encode($board, JSON_PRETTY_PRINT));
echo json_encode(['ok'=>true]);
