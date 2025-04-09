-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2024 at 01:41 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `cinema_crud`
--
USE `cinema_crud`;

--
-- Vider la table avant d'insérer `cinema`
--

TRUNCATE TABLE `cinema`;
--
-- Contenu de la table `cinema`
--

INSERT INTO `cinema` (`CINEMAID`, `DENOMINATION`, `ADRESSE`) VALUES
(1, 'Décavision', '7 avenue de Brogny, 74000 ANNECY'),
(2, 'Les Nemours', '22 rue Ste Claire, 74000 ANNECY'),
(3, 'UGC Astoria', '31 Cours Vitton, 69006 Lyon'),
(4, 'Pathé Bellecour', '79 Rue de la République, 69002 Lyon'),
(5, 'Cinéma Comoedia', '13 Avenue Berthelot, 69007 Lyon');


--
-- Vider la table avant d'insérer `film`
--

TRUNCATE TABLE `film`;
--
-- Contenu de la table `film`
--

INSERT INTO `film` (`FILMID`, `TITRE`, `TITREORIGINAL`) VALUES
(1, 'Un beau dimanche', NULL),
(2, 'La Grande Aventure Lego', 'The Lego Movie'),
(3, 'American Bluff', 'American Hustle'),
(4, 'Dallas Buyers Club', NULL),
(5, 'Wicked : Partie 1', 'Wicked: Part One'),
(6, 'Sonic 3: Le film', 'Sonic the Hedgehog 3'),
(7, 'Long Distance', 'Distant'),
(8, 'Avatar : La Voie de l''eau', 'Avatar: The Way of Water'),
(9, 'Spider-Man: Across the Spider-Verse', NULL),
(10, 'Mission Impossible : Dead Reckoning, Partie 2', 'Mission: Impossible - The Final Reckoning'),
(11, 'Dune : Deuxième Partie', 'Dune: Part Two'),
(12, 'The Marvels', NULL),
(13, 'Indiana Jones et le Cadran de la destinée', 'Indiana Jones and the Dial of Destiny'),
(14, 'Barbie', NULL),
(15, 'Oppenheimer', NULL),
(16, 'La Petite Sirène', 'The Little Mermaid'),
(17, 'The Flash', NULL);



--
-- Vider la table avant d'insérer `seance`
--

TRUNCATE TABLE `seance`;
--
-- Contenu de la table `seance`
--

INSERT INTO `seance` (`CINEMAID`, `FILMID`, `HEUREDEBUT`, `HEUREFIN`, `VERSION`) VALUES
(1, 2, '2014-02-24 17:30:00', '2014-02-24 19:20:00', 'VF'),
(1, 3, '2014-02-25 19:25:00', '2014-02-25 21:55:00', 'VF'),
(2, 1, '2014-02-24 19:00:00', '2014-02-24 20:50:00', 'VF'),
(2, 2, '2014-02-24 20:00:00', '2014-02-24 21:50:00', 'VF'),
(2, 4, '2014-02-24 16:30:00', '2014-02-24 18:40:00', 'VOSTFR'),
(3, 5, '2025-10-22 20:00:00', '2025-10-22 22:30:00', 'VOST'),
(3, 6, '2025-04-18 19:00:00', '2025-04-18 21:30:00', 'VF'),
(4, 7, '2025-05-01 16:00:00', '2025-05-01 18:00:00', 'VF'),
(1, 7, '2025-04-12 14:00:00', '2025-04-12 16:00:00', 'VOSTFR'),
(1, 1, '2025-04-12 16:30:00', '2025-04-12 18:20:00', 'VF'),
(1, 8, '2025-04-12 20:00:00', '2025-04-12 23:00:00', '3D'),
(2, 5, '2025-04-13 13:45:00', '2025-04-13 16:15:00', 'VF'),
(2, 6, '2025-04-13 17:00:00', '2025-04-13 19:30:00', 'VOST'),
(2, 9, '2025-04-13 20:00:00', '2025-04-13 22:30:00', 'VF'),
(3, 10, '2025-04-14 18:30:00', '2025-04-14 21:00:00', 'VOSTFR'),
(3, 11, '2025-04-14 21:30:00', '2025-04-15 00:00:00', 'VF'),
(3, 14, '2025-04-15 10:00:00', '2025-04-15 12:00:00', 'VF'),
(4, 12, '2025-04-15 14:00:00', '2025-04-15 16:00:00', 'VF'),
(4, 13, '2025-04-15 16:30:00', '2025-04-15 19:00:00', 'VOST'),
(4, 15, '2025-04-15 19:30:00', '2025-04-15 22:30:00', 'VO'),
(5, 16, '2025-04-16 11:00:00', '2025-04-16 13:00:00', 'VF'),
(5, 17, '2025-04-16 14:00:00', '2025-04-16 16:30:00', '3D'),
(5, 3,  '2025-04-16 17:00:00', '2025-04-16 19:30:00', 'VOSTFR');


--
-- Vider la table avant d'insérer `utilisateur`
--

TRUNCATE TABLE `utilisateur`;
--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`USERID`, `NOM`, `PRENOM`, `ADRESSECOURRIEL`, `PASSWORD`) VALUES
(18, 'Ponsard', 'Yann', 'yp@yp.yp', '$2y$10$cOyLS7z8BjbpjEflNjFYFuBhMp0zsCJxrjrkXIGZzYhTH/xdwFxpe'),
(19, 'rp', 'rp', 'rp@rp.rp', '$2y$10$5QzyFQLLfzuGNkveZnmqjOLSgxGvQuhxno63tzViYw4E04cgKjk76'),
(20, 'admin', 'admin', 'admin@adm.adm', '$2y$10$r2maAvjdNftF8bzZMRl8..m6aIxo.jthpa..tgSp1ehDN.YLNFuNe');

COMMIT;
