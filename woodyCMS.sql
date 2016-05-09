-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Client :  anticachgpjean.mysql.db
-- Généré le :  Mar 03 Mai 2016 à 13:12
-- Version du serveur :  5.5.46-0+deb7u1-log
-- Version de PHP :  5.4.45-0+deb7u2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `anticachgpjean`
--

-- --------------------------------------------------------

--
-- Structure de la table `archive_article-demo`
--

CREATE TABLE IF NOT EXISTS `archive_article-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `last_modificator` int(11) DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `archiver` int(11) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `@_image#file__image` varchar(511) DEFAULT NULL,
  `@_image#title__image` varchar(511) DEFAULT NULL,
  `@_image#link__image` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_string__headline-left` varchar(511) DEFAULT NULL,
  `@_text__left-column` text,
  `@_string__headline-center` varchar(511) DEFAULT NULL,
  `@_text__center-column` text,
  `@_string__headline-right` varchar(511) DEFAULT NULL,
  `@_text__right-column` text,
  `@_file#file__download` varchar(511) DEFAULT NULL,
  `@_file#text__download` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `archive_folder`
--

CREATE TABLE IF NOT EXISTS `archive_folder` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `last_modificator` int(11) DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `archiver` int(11) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `@_string__title` varchar(511) DEFAULT NULL,
  `@_string__subtitle` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `archive_folder-demo`
--

CREATE TABLE IF NOT EXISTS `archive_folder-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `last_modificator` int(11) DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `archiver` int(11) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `@_string__content-class` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_get_contents#get_article-demo__contents` tinyint(1) DEFAULT NULL,
  `@_get_contents#fk_localisation__contents` int(11) DEFAULT NULL,
  `@_get_contents#limit__contents` int(11) DEFAULT NULL,
  `@_get_contents#depth__contents` int(11) DEFAULT NULL,
  `@_get_contents#order__contents` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `archive_home-demo`
--

CREATE TABLE IF NOT EXISTS `archive_home-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `last_modificator` int(11) DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `archiver` int(11) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `@_string__meta-title` varchar(100) DEFAULT NULL,
  `@_string__meta-description` varchar(200) DEFAULT NULL,
  `@_text__meta-keywords` text,
  `@_image#file__logo` varchar(511) DEFAULT NULL,
  `@_image#title__logo` varchar(511) DEFAULT NULL,
  `@_image#link__logo` varchar(511) DEFAULT NULL,
  `@_link#href__contact-email` varchar(511) DEFAULT NULL,
  `@_link#text__contact-email` varchar(511) DEFAULT NULL,
  `@_link#external__contact-email` tinyint(1) DEFAULT '1',
  `@_string__footer-left` varchar(511) DEFAULT NULL,
  `@_string__footer-right` varchar(511) DEFAULT NULL,
  `@_file#file__download-highlight` varchar(511) DEFAULT NULL,
  `@_file#text__download-highlight` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `archive_user`
--

CREATE TABLE IF NOT EXISTS `archive_user` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `last_modificator` int(11) DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `archiver` int(11) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `@_string__last-name` varchar(511) DEFAULT NULL,
  `@_string__first-name` varchar(511) DEFAULT NULL,
  `@_connexion__connection` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `attribute_files`
--

CREATE TABLE IF NOT EXISTS `attribute_files` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) unsigned DEFAULT NULL,
  `file` varchar(511) DEFAULT NULL,
  `title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `attribute_images`
--

CREATE TABLE IF NOT EXISTS `attribute_images` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) unsigned DEFAULT NULL,
  `file` varchar(511) DEFAULT NULL,
  `title` varchar(511) DEFAULT NULL,
  `link` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `attribute_links`
--

CREATE TABLE IF NOT EXISTS `attribute_links` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) DEFAULT NULL,
  `href` varchar(511) DEFAULT NULL,
  `text` varchar(511) DEFAULT NULL,
  `external` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `content_article-demo`
--

CREATE TABLE IF NOT EXISTS `content_article-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_image#file__image` varchar(511) DEFAULT NULL,
  `@_image#title__image` varchar(511) DEFAULT NULL,
  `@_image#link__image` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_string__headline-left` varchar(511) DEFAULT NULL,
  `@_text__left-column` text,
  `@_string__headline-center` varchar(511) DEFAULT NULL,
  `@_text__center-column` text,
  `@_string__headline-right` varchar(511) DEFAULT NULL,
  `@_text__right-column` text,
  `@_file#file__download` varchar(511) DEFAULT NULL,
  `@_file#text__download` varchar(511) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `content_article-demo`
--

INSERT INTO `content_article-demo` (`id`, `name`, `context`, `creator`, `publication_date`, `modificator`, `modification_date`, `@_image#file__image`, `@_image#title__image`, `@_image#link__image`, `@_text__body`, `@_string__headline-left`, `@_text__left-column`, `@_string__headline-center`, `@_text__center-column`, `@_string__headline-right`, `@_text__right-column`, `@_file#file__download`, `@_file#text__download`) VALUES
(1, 'Article 1', '', 1, '2016-05-03 13:05:41', 1, '2016-05-03 13:05:41', '', '', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante elit, tristique vitae maximus id, gravida ornare lacus. Duis vel nunc scelerisque, fermentum dui quis, maximus metus. Quisque pellentesque dignissim rutrum. Quisque tempor nunc nunc, vitae congue ante imperdiet quis. Cras sapien dui, aliquet eget interdum et, convallis id nisi. Aenean gravida sapien at libero condimentum viverra. Nunc nec purus maximus, vulputate tortor quis, vehicula risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;', '', '', '', '', '', '', '', ''),
(2, 'Article 2', '', 1, '2016-05-03 13:06:24', 1, '2016-05-03 13:06:24', '', '', '', 'Nulla magna lectus, congue quis dapibus nec, tincidunt ut ante. Etiam ut felis iaculis, laoreet justo ut, efficitur nibh. Etiam aliquet diam ac tortor tempus, viverra suscipit quam condimentum. Quisque hendrerit mi est, id auctor dolor dignissim ac. Quisque sem nisl, rutrum a tristique vel, pulvinar id odio. Cras vel consequat enim. Aenean urna dolor, luctus eget nisl vitae, cursus pretium arcu. Nulla pharetra risus in sem tempor, non viverra dolor suscipit. Sed faucibus a arcu ut vestibulum. Praesent aliquam at mi in porta. In venenatis interdum nulla, in tincidunt turpis commodo id. Aenean vulputate eu dui non imperdiet. Curabitur ultricies efficitur pretium. Ut vitae dapibus magna.', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `content_folder`
--

CREATE TABLE IF NOT EXISTS `content_folder` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__title` varchar(511) DEFAULT NULL,
  `@_string__subtitle` varchar(511) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `content_folder`
--

INSERT INTO `content_folder` (`id`, `name`, `context`, `creator`, `publication_date`, `modificator`, `modification_date`, `@_string__title`, `@_string__subtitle`) VALUES
(1, 'Utilisateurs', '', 1, '2016-02-02 16:40:49', 1, '2016-03-19 18:40:13', 'Utilisateurs', 'RÃ©pertoire contenant les utilisateurs');

-- --------------------------------------------------------

--
-- Structure de la table `content_folder-demo`
--

CREATE TABLE IF NOT EXISTS `content_folder-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__content-class` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_get_contents#get_article-demo__contents` tinyint(1) DEFAULT NULL,
  `@_get_contents#fk_localisation__contents` int(11) DEFAULT NULL,
  `@_get_contents#limit__contents` int(11) DEFAULT NULL,
  `@_get_contents#depth__contents` int(11) DEFAULT NULL,
  `@_get_contents#order__contents` varchar(511) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `content_folder-demo`
--

INSERT INTO `content_folder-demo` (`id`, `name`, `context`, `creator`, `publication_date`, `modificator`, `modification_date`, `@_string__content-class`, `@_image#file__background-image`, `@_image#title__background-image`, `@_image#link__background-image`, `@_string__headline`, `@_text__body`, `@_get_contents#get_article-demo__contents`, `@_get_contents#fk_localisation__contents`, `@_get_contents#limit__contents`, `@_get_contents#depth__contents`, `@_get_contents#order__contents`) VALUES
(1, 'Rubrique 1', '', 1, '2016-05-03 13:00:09', 1, '2016-05-03 13:00:09', 'lecms', 'img_fond_apropos.jpg', '', '', 'Rubrique 1', '', 1, 16, 0, 0, 'priority asc'),
(2, 'Rubrique 2', '', 1, '2016-05-03 13:02:05', 1, '2016-05-03 13:02:05', '', 'img_fond_contact.jpg', '', '', 'Rubrique 2', '', 0, 1, 0, 0, 'priority asc');

-- --------------------------------------------------------

--
-- Structure de la table `content_home-demo`
--

CREATE TABLE IF NOT EXISTS `content_home-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__meta-title` varchar(100) DEFAULT NULL,
  `@_string__meta-description` varchar(200) DEFAULT NULL,
  `@_text__meta-keywords` text,
  `@_image#file__logo` varchar(511) DEFAULT NULL,
  `@_image#title__logo` varchar(511) DEFAULT NULL,
  `@_image#link__logo` varchar(511) DEFAULT NULL,
  `@_link#href__contact-email` varchar(511) DEFAULT NULL,
  `@_link#text__contact-email` varchar(511) DEFAULT NULL,
  `@_link#external__contact-email` tinyint(1) DEFAULT '1',
  `@_string__footer-left` varchar(511) DEFAULT NULL,
  `@_string__footer-right` varchar(511) DEFAULT NULL,
  `@_file#file__download-highlight` varchar(511) DEFAULT NULL,
  `@_file#text__download-highlight` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `content_home-demo`
--

INSERT INTO `content_home-demo` (`id`, `name`, `context`, `creator`, `publication_date`, `modificator`, `modification_date`, `@_string__meta-title`, `@_string__meta-description`, `@_text__meta-keywords`, `@_image#file__logo`, `@_image#title__logo`, `@_image#link__logo`, `@_link#href__contact-email`, `@_link#text__contact-email`, `@_link#external__contact-email`, `@_string__footer-left`, `@_string__footer-right`, `@_file#file__download-highlight`, `@_file#text__download-highlight`, `@_image#file__background-image`, `@_image#title__background-image`, `@_image#link__background-image`, `@_string__headline`, `@_text__body`) VALUES
(1, 'Site de dÃ©monstration', '', 1, '2016-05-03 12:46:25', 1, '2016-05-03 12:46:25', 'Woody CMS | Site de dÃ©monstration', 'Witch Case sociÃ©tÃ© d''Ã©dition web open source productrice de Woody CMS', '', 'logo_woody_gris.png', 'Logo Woody CMS', '', 'mailto:admin@witchcase.com', 'admin@witch-case.com', 1, 'Site rÃ©alisÃ© avec Woody CMS', 'Â©Witch case 2016. All Right Reserved', 'logo.jpg', 'TÃ©lÃ©charger Logo Witch case', 'img_fond_home.jpg', 'Woody CMS', '', 'Site de dÃ©monstration', '');

-- --------------------------------------------------------

--
-- Structure de la table `content_user`
--

CREATE TABLE IF NOT EXISTS `content_user` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__last-name` varchar(511) DEFAULT NULL,
  `@_string__first-name` varchar(511) DEFAULT NULL,
  `@_connexion__connection` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `content_user`
--

INSERT INTO `content_user` (`id`, `name`, `context`, `creator`, `publication_date`, `modificator`, `modification_date`, `@_string__last-name`, `@_string__first-name`, `@_connexion__connection`) VALUES
(1, 'Administrateur', '', 1, '2016-02-04 19:26:12', 1, '2016-02-04 19:26:12', 'Woody CMS', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft_article-demo`
--

CREATE TABLE IF NOT EXISTS `draft_article-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_image#file__image` varchar(511) DEFAULT NULL,
  `@_image#title__image` varchar(511) DEFAULT NULL,
  `@_image#link__image` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_string__headline-left` varchar(511) DEFAULT NULL,
  `@_text__left-column` text,
  `@_string__headline-center` varchar(511) DEFAULT NULL,
  `@_text__center-column` text,
  `@_string__headline-right` varchar(511) DEFAULT NULL,
  `@_text__right-column` text,
  `@_file#file__download` varchar(511) DEFAULT NULL,
  `@_file#text__download` varchar(511) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `draft_folder`
--

CREATE TABLE IF NOT EXISTS `draft_folder` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__title` varchar(511) DEFAULT NULL,
  `@_string__subtitle` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `draft_folder-demo`
--

CREATE TABLE IF NOT EXISTS `draft_folder-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__content-class` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text,
  `@_get_contents#get_article-demo__contents` tinyint(1) DEFAULT NULL,
  `@_get_contents#fk_localisation__contents` int(11) DEFAULT NULL,
  `@_get_contents#limit__contents` int(11) DEFAULT NULL,
  `@_get_contents#depth__contents` int(11) DEFAULT NULL,
  `@_get_contents#order__contents` varchar(511) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `draft_home-demo`
--

CREATE TABLE IF NOT EXISTS `draft_home-demo` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `content_key` int(11) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__meta-title` varchar(100) DEFAULT NULL,
  `@_string__meta-description` varchar(200) DEFAULT NULL,
  `@_text__meta-keywords` text,
  `@_image#file__logo` varchar(511) DEFAULT NULL,
  `@_image#title__logo` varchar(511) DEFAULT NULL,
  `@_image#link__logo` varchar(511) DEFAULT NULL,
  `@_link#href__contact-email` varchar(511) DEFAULT NULL,
  `@_link#text__contact-email` varchar(511) DEFAULT NULL,
  `@_link#external__contact-email` tinyint(1) DEFAULT '1',
  `@_string__footer-left` varchar(511) DEFAULT NULL,
  `@_string__footer-right` varchar(511) DEFAULT NULL,
  `@_file#file__download-highlight` varchar(511) DEFAULT NULL,
  `@_file#text__download-highlight` varchar(511) DEFAULT NULL,
  `@_image#file__background-image` varchar(511) DEFAULT NULL,
  `@_image#title__background-image` varchar(511) DEFAULT NULL,
  `@_image#link__background-image` varchar(511) DEFAULT NULL,
  `@_string__headline` varchar(511) DEFAULT NULL,
  `@_text__body` text
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `draft_user`
--

CREATE TABLE IF NOT EXISTS `draft_user` (
  `id` int(11) unsigned NOT NULL,
  `content_key` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modificator` int(11) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `@_string__last-name` varchar(511) DEFAULT NULL,
  `@_string__first-name` varchar(511) DEFAULT NULL,
  `@_connexion__connection` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `localisation`
--

CREATE TABLE IF NOT EXISTS `localisation` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `site` varchar(255) CHARACTER SET ascii DEFAULT NULL,
  `url` varchar(1023) CHARACTER SET ascii NOT NULL DEFAULT '/',
  `status` int(5) unsigned NOT NULL DEFAULT '0',
  `module` varchar(511) NOT NULL DEFAULT 'view',
  `target_table` varchar(255) CHARACTER SET ascii NOT NULL,
  `target_fk` int(11) unsigned DEFAULT NULL,
  `location_id` varchar(32) DEFAULT NULL,
  `is_main` int(1) unsigned NOT NULL DEFAULT '1',
  `context` varchar(255) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `level_1` int(11) unsigned DEFAULT NULL,
  `level_2` int(11) unsigned DEFAULT NULL,
  `level_3` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `localisation`
--

INSERT INTO `localisation` (`id`, `name`, `description`, `site`, `url`, `status`, `module`, `target_table`, `target_fk`, `location_id`, `is_main`, `context`, `datetime`, `priority`, `level_1`, `level_2`, `level_3`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C''est Ã  partir d''ici que sont crÃ©Ã©es les homes de chaque site de la plateforme. ', 'admin', '/', 0, 'root', '', NULL, '5b02d0443587a47f625aa3ce4970f50f', 1, NULL, '2015-07-01 16:36:41', 0, NULL, NULL, NULL),
(2, 'Utilisateurs', '', 'admin', '/utilisateurs', 0, 'view', 'content_folder', 1, '9dac789225beb25f38f3abba1312f83d', 1, NULL, '2016-02-02 16:40:49', 5, 11, NULL, NULL),
(3, 'Administrateur', '', 'admin', '/utilisateurs/administrateur', 0, 'view', 'content_user', 1, '68a2092655ae4a277d1f1b4c74a972df', 1, NULL, '2016-02-04 19:26:12', 0, 11, 1, NULL),
(4, 'Create', '', 'admin', '/create', 0, 'create', '', NULL, '66b393f2ef964dd01d379c3090b951b2', 1, NULL, '2015-07-29 18:37:04', 20, 4, NULL, NULL),
(5, 'Edit', '', 'admin', '/edit', 0, 'edit', '', NULL, 'a855c57fcf1a4a13c290fa7d166352f1', 1, NULL, '2015-07-21 18:45:54', 20, 3, NULL, NULL),
(6, 'Profiles', '', 'admin', '/profiles', 0, 'profiles', '', NULL, '11ab17576aba37603109ee0a969c56ac', 1, NULL, '2015-09-08 13:34:00', 20, 6, NULL, NULL),
(7, 'Structures', '', 'admin', '/structures', 0, 'structures', '', NULL, '799587706d47ce1167b5616606ca5a27', 1, NULL, '2015-10-28 16:50:20', 20, 7, NULL, NULL),
(8, 'Locations', '', 'admin', '/locations', 0, 'locations', '', NULL, '123456789d47ceazerty616606ca5a27', 1, NULL, '2015-10-28 16:50:20', 20, 8, NULL, NULL),
(9, 'Create Module', '', 'admin', '/createmodule', 0, 'createmodule', '', NULL, 'aaaaabbbbba0419f638014742e72ebb6', 1, NULL, '2016-01-26 18:58:20', 20, 9, NULL, NULL),
(10, 'CrÃ©er Nouveau Site', '', 'admin', '/createsite', 0, 'createsite', '', 0, 'f7e3e4513aefe224585c56baf3c90123', 1, NULL, '2016-01-28 20:38:36', 20, 10, NULL, NULL),
(11, 'login', '', 'admin', '/login', 0, 'login', '', 0, '4bbea08a23d07e6c149e146153b77ffc', 1, NULL, '2016-02-05 11:18:05', 20, 12, NULL, NULL),
(12, 'view', '', 'admin', '/view', 0, 'view', '', 0, '807e23a63dbf1f7f09d30fc4ec206311', 1, NULL, '2016-04-09 19:50:00', 20, 17, NULL, NULL),
(13, 'Site de dÃ©monstration', 'Ce site a pour but de vous montrer un exemple du fonctionnement de Woody CMS. ', 'admin', '/site-de-demonstration', 0, 'view', 'content_home-demo', 1, '23292293583afc89324de42dc49d975a', 1, NULL, '2016-05-03 12:46:25', 0, 18, NULL, NULL),
(14, 'Site de dÃ©monstration', 'Ce site a pour but de vous montrer un exemple du fonctionnement de Woody CMS. ', 'site-demo', '/', 0, 'view', 'content_home-demo', 1, '23292293583afc89324de42dc49d975a', 1, NULL, '2016-05-03 12:46:25', 0, 19, NULL, NULL),
(15, 'Rubrique 1', '', 'admin', '/site-de-demonstration/rubrique-1', 0, 'view', 'content_folder-demo', 1, '4a57224800d771f400986e26436e27f2', 1, NULL, '2016-05-03 13:00:09', 0, 18, 1, NULL),
(16, 'Rubrique 1', '', 'site-demo', '/rubrique-1', 0, 'view', 'content_folder-demo', 1, '4a57224800d771f400986e26436e27f2', 1, NULL, '2016-05-03 13:00:09', 0, 19, 1, NULL),
(17, 'Rubrique 2', '', 'admin', '/site-de-demonstration/rubrique-2', 0, 'view', 'content_folder-demo', 2, '4e78cfccfbce2bc6f9e4d6927e8814b9', 1, NULL, '2016-05-03 13:02:05', 0, 18, 2, NULL),
(18, 'Rubrique 2', '', 'site-demo', '/rubrique-2', 0, 'view', 'content_folder-demo', 2, '4e78cfccfbce2bc6f9e4d6927e8814b9', 1, NULL, '2016-05-03 13:02:05', 0, 19, 2, NULL),
(19, 'Article 1', '', 'admin', '/site-de-demonstration/rubrique-1/article-1', 0, '404', 'content_article-demo', 1, '6170816aa998dd7a0e3a6c88d3cdd29f', 1, NULL, '2016-05-03 13:05:41', 0, 18, 1, 1),
(20, 'Article 1', '', 'site-demo', '/rubrique-1/article-1', 0, '404', 'content_article-demo', 1, '6170816aa998dd7a0e3a6c88d3cdd29f', 1, NULL, '2016-05-03 13:05:41', 0, 19, 1, 1),
(21, 'Article 2', '', 'admin', '/site-de-demonstration/rubrique-1/article-2', 0, '404', 'content_article-demo', 2, 'cedc2a05093b1ad6d3c28677b78b96e7', 1, NULL, '2016-05-03 13:06:24', 0, 18, 1, 2),
(22, 'Article 2', '', 'site-demo', '/rubrique-1/article-2', 0, '404', 'content_article-demo', 2, 'cedc2a05093b1ad6d3c28677b78b96e7', 1, NULL, '2016-05-03 13:06:24', 0, 19, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `police`
--

CREATE TABLE IF NOT EXISTS `police` (
  `id` int(11) unsigned NOT NULL,
  `fk_profile` int(11) unsigned DEFAULT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'view',
  `action` varchar(255) DEFAULT NULL,
  `position` varchar(511) DEFAULT NULL,
  `inherit_subtree` tinyint(1) NOT NULL DEFAULT '1',
  `rigths_limitation` varchar(31) NOT NULL DEFAULT 'all',
  `group_fk_profile` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `police`
--

INSERT INTO `police` (`id`, `fk_profile`, `module`, `action`, `position`, `inherit_subtree`, `rigths_limitation`, `group_fk_profile`) VALUES
(1, 1, '*', '*', '*', 1, 'all', 0),
(6, 2, 'view', '*', '19,*', 1, 'all', 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_connexion`
--

CREATE TABLE IF NOT EXISTS `user_connexion` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'user signature',
  `email` varchar(511) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  `profiles` varchar(511) DEFAULT NULL,
  `target_table` varchar(255) DEFAULT NULL,
  `target_attribute` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'connexion',
  `target_attribute_var` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'fk_user_connexion',
  `attribute_name` varchar(511) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user_connexion`
--

INSERT INTO `user_connexion` (`id`, `name`, `email`, `login`, `pass_hash`, `profiles`, `target_table`, `target_attribute`, `target_attribute_var`, `attribute_name`, `datetime`) VALUES
(1, 'Administrator', 'admin@witch-case.com', 'admin', '$2y$11$Ifx.fy1u7tE1FbbvsnP/I.ybGY4Dfbixg.Nwf//TkoRj54XYm4TWG', 'public, administrator', 'content_user', 'connexion', '', 'connection', '2015-09-21 20:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `user_profile`
--

CREATE TABLE IF NOT EXISTS `user_profile` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user_profile`
--

INSERT INTO `user_profile` (`id`, `name`, `datetime`) VALUES
(1, 'administrator', '2015-09-21 19:30:00'),
(2, 'public', '2016-04-12 09:10:11');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `archive_article-demo`
--
ALTER TABLE `archive_article-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive_folder`
--
ALTER TABLE `archive_folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive_folder-demo`
--
ALTER TABLE `archive_folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive_home-demo`
--
ALTER TABLE `archive_home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive_user`
--
ALTER TABLE `archive_user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `attribute_files`
--
ALTER TABLE `attribute_files`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `attribute_images`
--
ALTER TABLE `attribute_images`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `attribute_links`
--
ALTER TABLE `attribute_links`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content_article-demo`
--
ALTER TABLE `content_article-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content_folder`
--
ALTER TABLE `content_folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content_folder-demo`
--
ALTER TABLE `content_folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content_home-demo`
--
ALTER TABLE `content_home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content_user`
--
ALTER TABLE `content_user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft_article-demo`
--
ALTER TABLE `draft_article-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft_folder`
--
ALTER TABLE `draft_folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft_folder-demo`
--
ALTER TABLE `draft_folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft_home-demo`
--
ALTER TABLE `draft_home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft_user`
--
ALTER TABLE `draft_user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `localisation`
--
ALTER TABLE `localisation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `police`
--
ALTER TABLE `police`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_connexion`
--
ALTER TABLE `user_connexion`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `archive_article-demo`
--
ALTER TABLE `archive_article-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `archive_folder`
--
ALTER TABLE `archive_folder`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `archive_folder-demo`
--
ALTER TABLE `archive_folder-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `archive_home-demo`
--
ALTER TABLE `archive_home-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `archive_user`
--
ALTER TABLE `archive_user`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `attribute_files`
--
ALTER TABLE `attribute_files`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `attribute_images`
--
ALTER TABLE `attribute_images`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `attribute_links`
--
ALTER TABLE `attribute_links`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `content_article-demo`
--
ALTER TABLE `content_article-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `content_folder`
--
ALTER TABLE `content_folder`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `content_folder-demo`
--
ALTER TABLE `content_folder-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `content_home-demo`
--
ALTER TABLE `content_home-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `content_user`
--
ALTER TABLE `content_user`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `draft_article-demo`
--
ALTER TABLE `draft_article-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `draft_folder`
--
ALTER TABLE `draft_folder`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `draft_folder-demo`
--
ALTER TABLE `draft_folder-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `draft_home-demo`
--
ALTER TABLE `draft_home-demo`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `draft_user`
--
ALTER TABLE `draft_user`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `localisation`
--
ALTER TABLE `localisation`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT pour la table `police`
--
ALTER TABLE `police`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `user_connexion`
--
ALTER TABLE `user_connexion`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
