SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';
SET NAMES utf8mb4;
USE fasichatroom;

-- Roles
INSERT INTO roles (name, description) VALUES
('etudiant', 'Étudiant'),
('enseignant', 'Enseignant'),
('assistant', 'Assistant'),
('doyen', 'Doyen'),
('vicedoyen', 'Vice-Doyen'),
('apparitaire', 'Apparitaire')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Promotions
INSERT INTO promotions (name, level, year) VALUES
('L2 FASI', 'Licence 2', 2024),
('L3 FASI', 'Licence 3', 2024)
ON DUPLICATE KEY UPDATE level = VALUES(level), year = VALUES(year);

-- Courses
INSERT INTO cours (code, titre, description, teacher_id, promotion_id, is_active, created_at) VALUES
('PHP-POO-L2', 'PHP POO — L2', 'Programmation orientée objet en PHP', NULL, 1, 1, NOW()),
('WEB-L2', 'Programmation Web — L2', 'Introduction au développement Web', NULL, 1, 1, NOW()),
('CYB-L4', 'Cybersécurité avancée L4', 'Sécurité des systèmes', NULL, NULL, 1, NOW())
ON DUPLICATE KEY UPDATE titre = VALUES(titre), description = VALUES(description), promotion_id = VALUES(promotion_id);

-- Example Valve announcements (leave auteur_id to be set after users are created)
-- INSERT INTO annonces_valve (auteur_id, titre, contenu, datePublication, dateExpiration, categorie) VALUES
-- (1, 'Annonce exemple', 'Ceci est une annonce de test', NOW(), NULL, 'information');

-- Example Convocation (set expediteur_id to a valid admin user after creating users)
-- INSERT INTO convocations (expediteur_id, objet, description, dateReunion, heure, lieu, dateEnvoi) VALUES
-- (1, 'Réunion pédagogique', 'Ordre du jour : ...', '2026-07-01', '14:00:00', 'Salle A-12', NOW());

-- Note for inserting users:
-- Use PHP to generate password hashes: password_hash('YourPassword', PASSWORD_DEFAULT)
-- Example insertion (replace <HASH> with the generated hash):
-- INSERT INTO utilisateurs (matricule, nom, email, motDePasse, role, dateCreation, promotion_id) VALUES
-- ('SI2024001', 'YVE SHONGO', 'si2024001@example.com', '<HASH>', 'etudiant', NOW(), 1);

COMMIT;
