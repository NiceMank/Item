<?php
include('../config/database.php');

$PubId = $_GET['pub_id'];

$likes = $conn->query("SELECT * FROM likes WHERE id_pub = '$PubId'");
$nbr = $likes->num_rows;
if ($nbr !== 0) {
    echo $nbr;
}

?>