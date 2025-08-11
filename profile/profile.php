<?php
session_start();

if (isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
} else {
    header("Location: connexion.php");
}
$user_id =  $_COOKIE['user_id'];
include('config.php');
// Préparer la requête pour récupérer les utilisateurs sauf l'utilisateur connecté
$stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$aff = $conn->prepare("SELECT * FROM users WHERE id = ?");
$aff->bind_param("i", $user_id);
$aff->execute();
$result = $aff->get_result();

$user_moi = $result->fetch_assoc();
$isChecked = ($user_moi['theme'] === 'sombre') ? 'checked' : '';
if ($user_moi['id'] == "") {
    header('Location: connexion.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farochat | Votre profil</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body class="<?php echo $user_moi['theme'] ?>">
    <?php
        include('header.php');
    ?>
</body>
</html>