<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('config.php');

$currentUserId = $_SESSION['user_id'];
// $receiverId = 2;
// $message = "On dit quoi";
$receiverId = $_POST['receiver_id'];
$message = $_POST['message'];

// Vérifier si la discussion existe déjà


// Enregistrer le message
$stmtd = $conn->prepare("INSERT INTO messages (id_moi, id_autre, message, discussion_id) VALUES ('$currentUserId', '$receiverId', \"$message\" ,'$disc')");
if ($stmtd->execute()) {
    echo "Message envoyé";
} else {
    echo "Erreur: " . $stmt->error;
}

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
