<?php
require_once '../models/Producto.php';
require_once '../interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $nombre = $parametros['nombre'];
    $tipo = $parametros['tipo'];
    $stock = $parametros['stock'];
    $precio = $parametros['precio'] ?? null;


    $producto = new Producto();
    $producto->codigo = $codigo;
    $producto->nombre = $nombre;
    $producto->tipo = Producto::obtenerTipoProducto($tipo);
    $producto->stock = $stock;
    $producto->precio = $precio;

    $flag = Producto::verificarProducto($nombre, $tipo, $codigo);
    var_dump($flag);
    if ($flag) {
      var_dump("1");
      Producto::modificarPrecioStock($producto);
      $payload = json_encode(array("mensaje" => "Producto existente, stock agregado"));
    } else {
      var_dump("2");

      $producto->crearProducto();
      $payload = json_encode(array("mensaje" => "Producto agregado con exito"));
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function cargarVariosProductos($codigo, $nombre, $tipo, $stock, $precio)
  {

    $producto = new Producto();
    $producto->nombre = $nombre;
    $producto->tipo = $tipo;
    $producto->stock = $stock;
    $producto->precio = $precio;

    $flag = Producto::verificarProducto($nombre, $tipo, $codigo);
    if ($flag) {
      Producto::modificarPrecioStock($producto);
      $payload = json_encode(array("mensaje" => "Producto existente, stock agregado"));
    } else {
      $producto->crearProducto();
      $payload = json_encode(array("mensaje" => "Producto agregado con exito"));
    }


    return $payload;
  }

  public function TraerUno($request, $response, $args)
  {
    $usr = $args['producto'];
    $usuario = Producto::obtenerProducto($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("lista" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args) 
  {
    
  }

  public function BorrarUno($request, $response, $args) {}


}
