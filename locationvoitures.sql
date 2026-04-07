-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 06 avr. 2026 à 22:42
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
(8, 'Siège bébé', '', 9.00, 1),
(9, 'Réservoir plein', '', 140.00, 1),
(10, 'Assurance protection plus', 'Protection contre le vol\nBris de glace, phares et pneumatiques\nProtection personnelle accident (conducteur et passagers)', 15.00, 1),
(11, 'Assurance tous risques', 'Assurance corporelle (conducteur et passagers)\nAssurance responsabilité civile\nProtection contre l’incendie et catastrophes naturelles\nProtection contre les dommages résultant d’une collision (conducteur non fautif)\nLimitation responsabilité locataire en cas de dommages au véhicule : 1500 TND\nProtec', 30.00, 1),
(12, 'Pas d’assurance', 'Le client ne souhaite pas d’assurance et assume tous les risques', 0.00, 1);

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
(13, 'saklyimen24@gmail.com', '11db473201afe3ad5e66a464ee272e25396de1794265348db45f4449f4e8ad9c', '2026-03-30 09:26:15'),
(14, 'yosratiss1@gmail.com', '0658bcc4ded3d13455e08946c15d3bf4d6998d08667af6ab6333f64dea9ae670', '2026-04-06 14:24:25');

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
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id_reservation`, `id_client`, `id_voiture`, `date_reservation`, `date_debut`, `date_fin`, `statut`, `total`) VALUES
(1, 1, 1, '2026-04-06 16:46:04', '2026-04-06', '2026-04-08', 'En attente', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reservation_options`
--

CREATE TABLE `reservation_options` (
  `idreservation` int(11) NOT NULL,
  `idoption` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `idclient` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `motdepasse` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telephone` varchar(20) NOT NULL,
  `datenaiss` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`idclient`, `email`, `nom`, `motdepasse`, `created_at`, `telephone`, `datenaiss`) VALUES
(1, 'yosratiss1@gmail.com', 'Yosra Tiss', '$2y$10$dd37fXZnHJW4xJ6HDBR9U.mDTG7cRCwNKyLTD29q13Q2lzwK8IvFe', '2026-03-31 10:18:39', '', '0000-00-00');

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
(2, 'Kia', 'Rio', 2020, 'imgVoitures\\Kia_Rio_2020\\front.png', 'imgVoitures/Kia_Rio_2020/interieur.png', 'imgVoitures/Kia_Rio_2020/cote.png', 'Berline', 'Automatic', 'Essence', 5, 4, 70),
(3, 'Peugeot', '208', 2024, 'imgVoitures\\Peugeot_208_2024\\front.avif', 'imgVoitures/Peugeot_208_2024/interieur.jpg', 'imgVoitures/Peugeot_208_2024/coté.png', 'Citadine', 'Automatic', 'Essence', 5, 2, 120),
(4, 'Peugeot', '2008', 2020, 'imgVoitures\\Peugeot_2008_2020\\front.webp', 'imgVoitures/Peugeot_2008_2020/interieur.webp', 'imgVoitures/Peugeot_2008_2020/cote.webp', 'Citadine', 'Automatic', 'Essence', 5, 2, 160),
(5, 'Seat', 'Ateca', 2020, 'imgVoitures\\Seat_Ateca_2020\\front.png', 'imgVoitures/Seat_Ateca_2020/interieur.jpg', 'imgVoitures/Seat_Ateca_2020/cote.png', 'SUV', 'Automatic', 'Diesel', 5, 4, 160),
(6, 'Seat', 'Ibiza', 2024, 'imgVoitures\\Seat_Ibiza_2024\\front.png', 'imgVoitures/Seat_Ibiza_2024/interieur.png', 'imgVoitures/Seat_Ibiza_2024/cote.png', 'Citadine', 'Manual', 'Essence', 5, 2, 80),
(7, 'Renault', 'Clio 5', 2022, 'imgVoitures\\Renault_Clio_5\\Renault_Clio_5_front.png', 'imgVoitures/Renault_Clio_5/Renault_Clio_5_inter.png', 'imgVoitures/Renault_Clio_5/Renault_Clio_5_cote.png', 'Citadine', 'Automatic', 'Essence', 5, 3, 90),
(8, 'Hyundai', 'i20', 2024, 'imgVoitures\\Hyundai_I20_BVA_2024\\img_front.png', 'imgVoitures/Hyundai_I20_BVA_2024/img_inter.png', 'imgVoitures/Hyundai_I20_BVA_2024/img_cote.png', 'Citadine', 'Manual', 'Essence', 5, 3, 120),
(9, 'Hyundai', 'Grand i10', 2024, 'imgVoitures\\Hyundai_Grand_i10\\img_front.png', 'imgVoitures/Hyundai_Grand_i10/img_inter.png', 'imgVoitures/Hyundai_Grand_i10/img_cote.png', 'Citadine', 'Automatic', 'Essence', 5, 2, 110),
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
  MODIFY `idadmin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `idclient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
