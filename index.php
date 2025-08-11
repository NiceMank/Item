<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header("Location: connexion.php");
    }else {
        header("Location: home");
    }
?>