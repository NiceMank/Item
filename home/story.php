<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_check();
$userId = require_login($conn, '../connexion.php');

if (isset($_POST['story'])) {
    $text = clip_str((string) $_POST['story'], 500);
    if ($text === '') {
        flash_set('La story est vide.');
        header('Location: index.php');
        exit;
    }
    $conn->begin_transaction();
    $del = $conn->prepare('DELETE FROM storie WHERE id_uti = ?');
    $del->bind_param('i', $userId);
    $del->execute();
    $del->close();
    $image = '';
    $ins = $conn->prepare('INSERT INTO storie (story_text, story_image, id_uti) VALUES (?, ?, ?)');
    $ins->bind_param('ssi', $text, $image, $userId);
    $ins->execute();
    $ins->close();
    $conn->commit();
    header('Location: index.php');
    exit;
}

if (!empty($_FILES['story_img']['name'])) {
    $path = save_image_upload($_FILES['story_img']);
    if ($path === null) {
        flash_set('Image de story invalide.');
        header('Location: index.php');
        exit;
    }
    $conn->begin_transaction();
    $del = $conn->prepare('DELETE FROM storie WHERE id_uti = ?');
    $del->bind_param('i', $userId);
    $del->execute();
    $del->close();
    $text = '';
    $ins = $conn->prepare('INSERT INTO storie (story_text, story_image, id_uti) VALUES (?, ?, ?)');
    $ins->bind_param('ssi', $text, $path, $userId);
    $ins->execute();
    $ins->close();
    $conn->commit();
}

header('Location: index.php');
exit;
