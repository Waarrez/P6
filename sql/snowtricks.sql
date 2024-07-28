-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : sam. 27 juil. 2024 à 15:48
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
('DoctrineMigrations\\Version20240709123521', '2024-07-09 12:36:03', 249),
('DoctrineMigrations\\Version20240714101846', '2024-07-20 14:59:11', 297),
('DoctrineMigrations\\Version20240717165150', '2024-07-20 14:59:12', 149),
('DoctrineMigrations\\Version20240717165616', '2024-07-20 14:59:12', 288),
('DoctrineMigrations\\Version20240717165831', '2024-07-20 14:59:12', 159),
('DoctrineMigrations\\Version20240717170053', '2024-07-20 14:59:12', 182),
('DoctrineMigrations\\Version20240717170149', '2024-07-20 14:59:13', 154),
('DoctrineMigrations\\Version20240722075249', '2024-07-22 07:53:01', 184),
('DoctrineMigrations\\Version20240722075646', '2024-07-22 07:56:52', 40),
('DoctrineMigrations\\Version20240722080952', '2024-07-22 08:09:55', 45),
('DoctrineMigrations\\Version20240726073051', '2024-07-26 07:30:58', 74),
('DoctrineMigrations\\Version20240726075522', '2024-07-26 07:55:26', 118);

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
-- Structure de la table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tricks_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C53D045F3B153154` (`tricks_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id`, `tricks_id`, `name`) VALUES
('01J3TDSBJ1K072ASS0DWVSH9K7', '01J3TCRA93ZMV49BKNK0EPHMC2', 'af9e33d19a47c16df803898fd6027043.webp'),
('01J3TDXTYWQ2P45Y1G20J9XS1N', '01J3TCRA93ZMV49BKNK0EPHMC2', '02895a960ded06799cac13cf8f4bf8fa.webp');

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `images` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `edit_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D8F0A91E5E237E06` (`name`),
  KEY `IDX_D8F0A91EF373DCF` (`groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `groups_id`, `name`, `content`, `images`, `created_at`, `edit_at`, `slug`) VALUES
('01J3TCRA93ZMV49BKNK0EPHMC2', '01J1SRRB9QTANJ8ES3G6EK8DPM', 'Mute', 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.', '66a5117305fde.jpg', '2024-07-27 15:25:39', '2024-07-27 15:46:57', 'Mute'),
('01J3TCTM25VBA4WP36007HTTZ0', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Sad', 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.', '66a511be8ce62.jpg', '2024-07-27 15:26:54', NULL, 'Sad'),
('01J3TCXSQAVX33R714FTP5PW0E', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Indy', 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.', '66a51226a5e2c.jpg', '2024-07-27 15:28:38', NULL, 'Indy'),
('01J3TCYJH792YD7ET5GK56JRDP', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Stalefish', 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière', '66a512bbe3ecd.jpg', '2024-07-27 15:29:04', '2024-07-27 15:31:07', 'Stalefish'),
('01J3TCZ28BSQWYK6V339EMP4PK', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Tail grab', 'Saisie de la partie arrière de la planche, avec la main arrière;', '66a512d7a24fe.webp', '2024-07-27 15:29:20', '2024-07-27 15:31:35', 'Tail-grab'),
('01J3TCZFBPZC33ZHPXC0QN5ZGZ', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Nose grab', 'Saisie de la partie avant de la planche, avec la main avant', '66a512efdc51e.webp', '2024-07-27 15:29:33', '2024-07-27 15:31:59', 'Nose-grab'),
('01J3TD02E2MGHB617BQ0TWFSKG', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Japan Air', 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.', '66a5133abd614.jpg', '2024-07-27 15:29:53', '2024-07-27 15:33:14', 'Japan-Air'),
('01J3TD0TKXSTZX287HC3TVSAN4', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Seat belt', 'Saisie du carre frontside à l\'arrière avec la main avant', '66a513b0ee10c.jpg', '2024-07-27 15:30:17', '2024-07-27 15:35:12', 'Seat-belt'),
('01J3TD1FQHHMZP8HF9J4MA120S', '01J1SRRRZK2X4H82E7KY1KKHDY', 'Truck driver', 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).', '66a513d1310e0.jpg', '2024-07-27 15:30:39', '2024-07-27 15:35:45', 'Truck-driver');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
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
('01J29DF4VEXP32D4XMFM495DEC', 'Warez', '[]', '$2y$13$OiVhjh34tS9qcNcXrmk9/eCDkU7k5r4Yd8EPdVvDUusxW3vHkWWbe', 'thimote.cabotte6259@gmail.com', '', '668bfdd80a656.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `video`
--

DROP TABLE IF EXISTS `video`;
CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tricks_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7CC7DA2C3B153154` (`tricks_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `video`
--

INSERT INTO `video` (`id`, `tricks_id`, `url`) VALUES
(45, '01J2K60Z4SYJ5JFM4DY55TFSM6', 'https://www.youtube.com/watch?v=jWmfT-qjm2Y&t=2015s'),
(46, '01J2K60Z4SYJ5JFM4DY55TFSM6', 'https://www.youtube.com/embed/mBB7CznvSPQ'),
(49, '01J2K65KT4GSMGPVKD82ERMD8S', 'https://www.youtube.com/embed/mBB7CznvSPQ'),
(53, '01J2K4NZ1RQWT51W54N9DC3V7T', 'https://www.youtube.com/embed/mBB7CznvSPQ'),
(54, '01J2K65KT4GSMGPVKD82ERMD8S', 'https://www.youtube.com/embed/mBB7CznvSPQ'),
(77, '01J2K4NZ1RQWT51W54N9DC3V7T', 'https://www.youtube.com/embed/axbLC9PqzfE'),
(81, '01J3TCTM25VBA4WP36007HTTZ0', 'https://www.youtube.com/embed/KEdFwJ4SWq4'),
(82, '01J3TCXSQAVX33R714FTP5PW0E', 'https://www.youtube.com/embed/6yA3XqjTh_w'),
(86, '01J3TCRA93ZMV49BKNK0EPHMC2', 'https://www.youtube.com/embed/wWmqtgDutls');

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
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045F3B153154` FOREIGN KEY (`tricks_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `trick`
--
ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91EF373DCF` FOREIGN KEY (`groups_id`) REFERENCES `group` (`id`);

--
-- Contraintes pour la table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `FK_7CC7DA2C3B153154` FOREIGN KEY (`tricks_id`) REFERENCES `trick` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
