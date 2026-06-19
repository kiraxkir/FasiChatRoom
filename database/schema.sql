SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS fasichatroom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fasichatroom;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level VARCHAR(50) NOT NULL,
    year YEAR NOT NULL,
    UNIQUE KEY uq_promotions_name_year (name, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(120) NULL UNIQUE,
    motDePasse VARCHAR(255) NOT NULL,
    role ENUM('etudiant','enseignant','assistant','doyen','vicedoyen','apparitaire') NOT NULL,
    dateCreation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    promotion_id INT NULL,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    titre VARCHAR(150) NOT NULL,
    description TEXT NULL,
    teacher_id INT NOT NULL,
    promotion_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enseignants_cours (
    enseignant_id INT NOT NULL,
    cours_id INT NOT NULL,
    PRIMARY KEY (enseignant_id, cours_id),
    FOREIGN KEY (enseignant_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE etudiants_cours (
    etudiant_id INT NOT NULL,
    cours_id INT NOT NULL,
    PRIMARY KEY (etudiant_id, cours_id),
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    contenu TEXT NULL,
    dateEnvoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    message_type ENUM('prive','public','convocation') NOT NULL DEFAULT 'prive',
    promotion_id INT NULL,
    cours_id INT NULL,
    destinataire_id INT NULL,
    reply_to INT NULL,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (reply_to) REFERENCES messages(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE convocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    objet VARCHAR(200) NOT NULL,
    description TEXT NULL,
    dateReunion DATE NOT NULL,
    heure TIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    dateEnvoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE convocation_recipients (
    convocation_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('enseignant','assistant','student','admin') NULL,
    seen_at DATETIME NULL,
    PRIMARY KEY (convocation_id, user_id),
    FOREIGN KEY (convocation_id) REFERENCES convocations(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mur_pedagogique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    type ENUM('question','annonce') NOT NULL DEFAULT 'question',
    datePublication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE annonces_valve (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    contenu TEXT NOT NULL,
    datePublication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dateExpiration DATETIME NULL,
    categorie ENUM('urgent','convocation','information','academique') NOT NULL DEFAULT 'information',
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fichiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    taille BIGINT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    typeMime VARCHAR(100) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    session_data TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
