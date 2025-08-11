<?php
    session_start();
    include('../config/database.php');
    if (isset($_POST['story'])) {
        $story = $_POST['story'];
        $id = $_SESSION['user_id'];

        $del = $conn->prepare("DELETE FROM storie WHERE id_uti = '$id'");
        $del->execute();
        $stor = $conn->prepare("INSERT INTO storie (story_text, id_uti) VALUES (\"$story\",'$id')");
        $stor->execute();
        header('Location: ../home/index.php');
    }else if (isset($_FILES['story_img'])) {
        $image = $_FILES['story_img']['name'];
        $id = $_SESSION['user_id'];
        
        $del = $conn->prepare("DELETE FROM storie WHERE id_uti = '$id'");
        $del->execute();
        $target_dir = "../upload/";
        $target_file = $target_dir . basename($image);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["story_img"]["tmp_name"], $target_file);
        $img_fin = $target_file;
        $mod = $conn->prepare("INSERT INTO storie (story_image, id_uti) VALUES (\"$img_fin\",'$id')");
        $mod->execute();
       header('Location: ../home/index.php');
    }
    header('Location: ../home/index.php');
?>
