CREATE TABLE gi_general_config(
RYAPI_APP_KEY char(41) NOT NULL,
RYAPI_APP_URL text NOT NULL,
RYAPI_APP_MAXAGE integer NOT NULL DEFAULT 0,
PRIMARY KEY(RYAPI_APP_KEY)
);

CREATE TABLE gi_guilde_api(
idguilde integer PRIMARY KEY,
nomguilde varchar(50),
apikey varchar(41),
mainguild integer NOT NULL,
FOREIGN KEY (mainguild) REFERENCES gi_guilde_api(idguilde)
) ENGINE=INNODB;

CREATE TABLE gi_perso_api( 
idperso integer PRIMARY KEY,
nomperso varchar(50),
apikey varchar(41),
mainguild integer NOT NULL,
FOREIGN KEY (mainguild) REFERENCES gi_guilde_api(idguilde)
) ENGINE=INNODB;

CREATE TABLE gi_perso_config(
idperso integer NOT NULL,
codecomp varchar(15) NOT NULL,
visible boolean NOT NULL DEFAULT true
);
