-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 17 déc. 2020 à 10:48
-- Version du serveur :  5.7.24
-- Version de PHP : 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bilemo`
--

-- --------------------------------------------------------

--
-- Structure de la table `doc_section`
--

DROP TABLE IF EXISTS `doc_section`;
CREATE TABLE IF NOT EXISTS `doc_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `date_add` datetime NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `request_method` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `doc_section`
--

INSERT INTO `doc_section` (`id`, `title`, `content`, `date_add`, `date_update`, `request_method`, `request_url`, `content_type`) VALUES
(2, 'Récupérez votre token d\'accès', 'Utilisez les identifiants clients par BileMo lors de la création de votre compte\r\nInclure dans le body, au format JSON:\r\n\r\n`{\"username\":\"votre_adresse@email.fr\",\"password\":\"votre_mot_de_passe\"}` \r\n\r\n*Demo :* `{\"username\":\"client@demo.fr\",\"password\":\"demodemo\"}`\r\n\r\nVous recevrez votre token a inclure avec la clé *\"Authorization\"* dans le header de chacune de vos requètes, avec le prefix \"Bearer\". \r\n\r\n**Exemple:**\r\n```\r\nBearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDc1OTYwMDgsImV4cCI6MTYwNzk1NjAwOCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoiY2xpZW50QGRlbW8uZnIifQ.vPL24JUSLJmMLZXq7Q08FQy_l5YVK1l9oMfcXqib__GuFegkU2OGsVuK4NLv4ZbZG_2jD3lKGBcTY2s-kpbiQhuWBYzjKGJ3RqCTKxqMg5iazt46LxldiN1XXzuk6dOwuSYXa09aOhiof5-lmSWMFXlU-mAagdiJlf2tV7Cw6YrqcLg6qb6ZsRjExwfdWScsOdLzBjEiuqSudWJK2bJkslP464EJl7oGNtk5Fo4SehiJ2VobKZtjRez02GoiUMzRLLxrVup1lqFacaYtEvPvPQmrAfMu8qLMzscML1CfdZSwMbPvj6OQoOxWkUO_is48iWNbNkoYGYl_mjMNn0Fj9OJuS-an_xpCnxKyLxJczQHbBEkIcaUe1SQqoJebsjctbDz3RunkxROe6fiwO8wBXpvoxKTp2_DfkXbKxNOxqtOev6kEYi1J3pdbt2p9FQfOINX4kB0W16SQhHJyUdLH1P3krSvlZcakXDcrq1BjxbpBaVKR35bq-Yau41DAZx_-2pnNeWrgdcy92vnBxyV1RX3bmDbExrRTWJBC3cGBBQLD2ATDkdd4IpaAtSqMJhGj9v4KvHqgE9ukVoqv7JvYbZbcjm_waoEuOUIL7I6rBgahmOvJKyPadeRT_dfw_AvZNqwW0SToRHo7gsY4DPWIW2frOPeI0HyBLcY2z6BsV1E\r\n```', '2020-12-17 09:55:17', '2020-12-17 10:44:05', 'POST', '/api/login_check', 'application/json'),
(3, 'Récupérer la liste des produits', '#### Pagination et filtre par marque:\r\nPar défaut, La page 1 avec 12 objets est renvoyée.\r\nAjouter le paramètre *page* pour changer de page, et *limit* pour modifier le nombre d\'objets par page.\r\nPour filtrer par marque, ajouter le paramètre \"brand\" avec l\'ID de la marque souhaitée.\r\n\r\n**Exemple:**\r\n/api/products?brand=8&page=3&limit=20', '2020-12-17 10:09:07', '2020-12-17 10:24:38', 'GET', '/api/products', 'application/json'),
(4, 'Récupérer la liste des marques', '#### Pagination\r\nPar défaut, La page 1 avec 12 objets est renvoyée.\r\nAjouter le paramètre *page* pour changer de page, et *limit* pour modifier le nombre d\'objects par page.\r\n\r\n**Exemple:**\r\n/api/brands?page=3&limit=5', '2020-12-17 10:20:19', '2020-12-17 10:24:05', 'GET', '/api/brands', 'application/json'),
(5, 'Récupérez la liste de vos utilisateurs', '#### Pagination\r\nPar défaut, La page 1 avec 12 objets est renvoyée.\r\nAjouter le paramètre *page* pour changer de page, et *limit* pour modifier le nombre d\'objects par page.\r\n\r\n**Exemple:**\r\n/api/users?page=3&limit=5', '2020-12-17 10:24:02', NULL, 'GET', '/api/users', 'application/json'),
(6, 'Récupérer les détails d\'un produit', '{id} = id du produit', '2020-12-17 10:27:04', '2020-12-17 10:29:25', 'GET', '/api/products/{id}', 'application/json'),
(7, 'Récupérer les détails d\'une marque', '{id} = id de la marque', '2020-12-17 10:28:35', NULL, 'GET', '/api/brands/{id}', 'application/json'),
(8, 'Récupérer les détails d\'un utilisateur', '{id} = id de l\'utilisateur', '2020-12-17 10:29:19', NULL, 'GET', '/api/users/{id}', 'application/json'),
(9, 'Ajouter un utilisateur', 'Il vous faudra envoyer les données du nouvel utilisateur au format JSON, comme ceci:  \r\n```{\r\n    \"firstname\": \"maggie\",\r\n    \"lastname\": \"leveque\",\r\n    \"email\": \"maggie.eveque@wanadoo.fr\"\r\n}```', '2020-12-17 10:31:38', '2020-12-17 10:33:02', 'POST', '/api/users', 'application/json'),
(10, 'Modifier un utilisateur', '{id} = id de l\'utilisateur à modifier\r\n\r\nIl vous faudra envoyer les données à modifier au format JSON, comme ceci:  \r\n```{\r\n    \"firstname\": \"magie\",\r\n    \"email\": \"magie.eveque@aol.fr\"\r\n}```', '2020-12-17 10:34:56', NULL, 'PATCH', '/api/users/{id}', 'application/json');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
