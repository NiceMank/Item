<link rel="stylesheet" href="../fontawesome/css/all.min.css">
<script defer src="../assets/js/color-thief.umd.js"></script>
<meta name="csrf-token" content="<?php echo h(csrf_token()); ?>">

<header>
    <a href="../home/index.php" id="logoW">
        <img src="../assets/img/WeLogo1.png" alt="Logo" id="webIco" class="ico">
        <span>Idem</span>
    </a>
    <div class="pro">
        <form action="../api/recherche.php" id="search-Ele" method="get" autocomplete="off">
            <label for="search">
                <i class="fa fa-search" aria-label="Rechercher"></i>
                <input type="text" name="search" id="search" placeholder="Rechercher..." maxlength="80">
                <button type="reset" class="fa fa-times" aria-label="Effacer"></button>
            </label>
        </form>
        <div id="searchResults" class="search-results" hidden></div>
        <a class="notifi fab fa-facebook-messenger" href="../chats/discussion.php" aria-label="Messagerie"></a>
        <button type="button" class="notifi fa fa-bell js-notifs" aria-label="Notifications">
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
        <div id="notifPanel" class="notif-panel" hidden></div>
        <a href="../profile/profile.php">
            <span class="ti"><?php echo h(display_name($user_moi ?? [])); ?></span>
            <img id="pr" src="<?php echo h(profile_src($user_moi['profil'] ?? '')); ?>" crossorigin="anonymous" alt="Photo de profil">
        </a>
    </div>
</header>
<style>
    header .pro .ti {
        margin-right: -10px;
        padding: 10px 20px;
        border-radius: 10px 2px 2px 10px;
        transition: background 0.5s, color 0.5s;
    }
    header .pro { position: relative; }
    header .pro .notifi { position: relative; text-decoration: none; background: none; border: 0; cursor: pointer; font-size: 22px; margin-right: 12px; color: rgb(14, 193, 102); }
    #search-Ele { display: flex; align-items: center; margin-right: 12px; min-width: 180px; }
    #search-Ele label { position: relative; display: flex; align-items: center; width: 100%; }
    #search-Ele input { height: 34px; width: 100%; border: none; border-radius: 10px; padding: 0 32px; background: rgba(255,255,255,.75); }
    #search-Ele i.fa { position: absolute; left: 10px; }
    #search-Ele button { position: absolute; right: 4px; background: none; border: 0; cursor: pointer; }
    .notif-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #e23b3b;
        color: white;
        border-radius: 10px;
        font-size: 11px;
        min-width: 16px;
        padding: 1px 4px;
        font-family: Arial, sans-serif;
    }
    .search-results, .notif-panel {
        position: absolute;
        top: 58px;
        right: 0;
        width: min(360px, 80vw);
        max-height: 360px;
        overflow: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,.18);
        z-index: 400;
        text-align: left;
    }
    body.sombre .search-results, body.sombre .notif-panel { background: #2b2c2e; color: #fff; }
    .search-item, .notif-item {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 10px 12px;
        color: inherit;
        text-decoration: none;
        border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .search-item img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
    .search-empty, .notif-item { padding: 12px; margin: 0; font-size: 14px; }
    .pub-date { margin-left: 8px; font-size: 12px; color: #6b7280; }
    #publication .pubs .pub .publ, #stories .storie span.st { white-space: pre-wrap; word-break: break-word; }
</style>
<script>
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    document.querySelectorAll('.ti').forEach(function (el) { el.style.color = 'white'; });
    window.addEventListener('load', function () {
        var img = document.getElementById('pr');
        var nameSpan = document.querySelector('.ti');
        if (!img || !nameSpan || typeof ColorThief === 'undefined') return;
        function applyColor() {
            try {
                var color = new ColorThief().getColor(img);
                var alpha = document.body.classList.contains('sombre') ? 1 : 0.5;
                nameSpan.style.background = 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + alpha + ')';
                nameSpan.style.color = 'white';
            } catch (e) {
                nameSpan.style.background = '#f0f0f0';
            }
        }
        if (img.complete) applyColor();
        else img.addEventListener('load', applyColor);
    });
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
        input.addEventListener('search', clearBox);
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target) && !box.contains(e.target)) clearBox();
        });
    })();
    document.querySelectorAll('.js-notifs').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var panel = document.getElementById('notifPanel');
            if (!panel) return;
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
