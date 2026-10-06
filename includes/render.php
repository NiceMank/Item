<?php
require_once __DIR__ . '/icons.php';

function render_publication(array $pub): void
{
    $text = trim((string) ($pub['pub_text'] ?? ''));
    $image = media_src($pub['pub_image'] ?? '');
    $author = trim((string) ($pub['prenom'] ?? '') . ' ' . (string) ($pub['nom'] ?? ''));
    if ($author === '') {
        $author = 'Utilisateur';
    }
    $profil = profile_src($pub['profil'] ?? '');
    $liked = (int) ($pub['liked'] ?? 0) > 0;
    $likes = (int) ($pub['like_count'] ?? 0);
    $comments = (int) ($pub['comment_count'] ?? 0);
    $pubId = (int) ($pub['id'] ?? 0);
    $authorId = (int) ($pub['id_user'] ?? 0);
    $dateLabel = '';
    if (!empty($pub['date'])) {
        $ts = strtotime((string) $pub['date']);
        if ($ts) {
            $dateLabel = date('d/m/Y H:i', $ts);
        }
    }
    ?>
    <div class="pub" id="pub-<?php echo $pubId; ?>">
        <div class="prop">
            <a href="../profile/profile.php?id=<?php echo $authorId; ?>">
                <img src="<?php echo h($profil); ?>" alt="">
            </a>
            <a class="name" href="../profile/profile.php?id=<?php echo $authorId; ?>"><?php echo h($author); ?></a>
            <?php if ($dateLabel !== ''): ?>
                <span class="pub-date"><?php echo h($dateLabel); ?></span>
            <?php endif; ?>
        </div>
        <?php if ($text !== ''): ?>
            <span class="publ"><?php echo nl2br(h($text)); ?></span>
        <?php endif; ?>
        <?php if ($image !== ''): ?>
            <span class="publ"><img src="<?php echo h($image); ?>" alt=""></span>
        <?php endif; ?>
        <div class="notes">
            <div class="like<?php echo $liked ? ' true' : ''; ?>" data-id="<?php echo $pubId; ?>">
                <?php echo icon('heart'); ?>
                <span class="nbr_like"><?php echo $likes > 0 ? (string) $likes : ''; ?></span>
                <span class="t">J'adore</span>
            </div>
            <div class="comment"
                data-id="<?php echo $pubId; ?>"
                data-nom="<?php echo h($author); ?>"
                data-profil="<?php echo h($profil); ?>"
                data-img="<?php echo h($image); ?>"
                data-text="<?php echo h($text); ?>">
                <?php echo icon('comment'); ?>
                <span class="nbr_com"><?php echo $comments > 0 ? (string) $comments : ''; ?></span>
                <span>Commenter</span>
            </div>
        </div>
    </div>
    <?php
}
