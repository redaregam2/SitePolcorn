<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';
session_start();

$client = new Google_Client();
// Même chemin corrigé ici aussi
$client->setAuthConfig(__DIR__ . '/../cr/client_secret_853377329007-5renni3s7mrp7uht35q45jo34ke0k9dt.apps.googleusercontent.com.json');
$client->setRedirectUri('https://polcorn.com/auth/google-callback.php');
$client->addScope(['openid','email','profile']);

if (!isset($_GET['code'])) {
  header('Location: /');
  exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

$oauth2 = new Google_Service_Oauth2($client);
$info   = $oauth2->userinfo->get();
$email   = $info->email;
$name    = $info->name;
$picture = $info->picture;

// Connexion à la base MySQL
// Nouveaux identifiants Hostinger
$dbHost     = 'localhost';                   // en général localhost sur Hostinger
$dbName     = 'u714302964_polcorn_db';       // nom de la base
$dbUser     = 'u714302964_reda';             // utilisateur MySQL
$dbPassword = 'Inzoumouda123*';              // mot de passe

try {
    $db = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8",
        $dbUser,
        $dbPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    exit('Erreur de connexion à la base : ' . $e->getMessage());
}

// Upsert user
$stmt = $db->prepare("SELECT id,pseudo FROM users WHERE email=:e");
$stmt->execute(['e'=>$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  // Insérer sans pseudo (l’utilisateur le renseignera plus tard)
  $stmt = $db->prepare("INSERT INTO users (email,name,picture,created_at) VALUES (:e,:n,:p,NOW())");
  $stmt->execute(['e'=>$email,'n'=>$name,'p'=>$picture]);
  $userId = $db->lastInsertId();
  $pseudo = null;
} else {
  $userId = $user['id'];
  $pseudo = $user['pseudo'];
}

$_SESSION['user'] = [
  'id'      => $userId,
  'email'   => $email,
  'name'    => $name,
  'picture' => $picture,
  'pseudo'  => $pseudo
];

// Si pas de pseudo, on redirige vers page pour le choisir
if (!$pseudo) {
  header('Location: /mon_compte.php');
} else {
  header('Location: /mes-jeux.php');
}
exit;
