<?php
session_start();
$conn = new mysqli('localhost', 'root', 'root', 'locationvoitures');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_voiture = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM favoris WHERE id_client = ? AND id_voiture = ?");
$stmt->bind_param("ii", $user_id, $id_voiture);
$stmt->execute();

header("Location: profil.php");
exit();
?>