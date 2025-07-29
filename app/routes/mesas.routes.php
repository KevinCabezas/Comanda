<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {

  $app->group('/mesas', function(RouteCollectorProxy $group) {
    $group->get('/lista', \MesaController::class . ':TraerTodos');

    $group->get('/lista-facturacion', \MesaController::class . ':obtenreListaMesasFacturacion'); // Correccion n° 21

    $group->get('/facturacion', \MesaController::class . ':obtenerFacturacionMesaEntreFechas'); // Correccion n° 22

    $group->post('/crear', \MesaController::class . ':CargarUno');
    $group->put('/modificar/{numero:[0-9]+}', \MesaController::class . ':ModificarUno');
  });
};