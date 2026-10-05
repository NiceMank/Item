<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_check();
$userId = require_login($conn, '../connexion.php');

if (isset($_POST['pub'])) {
    $text = clip_str((string) $_POST['pub'], 5000);
    if ($text === '') {
        flash_set('Écrivez quelque chose avant de publier.');
        header('Location: index.php');
        exit;
    }
    $image = '';
    $stmt = $conn->prepare('INSERT INTO publications (pub_text, pub_image, id_user) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $text, $image, $userId);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit;
}

if (!empty($_FILES['pub_img']['name'])) {
    $path = save_image_upload($_FILES['pub_img']);
    if ($path === null) {
        flash_set('Image invalide. Formats acceptés : JPG, PNG, GIF, WEBP (5 Mo max).');
        header('Location: index.php');
        exit;
    }
    $text = '';
    $stmt = $conn->prepare('INSERT INTO publications (pub_text, pub_image, id_user) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $text, $path, $userId);
    $stmt->execute();
    $stmt->close();
}

header('Location: index.php');
exit;
