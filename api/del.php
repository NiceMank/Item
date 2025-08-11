<?php
session_start();
include('../config/database.php');
$id = $_COOKIE['user_id'];
$sql = "DELETE FROM users  WHERE id = '$id'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$sql = "DELETE FROM discussion WHERE id_moi = '$id' OR id_autre = '$id'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$sql = "DELETE FROM messages WHERE id_moi = '$id' OR id_autre = '$id'";
$stmt = $conn->prepare($sql);
$stmt->execute();
session_unset();
session_destroy();


setcookie("user_id", "", time() - 3600, "/");
?>
<script>
    window.location.href = "../loading.php";
</script>