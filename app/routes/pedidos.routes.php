<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    //   //correccion 5
    $group->get('/lista', \PedidoController::class . ':TraerTodos')
      ->add(new ConfirmarPerfil(["socio"]));

    // correccion 1
    $group->post('/crear', \PedidoController::class . ':CargarUno')
      ->add(\VerificarMiddleware::class . ":mesaLibreMW")
      ->add(new ConfirmarPerfil(["mozo"]));

    $group->get('/consulta/pendientes', \PedidoController::class . ':pendientesPedidos')
      ->add(new ConfirmarPerfil(["cocinero", "pastelero", "cevecero", "bartender"]));

    $group->put('/actualizar/pendientes', \PedidoController::class . ':actualizarEstadosPedidos')
      ->add(\PedidoMW::class . ':verificarProductoPuestoMW')
      ->add(new ConfirmarPerfil(["cocinero", "pastelero", "cevecero", "bartender"]));
    //   ->add(\VerificarMiddleware::class . ':estadosCorrectosMW')
    //   ->add(\VerificarMiddleware::class . ':numeroPedidoBodyMW')

    
    $group->get('/consulta/cliente', \PedidoController::class . ':consultaPedidoCliente');
  });
};
