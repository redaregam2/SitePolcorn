<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
  'pseudo' => $_SESSION['user']['pseudo'] ?? ''
]);
