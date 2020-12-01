<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Firebase\JWT\JWT;
use App\Models\Turno;

class FacturaController{
    public function get(Request $request, Response $response, $args) {
        //OBTENGO ID DEL TOKEN
        $token =  $request->getHeader('token');
        $decoded = JWT::decode($token[0], "segundoParcial", array('HS256'));
        $idUsuario = $decoded->id;

        $turnos = Turno::join('usuarios', 'turnos.id_cliente', 'usuarios.id')->where('id_cliente', '=', $idUsuario)->select('usuarios.nombre', 'turnos.tipo_mascota', 'turnos.fecha_atencion', 'turnos.precio')->get();

        if(empty($turnos))
        {
            $response->getBody()->write("EL CLIENTE NO TIENE MASCOTAS INGRESADAS");
            return $response->withStatus(400);
        }
        elseif( $turnos != null)
        {
            
            $response->getBody()->write(json_encode($turnos));
        }
        else{
            $response->getBody()->write("HUBO UN ERROR AL MOSTRAR LOS TURNOS");
            return $response->withStatus(400);
        }
        return $response;
        
    }
}