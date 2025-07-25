<?php
require_once '../models/Usuario.php';
require_once '../interfaces/IApiUsable.php';
require_once '../utils/AutentificadorJWT.php';


class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $puesto = $parametros['puesto'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $clave = $parametros['clave'];

        $idPuesto = Usuario::obtnerPuestoId($puesto);
        // Creamos el usuario
        $usr = new Usuario();
        $usr->puesto = $idPuesto;
        $usr->nombre = $nombre;
        $usr->apellido = $apellido;
        $usr->clave = $clave;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function traerTipo($request, $response, $args)
    {
      $lista = Usuario::traerTipoUsuario();
      $payload = json_encode(array("tipos" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['usuarioId'];

        $idPuesto = Usuario::obtnerPuestoId($parametros['puesto']);
        
        $usuario = new Usuario();
        $usuario->id = $id;
        $usuario->puesto = $idPuesto;
        $usuario->nombre = $parametros['nombre'];
        $usuario->apellido = $parametros['apellido'];
        $usuario->clave = $parametros['clave'];
  
        Usuario::modificarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        // $usuarioId = $parametros['usuarioId'];
        $usuarioId = $args['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Login($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];

      // $payload = json_encode(array("mensaje" => "Bienvenido"));
      $datos = Usuario::verificarUsuario($usuario, $clave);

      $token = AutentificadorJWT::CrearToken($datos);
      
      $response->getBody()->write(json_encode(['token' => $token]));
      return $response->withHeader('Content-Type', 'application/json');
    }
}
