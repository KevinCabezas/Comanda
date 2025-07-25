<?php
require_once '../models/Usuario.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UsuarioMW
{
    public function verificarUsuario(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody() ?? [];

        $usuario = $parametros['usuario'] ?? null;
        $clave = $parametros['clave'] ?? null;
        // var_dump($usuario);

        if (!$usuario || !$clave) {
            $response = new Response();
            $payload = json_encode(["error" => "Faltan datos de autenticación"]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $datos = Usuario::verificarUsuario($usuario, $clave);

        if ($datos === false) {
            $response = new Response();
            $payload = json_encode(["error" => "Usuario o clave incorrecto"]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }


    public static function validarArchivoMW(Request $request, RequestHandler $handler)
    {
        $file = $request->getUploadedFiles();
        $archivo = $file['foto'] ?? null;

        if (!$archivo || $archivo->getError() !== UPLOAD_ERR_OK) {
            return self::reponseError('Error al subir el archivo');
        }

        $tipoArchivo = $archivo->getClientMediaType();
        $tamanioArchivo = $archivo->getSize();
        $tiposPermitidos = ['image/png', 'image/jpeg'];
        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            return self::reponseError('Tipo de archivo no permitido');
        }

        if ($tamanioArchivo > 200000) {
            return self::reponseError('El tamaño del archivo excede el limite permitido');
        }
        return $handler->handle($request);
    }

    private static function reponseError($mensaje)
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $mensaje]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function validarMailMW(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        if (!isset($parametros['usuario']) || empty($parametros['usuario'])) {
            $response = self::reponseError('El campo usuario es obligatorio');
        } else {
        }
        $response = $handler->handle($request);
        return $response;
    }


    // ----Modificar-------------------------------

    public static function modifcarPedidoMW(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        if (empty($parametros["nombre"]) || empty($parametros["tipo"]) || empty($parametros["marca"]) || empty($parametros["stock"])) {
            $response = self::reponseError('Completa todos los campos');
        } else {
            $response = $handler->handle($request);
        }
        return $response;
    }
}
