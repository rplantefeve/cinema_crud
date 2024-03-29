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
(2, 'Les Nemours', '22 rue Ste Claire, 74000 ANNECY');

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
(4, 'Dallas Buyers Club', NULL);

--
-- Vider la table avant d'insérer `prefere`
--

TRUNCATE TABLE `prefere`;
--
-- Contenu de la table `prefere`
--

INSERT INTO `prefere` (`USERID`, `FILMID`, `COMMENTAIRE`) VALUES
(18, 1, ''),
(18, 2, 'Super !');

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
(2, 4, '2014-02-24 16:30:00', '2014-02-24 18:40:00', 'VOSTFR');

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
