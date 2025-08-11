<?php
// Configurer la connexion à la base de données

include('../config/database.php');
session_start();

if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = '../upload/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        // Mise à jour de la base de données avec le chemin du fichier téléchargé
        $userId = $_SESSION['user_id']; // Remplacez par l'ID de l'utilisateur actuel
        $stmt = $conn->prepare('UPDATE users SET profil = ? WHERE id = ?');
        if ($stmt->execute([$uploadFile, $userId])) {
            header("Location: ../chats/");
        } else {
            echo 'Failed to update the database!';
        }
    } else {
        echo 'Failed to move uploaded file!';
    }
} else {
    echo 'File upload error!';
}
// header('Location: loading.php');
?>
