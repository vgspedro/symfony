-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 10-Fev-2019 às 20:47
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
  `datetime` datetime NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `lotation` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `available`
--

INSERT INTO `available` (`id`, `category_id`, `datetime`, `stock`, `lotation`) VALUES
(1, 16, '2019-02-20 08:30:00', 2, 30),
(2, 16, '2019-02-20 09:30:00', 25, 30),
(3, 16, '2019-02-21 10:30:00', 4, 30),
(4, 16, '2019-02-20 11:30:00', 5, 30);

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

--
-- Extraindo dados da tabela `blockdate`
--

INSERT INTO `blockdate` (`id`, `category_id`, `blockdate`, `charge_total`) VALUES
(1, 15, '', 1),
(2, 16, '10/02/2019', 0),
(3, 17, '21/02/2019', 1),
(4, 18, '21/02/2019', 1),
(5, 19, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `adult` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL,
  `baby` int(11) DEFAULT NULL,
  `posted_at` datetime NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','canceled','confirmed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) UNSIGNED NOT NULL COMMENT '(DC2Type:money)',
  `available_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `booking`
--

INSERT INTO `booking` (`id`, `adult`, `children`, `baby`, `posted_at`, `notes`, `status`, `amount`, `available_id`) VALUES
(20, 7, 3, 4, '2018-06-03 14:48:55', '<p>Well be waiting<a href=\"https://www.google.com/maps/dir//37.101808,-8.76528\" target=\"_blank\"> Here</a></p>', 'confirmed', 0, 1),
(21, 4, 4, 4, '2018-06-03 15:26:09', NULL, 'pending', 0, 1),
(22, 3, 3, 3, '2018-06-05 20:47:25', NULL, 'pending', 0, 4),
(23, 5, 2, 1, '2018-06-08 22:43:47', NULL, 'pending', 0, 2),
(24, 3, 2, 1, '2018-06-08 22:44:40', NULL, 'pending', 0, 2),
(25, 2, 2, 2, '2018-06-09 12:41:08', NULL, 'canceled', 0, 1),
(26, 4, 1, 0, '2018-06-10 18:39:51', '<p>Well be waiting<a href=\"https://www.google.com/maps/dir//37.101808,-8.76528\" target=\"_blank\"> Here</a></p>', 'pending', 0, 4),
(27, 3, 1, 0, '2018-06-10 19:25:51', NULL, 'pending', 0, 4),
(28, 2, 1, 0, '2018-06-16 07:25:20', NULL, 'pending', 0, 3),
(29, 2, 0, 0, '2018-06-16 13:47:42', NULL, 'pending', 0, 2);

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
  `warranty_payment` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `category`
--

INSERT INTO `category` (`id`, `name_pt`, `name_en`, `description_pt`, `description_en`, `children_price`, `adult_price`, `is_active`, `image`, `availability`, `highlight`, `warranty_payment_pt`, `warranty_payment_en`, `warranty_payment`) VALUES
(15, 'Visita Tradicional - 75 Min.', 'Traditional Visit - 75 Min.', 'Visite entre 15/20 grutas, algares e praias desertas. Inclui uma paragem de 10 min para mergulho, quando as condições do mar permitirem. O mergulho pode ser dado numa gruta ou numa praia deserta.\r\n(Bébés grátis até 23 meses.)', 'Visit about 15/20 caves. If we have good sea conditions, a 10 minute refreshing dive along the shore.\r\n(Free Babies till 23 months.)', 1500, 3000, 1, 'c1de35fcca54082b1874dbd824550143.jpeg', 15, 0, NULL, NULL, 0),
(16, 'Aluguer Kayak - 45 Min.', 'Kayak Rental - 45 Min.', 'É um aluguer livre que obriga o preenchimento de um termo de responsabilidade onde a pessoa que aluga tem de possuir o seu documento de identificação. \r\nNão guardamos bens pessoais no local. \r\nO equipamento é adquirido na entrada da praia ficando à responsabilidade de quem aluga a entrega do mesmo no final do aluguer.', 'Rental, no guide, only for children more than 5 years old.', 150, 1500, 1, '58ec7e3faedc9095005c1769f5294676.jpeg', 30, 0, NULL, NULL, 0),
(17, 'Visita de Portimão +- 90 Min.', 'Boarding in marina of Portimão +- 90 Min.', 'O passeio começa a partir da Marina de Portimão e também visitamos as grutas de Benagil, Carvoeiro, Carvalho e Marinha. É semelhante à nossa visita tradicional. Também é possível parar 10 minutos para um mergulho refrescante - com a mesma equipa da praia (Taruga). (Bébés grátis até 23 meses.)', 'The tour starts from Marina of Portimão and we also visit the Benagil sea caves, Carvoeiro, Carvalho and Marinha beach. It´s similar from our tour number 2. Also possible to stop 10 minutes for a refreshing dive - with the same team of the beach (Taruga). (Free Babies till 23 months.)', 1500, 3000, 1, '577ec312a2cbcd3d317a05cbd1e24fec.jpeg', 30, 0, 'Tem Garantia Pagamento, é necessário inserir os dados do cartão crédito, Apenas será cobrado na falta de comparência.', 'It has Guarantee Payment, it is necessary to insert your credit card information.  We will only charged in absence of appearance.', 1),
(18, 'Visita Express - 30 Min.', 'Express Visit - 30 Min.', 'Visite cerca de 8 grutas incluindo: Algar de Benagil, Praia da Marinha. (Bébés grátis até 23 meses.)', 'Express Visit - 30 Min. - Visit about 8 caves, included: Algar de Benagil, Marinha beach. (Free Babies till 23 months.)', 1000, 1500, 1, '74d8433950d0c9dd324a5e2806aa2d6c.jpeg', 30, 1, NULL, NULL, 0),
(19, 't', 't', 'rr', 'rr', 0, 0, 0, 'H:\\ARMAZEM\\TRABALHO\\symfony-taruga/public/upload/category/no-image.png', 45, 0, NULL, NULL, 0);

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
  `locale_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `client`
--

INSERT INTO `client` (`id`, `email`, `password`, `username`, `address`, `telephone`, `roles`, `rgpd`, `locale_id`) VALUES
(12, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 2),
(13, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5mMg==', 'RWhuIFB2ZWhldHbDo2IgMjgy', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(14, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 2),
(15, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(16, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(17, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(18, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(19, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWI=', 'RWhuIDI1IE5vZXZ5LCAzNCBZbnRiZiA=', 'OTg3NjU0MzIx', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1),
(20, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWIgaQ==', 'T3JhbnR2eQ==', 'ODUyOTg3NjU0', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 2),
(21, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWI=', 'T3JhbnR2eQ==', 'OTYzOTYzOTYz', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, 1);

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
(1, '{\"ops\":[{\"insert\":\"Well be waiting\"},{\"attributes\":{\"link\":\"https://www.google.com/maps/dir//37.101808,-8.76528\"},\"insert\":\" Here\"},{\"insert\":\"\\n\"}]}', '<p>Well be waiting<a href=\"https://www.google.com/maps/dir//37.101808,-8.76528\" target=\"_blank\"> Here</a></p>', 'Pick info');

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
(10, 18, '09:30,12:30,15:30'),
(11, 19, '10:00');

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
(1, 'im pt', 'im en', 'desc pt', 'desc en', 1, 'aaa8358bb5e15ef1307ef58850629682.jpeg'),
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
(21, 'Segue', 'Segue', 'Segue', 'Segue', 1, '81abf96a8a3c725f4e8132992bbe5c3f.jpeg'),
(22, 'Ma', 'Ma', 'Ma', 'Ma', 0, 'b72ae942195129660e8f1f1a577cc13f.jpeg'),
(23, 'Segue', 'Segue', 'Segue', 'Segue', 0, '67501a0bd015e1d48fe84b4fdae4674f.jpeg'),
(24, 'Segue', 'Segue', 'Segue', 'Segue', 0, '5f74574a59de7d9b05bc3bd1dfe0bc6b.jpeg');

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
('20190210133346', '2019-02-10 13:33:50');

-- --------------------------------------------------------

--
-- Estrutura da tabela `rgpd`
--

CREATE TABLE `rgpd` (
  `id` int(11) NOT NULL,
  `rgpd_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD KEY `IDX_E00CEDDE36D3FBA2` (`available_id`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `blockdate`
--
ALTER TABLE `blockdate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `easytext`
--
ALTER TABLE `easytext`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `rgpd`
--
ALTER TABLE `rgpd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
