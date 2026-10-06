let currentReceiverId = null;
let messageInterval = null;
let userInterval = null;

function csrf() {
    return typeof csrfToken === 'function' ? csrfToken() : '';
}

function scrollToBottom() {
    const messagesDiv = document.getElementById('messages');
    if (messagesDiv) messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function loadMessages(receiverId) {
    currentReceiverId = receiverId;
    updateMessages();
    scrollToBottom();
    if (messageInterval) clearInterval(messageInterval);
    messageInterval = setInterval(updateMessages, 2000);
}

function updateMessages() {
    if (!currentReceiverId) return;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/get_messages.php?receiver_id=' + encodeURIComponent(currentReceiverId), true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const box = document.getElementById('messa');
            if (box) box.innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

function updateDiscussionHeader(userName, userProfile, userDate, userOccup, userLieu, userGenre, userSitu, userEtat) {
    const photo = userProfile || '../assets/img/profile.png';
    ['profilePic', 'pro', 'pro2'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.src = photo;
    });
    const map = { username: userName, nom: userName, nom2: userName, date: userDate, occup: userOccup, lieu: userLieu, genre: userGenre, situ: userSitu, con1: userEtat, con2: userEtat };
    Object.keys(map).forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.textContent = map[id] || '';
    });
}

function me(userId, userName, userProfil, userDate, userOccup, userLieu, userGenre, userSitu, userEtat) {
    const message = document.querySelector('.discussion');
    const info = document.querySelector('.infor');
    const aucun = document.querySelector('.aucun');
    if (message) message.classList.add('active');
    if (info) info.classList.add('active');
    if (aucun) aucun.classList.add('active');
    document.getElementById('ut').classList.add('active');
    userProfil = userProfil || '../assets/img/profile.png';
    userDate = userDate ? 'Date de naissance : ' + userDate : 'Date de naissance : Aucun';
    userOccup = userOccup ? 'Travail : ' + userOccup : 'Travail : Aucun';
    userLieu = userLieu ? 'Lieu de Travail : ' + userLieu : 'Lieu de Travail : Aucun';
    userGenre = userGenre ? 'Genre : ' + userGenre : 'Genre : Aucun';
    userSitu = userSitu ? 'Situation Amoureuse : ' + userSitu : 'Situation Amoureuse : Aucun';
    const online = String(userEtat) === '1';
    userEtat = online ? 'Connecté' : 'Deconnecté';
    ['con1', 'con2'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('dec', !online);
    });
    loadMessages(userId);
    updateDiscussionHeader(userName, userProfil, userDate, userOccup, userLieu, userGenre, userSitu, userEtat);
    setTimeout(scrollToBottom, 400);
}

function updateUsers() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'show_user.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const list = document.getElementById('aff');
            if (list) list.innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

document.getElementById('me_send').addEventListener('submit', function (event) {
    event.preventDefault();
    const field = document.getElementById('messageInput');
    const message = field.value.trim();
    if (!currentReceiverId || !message) return;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../api/send_message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-CSRF-Token', csrf());
    xhr.onload = function () {
        if (xhr.status === 200) {
            field.value = '';
            updateMessages();
            updateUsers();
            setTimeout(scrollToBottom, 300);
        }
    };
    xhr.send('receiver_id=' + encodeURIComponent(currentReceiverId) + '&message=' + encodeURIComponent(message) + '&csrf=' + encodeURIComponent(csrf()));
});

const uploadIcon = document.getElementById('uploadIcon');
const imageInput = document.getElementById('imageInput');
if (uploadIcon && imageInput) {
    uploadIcon.onclick = function () { imageInput.click(); };
    imageInput.onchange = function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        if (!file || !preview) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            const send = document.getElementById('send');
            if (send) send.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };
}

const themeSwitch = document.getElementById('themeSwitch');
if (themeSwitch) {
    themeSwitch.addEventListener('click', function () {
        const body = new URLSearchParams();
        body.set('theme', themeSwitch.checked ? 'sombre' : 'clair');
        body.set('csrf', csrf());
        fetch('../api/update_theme.php', {
            method: 'POST',
            headers: { 'X-CSRF-Token': csrf() },
            body: body
        }).then(function (response) {
            if (response.ok) location.reload();
        });
    });
}

document.getElementById('openModal').onclick = function () {
    document.getElementById('settingsModal').style.display = 'block';
};
document.querySelectorAll('.modal .close').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const modal = btn.closest('.modal');
        if (modal) modal.style.display = 'none';
    });
});
window.addEventListener('click', function (event) {
    ['settingsModal', 'personalInfoModal'].forEach(function (id) {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.style.display = 'none';
    });
    const neu = document.getElementById('new_friend');
    if (event.target === neu) neu.classList.remove('active');
});

document.getElementById('logoutBtn').onclick = function () {
    if (!confirm('Êtes-vous sûr de vouloir vous deconnecter ?')) return;
    const body = new URLSearchParams();
    body.set('csrf', csrf());
    fetch('../auth/decon.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrf(), 'X-Requested-With': 'fetch' },
        body: body
    }).then(function (response) {
        if (response.ok) window.location.href = '../connexion.php';
    });
};

document.getElementById('deleteAccountBtn').onclick = function () {
    if (!confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) return;
    const body = new URLSearchParams();
    body.set('csrf', csrf());
    fetch('../api/del.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrf(), 'X-Requested-With': 'fetch' },
        body: body
    }).then(function (response) {
        if (response.ok) window.location.href = '../connexion.php';
    });
};

document.getElementById('editPersonalInfoBtn').onclick = function () {
    document.getElementById('personalInfoModal').style.display = 'block';
};

document.getElementById('aff').addEventListener('click', function (event) {
    const li = event.target.closest('li.user');
    if (!li || !li.dataset.id) return;
    me(li.dataset.id, li.dataset.name, li.dataset.profil, li.dataset.date, li.dataset.occup, li.dataset.lieu, li.dataset.genre, li.dataset.situ, li.dataset.etat);
});

updateUsers();
userInterval = setInterval(updateUsers, 4000);

document.getElementById('retour1').onclick = function () {
    document.getElementById('ut').classList.remove('active');
    document.querySelector('.discussion').classList.remove('active');
    document.querySelector('.infor').classList.remove('active');
};
document.getElementById('in').onclick = function () {
    document.querySelector('.infor').classList.add('activ');
    document.querySelector('.discussion').classList.add('pause');
};
document.getElementById('retour2').onclick = function () {
    document.querySelector('.infor').classList.remove('activ');
    document.querySelector('.discussion').classList.remove('pause');
};
document.querySelector('.ferm').onclick = function () {
    const info = document.querySelector('.infor');
    const message = document.querySelector('.discussion');
    info.classList.remove('activ');
    info.classList.remove('active');
    message.classList.remove('pause');
    message.classList.remove('active');
    document.getElementById('ut').classList.remove('active');
    document.querySelector('.aucun').classList.remove('active');
    currentReceiverId = null;
    if (messageInterval) clearInterval(messageInterval);
};

const messag = document.getElementById('messages');
messag.onscroll = function () {
    const haut = document.getElementById('desc_but');
    const nearBottom = messag.scrollHeight - messag.scrollTop - messag.clientHeight < 80;
    haut.classList.toggle('active', !nearBottom);
};

const newFriend = document.getElementById('new_friend');
document.getElementById('ajoute').onclick = function () { newFriend.classList.add('active'); };
document.getElementById('clo_new').onclick = function () { newFriend.classList.remove('active'); };

const searchNew = document.getElementById('new_uti');
const searchForm = document.getElementById('rech_new');
if (searchForm) searchForm.addEventListener('submit', function (e) { e.preventDefault(); });
if (searchNew) {
    searchNew.addEventListener('input', function () {
        const q = searchNew.value.toLowerCase();
        document.querySelectorAll('#new_friend .user').forEach(function (el) {
            const name = (el.textContent || '').toLowerCase();
            el.style.display = name.indexOf(q) === -1 ? 'none' : '';
        });
    });
}

document.querySelectorAll('#new_friend .aj').forEach(function (ajout) {
    const id = ajout.getAttribute('data-id');
    const status = new XMLHttpRequest();
    status.open('GET', '../api/friend_status.php?autre_id=' + encodeURIComponent(id), true);
    status.onload = function () {
        if (status.status === 200 && status.responseText.trim() === 'oui') {
            ajout.classList.remove('av');
            ajout.textContent = 'Ajouté(e)';
        } else {
            ajout.classList.add('av');
            ajout.textContent = '+';
        }
    };
    status.send();

    ajout.addEventListener('click', function (event) {
        event.stopPropagation();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../api/add_friend.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-Token', csrf());
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            if (xhr.responseText.trim() === 'oui') {
                ajout.classList.remove('av');
                ajout.textContent = 'Ajouté(e)';
            } else {
                ajout.classList.add('av');
                ajout.textContent = '+';
            }
            updateUsers();
        };
        xhr.send('autre_id=' + encodeURIComponent(id) + '&csrf=' + encodeURIComponent(csrf()));
    });
});
