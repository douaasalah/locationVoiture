<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'locationvoitures');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$id_voiture = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'add';

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT IGNORE INTO favoris (id_client, id_voiture) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $id_voiture);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("DELETE FROM favoris WHERE id_client = ? AND id_voiture = ?");
    $stmt->bind_param("ii", $user_id, $id_voiture);
    $stmt->execute();
}

echo "ok";
exit();
?>