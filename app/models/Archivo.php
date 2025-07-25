<?php

require_once '../models/Usuario.php';
require_once '../models/Pedido.php';
require_once '../models/Producto.php';

class Archivo
{
  private static $rutaDestino;

  public static function getRuta()
  {
    return self::$rutaDestino;
  }

  public static function crearDirectorio($directorio)
  {
    if (!is_dir($directorio)) {
      mkdir($directorio, 0777, true);
      return true;
    } else {
      return false;
    }
  }

  public static function crearNombreArchivo($objeto)
  {
    $mail = $objeto->usuario;
    $antesArroba = explode("@", $mail);
    $usuario = $antesArroba[0];
    $nombreArchivo = $objeto->nombre . "_" .
      ucwords($objeto->tipo) .  "_" .
      $objeto->marca .
      ucwords($usuario);
    return $nombreArchivo;
  }

  public static function subirImagenes($objeto, $archivo, $ruta, $clase)
  {
    self::crearDirectorio($ruta);
    switch ($clase) {
      case "producto":
        $nombreArchivo = $objeto->nombre . "_" . $objeto->tipo;
        echo $nombreArchivo;
        self::$rutaDestino = $ruta . $nombreArchivo . "." . pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
        break;

      case "venta":
        $nombre = str_replace(' ', '_', $objeto->nombre);
        $nombreArchivo = $nombre . '_' . $objeto->numeroPedido;
        self::$rutaDestino = $ruta . $nombreArchivo . "." . pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
        break;

      case "usuario":
        $fecha = new DateTime();
        $fechaFormat = $fecha->format('Y-m-d');

        $nombre = str_replace(' ', '_', $objeto->usuario);
        $nombreArchivo = $nombre . "_" . $objeto->perfil . $fechaFormat;
        self::$rutaDestino = $ruta . $nombreArchivo . "." . pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
        break;
    }

    $archivo->moveTo(self::$rutaDestino);
  }

  public static function descargarPdf($request, $response, $args)
  {
    $parametros = $request ->getParsedBody();
    $opcion = $parametros["lista"];
    var_dump($opcion . "1");
    // switch ($opcion) {
    //   case "productos":
    //     $lista = Producto::obtenerTodos();
    //     var_dump($opcion);
    //     break;
    //   case "usuarios":
    //     $lista = Usuario::obtenerTodos();
    //     var_dump($opcion);
    //     break;
    //   case "pedidos":
    //     $lista = Pedido::obtenerTodos();
    //     var_dump($opcion);
    //     break;
    // }
    $lista = Producto::obtenerTodos();

    $claves = array_keys(get_object_vars($lista[0]));

    // // Crear una instancia de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // $pdf->Output();
    $flag = false;
    // Agregar un logo
    $logo =  './ImagenLogo/logo.png'; // Ajusta la ruta según sea necesario
    if (file_exists($logo)) {
      $pdf->Image($logo, 10, 10, 30); // Posición x, y y tamaño
      $flag = true;
    } else {
      $payload = json_encode(array("Error" => 'imagen logo'));
      $response->getBody()->write($payload);
      $response->withHeader('Content-Type', 'application/json');
    }

    if ($flag) {

      // $anchoColum = 180 / count($claves) - 1;
      // Configurar fuente
      $pdf->SetFont('Arial', 'B', 16);
      $pdf->Ln(40); // Espacio después del logo
      $pdf->Cell(0, 10, 'Lista de ' , 1, 1, 'C');
      $contador = 0;
      foreach ($claves as $clave) {

        $pdf->SetFont('Arial', '', 12);
        if ($contador == 0) {
          // $anchoColum = 20;
          $pdf->Cell(10, 10, ucfirst($clave), 1, 0, 'C');

          $contador += 1;
        } else {
          $anchoColum = 180 / 6;

          $pdf->Cell($anchoColum, 10, ucfirst($clave), 1, 0, 'C');
        }
      }
      $pdf->Ln();

      // Contenido del PDF
      $pdf->SetFont('Arial', '', 12);
      foreach ($lista as $usuario) {
        foreach ($usuario as $valor) {
          // var_dump($valor);
          $pdf->Cell($anchoColum, 10, $valor ?? 'N/A', 1, 0, 'C');
          // $pdf->MultiCell($anchoColum, 10, $valor ?? 'N/A', 1, 'C');
        }
        $pdf->Ln();
      }



      // Generar el PDF en memoria
      $archivoPDF = $pdf->Output();

      // Configurar la respuesta para la descarga
      $response = $response->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'attachment; filename="documento.pdf"')
        ->withHeader('Content-Description', 'File Transfer')
        ->withHeader('Pragma', 'public');
      $response->getBody()->write($archivoPDF);
    }

    return $response;
    // $payload = json_encode(array("Mensaje" => 'entro pdf'));

    // $response->getBody()->write($payload);
    // return $response->withHeader('Content-Type', 'application/json');
  }
}
