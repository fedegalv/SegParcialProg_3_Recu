<?php

namespace App\Middlewares;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Models\Usuario; 
class DatosValidosUsuario
{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        $valido = false; //VALIDAR EMAIL
        $parsedBody = $request->getParsedBody();
        $email= $parsedBody['email'];
        $nombre = $parsedBody['nombre'];
        $clave = $parsedBody['clave'];
        $tipo = $parsedBody['tipo'];

        //EMAIL UNICO
        $emailRepetido = Usuario::where('email', '=', $email)->first();
        if($emailRepetido != null || $emailRepetido != false)
        {
            $response = new Response();
            $response->getBody()->write("EMAIL REPETIDO");
            return $response->withStatus(400);
        }
        //CHECKEA SI NOMBRE TIENE ESPACIOS
        if ( preg_match('/\s/',$nombre)) {
            $response = new Response();
            $response->getBody()->write("NOMBRE CONTIENE ESPACIOS");
            return $response->withStatus(400);
        }
        //NOMBRE REPETIDO
        $nombreRepetido = Usuario::where('nombre', '=', $nombre)->first();
        if($nombreRepetido != null || $nombreRepetido != false)
        {
            $response = new Response();
            $response->getBody()->write("NOMRE REPETIDO");
            return $response->withStatus(400);
        }
        //CLAVE 4 AL MENOS 4 CHARS
        if(strlen($clave) < 4)
        {
            $response = new Response();
            $response->getBody()->write("CLAVE MENOS 4 CARACTERES");
            return $response->withStatus(400);
        }
        //TIPO ES VALIDO
        if($tipo == 'cliente' || $tipo == 'admin')
        {
            //SI TODO SALE BIEN
            $response = $handler->handle($request);
            $existingContent = (string)$response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
        else{
            $response = new Response();
            $response->getBody()->write("TIPO INVALIDO");

            return $response->withStatus(400);
        }

        
        
    }
}