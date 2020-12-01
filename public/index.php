<?php
///1- EN CONSOLE composer init
///2- LUEGO composer require slim/slim:"4.*"
//3- LUEGO composer require slim/psr7
//4- USAR EL CODIGO DEBAJO COMO TEMPLATE
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//AGREGAR RequestHandler;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

//10-LUEGO DE CREAR EL NAMESPACE CONFIG AGREGARLO
use Config\Database;
//LUEGO DE CREAR EL NAMESPACE APP CON LA CLASE MODELO AGREGAR
//use App\Models\Alumno;
//EL MODELO NO HACE FALTA AGREGAR YA QUE SE MANEJA DESDE CONTROLLER
use App\Controllers;
use App\Controllers\UsuarioController;
use App\Controllers\MascotaController;
use App\Controllers\TurnoController;
use App\Controllers\FacturaController;


use App\Middlewares\MascotaRepetidaValidate;


use Slim\Routing\RouteCollectorProxy;

use App\Middlewares\JsonMiddleware;
use App\Middlewares\DatosValidosUsuario;
use App\Middlewares\AdminAuthMiddleware;
use App\Middlewares\ClienteAuthMiddleware;



require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();


$app->setBasePath('/SegParcialProg_3_Recu/public');

new Database;
//1
$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('[/]', UsuarioController:: class .":registro")->add(new DatosValidosUsuario);
})->add(new JsonMiddleware);

//2
$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', UsuarioController:: class .":login");
})->add(new JsonMiddleware);
//3
$app->group('/mascota', function (RouteCollectorProxy $group) {
    $group->post('[/]', MascotaController:: class .":add")->add(new AdminAuthMiddleware)->add(new MascotaRepetidaValidate);
})->add(new JsonMiddleware);
//4
$app->group('/turno', function (RouteCollectorProxy $group) {
    $group->post('[/]', TurnoController:: class .":add")->add(new ClienteAuthMiddleware);
    //6
    $group->put('[/{idTurno}]', TurnoController:: class .":marcarAtendido")->add(new AdminAuthMiddleware);
})->add(new JsonMiddleware);
//5
$app->group('/turnos', function (RouteCollectorProxy $group) {
    $group->get('[/]', TurnoController:: class .":getAll")->add(new AdminAuthMiddleware);
})->add(new JsonMiddleware);

//7
$app->group('/factura', function (RouteCollectorProxy $group) {
    $group->get('[/]', FacturaController:: class .":get")->add(new ClienteAuthMiddleware);
})->add(new JsonMiddleware);


$app->run();