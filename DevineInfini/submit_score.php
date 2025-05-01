<?php
session_start();
header('Content-Type: application/json');
$lbFile = __DIR__ . '/leaderboard_infini.json';

// Init JSON local si besoin
if (!file_exists($lbFile)) {
    file_put_contents($lbFile, json_encode([], JSON_PRETTY_PRINT));
}

// 🔐 Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Utilisateur non connecté']);
    exit;
}

$userId = $_SESSION['user']['id'];
$pseudo = $_SESSION['user']['pseudo'];
$game   = $_POST['game'] ?? '';
$score  = intval($_POST['score'] ?? -1);

if ($game !== 'devine_infini' || $score < 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Paramètres invalides']);
    exit;
}

// 🔁 Enregistrement en base de données
try {
    $config = require __DIR__ . '/../../config.php';
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Insère score dans la table
    $stmt = $pdo->prepare("INSERT INTO scores (user_id, game, score) VALUES (?, ?, ?)");
    $stmt->execute([$userId, 'devine_infini', $score]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur BDD: ' . $e->getMessage()]);
    exit;
}

// 🔁 Gère aussi le JSON local
$board = json_decode(file_get_contents($lbFile), true);

foreach ($board as $i => $entry) {
    if ($entry['pseudo'] === $pseudo) {
        if ($entry['score'] >= $score) {
            echo json_encode(['ok' => true, 'note' => 'Score non amélioré']);
            exit;
        }
        array_splice($board, $i, 1);
        break;
    }
}

$board[] = [
    'pseudo' => $pseudo,
    'score'  => $score,
    'date'   => date('c')
];

usort($board, fn($a, $b) => $b['score'] - $a['score']);
$board = array_slice($board, 0, 50);

file_put_contents($lbFile, json_encode($board, JSON_PRETTY_PRINT));
echo json_encode(['ok' => true, 'message' => 'Score enregistré']);
