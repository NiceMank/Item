<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

// Récupérer l'ID de l'utilisateur connecté
$currentUserId = $_SESSION['user_id'];
$recher = $_GET['search'];

// Récupérer les messages entre l'utilisateur actuel et le destinataire
$sql = "SELECT * FROM recette WHERE ? LIKE nom OR ? LIKE prenom";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $currentUserId, $receiverId, $receiverId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$sqle = "SELECT * FROM users WHERE id = ?";
$stm = $conn->prepare($sqle);
$stm->bind_param("i",$receiverId);
$stm->execute();
$resulte = $stm->get_result();
$resul = $resulte->fetch_assoc();
if ($resul['profil'] == "") {
    $profil = "../assets/img/profile.png";
}else {
    $profil = $resul['profil'];
}
$tile = $resul['nom']." ".$resul['prenom'];
while ($row = $result->fetch_assoc()) {
    $messageClass = ($row['id_moi'] == $currentUserId) ? 'moi' : 'autre';
    $title = ($row['id_moi'] == $currentUserId) ? 'moi' : $tile;
    echo "<div class='mess {$messageClass}' title='{$title}'>";
    if ($row['id_moi'] == $currentUserId) {
        // rien
    }else {
        echo "<img class=\"pro3\" src=\"".$profil."\">";
    }
    echo "<span>" . htmlspecialchars($row['message']) . "</span>";
    echo "</div>";
}

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
