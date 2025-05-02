<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Paris');

// On est déjà dans admin/, donc stats.json est ici
$statsFile = __DIR__ . '/stats.json';
if (!file_exists($statsFile)) {
    file_put_contents($statsFile, json_encode([
        'devine_affiche' => [],
        'devine_emoji'   => [],
        'devine_infini'  => [] // Ajout pour le jeu infini
    ], JSON_PRETTY_PRINT));
}
$stats = json_decode(file_get_contents($statsFile), true);

$game = $_REQUEST['game'] ?? '';
error_log("Game received: $game");

if (!in_array($game, ['devine_affiche', 'devine_emoji', 'devine_infini'])) {
    error_log("Invalid game: $game");
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Jeu non reconnu']);
    exit;
}

// Assurez-vous que la clé du jeu est un objet associatif
if (!isset($stats[$game]) || !is_array($stats[$game])) {
    $stats[$game] = [];
}

// Incrémentez le compteur pour la date du jour
$today = date('Y-m-d');
if (!isset($stats[$game][$today])) {
    $stats[$game][$today] = 0;
}
$stats[$game][$today]++;
error_log("Stats updated: " . json_encode($stats[$game]));

// Sauvegarde dans stats.json
file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));
echo json_encode(['ok' => true, 'game' => $game, 'date' => $today, 'count' => $stats[$game][$today]]);
