<?php
// Affiche les erreurs en dev (optionnel)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
session_start();

$client = new Google_Client();
// Chemin corrigé vers ton JSON dans cr/
$client->setAuthConfig(__DIR__ . '/../cr/client_secret_853377329007-5renni3s7mrp7uht35q45jo34ke0k9dt.apps.googleusercontent.com.json');
$client->setRedirectUri('https://polcorn.com/auth/google-callback.php');
$client->addScope(['openid','email','profile']);

// Puis le reste de ton code...
header('Location: '.$client->createAuthUrl());
exit;
