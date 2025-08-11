<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

$id = $_SESSION['user_id'];
// $receiverId = 2;
// $message = "On dit quoi";
$PubId = $_POST['PubId'];
$comment = $_POST['comment'];
// Enregistrer le message
$stmtd = $conn->prepare("INSERT INTO commentaires (id_user, id_pub, commentaire) VALUES ('$id', '$PubId', \"$comment\")");
if ($stmtd->execute()) {
    echo "Message envoyé";
} else {
    echo "Erreur: " . $stmt->error;
}

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
