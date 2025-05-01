<?php
// config.php

// 1) Détection de l’environnement selon l’hôte
$host = $_SERVER['HTTP_HOST'] ?? php_uname('n');
$isLocal = preg_match('#^(localhost|127\.0\.0\.1)#', $host);

// 2) Paramètres de connexion
if ($isLocal) {
    // ===== En local (XAMPP, Codespaces…) =====
    $dbHost = '127.0.0.1';
    $dbName = 'u714302964_polcorn_db';
    $dbUser = 'root';
    $dbPass = '';
} else {
    // ===== En production (Hostinger) =====
    $dbHost = 'localhost';
    $dbName = 'u714302964_polcorn_db';
    $dbUser = 'u714302964_reda';
    $dbPass = 'Inzoumouda123*';
}

// 3) Initialiser PDO
try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // En local on peut afficher l’erreur, en prod on logue et on affiche un message générique
    if ($isLocal) {
        die("Erreur de connexion à la base (local) : " . $e->getMessage());
    } else {
        error_log("DB prod error: " . $e->getMessage());
        die("Impossible de se connecter à la base de données.");
    }
}
