

CREATE TABLE usuarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(250) NOT NULL,
  apellido varchar(250)  NOT NULL,
  puesto int(11) NOT NULL,
  clave varchar(250) NOT NULL,
  fecha_ingreso date NOT NULL,
  fecha_baja date DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (puesto) REFERENCES tipo_usuario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
