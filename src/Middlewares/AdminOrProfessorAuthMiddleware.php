<?php

namespace App\Middlewares;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use \Firebase\JWT\JWT;

class AdminOrProfessorAuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        //ASI SE PUEDE OBTENER TOKEN
        $token =  $request->getHeader('token');
        $valido= false;
        try {
            $decoded = JWT::decode($token[0], "segundoParcial", array('HS256'));
            //var_dump($decoded);
            if($decoded->tipo == 'admin' || $decoded->tipo == 'profesor'){
                //echo "entre en valido";
                $valido= true;
            }
        } catch (\Throwable $th) {
            //echo "Entro en el catch";
            $valido = false;
        }
        
        if(!$valido)
        {
            $response = new Response();
            $response->getBody()->write("TOKEN O AUTENTICACION INVALIDA, NO ES ADMIN O PROFESOR");
            //throw new \Slim\Exception\HttpForbiddenException($request);
            return $response->withStatus(403);
        }else{
            //echo "Entro en valido";
            $response = $handler->handle($request);
            $existingContent = (string)$response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
    }
}