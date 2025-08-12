<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {

  $app->group('/archivos', function (RouteCollectorProxy $group) {

    $group->post('/foto', \ArchivoController::class . ':guardarFoto')
      ->add(new ConfirmarPerfil(["mozo"]));

    // ->add(\ValidarMiddleware::class . ":validarArchivoMW");
  });
};
