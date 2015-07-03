DROP DATABASE IF EXISTS `guildInf_test`;
CREATE DATABASE `guildInf_test`;

USE `guildInf_test`;

DROP TABLE IF EXISTS `guildInf_test`.`gi_GeneralConfig`;
CREATE TABLE `guildInf_test`.`gi_GeneralConfig` (
  `RYAPI_APP_KEY` varchar(41) NOT NULL,
  `RYAPI_APP_URL` text NOT NULL,
  `RYAPI_APP_MAXAGE` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `RYAPI_APP_KEY` (`RYAPI_APP_KEY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `guildInf_test`.`gi_Guilds`;
CREATE TABLE `guildInf_test`.`gi_Guilds` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `apiKey` varchar(41) DEFAULT NULL,
  `mainGuildId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `guildInf_test`.`gi_Homins`;
CREATE TABLE `guildInf_test`.`gi_Homins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `apiKey` varchar(41) DEFAULT NULL,
  `guildId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `guildInf_test`.`gi_GeneralConfig`
VALUES ('_APPKEY_', '_APPURL_', 0);

INSERT INTO `guildInf_test`.`gi_Guilds` (`id`, `name`, `apiKey`, `mainGuildId`)
SELECT `g`.`idguilde`, `g`.`nomguilde`, `g`.`apikey`, `g`.`mainguild`
FROM `competences`.`gi_guilde_api` AS `g`
WHERE `g`.`idguilde` > 0;

INSERT INTO `guildInf_test`.`gi_Homins` (`id`, `name`, `apiKey`, `guildId`)
SELECT `p`.`idperso`, `p`.`nomperso`, `p`.`apikey`, `p`.`mainguild`
FROM `competences`.`gi_perso_api` AS `p`
WHERE `p`.`idperso` > 0;

GRANT USAGE ON *.* TO 'guildInfAdmin'@'localhost'
/* Mysql n'offre pas la possibilité de vérifier si un utilisateur existe ou non
 * Le grant usage va le créer si jamais il n'existe pas, seule façon d'éviter une erreur à cette ligne lors d'une deuxième exécution du script */
DROP USER 'guildInfAdmin'@'localhost';
CREATE USER 'guildInfAdmin'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON `guildInf_test`.* TO 'guildInfAdmin'@'localhost';
FLUSH PRIVILEGES;
