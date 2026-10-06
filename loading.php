<?php
require_once __DIR__ . '/config/database.php';
if (!current_user_id($conn)) {
    header('Location: connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idem | Chargement</title>
    <link rel="stylesheet" href="assets/css/ios.css">
    <link rel="shortcut icon" href="assets/img/WeLogo1.png" type="image/x-icon">
</head>
<body class="page-load">
    <div class="auth-card load-card">
        <div class="brand-hero">
            <span class="brand-mark">I</span>
        </div>
        <h1>Idem</h1>
        <p class="sub">Ouverture de votre espace</p>
        <div class="spinner" role="status" aria-label="Chargement"></div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = "home/index.php";
        }, 800);
    </script>
</body>
</html>
