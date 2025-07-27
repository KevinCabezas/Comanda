
CREATE TABLE pedidos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    numero_pedido INT(11) NOT NULL UNIQUE,
    estado VARCHAR(250) NOT NULL,
    cliente VARCHAR(250) NOT NULL,
    numero_mesa INT(11) NOT NULL,  -- Cambiado el nombre para reflejar que referencia id
    tiempo VARCHAR(250) NOT NULL,
    hora TIME NOT NULL,  -- Cambiado a tipo TIME
    fecha DATE NOT NULL,  -- Cambiado a tipo DATE
    PRIMARY KEY (id),
    FOREIGN KEY (numero_mesa) REFERENCES mesas(numero) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

