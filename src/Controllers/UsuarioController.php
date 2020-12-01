<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Firebase\JWT\JWT;


use App\Models\Usuario;

class UsuarioController
{
    public function registro(Request $request, Response $response)
    {
        $parsedBody = $request->getParsedBody();
        $usuario = new Usuario();
        $usuario->email = $parsedBody['email'];
        $usuario->clave = $parsedBody['clave'];
        $usuario->tipo = $parsedBody['tipo'];
        $usuario->nombre = $parsedBody['nombre'];

        $rta = $usuario->save();
        if ($rta) {
            $response->getBody()->write("USUARIO REGISTRADO CON EXITO");
        } else {
            $response->getBody()->write("HUBO UN ERROR AL REGISTRAR");
            return $response->withStatus(400);
        }
        //$response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function login(Request $request, Response $response)
    {

        $parsedBody = $request->getParsedBody();
        //$email = $parsedBody['email'];
        //$nombre = $parsedBody['nombre'];
        $clave = $parsedBody['clave'];
        if (isset($parsedBody['email'])) {

            $email = $parsedBody['email'];
            $rtaEmail = Usuario::where('email', '=', $email)
                ->where('clave', '=', $clave)->first();
            if ($rtaEmail != null || $rtaEmail != false) {
                $tipo = $rtaEmail->tipo;
                $payload = array(
                    "email" => $email,
                    "tipo" => $tipo,
                    "id" => $rtaEmail->id
                );
                $jwt = JWT::encode($payload, "segundoParcial");
                $response->getBody()->write(json_encode($jwt));
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("LOGIN INCORRECTO");
                return $response->withStatus(400);
                return $response;
            }
        } else if (isset($parsedBody['nombre'])) {
            //COMPARA SI EL MAIL Y CLVE ESTAN EN BD
            $nombre = $parsedBody['nombre'];
            $rtaNombre = Usuario::where('nombre', '=', $nombre)
                ->where('clave', '=', $clave)->first();
            if ($rtaNombre != null || $rtaNombre != false) {
                $tipo = $rtaNombre->tipo;
                $payload = array(
                    "nombre" => $nombre,
                    "tipo" => $tipo,
                    "id" => $rtaNombre->id
                );
                $jwt = JWT::encode($payload, "segundoParcial");
                $response->getBody()->write(json_encode($jwt));
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("LOGIN INCORRECTO");
                return $response->withStatus(400);
                return $response;
            }
        }
        $response->getBody()->write("NOMRE O EMAIL INVALIDO");
        return $response->withStatus(400);
    }
}
