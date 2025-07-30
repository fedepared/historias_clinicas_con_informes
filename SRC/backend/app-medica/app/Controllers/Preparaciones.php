<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PreparacionesModel;
use CodeIgniter\API\ResponseTrait;
use Dompdf\Dompdf;
use Dompdf\Options; // Esta línea está bien para la importación del namespace
use CodeIgniter\Files\File;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Preparaciones extends BaseController
{
    use ResponseTrait;

    protected $preparacionesModel;

    public function __construct()
    {
        $this->preparacionesModel = new PreparacionesModel();

    }

  private function enviarCorreoPHPMailer($destinatario, $asunto, $mensaje, $adjuntos = [])
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP (la misma que tú tienes)
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
            $mail->addAddress($destinatario);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            // Adjuntos
            foreach ($adjuntos as $adjunto) {
                if (file_exists($adjunto)) {
                    $mail->addAttachment($adjunto);
                    log_message('debug', 'Adjuntando archivo: ' . $adjunto);
                } else {
                    log_message('error', 'Adjunto no encontrado: ' . $adjunto);
                }
            }

            $mail->send();
            return ['success' => true, 'message' => 'Correo enviado correctamente.'];
        } catch (Exception $e) {
            log_message('error', 'Error al enviar el correo (enviarCorreoPHPMailer): ' . $e->getMessage() . ' Info: ' . $mail->ErrorInfo);
            return ['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo];
        }
    }

   public function generatePreparacionPDF()
    {
        set_time_limit(300);
        log_message('debug', 'Iniciando generación de PDF de preparación para envío por correo.');

        try {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);

      
                $emailDestinatario = 'agustin.moya.4219@gmail.com';
        
            if (empty($emailDestinatario) || !filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) {
                log_message('error', 'Email de destinatario no válido o no proporcionado.');
                return $this->failValidationErrors(['email_destinatario' => 'Se requiere un correo electrónico de destinatario válido para enviar el PDF de preparación.']);
            }

            log_message('debug', 'Paso 1: Obteniendo datos de preparación del tipo "vcc_tarde" de la base de datos.');
            $response = $this->getByTipoPreparacion('vcc_tarde'); // Llamada a tu función existente

            $preparationsDataFromDB = [];
            if ($response instanceof ResponseInterface) {
                $decodedResponse = json_decode($response->getBody(), true);
                if (isset($decodedResponse['data']) && is_array($decodedResponse['data'])) {
                    $preparationsDataFromDB = $decodedResponse['data'];
                }
            } else {
                // Asume que si no es ResponseInterface, ya es un array de datos (como tu mock lo haría)
                $preparationsDataFromDB = $response['data'] ?? [];
            }

            if (empty($preparationsDataFromDB)) {
                log_message('error', 'No se encontraron datos de preparación para "vcc_tarde".');
                return $this->failNotFound('No se encontraron datos de preparación para el tipo "vcc_tarde" en la base de datos.');
            }
            log_message('debug', 'Paso 1 completado. Número de elementos de preparación obtenidos: ' . count($preparationsDataFromDB));

            $preparationsById = [];
            foreach ($preparationsDataFromDB as $prep) {
                $preparationsById[(string)$prep['id_preparacion']] = $prep;
            }

            $orderedTextObjects = [];
            $rootId = null;
            foreach ($preparationsById as $prep) {
                if ($prep['id_padre'] === null) {
                    $rootId = (string)$prep['id_preparacion'];
                    break;
                }
            }

            if ($rootId === null) {
                log_message('error', 'No se encontró un texto raíz (id_padre: null) para la preparación "vcc_tarde".');
                return $this->failServerError('No se encontró un texto raíz (id_padre: null) para la preparación "vcc_tarde".');
            }
            log_message('debug', 'Paso 2: Iniciando construcción de objetos de texto ordenados desde el ID raíz: ' . $rootId);

            $this->buildOrderedTextObjectsArray($rootId, $preparationsById, $orderedTextObjects); // Llamada a tu función existente
            log_message('debug', 'Paso 2 completado. Total de objetos de texto ordenados: ' . count($orderedTextObjects));
            log_message('debug', 'Contenido de $orderedTextObjects: ' . json_encode($orderedTextObjects, JSON_PRETTY_PRINT));

            // Cargar imágenes como Base64 (tal cual lo tienes)
            $logoPath = FCPATH . 'images/logo.png';
            $firmaPath = FCPATH . 'images/firma.png';

            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoType = mime_content_type($logoPath);
                $logoBase64 = 'data:' . $logoType . ';base64,' . base64_encode(file_get_contents($logoPath));
                log_message('debug', 'Logo cargado como Base64. Tamaño: ' . strlen($logoBase64) . ' bytes.');
            } else {
                log_message('error', 'Error: El archivo de logo no se encontró en: ' . $logoPath);
            }

            $firmaBase64 = '';
            if (file_exists($firmaPath)) {
                $firmaType = mime_content_type($firmaPath);
                $firmaBase64 = 'data:' . $firmaType . ';base64,' . base64_encode(file_get_contents($firmaPath));
                log_message('debug', 'Firma cargada como Base64. Tamaño: ' . strlen($firmaBase64) . ' bytes.');
            } else {
                log_message('error', 'Error: El archivo de firma no se encontró en: ' . $firmaPath);
            }
            log_message('debug', 'URLs de logo y firma (Base64).');


            $preparacionContentHtml = '';
            if (!empty($orderedTextObjects)) {
                log_message('debug', 'Paso 4: Construyendo el HTML con el contenido de preparación.');
                foreach ($orderedTextObjects as $prepObject) {
                    foreach ($prepObject as $fieldName => $text) {
                        $preparacionContentHtml .= "<p style='margin-top: 0px; margin-bottom: 0px; white-space: pre-line;'>" . nl2br(htmlspecialchars($text)) . "</p>";
                    }
                }
            } else {
                $preparacionContentHtml = "<p>No se encontraron textos para esta preparación.</p>";
            }
            log_message('debug', 'Paso 4 completado. Tamaño de $preparacionContentHtml (bytes): ' . strlen($preparacionContentHtml));


            $html = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; width: 100%; }
        .header-box { display: table; width: 100%; border: 2px solid #007bff; table-layout: fixed; padding: 20px; height: 10rem; }
        .header-logo { display: table-cell; width: 15%; text-align: left; vertical-align: top; position: relative; height: 10rem; }
        .header-info { margin-top:40px; display: table-cell; width: 85%; text-align: center; vertical-align: middle; font-size: 11px; }
        .logo-img { margin-top: 10px; width: 50px; height: auto; }
        .logo-caption { position: absolute; bottom: 0; left: 0; font-size: 8px; color: #888; text-align: left; line-height: 1.2; width: 100px; }
        .titulo-principal { font-size: 20px; text-align: center; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .section { margin-bottom: 15px; }
        .section-title, .section-title1 { font-size: 15px; font-weight: bold; margin-top: 10px; margin-bottom: 5px; text-decoration: underline; }
        .section-title { color: #004085; }
        .section-title1 { color: black; }
        .field { margin: 5px 0; }
        .instrucciones { color: red; font-size: 13px; font-weight: bold; margin-top: 15px; }
        .footer-box { margin-top: 30px; padding-top: 10px; border-top: 1px solid #000; font-size: 11px; text-align: center; }
        .doctor-name { color: #004085; font-size: 18px; font-weight: bold; }
        p { line-height: 1.2; }
    </style>
</head>
<body>
    <div class='header-box'>
        <div class='header-logo'>
            <img src='{$logoBase64}' class='logo-img' alt='Logo Clínica Santa Isabel'>
            <div class='logo-caption'>
                CLÍNICA SANTA ISABEL<br>
                VIDEOENDOSCOPIAS DIGESTIVAS
            </div>
        </div>
        <div class='header-info'>
            <div class='doctor-name'>DRA. ESTRIN DIANA</div>
            MN 84767 MP 334731<br>
            MEDICA ESPECIALISTA EN GASTROENTEROLOGIA, ENDOSCOPIAS DIGESTIVAS DIAGNOSTICAS Y TERAPEUTICAS<br>
            <strong>ANESTESIOLOGOS:</strong><br>
            DR GARCIA ALBERTO DANIEL MN 58499 – DRA GARCIA MACCHI MARIANA – <br> DR GIOVANETTI NICOLAS MN 140504<br>
            <strong>ASISTENTES:</strong><br>
            PALACIOS LAURA MN 3909 – POCZTER NADIA MN 8075 – MIRANDA ANDREA MN 10974
        </div>
    </div>

    <div class='titulo-principal'><br>PREPARACIÓN PARA ESTUDIO<br><br></div>

    {$preparacionContentHtml}

    <div class='footer-box'>
        <img src='{$firmaBase64}' style='width: 150px;' alt='Firma digital'><br>
        <p><strong>FIRMA DIGITAL Y SELLO</strong></p>
        <p><strong class='instrucciones'>IMPORTANTE:</strong> Lea atentamente estas instrucciones de preparación.</p>
    </div>
</body>
</html>";

            log_message('debug', 'Paso 5: HTML final construido. Tamaño total del HTML (bytes): ' . strlen($html));
            log_message('debug', 'Paso 6: Cargando HTML en Dompdf.');
            $dompdf->loadHtml($html);
            log_message('debug', 'Paso 6 completado. Estableciendo tamaño de papel.');

            $dompdf->setPaper('A4', 'portrait');
            log_message('debug', 'Paso 7: Papel establecido. Iniciando el renderizado del PDF.');

            $dompdf->render();
            log_message('debug', 'Paso 7 completado: PDF renderizado exitosamente.');

            // ***************************************************************
            // *** ESTOS SON LOS ÚNICOS CAMBIOS NECESARIOS PARA EL ENVÍO DE CORREO ***
            // ***************************************************************

            // 1. Definir la ruta donde se guardará el PDF temporalmente
            $tempDir = WRITEPATH . 'uploads/preparaciones_temp/';
            // Asegúrate de que el directorio exista
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true); // Crear con permisos de escritura (0777 para desarrollo, ajustar en producción)
                log_message('debug', 'Directorio temporal creado: ' . $tempDir);
            } else {
                log_message('debug', 'Directorio temporal ya existe: ' . $tempDir);
            }
            
            $pdfFileName = 'preparacion_vcc_tarde_' . date('Ymd_His') . '.pdf';
            $pdfFilePath = $tempDir . $pdfFileName;

            // 2. Guardar el PDF en el archivo temporal
            file_put_contents($pdfFilePath, $dompdf->output());
            log_message('debug', 'PDF de preparación guardado temporalmente en: ' . $pdfFilePath);

            // 3. Preparar datos para el envío de correo
            $asunto = 'Instrucciones de Preparación para su Estudio Médico';
            $mensaje = 'Estimado/a paciente,<br><br>';
            $mensaje .= 'Adjunto a este correo, encontrará las instrucciones detalladas para la preparación de su estudio médico.<br><br>';
            $mensaje .= 'Por favor, lea la información cuidadosamente y siga todas las indicaciones.<br><br>';
            $mensaje .= 'Saludos cordiales,<br>Clínica Santa Isabel.';

            $adjuntos = [$pdfFilePath]; // Tu array de adjuntos, con la ruta del PDF temporal

            // 4. Enviar el correo usando tu función existente
            log_message('debug', 'Paso 8: Intentando enviar correo con el PDF de preparación a: ' . $emailDestinatario);
            $mailResult = $this->enviarCorreoPHPMailer($emailDestinatario, $asunto, $mensaje, $adjuntos);

            // 5. Eliminar el archivo temporal después de intentar enviar
            if (file_exists($pdfFilePath)) {
                unlink($pdfFilePath);
                log_message('debug', 'Archivo temporal de PDF eliminado: ' . $pdfFilePath);
            }

            // 6. Devolver una respuesta JSON (ya no se descarga el PDF directamente)
            if ($mailResult['success']) {
                log_message('info', 'PDF de preparación enviado exitosamente por correo a ' . $emailDestinatario);
                return $this->respondCreated([
                    'success' => true,
                    'message' => 'PDF de preparación generado y enviado por correo a ' . $emailDestinatario,
                    'email_sent' => true,
                    'file_path_sent' => str_replace(WRITEPATH, '', $pdfFilePath) // Opcional: para referencia
                ]);
            } else {
                log_message('error', 'Fallo al enviar el PDF de preparación por correo a ' . $emailDestinatario . ': ' . $mailResult['message']);
                return $this->failServerError('PDF generado, pero hubo un error al enviar el correo: ' . $mailResult['message']);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error general al generar o enviar el PDF de preparación: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            return $this->failServerError('Ocurrió un error al generar o enviar el PDF: ' . $e->getMessage());
        }
    }


    /**
     * Función auxiliar para construir el array de objetos de texto ordenados recursivamente.
     * Se mantiene igual que en la respuesta anterior, ya que el procesamiento de la jerarquía es el mismo.
     *
     * @param string $currentId ID del elemento actual.
     * @param array $preparationsById Array de preparaciones mapeadas por ID.
     * @param array $orderedTextObjects Referencia al array donde se almacenarán los objetos de texto ordenados.
     */
    protected function buildOrderedTextObjectsArray(string $currentId, array $preparationsById, array &$orderedTextObjects)
    {
        if (!isset($preparationsById[$currentId])) {
            return;
        }

        $currentPrep = $preparationsById[$currentId];

        // Añadir el objeto con el formato "nombre_campo": "texto"
        $orderedTextObjects[] = [
            $currentPrep['nombre_campo'] => $currentPrep['texto']
        ];

        // Encontrar los hijos del elemento actual y ordenarlos por id_preparacion
        $children = [];
        foreach ($preparationsById as $prep) {
            if ((string)$prep['id_padre'] === $currentId) {
                $children[] = $prep;
            }
        }

        // Ordenar los hijos por 'id_preparacion' (ascendente)
        usort($children, function ($a, $b) {
            return (int)$a['id_preparacion'] <=> (int)$b['id_preparacion'];
        });

        // Llamar recursivamente para cada hijo
        foreach ($children as $child) {
            $this->buildOrderedTextObjectsArray((string)$child['id_preparacion'], $preparationsById, $orderedTextObjects);
        }
    }

    /**
     * Obtiene una preparación por su ID y la devuelve como JSON.
     *
     * @param int $id El ID de la preparación a buscar.
     * @return ResponseInterface
     */

    public function getByIdPreparaciones($id)
    {
        try {
            $preparacion = $this->preparacionesModel->find($id);

            if (!$preparacion) {
                return $this->failNotFound('Preparación no encontrada.');
            }

            // Convertir es_editable a booleano
            if (isset($preparacion['es_editable'])) {
                $preparacion['es_editable'] = ($preparacion['es_editable'] == "1");
            }

            return $this->respond([
                'status' => 'success',
                'data' => $preparacion
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'Error en getByIdPreparaciones: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error interno al obtener la preparación. Detalles en logs.');
        }
    }


    public function getByTipoPreparacion($tipo)
    {
        // 1. Validación del parámetro de tipo
        if (empty($tipo) || !is_string($tipo)) {
            return $this->failValidationErrors(['tipo' => 'El tipo de preparación es requerido y debe ser una cadena válida.']);
        }
        try {
            $preparaciones = $this->preparacionesModel
                ->where('tipo_preparacion', $tipo)
                ->orderBy('id_preparacion', 'ASC')
                ->findAll();

            if (empty($preparaciones)) {
                return $this->failNotFound('No se encontraron preparaciones para el tipo "' . esc($tipo) . '".');
            }

            // Convertir es_editable a booleano en cada preparación
            foreach ($preparaciones as &$prep) {
                if (isset($prep['es_editable'])) {
                    $prep['es_editable'] = ($prep['es_editable'] == "1");
                }
            }

            return $this->respond([
                'status' => 'success',
                'error' => null,
                'messages' => ['success' => 'Preparaciones encontradas exitosamente.'],
                'data' => $preparaciones
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'Error en getByTipoPreparacion: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error interno al obtener las preparaciones por tipo. Detalles en logs.');
        }
    }



    public function updateTexto($id)
    {
        try {
            // 1. Verificar si la preparación existe
            $preparacionExistente = $this->preparacionesModel->find($id);

            if (!$preparacionExistente) {
                return $this->failNotFound('Preparación no encontrada con ID: ' . $id);
            }
            $input = $this->request->getJSON();

            if (!isset($input->texto)) {
                return $this->failValidationErrors('El campo "texto" es requerido para la actualización.');
            }

            $nuevoTexto = $input->texto;
            $dataToUpdate = [
                'texto' => $nuevoTexto
            ];
            $updated = $this->preparacionesModel->update($id, $dataToUpdate);

            if ($updated) {

                $preparacionActualizada = $this->preparacionesModel->find($id);

                return $this->respond([
                    'status' => 'success',
                    'messages' => ['success' => 'Texto de preparación actualizado exitosamente.'],
                    'data' => $preparacionActualizada
                ], ResponseInterface::HTTP_OK);
            } else {

                return $this->failServerError('Fallo al actualizar la preparación.');
            }
        } catch (\ReflectionException $e) {

            log_message('error', 'ReflectionException en updateTexto: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor (ReflectionException): ' . $e->getMessage());
        } catch (\Exception $e) {

            log_message('error', 'Error general en updateTexto: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error inesperado al actualizar la preparación.');
        }
    }
}