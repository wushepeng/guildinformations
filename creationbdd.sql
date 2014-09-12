CREATE TABLE gi_general_config(
RYAPI_APP_KEY varchar(?) NOT NULL PRIMARY KEY,
RYAPI_APP_URL NOT NULL DEFAULT 'http://www.la-firme-matis.com/guildinformations/controleur.php',
RYAPI_APP_MAXAGE integer NOT NULL DEFAULT 0,
GUILDID integer
);

CREATE TABLE gi_perso_api( 
idperso integer PRIMARY KEY,
nomperso varchar(50),
apikey varchar(?),
);
CREATE TABLE gi_guilde_api(
idguilde integer PRIMARY KEY,
nomguilde varchar(50),
apikey varchar(?),
);

CREATE TABLE gi_perso_config(
idperso integer NOT NULL,
codecomp varchar(15) NOT NULL,
visible boolean NOT NULL DEFAULT true
);

