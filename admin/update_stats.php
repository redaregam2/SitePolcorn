<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Paris');

// On est déjà dans admin/, donc stats.json est ici
$statsFile = __DIR__ . '/stats.json';
if (!file_exists($statsFile)) {
    file_put_contents($statsFile, json_encode([
        'devine_affiche'=>[],
        'devine_emoji'=>[]
    ], JSON_PRETTY_PRINT));
}
$stats = json_decode(file_get_contents($statsFile), true);

$game = $_REQUEST['game'] ?? '';
if (!in_array($game, ['devine_affiche', 'devine_emoji', 'devine_infini'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Jeu non reconnu']);
    exit;
}

$today = date('Y-m-d');
if (!isset($stats[$game][$today])) {
    $stats[$game][$today] = 0;
}
$stats[$game][$today]++;

file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));
echo json_encode(['ok'=>true,'game'=>$game,'date'=>$today,'count'=>$stats[$game][$today]]);
