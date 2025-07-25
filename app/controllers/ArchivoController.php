<?php
require_once '../models/Archivo.php';
require_once '../interfaces/IApiUsable.php';

// class MesaController extends Mesa implements IApiUsable
class ArchivoController extends Archivo
{
  public function guardarFoto($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $archivo = $request->getUploadedFiles();
    $numeroPedido = $parametros['numeroPedido'];
    $cliente = $parametros['cliente'];
    $foto = $archivo['foto'];

    $imagen = new stdClass();
    $imagen->nombre = $cliente;
    $imagen->numeroPedido = $numeroPedido;
    Archivo::subirImagenes($imagen, $foto, 'assets/ImagenesPedidos/2024/', 'venta');
    $payload = json_encode(array("Mensaje" => 'foto guardada'));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
