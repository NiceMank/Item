<?php
    session_start();
    include('../config/database.php');
    if (isset($_POST['pub'])) {
        $pub = $_POST['pub'];
        $id = $_SESSION['user_id'];

        $stor = $conn->prepare("INSERT INTO publications (pub_text, id_user) VALUES (\"$pub\",'$id')");
        $stor->execute();
        header('Location: index.php');
    }else if (isset($_FILES['pub_img'])) {
        $image = $_FILES['pub_img']['name'];
        $id = $_SESSION['user_id'];
        
        $target_dir = "../upload/";
        $target_file = $target_dir . basename($image);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["pub_img"]["tmp_name"], $target_file);
        $img_fin = $target_file;
        $mod = $conn->prepare("INSERT INTO publications (pub_image, id_user) VALUES ( \"$img_fin\",'$id')");
        $mod->execute();
       header('Location: index.php');
    }
    // header('Location: index.php');
?>
