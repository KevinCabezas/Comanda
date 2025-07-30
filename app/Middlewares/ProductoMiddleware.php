<?php
require_once '../models/Mesa.php';
require_once '../models/Pedido.php';
require_once '../models/Producto.php';

use Illuminate\Support\Facades\Process;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use \Slim\Routing\RouteContext;

class ProductoMW
{

  public function verificarProductoDataMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();

    // Verificar existencia de campos requeridos
    if (
      !isset($parametros['codigo']) ||
      !isset($parametros['nombre']) ||
      !isset($parametros['tipo']) ||
      !isset($parametros['stock']) ||
      !isset($parametros['precio'])
    ) {
      return self::reponseError('Faltan datos requeridos: codigo, nombre, tipo, stock o precio');
    }

    // Validar tipos y valores
    if (!is_numeric($parametros['codigo']) || $parametros['codigo'] <= 0) {
      return self::reponseError('El codigo debe ser un numero positivo');
    }
    if (!is_string($parametros['nombre']) || strlen(trim($parametros['nombre'])) == 0) {
      return self::reponseError('El nombre debe ser un texto no vacío');
    }
    if (!is_string($parametros['tipo']) || strlen(trim($parametros['tipo'])) == 0) {
      return self::reponseError('El tipo debe ser un texto no vacío');
    }
    if (!is_numeric($parametros['stock']) || $parametros['stock'] < 0) {
      return self::reponseError('El stock debe ser un numero mayor o igual a 0');
    }
    if (!is_numeric($parametros['precio']) || $parametros['precio'] <= 0) {
      return self::reponseError('El precio debe ser un numero positivo');
    }

    $response = $handler->handle($request);
    return $response;
  }


  public function codigoProductoMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $codigo = $parametros['codigo'];
    $nombre = $parametros['nombre'];
    $tipo = $parametros['tipo'];

    // Validaciones de existencia
    $tipoExiste        = Producto::verificarProductoTipo($tipo); // bool
    $coincideExacto    = Producto::verificarProducto($nombre, $tipo, $codigo); // bool
    $nombreCodigoMatch = Producto::verificarProductoDos($nombre, $codigo); // bool

    if (!$tipoExiste) {
      return self::reponseError('Error: El tipo de producto no existe');
    }

    if ($coincideExacto) {
      // Nombre, tipo y código coinciden → permitir sumar stock
      return $handler->handle($request);
    }

    if (!$nombreCodigoMatch) {
      // El nombre y código no coinciden → se permite crear solo si es nuevo (y tipo válido)
      return $handler->handle($request);
    }

    // Si el nombre y el código existen pero no coinciden → conflicto
    return self::reponseError("Error: el nombre y/o código ya existen en productos diferentes");
  }




  public static function validarProductoCsv($codigo, $nombre, $tipo)
  {
    // Validaciones
    $tipoExiste        = Producto::verificarProductoTipo($tipo);
    $coincideExacto    = Producto::verificarProducto($nombre, $tipo, $codigo);
    $nombreCodigoMatch = Producto::verificarProductoDos($nombre, $codigo);

    if (!$tipoExiste) {
      return "Error: Tipo de producto '$tipo' no existe.";
    }

    if ($coincideExacto) {
      return true; // Todo OK, producto existente → se suma stock
    }

    if (!$nombreCodigoMatch) {
      return true; // Producto nuevo → se permite crear
    }

    return "Error: nombre '$nombre' o código '$codigo' ya existen pero no coinciden.";
  }

  public function mesaLibreMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $mesaNumero = $parametros['mesa'];
    $mesa = Mesa::obtenerMesa($mesaNumero);

    if ($mesa->estado == "libre") {
      $response = $handler->handle($request);
    } else {
      return self::reponseError('Mesa ocupada');
    }

    return $response;
  }

  public function funcionCorrectosMW(Request $request, RequestHandler $handler)
  {
    $funcion = self::requestArgs($request, 'funcion');
    if ($funcion === "cocinero" || $funcion === "bartender" || $funcion === "pastelero" || $funcion === "cervecero") {
      $response = $handler->handle($request);
    } else {
      return self::reponseError('Funciones Disponibles: cocinero, bartender, pastelero, cervecero');
    }

    return $response;
  }

  public function estadosCorrectosMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $estado = $parametros['estado'];
    if ($estado === "listo" || $estado === "preparacion" || $estado === "pendiente") {
      $response = $handler->handle($request);
    } else {
      return self::reponseError('Estados Disponibles: listo, preparacion, pendiente');
    }

    return $response;
  }

  public function numeroPedidoArgsMW(Request $request, RequestHandler $handler)
  {
    $numero = self::requestArgs($request, 'numeroPedido');
    $pedido = Pedido::obtenerPedido($numero);
    if ($pedido !== false) {
      $response = $handler->handle($request);
    } else {
      return self::reponseError("Pedido inexistente");
    }

    return $response;
  }

  public function numeroPedidoBodyMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $numero = $parametros['numeroPedido'];

    $pedido = Pedido::obtenerPedido($numero);
    if ($pedido !== false) {
      $response = $handler->handle($request);
    } else {
      return self::reponseError("Pedido inexistente");
    }

    return $response;
  }

  public function verificarProductoPuestoMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $producto = $parametros['codigoProducto'];

    $token = PedidoController::obtenerToken($request);
    $datos = AutentificadorJWT::ObtenerData($token);
    $puestoEmpleado = $datos->puesto;
    $codigoOk = Producto::verificarProductoPuesto($producto, $puestoEmpleado);
    if ($codigoOk !== false) {
      $response = $handler->handle($request);
    } else {
      return self::reponseError("Codigo del Producto Inavalido");
    }

    return $response;
  }

  public function codigoMesaMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $codigo = $parametros['codigoMesa'];
    $mesa = Mesa::obtenerMesa('', $codigo);
    if ($mesa !== false) {
      $response = $handler->handle($request);
    } else {
      return self::reponseError("Codigo incorrecto");
    }

    return $response;
  }

  public function estadoMozoMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $numero = $parametros['numeroPedido'];
    $numeroMesa = $parametros['numeroMesa'];

    $pedido = Pedido::obtenerPedido($numero);
    if ($pedido !== false && $pedido->numeroMesa == $numeroMesa) {
      $response = $handler->handle($request);
    } else {
      return self::reponseError("Pedido o Mesa incorrecta");
    }

    return $response;
  }

  public function estadoMesaMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $estadoMesa = $parametros['estadoMesa'];
    if ($estadoMesa === "pagando" || $estadoMesa === "comiendo") {
      $response = $handler->handle($request);
    } else {
      return self::reponseError('Estados Disponibles: comiendo, pagando');
    }

    return $response;
  }

  public function estadoPedidoMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $estadoPedido = $parametros['estadoPedido'];
    if ($estadoPedido === "abonado" || $estadoPedido === "entregado") {
      $response = $handler->handle($request);
    } else {
      return self::reponseError('Estados Disponibles: entregado, abonado');
    }

    return $response;
  }

  public function cerraMesaMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $numeroMesa = $parametros['numeroMesa'];
    $estado = $parametros['estado'];

    $mesa = Mesa::obtenerMesa($numeroMesa);
    if ($mesa->estado === 'pagando' && $estado == 'cerrada') {
      $response = $handler->handle($request);
    } else if ($mesa->estado === 'comiendo' || $mesa->estado === 'esperando') {
      return self::reponseError('Mesa no esta lista para cerrar');
    } else if ($estado !== 'cerrada') {
      return self::reponseError('Estado disponible: cerrada');
    } else {
      return self::reponseError('mesa libre o ya cerrada');
    }

    return $response;
  }

  public function encuestaMW(Request $request, RequestHandler $handler)
  {
    $parametros = $request->getParsedBody();
    $mesa = $parametros['mesa'];
    $mozo = $parametros['mozo'];
    $cocinero = $parametros['cocinero'];
    $restaurante = $parametros['restaurante'];
    $comentario = $parametros['comentario'];

    $flag = true;
    $flag2 = true;

    if (strlen($comentario) > 66) {
      $flag = false;
    }

    if ($mesa < 1 || $mesa > 10) {
      $flag2 = false;
    }
    if ($mozo < 1 || $mozo > 10) {
      $flag2 = false;
    }
    if ($cocinero < 1 || $cocinero > 10) {
      $flag2 = false;
    }
    if ($restaurante < 1 || $restaurante > 10) {
      $flag2 = false;
    }


    if ($flag && $flag2) {
      $response = $handler->handle($request);
    } else if (!$flag2) {
      return self::reponseError('Ingrese numeros entre 1 y 10');
    } else {
      return self::reponseError('Ingrese solo 66 caracteres en el comentario');
    }

    return $response;
  }


  private static function reponseError($mensaje)
  {
    $response = new Response();
    $response->getBody()->write(json_encode(['error' => $mensaje]));
    return $response->withHeader('Content-Type', 'application/json');
  }

  private static function requestArgs($request, $args)
  {
    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();
    return $route->getArgument($args);
  }
}
