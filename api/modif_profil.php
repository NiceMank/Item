<?php
session_start();
include('../config/database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileTmpPath = $_FILES['image']['name'];
    $target_dir = "../upload/";
    $id = $_COOKIE['user_id'];
    $target_file = $target_dir . basename($fileTmpPath);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    $img_fin = $target_file;
    $mod = $conn->prepare("UPDATE users SET profil = \"$img_fin\" WHERE id = ? ");
    $mod->execute(array($id));
    echo json_encode(['success' => true]);
    header("Location: ../chats/");
}
?>
