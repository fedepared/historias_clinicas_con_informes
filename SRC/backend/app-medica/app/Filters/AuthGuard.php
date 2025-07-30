<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

// NO uses ResponseTrait en un filtro
// use CodeIgniter\API\ResponseTrait; // <<< Esta línea DEBE ESTAR ELIMINADA

helper('jwt'); // Asegúrate de que tu helper 'jwt' esté cargado

class AuthGuard implements FilterInterface
{
    // NO uses el trait ResponseTrait aquí
    // use ResponseTrait; // <<< Esta línea DEBE ESTAR ELIMINADA

    public function before(RequestInterface $request, $arguments = null)
    {
        log_message('debug', 'AuthGuard: Iniciando para URL: ' . $request->getURI()->getPath() . ' Metodo: ' . $request->getMethod());

        // Obtener la instancia del objeto Response del servicio
        $response = service('response');

        // *** IMPORTANTE: NO HAY LOGICA ESPECÍFICA PARA PETICIONES OPTIONS AQUÍ ***
        // Se asume que el filtro 'cors' ya manejó las peticiones OPTIONS
        // y que el filtro 'auth' ha sido excluido para peticiones OPTIONS en Config/Filters.php.
        // Si una petición OPTIONS llega hasta aquí, algo en la configuración de filtros no está bien.

        // --- Lógica de autenticación JWT para otros métodos (GET, POST, PUT, DELETE) ---
        log_message('debug', 'AuthGuard: Validando token para metodo: ' . $request->getMethod());
        $header = $request->getHeaderLine('Authorization');
        log_message('debug', 'AuthGuard: Authorization Header: ' . $header);

        $token = null;

        if (empty($header) || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            log_message('error', 'AuthGuard: Token JWT no proporcionado o formato incorrecto. Header: ' . $header);
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Acceso no autorizado: Token JWT no proporcionado o formato incorrecto.'
            ]);
        }

        $token = $matches[1];
        log_message('debug', 'AuthGuard: Extracted Token: ' . $token);

        // 2. Validar el Token JWT
        try {
            $decodedToken = decodeJWT($token);
            log_message('debug', 'AuthGuard: Decoded Token: ' . json_encode($decodedToken));

            if (is_null($decodedToken)) {
                log_message('error', 'AuthGuard: Token inválido o expirado o error de decodificación.');
                return $response->setStatusCode(401)->setJSON([
                    'status'  => 'error',
                    'code'    => 401,
                    'message' => 'Acceso no autorizado: Token inválido o expirado.'
                ]);
            }

            // Opcional: Almacenar los datos decodificados en el Request para usar en controladores
            $request->decodedJWT = $decodedToken;

            log_message('debug', 'AuthGuard: Token válido. Permitiendo acceso.');
            return; // Permite la ejecución de la ruta

        } catch (Exception $e) {
            log_message('error', 'AuthGuard: Excepcion JWT: ' . $e->getMessage());
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Acceso no autorizado: ' . $e->getMessage()
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita lógica aquí
    }
}