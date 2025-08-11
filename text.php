<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <style>
        /* Styles pour la mise en page */
        .liste {
            width: 20%;
            float: left;
            border-right: 1px solid #ddd;
            padding-right: 10px;
        }

        .user {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        .user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .info {
            display: flex;
            flex-direction: column;
        }

        .discussion {
            width: 75%;
            float: left;
            padding-left: 10px;
        }

        .entete {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }

        .entete img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .messages {
            height: 400px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }

        .envoie {
            display: flex;
            align-items: center;
        }

        .envoie textarea {
            flex: 1;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    
    
    

    <script>
        let currentReceiverId = null;
        let messageInterval = null;

        function loadMessages(receiverId) {
            currentReceiverId = receiverId;
            updateMessages();
            // Configurer le rafraîchissement automatique
            if (messageInterval) clearInterval(messageInterval);
            messageInterval = setInterval(updateMessages, 2000); // 2000 ms = 2 secondes

            // Mettre à jour les informations du profil
            document.getElementById('profilePic').src = `path_to_profile_pic/${receiverId}.jpg`; // Remplacer par la source réelle
            document.getElementById('username').innerText = `Nom de l'utilisateur ${receiverId}`; // Remplacer par le nom réel
        }

        function updateMessages() {
            if (!currentReceiverId) return;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_messages.php?receiver_id=${currentReceiverId}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messages').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function sendMessage() {
            const message = document.getElementById('messageInput').value;
            if (!currentReceiverId || !message.trim()) return;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messageInput').value = '';
                    updateMessages();
                }
            };
            xhr.send(`receiver_id=${currentReceiverId}&message=${encodeURIComponent(message.trim())}`);
        }
    </script>
</body>
</html>
