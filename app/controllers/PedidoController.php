<?php
require_once '../models/Pedido.php';
require_once '../models/Producto.php';
require_once '../interfaces/IApiUsable.php';
// require_once '../db/archivos.php';
require_once '../utils/AutentificadorJWT.php';
require_once '../controllers/ProductoController.php';

class PedidoController extends Pedido
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $numeroMesa = $parametros['mesa'];
    $productos = $parametros['producto'];
    $cliente = $parametros['cliente'];

    // var_dump($productos);
    $numeroPedido = Pedido::crearNumeroPedido();
    $pedido = new Pedido();
    $pedido->numeroPedido = $numeroPedido;
    $pedido->estado = 'pendiente';
    $pedido->cliente = $cliente;
    $pedido->numeroMesa = $numeroMesa;
    $pedido->crearPedido();


    foreach ($productos as $producto => $cantidad) {
      $codigoProducto = Producto::obtnerCodigoproducto($producto);
      $detalles = new stdClass();
      $detalles->numeroPedido = $numeroPedido;
      $detalles->cantidad = $cantidad;
      $detalles->codigoProducto = $codigoProducto;
      $detalles->tiempo = "indefinido";
      $detalles->estado = 'pendiente';
      Pedido::crearPedidoDetalles($detalles);
    }
    $mesa = new Mesa();
    $mesa->numero = $numeroMesa;
    $mesa->estado = 'esperando';
    Mesa::modificarMesa($mesa);

    $mesa = Mesa::obtenerMesa($numeroMesa);
    $payload = json_encode(array("Numero Pedido" => $numeroPedido, "Codigo" => $mesa->codigo));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // cosnulta-sociopedido demora-------------------------------

  public function TraerUno($request, $response, $args)
  {
    $usr = $args['numeroPedido'];
    Pedido::actualizarEstadoPedido($usr);
    Pedido::calcularTiempo($usr);

    $usuario = Pedido::obtenerPedido($usr);
    $payload = json_encode($usuario);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("lista" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // CONSULTA PEDIDO CLIENTE---------------------------------------

  public function pedidoCliente($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $codigoMesa = $parametros['codigoMesa'];
    Pedido::actualizarEstadoPedido($numeroPedido);
    Pedido::calcularTiempo($numeroPedido);

    $lista = Pedido::estadoPedidoCliente($codigoMesa, $numeroPedido);

    $payload = json_encode(array("listaPedido" => $lista));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  // Consultar Pendientes detalles Empleados---------------------------

  public function pendientesPedidos($request, $response, $args)
  {
    // $parametros = $request->getParsedBody();
    // $estado = $parametros['estado'];

    // Obtener parámetro de la URL (Query Param)
    $queryParams = $request->getQueryParams();
    $estado = $queryParams['estado'] ?? null;

    $token = self::obtenerToken($request);
    $datos = AutentificadorJWT::ObtenerData($token);
    $puestoEmpleado = $datos->puesto;


    $lista = Pedido::traerPedidosPendientes($puestoEmpleado, $estado);
    $payload = json_encode(array("$estado" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // cambiar estado --> empleados------------------------

  public function actualizarEstadosPedidos($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $producto = $parametros['producto'];
    $estado = $parametros['estado'];
    $tiempo = $parametros['tiempo'];

    $codigoProducto = Producto::obtnerCodigoproducto($producto);
    Pedido::modificarPedidoEstado($numeroPedido, $codigoProducto, $estado, $tiempo);
    Pedido::actualizarEstadoPedido($numeroPedido);

    $payload = json_encode(array("mensaje" => "Pedido->$estado"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // ----- consultar pedidos-principal Mozo ----------------------

  public function consultarPedidoMozo($request, $response, $args)
  {
    $lista = Pedido::traerTodosPedidos();
    $payload = json_encode(array("Lista" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function cambiarEstadoPedidoMozo($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $numeroMesa = $parametros['numeroMesa'];
    $estadoMesa = $parametros['estadoMesa'];
    $estadoPedido = $parametros['estadoPedido'];

    $mesa = new Mesa();
    $mesa->numero = $numeroMesa;
    $mesa->estado = $estadoMesa;
    Mesa::modificarMesa($mesa);
    Pedido::modificarPedidoEstadoPricipal($numeroPedido, $estadoPedido, 'terminado');
    $payload = json_encode(array("Mensaje" => 'Pedido actualizado'));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // cuenta cobrar pedido con detalles-------------------------

  public function consultarPedidoDetallado($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $numeroMesa = $parametros['numeroMesa'];

    $cuenta = Pedido::traerCuentaPedidoDetallada($numeroMesa, $numeroPedido);
    $payload = json_encode(array("Cuenta" => $cuenta));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function consultarPedidoTotal($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['estadoPedido'];

    $cuenta = Pedido::traerCuentaPedidoTotal($numeroPedido);
    $payload = json_encode(array("Cuenta" => $cuenta));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function  cobrarCuenta($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $numeroMesa = $parametros['numeroMesa'];
    $estado = 'abonado';
    $mesa = new Mesa();
    $mesa->numero = $numeroMesa;
    $mesa->estado = "pagando";

    Mesa::modificarMesa($mesa);
    Pedido::modificarPedidoEstadoCobro($numeroPedido, $numeroMesa, $estado);

    $payload = json_encode(array("mensaje" => "Pedido abonado"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // CERRAR MESA---------------------------------

  public function cerrarMesa($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroMesa = $parametros['numeroMesa'];
    $estado = $parametros['estado'];

    $mesa = new Mesa();
    $mesa->numero = $numeroMesa;
    $mesa->estado = $estado;
    $mesa->usos = 1;

    Mesa::modificarMesa($mesa);
    $payload = json_encode(array("mensaje" => "Mesa cerrada"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // Encuesta --- foto------------------------------------------------------------------
  public function llenarEncuesta($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $numeroPedido = $parametros['numeroPedido'];
    $numeroMesa = $parametros['numeroMesa'];
    $mesa = $parametros['mesa'];
    $restaurante = $parametros['restaurante'];
    $mozo = $parametros['mozo'];
    $cocinero = $parametros['cocinero'];
    $comentario = $parametros['comentario'];

    $encuesta = new stdClass();
    $encuesta->numeroPedido = $numeroPedido;
    $encuesta->numeroMesa = $numeroMesa;
    $encuesta->mesa = $mesa;
    $encuesta->restaurante = $restaurante;
    $encuesta->mozo = $mozo;
    $encuesta->cocinero = $cocinero;
    $encuesta->comentario = $comentario;
    Pedido::crearEncuesta($encuesta);


    $payload = json_encode(array("mensaje" => "Encuesta creada"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function obtnerMejorEncuesta($request, $response, $args)
  {

    $encuesta = Pedido::mejorEncuesta();
    $payload = json_encode(array("Mejor Encuesta" => $encuesta));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  

  public static function obtenerToken($request)
  {
    $authorizationHeader = $request->getHeader('HTTP_AUTHORIZATION');

    $authorizationToken = isset($authorizationHeader[0]) ? $authorizationHeader[0] : null;

    $token = null;

    if ($authorizationToken) {
      $tokenParts = explode(' ', $authorizationToken);
      $token = isset($tokenParts[1]) ? $tokenParts[1] : null;
    }

    return $token;
  }

  public function descargarCSV($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $encabezado = [];

    foreach ($lista[0] as $key => $value) {
      $encabezado[] = ucfirst($key);
    }

    $encabezadoString = implode(",", $encabezado);
    $archivoCSV = $encabezadoString . "\n";

    foreach ($lista as $pedido) {
      $fila = "";
      foreach ($pedido as $key => $value) {
        $fila .= "$value,";
      }
      $fila = rtrim($fila, ",");
      $archivoCSV .= "$fila\n";
    }

    $response = $response->withHeader('Content-Type', 'text/csv')
      ->withHeader('Content-Disposition', 'attachment; filename="Pedidos.csv"')
      ->withHeader('Content-Description', 'File Transfer')
      ->withHeader('Pragma', 'public');
    $response->getBody()->write($archivoCSV);

    return $response;
  }


  public function cargarCsv($request, $response, $args)
  {
    $archivo = $request->getUploadedFiles();

    $csv = $archivo['csv'];

    $rutaTemporal = $csv->getStream()->getMetadata('uri');

    if (($archivo = fopen($rutaTemporal, "r")) !== false) {
      while (($datos = fgetcsv($archivo, 1000, ";")) !== false) {
        $payload = ProductoController::cargarVariosProductos($datos[0], $datos[1], $datos[2], $datos[3], $datos[4]);
      }
      fclose($archivo);
    } else {
      $payload = json_encode(array("Mensaje" => 'error archivo'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  // https://github.com/KevinCabezas/tp-slim-progra3.git
}
