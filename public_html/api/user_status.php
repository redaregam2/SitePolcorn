<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user']) && isset($_SESSION['user']['pseudo'])) {
  echo json_encode([
    'logged_in' => true,
    'pseudo'    => $_SESSION['user']['pseudo'],
    'user_id'   => $_SESSION['user']['id'] ?? null
  ]);
} else {
  echo json_encode([
    'logged_in' => false
  ]);
}
?>
