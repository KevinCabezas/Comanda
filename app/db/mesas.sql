
CREATE TABLE mesas (
    id int(11) NOT NULL AUTO_INCREMENT,
    numero int(11) NOT NULL UNIQUE, 
    estado varchar(250) NOT NULL,
    codigo varchar(5) NOT NULL,
    usos varchar(250) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
