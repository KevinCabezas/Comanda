<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


// // Routes Usuarios
return function (App $app) {
  $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/lista', \UsuarioController::class . ':TraerTodos')
    ->add(new ConfirmarPerfil(["socio"]));

    $group->post('/crear', \UsuarioController::class . ':CargarUno')
    ->add(\UsuarioMW::class . ":verificarUsuarioDataMW");

    // $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');

    $group->put('/modificar/{usuarioId}', \UsuarioController::class . ':ModificarUno')
    ->add(\UsuarioMW::class . ":verificarUsuarioDataMW");

    $group->delete('/borrar/{usuarioId}', \UsuarioController::class . ':BorrarUno');

    $group->post('/login', \UsuarioController::class . ':Login')
    ->add(\UsuarioMW::class . ":verificarUsuarioMW");

    $group->get('/registros', \UsuarioController::class . ':obtnerUnRegistroLogin');
  });
};
