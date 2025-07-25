<?php
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ConfirmarPerfil
{
    private $perfilesPermitidos;

    public function __construct(array $perfiles)
    {
        $this->perfilesPermitidos = $perfiles;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $token = $request->getHeader('authorization')[0] ?? '';
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        try {
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            if (!in_array($data->puesto, $this->perfilesPermitidos)) {
                throw new Exception('Puesto no autorizado');
            }

            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $response->getBody()->write('Error en acceso: ' . $e->getMessage());
            return $response->withStatus(403);
        }
    }
}