CREATE TABLE productos(
  id INT(11) NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  nombre VARCHAR(250) NOT NULL,
  tipo INT(11) NOT NULL,
  stock INT(11) NOT NULL,
  precio DECIMAL(10,2),
  fecha_creacion DATE NOT NULL,
  fecha_baja DATE DEFAULT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(tipo) REFERENCES tipo_productos(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO productos( codigo, nombre, tipo, stock, precio ) VALUES
('1001', 'daikiry', 4, 50, 900),
('1002', 'mojito', 4, 50, 1000),
('1003', 'corona', 3, 50, 1100),
('1004', 'brahma', 3, 50, 950),
('1005', 'pizza', 1, 50, 1500),
('1006', 'hamburguesa', 1, 50, 1300),
('1007', 'flan', 2, 50, 800),
('1008', 'helado', 2, 50, 800),