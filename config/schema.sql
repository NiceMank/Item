CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    prenom VARCHAR(80) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    etat_compte CHAR(1) NOT NULL DEFAULT '0',
    profil VARCHAR(255) NOT NULL DEFAULT '',
    theme VARCHAR(20) NOT NULL DEFAULT 'clair',
    genre VARCHAR(40) NOT NULL DEFAULT '',
    date_nais VARCHAR(20) NOT NULL DEFAULT '',
    travail VARCHAR(120) NOT NULL DEFAULT '',
    Lieu_travail VARCHAR(120) NOT NULL DEFAULT '',
    situation VARCHAR(80) NOT NULL DEFAULT '',
    remember_token CHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_email (email),
    UNIQUE KEY uniq_remember (remember_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    pub_text TEXT NULL,
    pub_image VARCHAR(255) NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_pub_user (id_user),
    KEY idx_pub_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS storie (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_uti INT UNSIGNED NOT NULL,
    story_text TEXT NULL,
    story_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_story_user (id_uti)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS likes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    id_pub INT UNSIGNED NOT NULL,
    user_name VARCHAR(160) NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_like (id_user, id_pub),
    KEY idx_like_pub (id_pub)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commentaires (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    id_pub INT UNSIGNED NOT NULL,
    commentaire TEXT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_comment_pub (id_pub),
    KEY idx_comment_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS discussion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_moi INT UNSIGNED NOT NULL,
    id_autre INT UNSIGNED NOT NULL,
    etat CHAR(1) NOT NULL DEFAULT '1',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_disc_pair (id_moi, id_autre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_moi INT UNSIGNED NOT NULL,
    id_autre INT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    discussion_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_msg_pair (id_moi, id_autre),
    KEY idx_msg_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    message VARCHAR(255) NOT NULL,
    type VARCHAR(32) NOT NULL DEFAULT 'info',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_notif_user (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
