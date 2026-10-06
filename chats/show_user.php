<?php
require_once __DIR__ . '/../config/database.php';

$userId = current_user_id($conn);
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
if (!$userId) {
    exit;
}

function format_time_ago($datetime): string
{
    $timestamp = strtotime((string) $datetime);
    if (!$timestamp) {
        return '';
    }
    $diff = time() - $timestamp;
    if ($diff < 60) {
        return "À l'instant";
    }
    if ($diff < 3600) {
        return 'Il y a ' . floor($diff / 60) . ' min';
    }
    if ($diff < 86400) {
        return 'Il y a ' . floor($diff / 3600) . ' h';
    }
    if ($diff < 172800) {
        return 'Hier à ' . date('H:i', $timestamp);
    }
    return date('d/m/Y à H:i', $timestamp);
}

$sql = 'SELECT u.*,
        (SELECT MAX(m.created_at) FROM messages m
            WHERE (m.id_moi = ? AND m.id_autre = u.id) OR (m.id_moi = u.id AND m.id_autre = ?)) AS last_at
        FROM users u
        WHERE u.id != ?
        AND (
            EXISTS (SELECT 1 FROM discussion d WHERE (d.id_moi = ? AND d.id_autre = u.id) OR (d.id_moi = u.id AND d.id_autre = ?))
            OR EXISTS (SELECT 1 FROM messages m2 WHERE (m2.id_moi = ? AND m2.id_autre = u.id) OR (m2.id_moi = u.id AND m2.id_autre = ?))
        )
        ORDER BY last_at DESC, u.nom ASC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiiiii', $userId, $userId, $userId, $userId, $userId, $userId, $userId);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$users) {
    echo '<li class="no">Aucun contact. Utilisez + pour démarrer une discussion.</li>';
    exit;
}

$msgStmt = $conn->prepare('SELECT id_moi, message, created_at FROM messages
    WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)
    ORDER BY created_at DESC, id DESC LIMIT 1');

foreach ($users as $user) {
    $otherId = (int) $user['id'];
    $msgStmt->bind_param('iiii', $userId, $otherId, $otherId, $userId);
    $msgStmt->execute();
    $last = $msgStmt->get_result()->fetch_assoc();
    $name = display_name($user);
    $preview = 'Aucun message';
    $when = '';
    if ($last) {
        $prefix = (int) $last['id_moi'] === $userId ? 'Vous : ' : '';
        $preview = $prefix . (string) $last['message'];
        if (function_exists('mb_strimwidth')) {
            $preview = mb_strimwidth($preview, 0, 30, '...');
        } elseif (strlen($preview) > 30) {
            $preview = substr($preview, 0, 27) . '...';
        }
        $when = format_time_ago($last['created_at'] ?? '');
    }
    ?>
    <li class="user"
        data-id="<?php echo $otherId; ?>"
        data-name="<?php echo h($name); ?>"
        data-profil="<?php echo h(profile_src($user['profil'] ?? '')); ?>"
        data-date="<?php echo h($user['date_nais'] ?? ''); ?>"
        data-occup="<?php echo h($user['travail'] ?? ''); ?>"
        data-lieu="<?php echo h($user['Lieu_travail'] ?? ''); ?>"
        data-genre="<?php echo h($user['genre'] ?? ''); ?>"
        data-situ="<?php echo h($user['situation'] ?? ''); ?>"
        data-etat="<?php echo h($user['etat_compte'] ?? '0'); ?>">
        <img src="<?php echo h(profile_src($user['profil'] ?? '')); ?>" alt="">
        <?php echo ($user['etat_compte'] ?? '0') === '1' ? '<span class="op vert"></span>' : '<span class="op orange"></span>'; ?>
        <div class="info">
            <span><?php echo h($name); ?></span>
            <span class="apercu"><?php echo h($preview); ?>
                <?php if ($when !== ''): ?><span class="date_msg"><?php echo h($when); ?></span><?php endif; ?>
            </span>
        </div>
    </li>
    <?php
}
$msgStmt->close();
