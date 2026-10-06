<?php
require_once __DIR__ . '/config/database.php';

if (current_user_id($conn)) {
    header('Location: home/index.php');
    exit;
}

$erreur = null;
$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nom = clip_str((string) ($_POST['nom'] ?? ''), 80);
    $prenom = clip_str((string) ($_POST['prenom'] ?? ''), 80);
    $email = strtolower(clip_str((string) ($_POST['email'] ?? ''), 190));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_re'] ?? '');

    if ($nom === '' || $prenom === '') {
        $erreur = 'Le nom et le prénom sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } elseif (strlen($password) < 6) {
        $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();
        $check->close();
        if ($exists) {
            $erreur = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $online = '1';
            $insert = $conn->prepare('INSERT INTO users (nom, prenom, email, password, etat_compte) VALUES (?, ?, ?, ?, ?)');
            $insert->bind_param('sssss', $nom, $prenom, $email, $hash, $online);
            $insert->execute();
            $newId = (int) $insert->insert_id;
            $insert->close();
            remember_user($conn, $newId);
            header('Location: loading.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/ios.css">
    <link rel="shortcut icon" href="assets/img/WeLogo1.png" type="image/x-icon">
</head>
<body class="page-auth">
    <div class="auth-card">
        <div class="brand-hero">
            <span class="brand-mark">I</span>
        </div>
        <h1>Créer un compte</h1>
        <p class="sub">Rejoignez Idem en quelques secondes</p>
        <?php if ($erreur): ?>
            <p class="err"><?php echo h($erreur); ?></p>
        <?php endif; ?>
        <form action="" method="POST" id="registerForm">
            <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
            <input type="text" name="nom" placeholder="Votre nom" required maxlength="80" value="<?php echo h($nom); ?>">
            <input type="text" name="prenom" placeholder="Votre prenom" required maxlength="80" value="<?php echo h($prenom); ?>">
            <input type="email" name="email" placeholder="Adresse email" required maxlength="190" value="<?php echo h($email); ?>">
            <input type="password" id="password" name="password" placeholder="Mot de passe" required minlength="6">
            <input type="password" id="confirmPassword" name="password_re" placeholder="Retapez le mot de passe" required minlength="6">
            <button type="submit">S'inscrire</button>
        </form>
        <div class="link">
            <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function (event) {
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
