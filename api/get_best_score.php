<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
  echo json_encode(['ok'=>false]);
  exit;
}

$userId = $_SESSION['user']['id'];
$game   = $_GET['game'] ?? '';

if (!in_array($game, ['devine_affiche', 'devine_emoji', 'devine_infini'])) {

  echo json_encode(['ok'=>false]);
  exit;
}

try {
  $config = require __DIR__ . '/../../config.php';
  $db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  $stmt = $db->prepare("
    SELECT MAX(score) FROM user_game_sessions
    WHERE user_id = :uid AND game_scope = :game
  ");
  $stmt->execute(['uid'=>$userId, 'game'=>$game]);
  $best = (int) $stmt->fetchColumn();

  echo json_encode(['ok'=>true, 'best'=>$best]);
} catch (PDOException $e) {
  echo json_encode(['ok'=>false]);
}
?>
