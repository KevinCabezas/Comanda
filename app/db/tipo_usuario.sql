CREATE TABLE tipo_usuario (
    id INT(11) NOT NULL AUTO_INCREMENT,
    puesto VARCHAR(250) NOT NULL,
    PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tipo_usuario (puesto) VALUES
('socio'),
('bartender'),
('cocinero'),
('cervecero'),
('mozo'),
('pastelero');


