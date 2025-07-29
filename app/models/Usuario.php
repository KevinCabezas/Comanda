<?php

class Usuario
{
  public $id;
  public $puesto;
  public $nombre;
  public $apellido;
  public $clave;
  public $fecha_ingreso;
  public $fecha_baja;

  public function crearUsuario()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO usuarios (puesto, nombre, apellido, clave, fecha_ingreso) 
       VALUES (:puesto, :nombre, :apellido, :clave, :fecha_ingreso)"
    );
    $fecha = new DateTime(date("d-m-Y"));

    $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
    $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
    $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
    $consulta->bindValue(':fecha_ingreso', date_format($fecha, 'Y-m-d'));
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtnerPuestoId($nombrePuesto)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM tipo_usuario WHERE puesto = :puesto");
    $consulta->bindValue(':puesto', $nombrePuesto, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchColumn();
  }

  public static function traerTipoUsuario()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, puesto FROM tipo_usuario");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id, puesto, nombre, apellido, clave, fecha_ingreso, fecha_baja FROM usuarios"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
  }


  public static function obtenerUsuario($usuario)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id, puesto, nombre, apellido, clave, fecha_ingreso, fecha_baja FROM usuarios WHERE id = :id"
    );
    $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetchObject('Usuario');
  }

  public static function modificarUsuario($user)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE usuarios SET puesto = :puesto, nombre = :nombre, apellido = :apellido, clave = :clave WHERE id = :id"
    );
    // $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET puesto = :puesto WHERE id = :id");
    $consulta->bindValue(':id', $user->id, PDO::PARAM_INT);
    $consulta->bindValue(':puesto', $user->puesto, PDO::PARAM_STR);
    $consulta->bindValue(':nombre', $user->nombre, PDO::PARAM_STR);
    $consulta->bindValue(':apellido', $user->apellido, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $user->clave, PDO::PARAM_STR);
    $consulta->execute();
  }

  public static function borrarUsuario($usuario)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja WHERE id = :id");
    $fecha = new DateTime(date("d-m-Y"));
    $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d'));
    $consulta->execute();
  }

  public static function verificarUsuario($nombre, $clave)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT u.nombre, u.apellido, t.puesto 
        FROM usuarios u 
        JOIN tipo_usuario t ON t.id = u.puesto
        WHERE u.nombre = :nombre 
        AND u.clave = :clave"
    );
    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetch(PDO::FETCH_OBJ);
  }

  public static function registroLogin($usuarioId)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO registro_logins (usuario, fecha, hora)
      VALUES (:id, :fecha, :hora)"
    );

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = new DateTime();
    $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d'));
    $consulta->bindValue(':hora', date_format($fecha, 'H:i'));
    $consulta->execute();
    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtnerId($nombre, $clave)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id FROM usuarios WHERE nombre = :nombre AND clave = :clave"
    );
    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $clave, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetchColumn();
  }


  // Corrección n° 20
  public static function obtenerRegistro($nombre, $apellido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT u.nombre, u.apellido, rl.fecha, rl.hora
      FROM registro_logins rl
      JOIN usuarios u ON u.id = rl.usuario
      WHERE u.nombre = :nombre AND u.apellido = :apellido"
    );
    $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject();
  }
}
