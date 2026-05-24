CREATE DATABASE IF NOT EXISTS todolist
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE todolist;

-- Création des tables

CREATE TABLE CATEGORIE
(
    id_categorie INT NOT NULL AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_categorie)
) ENGINE=InnoDB;

CREATE TABLE PRIORITE
(
    id_priorite INT NOT NULL AUTO_INCREMENT,
    libelle_priorite VARCHAR(30) NOT NULL,
    niveau INT NOT NULL,
    PRIMARY KEY (id_priorite)
) ENGINE=InnoDB;

CREATE TABLE STATUT
(
    id_statut INT NOT NULL AUTO_INCREMENT,
    libelle_statut VARCHAR(30) NOT NULL,
    PRIMARY KEY (id_statut)
) ENGINE=InnoDB;

CREATE TABLE ETIQUETTE
(
    id_etiquette INT NOT NULL AUTO_INCREMENT,
    nom_etiquette VARCHAR(50) NOT NULL,
    couleur CHAR(7) NOT NULL,
    PRIMARY KEY (id_etiquette)
) ENGINE=InnoDB;

CREATE TABLE TACHE
(
    id_tache INT NOT NULL AUTO_INCREMENT,
    titre VARCHAR(50) NOT NULL,
    description VARCHAR(150),
    date_limite DATE,
    date_creation DATE NOT NULL,
    date_modification DATE NOT NULL,
    id_categorie INT NOT NULL,
    id_priorite INT NOT NULL,
    id_statut INT NOT NULL,
    PRIMARY KEY (id_tache),
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie),
    FOREIGN KEY (id_priorite) REFERENCES PRIORITE(id_priorite),
    FOREIGN KEY (id_statut) REFERENCES STATUT(id_statut)
) ENGINE=InnoDB;

CREATE TABLE EST_MARQUEE_PAR
(
    id_tache INT NOT NULL,
    id_etiquette INT NOT NULL,
    PRIMARY KEY (id_tache, id_etiquette),
    FOREIGN KEY (id_tache) REFERENCES TACHE(id_tache),
    FOREIGN KEY (id_etiquette) REFERENCES ETIQUETTE(id_etiquette)
) ENGINE=InnoDB;

-- Insertion des données

INSERT INTO CATEGORIE (nom_categorie) VALUES
('Études'),
('Travail'),
('Courses'),
('Maison'),
('Autre');

INSERT INTO PRIORITE (libelle_priorite, niveau) VALUES
('Peu important', 1),
('Important', 2),
('Très important', 3);

INSERT INTO STATUT (libelle_statut) VALUES
('En attente'),
('En cours'),
('Terminé');

INSERT INTO ETIQUETTE (nom_etiquette, couleur) VALUES
('Urgent', '#dc3545'),
('Facile', '#28a745'),
('Long', '#ffc107'),
('Rapide', '#17a2b8'),
('Créatif', '#6f42c1');

-- DONNEES DE TEST (tâches exemples)

INSERT INTO TACHE (titre, description, date_limite, date_creation, date_modification, id_categorie, id_priorite, id_statut) VALUES
('Réviser PHP', 'Revoir les chapitres sur mysqli et les sessions', '2026-06-10', CURDATE(), CURDATE(), 1, 3, 2),
('Faire les courses', 'Fruits, légumes, pain', '2026-05-26', CURDATE(), CURDATE(), 3, 2, 1),
('Ranger le salon', NULL, '2026-05-30', CURDATE(), CURDATE(), 4, 1, 1),
('Préparer le rapport de stage', 'Rédiger l\'introduction et le plan', '2026-06-15', CURDATE(), CURDATE(), 2, 3, 2),
('Acheter cadeau anniversaire', 'Idée : livre ou jeu de société', '2026-06-01', CURDATE(), CURDATE(), 5, 2, 1),
('Terminer le projet TodoList', 'Finir le dashboard et les filtres', '2026-06-20', CURDATE(), CURDATE(), 1, 3, 2),
('Nettoyer la cuisine', NULL, NULL, CURDATE(), CURDATE(), 4, 1, 3),
('Répondre aux mails pro', 'Mails de la semaine dernière', '2026-05-25', CURDATE(), CURDATE(), 2, 2, 1);

INSERT INTO EST_MARQUEE_PAR (id_tache, id_etiquette) VALUES
(1, 2),
(1, 4),
(2, 4),
(4, 1),
(4, 3),
(6, 1),
(6, 3),
(7, 2),
(8, 1);
