
CREATE TABLE detalles_pedidos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  numero_pedido INT(11) NOT NULL,
  cantidad INT(11) NOT NULL,
  codigo_producto VARCHAR(50) NOT NULL,
  tiempo_demora VARCHAR(255) NOT NULL,
  estado VARCHAR(255) NOT NULL,
  hora TIME NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (codigo_producto) REFERENCES productos(codigo),
  FOREIGN KEY (numero_pedido) REFERENCES pedidos(numero_pedido) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
