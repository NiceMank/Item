<?php
include('../config/database.php');

$id_pub = $_GET['pub_id'];

$user_name = $_GET['name'];
$tru = $conn->query("SELECT * FROM likes WHERE id_pub = '$id_pub' and user_name = '$user_name'");
$nbr_tru = $tru->num_rows;
if ($nbr_tru !== 0) {
    echo "true";
}
?>