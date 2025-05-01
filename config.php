<?php
// config.php

// 1) Détection de l’environnement selon l’hôte
$host = $_SERVER['HTTP_HOST'] ?? php_uname('n');
$isLocal = preg_match('#^(localhost|127\.0\.0\.1)#', $host);

// 2) Paramètres de connexion
if ($isLocal) {
    // ===== En local =====
    $dbHost = '127.0.0.1';
    $dbName = 'u714302964_polcorn_db'; // Remplacez par le nom de votre base locale si différent
    $dbUser = 'root';
    $dbPass = '';
} else {
    // ===== En production =====
    $dbHost = 'localhost';
    $dbName = 'u714302964_polcorn_db';
    $dbUser = 'u714302964_reda';
    $dbPass = 'Inzoumouda123*';
}

// 3) Retourner les paramètres sous forme de tableau
return [
    'db_host' => $dbHost,
    'db_name' => $dbName,
    'db_user' => $dbUser,
    'db_pass' => $dbPass,
];
