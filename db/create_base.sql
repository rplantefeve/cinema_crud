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
CREATE DATABASE IF NOT EXISTS `cinema_crud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cinema_crud`;

-- --------------------------------------------------------

--
-- Table structure for table `cinema`
--

DROP TABLE IF EXISTS `cinema`;
CREATE TABLE IF NOT EXISTS `cinema` (
  `CINEMAID` int NOT NULL AUTO_INCREMENT,
  `DENOMINATION` varchar(50) NOT NULL,
  `ADRESSE` varchar(150) NOT NULL,
  PRIMARY KEY (`CINEMAID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `film`
--

DROP TABLE IF EXISTS `film`;
CREATE TABLE IF NOT EXISTS `film` (
  `FILMID` int NOT NULL AUTO_INCREMENT,
  `TITRE` varchar(100) NOT NULL,
  `TITREORIGINAL` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`FILMID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `prefere`
--

DROP TABLE IF EXISTS `prefere`;
CREATE TABLE IF NOT EXISTS `prefere` (
  `USERID` int NOT NULL DEFAULT '0',
  `FILMID` int NOT NULL DEFAULT '0',
  `COMMENTAIRE` text,
  PRIMARY KEY (`USERID`,`FILMID`),
  KEY `fk_prefere_film` (`FILMID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `seance`
--

DROP TABLE IF EXISTS `seance`;
CREATE TABLE IF NOT EXISTS `seance` (
  `CINEMAID` int NOT NULL DEFAULT '0',
  `FILMID` int NOT NULL DEFAULT '0',
  `HEUREDEBUT` datetime NOT NULL,
  `HEUREFIN` datetime NOT NULL,
  `VERSION` varchar(6) NOT NULL,
  PRIMARY KEY (`CINEMAID`,`FILMID`,`HEUREDEBUT`) USING BTREE,
  KEY `fk_seance_film` (`FILMID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `USERID` int NOT NULL AUTO_INCREMENT,
  `NOM` varchar(50) NOT NULL,
  `PRENOM` varchar(30) NOT NULL,
  `ADRESSECOURRIEL` varchar(90) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;
