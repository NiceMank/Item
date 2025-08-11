<?php
session_start();
include('config/database.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['password_re'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $erreur = "Cet email est déjà utilisé.";
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, password, etat_compte) VALUES (?, ?, ?, ?, '1')");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $hashedPassword);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $new_user_id;
        setcookie("user_id", $id, time() + (86400), "/");
        header("Location: loading.php");
    } else {
        $erreur = "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/img/img.png" type="image/x-icon">
</head>
<body>
    <div class="container">
    <?php
            if (isset($erreur)) {
                ?>
                    <span class="err">
                        <?php
                        echo $erreur;
                        ?>
                    </span>
                <?php
            }
        ?>
        <h2>Inscription</h2>
        <form action="" method="POST" id="registerForm">
            <input type="text" name="nom" placeholder="Votre nom" required>
            <input type="text" name="prenom" placeholder="Votre prenom" required>
            <input type="email" name="email" placeholder="Adresse email" required>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <input type="password" id="confirmPassword" name="password_re" placeholder="Retapez le mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="link">
            <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas.');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
