CREATE TABLE tipo_productos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(250) NOT NULL,
  encargado INT(11) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (encargado) REFERENCES tipo_usuario(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tipo_productos(nombre) VALUES 
('comida'),
('postre'),
('trago'),
('cerveza');
