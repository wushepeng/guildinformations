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

/* Dans l'ancienne base de donnés le champ nom, pour les guildes et les homins, peut être à null
 * Si c'est le cas, on ajoute un nom par défaut, le nom étant mis à jour par la nouvelle application
 */
UPDATE `competences2`.`gi_guilde_api`
SET `nomguilde` = "theNameWillBeUpdatedByTheApp"
WHERE `nomguilde` IS NULL;

UPDATE `competences2`.`gi_perso_api`
SET `nomperso` = "theNameWillBeUpdatedByTheApp"
WHERE `nomperso` IS NULL;

INSERT INTO `gi_GeneralConfig`
VALUES ('_APPKEY_', '_APPURL_', 0);

INSERT INTO `gi_Guilds` (`id`, `name`, `apiKey`, `mainGuildId`)
SELECT `g`.`idguilde`, `g`.`nomguilde`, `g`.`apikey`, `g`.`mainguild`
FROM `competences2`.`gi_guilde_api` AS `g`
WHERE `g`.`idguilde` > 0;

INSERT INTO `gi_Homins` (`id`, `name`, `apiKey`, `guildId`)
SELECT `p`.`idperso`, `p`.`nomperso`, `p`.`apikey`, `p`.`mainguild`
FROM `competences2`.`gi_perso_api` AS `p`
WHERE `p`.`idperso` > 0;
