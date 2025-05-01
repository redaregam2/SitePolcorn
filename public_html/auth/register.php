<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 1) Connexion à la base
// Ancien
// Nouvelle connexion, avec le nom exact de ta base et charset utf8mb4 recommandé
$config = require __DIR__ . '/../../config.php';

$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// 2) Récupère et valide les champs
$email    = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
$pass     = $_POST['password'] ?? '';
$pass_cfm = $_POST['password_confirm'] ?? '';

if (!$email || !$pass || $pass !== $pass_cfm) {
  die('Erreur d’inscription : vérifiez vos saisies.');
}

// 3) Vérifie que l’email n’existe pas déjà
$stmt = $db->prepare("SELECT id FROM users WHERE email=:e");
$stmt->execute(['e'=>$email]);
if ($stmt->fetch()) {
  die('Un compte existe déjà avec cet e-mail.');
}

// 4) Hash du mot de passe et insertion
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $db->prepare("
  INSERT INTO users (email,password_hash,created_at)
  VALUES (:e,:h,NOW())
");
$stmt->execute(['e'=>$email,'h'=>$hash]);

// 5) Récupère l’ID et crée la session
$userId = $db->lastInsertId();
$_SESSION['user'] = [
  'id'    => $userId,
  'email' => $email,
  'pseudo'=> null
];

// 6) Redirection
header('Location: /mes-jeux.php');
exit;
