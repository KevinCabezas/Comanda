<?php

class Producto
{
  public $id;
  public $codigo;
  public $nombre;
  public $tipo;
  public $stock;
  public $precio;
  public $fecha_creacion;
  // public $empleado;

  public function crearProducto()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO productos (codigo, nombre, tipo, stock, precio, fecha_creacion) 
      VALUES (:codigo, :nombre, :tipo, :stock, :precio, :fecha_creacion)"
    );
    $fecha = new DateTime(date("d-m-Y"));

    $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
    $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
    $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
    $consulta->bindValue(':stock', $this->stock, PDO::PARAM_STR);
    $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
    $consulta->bindValue(':fecha_creacion', date_format($fecha, 'Y-m-d'));
    $consulta->execute();
    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function verificarCodigoProducto()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT codigo FROM productos"
    );
    $consulta->execute();

    //retorna como un objeto generico
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }
  public static function traerIdProducto()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre FROM productos");
    $consulta->execute();

    //retorna como un objeto generico
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function obtenerProducto($producto)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id, codigo, nombre, tipo, stock, precio, fecha_creacion FROM productos WHERE nombre = :nombre"
    );
    $consulta->bindValue(':nombre', $producto, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetchObject('Producto');
  }

  public static function modificarPrecioStock($producto)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE productos 
        SET stock = stock + :stock, precio = COALESCE(:precio, precio) 
        WHERE nombre = :nombre"
    );
    $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
    $consulta->bindValue(':stock', $producto->stock, PDO::PARAM_INT);
    $consulta->bindValue(':precio', is_numeric($producto->precio) ? $producto->precio : null, PDO::PARAM_STR);
    $consulta->execute();
  }

  // MW--------------------------------------
  public static function verificarProducto($nombre, $tipo, $codigo)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * 
      FROM productos pr 
      JOIN tipo_productos tp ON pr.tipo = tp.id
      WHERE pr.nombre = :nombre AND tp.nombre = :tipo AND pr.codigo = :codigo"
    );

    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
      return true;
    }
    return false;
  }

  public static function verificarProductoDos($nombre, $codigo)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * 
      FROM productos 
      WHERE nombre = :nombre  OR codigo = :codigo"
    );

    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
      return true;
    }
    return false;
  }


  public static function verificarProductoTipo($tipo)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * 
      FROM tipo_productos 
      WHERE nombre = :tipo"
    );

    $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
      return true;
    }
    return false;
  }


  public static function verificarProductoPuesto($producto, $puesto)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT * 
        FROM productos 
        WHERE empleado = :puesto AND codigo = :producto"
    );

    $consulta->bindParam(':producto', $producto, PDO::PARAM_INT);
    $consulta->bindParam(':puesto', $puesto, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_OBJ);
  }


  public static function obtnerCodigoproducto($nombreProducto)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo FROM productos WHERE nombre = :nombre");
    $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchColumn();
  }

  public static function obtenerTipoProducto($tipo)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "SELECT id FROM tipo_productos WHERE nombre = :tipo"
    );
    $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchColumn();
  }
}
