<?php
error_log("Données reçues dans record_session.php : " . print_r($_POST, true));
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
$currentCombo = isset($_POST['currentCombo']) ? (int)$_POST['currentCombo'] : 0;
// 1) Connexion BD
$config = require __DIR__ . '/../../config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
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
  if ($a['game_scope'] === 'devine_infini') {
    if ($a['threshold_type'] === 'boolean') {
        switch ($a['code']) {
            case 'combo_10':
              error_log("Combo actuel : $currentCombo");
                // Vérifie si le joueur a fait un combo de 10 bonnes réponses consécutives
                if ($currentCombo >= 10) $unlock = true;
                break;
            case 'fast_answer':
                if ($durationMs < 3000) $unlock = true;
                break;
            case '500_points':
                if ($score >= 500) $unlock = true;
                break;
            case 'top1':
                $top = $db->prepare("
                    SELECT COUNT(*) FROM user_game_sessions
                    WHERE game_scope = :g AND score > :s
                ");
                $top->execute(['g' => $game, 's' => $score]);
                $nbBetter = $top->fetchColumn();
                if ($nbBetter == 0) $unlock = true;
                break;
        }
    }
  } else {
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
