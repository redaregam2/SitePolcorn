<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false, 'error'=>'Non connecté']);
  exit;
}

$userId = $_SESSION['user']['id'];
$game   = $_POST['game'] ?? '';
$score  = intval($_POST['score'] ?? -1);

if (!in_array($game, ['devine_affiche', 'devine_emoji']) || $score < 0) {
  http_response_code(400);
  echo json_encode(['ok'=>false, 'error'=>'Paramètres invalides']);
  exit;
}

try {
  $db = new PDO(
    'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8',
    'u714302964_reda', 'Inzoumouda123*',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  // Vérifier ancien meilleur score
  $stmt = $db->prepare("
    SELECT score FROM user_game_sessions
    WHERE user_id = :uid AND game_scope = :game
    ORDER BY score DESC LIMIT 1
  ");
  $stmt->execute(['uid'=>$userId, 'game'=>$game]);
  $best = $stmt->fetchColumn();

  if ($best !== false && $best >= $score) {
    // Si le meilleur score est supérieur, ne pas enregistrer
    echo json_encode(['ok'=>false, 'error'=>'Score non amélioré']);
    exit;
  }

  // Sinon, on enregistre une nouvelle session
  $stmt = $db->prepare("
  INSERT INTO user_game_sessions (user_id, game_scope, score, correct, duration_ms, created_at)
  VALUES (:uid, :game, :score, :correct, :duration, NOW())
");
$stmt->execute([
  'uid'      => $userId,
  'game'     => $game,
  'score'    => $score,
  'correct'  => $_POST['correct'] ?? 0,   // si dispo
  'duration' => $_POST['duration_ms'] ?? 0
]);


  echo json_encode(['ok'=>true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'Erreur serveur']);
}
?>
