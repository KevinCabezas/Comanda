<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;//nucleo central del los middleware
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '../../../vendor/autoload.php';

require_once '../db/AccesoDatos.php';
// require_once '../db/archivos.php';
require_once '../Middlewares/ConfirmarPerfil.php';
require_once '../Middlewares/UsuarioMiddleware.php';
require_once '../Middlewares/VericacionesMiddleware.php';

require_once '../controllers/UsuarioController.php';
require_once '../controllers/ProductoController.php';
require_once '../controllers/PedidoController.php';
require_once '../controllers/MesaController.php';
require_once '../controllers/ArchivoController.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

$app = AppFactory::create();


// Middleware para permitir CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response;
});

$app->add(function ($request, $handler) {
  $response = $handler->handle($request);
  return $response
    ->withHeader('Access-Control-Allow-Origin', '*') // Permitir el frontend
    ->withHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
    ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
    ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

(require __DIR__ . '/../routes/usuarios.routes.php')($app);
(require __DIR__ . '/../routes/pedidos.routes.php')($app);
(require __DIR__ . '/../routes/productos.routes.php')($app);
(require __DIR__ . '/../routes/mesas.routes.php')($app);
(require __DIR__ . '/../routes/archivos.routes.php')($app);

$app->run();

// // Routes Usuarios
// $app->group('/usuarios', function (RouteCollectorProxy $group) {
//     $group->post('[/]', \UsuarioController::class . ':CargarUno');
//     $group->get('/lista', \UsuarioController::class . ':TraerTodos');
//     $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
//     $group->put('/{usuarioId:[0-9]+}', \UsuarioController::class . ':ModificarUno');
//     $group->delete('/{usuarioId:[0-9]+}', \UsuarioController::class . ':BorrarUno');

// });

// $app->post('/usuarios/login', \UsuarioController::class . ':Login')
// ->add(\ValidarMiddleware::class . ":userMW");


// // $app->get('[/]', function (Request $request, Response $response) {    
// //     $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
// //     $response->getBody()->write($payload);
// //     return $response->withHeader('Content-Type', 'application/json');
// // });

// // Routes Productos
// $app->group('/productos', function (RouteCollectorProxy $group) {
//   $group->get('/lista', \ProductoController::class . ':TraerTodos');
//   $group->post('/agregar', \ProductoController::class . ':CargarUno');
//   // ->add(new ConfirmarPerfil([1]));

//   $group->get('/{producto}', \ProductoController::class . ':TraerUno');
// });

// // Routes Pedidos-------------------------------------------------

// $app->group('/pedidos', function (RouteCollectorProxy $group) {
  
//   // correccion 1
//   $group->post('/crear', \PedidoController::class . ':CargarUno')
//   ->add(\VerificarMiddleware::class . ":mesaLibreMW");
//   // ->add(new ConfirmarPerfil([5]));
  
//   //correccion 2
//   $group->post('/foto', \PedidoController::class . ':guardarFoto')
//   ->add(\ValidarMiddleware::class . ":validarArchivoMW");
//   // ->add(new ConfirmarPerfil([5]));
  
  
//   // correccion 3 y 6
//   $group->put('/actualizarEstados', \PedidoController::class . ':actualizarEstadosPedidos')
//   ->add(\VerificarMiddleware::class . ':verificarProductoPuestoMW')
//   ->add(\VerificarMiddleware::class . ':estadosCorrectosMW')
//   ->add(\VerificarMiddleware::class . ':numeroPedidoBodyMW')
//   ->add(new ConfirmarPerfil([2,3,4,6]));
  
//   //correccion 4
//   $group->get('/consultaCliente', \PedidoController::class . ':pedidoCliente')
//   ->add(\VerificarMiddleware::class . ':codigoMesaMW')
//   ->add(\VerificarMiddleware::class . ':numeroPedidoBodyMW');

//   //correccion 5
//   $group->get('/lista', \PedidoController::class . ':TraerTodos');
//   // ->add(new ConfirmarPerfil([1]));

//   // correcion 7
//   $group->get('/consultaMozo', \PedidoController::class . ':consultarPedidoMozo');
//   $group->put('/estadoMozo', \PedidoController::class . ':cambiarEstadoPedidoMozo')
//   ->add(\VerificarMiddleware::class . ':estadoPedidoMW')
//   ->add(\VerificarMiddleware::class . ':estadoMesaMW')
//   ->add(\VerificarMiddleware::class . ':estadoMozoMW');
//   // ->add(new ConfirmarPerfil([5]));

//   // correcion 9
//   $group->get('/cuentaDetallada', \PedidoController::class . ':consultarPedidoDetallado')
//   ->add(\VerificarMiddleware::class . ':estadoMozoMW');
//   // ->add(new ConfirmarPerfil([1,5]));

//   $group->get('/cuentaTotal', \PedidoController::class . ':consultarPedidoTotal')
//   ->add(\VerificarMiddleware::class . ':estadoPedidoMW');
//   // ->add(new ConfirmarPerfil([1,5]));

//   $group->put('/cobrar', \PedidoController::class . ':cobrarCuenta')
//   ->add(\VerificarMiddleware::class . ':estadoPedidoMW');
//   // ->add(new ConfirmarPerfil([5]));

  
//   // correccion 10
//   $group->put('/mesa', \PedidoController::class . ':cerrarMesa')
//   ->add(\VerificarMiddleware::class . ':cerraMesaMW');
//   // ->add(new ConfirmarPerfil([1]));

//   // correccion 11
//   $group->post('/encuesta', \PedidoController::class . ':llenarEncuesta')
//   ->add(\VerificarMiddleware::class . ':encuestaMW')
//   ->add(\VerificarMiddleware::class . ':estadoMozoMW');
//   // ->add(new ConfirmarPerfil([1]));

//   // correccion 12
//   $group->get('/comentarios', \PedidoController::class . ':obtnerMejorEncuesta');
//   // ->add(new ConfirmarPerfil([1]));
  
//   $group->get('/descargar', \PedidoController::class . ':descargarCSV');
//   // ->add(new ConfirmarPerfil([1]));------------

//   $group->post('/cargarCsv', \PedidoController::class . ':cargarCsv');

//   // 3
//   $group->get('/{funcion:[a-zA-Z]+}', \PedidoController::class . ':pendientesPedidos')
//   ->add(\VerificarMiddleware::class . ":estadosCorrectosMW");
//   // ->add(new ConfirmarPerfil([2,3,4,6]));
//   // 5
//   $group->get('/{numeroPedido:[0-9]+}', \PedidoController::class . ':TraerUno')
//   ->add(\VerificarMiddleware::class . ":numeroPedidoArgsMW");


// });

// // Routes Mesas-----------------------------------------------------------------------------
// $app->group('/mesas', function (RouteCollectorProxy $group) {
//   $group->get('/lista', \MesaController::class . ':TraerTodos');
//   $group->get('/masUsada', \MesaController::class . ':MesaMasUsada');
  
//   $group->get('/{numero}', \MesaController::class . ':TraerUno');
//   $group->post('[/]', \MesaController::class . ':CargarUno');
//   $group->put('/{numero}', \MesaController::class . ':ModificarUno');
// });

// $app->group('/archivos', function (RouteCollectorProxy $group) {
//   $group->get('/pdf', \Archivo::class . ':descargarPdf');
// });

// $app->get('/prueva', \PedidoController::class . ':pendientesPedidos');






