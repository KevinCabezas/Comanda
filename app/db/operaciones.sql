CREATE TABLE operaciones (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario INT(11) NOT NULL UNIQUE,
    cantidad INT(11) NOT NULL,
    hora  TIME NOT NULL,
    fecha DATE NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;