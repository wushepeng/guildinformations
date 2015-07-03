DROP TABLE IF EXISTS `gi_GeneralConfig`;
CREATE TABLE `gi_GeneralConfig` (
  `RYAPI_APP_KEY` varchar(41) NOT NULL,
  `RYAPI_APP_URL` text NOT NULL,
  `RYAPI_APP_MAXAGE` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `RYAPI_APP_KEY` (`RYAPI_APP_KEY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `gi_Guilds`;
CREATE TABLE `gi_Guilds` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `apiKey` varchar(41) DEFAULT NULL,
  `mainGuildId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `gi_Homins`;
CREATE TABLE `gi_Homins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `apiKey` varchar(41) DEFAULT NULL,
  `guildId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE `competences2`.`gi_guilde_api`
SET `nomguilde` = "theNameWillBeUpdatedByTheApp"
WHERE `nomguilde` == NULL;

UPDATE `competences2`.`gi_perso_api`
SET `nomperso` = "theNameWillBeUpdatedByTheApp"
WHERE `nomperso` == NULL;

INSERT INTO `gi_GeneralConfig`
VALUES ('_APPKEY_', '_APPURL_', 0);

INSERT INTO `gi_Guilds` (`id`, `name`, `apiKey`, `mainGuildId`)
SELECT `g`.`idguilde` AS `id`, `g`.`nomguilde` AS `name`, `g`.`apikey` AS `apiKey`, `g`.`mainguild` AS `mainGuildId`
FROM `competences2`.`gi_guilde_api` AS `g`
WHERE `g`.`idguilde` != NULL;

INSERT INTO `gi_Homins` (`id`, `name`, `apiKey`, `guildId`)
SELECT `p`.`idperso` AS `id`, `p`.`nomperso` AS `name`, `p`.`apikey` AS `apiKey`, `p`.`mainguild` AS `guildId`
FROM `competences2`.`gi_perso_api` AS `p`
WHERE `p`.`idperso` != NULL;
