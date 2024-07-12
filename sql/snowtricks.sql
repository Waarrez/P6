-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 12 juil. 2024 à 19:08
-- Version du serveur : 10.6.5-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snowtricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trick_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_9474526CB281BE2E` (`trick_id`),
  KEY `IDX_9474526C67B3B43D` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `trick_id`, `content`, `users_id`, `created_at`) VALUES
('01J2BSX5X8MT28EECK3Y7FZGES', '01J1SRX33011SGWH2VHZ09S3F7', 'Très belle figure', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-09 13:11:11'),
('01J2K2HBH7HT4GBREPHTN47AJ1', '01J1SRX33011SGWH2VHZ09S3F7', 'Développeur web', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 08:56:42'),
('01J2K7390JRFSVHC3MJDZKGYG0', '01J1SRX33011SGWH2VHZ09S3F7', 'Test', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 10:16:23'),
('01J2K73BCZ00NWSQBBWPNTW5WE', '01J1SRX33011SGWH2VHZ09S3F7', 'Test commentaire', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 10:16:26'),
('01J2K7XS9ZJTSY4H0SAGG68J0P', '01J1SRX33011SGWH2VHZ09S3F7', 'Très belle figure', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 10:30:52'),
('01J2M51BA1HRN6JQ1TGTYVVZ8S', '01J1SRX33011SGWH2VHZ09S3F7', 'Développeur web', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 18:59:37'),
('01J2M57JR5K5MAR90466B3NBD6', '01J1SRX33011SGWH2VHZ09S3F7', 'Test', '01J29DF4VEXP32D4XMFM495DEC', '2024-07-12 19:03:02');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20231013102957', '2023-10-13 10:30:47', 128),
('DoctrineMigrations\\Version20231026140437', '2023-10-26 14:05:01', 272),
('DoctrineMigrations\\Version20231026160307', '2023-10-26 16:03:20', 44),
('DoctrineMigrations\\Version20231027104538', '2023-10-27 10:45:54', 220),
('DoctrineMigrations\\Version20231124101222', '2023-11-24 10:13:06', 271),
('DoctrineMigrations\\Version20240702121051', '2024-07-02 12:11:06', 52),
('DoctrineMigrations\\Version20240708085624', '2024-07-08 08:56:50', 61),
('DoctrineMigrations\\Version20240708115315', '2024-07-08 11:53:24', 41),
('DoctrineMigrations\\Version20240709123521', '2024-07-09 12:36:03', 249);

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group`
--

INSERT INTO `group` (`id`, `name`) VALUES
('01J1SRRB9QTANJ8ES3G6EK8DPM', 'Butters'),
('01J1SRRRZK2X4H82E7KY1KKHDY', 'Grabs'),
('01J1SRSCMV5NKF57KWK797GPJT', 'Spins '),
('01J1SRT6653CPFJ0MWTYAQ29V6', 'Flips');

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tricks_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E01FBE6A3B153154` (`tricks_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trick`
--

DROP TABLE IF EXISTS `trick`;
CREATE TABLE IF NOT EXISTS `trick` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `groups_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `edit_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8F0A91EF373DCF` (`groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `groups_id`, `name`, `content`, `images`, `medias`, `created_at`, `edit_at`, `slug`) VALUES
('01J1SRX33011SGWH2VHZ09S3F7', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Mute', 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.', '6683fb884f96a.jpg', 'https://www.youtube.com/embed/PFMzcvcowC4', '2024-07-02 13:07:20', '2024-07-02 15:59:09', 'Mute'),
('01J2K4FA2B3NV51293ZMBS4DYK', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Sad', 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.', '6690f7b873fda.jpg', 'https://www.youtube.com/embed/KEdFwJ4SWq4', '2024-07-12 09:30:32', NULL, 'Sad'),
('01J2K4NZ1RQWT51W54N9DC3V7T', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Indy', 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.', '6690f892822ef.jpg', 'https://www.youtube.com/embed/6yA3XqjTh_w', '2024-07-12 09:34:10', NULL, 'Indy'),
('01J2K5F1WFX2YB1Q3N52M0NZ3K', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Stalefish', 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.', '6690fbc89affb.jpg', 'https://www.youtube.com/embed/f9FjhCt_w2U', '2024-07-12 09:47:52', NULL, 'Stalefish'),
('01J2K5N1BSFEBXPG313PQ175EE', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Tail grab', 'Saisie de la partie arrière de la planche, avec la main arrière.', '6690fc8cabb9c.jpg', 'https://www.youtube.com/embed/_Qq-YoXwNQY', '2024-07-12 09:51:08', NULL, 'Tail-grab'),
('01J2K5QM476JBZW0DMDHWXGB59', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Nose grab', 'Saisie de la partie avant de la planche, avec la main avant.', '6690fce1700e2.jpg', 'https://www.youtube.com/embed/M-W7Pmo-YMY', '2024-07-12 09:52:33', NULL, 'Nose-grab'),
('01J2K5YCPEYQZ3GYNCRY3MM8Z3', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Japan air', 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.', '6690fdbf39009.jpg', 'https://www.youtube.com/embed/jH76540wSqU', '2024-07-12 09:56:15', NULL, 'Japan-air'),
('01J2K60Z4SYJ5JFM4DY55TFSM6', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Seat belt', 'Saisie du carre frontside à l\'arrière avec la main avant.', '6690fe139e091.jpg', 'https://www.youtube.com/embed/4vGEOYNGi_c', '2024-07-12 09:57:39', NULL, 'Seat-belt'),
('01J2K65KT4GSMGPVKD82ERMD8S', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Truck driver', 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).', '6690feabd7c53.jpg', 'https://www.youtube.com/embed/mslkQJbORDM', '2024-07-12 10:00:11', NULL, 'Truck-driver');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirm_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `email`, `confirm_account`, `user_picture`) VALUES
('01J29DF4VEXP32D4XMFM495DEC', 'Warez', '[]', '$2y$13$/eOa1nBdIyItjuuEdKAXfOiGhdlFv5.m37rfceT.7Hkh/GZFA6owS', 'thimote.cabotte6259@gmail.com', '', '668bfdd80a656.jpg'),
('01J29G4ES9657ZEYH3BPEDC3SQ', 'Zitrox', '[]', '$2y$13$4PxPo/sC01gOLbW8JDEoOOVoazMFoqoOMu1WpLVkFjjZevcHHN9rS', 'zitroxofficiel@gmail.com', '', '');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C67B3B43D` FOREIGN KEY (`users_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `FK_E01FBE6A3B153154` FOREIGN KEY (`tricks_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `trick`
--
ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91EF373DCF` FOREIGN KEY (`groups_id`) REFERENCES `group` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
