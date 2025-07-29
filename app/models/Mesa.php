<?php

class Mesa
{
  public $id;
  public $numero;
  public $estado;
  public $codigo;
  public $usos;

  public function crearMesa()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (numero, estado, codigo, usos) VALUES (:numero, :estado, :codigo, :usos)");
    $codigoUnico = self::generarCodigo(5);

    $consulta->bindValue(':numero', $this->numero, PDO::PARAM_STR);
    $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
    $consulta->bindValue(':codigo', $codigoUnico);
    $consulta->bindValue(':usos', 0);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT numero, estado FROM mesas");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function obtenerMesa($mesa, $codigo = null)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * FROM mesas 
        WHERE numero = :numero 
        OR codigo = :codigo"
    );
    $consulta->bindValue(':numero', $mesa, PDO::PARAM_INT);
    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject('Mesa');
  }

  public static function modificarMesa($mesa)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado, usos = usos + :usos WHERE numero = :numero");
    $consulta->bindValue(':numero', $mesa->numero, PDO::PARAM_STR);
    $consulta->bindValue(':estado', $mesa->estado, PDO::PARAM_STR);
    $consulta->bindValue(':usos', $mesa->usos, PDO::PARAM_INT);
    $consulta->execute();
  }

  public static function obtenerMesaUsos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * FROM mesas ORDER BY usos DESC LIMIT 1"
    );

    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_OBJ);
    // return $consulta->fetchObject('Mesa');
  }

  public static function generarCodigo($caracteres)
  {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $caracteres);
  }


  // Corrección n°: 21

  public static function listaMesaFacturacion()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT p.numero_mesa, SUM(dp.cantidad * pr.precio) AS total_factura
      FROM pedidos p
      JOIN detalles_pedidos dp ON p.numero_pedido = dp.numero_pedido
      JOIN productos pr ON dp.codigo_producto = pr.codigo
      GROUP BY p.numero_pedido, p.numero_mesa
      ORDER BY total_factura ASC"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }


  // Corrección n°: 22

  public static function mesaFacturacionFecha($mesa, $fechaUno, $fechaDos)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT p.numero_mesa, SUM(dp.cantidad * pr.precio) AS total_facturado
      FROM pedidos p
      JOIN detalles_pedidos dp ON p.numero_pedido = dp.numero_pedido
      JOIN productos pr ON dp.codigo_producto = pr.codigo
      WHERE p.numero_mesa = :mesa AND p.fecha BETWEEN :fechaUno AND :fechaDos
      GROUP BY p.numero_mesa"
    );
    $consulta->bindValue(':mesa', $mesa, PDO::PARAM_INT);
    $consulta->bindValue(':fechaUno', $fechaUno, PDO::PARAM_STR);
    $consulta->bindValue(':fechaDos', $fechaDos, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }
}
