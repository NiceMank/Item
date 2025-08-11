<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

$id = $_SESSION['user_id'];
// $PubId = 2;
// $name = "Farel Kant";
$PubId = $_POST['PubId'];
$name = $_POST['name'];

$likes = $conn->query("SELECT * FROM likes WHERE id_pub = '$PubId' and user_name = '$name'");
$nbr = $likes->num_rows;
if ($nbr < 1) {
    $stmtd = $conn->prepare("INSERT INTO likes (id_user, id_pub, user_name) VALUES ('$id', '$PubId', \"$name\")");
    if ($stmtd->execute()) {
        echo "Message 1 envoyé";
    } else {
        echo "Erreur: " . $stmtd->error;
    }
}else {
    $stmtd = $conn->prepare("DELETE FROM likes WHERE id_user = '$id' and id_pub = '$PubId'");
    if ($stmtd->execute()) {
        echo "Message 2 envoyé";
    } else {
        echo "Erreur: " . $stmtd->error;
    }
}


// Fermeture de la connexion
$stmtd->close();
$conn->close();
?>
