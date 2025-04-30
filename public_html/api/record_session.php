<?php
// /api/record_session.php
session_start();
if(empty($_SESSION['user']['id'])){
  http_response_code(401);
  echo json_encode(['error'=>'Non authentifié']); exit;
}
$userId     = $_SESSION['user']['id'];
$game       = $_POST['game']       ?? '';
$score      = (int)$_POST['score'];
$correct    = (int)$_POST['correct'];
$durationMs = (int)$_POST['duration_ms'];

// 1) Connexion BD
$db = new PDO(
  'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8mb4',
  'u714302964_reda','Inzoumouda123*',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);

// 2) Enregistrer la session
$stmt = $db->prepare("
  INSERT INTO user_game_sessions
    (user_id,game_scope,score,correct,duration_ms)
  VALUES
    (:u,:g,:sc,:co,:du)
");
$stmt->execute([
  'u'=>$userId,'g'=>$game,
  'sc'=>$score,'co'=>$correct,'du'=>$durationMs
]);

// 3) Détection des nouveaux trophées
$new = [];
$achs = $db->prepare("SELECT * FROM achievements WHERE game_scope=:g");
$achs->execute(['g'=>$game]);

foreach($achs->fetchAll(PDO::FETCH_ASSOC) as $a){
  // vérifier si déjà débloqué
  $ck = $db->prepare("
    SELECT 1 FROM user_achievements
     WHERE user_id=:u AND achievement_id=:aid
  ");
  $ck->execute(['u'=>$userId,'aid'=>$a['id']]);
  if($ck->fetch()) continue;

  $unlock = false;
  if($a['threshold_type']=='boolean'){
    switch($a['code']){
      case 'fast_answer':
        if($durationMs < 3000) $unlock = true; break;
      case 'perfect_score':
        if($correct == 10) $unlock = true; break;
      case '500_points':
        if($score >= 500) $unlock = true; break;
      case 'top1':
  $top = $db->prepare("
    SELECT COUNT(*) FROM user_game_sessions
    WHERE game_scope = :g AND score > :s
  ");
  $top->execute(['g'=>$game, 's'=>$score]);
  $nbBetter = $top->fetchColumn();
  if ($nbBetter == 0) $unlock = true;
  break;

    }
  } else { // count
    $cnt = $db->prepare("
      SELECT COUNT(*) FROM user_game_sessions
      WHERE user_id=:u AND game_scope=:g
    ");
    $cnt->execute(['u'=>$userId,'g'=>$game]);
    if($cnt->fetchColumn() >= $a['threshold_value']) $unlock = true;
  }

  if($unlock){
    $db->prepare("
      INSERT INTO user_achievements (user_id,achievement_id)
      VALUES (:u,:aid)
    ")->execute(['u'=>$userId,'aid'=>$a['id']]);
    $new[] = ['name'=>$a['name'],'desc'=>$a['description']];
  }
}

// 4) Réponse JSON
header('Content-Type: application/json');
echo json_encode(['new_achievements'=>$new]);
