CREATE TABLE encuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeroPedido VARCHAR(5) NOT NULL,
    numeroMesa INT(11) NOT NULL,
    mesa INT(11) NOT NULL,
    restaurante INT(11) NOT NULL,
    mozo INT(11) NOT NULL,
    cocinero INT(11) NOT NULL,
    comentario VARCHAR(66)
);