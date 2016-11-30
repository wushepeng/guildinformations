#Informations de guilde

Ce projet consiste en une application destinée aux guildes pour le jeu [Ryzom](http://www.ryzom.com).

Les fonctionnalités proposées sont:
- [x] Stockage des clés API en base de données
- [x] Affichage du contenu des Hall de Guilde
- [x] Tri et recherches dans les inventaires
- [x] Listes des compétences des membres

##Mettre en place le projet

Premièrement, vous aurez besoin de [Composer](https://getcomposer.org/download/).

```
curl -sS https://getcomposer.org/installer | php
```

Téléchargez le présent repository, ouvrez un terminal dans le dossier et installez le projet en exécutant la commande

```
php composer.phar install
```

Enfin, il faut mettre en place un nouveau *VirtualHost* Apache.

```
sudo vim /etc/apache2/sites-available/fileName
```

```
<VirtualHost *:80>
    ServerAdmin mail@domain.com
        DocumentRoot "/path/to/directory/public"
        ServerName domain.com
        ServerAlias www.domain.com

        <Directory "/path/to/directory/public">
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>
</VirtualHost>
```

```
sudo a2ensite fileName
```

L'application est à présent disponible en [local](http://localhost).

De plus pour le bon fonctionnement, vous avez besoin d'une base de données mysql active. Vous pouvez installer [phpmyadmin](http://doc.ubuntu-fr.org/phpmyadmin) pour gérer la base de données par exemple.

Une fois que vous avez installé votre gestionnaire de base de données préféré, créez une nouvelle base de données et exécutez le script suivant pour créer les différentes tables.

```sql
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
```

Avant d'aller plus loin il vous faut vous connecter sur l'application [Ryzom](http://app.ryzom.com), et ajouter une nouvelle app dans l'AppZone. Entrez un nom, indiquez l'url `http://127.0.0.1/ryzom/app`, copiez la clé api et enregistrez.

Enfin, revenez sur la base de données et initialisez la configuration générale:

```sql
INSERT INTO `gi_GeneralConfig` (
`RYAPI_APP_KEY` ,
`RYAPI_APP_URL` ,
`RYAPI_APP_MAXAGE`
)
VALUES (
'yourAppKey', 'yourAppUrl/ryzom/app', '0'
);
```

Une fois que vous avez votre base de données, il reste une dernière étape, à savoir, configurer la connexion de l'application à la base de données. Pour cela, il suffit de changer les informations de connexion dans le fichier `App/AbstractResource.php:38`.

```php
$connectionOptions = array(
	'driver'   => 'pdo_mysql',
	'host'     => '...',
	'dbname'   => '...',
	'user'     => '...',
	'password' => '...'
);
```

Enjoy.

##Je comprends rien à ton code!

*Quelques explications s'imposent...*

###Arborescence du projet

* guildinformations
  * `App/`: php de l'application
    * `Entity/`: définition des objets, avec mapping pour l'ORM
      * `GeneralConfig.php`: objet encapsulant les configurations de l'application
      * `Guild.php`: objet représentant une guilde
      * `Homin.php`: objet représentant un homin
    * `Repository/`: les requêtes sql customs sur les entités sont définies dans ce dossier
    * `Resource/`: CRUD pour chaque entité
    * `TwigExtension/`: fonctions personnelles utilisées dans Twig
    * `AbstractResource.php`: connexion de l'ORM à la base de données
    * `Logger.php`: classe définissant le logger de l'application
    * `Router.php`: définition des routes de l'application
    * `Utilities.php`: fonctions utiles
  * `lang/`: contient tous les textes utilisés dans l'application, pour chaque langage supporté
  * `libs/`: les différentes librairies utilisées sont dans ce dossier
  * `logs/`: les logs seront écrits dans ce dossier
  * `public/`: dossier racine du site web
    * `css/`: ici les feuilles de style css
    * `images/`: ici les images
    * `.htaccess`: règles pour une application Slim
    * `index.php`: point d'entrée
  * `templates/`: contient toutes les vues de l'application
    * `cache/`: ce dossier est utilisé par le moteur de templates (requiert les droits d'écriture)
    * `ingame/`: on trouve ici les vues utilisées pour l'affichage dans le jeu
    * `*.html.twig`: les vues pour l'affichage depuis le navigateur web
  * `LICENSE`: license
  * `README.md`: le présent fichier
  * `composer.json`: les  dépendances du projet
  * `migrate-database-v1-to-v2.sql`: script à utiliser pour migrer la base de données de la version 1 à la v2

###Documentations

L'application utilise [SlimPHP](http://docs.slimframework.com/) pour servir le contenu (routing, middleware).

L'ORM utilisé est [Doctrine](http://doctrine-orm.readthedocs.org/en/latest/) pour gérer la persistance des données.

Le moteur de template pour générer les vues est [Twig](http://twig.sensiolabs.org/documentation).