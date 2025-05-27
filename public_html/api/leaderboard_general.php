<?php
// filepath: public_html/api/leaderboard_general.php
header('Content-Type: application/json');
session_start();

$config = require __DIR__ . '/../../config.php';

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

// Récupère le meilleur score de chaque joueur pour chaque jeu
$stmt = $db->prepare("
    SELECT u.pseudo,
        MAX(CASE WHEN s.game_scope = 'devine_affiche' THEN s.score ELSE 0 END) as affiche,
        MAX(CASE WHEN s.game_scope = 'devine_emoji' THEN s.score ELSE 0 END) as emoji,
        MAX(CASE WHEN s.game_scope = 'devine_infini' THEN s.score ELSE 0 END) as infini
    FROM user_game_sessions s
    JOIN users u ON u.id = s.user_id
    WHERE u.pseudo IS NOT NULL AND u.pseudo != ''
    GROUP BY u.id
");
$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcule le total et trie
foreach ($scores as &$row) {
    $row['total'] = (int)$row['affiche'] + (int)$row['emoji'] + (int)$row['infini'];
}
usort($scores, fn($a, $b) => $b['total'] <=> $a['total']);

echo json_encode($scores);