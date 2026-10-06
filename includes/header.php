<?php require_once __DIR__ . '/icons.php'; ?>
<meta name="csrf-token" content="<?php echo h(csrf_token()); ?>">
<meta name="theme-color" content="<?php echo theme_class($user_moi['theme'] ?? '') === 'sombre' ? '#000000' : '#f2f2f7'; ?>">

<header class="nav">
    <div class="nav-row">
        <a href="../home/index.php" id="logoW" class="brand">
            <span class="brand-mark">I</span>
            <span class="brand-name">Idem</span>
        </a>
        <div class="pro nav-actions">
            <a class="icon-btn notifi" href="../chats/discussion.php" aria-label="Messagerie"><?php echo icon('chat'); ?></a>
            <button type="button" class="icon-btn notifi js-notifs" aria-label="Notifications">
                <?php echo icon('bell'); ?>
                <?php
                $unread = 0;
                if (isset($conn, $user_moi['id']) && $conn instanceof mysqli) {
                    $uidBadge = (int) $user_moi['id'];
                    $badgeStmt = $conn->prepare('SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 0');
                    $badgeStmt->bind_param('i', $uidBadge);
                    $badgeStmt->execute();
                    $unread = (int) ($badgeStmt->get_result()->fetch_assoc()['c'] ?? 0);
                    $badgeStmt->close();
                }
                if ($unread > 0): ?>
                    <span class="notif-badge"><?php echo $unread > 9 ? '9+' : (string) $unread; ?></span>
                <?php endif; ?>
            </button>
            <a class="avatar-link" href="../profile/profile.php">
                <img id="pr" src="<?php echo h(profile_src($user_moi['profil'] ?? '')); ?>" alt="Photo de profil">
                <span class="ti"><?php echo h(display_name($user_moi ?? [])); ?></span>
            </a>
        </div>
    </div>
    <div class="search-wrap">
        <form action="../api/recherche.php" id="search-Ele" class="search-field" method="get" autocomplete="off">
            <label for="search">
                <?php echo icon('search'); ?>
                <input type="text" name="search" id="search" placeholder="Rechercher" maxlength="80">
                <button type="reset" aria-label="Effacer"><?php echo icon('close'); ?></button>
            </label>
        </form>
        <div id="searchResults" class="search-results" hidden></div>
    </div>
    <div id="notifPanel" class="notif-panel" hidden></div>
</header>
<script>
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    (function () {
        var form = document.getElementById('search-Ele');
        var input = document.getElementById('search');
        var box = document.getElementById('searchResults');
        if (!form || !input || !box) return;
        var timer = null;
        function clearBox() {
            box.hidden = true;
            box.replaceChildren();
        }
        function render(data) {
            box.replaceChildren();
            var users = (data && data.users) || [];
            var posts = (data && data.posts) || [];
            if (!users.length && !posts.length) {
                var empty = document.createElement('p');
                empty.className = 'search-empty';
                empty.textContent = 'Aucun résultat';
                box.appendChild(empty);
                box.hidden = false;
                return;
            }
            users.forEach(function (u) {
                var a = document.createElement('a');
                a.href = '../profile/profile.php?id=' + encodeURIComponent(u.id);
                a.className = 'search-item';
                var img = document.createElement('img');
                img.alt = '';
                img.src = u.profil || '../assets/img/profile.png';
                var span = document.createElement('span');
                span.textContent = u.name || '';
                a.appendChild(img);
                a.appendChild(span);
                box.appendChild(a);
            });
            posts.forEach(function (p) {
                var a = document.createElement('a');
                a.href = '../home/index.php#pub-' + encodeURIComponent(p.id);
                a.className = 'search-item';
                var span = document.createElement('span');
                span.textContent = (p.author ? p.author + ' : ' : '') + (p.text || '');
                a.appendChild(span);
                box.appendChild(a);
            });
            box.hidden = false;
        }
        function run() {
            var q = input.value.trim();
            if (!q) { clearBox(); return; }
            fetch('../api/recherche.php?format=json&q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(render)
                .catch(clearBox);
        }
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(run, 250);
        });
        form.addEventListener('submit', function (e) {
            if (window.fetch) e.preventDefault();
            run();
        });
        form.addEventListener('reset', function () { setTimeout(clearBox, 0); });
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target) && !box.contains(e.target)) clearBox();
        });
    })();
    document.querySelectorAll('.js-notifs').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var panel = document.getElementById('notifPanel');
            if (!panel) return;
            var search = document.getElementById('searchResults');
            if (search) search.hidden = true;
            panel.hidden = !panel.hidden;
            if (panel.hidden) return;
            fetch('../api/notifications.php')
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    panel.innerHTML = html;
                    document.querySelectorAll('.notif-badge').forEach(function (b) { b.remove(); });
                });
        });
    });
</script>
