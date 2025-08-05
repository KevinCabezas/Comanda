<?php

require_once '../models/Mesa.php';
require_once '../interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $mesa = new Mesa();
    $mesa->numero = $parametros['numero'];
    $mesa->estado = $parametros['estado'];
    $mesa->crearMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $numeroMesa = $args['numero'];
    $mesa = Mesa::obtenerMesa($numeroMesa);
    $payload = json_encode($mesa);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::obtenerTodos();
    $payload = json_encode(array("lista" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $id = $args['mesaId'];

    $mesa = new Mesa();
    $mesa->id = $id;
    $mesa->numero = $parametros['numero'];
    $mesa->estado = $parametros['estado'];
    Mesa::modificarMesa($mesa);
    
    $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function MesaMasUsada($request, $response, $args)
  {
    $mesa = Mesa::obtenerMesaUsos();
    $payload = json_encode(array("Mesa" => $mesa));
    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {

    $id = $args['mesaId'];
    Mesa::borrarMesa($id);

    $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function obtenreListaMesasFacturacion($request, $response, $args)
  {
    $lista = Mesa::listaMesaFacturacion();
    $payload = json_encode(array("lista" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  // Correción n° 22
  public function obtenerFacturacionMesaEntreFechas($request, $response, $args)
  {
    $queryParams = $request->getQueryParams();
    $numeroMesa = $queryParams['mesa'] ?? null;
    $fechaUno = $queryParams['fechaUno'] ?? null;
    $fechaDos = $queryParams['fechaDos'] ?? null;
    $mesa = Mesa::mesaFacturacionFecha($numeroMesa, $fechaUno, $fechaDos);
    $payload = json_encode($mesa);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
