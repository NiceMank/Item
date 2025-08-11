<?php
session_start();
include('config/database.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparer et lier
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Vérifier si l'utilisateur existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        // Vérifier le mot de passe
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            setcookie("user_id", $id, time() + (86400), "/");
            $sql = "UPDATE users SET etat_compte = '1' WHERE id = '$id'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            header("Location: loading.php");
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect";
        }
    } else {
        $erreur = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/img/img.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <?php
            if (isset($erreur)) {
                ?>
                    <span class="err">
                    Email ou mot de passe incorrect
                    </span>
                <?php
            }
        ?>
        <h2>Connexion à FaroChat</h2>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <div class="link">
            <p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </div>
</body>
</html>
