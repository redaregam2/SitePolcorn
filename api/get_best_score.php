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
  $db = new PDO(
    'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8',
    'u714302964_reda', 'Inzoumouda123*',
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
