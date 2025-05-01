<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 1) Connexion à la base
$config = require __DIR__ . '/../../config.php';

$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// 2) Récupère et valide
$email = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
$pass  = $_POST['password'] ?? '';
if (!$email || !$pass) {
  die('Champ manquant.');
}

// 3) Récupère l’utilisateur
$stmt = $db->prepare("
  SELECT id, email, password_hash, pseudo
  FROM users
  WHERE email=:e
");
$stmt->execute(['e'=>$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($pass, $user['password_hash'])) {
  die('E-mail ou mot de passe incorrect.');
}

// 4) Démarre la session
$_SESSION['user'] = [
  'id'      => $user['id'],
  'email'   => $user['email'],
  'pseudo'  => $user['pseudo']
];

// 5) Redirection
header('Location: /mes-jeux.php');
exit;
