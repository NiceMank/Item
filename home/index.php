<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/render.php';
require_once __DIR__ . '/../includes/tabbar.php';

$userId = require_login($conn, '../connexion.php');
$user_moi = fetch_user($conn, $userId);
if (!$user_moi) {
    forget_user();
    header('Location: ../connexion.php');
    exit;
}

$myStories = $conn->prepare('SELECT story_text, story_image FROM storie WHERE id_uti = ? ORDER BY id DESC');
$myStories->bind_param('i', $userId);
$myStories->execute();
$ownStories = $myStories->get_result()->fetch_all(MYSQLI_ASSOC);
$myStories->close();

$otherStoriesStmt = $conn->prepare('SELECT s.story_text, s.story_image, u.id AS uid, u.nom, u.prenom, u.profil
    FROM storie s JOIN users u ON u.id = s.id_uti
    WHERE s.id_uti != ? ORDER BY s.id DESC LIMIT 10');
$otherStoriesStmt->bind_param('i', $userId);
$otherStoriesStmt->execute();
$otherStories = $otherStoriesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$otherStoriesStmt->close();

$publications = fetch_publications($conn, $userId, null, 30);
$flash = flash_get();
$myPhoto = profile_src($user_moi['profil'] ?? '');

function render_story(string $text, string $image, string $profil, string $label, int $userId): void
{
    $text = trim($text);
    $image = media_src($image);
    if ($text === '' && $image === '') {
        return;
    }
    echo '<div class="storie">';
    if ($text !== '') {
        echo '<span class="st">' . nl2br(h($text)) . '</span>';
    } else {
        echo '<img src="' . h($image) . '" alt="">';
    }
    echo '<div class="info"><img src="' . h($profil) . '" alt="">';
    echo '<a class="uti" href="../profile/profile.php?id=' . $userId . '">' . h($label) . '</a></div></div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Idem | Accueil</title>
    <link rel="shortcut icon" href="../assets/img/WeLogo1.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/ios.css">
</head>
<body class="<?php echo h(theme_class($user_moi['theme'] ?? '')); ?> page-home" data-profil="<?php echo h($myPhoto); ?>">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <section id="corps">
        <div class="large-title">
            <p class="eyebrow"><?php echo h($user_moi['prenom']); ?></p>
            <h1>Accueil</h1>
        </div>
        <section id="stories">
            <div id="ajout" class="storie ajout">
                <img src="<?php echo h($myPhoto); ?>" alt="">
                <span class="add"><?php echo icon('plus'); ?><b>Votre story</b></span>
            </div>
            <?php
            foreach ($ownStories as $story) {
                render_story((string) ($story['story_text'] ?? ''), (string) ($story['story_image'] ?? ''), $myPhoto, 'Vous', $userId);
            }
            foreach ($otherStories as $story) {
                $label = trim((string) ($story['prenom'] ?? '') . ' ' . (string) ($story['nom'] ?? ''));
                render_story(
                    (string) ($story['story_text'] ?? ''),
                    (string) ($story['story_image'] ?? ''),
                    profile_src($story['profil'] ?? ''),
                    $label !== '' ? $label : 'Utilisateur',
                    (int) $story['uid']
                );
            }
            ?>
        </section>
        <section id="publication">
            <?php if ($flash): ?>
                <p class="flash"><?php echo h($flash); ?></p>
            <?php endif; ?>
            <div class="new_pub">
                <div class="ajou">
                    <img src="<?php echo h($myPhoto); ?>" alt="">
                    <span id="neuf">Quoi de neuf <?php echo h($user_moi['prenom']); ?> ? ...</span>
                </div>
                <div class="imag">
                    <?php echo icon('image'); ?>
                    <span id="ig">Photo</span>
                </div>
            </div>
            <div class="pubs">
                <?php if (!$publications): ?>
                    <p class="no-pub">Aucune publication pour le moment.</p>
                <?php endif; ?>
                <?php foreach ($publications as $pub) {
                    render_publication($pub);
                } ?>
            </div>
        </section>
        <section id="texte" class="public">
            <div class="publica">
                <button type="button" id="close_pub1" aria-label="Fermer"><?php echo icon('close'); ?></button>
                <div class="entete">Nouvelle publication</div>
                <form action="publication.php" method="post">
                    <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                    <textarea name="pub" id="textarea" required maxlength="5000" placeholder="Ecrivez quelque chose ..."></textarea>
                    <button type="submit">Publier</button>
                </form>
            </div>
        </section>
        <section id="images" class="public">
            <div class="publica">
                <button type="button" id="close_pub2" aria-label="Fermer"><?php echo icon('close'); ?></button>
                <div class="entete">Publier une photo</div>
                <form action="publication.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                    <input type="file" name="pub_img" id="imge" required accept="image/jpeg,image/png,image/gif,image/webp">
                    <button type="button" class="sel" id="pick_pub">Choisir une photo</button>
                    <img id="preview2" alt="">
                    <button type="button" id="Icon" aria-label="Changer"><?php echo icon('edit'); ?></button>
                    <button type="submit" id="publier">Publier</button>
                </form>
            </div>
        </section>
        <?php include __DIR__ . '/../includes/comment_modal.php'; ?>
        <section id="stor">
            <div class="sto">
                <button type="button" id="close_stor" aria-label="Fermer"><?php echo icon('close'); ?></button>
                <div id="part_1" class="par part1">
                    <div class="entete">Nouvelle story</div>
                    <div id="type_text" class="type text"><span>Texte</span><?php echo icon('edit'); ?></div>
                    <div id="type_img" class="type img"><span>Photo</span><?php echo icon('image'); ?></div>
                </div>
                <div id="part_2" class="par part2">
                    <form id="story_text" action="story.php" method="post">
                        <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                        <textarea name="story" id="text" placeholder="Commencez à ecrire" required maxlength="500"></textarea>
                        <button id="submitBtn" type="submit">Publier</button>
                        <div class="spinner" id="loader" role="status" aria-label="Publication"></div>
                    </form>
                </div>
                <div id="part_3" class="par part3">
                    <form id="story_img" action="story.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
                        <input type="file" name="story_img" id="img" required accept="image/jpeg,image/png,image/gif,image/webp">
                        <button type="button" class="sel" id="pick_story">Choisir une photo</button>
                        <img id="preview" alt="">
                        <button type="button" id="uploadIcon" aria-label="Changer"><?php echo icon('edit'); ?></button>
                        <button type="submit" id="submit">Publier</button>
                    </form>
                </div>
            </div>
        </section>
    </section>
    <?php render_tabbar('home'); ?>
    <script src="../assets/js/feed.js"></script>
    <script>
        const ajout = document.getElementById('ajout');
        const newStory = document.getElementById('stor');
        const closeStor = document.getElementById('close_stor');
        const typeText = document.getElementById('type_text');
        const typeImg = document.getElementById('type_img');
        const part1 = document.getElementById('part_1');
        const part2 = document.getElementById('part_2');
        const part3 = document.getElementById('part_3');
        ajout.onclick = function () { newStory.classList.add('active'); };
        typeText.onclick = function () { part2.classList.add('active'); part1.classList.add('none'); };
        typeImg.onclick = function () { part3.classList.add('active'); part1.classList.add('none'); };
        closeStor.onclick = function () {
            newStory.classList.remove('active');
            part1.classList.remove('none');
            part2.classList.remove('active');
            part3.classList.remove('active');
        };
        document.getElementById('story_text').addEventListener('submit', function (event) {
            event.preventDefault();
            document.getElementById('submitBtn').style.visibility = 'hidden';
            document.getElementById('loader').style.display = 'block';
            event.currentTarget.submit();
        });
        document.getElementById('close_pub1').onclick = function () { document.getElementById('texte').classList.remove('active'); };
        document.getElementById('close_pub2').onclick = function () { document.getElementById('images').classList.remove('active'); };
        document.getElementById('neuf').onclick = function () { document.getElementById('texte').classList.add('active'); };
        document.getElementById('ig').onclick = function () { document.getElementById('images').classList.add('active'); };
        document.getElementById('uploadIcon').onclick = function () { document.getElementById('img').click(); };
        document.getElementById('Icon').onclick = function () { document.getElementById('imge').click(); };
        document.getElementById('pick_pub').onclick = function () { document.getElementById('imge').click(); };
        document.getElementById('pick_story').onclick = function () { document.getElementById('img').click(); };
        document.getElementById('imge').onchange = function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview2');
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                document.getElementById('Icon').style.display = 'block';
                document.getElementById('publier').style.display = 'block';
            };
            reader.readAsDataURL(file);
        };
        document.getElementById('img').onchange = function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                document.getElementById('uploadIcon').style.display = 'block';
                document.getElementById('submit').style.display = 'block';
            };
            reader.readAsDataURL(file);
        };
    </script>
</body>
</html>
