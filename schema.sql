-- ===============================================
-- 1. CREATION DE LA BASE DE DONNEES (Schema) 
-- ===============================================

DROP TABLE IF EXISTS Trajets;
DROP TABLE IF EXISTS UserAccounts;
DROP TABLE IF EXISTS ProfilsUtilisateurs; 
DROP TABLE IF EXISTS Agences;
DROP TABLE IF EXISTS Utilisateurs;

-- Table Agencies (Agences/Villes)
CREATE TABLE Agences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL
);

-- Table Users (Informations de l'employé - Non modifiable par l'app)
CREATE TABLE ProfilsUtilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL 
);

-- Table UserAccounts (Comptes de connexion)
CREATE TABLE UserAccounts (
    user_id INT PRIMARY KEY, 
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER' NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES ProfilsUtilisateurs(id) ON DELETE CASCADE
);

-- Table Trips (Trajets planifiés)
CREATE TABLE Trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agence_depart_id INT NOT NULL,
    agence_arrivee_id INT NOT NULL,
    date_depart DATETIME NOT NULL, 
    date_arrivee DATETIME NOT NULL,
    total_places SMALLINT NOT NULL,
    places_disponibles SMALLINT NOT NULL,
    personne_contact VARCHAR(255),
    auteur_id INT NOT NULL, 
    FOREIGN KEY (agence_depart_id) REFERENCES Agences(id),
    FOREIGN KEY (agence_arrivee_id) REFERENCES Agences(id),
    FOREIGN KEY (auteur_id) REFERENCES ProfilsUtilisateurs(id)
);


-- ===============================================
-- 2. INSERTION DES DONNEES INITIALES (Seed Data)
-- ===============================================

-- --- A. AGENCES ---
INSERT INTO Agences (nom) VALUES
('Paris'), ('Lyon'), ('Marseille'), ('Toulouse'), ('Nice'), ('Nantes'), ('Strasbourg'), 
('Montpellier'), ('Bordeaux'), ('Lille'), ('Rennes'), ('Reims');

-- --- B. PROFIL UTILISATEUR (Profils RH) ---
INSERT INTO ProfilsUtilisateurs (nom, prenom, telephone, email) VALUES
('Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr'), -- ID 1 (Admin par défaut)
('Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr'),    -- ID 2
('Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr'), -- ID 3
('Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr'), -- ID 4
('Lefèvre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr'),   -- ID 5
('Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr');     -- ID 6 

-- --- C. COMPTES UTILISATEUR (Simulés) ---
INSERT INTO UserAccounts (user_id, password_hash, role) VALUES
(1, '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefgh', 'ROLE_ADMIN'), 
(6, '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefgh', 'ROLE_USER');    

-- --- D. TRAJETS DE TEST ---
INSERT INTO Trajets (agence_depart_id, agence_arrivee_id, date_depart, date_arrivee, total_places, places_disponibles, personne_contact, auteur_id) VALUES
(1, 2, CURDATE() - INTERVAL 3 DAY + INTERVAL '09:00:00' HOUR_SECOND, CURDATE() - INTERVAL 3 DAY + INTERVAL '12:00:00' HOUR_SECOND, 4, 2, 'Contact Lyon', 1),
(5, 3, CURDATE() + INTERVAL 2 DAY + INTERVAL '08:00:00' HOUR_SECOND, CURDATE() + INTERVAL 2 DAY + INTERVAL '11:00:00' HOUR_SECOND, 2, 1, 'Contact Nice/Marseille', 6),
(9, 1, CURDATE() + INTERVAL 5 DAY + INTERVAL '14:00:00' HOUR_SECOND, CURDATE() + INTERVAL 5 DAY + INTERVAL '17:00:00' HOUR_SECOND, 8, 8, 'Contact Bordeaux', 1),
(1, 5, CURDATE() - INTERVAL 7 DAY + INTERVAL '09:00:00' HOUR_SECOND, CURDATE() - INTERVAL 7 DAY + INTERVAL '12:00:00' HOUR_SECOND, 3, 1, 'Ancien Trajet', 6);
-- FIN DU SCHEMA SQL