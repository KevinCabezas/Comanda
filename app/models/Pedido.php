<?php

class Pedido
{
  public $id;
  public $numeroPedido;
  public $numeroMesa;
  public $estado;
  public $cliente;
  public $hora;
  public $tiempo;
  public $fecha;

  public function crearPedido()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO pedidos (numero_pedido, estado, cliente, numero_mesa, tiempo, hora, fecha) 
      VALUES (:numero_pedido, :estado, :cliente, :numero_mesa, :tiempo, :hora, :fecha)"
    );
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = new DateTime();

    $consulta->bindValue(':numero_pedido', $this->numeroPedido, PDO::PARAM_STR);
    $consulta->bindValue(':numero_mesa', $this->numeroMesa, PDO::PARAM_STR);
    $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
    $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
    $consulta->bindValue(':tiempo', 'indefinido', PDO::PARAM_STR);
    $consulta->bindValue(':hora', date_format($fecha, 'H:i'));
    $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d'));
    $consulta->execute();
    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function crearPedidoDetalles($pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO detalles_pedidos (numero_pedido, cantidad, codigo_producto, tiempo_demora, estado, hora) 
      VALUES (:numero_pedido, :cantidad, :codigo_producto, :tiempo_demora, :estado, :hora)"
    );

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = new DateTime();

    $consulta->bindValue(':numero_pedido', $pedido->numeroPedido, PDO::PARAM_STR);
    $consulta->bindValue(':cantidad', $pedido->cantidad, PDO::PARAM_STR);
    $consulta->bindValue(':codigo_producto', $pedido->codigoProducto, PDO::PARAM_STR);
    $consulta->bindValue(':tiempo_demora', $pedido->tiempo, PDO::PARAM_STR);
    $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
    $consulta->bindValue(':hora', date_format($fecha, 'H:i'));
    $consulta->execute();
    return $objAccesoDatos->obtenerUltimoId();
  }


  // public static function obtnerCodigoproducto($nombreProducto)
  // {
  //   $objAccesoDatos = AccesoDatos::obtenerInstancia();
  //   $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo FROM productos WHERE nombre = :nombre");
  //   $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
  //   $consulta->execute();
  //   return $consulta->fetchColumn();
  // }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT numero_pedido, numero_mesa, estado, cliente, hora,fecha 
        FROM pedidos"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function obtenerPedido($pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT numero_pedido, numero_mesa, estado,tiempo, hora 
        FROM pedidos 
        WHERE numero_pedido = :numero_pedido"
    );
    $consulta->bindValue(':numero_pedido', $pedido, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_OBJ);
  }

  public static function modificarPedido($pedido)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE pedidos 
      SET numeroMesa = :numeroMesa, estado = :estado, codigoProducto = :codigoProducto, cantidad = :cantidad 
      WHERE numeroPedido = :numeroPedido"
    );
    $consulta->bindValue(':numeroPedido', $pedido->numeroPedido, PDO::PARAM_INT);
    $consulta->bindValue(':numeroMesa', $pedido->numeroMesa, PDO::PARAM_STR);
    $consulta->bindValue(':codigoProducto', $pedido->codigoProducto, PDO::PARAM_STR);
    $consulta->bindValue(':cantidad', $pedido->cantidad, PDO::PARAM_STR);
    $consulta->execute();
  }


  public static function crearNumeroPedido()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(numero_pedido) as ultimo_numero FROM pedidos");
    $consulta->execute();
    $resultado = $consulta->fetch(PDO::FETCH_OBJ);

    $nuevoNumero = 1; // Valor por defecto si no hay pedidos

    if ($resultado && isset($resultado->ultimo_numero)) {
      $nuevoNumero = (int)$resultado->ultimo_numero + 1;
    }

    return str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
  }

  //Corrección n° 3
  public static function traerPedidosPendientes($puesto, $estado)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT dp.numero_pedido, dp.estado, dp.cantidad, pr.nombre, tu.puesto
       FROM detalles_pedidos dp
       JOIN productos pr ON dp.codigo_producto = pr.codigo
       JOIN tipo_productos tp ON tp.id = pr.tipo
       JOIN tipo_usuario tu ON tu.id = tp.encargado
       WHERE tu.puesto = :empleado 
       AND dp.estado = :estado"
    );

    $consulta->bindParam(':empleado', $puesto, PDO::PARAM_STR);
    $consulta->bindParam(':estado', $estado, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function modificarPedidoEstado($pedido, $producto, $estado, $tiempo, $puesto)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE detalles_pedidos SET estado = :estado, tiempo_demora = :tiempo_demora
        WHERE numero_pedido = $pedido AND codigo_producto = $producto"
    );

    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    $consulta->bindValue(':tiempo_demora', $tiempo, PDO::PARAM_STR);
    $consulta->execute();
  }

  public static function modificarPedidoEstadoCobro($pedido, $mesa, $estado)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE pedidos SET estado = :estado WHERE numero_pedido = :pedido AND numero_mesa = :mesa"
    );
    $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
    $consulta->bindValue(':mesa', $mesa, PDO::PARAM_INT);
    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    $consulta->execute();
  }

  public static function estadoPedidoCliente($codigo, $pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT p.tiempo, p.estado, p.numero_pedido, p.cliente 
        FROM pedidos p 
        JOIN mesas m ON p.numero_mesa = m.numero 
        WHERE p.numero_pedido = :pedido AND m.codigo = :codigo"
    );

    $consulta->bindParam(':pedido', $pedido);
    $consulta->bindParam(':codigo', $codigo);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }


  public static function traerCuentaPedidoDetallada($numeroMesa, $numeroPedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT 
            pd.numero_pedido, 
            pr.nombre as nombre_producto,
            pd.cantidad, 
            p.numero_mesa,
            (pd.cantidad * pr.precio) as total
        FROM detalles_pedidos pd
        INNER JOIN pedidos p ON pd.numero_pedido = p.numero_pedido
        INNER JOIN productos pr ON pd.codigo_producto = pr.codigo
        WHERE p.numero_mesa = :numero_mesa AND pd.numero_pedido = :numero_pedido"
    );
    $consulta->bindValue(':numero_mesa', $numeroMesa, PDO::PARAM_INT);
    $consulta->bindValue(':numero_pedido', $numeroPedido, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function traerCuentaPedidoTotal($estado)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT 
                p.numero_mesa,
                pd.numero_pedido,
                p.estado,
                SUM(pd.cantidad * pr.precio) as total
            FROM detalles_pedidos pd
            INNER JOIN pedidos p ON pd.numero_pedido = p.numero_pedido
            INNER JOIN productos pr ON pd.codigo_producto = pr.codigo
            WHERE p.estado = :estado
            GROUP BY p.numero_mesa, pd.numero_pedido"
    );

    $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function verificarEstadosPedido($pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT numero_pedido, codigo_producto, tiempo_demora, estado 
        FROM detalles_pedidos
        WHERE numero_pedido = :pedido"
    );
    $consulta->bindParam(':pedido', $pedido, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }


  public static function modificarPedidoEstadoPricipal($pedido, $estado, $tiempo)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE pedidos SET estado = :estado, tiempo = :tiempo
        WHERE numero_pedido = $pedido"
    );

    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
    $consulta->execute();
  }

  public static function verificarEstadostiempo($pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT MAX(TIME_TO_SEC(tiempo_demora)) AS maxTiempoDemora 
        FROM detalles_pedidos
        WHERE numero_pedido = :pedido
        AND tiempo_demora != 'indefinido'"
    );
    $consulta->bindParam(':pedido', $pedido, PDO::PARAM_STR);
    $consulta->execute();

    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
      $maxTiempoDemora = gmdate("H:i", $resultado['maxTiempoDemora']);
    } else {
      $maxTiempoDemora = 'indefinido';
    }

    return $maxTiempoDemora;
  }

  public static function traerTodosPedidos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT numero_pedido, estado, numero_mesa
        FROM pedidos"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function crearEncuesta($encuesta)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO encuesta (numeroPedido, numeroMesa, mesa, restaurante, mozo, cocinero, comentario) 
        VALUES (:numeroPedido, :numeroMesa, :mesa, :restaurante, :mozo, :cocinero, :comentario)"
    );

    $consulta->bindValue(':numeroPedido', $encuesta->numeroPedido, PDO::PARAM_STR);
    $consulta->bindValue(':numeroMesa', $encuesta->numeroMesa, PDO::PARAM_INT);
    $consulta->bindValue(':mesa', $encuesta->mesa, PDO::PARAM_INT);
    $consulta->bindValue(':restaurante', $encuesta->restaurante, PDO::PARAM_INT);
    $consulta->bindValue(':mozo', $encuesta->mozo, PDO::PARAM_INT);
    $consulta->bindValue(':cocinero', $encuesta->cocinero, PDO::PARAM_STR);
    $consulta->bindValue(':comentario', $encuesta->comentario, PDO::PARAM_STR);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function mejorEncuesta()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT comentario, ROUND((mesa + restaurante + mozo + cocinero) / 4, 2) AS puntuacion
        FROM encuesta
        ORDER BY puntuacion DESC
        LIMIT 2"
    );

    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_OBJ);
  }

  public static function actualizarEstadoPedido($numeroPedido)
  {
    $lista = Pedido::verificarEstadosPedido($numeroPedido);

    $contPreparacion = 0;
    $contListo = 0;
    $pendiente = 0;


    foreach ($lista as $pedido) {
      if ($pedido->estado == "preparacion") {
        $contPreparacion += 1;
      } else if ($pedido->estado == "listo") {
        $contListo += 1;
      } else {
        $pendiente += 1;
      }
    }
    echo $contPreparacion;
    if ($contPreparacion == 0 && $pendiente == 0 && $contListo > 0) {
      $tiempo = self::verificarEstadostiempo($numeroPedido);
      echo $tiempo;
      self::modificarPedidoEstadoPricipal($numeroPedido, 'listo', $tiempo);
    } else if ($pendiente == 0 && $contListo == 0 && $contPreparacion > 0) {
      $tiempo = self::verificarEstadostiempo($numeroPedido);
      $partes = explode(':', $tiempo);
      $minutos = $partes[1] . " min";

      self::modificarPedidoEstadoPricipal($numeroPedido, 'preparacion', $minutos);
    }
  }

  public static function formatTiempo($horaString)
  {
    list($hora, $minutes) = explode(':', $horaString);
    return (int)$hora * 60 + (int)$minutes;
  }

  public static function calcularTiempo($numero)
  {
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $hora = new DateTime();
    $horaActual = $hora->format('H:i');

    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT tiempo, hora FROM pedidos
       WHERE numero_pedido = :numero_pedido"
    );
    $consulta->bindValue(':numero_pedido', $numero, PDO::PARAM_STR);
    $consulta->execute();
    $objeto = $consulta->fetch(PDO::FETCH_OBJ);

    $horaActualMin = self::formatTiempo($horaActual);
    $horaPedidoMin = self::formatTiempo($objeto->hora);

    $string = $objeto->tiempo;
    $tiempoMin = (int) str_replace(' min', '', $string);

    $tiempoRestante = ($tiempoMin + 5) - ($horaActualMin - $horaPedidoMin);

    if ($tiempoRestante > 0) {
      $estado = $tiempoRestante . " min";
    } else {
      $estado = "retrazado";
    }

    self::modificarPedidoEstadoPricipal($numero, "preparacion", $estado);
  }

  
}
