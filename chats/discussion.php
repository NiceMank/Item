<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/tabbar.php';

$userId = require_login($conn, '../connexion.php');
$user_moi = fetch_user($conn, $userId);
if (!$user_moi) {
    forget_user();
    header('Location: ../connexion.php');
    exit;
}
$isChecked = theme_class($user_moi['theme'] ?? '') === 'sombre' ? 'checked' : '';
$flash = flash_get();

$list = $conn->prepare('SELECT id, nom, prenom, profil FROM users WHERE id != ? ORDER BY nom ASC, prenom ASC');
$list->bind_param('i', $userId);
$list->execute();
$users = $list->get_result()->fetch_all(MYSQLI_ASSOC);
$list->close();
$photo = profile_src($user_moi['profil'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Idem | Messages</title>
    <link rel="stylesheet" href="../assets/css/ios.css">
    <link rel="shortcut icon" href="../assets/img/WeLogo1.png" type="image/x-icon">
</head>
<body class="<?php echo h(theme_class($user_moi['theme'] ?? '')); ?> page-chat" data-profil="<?php echo h($photo); ?>">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section id="farochat">
        <section id="ut" class="utilisateurs">
            <?php if ($flash): ?><p class="flash"><?php echo h($flash); ?></p><?php endif; ?>
            <button type="button" class="para" id="openModal" aria-label="Réglages"><img src="<?php echo h($photo); ?>" alt=""></button>
            <h1>Messages <button type="button" id="refreshUsers" aria-label="Actualiser"><?php echo icon('refresh'); ?></button></h1>
            <button type="button" id="ajoute" aria-label="Nouvelle discussion"><?php echo icon('edit'); ?></button>
            <div class="liste" id="list_user">
                <ul id="aff"></ul>
            </div>
        </section>
        <section id="disc" class="discussion">
            <div class="entete">
                <button type="button" id="retour1" aria-label="Retour"><?php echo icon('back'); ?></button>
                <img id="profilePic" src="../assets/img/profile.png" alt="">
                <div class="info">
                    <span id="username"></span>
                    <span id="con1" class="apercu"></span>
                </div>
                <button type="button" id="in" aria-label="Infos"><?php echo icon('more'); ?></button>
            </div>
            <div class="messages" id="messages">
                <div class="inform">
                    <img id="pro2" class="inf-img" src="../assets/img/profile.png" alt="">
                    <span id="nom2" class="inf-nom"></span>
                    <span class="secu">Commencez à écrire !!</span>
                </div>
                <hr>
                <div id="messa"></div>
            </div>
            <a href="#desc" id="desc_but" aria-label="Bas de la discussion"><?php echo icon('down'); ?></a>
            <form class="envoie" action="../api/send_message.php" id="me_send" method="post">
                <textarea name="message" id="messageInput" required placeholder="Message" maxlength="2000"></textarea>
                <button type="submit" aria-label="Envoyer"><?php echo icon('send'); ?></button>
            </form>
        </section>
        <section class="infor">
            <div class="part1">
                <button type="button" id="retour2" aria-label="Retour"><?php echo icon('back'); ?></button>
                <img id="pro" class="inf-img" src="" alt="">
                <span id="nom" class="inf-nom"></span>
                <span id="con2" class="apercu"></span>
            </div>
            <div class="part2">
                <span id="date"><b>Date de Naissance :</b></span>
                <span id="occup"><b>Occupation :</b></span>
                <span id="lieu"><b>Lieu de Travail :</b></span>
                <span id="genre"><b>Genre :</b></span>
                <span id="situ"><b>Situation Amoureuse :</b></span>
            </div>
            <div class="part3">
                <button class="ferm" type="button">Fermer la discusion</button>
            </div>
        </section>
        <span class="aucun"><b>Selectionnez un contact</b></span>
    </section>

    <div id="settingsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Paramètres</h2>
            <form id="profilePicForm" action="../api/upload.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../chats/discussion.php">
                <div class="profile-photo-container">
                    <img id="imagePreview" src="<?php echo h($photo); ?>" alt="Aperçu de l'image">
                    <button type="button" id="uploadIcon" aria-label="Changer la photo"><?php echo icon('edit'); ?></button>
                    <button type="submit" id="send">Enregistrer</button>
                    <input name="image" type="file" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
                </div>
                <br>
                <span class="nom"><?php echo h(display_name($user_moi)); ?></span>
                <br><br>
                <button type="button" id="editPersonalInfoBtn">Modifier informations personnelles</button>
                <br><br>
                <div class="set-row">
                    <span>Mode sombre</span>
                    <label class="switch">
                        <input type="checkbox" id="themeSwitch" <?php echo $isChecked; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <br><br>
                <button type="button" id="logoutBtn">Se déconnecter</button>
                <br><br>
                <button type="button" id="deleteAccountBtn" class="danger">Supprimer le compte</button>
            </form>
        </div>
    </div>

    <div id="personalInfoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier informations personnelles</h2>
            <form id="personalInfoForm" method="post" action="../api/modif_info.php">
                <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="redirect" value="../chats/discussion.php">
                <label for="lastName">Nom :(Obligatoire)</label>
                <input type="text" id="lastName" name="lastName" required maxlength="80" value="<?php echo h($user_moi['nom']); ?>">
                <label for="firstName">Prénom :(Obligatoire)</label>
                <input type="text" id="firstName" name="firstName" required maxlength="80" value="<?php echo h($user_moi['prenom']); ?>">
                <label for="genre">Sexe :</label>
                <input type="text" id="genre" name="genre" maxlength="40" value="<?php echo h($user_moi['genre']); ?>">
                <label for="birthdate">Date de naissance :</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo h($user_moi['date_nais']); ?>">
                <label for="job">Occupation :</label>
                <input type="text" id="job" name="job" maxlength="120" value="<?php echo h($user_moi['travail']); ?>">
                <label for="workplace">Lieu de travail :</label>
                <input type="text" id="workplace" name="workplace" maxlength="120" value="<?php echo h($user_moi['Lieu_travail']); ?>">
                <label for="relationshipStatus">Situation amoureuse :</label>
                <input type="text" id="relationshipStatus" name="relationshipStatus" maxlength="80" value="<?php echo h($user_moi['situation']); ?>">
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>

    <section id="new_friend">
        <div class="new">
            <div class="tittle"><span>Nouvelle discussion</span></div>
            <button type="button" id="clo_new" aria-label="Fermer"><?php echo icon('close'); ?></button>
            <form id="rech_new" action="">
                <input type="search" name="new_uti" id="new_uti" placeholder="Rechercher">
                <button type="submit" aria-label="Rechercher"><?php echo icon('search'); ?></button>
            </form>
            <div class="resu">
                <?php if ($users): ?>
                    <?php foreach ($users as $user): ?>
                        <div class="user">
                            <img src="<?php echo h(profile_src($user['profil'] ?? '')); ?>" alt="">
                            <span><?php echo h(display_name($user)); ?></span>
                            <span class="aj av" data-id="<?php echo (int) $user['id']; ?>">+</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no">Aucun utilisateur trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php render_tabbar('chat'); ?>
    <script src="../assets/js/discussion.js"></script>
    <script>
        document.getElementById('refreshUsers').addEventListener('click', function () {
            if (typeof updateUsers === 'function') updateUsers();
        });
    </script>
</body>
</html>
