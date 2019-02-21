-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 21-Fev-2019 às 23:41
-- Versão do servidor: 10.1.22-MariaDB
-- PHP Version: 7.0.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `available`
--

CREATE TABLE `available` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `datetimestart` datetime NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `lotation` int(11) DEFAULT NULL,
  `datetimeend` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `available`
--

INSERT INTO `available` (`id`, `category_id`, `datetimestart`, `stock`, `lotation`, `datetimeend`) VALUES
(59, 16, '2019-02-20 11:30:00', 1, 30, '2019-02-20 12:15:00'),
(60, 17, '2019-02-20 09:45:00', 27, 30, '2019-02-20 11:15:00'),
(61, 16, '2019-02-21 08:30:00', 27, 30, '2019-02-21 09:15:00'),
(62, 16, '2019-02-25 08:30:00', 30, 30, '2019-02-25 09:15:00'),
(63, 16, '2019-02-26 08:30:00', 30, 30, '2019-02-26 09:15:00'),
(107, 16, '2019-02-25 11:30:00', 30, 30, '2019-02-25 12:15:00'),
(108, 16, '2019-02-26 11:30:00', 30, 30, '2019-02-26 12:15:00'),
(109, 16, '2019-02-27 11:30:00', 30, 30, '2019-02-27 12:15:00'),
(110, 16, '2019-02-28 11:30:00', 28, 30, '2019-02-28 12:15:00'),
(111, 16, '2019-03-01 11:30:00', 30, 30, '2019-03-01 12:15:00'),
(112, 16, '2019-03-02 11:30:00', 30, 30, '2019-03-02 12:15:00'),
(113, 16, '2019-03-03 11:30:00', 30, 30, '2019-03-03 12:15:00'),
(183, 18, '2019-02-25 15:30:00', 30, 30, '2019-02-25 16:00:00'),
(184, 18, '2019-02-26 15:30:00', 30, 30, '2019-02-26 16:00:00'),
(185, 18, '2019-02-27 15:30:00', 27, 30, '2019-02-27 16:00:00'),
(186, 18, '2019-02-28 15:30:00', 30, 30, '2019-02-28 16:00:00'),
(187, 16, '2019-02-25 10:30:00', 30, 30, '2019-02-25 11:15:00'),
(188, 16, '2019-02-26 10:30:00', 30, 30, '2019-02-26 11:15:00'),
(189, 16, '2019-02-27 10:30:00', 0, 30, '2019-02-27 11:15:00'),
(190, 16, '2019-02-28 10:30:00', 22, 30, '2019-02-28 11:15:00'),
(191, 18, '2019-02-28 09:30:00', 27, 30, '2019-02-28 10:00:00'),
(192, 18, '2019-03-01 09:30:00', 30, 30, '2019-03-01 10:00:00'),
(193, 18, '2019-03-02 09:30:00', 30, 30, '2019-03-02 10:00:00'),
(194, 18, '2019-03-03 09:30:00', 30, 30, '2019-03-03 10:00:00'),
(195, 18, '2019-03-04 09:30:00', 27, 30, '2019-03-04 10:00:00'),
(196, 17, '2019-02-28 14:45:00', 30, 30, '2019-02-28 16:15:00'),
(197, 17, '2019-03-01 14:45:00', 30, 30, '2019-03-01 16:15:00'),
(198, 17, '2019-03-02 14:45:00', 30, 30, '2019-03-02 16:15:00'),
(199, 17, '2019-03-03 14:45:00', 29, 30, '2019-03-03 16:15:00'),
(200, 17, '2019-03-04 14:45:00', 30, 30, '2019-03-04 16:15:00'),
(201, 17, '2019-03-05 14:45:00', 30, 30, '2019-03-05 16:15:00'),
(202, 17, '2019-03-06 14:45:00', 30, 30, '2019-03-06 16:15:00'),
(203, 17, '2019-03-07 14:45:00', 30, 30, '2019-03-07 16:15:00'),
(204, 17, '2019-03-08 14:45:00', 30, 30, '2019-03-08 16:15:00'),
(205, 17, '2019-03-09 14:45:00', 30, 30, '2019-03-09 16:15:00'),
(206, 17, '2019-03-10 14:45:00', 30, 30, '2019-03-10 16:15:00'),
(207, 15, '2019-02-25 09:00:00', 15, 15, '2019-02-25 10:15:00'),
(208, 15, '2019-02-26 09:00:00', 15, 15, '2019-02-26 10:15:00'),
(209, 15, '2019-02-27 09:00:00', 15, 15, '2019-02-27 10:15:00'),
(210, 15, '2019-02-28 09:00:00', 15, 15, '2019-02-28 10:15:00'),
(211, 15, '2019-03-01 09:00:00', 15, 15, '2019-03-01 10:15:00'),
(212, 15, '2019-03-02 09:00:00', 15, 15, '2019-03-02 10:15:00'),
(213, 15, '2019-03-03 09:00:00', 15, 15, '2019-03-03 10:15:00'),
(214, 15, '2019-03-04 09:00:00', 15, 15, '2019-03-04 10:15:00'),
(215, 15, '2019-03-05 09:00:00', 15, 15, '2019-03-05 10:15:00'),
(216, 15, '2019-03-06 09:00:00', 15, 15, '2019-03-06 10:15:00'),
(217, 15, '2019-03-07 09:00:00', 15, 15, '2019-03-07 10:15:00'),
(218, 15, '2019-03-08 09:00:00', 12, 15, '2019-03-08 10:15:00'),
(219, 15, '2019-03-09 09:00:00', 15, 15, '2019-03-09 10:15:00'),
(220, 15, '2019-03-10 09:00:00', 15, 15, '2019-03-10 10:15:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `blockdate`
--

CREATE TABLE `blockdate` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `blockdate` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge_total` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `adult` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL,
  `baby` int(11) DEFAULT NULL,
  `posted_at` date NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','canceled','confirmed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) UNSIGNED NOT NULL COMMENT '(DC2Type:money)',
  `available_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `date_event` date NOT NULL,
  `time_event` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `booking`
--

INSERT INTO `booking` (`id`, `adult`, `children`, `baby`, `posted_at`, `notes`, `status`, `amount`, `available_id`, `client_id`, `date_event`, `time_event`) VALUES
(45, 1, 0, 0, '2019-02-16', NULL, 'pending', 1500, 59, 67, '2019-02-20', '11:30:00'),
(47, 1, 1, 0, '2019-02-16', NULL, 'pending', 2000, 59, 69, '2019-02-20', '11:30:00'),
(48, 2, 2, 0, '2019-02-16', '<p>Estamos à sua espera aqui: <a href=\"https://www.google.com/maps/dir//37.1248149,-8.5282595\" target=\"_blank\">clique</a></p>', 'confirmed', 7000, 59, 70, '2019-02-20', '11:30:00'),
(49, 2, 2, 0, '2019-02-17', NULL, 'pending', 4000, 59, 71, '2019-02-20', '11:30:00'),
(50, 2, 3, 0, '2019-02-17', '<p>Well be waiting<a href=\"https://www.google.com/maps/dir//37.101808,-8.76528\" target=\"_blank\"> Here</a></p>', 'canceled', 10500, 60, 72, '2019-02-20', '09:45:00'),
(51, 1, 1, 1, '2019-02-17', '<p><br></p>', 'canceled', 4500, 60, 73, '2019-02-20', '09:45:00'),
(52, 1, 1, 1, '2019-02-18', NULL, 'pending', 4500, 60, 74, '2019-02-20', '09:45:00'),
(53, 1, 1, 1, '2019-02-20', NULL, 'pending', 2500, 195, 75, '2019-03-04', '09:30:00'),
(54, 1, 0, 7, '2019-02-20', NULL, 'pending', 1500, 190, 76, '2019-02-28', '10:30:00'),
(55, 1, 1, 0, '2019-02-20', NULL, 'pending', 2000, 110, 77, '2019-02-28', '11:30:00'),
(56, 1, 1, 1, '2019-02-20', NULL, 'pending', 2000, 61, 78, '2019-02-21', '08:30:00'),
(57, 1, 1, 1, '2019-02-20', NULL, 'pending', 2500, 185, 79, '2019-02-27', '15:30:00'),
(58, 1, 1, 1, '2019-02-20', NULL, 'pending', 4500, 218, 80, '2019-03-08', '09:00:00'),
(59, 1, 1, 1, '2019-02-21', NULL, 'pending', 2500, 191, 81, '2019-02-28', '09:30:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name_pt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_pt` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `children_price` int(11) UNSIGNED NOT NULL COMMENT '(DC2Type:money)',
  `adult_price` int(11) UNSIGNED NOT NULL COMMENT '(DC2Type:money)',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability` int(11) NOT NULL,
  `highlight` tinyint(1) NOT NULL DEFAULT '0',
  `warranty_payment_pt` varchar(350) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty_payment_en` varchar(350) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty_payment` tinyint(1) NOT NULL DEFAULT '0',
  `duration` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `category`
--

INSERT INTO `category` (`id`, `name_pt`, `name_en`, `description_pt`, `description_en`, `children_price`, `adult_price`, `is_active`, `image`, `availability`, `highlight`, `warranty_payment_pt`, `warranty_payment_en`, `warranty_payment`, `duration`) VALUES
(15, 'Visita Tradicional', 'Traditional Visit', 'Visite entre 15/20 grutas, algares e praias desertas. Inclui uma paragem de 10 min para mergulho, quando as condições do mar permitirem. O mergulho pode ser dado numa gruta ou numa praia deserta.\r\n(Bébés grátis até 23 meses.)', 'Visit about 15/20 caves. If we have good sea conditions, a 10 minute refreshing dive along the shore.\r\n(Free Babies till 23 months.)', 1500, 3000, 1, 'c1de35fcca54082b1874dbd824550143.jpeg', 15, 0, NULL, NULL, 0, '01:15'),
(16, 'Aluguer Kayak', 'Kayak Rental', 'É um aluguer livre que obriga o preenchimento de um termo de responsabilidade onde a pessoa que aluga tem de possuir o seu documento de identificação. \r\nNão guardamos bens pessoais no local. \r\nO equipamento é adquirido na entrada da praia ficando à responsabilidade de quem aluga a entrega do mesmo no final do aluguer.', 'Rental, no guide, only for children more than 5 years old.', 500, 1500, 1, '58ec7e3faedc9095005c1769f5294676.jpeg', 30, 0, NULL, NULL, 0, '00:45'),
(17, 'Visita de Portimão', 'Boarding in marina of Portimão', 'O passeio começa a partir da Marina de Portimão e também visitamos as grutas de Benagil, Carvoeiro, Carvalho e Marinha. É semelhante à nossa visita tradicional. Também é possível parar 10 minutos para um mergulho refrescante - com a mesma equipa da praia (Taruga). (Bébés grátis até 23 meses.)', 'The tour starts from Marina of Portimão and we also visit the Benagil sea caves, Carvoeiro, Carvalho and Marinha beach. It´s similar from our tour number 2. Also possible to stop 10 minutes for a refreshing dive - with the same team of the beach (Taruga). (Free Babies till 23 months.)', 1500, 3000, 1, '577ec312a2cbcd3d317a05cbd1e24fec.jpeg', 30, 0, 'Tem Garantia Pagamento, é necessário inserir os dados do cartão crédito, Apenas será cobrado na falta de comparência.', 'It has Guarantee Payment, it is necessary to insert your credit card information.  We will only charged in absence of appearance.', 1, '01:30'),
(18, 'Visita Express', 'Express Visit', 'Visite cerca de 8 grutas incluindo: Algar de Benagil, Praia da Marinha. (Bébés grátis até 23 meses.)', 'Visit about 8 caves, included: Algar de Benagil, Marinha beach. (Free Babies till 23 months.)', 1000, 1500, 1, '74d8433950d0c9dd324a5e2806aa2d6c.jpeg', 30, 1, NULL, NULL, 0, '00:30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `rgpd` tinyint(1) NOT NULL,
  `locale_id` int(11) DEFAULT NULL,
  `cvv` longtext COLLATE utf8mb4_unicode_ci,
  `card_name` longtext COLLATE utf8mb4_unicode_ci,
  `card_nr` longtext COLLATE utf8mb4_unicode_ci,
  `card_date` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `client`
--

INSERT INTO `client` (`id`, `email`, `password`, `username`, `address`, `telephone`, `roles`, `rgpd`, `locale_id`, `cvv`, `card_name`, `card_nr`, `card_date`) VALUES
(67, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(69, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(70, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(71, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(72, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, 'MDEy', 'Q3JxZWIgSXZydG5m', '', 'MDMvMjAxOQ=='),
(73, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, 'MDEy', 'Q3JxZWIgSXZydG5m', '', 'MDMvMjAxOQ=='),
(74, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, 'MDEy', 'Q3JxZWIgSXZydG5m', '', 'MDMvMjAxOQ=='),
(75, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(76, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(77, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(78, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 2, NULL, NULL, '', NULL),
(79, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(80, 'aXRmY3JxZWJAb2J0aGYucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2Njc1Ng==', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL),
(81, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 1, 1, NULL, NULL, '', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `easytext`
--

CREATE TABLE `easytext` (
  `id` int(11) NOT NULL,
  `easy_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `easy_text_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `easytext`
--

INSERT INTO `easytext` (`id`, `easy_text`, `easy_text_html`, `name`) VALUES
(1, '{\"ops\":[{\"insert\":\"Well be waiting\"},{\"attributes\":{\"link\":\"https://www.google.com/maps/dir//37.1248149,-8.5282595\"},\"insert\":\" Here\"},{\"insert\":\"\\n\"}]}', '<p>Well be waiting<a href=\"https://www.google.com/maps/dir//37.1248149,-8.5282595\" target=\"_blank\"> Here</a></p>', 'PickUp Info en_EN'),
(2, '{\"ops\":[{\"insert\":\"Estamos à sua espera aqui: \"},{\"attributes\":{\"link\":\"https://www.google.com/maps/dir//37.1248149,-8.5282595\"},\"insert\":\"clique\"},{\"insert\":\"\\n\"}]}', '<p>Estamos à sua espera aqui: <a href=\"https://www.google.com/maps/dir//37.1248149,-8.5282595\" target=\"_blank\">clique</a></p>', 'PickUp Info pt_PT');

-- --------------------------------------------------------

--
-- Estrutura da tabela `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `event` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `event`
--

INSERT INTO `event` (`id`, `category_id`, `event`) VALUES
(1, 15, '09:00,11:00,13:00,15:00'),
(2, 16, '08:30,09:30,10:30,11:30'),
(3, 17, '09:45,14:45'),
(10, 18, '09:30,12:30,15:30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `name_pt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_pt` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `gallery`
--

INSERT INTO `gallery` (`id`, `name_pt`, `name_en`, `description_pt`, `description_en`, `is_active`, `image`) VALUES
(1, 'im pt4', 'im en', 'desc pt', 'desc en', 1, 'aaa8358bb5e15ef1307ef58850629682.jpeg'),
(2, 'im pt', 'im en', 'desc pt', 'desc en', 1, '972dabbc8130385d8e51b3add68d24c3.jpeg'),
(3, 'img 2', 'img 2', 'img 2', 'img 2', 1, '79741a0156e7c8b39a48901b5eed5292.jpeg'),
(4, 'img 2', 'img 2', 'img 2', 'img 2', 1, '09ba4f89a15f55955e63db76c6d8509d.jpeg'),
(5, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'a3fd85d1dc0dbfeb849e1d3c5828ce33.jpeg'),
(6, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'f7b351cd838ad5feb744241d2e8ecf0d.jpeg'),
(7, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'b533c1e58ebe0bcd250bced3a8d6aa54.jpeg'),
(8, 'img 2', 'img 2', 'img 2', 'img 2', 1, '77556ee3bb9ad71a414fedc3e50e58d9.jpeg'),
(9, 'img 2', 'img 2', 'img 2', 'img 2', 1, '4d1e3614ce1403dee6eb908682913d9b.jpeg'),
(10, 'img 2', 'img 2', 'img 2', 'img 2', 1, '8bff3f4eddf7775d113667792275eb39.jpeg'),
(11, 'img 2', 'img 2', 'img 2', 'img 2', 1, '837851b287ed6bec0bbadf21830a5daa.jpeg'),
(12, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'febd1fc24a91cfcea5c32c7772061d0a.jpeg'),
(13, 'img 2', 'img 2', 'img 2', 'img 2', 1, '923467b100b63208148a132b96ff158d.jpeg'),
(14, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'e75abc66807d2a260c2a7ff155c67a30.jpeg'),
(15, 'img 2', 'img 2', 'img 2', 'img 2', 1, '0ad7090b02d2bb37958ff73f481575d5.jpeg'),
(16, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'cf07f2b40ba6763c751d87cf3913faad.jpeg'),
(17, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'caf68c2d0e92c02d9466d037e75af928.jpeg'),
(18, 'img 2', 'img 2', 'img 2', 'img 2', 1, 'dff35cf7c8315fbc8d6584bc86087738.jpeg'),
(19, 'img 2', 'img 2', 'img 2', 'img 2', 1, '52ff070121b0439f4564c07175e11222.jpeg'),
(20, 'img 2', 'img 2', 'img 2', 'img 2', 1, '294cc4bbe18ee2ca67f7cea34dd161b8.jpeg'),
(21, 'ka', 'ka', '', '', 0, 'f3d6fb71938ddb0e213ffba0acda3603.jpeg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `locales`
--

CREATE TABLE `locales` (
  `id` int(11) NOT NULL,
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `locales`
--

INSERT INTO `locales` (`id`, `name`, `filename`) VALUES
(1, 'pt_PT', 'icon-pt.jpg'),
(2, 'en_EN', 'icon-en.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190201203509', NULL),
('20190204191325', '2019-02-04 19:14:01'),
('20190204203153', '2019-02-04 20:32:04'),
('20190205080336', '2019-02-05 08:03:59'),
('20190205191432', '2019-02-05 19:14:51'),
('20190206193540', '2019-02-06 19:36:12'),
('20190206193810', '2019-02-06 19:38:17'),
('20190206194357', '2019-02-06 19:44:04'),
('20190206194837', '2019-02-06 19:48:42'),
('20190206195833', '2019-02-06 19:58:38'),
('20190207185635', '2019-02-07 18:56:55'),
('20190207200228', '2019-02-07 20:02:36'),
('20190207204215', '2019-02-07 20:42:23'),
('20190207204309', '2019-02-07 20:43:15'),
('20190207205023', '2019-02-07 20:50:29'),
('20190207205121', '2019-02-07 20:51:27'),
('20190207223658', '2019-02-07 22:37:04'),
('20190207223900', '2019-02-07 22:39:06'),
('20190207224018', '2019-02-07 22:40:25'),
('20190207224304', '2019-02-07 22:43:09'),
('20190207225033', '2019-02-07 22:50:39'),
('20190207225153', '2019-02-07 22:51:57'),
('20190208074523', '2019-02-08 07:45:51'),
('20190208075028', '2019-02-08 07:50:34'),
('20190208075509', '2019-02-08 07:55:14'),
('20190208080244', '2019-02-08 08:02:49'),
('20190210131653', '2019-02-10 13:17:15'),
('20190210133346', '2019-02-10 13:33:50'),
('20190213075426', '2019-02-13 07:54:52'),
('20190213082355', '2019-02-13 08:24:01'),
('20190213193534', '2019-02-13 19:35:39'),
('20190213201327', '2019-02-13 20:13:32'),
('20190216130952', '2019-02-16 13:10:12'),
('20190216132110', '2019-02-16 13:21:14'),
('20190216134753', '2019-02-16 13:47:59'),
('20190216135720', '2019-02-16 13:57:25'),
('20190216135923', '2019-02-16 13:59:28'),
('20190216144120', '2019-02-16 14:41:49'),
('20190216181801', '2019-02-16 18:18:10'),
('20190216182415', '2019-02-16 18:24:23'),
('20190216192516', '2019-02-16 19:25:21'),
('20190216193055', '2019-02-16 19:30:59'),
('20190216193101', '2019-02-16 19:36:17'),
('20190216193611', '2019-02-16 19:36:17'),
('20190216193803', '2019-02-16 19:38:09'),
('20190217154242', '2019-02-17 15:42:59'),
('20190219215349', '2019-02-19 21:54:09');

-- --------------------------------------------------------

--
-- Estrutura da tabela `rgpd`
--

CREATE TABLE `rgpd` (
  `id` int(11) NOT NULL,
  `rgpd_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locales_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `rgpd`
--

INSERT INTO `rgpd` (`id`, `rgpd_html`, `name`, `locales_id`) VALUES
(1, '<p>A partir de 25 de maio passa a ser aplicável o Regulamento Geral sobre a Proteção de Dados Pessoais – Regulamento n.º 2016/679 do Parlamento Europeu e do Conselho, de 27 de abril de 2016, que estabelece as regras relativas à proteção, tratamento e livre circulação dos dados pessoais das pessoas singulares e que se aplica diretamente a todas as entidades que procedam ao tratamento desses dados, em qualquer Estado Membro da União Europeia, nomeadamente Portugal.</p>\r\n<p>O objetivo desta comunicação é dar-lhe a conhecer as novas regras aplicáveis ao tratamento dos seus dados pessoais, os direitos que lhe assistem, assim como informar da forma como pode gerir, diretamente e de forma simples, os respetivos consentimentos.</p>\r\n<p>\r\n\r\n<b>Entidade responsável pelo tratamento:</b><br>\r\nVento Aprumado, Lda.<br>\r\nBeco Dinis Gregorinho Lt.51A<br>\r\n8400-665 Belavista Parchal<br>\r\n\r\nEndereço do Encarregado de Proteção de Dados: protecaodedados@tarugabenagiltours.pt<br>\r\n</p>\r\n\r\n<p>\r\nOs nossos registos incluem dados que foram obtidos através do formulario de reservas em https://tarugabenagiltours.pt<br>\r\nAssim, e para além das situações em que tratamos dados para cumprimento de imposições legais, tratamos os seus dados para as seguintes finalidades:<br>\r\nA Taruga Benagil Tours conservará os seus dados pessoais pelo período de 30 (trinta) dias a partir da data da realização da sua Experiência/Tour.<br>\r\nOs seus dados pessoais podem ser comunicados a autoridades judiciais, fiscais e regulatórias, com a finalidade do cumprimento de imposições legais.<br>\r\n</p>\r\n<p>\r\nEm qualquer momento, tem o direito de aceder aos seus dados pessoais, bem como, dentro dos limites dos serviços prestados e do Regulamento, de os alterar, opor-se ao respetivo tratamento, decidir sobre o tratamento automatizado dos mesmos, retirar o consentimento e exercer os demais direitos previstos na lei. Caso retire o seu consentimento, tal não compromete a licitude do tratamento efetuado até essa data. Tem o direito de receber uma notificação, nos termos previstos no Regulamento, caso ocorra uma violação dos seus dados pessoais, podendo apresentar reclamações perante a(s) autoridade(s).\r\n</p>\r\n<p>\r\nGarantimos todos os direitos consagrados no Regulamento. Para tal, a partir de 25-05-2018 pode aceder rápida, comodamente e de forma segura, e aí verificar os seus dados e as subscrições que possui ativas e atuar sobre essa informação.\r\n</p>\r\n<p>\r\nEstamos, como sempre estivemos, empenhados na proteção e confidencialidade dos seus dados pessoais. Tomámos as medidas técnicas e organizativas necessárias ao cumprimento do Regulamento, garantindo que o tratamento dos seus dados pessoais é lícito, leal, transparente e limitado às finalidades autorizadas. Adotámos as medidas que consideramos adequadas para assegurar a exatidão, integridade e confidencialidade dos seus dados pessoais, bem como todos os demais direitos que lhe assistem.</p>\r\n<p>Caso não autorize o registo dos seus dados pessoais, não é possivel efetuar a reserva.</p>', 'RGPD (Regulamento Geral de Proteção de Dados)', 1),
(2, '<p>As of 25 May, the General Regulation on the Protection of Personal Data - Regulation 2016/679 of the European Parlament and of the Council of 27 April 2016 rules on the protection, processing and free movement of personal data of natural persons and which applies directly to all entities handling such data in any Member State of the European Union, in particular Portugal. <!-- p-->\r\n</p><p> The aim of this communication is to inform you of the new rules applicable to the processing of your personal data, the rights that you have, and how you can directly and simply manage your consents.<!-- p-->\r\n</p><p>\r\n\r\n<b>Body responsible for treatment: <!-- b--> <br>\r\nVento Aprumado, Lda. <br>\r\nBeco Dinis Gregorinho Lt.51A <br>\r\n8400-665 Belavista Parchal <br>\r\nAddress of the Data Protection Officer: protecaodedados@tarugabenagiltours.pt <br>\r\n<!-- p-->\r\n\r\n</b></p><p><b>\r\nOur records include data that was obtained through the booking form at https://tarugabenagiltours.pt <br>\r\nThus, in addition to the situations in which we treat data to comply with legal requirements, we treat your data for the following purposes: <br>\r\nTaruga Benagil Tours will retain your personal data for a period of 30 (thirty) days from the date of your Experience / Tour.\r\nYour personal data can be communicated to judicial, tax and regulatory authorities, with the purpose of complying with legal impositions.\r\n<!-- p-->\r\n</b></p><p><b>\r\nAt any time, you have the right to access your personal data, as well as, within the limits of the services provided and the Regulation, to change them, oppose their treatment, decide on the automated treatment of them, withdraw consent and exercise the other rights provided for by law. If you withdraw your consent, this does not compromise the lawfulness of the treatment made up to that date. You have the right to receive a notification under the terms of the Regulation in case there is a violation of your personal data and you can submit complaints to the authority (s).\r\n<!-- p-->\r\n</b></p><p><b>\r\nWe guarantee all rights enshrined in the Regulation. To do so, as of 25-05-2018 you can access quickly, conveniently and securely, and there verify your data and the subscriptions you have active and act on that information.\r\n<!-- p-->\r\n</b></p><p><b>\r\nWe are, as we have always been, committed to the protection and confidentiality of your personal data. We have taken the technical and organizational measures necessary to comply with the Regulation, ensuring that the processing of your personal data is lawful, fair, transparent and limited to the authorized purposes. We have adopted the measures we deem appropriate to ensure the accuracy, completeness and confidentiality of your personal data, as well as all other rights you may have. <!-- P-->\r\n</b></p><p><b> If you do not authorize the registration of your personal data, its not possible to make a reservation. <!-- p--></b></p>', 'GDPR (General Data Protection Regulation)', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `email`, `username`, `password`, `roles`, `status`) VALUES
(2, 'vgspedro@gmail.com', 'pedro', '$2y$13$uvlH4Ntoc0XwQUQUNh3hPudpn7gLEEBpvS.h3O3VutuM8WbBhDcAC', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 1),
(3, 'vgspedro15@sapo.pt', 'pedro15', '$2y$13$3zW3eBXx7eo8zqmNtzn5EeFsb6A887X55rOFDqLtts0LPbJF7ZNq6', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `warning`
--

CREATE TABLE `warning` (
  `id` int(11) NOT NULL,
  `info_pt` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info_en` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `warning`
--

INSERT INTO `warning` (`id`, `info_pt`, `info_en`, `visible`) VALUES
(10, 'Avisos em PT ... 13', 'Warning EN .....', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `available`
--
ALTER TABLE `available`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A58FA48512469DE2` (`category_id`);

--
-- Indexes for table `blockdate`
--
ALTER TABLE `blockdate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_857EE7BA12469DE2` (`category_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E00CEDDE36D3FBA2` (`available_id`),
  ADD KEY `IDX_E00CEDDE19EB6921` (`client_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C7440455E559DFD1` (`locale_id`);

--
-- Indexes for table `easytext`
--
ALTER TABLE `easytext`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3BAE0AA712469DE2` (`category_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `rgpd`
--
ALTER TABLE `rgpd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C80AB619164006B8` (`locales_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- Indexes for table `warning`
--
ALTER TABLE `warning`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `available`
--
ALTER TABLE `available`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;
--
-- AUTO_INCREMENT for table `blockdate`
--
ALTER TABLE `blockdate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT for table `easytext`
--
ALTER TABLE `easytext`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `rgpd`
--
ALTER TABLE `rgpd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `warning`
--
ALTER TABLE `warning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `available`
--
ALTER TABLE `available`
  ADD CONSTRAINT `FK_A58FA48512469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Limitadores para a tabela `blockdate`
--
ALTER TABLE `blockdate`
  ADD CONSTRAINT `FK_857EE7BA12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Limitadores para a tabela `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `FK_E00CEDDE19EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_E00CEDDE36D3FBA2` FOREIGN KEY (`available_id`) REFERENCES `available` (`id`);

--
-- Limitadores para a tabela `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C7440455E559DFD1` FOREIGN KEY (`locale_id`) REFERENCES `locales` (`id`);

--
-- Limitadores para a tabela `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Limitadores para a tabela `rgpd`
--
ALTER TABLE `rgpd`
  ADD CONSTRAINT `FK_C80AB619164006B8` FOREIGN KEY (`locales_id`) REFERENCES `locales` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
