(function () {
    var currentPub = null;
    var messageInterval = null;
    var activeCommentBtn = null;

    function token() {
        return typeof csrfToken === 'function' ? csrfToken() : '';
    }

    function myPhoto() {
        return document.body.getAttribute('data-profil') || '../assets/img/profile.png';
    }

    function updateMessages() {
        if (!currentPub) return;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../api/get_comment.php?pub_id=' + encodeURIComponent(currentPub), true);
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            var box = document.getElementById('comss');
            if (box) box.innerHTML = xhr.responseText;
            if (activeCommentBtn) {
                var count = box ? box.querySelectorAll('.com').length : 0;
                var badge = activeCommentBtn.querySelector('.nbr_com');
                if (badge) badge.textContent = count > 0 ? String(count) : '';
            }
        };
        xhr.send();
    }

    function openComments(button) {
        var userProfil = button.getAttribute('data-profil') || '../assets/img/profile.png';
        var pubImage = button.getAttribute('data-img') || '';
        var pubText = button.getAttribute('data-text') || '';
        activeCommentBtn = button;
        currentPub = button.getAttribute('data-id');
        var author = document.getElementById('pub_im');
        var me = document.getElementById('pub_im2');
        var name = document.getElementById('pub_nom');
        var text = document.getElementById('pub_texte');
        var image = document.getElementById('pub_imge');
        if (author) author.src = userProfil;
        if (me) me.src = myPhoto();
        if (name) name.textContent = button.getAttribute('data-nom') || '';
        if (text) text.textContent = pubText;
        if (image) {
            if (pubImage) {
                image.src = pubImage;
                image.style.display = 'block';
            } else {
                image.removeAttribute('src');
                image.style.display = 'none';
            }
        }
        var panel = document.getElementById('comment');
        if (panel) panel.classList.add('active');
        updateMessages();
        if (messageInterval) clearInterval(messageInterval);
        messageInterval = setInterval(updateMessages, 4000);
    }

    function bindFeed() {
        document.querySelectorAll('.comment').forEach(function (element) {
            if (element.dataset.bound === '1') return;
            element.dataset.bound = '1';
            element.addEventListener('click', function () { openComments(element); });
        });
        document.querySelectorAll('.like').forEach(function (element) {
            if (element.dataset.bound === '1') return;
            element.dataset.bound = '1';
            element.addEventListener('click', function () {
                var pubId = element.getAttribute('data-id');
                element.classList.toggle('true');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../api/like_add.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-Token', token());
                xhr.onload = function () {
                    if (xhr.status !== 200) {
                        element.classList.toggle('true');
                        return;
                    }
                    var xhr2 = new XMLHttpRequest();
                    xhr2.open('GET', '../api/get_like.php?pub_id=' + encodeURIComponent(pubId), true);
                    xhr2.onload = function () {
                        if (xhr2.status === 200) {
                            var badge = element.querySelector('.nbr_like');
                            if (badge) badge.textContent = xhr2.responseText;
                        }
                    };
                    xhr2.send();
                };
                xhr.send('PubId=' + encodeURIComponent(pubId) + '&csrf=' + encodeURIComponent(token()));
            });
        });
        var form = document.getElementById('send_comm');
        if (form && form.dataset.bound !== '1') {
            form.dataset.bound = '1';
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var field = document.getElementById('comm');
                var comment = field ? field.value.trim() : '';
                if (!currentPub || !comment) return;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../api/send_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-Token', token());
                xhr.onload = function () {
                    if (xhr.status === 200 && field) {
                        field.value = '';
                        updateMessages();
                    }
                };
                xhr.send('PubId=' + encodeURIComponent(currentPub) + '&comment=' + encodeURIComponent(comment) + '&csrf=' + encodeURIComponent(token()));
            });
        }
        var close = document.getElementById('commm');
        if (close && close.dataset.bound !== '1') {
            close.dataset.bound = '1';
            close.addEventListener('click', function () {
                var panel = document.getElementById('comment');
                if (panel) panel.classList.remove('active');
                if (messageInterval) clearInterval(messageInterval);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindFeed);
    } else {
        bindFeed();
    }
})();
