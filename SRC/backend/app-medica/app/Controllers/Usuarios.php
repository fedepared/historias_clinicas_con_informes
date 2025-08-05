<?php

namespace App\Controllers;


use CodeIgniter\HTTP\ResponseInterface;
use PHPMailer\PHPMailer\PHPMailer;
use App\Models\UsuariosModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Usuarios extends BaseController
{
    use ResponseTrait;
    private $UsuariosModel;

    public function __construct()
    {

        $this->UsuariosModel = new UsuariosModel();
    }
    public function options()
    {

        return $this->response->setStatusCode(200);
    }
    public function verificarSesion()
    {
        if (!session()->has('id_usuario')) {
            return $this->response->setJSON(['status' => 'expirado']);
        }

        return $this->response->setJSON(['status' => 'activo']);
    }


    private function enviarCorreoPHPMailer($to, $subject, $message)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';  // Verificá que sea el correcto
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return 'Correo enviado correctamente';
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            return 'Error al enviar el correo: ' . $mail->ErrorInfo;
        }
    }

    public function solicitarCambioPassword()
    {
        try {
            // 1. Obtener el correo electrónico de la solicitud (asumiendo JSON)
            $input = $this->request->getJSON(true);
            $userMail = $input['mail'] ?? null; // Usa el operador null coalescing para seguridad

            // REEMPLAZO DE failValidationError
            if (empty($userMail) || !filter_var($userMail, FILTER_VALIDATE_EMAIL)) {
                return $this->fail('Debe proporcionar una dirección de correo electrónico válida.', ResponseInterface::HTTP_BAD_REQUEST); // <-- CAMBIO AQUÍ
            }

            // 3. Buscar al usuario por correo electrónico
            $usuario = $this->UsuariosModel->getByMail($userMail);

            if (!$usuario) {
                // Es una buena práctica no revelar si el correo existe o no por seguridad.
                // Podrías responder con un mensaje genérico de "Si la dirección de correo existe..."
                // Pero para desarrollo, un 404 es útil.
                return $this->failNotFound('Usuario no encontrado con ese correo electrónico.');
            }

            // 4. Generar el código de verificación aleatorio
            $codigoCambio = random_int(100000, 999999);

            // 5. Datos a actualizar en la base de datos
            // ¡IMPORTANTE! NO DEBES HASHEAR LA CONTRASEÑA EN ESTE PASO.
            // La contraseña en 'pass' debe ser la original del usuario, no el código aux.
            // Solo actualizas 'pass_aux' y 'pidio_cambio'.
            $dataUpdate = [
                'pass_aux' => $codigoCambio,
                'pidio_cambio' => true,
                // 'pass' => password_hash($codigoCambio, PASSWORD_DEFAULT) // <-- ¡ELIMINAR ESTA LÍNEA!
                // Esto sobreescribiría la contraseña real del usuario con el código.
            ];

            // 6. Actualiza el usuario con su id_usuario específico
            // Usa el ID del usuario encontrado, no un ID fijo (como el '1' que tenías antes).
            if (!$this->UsuariosModel->update($usuario['id_usuario'], $dataUpdate)) {
                return $this->failServerError('Error al guardar el código de verificación.');
            }

            // 7. Preparar y enviar el correo electrónico
            $asunto = 'Solicitud de Cambio de Contraseña';
            $mensaje = "
                <p>Hola <strong>{$usuario['nombre_usuario']}</strong>,</p>
                <p>Has solicitado cambiar tu contraseña. Tu código de verificación es:</p>
                <h2>$codigoCambio</h2>
                <p>Utiliza este código para establecer una nueva contraseña.</p>
                <p>Si no solicitaste este cambio, simplemente ignora este correo.</p>
            ";

            // Enviar el correo (asumiendo que enviarCorreoPHPMailer está definido y es accesible)
            $resultadoCorreo = $this->enviarCorreoPHPMailer($usuario['mail'], $asunto, $mensaje);

            if (strpos($resultadoCorreo, 'Correo enviado correctamente') !== false) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Se ha enviado un código de verificación a tu correo electrónico.',
                ], ResponseInterface::HTTP_OK); // 200 OK
            } else {
                // 8. Revertir cambios si falla el envío del correo
                // IMPORTANTE: Asegúrate de que el 'pass' no haya sido modificado.
                $this->UsuariosModel->update($usuario['id_usuario'], [
                    'pass_aux' => null,
                    'pidio_cambio' => false,
                    // No revertir 'pass' aquí, porque no deberíamos haberlo cambiado inicialmente.
                ]);

                return $this->failServerError('Error al enviar el correo: ' . $resultadoCorreo);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción inesperada
            return $this->failServerError('Ocurrió un error inesperado al procesar la solicitud: ' . $e->getMessage());
        }
    }


    public function verificarYActualizarPassword()
    {
        $model = new UsuariosModel();
        $data = $this->request->getJSON(true);

        // Validar datos de entrada
        if (
            !$data ||
            !isset($data['mail']) ||
            !isset($data['codigo']) ||
            !isset($data['password_nuevo'])
        ) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Se requieren el correo, el código y la nueva contraseña.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $mail = trim($data['mail']);
        $codigo = trim($data['codigo']);
        $passwordNuevo = trim($data['password_nuevo']);

        // Buscar al usuario (asumimos ID 1 por ahora)
        $usuario = $model->find(1);

        if (
            !$usuario ||
            $usuario['mail'] !== $mail ||
            $usuario['pass_aux'] !== $codigo ||
            !$usuario['pidio_cambio']
        ) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Datos inválidos o el código ha expirado.'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Actualizar la contraseña
        $passwordHashNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);
        $dataUpdate = [
            'pass' => $passwordHashNuevo,
            'pass_aux' => null,
            'pidio_cambio' => false,
        ];

        if ($model->update($usuario['id_usuario'], $dataUpdate)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Contraseña actualizada exitosamente.'
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar la contraseña en la base de datos.'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cambiarPassword()
    {

        $idUsuario = session()->get('id_usuario');

        // 2. Verificar que el usuario haya iniciado sesión
        if (!$idUsuario) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Acceso no autorizado. Debes iniciar sesión.'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED); // 401 Unauthorized
        }

        $model = new UsuariosModel(); // Instanciamos el modelo de usuarios
        $data = $this->request->getJSON(true);
        // Verificar si se recibieron los datos correctamente
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Validar que los datos requeridos estén presentes
        if (!isset($data['password_nuevo']) || !isset($data['password_confirmar'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: password_nuevo, password_confirmar'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }


        // $passwordActual = $data['password_actual'];
        $passwordNuevo = $data['password_nuevo'];
        $passwordConfirmar = $data['password_confirmar'];

        // Buscar el usuario por ID
        $usuario = $model->find($idUsuario);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }


        // Verificar si la nueva contraseña y la confirmación coinciden
        if ($passwordNuevo !== $passwordConfirmar) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La nueva contraseña y la confirmación no coinciden'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }


        // Hashear la nueva contraseña
        $passwordHashNuevo = password_hash($passwordNuevo, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $dataUpdate = [
            'pass' => $passwordHashNuevo,
            'pass_aux' => null,
            'pidio_cambio' => false,
        ];

        if ($model->update($idUsuario, $dataUpdate)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Contraseña actualizada exitosamente'
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar la contraseña'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function login()
    {

        $model = new UsuariosModel();

        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (!isset($data['nombre_usuario']) || !isset($data['pass'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos requeridos: nombre_usuario, pass'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $usuario = $model->where('nombre_usuario', $data['nombre_usuario'])->first();

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if (!password_verify($data['pass'], $usuario['pass'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Contraseña incorrecta'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // --- Lógica JWT ---
        // Prepara los claims para el token.
        // EVITA poner información sensible o mucha información en el JWT si no es necesario.
        // 'id_usuario' y 'nombre_usuario' son claims comunes.
        $payload = [
            'id' => $usuario['id_usuario'], // 'uid' es un nombre común para user ID
            'username' => $usuario['nombre_usuario'],
            'email' => $usuario['mail'], // Opcional: si necesitas el email en el frontend sin otra petición
            'pidio_cambio' => $usuario['pidio_cambio'], // Puedes incluir otros datos relevantes
            'duracion_sesion' => 7200, 
        ];

        $jwtToken = generateJWT($payload); // Usa la función helper para generar el JWT

        // Si las credenciales son correctas, devolver una respuesta de éxito con el JWT
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso',
            'token' => $jwtToken, // Envía el JWT al frontend
            
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }



    public function logout()
    {

        session()->destroy();


        return $this->respond([
            'status' => 'success',
            'message' => 'Sesión cerrada exitosamente.'
        ], ResponseInterface::HTTP_OK); // Devuelve un código 200 OK

    }


    // 🔹 Obtener todos los usuarios
    public function getUsuarios()
    {
        $usuarios = $this->UsuariosModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $usuarios
        ]);
    }

    // 🔹 Obtener un usuario por ID
    public function getByIdUsuarios($id)
    {
        $usuario = $this->UsuariosModel->find($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $usuario
        ]);
    }

    // --- Función para crear un nuevo usuario con verificación de correo ---
    public function postUsuarios()
    {
        try {
            // 1. Obtener los datos del body de la solicitud (asumiendo JSON para APIs REST)
            $data = $this->request->getJSON(true);

            // Verificar si se recibieron datos
            if (empty($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se recibieron datos para crear el usuario.'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // 2. Validar campos mínimos (puedes añadir más validación con rules en el modelo)
            $requiredFields = ['nombre_usuario', 'pass', 'mail'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty(trim($data[$field]))) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => "El campo '{$field}' es requerido."
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                }
            }

            // 3. Sanitizar y limpiar datos (ej. trim)
            $nombreUsuario = trim($data['nombre_usuario']);
            $mailUsuario   = trim($data['mail']);
            $passUsuario   = $data['pass'];

            // 4. HASHEAR LA CONTRASEÑA (¡MUY IMPORTANTE!)
            $hashedPassword = password_hash($passUsuario, PASSWORD_DEFAULT);

            // 5. Verificar si el mail ya existe utilizando el método del modelo
            // Usa $this->usuariosModel para acceder a tu modelo
            $usuarioExistente = $this->UsuariosModel->getByMail($mailUsuario);

            if ($usuarioExistente) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Ya existe un usuario registrado con este correo electrónico.'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // 6. Preparar los datos para insertar
            $insertData = [
                'nombre_usuario' => $nombreUsuario,
                'pass'           => $hashedPassword,
                'mail'           => $mailUsuario,
                'pidio_cambio'   => 0,
                'pass_aux'       => null
            ];

            // 7. Intentar insertar el nuevo usuario
            if ($this->UsuariosModel->insert($insertData)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Usuario creado exitosamente.'
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error al crear el usuario.',
                    'errors'  => $this->UsuariosModel->errors()
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado al crear el usuario: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // 🔹 Eliminar un usuario por ID
    public function deleteUsuarios($id)
    {
        $usuario = $this->UsuariosModel->find($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $this->UsuariosModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    public function updateUsuarios($id)
    {
        try {
            // 1. Verificar si el usuario existe antes de intentar actualizar
            $usuarioExistente = $this->UsuariosModel->find($id);

            if (!$usuarioExistente) {
                return $this->failNotFound('Usuario no encontrado.'); // 404 Not Found
            }

            // 2. Obtener los datos del cuerpo de la solicitud (JSON)
            $input = $this->request->getJSON(true);

            // 3. Preparar los datos para la actualización
            $dataToUpdate = [];

            // Solo agrega los campos si están presentes en la solicitud y son permitidos
            // y NO son campos de seguridad como 'pass', 'pidio_cambio' o 'pass_aux'.
            if (isset($input['nombre_usuario']) && !empty(trim($input['nombre_usuario']))) {
                $dataToUpdate['nombre_usuario'] = trim($input['nombre_usuario']);
            }

            if (isset($input['mail']) && !empty(trim($input['mail']))) {
                $mail = trim($input['mail']);
                // Opcional: Validar el formato del correo si no lo haces en el modelo
                if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    return $this->fail('El formato del correo electrónico no es válido.', ResponseInterface::HTTP_BAD_REQUEST);
                }
                $dataToUpdate['mail'] = $mail;
            }

            // 4. Verificar si hay datos para actualizar
            if (empty($dataToUpdate)) {
                return $this->fail('No se proporcionaron datos válidos para actualizar el usuario.', ResponseInterface::HTTP_BAD_REQUEST);
            }

            // 5. Verificar si el nuevo correo ya está en uso por otro usuario (si se está actualizando el correo)
            if (isset($dataToUpdate['mail']) && $dataToUpdate['mail'] !== $usuarioExistente['mail']) {
                $usuarioConMismoMail = $this->UsuariosModel->getByMail($dataToUpdate['mail']);
                if ($usuarioConMismoMail && $usuarioConMismoMail['id_usuario'] != $id) {
                    return $this->fail('Ya existe un usuario con este correo electrónico.', ResponseInterface::HTTP_CONFLICT);
                }
            }

            // 6. Realizar la actualización
            if ($this->UsuariosModel->update($id, $dataToUpdate)) {
                // Recuperar el usuario actualizado para la respuesta (opcional, pero útil)
                $usuarioActualizado = $this->UsuariosModel->find($id);

                return $this->respond([
                    'status'  => 'success',
                    'message' => 'Usuario actualizado correctamente',
                    'data'    => $usuarioActualizado
                ], ResponseInterface::HTTP_OK);
            } else {
                // Si la actualización falló (ej. por reglas de validación en el modelo)
                return $this->failServerError('Error al actualizar el usuario: ' . json_encode($this->UsuariosModel->errors()));
            }
        } catch (\Exception $e) {
            // Manejo de excepciones generales
            return $this->failServerError('Ocurrió un error inesperado al actualizar el usuario: ' . $e->getMessage());
        }
    }
}
