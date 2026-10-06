<?php
require_once __DIR__ . '/config/database.php';

if (current_user_id($conn)) {
    header('Location: home/index.php');
    exit;
}

$erreur = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = clip_str((string) ($_POST['email'] ?? ''), 190);
    $password = (string) ($_POST['password'] ?? '');
    $stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, (string) $user['password'])) {
        remember_user($conn, (int) $user['id']);
        header('Location: loading.php');
        exit;
    }
    $erreur = 'Email ou mot de passe incorrect';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/ios.css">
    <link rel="shortcut icon" href="assets/img/WeLogo1.png" type="image/x-icon">
</head>
<body class="page-auth">
    <div class="auth-card">
        <div class="brand-hero">
            <span class="brand-mark">I</span>
        </div>
        <h1>Idem</h1>
        <p class="sub">Connectez-vous pour continuer</p>
        <?php if ($erreur): ?>
            <p class="err"><?php echo h($erreur); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
            <input type="email" name="email" placeholder="Adresse e-mail" required maxlength="190" autocomplete="email">
            <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
            <button type="submit">Se connecter</button>
        </form>
        <div class="link">
            <p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </div>
</body>
</html>
