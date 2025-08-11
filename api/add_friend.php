<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

$UserId = $_SESSION['user_id'];
$autre_id = $_POST['autre_id'];

// Vérifier si la discussion existe déjà
$sql = "SELECT * FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $UserId, $autre_id, $autre_id, $UserId);
$stmt->execute();
$result = $stmt->get_result();
$did = $result->fetch_assoc();

if ($result->num_rows === 0) {
    // Insérer une nouvelle discussion
    
    $sql = "INSERT INTO discussion (id_moi, id_autre) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $UserId, $autre_id);
    $stmt->execute();

    $sqle = "SELECT * FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)";
    $stm = $conn->prepare($sqle);
    $stm->bind_param("iiii", $UserId, $autre_id, $autre_id, $UserId);
    $stm->execute();
    $resultd = $stm->get_result();
    $di = $resultd->fetch_assoc();
    $disc = $di['id'];
    echo "non";
}else {
    $sqle = "DELETE  FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)";
    $stm = $conn->prepare($sqle);
    $stm->bind_param("iiii", $UserId, $autre_id, $autre_id, $UserId);
    $stm->execute();
    
    echo "oui";
}

?>