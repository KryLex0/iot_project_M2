-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  Dim 13 nov. 2022 à 19:37
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `iot_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `user_location`
--

DROP TABLE IF EXISTS `user_location`;
CREATE TABLE IF NOT EXISTS `user_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_user` varchar(255) COLLATE utf8_bin NOT NULL,
  `postcode_user` int(11) NOT NULL,
  `town_user` varchar(255) COLLATE utf8_bin NOT NULL,
  `country_user` varchar(255) COLLATE utf8_bin NOT NULL,
  `longitude_user` float NOT NULL DEFAULT 0,
  `latitude_user` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `user_location`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
