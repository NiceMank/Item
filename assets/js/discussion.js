// function searchUsers() {
// const searchQuery = document.getElementById('searchBar').value;

// const xhr = new XMLHttpRequest();
// xhr.open('GET', `search_users.php?q=${encodeURIComponent(searchQuery)}`, true);
// xhr.onload = function() {
//     if (xhr.status === 200) {
//         document.getElementById('aff').innerHTML = xhr.responseText;
//         // Ajouter les écouteurs d'événements aux nouveaux éléments
//         document.querySelectorAll('.user').forEach(function(element) {
//     element.addEventListener('click', function() {
//         let userId = this.getAttribute('data-id');
//         let userName = this.getAttribute('data-nom');
//         let userProfil = this.getAttribute('data-profil');
//         let userDate = this.getAttribute('data-date-nais');
//         let userOccup = this.getAttribute('data-occup');
//         let userLieu = this.getAttribute('data-lieu-trav');
//         let userGenre = this.getAttribute('data-genre');
//         let userSitu = this.getAttribute('data-situation');
//         let userEtat = this.getAttribute('data-etat');
//         if (userProfil == "") {
//             userProfil = "img/profile.png";
//         }
//         if (userDate == "") {
//             userDate = "Date de naissance : Aucun";
//         }else{
//             userDate = "Date de naissance : "+userDate;
//         }
//         if (userOccup == "") {
//             userOccup = "Travail : Aucun";
//         }else{
//             userOccup = "Travail : "+userOccup;
//         }
//         if (userLieu == "") {
//             userLieu = "Lieu de Travail : Aucun";
//         }else{
//             userLieu = "Lieu de Travail : "+userLieu;
//         }
//         if (userGenre == "") {
//             userGenre = "Genre : Aucun";
//         }else{
//             userGenre = "Genre : "+userGenre;
//         }
//         if (userSitu == "") {
//             userSitu = "Situation Amoureuse : Aucun";
//         }else{
//             userSitu = "Situation Amoureuse : "+userSitu;
//         }
//         if (userEtat == "1") {
//             userEtat = "Connecté";
//             document.getElementById('con1').classList.remove('dec');
//             document.getElementById('con2').classList.remove('dec');
//         }else{
//             userEtat = "Deconnecté";
//             document.getElementById('con1').classList.add('dec');
//             document.getElementById('con2').classList.add('dec');
//         }
//         // scrollToBottom();
        
//         loadMessages(userId)
        
//         updateDiscussionHeader(userName, userProfil, userDate, userOccup, userLieu, userGenre, userSitu, userEtat);
//     });
// });
//     }
// };
// xhr.send();
// }

let currentReceiverId = null;
let messageInterval = null;
let userInterval = null;
function scrollToBottom() {
    const messagesDiv = document.getElementById('messages');
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Afficher et actualiser les messages

        // us = document.querySelectorAll('.user');
        // us.forEach(user => {
        //     user.onclick =  function() {
                
        //     };
        // })

        function loadMessages(receiverId) {
            currentReceiverId = receiverId;
            updateMessages();
            scrollToBottom();
            // Configurer le rafraîchissement automatique
            if (messageInterval) clearInterval(messageInterval);
            messageInterval = setInterval(updateMessages, 2000); // 2000 ms = 2 secondes
        }
        setTimeout(scrollToBottom, 1000); // 2000 ms = 2 secondes
        
            function updateMessages() {
            if (!currentReceiverId) return;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_messages.php?receiver_id=${currentReceiverId}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messa').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        const new_mess = document.getElementById('me_send');
        new_mess.onsubmit = function(event){
            event.preventDefault();
            const message = document.getElementById('messageInput').value;
            if (!currentReceiverId || !message.trim()) return;
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messageInput').value = "";
                    updateMessages();
                }
            };
            xhr.send(`receiver_id=${currentReceiverId}&message=${encodeURIComponent(message.trim())}`);
            setTimeout(scrollToBottom, 1000);
        }

        function updateDiscussionHeader(userName, userProfile, userDate, userOccup, userLieu, userGenre, userSitu, userEtat) {
        document.getElementById('profilePic').src = userProfile;
        document.getElementById('pro').src = userProfile;
        document.getElementById('pro2').src = userProfile;
        // document.querySelectorAll('.pro3').src = userProfile;
        document.getElementById('username').innerText = userName;
        document.getElementById('nom').innerText = userName;
        document.getElementById('nom2').innerText = userName;
        document.getElementById('date').innerText = userDate;
        document.getElementById('occup').innerText = userOccup;
        document.getElementById('lieu').innerText = userLieu;
        document.getElementById('genre').innerText = userGenre;
        document.getElementById('situ').innerText = userSitu;
        document.getElementById('con1').innerText = userEtat;
        document.getElementById('con2').innerText = userEtat;
        }

// Modifier Profil

        document.getElementById('uploadIcon').onclick = function() {
            document.getElementById('imageInput').click();
            // document.getElementById('send').style.display = 'block';
        };
        document.getElementById('imageInput').onchange = function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    document.getElementById('send').style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            }
        };

// Modifier le theme

function toggleTheme() {
    // Récupérer l'état actuel du bouton
    const checkbox = document.getElementById('themeSwitch');
    const newState = checkbox.checked ? 'sombre' : 'clair';

    // Rediriger vers la page de mise à jour du thème
    window.location.href = `update_theme.php?theme=${newState}`;
}


// Reste

    const users = document.querySelectorAll('.user');
        const message = document.querySelector('.discussion');
        const info = document.querySelector('.infor');
        const aucun = document.querySelector('.aucun');
        const ferm = document.querySelector('.ferm');
        const scrollableDiv = document.querySelector('.messages');
        const param = document.getElementById('#param');
        const para = document.querySelector('.para');
        const close = document.querySelector('.close');
                // Ouvrir le modal
        document.getElementById('openModal').onclick = function() {
            document.getElementById('settingsModal').style.display = 'block';
        };

        // Fermer le modal quand l'utilisateur clique sur la croix (X)
        document.querySelector('.close').onclick = function() {
            document.getElementById('settingsModal').style.display = 'none';
        };

        // Fermer le modal quand l'utilisateur clique en dehors du modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('settingsModal')) {
                document.getElementById('settingsModal').style.display = 'none';
            }
        };

        
        document.getElementById('logoutBtn').onclick = function() {
            // Ajoutez ici la logique pour se déconnecter
            if (confirm('Êtes-vous sûr de vouloir vous deconnecter ?')) {
                window.location.href = "decon.php?id=<?php echo $_SESSION['user_id'] ?>";
            }
            alert('Déconnecté');
        };

        // Supprimer le compte
        document.getElementById('deleteAccountBtn').onclick = function() {
            // Ajoutez ici la logique pour supprimer le compte
            if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
                window.location.href = "del.php?id=<?php echo $_SESSION['user_id'] ?>";
                alert('Compte supprimé');
            }
        };


        // // Ouvrir le modal des informations personnelles
        document.getElementById('editPersonalInfoBtn').onclick = function() {
            document.getElementById('personalInfoModal').style.display = 'block';
        };

        // Fermer le modal des informations personnelles quand l'utilisateur clique sur la croix (X)
        document.querySelector('#personalInfoModal .close').onclick = function() {
            document.getElementById('personalInfoModal').style.display = 'none';
        };

        // Fermer le modal des informations personnelles quand l'utilisateur clique en dehors du modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('personalInfoModal')) {
                document.getElementById('personalInfoModal').style.display = 'none';
            }
        };
        
        users.forEach(user => {
            user.onclick = function () {
                
            }
        })
        updateUsers();
        // Configurer le rafraîchissement automatique
        if (userInterval) clearInterval(userInterval);
        userInterval = setInterval(updateUsers, 2000); // 2000 ms = 2 secondes
        
        function me(userId,userName,userProfil,userDate,userOccup,userLieu,userGenre,userSitu,userEtat) {
                message.classList.add('active');
                info.classList.add('active');
                aucun.classList.add('active');
                users.forEach(d => d.style.backgroundColor = 'transparent');
                // this.style.backgroundColor = 'rgb(152, 192, 230)';
                if (document.querySelector('body.sombre')) {
                    users.forEach(d => d.style.backgroundColor = 'transparent');
                    // this.style.backgroundColor = 'rgba(255, 255, 255, 0.062)';
                }

                document.getElementById('ut').classList.add('active');

                if (userProfil == "") {
                    userProfil = "img/profile.png";
                }
                if (userDate == "") {
                    userDate = "Date de naissance : Aucun";
                }else{
                    userDate = "Date de naissance : "+userDate;
                }
                if (userOccup == "") {
                    userOccup = "Travail : Aucun";
                }else{
                    userOccup = "Travail : "+userOccup;
                }
                if (userLieu == "") {
                    userLieu = "Lieu de Travail : Aucun";
                }else{
                    userLieu = "Lieu de Travail : "+userLieu;
                }
                if (userGenre == "") {
                    userGenre = "Genre : Aucun";
                }else{
                    userGenre = "Genre : "+userGenre;
                }
                if (userSitu == "") {
                    userSitu = "Situation Amoureuse : Aucun";
                }else{
                    userSitu = "Situation Amoureuse : "+userSitu;
                }
                if (userEtat == "1") {
                    userEtat = "Connecté";
                    document.getElementById('con1').classList.remove('dec');
                    document.getElementById('con2').classList.remove('dec');
                }else{
                    userEtat = "Deconnecté";
                    document.getElementById('con1').classList.add('dec');
                    document.getElementById('con2').classList.add('dec');
                }
                // scrollToBottom();
                
                loadMessages(userId)
                
                updateDiscussionHeader(userName, userProfil, userDate, userOccup, userLieu, userGenre, userSitu, userEtat);
        }
        function updateUsers() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `show_user.php`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {

                    document.getElementById('aff').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        
        document.getElementById('retour1').onclick = function () {
            document.getElementById('ut').classList.remove('active');
            message.classList.remove('active');
            info.classList.remove('active');
        }
        document.getElementById('in').onclick = function () {
            info.classList.add('activ');
            message.classList.add('pause');
        }
        document.getElementById('retour2').onclick = function () {
            info.classList.remove('activ');
            message.classList.remove('pause');
        }
        ferm.onclick = function () {
            info.classList.remove('activ');
            message.classList.remove('pause');
            message.classList.remove('active');
            document.getElementById('ut').classList.remove('active');
            info.classList.remove('active');
            aucun.classList.remove('active');
            // <?php
            //     $id = $_SESSION['user_id'];
            //     $sql = "UPDATE discussion SET etat = '0' WHERE id_moi = '$id' OR id_autre = '$id'";
            //     $stmt = $conn->prepare($sql);
            //     $stmt->execute();
            // ?>
        }
        const messag = document.getElementById('messages');
        messag.onscroll = function(){
            const haut = document.getElementById('desc_but');
            if ( messag.scroll == 0) {
                haut.classList.toggle("active");
            }else{
                haut.classList.add("active");
            }
        }
        
        const new_friend = document.getElementById('new_friend');
        let ajoute = document.getElementById('ajoute');
        let clo_new = document.getElementById('clo_new');
        ajoute.onclick = function () {
            new_friend.classList.add("active");
        }
        clo_new.onclick = function () {
            new_friend.classList.remove("active");
        }
        
        let aj = document.querySelectorAll('.aj');
        aj.forEach(ajout=> {
            ajout.onclick = function(){
                ajout.classList.toggle('av');
            if (ajout.textContent == "Ajouté(e)") {
                ajout.innerText = "+";
            }else{
                ajout.innerText = "Ajouté(e)";
            }
            let id = ajout.getAttribute('data-id');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_friend.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // ajout.innerText = "Ajouté(e)";
                }
            };
            xhr.send(`autre_id=${id}`);
        }
    })
    window.onclick = function(event) {
        if (event.target == new_friend) {
            new_friend.classList.remove("active");
        }
    };
        aj.forEach(aju =>  {
            // aju.innerText = "Ajouté(e)";
            let id = aju.getAttribute('data-id');
            
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `friend.php?autre_id=${id}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if(xhr.responseText == "non"){
                        aju.innerText = "+";
                    }else if (xhr.responseText == "oui") {
                        aju.classList.remove('av');
                        aju.innerText = "Ajouté(e)";
                        aju.innerText = "Ajouté(e)";
                    }
                }
            };

            xhr.send();

            
        })