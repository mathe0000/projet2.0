-- Création de la base de données
CREATE DATABASE IF NOT EXISTS shoptonmaillot;
USE shoptonmaillot;

-- Table Utilisateur
CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse_mail VARCHAR(150) NOT NULL UNIQUE,
    numero_tel VARCHAR(20),
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Table Annonce
CREATE TABLE annonce (
    id_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    sport VARCHAR(50),
    genre VARCHAR(20),
    equipe VARCHAR(100),
    type_maillot VARCHAR(50),
    taille VARCHAR(10),
    prix DECIMAL(10,2),
    description TEXT,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

-- Table Favoris
CREATE TABLE favoris (
    id_favoris INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    id_utilisateur INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

-- Table Message
CREATE TABLE message (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_envoyeur INT NOT NULL,
    id_recepteur INT NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_envoyeur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_recepteur) REFERENCES utilisateur(id_utilisateur)
);

-- Table Photo
CREATE TABLE photo (
    id_photo INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce)
);
