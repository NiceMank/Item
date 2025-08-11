<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('config.php');

$UserId = $_SESSION['user_id'];
// $autre_id = 3;
$autre_id = $_POST['autre_id'];
// Vérifier si la discussion existe déjà
$sql = "SELECT * FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $UserId, $autre_id, $autre_id, $UserId);
$stmt->execute();
$result = $stmt->get_result();
$did = $result->fetch_assoc();

if ($result->num_rows === 0) {
//     echo "non";
// }else {
    echo "oui";
}

?>