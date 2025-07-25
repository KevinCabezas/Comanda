<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {

  $app->group('/mesas', function(RouteCollectorProxy $group) {
    $group->get('/lista', \MesaController::class . ':TraerTodos');
    $group->post('/crear', \MesaController::class . ':CargarUno');
    $group->put('/modificar/{numero:[0-9]+}', \MesaController::class . ':ModificarUno');
  });
};