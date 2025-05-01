<?php
header('Content-Type: application/json');
session_start();

$config = require __DIR__ . '/../../config.php';

// Vérifie que 'game' est fourni
if (!isset($_GET['game']) || !in_array($_GET['game'], ['devine_affiche', 'devine_emoji', 'devine_infini'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre game invalide']);
    exit;
}

$game = $_GET['game'];

try {
    $db = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur connexion DB']);
    exit;
}

// Récupère le meilleur score de chaque joueur
$stmt = $db->prepare("
    SELECT u.pseudo, MAX(s.score) as best_score
    FROM user_game_sessions s
    JOIN users u ON u.id = s.user_id
    WHERE s.game_scope = :game
      AND u.pseudo IS NOT NULL
      AND u.pseudo != ''
    GROUP BY u.id
    ORDER BY best_score DESC
    LIMIT 50
");
$stmt->execute(['game' => $game]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reformate pour correspondre à ce qu’attend le JS
$result = array_map(function($row) {
    return [
        'pseudo' => $row['pseudo'],
        'score'  => (int)$row['best_score']
    ];
}, $scores);

echo json_encode($result);
