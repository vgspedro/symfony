-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02-Fev-2019 às 14:39
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
-- Estrutura da tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 15, '15/02/2019', 1),
(2, 16, '15/02/2019', 1),
(3, 17, '15/02/2019', 1),
(4, 18, '15/02/2019', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `adult` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL,
  `baby` int(11) DEFAULT NULL,
  `date` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hour` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tourtype` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `booking`
--

INSERT INTO `booking` (`id`, `adult`, `children`, `baby`, `date`, `hour`, `tourtype`, `message`, `posted_at`, `notes`, `status`) VALUES
(20, 7, 3, 4, '2018-06-17', '12:45', '15', '5', '2018-06-03 14:48:55', NULL, 'CONFIRMED'),
(21, 4, 4, 4, '2018-06-20', '12:45', '15', '4', '2018-06-03 15:26:09', NULL, 'PENDING'),
(22, 3, 3, 3, '2018-06-20', '09:25', '16', 'mensagem', '2018-06-05 20:47:25', NULL, 'PENDING'),
(23, 5, 2, 1, '2018-06-17', '10:30', '17', 'ff', '2018-06-08 22:43:47', NULL, 'PENDING'),
(24, 3, 2, 1, '2018-06-18', '10:15', '16', 'rft', '2018-06-08 22:44:40', NULL, 'PENDING'),
(25, 2, 2, 2, '2018-06-18', '09:25', '16', '222', '2018-06-09 12:41:08', NULL, 'CANCELED'),
(26, 4, 1, 0, '2018-06-17', '10:15', '16', 'ok', '2018-06-10 18:39:51', NULL, 'PENDING'),
(27, 3, 1, 0, '2018-06-19', '10:15', '16', 'a minha mensagem', '2018-06-10 19:25:51', NULL, 'PENDING'),
(28, 2, 1, 0, '2018-07-05', '13:30', '16', 'teste', '2018-06-16 07:25:20', NULL, 'PENDING'),
(29, 2, 0, 0, '2018-06-26', '08:30', '15', 'teste', '2018-06-16 13:47:42', NULL, 'PENDING');

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
  `children_price` decimal(10,2) NOT NULL,
  `adult_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `category`
--

INSERT INTO `category` (`id`, `name_pt`, `name_en`, `description_pt`, `description_en`, `children_price`, `adult_price`, `is_active`, `image`) VALUES
(15, 'Visita Tradicional - 1 Hora 15 Min.', 'Traditional Visit - 1 Hour 15 Min.', 'Visite entre 15/20 grutas, algares e praias desertas. Inclui uma paragem de 10 min para mergulho, quando as condições do mar permitirem. O mergulho pode ser dado numa gruta ou numa praia deserta.\r\n(Bébés grátis até 23 meses.)', 'Visit about 15/20 caves. If we have good sea conditions, a 10 minute refreshing dive along the shore.\r\n(Free Babies till 23 months.)', '15.00', '25.00', 1, 'c1de35fcca54082b1874dbd824550143.jpeg'),
(16, 'Aluguer Kayak - 45 Min.', 'Kayak Rental - 45 Min.', 'Aluguer livre não acompanhado por guia e não autorizado a crianças menores de 5 anos.', 'Rental, no guide, only for children more than 5 years old.', '0.00', '15.00', 1, '58ec7e3faedc9095005c1769f5294676.jpeg'),
(17, 'Visita a partir de Portimão +- 1 Hora 30 Min.', 'Boarding in marina of Portimão +- 1 Hour 30 Min.', 'O passeio começa a partir da Marina de Portimão e também visitamos as grutas de Benagil, Carvoeiro, Carvalho e Marinha. É semelhante à nossa visita tradicional. Também é possível parar 10 minutos para um mergulho refrescante - com a mesma equipa da praia (Taruga). (Bébés grátis até 23 meses.)', 'The tour starts from Marina of Portimão and we also visit the Benagil sea caves, Carvoeiro, Carvalho and Marinha beach. It´s similar from our tour number 2. Also possible to stop 10 minutes for a refreshing dive - with the same team of the beach (Taruga). (Free Babies till 23 months.)', '15.00', '25.00', 1, '577ec312a2cbcd3d317a05cbd1e24fec.jpeg'),
(18, 'Visita Express - 30 Min.', 'Express Visit - 30 Min.', 'Visite cerca de 8 grutas incluindo: Algar de Benagil, Praia da Marinha. (Bébés grátis até 23 meses.)', 'Express Visit - 30 Min. - Visit about 8 caves, included: Algar de Benagil, Marinha beach. (Free Babies till 23 months.)', '10.00', '15.00', 1, '74d8433950d0c9dd324a5e2806aa2d6c.jpeg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `rgpd` tinyint(1) NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `client`
--

INSERT INTO `client` (`id`, `booking_id`, `email`, `password`, `username`, `address`, `telephone`, `roles`, `rgpd`, `language`) VALUES
(12, 20, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(13, 21, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5mMg==', 'RWhuIFB2ZWhldHbDo2IgMjgy', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(14, 22, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(15, 23, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(16, 24, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(17, 25, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(18, 26, 'aXRmY3JxZWJAdHpudnkucGJ6', '', 'Q3JxZWIgSXZydG5m', 'RWhuIFB2ZWhldHbDo2IgMjg=', 'OTI2NjQ3NzU2', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(19, 27, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWI=', 'RWhuIDI1IE5vZXZ5LCAzNCBZbnRiZiA=', 'OTg3NjU0MzIx', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(20, 28, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWIgaQ==', 'T3JhbnR2eQ==', 'ODUyOTg3NjU0', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, ''),
(21, 29, 'aXRmY3JxZWIxNUBmbmNiLmNn', '', 'Y3JxZWI=', 'T3JhbnR2eQ==', 'OTYzOTYzOTYz', 'a:1:{i:0;s:11:\"ROLE_CLIENT\";}', 0, '');

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
(1, 15, '08:30,15:30,16:45'),
(2, 16, '05:30'),
(3, 17, '10:30,11:30'),
(10, 18, '20:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `image`
--

CREATE TABLE `image` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `filename` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `migration_versions`
--

INSERT INTO `migration_versions` (`version`) VALUES
('20190201203509');

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
(10, 'Avisos em PT ... 13', 'Warning EN .....', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_880E0D76E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_880E0D76F85E0677` (`username`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `UNIQ_C74404553301C60` (`booking_id`);

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
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C53D045F12469DE2` (`category_id`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blockdate`
--
ALTER TABLE `blockdate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `easytext`
--
ALTER TABLE `easytext`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
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
-- Limitadores para a tabela `blockdate`
--
ALTER TABLE `blockdate`
  ADD CONSTRAINT `FK_857EE7BA12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Limitadores para a tabela `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C74404553301C60` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`);

--
-- Limitadores para a tabela `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Limitadores para a tabela `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045F12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
