<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


// // Routes Usuarios
return function (App $app) {
  $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/lista', \UsuarioController::class . ':TraerTodos')
    ->add(new ConfirmarPerfil(["socio"]));

    $group->post('/crear', \UsuarioController::class . ':CargarUno');
    // $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    // $group->put('/{usuarioId:[0-9]+}', \UsuarioController::class . ':ModificarUno');
    // $group->delete('/{usuarioId:[0-9]+}', \UsuarioController::class . ':BorrarUno');
    $group->post('/login', \UsuarioController::class . ':Login')
    ->add(\UsuarioMW::class . ":verificarUsuario");

    $group->get('/registros', \UsuarioController::class . ':obtnerUnRegistroLogin');
  });
};
