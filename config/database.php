<?php
    
    $servername = "localhost";
    $username = "root"; // changez selon votre configuration
    $password = ""; // changez selon votre configuration
    $dbname = "messagerie";
    
    // Créer la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);
    date_default_timezone_set('Africa/Porto-Novo');
?>