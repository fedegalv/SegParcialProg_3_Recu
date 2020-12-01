<?php

namespace App\Middlewares;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Models\Mascota; 
class MascotaRepetidaValidate
{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
       
        $parsedBody = $request->getParsedBody();
        $tipo = $parsedBody['tipo'];

        //EMAIL UNICO
        $tipoRepetido = Mascota::where('tipo', '=', $tipo)->first();
        if($tipoRepetido != null || $tipoRepetido != false)
        {
            $response = new Response();
            $response->getBody()->write("TIPO NO ES UNICO, ES REPETIDO");
            return $response->withStatus(400);
        }
        else{
            $response = $handler->handle($request);
            $existingContent = (string)$response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
        
    }
}