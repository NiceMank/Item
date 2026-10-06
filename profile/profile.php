<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/render.php';

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h(display_name($view)); ?> | Profil</title>
    <link rel="stylesheet" href="../assets/css/accueil.css">
    <link rel="shortcut icon" href="../assets/img/WeLogo1.png" type="image/x-icon">
    <style>
        .profile-card, .profile-edit, .friends {
            background: rgba(255,255,255,.86);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
        }
        body.sombre .profile-card, body.sombre .profile-edit, body.sombre .friends { background: rgba(40,40,43,.92); color: #fff; }
        .profile-head { display: flex; gap: 16px; align-items: center; }
        .profile-head img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; }
        .profile-edit form, .avatar-form { display: flex; flex-direction: column; gap: 8px; }
        .profile-edit input, .avatar-form input[type="file"] { padding: 8px; border-radius: 8px; border: 1px solid #ccc; }
        .profile-edit button, .avatar-form button, .friend-btn, .danger {
            border: 0; border-radius: 8px; padding: 10px 14px; cursor: pointer; background: #4398e9; color: white;
        }
        .danger { background: #c0392b; }
        .friends a { display: flex; gap: 8px; align-items: center; margin: 8px 0; color: inherit; text-decoration: none; }
        .friends img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .flash { background: #c0392b; color: white; padding: 10px 12px; border-radius: 8px; }
    </style>
</head>
<body class="<?php echo h(theme_class($user_moi['theme'] ?? '')); ?>" data-profil="<?php echo h($myPhoto); ?>">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <section id="corps">
        <?php if ($flash): ?><p class="flash"><?php echo h($flash); ?></p><?php endif; ?>
        <section class="profile-card">
            <div class="profile-head">
                <img src="<?php echo h($photo); ?>" alt="">
                <div>
                    <h2><?php echo h(display_name($view)); ?></h2>
                    <p><?php echo h($view['travail'] ?? ''); ?><?php echo ($view['Lieu_travail'] ?? '') !== '' ? ' · ' . h($view['Lieu_travail']) : ''; ?></p>
                    <p>
                        <?php if (($view['genre'] ?? '') !== '') echo h($view['genre']) . ' · '; ?>
                        <?php if (($view['date_nais'] ?? '') !== '') echo 'Né(e) le ' . h($view['date_nais']) . ' · '; ?>
                        <?php echo h($view['situation'] ?? ''); ?>
                    </p>
                    <p><?php echo ($view['etat_compte'] ?? '0') === '1' ? 'Connecté' : 'Déconnecté'; ?></p>
                    <?php if (!$isSelf): ?>
                        <button type="button" class="friend-btn" id="friendBtn" data-id="<?php echo $viewId; ?>">
                            <?php echo $isFriend ? 'Retirer de mes discussions' : 'Ajouter'; ?>
                        </button>
                        <a href="../chats/discussion.php">Écrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ($isSelf): ?>
        <section class="profile-edit">
            <h3>Photo de profil</h3>
            <form class="avatar-form" action="../api/upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../profile/profile.php">
                <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <button type="submit">Enregistrer la photo</button>
            </form>
            <h3>Informations</h3>
            <form action="../api/modif_info.php" method="post">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../profile/profile.php">
                <label>Nom <input type="text" name="lastName" required maxlength="80" value="<?php echo h($view['nom']); ?>"></label>
                <label>Prénom <input type="text" name="firstName" required maxlength="80" value="<?php echo h($view['prenom']); ?>"></label>
                <label>Sexe <input type="text" name="genre" maxlength="40" value="<?php echo h($view['genre']); ?>"></label>
                <label>Date de naissance <input type="date" name="birthdate" value="<?php echo h($view['date_nais']); ?>"></label>
                <label>Occupation <input type="text" name="job" maxlength="120" value="<?php echo h($view['travail']); ?>"></label>
                <label>Lieu de travail <input type="text" name="workplace" maxlength="120" value="<?php echo h($view['Lieu_travail']); ?>"></label>
                <label>Situation <input type="text" name="relationshipStatus" maxlength="80" value="<?php echo h($view['situation']); ?>"></label>
                <button type="submit">Enregistrer</button>
            </form>
            <p>
                <label class="switch">Thème sombre
                    <input type="checkbox" id="themeSwitch" <?php echo theme_class($user_moi['theme'] ?? '') === 'sombre' ? 'checked' : ''; ?>>
                </label>
            </p>
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
                <p>Aucune discussion pour le moment.</p>
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
                <?php if (!$posts): ?><p>Aucune publication.</p><?php endif; ?>
                <?php foreach ($posts as $post) render_publication($post); ?>
            </div>
        </section>
        <?php include __DIR__ . '/../includes/comment_modal.php'; ?>
    </section>
    <footer>
        <ul>
            <li><a href="../home/index.php"><i class="fas fa-home"></i></a></li>
            <li><a href="../chats/discussion.php"><i class="fas fa-comments"></i></a></li>
            <li><a href="#" class="js-notifs"><i class="fas fa-bell"></i></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
        </ul>
    </footer>
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
                    friendBtn.textContent = text.trim() === 'oui' ? 'Retirer de mes discussions' : 'Ajouter';
                });
            });
        }
    </script>
</body>
</html>
