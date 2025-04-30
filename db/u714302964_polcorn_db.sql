-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 30 avr. 2025 à 17:36
-- Version du serveur : 10.11.10-MariaDB
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u714302964_polcorn_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `game_scope` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `threshold_type` enum('count','boolean') NOT NULL DEFAULT 'boolean',
  `threshold_value` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achievements`
--

INSERT INTO `achievements` (`id`, `code`, `game_scope`, `name`, `description`, `icon`, `threshold_type`, `threshold_value`, `created_at`) VALUES
(1, 'games_played', 'devine_affiche', '100 parties jouées', 'Jouez 100 parties', '🎯', 'count', 100, '2025-04-29 00:22:02'),
(2, 'fast_answer', 'devine_affiche', 'Réponse en - de 3s', 'Trouvez un film en moins de 3 secondes', '⚡', 'boolean', NULL, '2025-04-29 00:22:02'),
(3, 'perfect_score', 'devine_affiche', '10/10', 'Obtenez un score parfait 10/10', '🏆', 'boolean', NULL, '2025-04-29 00:22:02'),
(4, '500_points', 'devine_affiche', '500+ points', 'Faites plus de 500 points', '💰', 'boolean', NULL, '2025-04-29 00:22:02'),
(5, 'top1', 'devine_affiche', 'Top #1', 'Terminez 1er du classement', '🥇', 'boolean', NULL, '2025-04-29 00:22:02'),
(41, 'games_played', 'devine_emoji', '100 parties jouées', 'Jouez 100 parties', '🎯', 'count', 100, '2025-04-29 02:39:35'),
(42, 'fast_answer', 'devine_emoji', 'Réponse éclair', 'Trouvez un film en <3 s', '⚡', 'boolean', NULL, '2025-04-29 02:39:35'),
(43, 'perfect_score', 'devine_emoji', 'Score parfait', 'Obtenez un 10/10', '🏆', 'boolean', NULL, '2025-04-29 02:39:35'),
(44, '500_points', 'devine_emoji', '500+ points', 'Cumulez >= 500 points', '💰', 'boolean', NULL, '2025-04-29 02:39:35'),
(45, 'top1', 'devine_emoji', 'Top #1', 'Finissez 1er au classement', '🥇', 'boolean', NULL, '2025-04-29 02:39:35');

-- --------------------------------------------------------

--
-- Structure de la table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game` enum('devine_affiche','devine_emoji','devine_infini') NOT NULL,
  `score` int(11) NOT NULL,
  `played_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggestions`
--

CREATE TABLE `suggestions` (
  `id` int(11) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `before` varchar(255) DEFAULT NULL,
  `after` varchar(255) DEFAULT NULL,
  `emojis` text DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `aliases` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `google_id`, `email`, `password_hash`, `name`, `pseudo`, `created_at`, `picture`) VALUES
(1, NULL, 'reda.regam@gmail.com', NULL, 'Réda', 'redacraft46officiel', '2025-04-28 23:10:48', 'https://lh3.googleusercontent.com/a/ACg8ocI5j2qfW6bSz0NETUpgU5vupKzFnJ4JD8N-P9EQXteBorfIsA=s96-c'),
(2, NULL, 'aderabi.web@gmail.com', NULL, 'Réda Regam', 'test', '2025-04-28 23:25:18', 'https://lh3.googleusercontent.com/a/ACg8ocI6Go-1qTQDBMEs3y-jtZf7UVav-ekcLfU4R-K1xGx35vRYPg=s96-c'),
(3, NULL, 'latelierducadre31@gmail.com', '$2y$10$9X9pP7j9xYFbw6Z.AokmB.xbUUZv51Lnk5MZjr62PkVZPqnVuld3C', '', 'redashlag46', '2025-04-28 23:43:44', NULL),
(4, NULL, 'akatsmovie@gmail.com', NULL, 'Réda Regam', 'test2', '2025-04-29 02:41:08', 'https://lh3.googleusercontent.com/a/ACg8ocITruEjl7Jd9n_PQPLA3DWgMaSvffRx4Xoe9R_ZNMU3vbcBqA=s96-c'),
(5, NULL, 'test@test.fr', '$2y$10$S94bl6ufCeMkKa8D.LnuPudDHzvjhDwtznOdxCMk4lxEUhHuoLD62', '', '', '2025-04-29 02:50:20', NULL),
(6, NULL, 'redacraft46@gmail.com', '$2y$10$pY/sEGYkSRtBUSRitN6AlOnwlyjmwdHVPZnJ5dOWm4ljMd4zHQO/2', '', 'redaa', '2025-04-29 04:50:34', NULL),
(7, NULL, 'redaecompro@gmail.com', NULL, 'Reda', 'NathanRappet46', '2025-04-29 16:15:11', 'https://lh3.googleusercontent.com/a/ACg8ocJZcxsrrITiXHlxeLuNF3-rS1PGW7LhOOGz0lKxSXdkO9UC=s96-c'),
(8, NULL, 'paul19.pds@gmail.com', NULL, 'Paul Da Silva', 'CaptnCook', '2025-04-29 21:20:00', 'https://lh3.googleusercontent.com/a/ACg8ocI7nkxRhuT3DCsRRrL8vP6FPn_SZXRSfuW2Nuerh6MWTd-qoqhN=s96-c');

-- --------------------------------------------------------

--
-- Structure de la table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `unlocked_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_achievements`
--

INSERT INTO `user_achievements` (`user_id`, `achievement_id`, `unlocked_at`) VALUES
(1, 2, '2025-04-29 19:04:47'),
(1, 4, '2025-04-29 04:56:56'),
(1, 42, '2025-04-29 04:58:42'),
(1, 43, '2025-04-29 14:58:58'),
(1, 44, '2025-04-29 14:58:58'),
(1, 45, '2025-04-29 22:21:14'),
(2, 2, '2025-04-29 02:01:49'),
(2, 42, '2025-04-30 16:12:11'),
(3, 2, '2025-04-29 01:29:28'),
(3, 42, '2025-04-29 15:08:19'),
(3, 44, '2025-04-29 15:08:19'),
(4, 2, '2025-04-29 02:41:37'),
(4, 42, '2025-04-29 02:42:03'),
(4, 43, '2025-04-29 03:35:37'),
(4, 44, '2025-04-29 03:34:45'),
(5, 2, '2025-04-29 02:50:47'),
(7, 42, '2025-04-29 16:18:42'),
(8, 42, '2025-04-29 21:21:32');

-- --------------------------------------------------------

--
-- Structure de la table `user_game_plays`
--

CREATE TABLE `user_game_plays` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_scope` varchar(50) NOT NULL,
  `played_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_game_sessions`
--

CREATE TABLE `user_game_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_scope` varchar(50) NOT NULL COMMENT 'devine_affiche / devine_emoji',
  `score` int(11) NOT NULL,
  `correct` int(11) NOT NULL,
  `duration_ms` int(11) NOT NULL COMMENT 'durée totale partie en ms',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_game_sessions`
--

INSERT INTO `user_game_sessions` (`id`, `user_id`, `game_scope`, `score`, `correct`, `duration_ms`, `created_at`) VALUES
(1, 3, 'devine_affiche', 76, 1, 3264, '2025-04-29 01:27:01'),
(2, 3, 'devine_affiche', 283, 4, 2105, '2025-04-29 01:29:28'),
(3, 3, 'devine_affiche', 74, 1, 339, '2025-04-29 01:31:31'),
(4, 3, 'devine_affiche', 78, 1, 217, '2025-04-29 01:37:56'),
(5, 3, 'devine_affiche', 0, 0, 640, '2025-04-29 01:38:11'),
(6, 3, 'devine_affiche', 0, 0, 387, '2025-04-29 01:39:08'),
(7, 3, 'devine_affiche', 73, 1, 689, '2025-04-29 01:41:11'),
(8, 3, 'devine_affiche', 75, 1, 449, '2025-04-29 01:50:28'),
(9, 3, 'devine_emoji', 0, 0, 1804, '2025-04-29 02:00:04'),
(10, 3, 'devine_emoji', 85, 1, 1539, '2025-04-29 02:00:15'),
(11, 3, 'devine_affiche', 82, 1, 518, '2025-04-29 02:00:38'),
(12, 2, 'devine_affiche', 84, 1, 549, '2025-04-29 02:01:49'),
(13, 2, 'devine_emoji', 87, 1, 1351, '2025-04-29 02:02:21'),
(14, 4, 'devine_affiche', 73, 1, 23, '2025-04-29 02:41:37'),
(15, 4, 'devine_emoji', 89, 1, 1185, '2025-04-29 02:42:03'),
(16, 4, 'devine_affiche', 339, 5, 4741, '2025-04-29 02:49:03'),
(17, 5, 'devine_affiche', 88, 1, 483, '2025-04-29 02:50:47'),
(18, 4, 'devine_emoji', 0, 0, 229, '2025-04-29 03:11:39'),
(19, 4, 'devine_emoji', 566, 9, 1681, '2025-04-29 03:34:45'),
(20, 4, 'devine_emoji', 686, 10, 1344, '2025-04-29 03:35:37'),
(21, 4, 'devine_affiche', 686, 0, 0, '2025-04-29 03:35:37'),
(22, 4, 'devine_emoji', 44, 1, 519, '2025-04-29 03:48:26'),
(23, 4, 'devine_emoji', 53, 1, 191, '2025-04-29 03:56:25'),
(24, 1, 'devine_affiche', 552, 9, 4254, '2025-04-29 04:56:56'),
(25, 1, 'devine_emoji', 433, 6, 1666, '2025-04-29 04:58:42'),
(26, 1, 'devine_emoji', 665, 10, 3038, '2025-04-29 14:58:58'),
(27, 1, 'devine_affiche', 665, 10, 3039, '2025-04-29 14:58:58'),
(28, 1, 'devine_affiche', 724, 10, 2789, '2025-04-29 15:02:47'),
(29, 1, 'devine_emoji', 724, 10, 2788, '2025-04-29 15:02:47'),
(30, 3, 'devine_emoji', 668, 9, 1934, '2025-04-29 15:08:19'),
(31, 1, 'devine_emoji', 0, 0, 10007, '2025-04-29 15:53:34'),
(32, 7, 'devine_emoji', 88, 2, 289, '2025-04-29 16:18:42'),
(33, 1, 'devine_affiche', 0, 0, 142, '2025-04-29 19:04:47'),
(34, 1, 'devine_affiche', 234, 6, 8529, '2025-04-29 19:17:04'),
(35, 1, 'devine_affiche', 330, 5, 4215, '2025-04-29 19:22:24'),
(36, 1, 'devine_affiche', 306, 5, 5107, '2025-04-29 19:30:22'),
(37, 1, 'devine_affiche', 184, 3, 200, '2025-04-29 19:41:36'),
(38, 1, 'devine_affiche', 45, 1, 203, '2025-04-29 19:42:03'),
(39, 1, 'devine_affiche', 150, 2, 331, '2025-04-29 19:54:26'),
(40, 1, 'devine_affiche', 246, 5, 464, '2025-04-29 20:05:00'),
(41, 1, 'devine_affiche', 81, 1, 116, '2025-04-29 20:05:19'),
(42, 1, 'devine_affiche', 299, 5, 4251, '2025-04-29 20:14:19'),
(43, 1, 'devine_affiche', 0, 0, 869, '2025-04-29 20:15:17'),
(44, 1, 'devine_affiche', 130, 2, 123, '2025-04-29 20:16:12'),
(45, 1, 'devine_affiche', 128, 2, 675, '2025-04-29 20:32:01'),
(46, 1, 'devine_affiche', 120, 2, 118, '2025-04-29 20:32:27'),
(47, 1, 'devine_affiche', 58, 1, 655, '2025-04-29 21:06:27'),
(48, 1, 'devine_affiche', 89, 2, 391, '2025-04-29 21:10:39'),
(49, 1, 'devine_affiche', 62, 1, 414, '2025-04-29 21:11:43'),
(50, 8, 'devine_emoji', 410, 7, 2609, '2025-04-29 21:21:32'),
(51, 8, 'devine_affiche', 269, 5, 3097, '2025-04-29 21:25:10'),
(52, 8, 'devine_emoji', 419, 7, 3702, '2025-04-29 21:37:17'),
(53, 1, 'devine_emoji', 531, 8, 2438, '2025-04-29 21:55:35'),
(54, 8, 'devine_affiche', 351, 8, 5750, '2025-04-29 21:59:53'),
(55, 1, 'devine_affiche', 64, 2, 1482, '2025-04-29 22:04:08'),
(56, 1, 'devine_emoji', 0, 0, 159, '2025-04-29 22:19:40'),
(57, 1, 'devine_emoji', 655, 10, 1431, '2025-04-29 22:20:37'),
(58, 1, 'devine_emoji', 736, 10, 2972, '2025-04-29 22:21:14'),
(59, 1, 'devine_affiche', 106, 2, 142, '2025-04-29 22:22:00'),
(60, 1, 'devine_emoji', 0, 0, 423, '2025-04-29 22:23:20'),
(61, 1, 'devine_emoji', 0, 0, 273, '2025-04-29 23:01:16'),
(62, 1, 'devine_infini', 341, 4, 70000, '2025-04-30 03:21:42'),
(63, 1, 'devine_infini', 90, 1, 40000, '2025-04-30 04:01:09'),
(64, 1, 'devine_emoji', 52, 1, 363, '2025-04-30 04:11:59'),
(65, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 04:33:39'),
(66, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 04:34:13'),
(67, 1, 'devine_infini', 68, 1, 40000, '2025-04-30 04:34:47'),
(68, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 04:36:52'),
(69, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 04:38:52'),
(70, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 04:49:52'),
(71, 1, 'devine_affiche', 0, 0, 60002, '2025-04-30 04:59:52'),
(72, 1, 'devine_infini', 393, 4, 70000, '2025-04-30 05:06:13'),
(73, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 05:13:59'),
(74, 1, 'devine_infini', 75, 1, 40000, '2025-04-30 05:14:18'),
(75, 1, 'devine_infini', 348, 4, 70000, '2025-04-30 05:15:16'),
(76, 1, 'devine_infini', 786, 8, 110000, '2025-04-30 05:18:05'),
(77, 1, 'devine_infini', 632, 7, 100000, '2025-04-30 05:23:02'),
(78, 1, 'devine_infini', 564, 6, 90000, '2025-04-30 05:27:14'),
(79, 1, 'devine_emoji', 141, 2, 468, '2025-04-30 05:28:51'),
(80, 1, 'devine_affiche', 62, 1, 471, '2025-04-30 05:30:30'),
(81, 1, 'devine_infini', 81, 1, 40000, '2025-04-30 05:31:34'),
(82, 1, 'devine_infini', 254, 3, 60000, '2025-04-30 05:34:12'),
(83, 7, 'devine_infini', 969, 11, 140000, '2025-04-30 05:42:20'),
(84, 7, 'devine_infini', 618, 7, 100000, '2025-04-30 05:44:03'),
(85, 1, 'devine_infini', 402, 6, 90000, '2025-04-30 06:00:57'),
(86, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 14:19:16'),
(87, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 15:28:18'),
(88, 1, 'devine_infini', 152, 2, 50000, '2025-04-30 15:33:12'),
(89, 1, 'devine_infini', 0, 0, 30000, '2025-04-30 15:34:06'),
(90, 1, 'devine_infini', 845, 10, 130000, '2025-04-30 16:04:04'),
(91, 2, 'devine_emoji', 0, 0, 217, '2025-04-30 16:12:11'),
(92, 2, 'devine_emoji', 0, 0, 206, '2025-04-30 16:12:35');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_scope_code` (`game_scope`,`code`);

--
-- Index pour la table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Index pour la table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`user_id`,`achievement_id`),
  ADD KEY `achievement_id` (`achievement_id`);

--
-- Index pour la table `user_game_plays`
--
ALTER TABLE `user_game_plays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`game_scope`);

--
-- Index pour la table `user_game_sessions`
--
ALTER TABLE `user_game_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_scope` (`game_scope`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `user_game_plays`
--
ALTER TABLE `user_game_plays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_game_sessions`
--
ALTER TABLE `user_game_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
