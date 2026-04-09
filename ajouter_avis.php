<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'locationvoitures');
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=home.php");
    exit;
}

// Vérifier que la requête est bien POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: home.php");
    exit;
}

// Récupérer et nettoyer les données du formulaire
$nom = trim($_POST['nom'] ?? '');
$ville = trim($_POST['ville'] ?? '');
$commentaire = trim($_POST['commentaire'] ?? '');
$note = intval($_POST['note'] ?? 5);

// Validation de base
if (empty($nom) || empty($ville) || empty($commentaire)) {
    header("Location: home.php?error=champs_vides");
    exit;
}

// Note doit être entre 1 et 5
if ($note < 1 || $note > 5) {
    $note = 5;
}

// Insertion en base de données avec requête préparée
$stmt = $conn->prepare("INSERT INTO avis (nom, ville, commentaire, note, date_creation) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("sssi", $nom, $ville, $commentaire, $note);

if ($stmt->execute()) {
    header("Location: home.php?success=avis_ajoute");
} else {
    header("Location: home.php?error=erreur_insertion");
}

$stmt->close();
$conn->close();
?>