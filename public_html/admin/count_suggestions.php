<?php
header('Content-Type: application/json');

// Connexion BD
$config = require __DIR__ . '/../../config.php';
$db = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Compte les suggestions
$stmt = $db->query("SELECT COUNT(*) FROM suggestions");
$count = (int) $stmt->fetchColumn();

// Répond en JSON
echo json_encode(['count' => $count]);
