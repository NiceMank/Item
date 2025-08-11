<?php
include('../config/database.php');
session_start();
// Récupération des données POST
$lastName = $_POST['lastName'];
$firstName = $_POST['firstName'];

$genre = $_POST['genre'];
$birthdate = $_POST['birthdate'];
$job = $_POST['job'];
$workplace = $_POST['workplace'];
$relationshipStatus = $_POST['relationshipStatus'];
$userId = $_SESSION['user_id']; // Supposons que l'ID de l'utilisateur est stocké dans la session


$sql = "UPDATE users SET 
    nom = ?, 
    prenom = ?, 
    genre = ?, 
    date_nais = ?, 
    travail = ?, 
    Lieu_travail = ?, 
    situation = ? 
    WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $lastName, $firstName, $genre, $birthdate, $job, $workplace, $relationshipStatus, $userId);

if ($stmt->execute()) {
    header("Location: ../chats/");
} else {
    echo "Erreur: " . $stmt->error;
}

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
