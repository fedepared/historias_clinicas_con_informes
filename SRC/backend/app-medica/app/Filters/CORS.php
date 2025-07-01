<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CORS implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Esto se ejecutará ANTES de que el controlador procese la solicitud.

        // Establece los encabezados que permiten el acceso desde tu origen de Angular
        header('Access-Control-Allow-Origin: http://localhost:62491'); // <--- ¡Importante! Tu origen de Angular
        header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Allow: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Credentials: true'); // Necesario si usas cookies o sesiones

        // Manejar solicitudes pre-vuelo OPTIONS
        // Si la solicitud es OPTIONS, respondemos con 200 OK y terminamos el script.
        if ($request->getMethod() === 'OPTIONS') {
            // Detenemos la ejecución aquí, ya que la solicitud OPTIONS no necesita
            // ser procesada por el controlador o la lógica de la aplicación.
            // Los encabezados ya han sido enviados.
            die();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Esto se ejecutará DESPUÉS de que el controlador haya procesado la solicitud.
        // Puedes agregar encabezados aquí si es necesario, pero para CORS 'before' suele ser suficiente.
        // CodeIgniter agregará los encabezados establecidos en 'before' a la respuesta final.
    }
}
