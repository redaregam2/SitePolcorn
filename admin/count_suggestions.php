<?php
header('Content-Type: application/json');

// Connexion BD
$db = new PDO(
  'mysql:host=localhost;dbname=u714302964_polcorn_db;charset=utf8',
  'u714302964_reda',
  'Inzoumouda123*',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Compte les suggestions
$stmt = $db->query("SELECT COUNT(*) FROM suggestions");
$count = (int) $stmt->fetchColumn();

// Répond en JSON
echo json_encode(['count' => $count]);
