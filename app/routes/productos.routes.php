<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('/lista', \ProductoController::class . ':TraerTodos')
    ->add(new ConfirmarPerfil(["socio"]));

    $group->post('/crear', \ProductoController::class . ':CargarUno')
    ->add(\ProductoMW::class . ":codigoProductoMW")
    ->add(\ProductoMW::class . ":verificarProductoDataMW");

    $group->post('/cargar-productos', \ProductoController::class . ':cargarCsv');
    // ->add(new ConfirmarPerfil([1]));

    $group->put('/{productoId}', \ProductoController::class . ':ModificarUno')
    ->add(\ProductoMW::class . ":verificarProductoDataMW");
    
    $group->get('/{productoId}', \ProductoController::class . ':TraerUno');
    $group->delete('/{productoId}', \ProductoController::class . ':BorrarUno');
  });
};
