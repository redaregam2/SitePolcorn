<?php
header('Content-Type: application/json');
$lbFile = __DIR__ . '/leaderboard.json';
// Init si besoin
if (!file_exists($lbFile)) {
    file_put_contents($lbFile, json_encode([], JSON_PRETTY_PRINT));
}

// Récup POST
$pseudo = trim($_POST['pseudo'] ?? '');
$game   = $_POST['game']      ?? '';
$score  = intval($_POST['score'] ?? -1);

// Validation
if ($game !== 'devine_emoji' || !$pseudo || $score < 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Paramètres invalides']);
    exit;
}

// Charge le classement
$board = json_decode(file_get_contents($lbFile), true);

// Cherche et compare ancien score
foreach ($board as $i => $entry) {
    if ($entry['pseudo'] === $pseudo) {
        if ($entry['score'] >= $score) {
            // n’améliore pas ⇒ rien ne change
            echo json_encode(['ok'=>false,'error'=>'Score non amélioré']);
            exit;
        }
        // supprime l’ancien
        array_splice($board, $i, 1);
        break;
    }
}

// Ajoute le nouveau
$board[] = [
    'pseudo' => $pseudo,
    'score'  => $score,
    'date'   => date('c')
];

// Trie et coupe à 50
usort($board, fn($a,$b)=> $b['score'] - $a['score']);
$board = array_slice($board, 0, 50);

// Enregistre
file_put_contents($lbFile, json_encode($board, JSON_PRETTY_PRINT));
echo json_encode(['ok'=>true]);
