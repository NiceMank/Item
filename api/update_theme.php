<?php
// Démarrer la session
session_start();

include('../config/database.php');
// Supposons que l'ID de l'utilisateur est stocké dans la session
$userId = $_SESSION['user_id'];

// Récupérer le nouveau thème depuis la requête GET
$newTheme = $_GET['theme'];

// Mettre à jour le thème de l'utilisateur
$sql = "UPDATE users SET theme = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newTheme, $userId);

if ($stmt->execute()) {
    // Rediriger vers la page principale après la mise à jour
    header("Location: ../chats/");
} else {
    echo "Erreur: " . $stmt->error;
}

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
