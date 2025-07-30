<?php

// app/Helpers/jwt_helper.php

// Importar las clases de JWT para asegurar que estén disponibles
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // Importa la clase Key, necesaria para JWT::decode en versiones recientes

if (!function_exists('getJWTSecret')) {
    function getJWTSecret()
    {
        // Busca la clave secreta JWT en las variables de entorno
        $secret = env('JWT_SECRET');

        if (empty($secret)) {
            // En un entorno de producción, esto debería lanzar una excepción
            // o loguear un error crítico. Para desarrollo, un mensaje simple.
            log_message('error', 'JWT_SECRET no está definido en el archivo .env');
            // Considera una clave por defecto para desarrollo o lanza una excepción.
            // Es CRÍTICO que en producción esta clave esté definida y sea fuerte.
            return 'default_secret_for_development_only';
        }
        return $secret;
    }
}

if (!function_exists('generateJWT')) {
    function generateJWT(array $payload)
    {
        $key = getJWTSecret();
        $iat = time(); // Issued at: tiempo en que el token fue emitido
        $exp = $iat + (3600 * 24); // Expiration time: 24 horas a partir de ahora (puedes ajustar esto)

        $payload = array_merge($payload, [
            'iat' => $iat,
            'exp' => $exp,
            'iss' => base_url(), // Emisor (issuer), tu dominio
            // 'aud' => 'your_audience', // Audiencia, si tuvieras varias apps consumiendo la API
        ]);

        // Usa la clase JWT importada
        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('decodeJWT')) {
    function decodeJWT(string $jwtToken)
    {
        $key = getJWTSecret();
        try {
            // Usa la clase Key importada para la clave secreta
            // y la clase JWT importada para decodificar.
            return (array) JWT::decode($jwtToken, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            // Puedes loguear el error o manejarlo de otra forma
            log_message('error', 'Error al decodificar JWT: ' . $e->getMessage());
            return null; // O lanza una excepción, dependiendo de cómo quieras manejar errores
        }
    }
}

