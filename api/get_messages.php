<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

// Récupérer l'ID de l'utilisateur connecté
$currentUserId = $_SESSION['user_id'];
$receiverId = $_GET['receiver_id'];

// Récupérer les messages entre l'utilisateur actuel et le destinataire
$sql = "SELECT * FROM messages WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?) ORDER BY created_at ASC";
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
    // $sqle = "SELECT * FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)";
    // $stm = $conn->prepare($sqle);
    // $stm->bind_param("iiii", $currentUserId, $receiverId, $receiverId, $currentUserId);
    // $stm->execute();
    // $resultd = $stm->get_result();
    // $di = $resultd->fetch_assoc();
    // $disc = $di['etat'];
    $messageClass = ($row['id_moi'] == $currentUserId) ? 'moi' : 'autre';
    $title = ($row['id_moi'] == $currentUserId) ? 'Vous' : $tile;
    echo "<div class='mess {$messageClass}' title='{$title}'>";
    if ($row['id_moi'] == $currentUserId) {
        // rien
    }else {
        // echo "<img class=\"pro3\" src=\"".$profil."\">";
    }
    echo "<span>" . htmlspecialchars($row['message']) . "</span>";
    if ($row['id_moi'] == $currentUserId) {
        echo "<i class=\"fas fa-check un\"></i>";
        echo "<i class=\"fas fa-check deux\"></i>";
    }
    
    echo "</div>";
}
echo "<div id='desc'></div>";
// $_SESSION['statut_autre'] = null;

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
