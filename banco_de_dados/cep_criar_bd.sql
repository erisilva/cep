DROP DATABASE if exists `cep`;

/*DEFINA O BANCO DE DADOS A SER CRIADO PELO SCRIPT*/
CREATE DATABASE `cep` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `cep`;


--
-- Table structure for table `ac`
--

DROP TABLE IF EXISTS `cep`;
CREATE TABLE `cep` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cep` varchar(9) NOT NULL,
  `cidade` varchar(180) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `bairro` varchar(180) DEFAULT NULL, 
  `uf` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;