<?php
session_start();
include('config.php');
$id = $_COOKIE['user_id'];
$sql = "UPDATE users SET etat_compte = '0' WHERE id = '$id'";
$stmt = $conn->prepare($sql);
$stmt->execute();
session_unset();
session_destroy();


setcookie("user_id", "", time() - 3600, "/");
?>
<script>
    window.location.href = "loading.php";
</script>