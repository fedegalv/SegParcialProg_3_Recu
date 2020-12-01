<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Mascota;

class MascotaController{

    
    public function add(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $mascota = new Mascota();
        $mascota->tipo = $parsedBody['tipo'];
        $mascota->precio = $parsedBody['precio'];

        $rta = $mascota->save();
        if ($rta) {
            $response->getBody()->write("MASCOTA AGREGADA CON EXITO");
        } else {
            $response->getBody()->write("HUBO UN ERROR AL REGISTRAR MASCOTA");
        }
        //$response->getBody()->write(json_encode($rta));
        return $response;
    }

}