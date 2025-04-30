<?php
header('Content-Type: application/json');

$lbFile = __DIR__ . '/leaderboard_infini.json';

// Si le fichier n'existe pas, on retourne un tableau vide
if (!file_exists($lbFile)) {
    echo json_encode([]);
    exit;
}

// Lecture et parsing du fichier JSON
$board = json_decode(file_get_contents($lbFile), true);

// Sécurité : on ne renvoie que pseudo + score
$cleanBoard = array_map(fn($entry) => [
    'pseudo' => $entry['pseudo'],
    'score'  => $entry['score']
], $board);

// Réponse
echo json_encode($cleanBoard);
