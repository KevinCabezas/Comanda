<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('/lista', \ProductoController::class . ':TraerTodos');
    $group->post('/crear', \ProductoController::class . ':CargarUno');
    // ->add(new ConfirmarPerfil([1]));

    $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  });
};
