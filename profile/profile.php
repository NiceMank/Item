<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/render.php';
require_once __DIR__ . '/../includes/tabbar.php';

$uid = require_login($conn, '../connexion.php');
$user_moi = fetch_user($conn, $uid);
if (!$user_moi) {
    forget_user();
    header('Location: ../connexion.php');
    exit;
}

$viewId = isset($_GET['id']) ? (int) $_GET['id'] : $uid;
$view = fetch_user($conn, $viewId);
if (!$view) {
    header('Location: profile.php');
    exit;
}
$isSelf = $viewId === $uid;
$isFriend = !$isSelf && discussion_exists($conn, $uid, $viewId);
$posts = fetch_publications($conn, $uid, $viewId, 30);
$flash = flash_get();

$friendsStmt = $conn->prepare('SELECT DISTINCT u.id, u.nom, u.prenom, u.profil
    FROM users u
    JOIN discussion d ON ((d.id_moi = ? AND d.id_autre = u.id) OR (d.id_autre = ? AND d.id_moi = u.id))
    WHERE u.id != ?
    ORDER BY u.nom, u.prenom');
$friendsStmt->bind_param('iii', $viewId, $viewId, $viewId);
$friendsStmt->execute();
$friends = $friendsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$friendsStmt->close();

$photo = profile_src($view['profil'] ?? '');
$myPhoto = profile_src($user_moi['profil'] ?? '');
$meta = array_filter([
    $view['travail'] ?? '',
    $view['Lieu_travail'] ?? '',
]);
$details = [];
if (($view['genre'] ?? '') !== '') {
    $details[] = $view['genre'];
}
if (($view['date_nais'] ?? '') !== '') {
    $details[] = $view['date_nais'];
}
if (($view['situation'] ?? '') !== '') {
    $details[] = $view['situation'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo h(display_name($view)); ?> | Profil</title>
    <link rel="stylesheet" href="../assets/css/ios.css">
    <link rel="shortcut icon" href="../assets/img/WeLogo1.png" type="image/x-icon">
</head>
<body class="<?php echo h(theme_class($user_moi['theme'] ?? '')); ?> page-profile" data-profil="<?php echo h($myPhoto); ?>">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <section id="corps">
        <div class="large-title">
            <p class="eyebrow">Profil</p>
            <h1><?php echo $isSelf ? 'Vous' : h($view['prenom']); ?></h1>
        </div>
        <?php if ($flash): ?><p class="flash"><?php echo h($flash); ?></p><?php endif; ?>
        <section class="profile-hero">
            <img src="<?php echo h($photo); ?>" alt="">
            <h2><?php echo h(display_name($view)); ?></h2>
            <?php if ($meta): ?><p><?php echo h(implode(' · ', $meta)); ?></p><?php endif; ?>
            <?php if ($details): ?><p><?php echo h(implode(' · ', $details)); ?></p><?php endif; ?>
            <p><?php echo ($view['etat_compte'] ?? '0') === '1' ? 'En ligne' : 'Hors ligne'; ?></p>
            <?php if (!$isSelf): ?>
                <button type="button" class="friend-btn" id="friendBtn" data-id="<?php echo $viewId; ?>">
                    <?php echo $isFriend ? 'Retirer' : 'Ajouter'; ?>
                </button>
                <a class="linkish" href="../chats/discussion.php">Message</a>
            <?php endif; ?>
        </section>

        <?php if ($isSelf): ?>
        <section class="group profile-edit">
            <h3>Photo</h3>
            <form class="avatar-form" action="../api/upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../profile/profile.php">
                <label><span>Image</span><input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required></label>
                <button type="submit">Enregistrer la photo</button>
            </form>
        </section>
        <section class="group profile-edit">
            <h3>Informations</h3>
            <form action="../api/modif_info.php" method="post">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../profile/profile.php">
                <label><span>Nom</span><input type="text" name="lastName" required maxlength="80" value="<?php echo h($view['nom']); ?>"></label>
                <label><span>Prénom</span><input type="text" name="firstName" required maxlength="80" value="<?php echo h($view['prenom']); ?>"></label>
                <label><span>Sexe</span><input type="text" name="genre" maxlength="40" value="<?php echo h($view['genre']); ?>"></label>
                <label><span>Naissance</span><input type="date" name="birthdate" value="<?php echo h($view['date_nais']); ?>"></label>
                <label><span>Occupation</span><input type="text" name="job" maxlength="120" value="<?php echo h($view['travail']); ?>"></label>
                <label><span>Lieu</span><input type="text" name="workplace" maxlength="120" value="<?php echo h($view['Lieu_travail']); ?>"></label>
                <label><span>Situation</span><input type="text" name="relationshipStatus" maxlength="80" value="<?php echo h($view['situation']); ?>"></label>
                <button type="submit">Enregistrer</button>
            </form>
        </section>
        <section class="group">
            <h3>Réglages</h3>
            <div class="set-row">
                <span>Mode sombre</span>
                <label class="switch">
                    <input type="checkbox" id="themeSwitch" <?php echo theme_class($user_moi['theme'] ?? '') === 'sombre' ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
            <form action="../auth/decon.php" method="post">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <button type="submit">Se déconnecter</button>
            </form>
            <form action="../api/del.php" method="post" onsubmit="return confirm('Supprimer définitivement ce compte ?');">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <button type="submit" class="danger">Supprimer le compte</button>
            </form>
        </section>
        <?php endif; ?>

        <section class="friends">
            <h3>Discussions</h3>
            <?php if (!$friends): ?>
                <p class="empty">Aucune discussion pour le moment.</p>
            <?php endif; ?>
            <?php foreach ($friends as $friend): ?>
                <a href="profile.php?id=<?php echo (int) $friend['id']; ?>">
                    <img src="<?php echo h(profile_src($friend['profil'] ?? '')); ?>" alt="">
                    <span><?php echo h(display_name($friend)); ?></span>
                </a>
            <?php endforeach; ?>
        </section>

        <section id="publication">
            <div class="pubs">
                <?php if (!$posts): ?><p class="empty">Aucune publication.</p><?php endif; ?>
                <?php foreach ($posts as $post) {
                    render_publication($post);
                } ?>
            </div>
        </section>
        <?php include __DIR__ . '/../includes/comment_modal.php'; ?>
    </section>
    <?php render_tabbar('profile'); ?>
    <script src="../assets/js/feed.js"></script>
    <script>
        var theme = document.getElementById('themeSwitch');
        if (theme) {
            theme.addEventListener('change', function () {
                var body = new URLSearchParams();
                body.set('theme', theme.checked ? 'sombre' : 'clair');
                body.set('csrf', csrfToken());
                fetch('../api/update_theme.php', {
                    method: 'POST',
                    headers: {'X-CSRF-Token': csrfToken()},
                    body: body
                }).then(function () { location.reload(); });
            });
        }
        var friendBtn = document.getElementById('friendBtn');
        if (friendBtn) {
            friendBtn.addEventListener('click', function () {
                var body = new URLSearchParams();
                body.set('autre_id', friendBtn.getAttribute('data-id'));
                body.set('csrf', csrfToken());
                fetch('../api/add_friend.php', {
                    method: 'POST',
                    headers: {'X-CSRF-Token': csrfToken()},
                    body: body
                }).then(function (r) { return r.text(); }).then(function (text) {
                    friendBtn.textContent = text.trim() === 'oui' ? 'Retirer' : 'Ajouter';
                });
            });
        }
    </script>
</body>
</html>
