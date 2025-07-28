<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CORS implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin') ?: '*';

    // Agregar headers directamente a la salida
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');

    // Terminar ejecución para preflight (OPTIONS)
    if ($request->getMethod() === 'options') {
        http_response_code(200);
        exit;
    }
    //   $origin = $request->getHeaderLine('Origin') ?: '*';
    // $response = service('response');

    // $response->setHeader('Access-Control-Allow-Origin', $origin);
    // $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    // $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    // $response->setHeader('Access-Control-Allow-Credentials', 'true');

    
    // if ($request->getMethod() === 'options') {
    //     return $response->setStatusCode(200);
    // }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
