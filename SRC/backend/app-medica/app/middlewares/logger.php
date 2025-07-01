<?php/* 
use Slim\Psr7\Response as Response;

class Logger
{

    private static function validarToken($request)
    {
        $header = $request->getHeaderLine('Authorization');
        if (empty($header)) {
            return null; // Token vacío
        }

        $token = trim(explode("Bearer", $header)[1]);
        return $token;
    }

    private static function manejarExcepcion($response, $e)
    {
        $payload = json_encode(array('error' => $e->getMessage()));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificadorUsuariosRegistrados($request, $handler)
    {
        $token = self::validarToken($request);
        if ($token === null) {
            $response = new Response();
            $response->getBody()->write(json_encode(array('error' => 'token vacio')));
            return $response->withHeader('Content-Type', 'application/json');
        }

        try {
            AutentificadorJWT::VerificarToken($token);
            return $handler->handle($request);
        } catch (Exception $e) {
            return self::manejarExcepcion(new Response(), $e);
        }
    }

    public static function VerificadorAdmin($request, $handler)
    {
        $token = self::validarToken($request);
        if ($token === null) {
            $response = new Response();
            $response->getBody()->write(json_encode(array('error' => 'token vacio')));
            return $response->withHeader('Content-Type', 'application/json');
        }

        try {
            $payload = AutentificadorJWT::ObtenerData($token);
            if ($payload->perfil === 'administrador') {
                return $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array('error' => 'error de autenticacion')));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            return self::manejarExcepcion(new Response(), $e);
        }
    }

    public static function VerificadorMozo($request, $handler)
    {
        $token = self::validarToken($request);
        if ($token === null) {
            $response = new Response();
            $response->getBody()->write(json_encode(array('error' => 'token vacio')));
            return $response->withHeader('Content-Type', 'application/json');
        }

        try {
            $payload = AutentificadorJWT::ObtenerData($token);
            if ($payload->perfil === 'mozo') {
                return $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array('error' => 'error de autenticacion')));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            return self::manejarExcepcion(new Response(), $e);
        }
    }

    public static function VerificadorAdminOMozo($request, $handler)
    {
        $token = self::validarToken($request);
        if ($token === null) {
            $response = new Response();
            $response->getBody()->write(json_encode(array('error' => 'token vacio')));
            return $response->withHeader('Content-Type', 'application/json');
        }

        try {
            $payload = AutentificadorJWT::ObtenerData($token);
            if ($payload->perfil === 'administrador' || $payload->perfil === 'mozo') {
                return $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array('error' => 'error de autenticacion')));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            return self::manejarExcepcion(new Response(), $e);
        }
    }

    public static function PerfilEmpleado($request)
    {
        $token = self::validarToken($request);
        if ($token === null) {
            return null; // Token vacío
        }

        $data = AutentificadorJWT::ObtenerData($token);
        return $data->perfil;
    }

} */
