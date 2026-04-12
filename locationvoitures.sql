-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 11 avr. 2026 à 15:44
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `locationvoitures`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `idadmin` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(70) NOT NULL,
  `motdepasse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`idadmin`, `nom`, `email`, `motdepasse`) VALUES
(1, 'admin', 'douaasalah262@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `note` int(11) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `nom`, `ville`, `commentaire`, `note`, `date_creation`) VALUES
(5, 'sakly imen', 'Monastir', 'Je conseille fortement ! Rien à dire ! 🔥🔥', 5, '2026-04-09 10:42:55'),
(6, 'miral trabelsi', 'Sousse', 'Service excellent, la voiture était prête à l\'heure, la communication et les informations fournies étaient excellentes, et la voiture était propre et bien entretenue.\r\n\r\nJe recommande vivement cette agence', 5, '2026-04-09 10:44:51'),
(7, 'Mohamed hlel', 'Nabeul', 'Très bonne expérience avec eux. Nous avons obtenu un bon prix pour une voiture en excellent état, sans aucun problème. La voiture était propre et tout a été pris en charge sur place ; ils l\'ont même livrée à l\'hôtel.\r\n\r\nDe plus, ils ont été très réactifs par e-mail pendant notre voyage, ce qui nous a vraiment rassurés et nous a permis de voyager comme prévu. Cinq étoiles sans hésiter !', 5, '2026-04-09 10:47:39'),
(8, 'Ahmed boussaid', 'Tunis', 'Service excellent, qui mérite toute notre reconnaissance et notre respect. Bonne continuation.\r\n', 4, '2026-04-09 12:42:27'),
(9, 'Salah', 'monastir', 'çok guzel', 4, '2026-04-09 19:24:24'),
(10, 'Salah', 'monastir', 'çok guzel', 5, '2026-04-09 19:24:34');

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `message`, `is_read`, `created_at`) VALUES
(1, 'salah', 'douaasalah262@gmail.com', '+21693062728', 'first try it\'s nice!', 0, '2026-04-09 00:45:50'),
(2, 'salah', 'douaasalah262@gmail.com', '+21693062728', 'hu', 0, '2026-04-09 16:08:13'),
(3, 'salah', 'douaasalah262@gmail.com', '+21693062728', 'çok guzel', 0, '2026-04-09 16:18:51');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_voiture` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id`, `id_client`, `id_voiture`, `created_at`) VALUES
(41, 1, 2, '2026-04-09 11:14:21'),
(46, 1, 1, '2026-04-09 11:45:13'),
(50, 2, 3, '2026-04-10 12:22:11');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `prix` decimal(5,2) NOT NULL,
  `est_inclus` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `nom`, `description`, `prix`, `est_inclus`) VALUES
(1, 'Bluetooth', '', 0.00, 0),
(2, 'Climatisation', '', 0.00, 0),
(3, 'Radio / Audio', '', 0.00, 0),
(4, 'Fermeture centralisée', '', 0.00, 0),
(5, 'Clé USB', '', 0.00, 0),
(6, 'Caméra de recul', '', 0.00, 0),
(7, 'GPS', '', 0.00, 0),
(8, 'Siège bébé', '', 3.00, 1),
(9, 'Réservoir plein', '', 140.00, 1),
(10, 'Protection insurance', 'Protection contre le vol\nBris de glace, phares et pneumatiques\nProtection personnelle accident (conducteur et passagers)', 15.00, 1),
(11, 'Full insurance', 'Assurance corporelle (conducteur et passagers)\nAssurance responsabilité civile\nProtection contre l’incendie et catastrophes naturelles\nProtection contre les dommages résultant d’une collision (conducteur non fautif)\nLimitation responsabilité locataire en cas de dommages au véhicule : 1500 TND\nProtec', 30.00, 1),
(12, 'No insurance', 'Le client ne souhaite pas d’assurance et assume tous les risques', 0.00, 1),
(13, 'Private Driver', '', 50.00, 1),
(14, 'Unlimited Wi-Fi 4G', '', 10.00, 1);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(13, 'saklyimen24@gmail.com', '11db473201afe3ad5e66a464ee272e25396de1794265348db45f4449f4e8ad9c', '2026-03-30 09:26:15');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id_reservation` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_voiture` int(11) NOT NULL,
  `date_reservation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` varchar(20) DEFAULT 'En attente',
  `total` decimal(10,2) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `datenaiss` date DEFAULT NULL,
  `civility` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id_reservation`, `id_client`, `id_voiture`, `date_reservation`, `date_debut`, `date_fin`, `statut`, `total`, `telephone`, `adresse`, `datenaiss`, `civility`) VALUES
(1, 1, 1, '2026-04-09 20:41:45', '2026-04-09', '2026-04-10', 'En attente', NULL, NULL, NULL, NULL, NULL),
(18, 14, 7, '2026-04-10 21:08:59', '2026-04-11', '2026-04-14', 'En attente', 270.00, NULL, NULL, NULL, NULL),
(22, 17, 3, '2026-04-10 23:58:13', '2026-04-12', '2026-04-15', 'En attente', 450.00, '93223079', 'lamtaa', '1999-12-05', 'monsieur'),
(23, 17, 14, '2026-04-10 23:59:28', '2026-04-12', '2026-04-15', 'En attente', 2550.00, '93223079', 'lamtaa', '1999-12-05', 'monsieur'),
(24, 17, 1, '2026-04-11 00:10:05', '2026-04-12', '2026-04-15', 'En attente', 240.00, '93223079', 'lamtaa', '1999-12-05', 'monsieur'),
(25, 18, 4, '2026-04-11 00:21:01', '2026-04-12', '2026-04-15', 'En attente', 570.00, '+21693062728', 'sayada', '2009-10-16', 'monsieur'),
(26, 18, 2, '2026-04-11 00:57:54', '2026-04-12', '2026-04-15', 'En attente', 315.00, '+21693062728', 'sayada', '2009-10-16', 'madame'),
(27, 18, 2, '2026-04-11 01:03:19', '2026-04-12', '2026-04-15', 'En attente', 315.00, '+21693062728', 'sayada', '2009-10-16', 'madame'),
(28, 18, 5, '2026-04-11 09:02:55', '2026-04-12', '2026-04-15', 'En attente', 525.00, '+21693062728', 'sayada', '2000-10-16', 'monsieur');

-- --------------------------------------------------------

--
-- Structure de la table `reservation_options`
--

CREATE TABLE `reservation_options` (
  `idreservation` int(11) NOT NULL,
  `idoption` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation_options`
--

INSERT INTO `reservation_options` (`idreservation`, `idoption`) VALUES
(18, 12),
(22, 5),
(22, 11),
(23, 4),
(23, 12),
(24, 12),
(25, 4),
(25, 11),
(28, 1),
(28, 2),
(28, 4),
(28, 5),
(28, 10);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `idclient` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `motdepasse` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`idclient`, `email`, `nom`, `motdepasse`, `created_at`) VALUES
(1, 'saklyimen24@gmail.com', 'sakly imen', '$2y$10$qtdEOAmidx5rwW0h5und7eQKt5Iqohu7.ZIdqjM.Ay55pQ4zODic6', '2026-04-08 14:47:11'),
(14, 'douaasalah286@gmail.com', 'Salah', '$2y$10$if50wkPnYkYuggPh2ldDa.YIXMe/rfQaH0iI5KLH78iV65odtBdDu', '2026-04-10 21:08:59'),
(17, 'douaasalah262@gmail.com', 'abdesatar', '$2y$10$a.OREfpiFMX7H4doIalxbuf9nSAgN0Fiy4NcU87/7u59ROFp7JzDW', '2026-04-10 23:55:28'),
(18, 'douaasalah005@gmail.com', 'dhaker', '$2y$10$6/bz0aAO89tgSEk6Uz9feetmFp30qqepFySTW4YCSyDpEaXb0mnFa', '2026-04-11 00:19:32');

-- --------------------------------------------------------

--
-- Structure de la table `voitures`
--

CREATE TABLE `voitures` (
  `id` int(11) NOT NULL,
  `marque` varchar(20) NOT NULL,
  `modele` varchar(20) NOT NULL,
  `annee` int(11) NOT NULL,
  `imgfront` varchar(255) NOT NULL,
  `imginter` varchar(255) NOT NULL,
  `imgcote` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `boite` varchar(20) NOT NULL,
  `carburant` varchar(20) NOT NULL,
  `places` int(11) NOT NULL,
  `bagages` int(11) NOT NULL,
  `prix` decimal(6,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voitures`
--

INSERT INTO `voitures` (`id`, `marque`, `modele`, `annee`, `imgfront`, `imginter`, `imgcote`, `type`, `boite`, `carburant`, `places`, `bagages`, `prix`) VALUES
(1, 'Kia', 'Picanto', 2023, 'imgVoitures\\Kia_Picanto_2023\\front.png', 'imgVoitures/Kia_Picanto_2023/interieur.png', 'imgVoitures/Kia_Picanto_2023/cote.png', 'Citadine', 'Automatic', 'Essence', 5, 4, 80),
(2, 'Kia', 'Rio', 2020, 'imgVoitures\\Kia_Rio_2020\\front.png', 'imgVoitures/Kia_Rio_2020/interieur.png', 'imgVoitures/Kia_Rio_2020/cote.png', 'Berline', 'Automatic', 'Essence', 5, 4, 90),
(3, 'Peugeot', '208', 2024, 'imgVoitures\\Peugeot_208_2024\\front.avif', 'imgVoitures/Peugeot_208_2024/interieur.jpg', 'imgVoitures/Peugeot_208_2024/coté.png', 'Citadine', 'Automatic', 'Essence', 5, 2, 120),
(4, 'Peugeot', '2008', 2020, 'imgVoitures\\Peugeot_2008_2020\\front.webp', 'imgVoitures/Peugeot_2008_2020/interieur.webp', 'imgVoitures/Peugeot_2008_2020/cote.webp', 'Citadine', 'Automatic', 'Essence', 5, 2, 160),
(5, 'Seat', 'Ateca', 2020, 'imgVoitures\\Seat_Ateca_2020\\front.png', 'imgVoitures/Seat_Ateca_2020/interieur.jpg', 'imgVoitures/Seat_Ateca_2020/cote.png', 'SUV', 'Automatic', 'Diesel', 5, 4, 160),
(6, 'Seat', 'Ibiza', 2024, 'imgVoitures\\Seat_Ibiza_2024\\front.png', 'imgVoitures/Seat_Ibiza_2024/interieur.png', 'imgVoitures/Seat_Ibiza_2024/cote.png', 'Citadine', 'Manual', 'Essence', 5, 2, 80),
(7, 'Renault', 'Clio 5', 2022, 'imgVoitures\\Renault_Clio_5\\Renault_Clio_5_front.png', 'imgVoitures/Renault_Clio_5/Renault_Clio_5_inter.png', 'imgVoitures/Renault_Clio_5/Renault_Clio_5_cote.png', 'Citadine', 'Automatic', 'Essence', 5, 3, 90),
(8, 'Hyundai', 'i20', 2024, 'imgVoitures\\Hyundai_I20_BVA_2024\\img_front.png', 'imgVoitures/Hyundai_I20_BVA_2024/img_inter.png', 'imgVoitures/Hyundai_I20_BVA_2024/img_cote.png', 'Citadine', 'Manual', 'Essence', 5, 3, 120),
(9, 'Hyundai', 'Grand i10', 2024, 'imgVoitures\\Hyundai_Grand_i10\\img_front.png', 'imgVoitures/Hyundai_Grand_i10/img_inter.png', 'imgVoitures/Hyundai_Grand_i10/img_cote.png', 'Citadine', 'Automatic', 'Essence', 5, 2, 90),
(10, 'Dacia', 'Sandero Stepway', 2024, 'imgVoitures\\Dacia_Sandero_Stepway\\dacia_sandero-front.jpg', 'imgVoitures/Dacia_Sandero_Stepway/dacia_sandero-inter.png', 'imgVoitures/Dacia_Sandero_Stepway/dacia_cote.webp', 'SUV', 'Manual', 'Diesel', 5, 3, 150),
(11, 'Dacia', 'Logan', 2023, 'imgVoitures\\Dacia_Logan_2023\\dacia_logan_front.webp', 'imgVoitures/Dacia_Logan_2023/dacia_logan_int.png', 'imgVoitures/Dacia_Logan_2023/dacia_logan_cote.png', 'Citadine', 'Manual', 'Essence', 5, 3, 110),
(12, 'Renault', 'Clio 3', 2015, 'imgVoitures\\clio_3\\clio3_front.png', 'imgVoitures/clio_3/clio3_int.png', 'imgVoitures/clio_3/clio3_cote.png', 'Citadine', 'Manual', 'Essence', 5, 3, 75),
(13, 'Peugeot', 'Traveller', 2024, 'imgVoitures\\Peugeot_TRAVELLER_2024\\Peugeot_TRAVELLER_2024_front.jpg', 'imgVoitures/Peugeot_TRAVELLER_2024/Peugeot-Traveller-inter.png', 'imgVoitures/Peugeot_TRAVELLER_2024/Peugeot-Traveller-cote.jpg', 'Minivan', 'Manual', 'Diesel', 9, 6, 290),
(14, 'Land Rover', 'Range Rover', 2025, 'imgVoitures\\Range_Rover_vogue_2025\\front.png', 'imgVoitures/Range_Rover_vogue_2025/inter.png', 'imgVoitures/Range_Rover_vogue_2025/cote.png', 'Luxury', 'Automatic', 'Diesel', 5, 4, 850);

-- --------------------------------------------------------

--
-- Structure de la table `voitures_options`
--

CREATE TABLE `voitures_options` (
  `id` int(11) NOT NULL,
  `idv` int(11) NOT NULL,
  `ido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voitures_options`
--

INSERT INTO `voitures_options` (`id`, `idv`, `ido`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 2, 1),
(9, 2, 2),
(10, 2, 3),
(11, 2, 4),
(12, 2, 5),
(13, 2, 6),
(14, 2, 7),
(15, 3, 1),
(16, 3, 2),
(17, 3, 3),
(18, 3, 4),
(19, 3, 5),
(20, 3, 6),
(21, 3, 7),
(22, 4, 1),
(23, 4, 2),
(24, 4, 3),
(25, 4, 4),
(26, 4, 5),
(27, 4, 6),
(28, 4, 7),
(29, 5, 1),
(30, 5, 2),
(31, 5, 3),
(32, 5, 4),
(33, 5, 5),
(34, 6, 1),
(35, 6, 2),
(36, 6, 3),
(37, 6, 4),
(38, 6, 5),
(39, 6, 7),
(40, 7, 1),
(41, 7, 2),
(42, 7, 3),
(43, 7, 4),
(44, 7, 5),
(45, 7, 6),
(46, 7, 7),
(47, 8, 1),
(48, 8, 2),
(49, 8, 3),
(50, 8, 4),
(51, 8, 5),
(52, 8, 6),
(53, 8, 7),
(54, 9, 1),
(55, 9, 2),
(56, 9, 3),
(57, 9, 4),
(58, 9, 5),
(59, 9, 7),
(60, 10, 1),
(61, 10, 2),
(62, 10, 3),
(63, 10, 4),
(64, 10, 5),
(65, 11, 1),
(66, 11, 2),
(67, 11, 3),
(68, 11, 4),
(69, 11, 5),
(70, 11, 7),
(71, 12, 1),
(72, 12, 2),
(73, 12, 3),
(74, 12, 4),
(75, 12, 5),
(76, 13, 1),
(77, 13, 2),
(78, 13, 3),
(79, 13, 4),
(80, 13, 5),
(81, 13, 6),
(82, 13, 7),
(83, 14, 1),
(84, 14, 2),
(85, 14, 3),
(86, 14, 4),
(87, 14, 5),
(88, 14, 6),
(89, 14, 7);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idadmin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favori` (`id_client`,`id_voiture`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `reservation_ibfk_1` (`id_client`),
  ADD KEY `reservation_ibfk_2` (`id_voiture`);

--
-- Index pour la table `reservation_options`
--
ALTER TABLE `reservation_options`
  ADD PRIMARY KEY (`idreservation`,`idoption`),
  ADD KEY `idoption` (`idoption`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idclient`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `voitures_options`
--
ALTER TABLE `voitures_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idv` (`idv`),
  ADD KEY `ido` (`ido`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `idadmin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `idclient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `voitures`
--
ALTER TABLE `voitures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `voitures_options`
--
ALTER TABLE `voitures_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `users` (`idclient`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`id_voiture`) REFERENCES `voitures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation_options`
--
ALTER TABLE `reservation_options`
  ADD CONSTRAINT `reservation_options_ibfk_1` FOREIGN KEY (`idreservation`) REFERENCES `reservation` (`id_reservation`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_options_ibfk_2` FOREIGN KEY (`idoption`) REFERENCES `options` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `voitures_options`
--
ALTER TABLE `voitures_options`
  ADD CONSTRAINT `voitures_options_ibfk_1` FOREIGN KEY (`idv`) REFERENCES `voitures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `voitures_options_ibfk_2` FOREIGN KEY (`ido`) REFERENCES `options` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
