<?php

namespace App\Controllers;

use App\Models\Mascota;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Firebase\JWT\JWT;
use App\Models\Turno;

class TurnoController{

    
    public function add(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        //TOKEN
        $token =  $request->getHeader('token');
        $decoded = JWT::decode($token[0], "segundoParcial", array('HS256'));
        $tipo = $parsedBody['tipo'];
      
       
        //BUSCO LA MASCOTA POR SU TIPO PARA SACAR PRECIO
        $mascota = Mascota::where('tipo','=', $tipo)->first();
        //var_dump($mascota);
        if($mascota != null){
            $turno = new Turno();
            $turno->tipo_mascota = $tipo;
            $turno->fecha_atencion = $parsedBody['fecha'];
            $turno->id_cliente = $decoded->id;
            $turno->atendido = false;
            $turno->precio = $mascota->precio;
            $rta = $turno->save();
            if ($rta) {
                $response->getBody()->write("TURNO AGREGADA CON EXITO");
            } else {
                $response->getBody()->write("HUBO UN ERROR AL REGISTRAR MASCOTA");
            }
        //$response->getBody()->write(json_encode($rta));
            return $response;
        }else{
            $response->getBody()->write("HUBO UN ERROR AL REGISTRAR TURNO, TIPO MASCOTA INVALIDO O NO EXISTE");
            return $response;
        }

    }

    public function getAll(Request $request, Response $response, $args) {
        $turnos = Turno::join('usuarios', 'turnos.id_cliente', 'usuarios.id')->select('usuarios.nombre', 'turnos.tipo_mascota', 'turnos.fecha_atencion', 'turnos.precio')->get();
        
        if( $turnos != null)
        {
            
            $response->getBody()->write(json_encode($turnos));
        }
        else{
            $response->getBody()->write("HUBO UN ERROR AL MOSTRAR LOS TURNOS");
            return $response->withStatus(400);
        }
        return $response;
        
    }
    public function marcarAtendido(Request $request, Response $response, $args) {
        $idTurno = $args['idTurno'];
        $turno = Turno::find($idTurno);
        
        if( $turno != null)
        {
            //MARCA COMO ATENDIDO
            $turno->atendido = true;
            $rta = $turno->save();
            if ($rta) {
                $response->getBody()->write("CAMBIADO TURNO A ATENDIDO CON EXITO");
            } else {
                $response->getBody()->write("HUBO UN ERROR AL CAMBIAR ESTADO DEL TURNO");
            }
            return $response;
        }
        else{
            $response->getBody()->write("NO SE ENCONTRO EL TURNO");
            return $response->withStatus(400);
        }
    }

}