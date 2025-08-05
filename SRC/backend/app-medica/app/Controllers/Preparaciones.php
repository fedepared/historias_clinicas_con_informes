<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PreparacionesModel;

use Dompdf\Dompdf;
use Dompdf\Options; // Esta línea está bien para la importación del namespace
use CodeIgniter\Files\File;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

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

            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;
     
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

    public function sendCuestionarioWordEmail()
    {
        log_message('debug', 'Iniciando envío de cuestionario Word por correo.');

        try {
            $json = $this->request->getJSON();
            if (!$json || !isset($json->email_destinatario)) {
                $response = [
                    'status'   => 400,
                    'error'    => true,
                    'message' => 'Se requiere un correo electrónico de destinatario en el cuerpo de la solicitud JSON.'
                ];
                return $this->response->setStatusCode(400)->setJSON($response);
            }

            $emailDestinatario = $json->email_destinatario;

            // 3. Validar el formato del email
            if (!filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'status'   => 400,
                    'error'    => true,
                    'message' => 'El formato del correo electrónico proporcionado no es válido.'
                ];
                log_message('error', 'Formato de email de destinatario inválido: ' . $emailDestinatario);
                return $this->response->setStatusCode(400)->setJSON($response);
            }
            $wordFilePath = FCPATH . 'quest/cuestionario_editado.docx';

            log_message('debug', 'Ruta del archivo Word a adjuntar: ' . $wordFilePath);
            if (!file_exists($wordFilePath)) {
                $response = [
                    'status'   => 404,
                    'error'    => true,
                    'message' => 'El archivo del cuestionario Word no se encontró en el servidor.'
                ];
                log_message('error', 'Error: El archivo de cuestionario Word no se encontró en la ruta esperada: ' . $wordFilePath);
                return $this->response->setStatusCode(404)->setJSON($response);
            }

            $asunto = 'Cuestionario de Salud Importante - Clínica Santa Isabel';
            $mensaje = 'Estimado/a paciente,<br><br>';
            $mensaje .= 'Adjunto a este correo, encontrará un cuestionario de salud importante que le solicitamos completar antes de su próxima cita.<br><br>';
            $mensaje .= 'Por favor, descárguelo, complételo y tráigalo el día de su consulta.<br><br>';
            $mensaje .= 'Saludos cordiales,<br>Clínica Santa Isabel.';

            $adjuntos = [$wordFilePath]; // Array con la ruta del archivo Word
            $mailResult = $this->enviarCorreoPHPMailer($emailDestinatario, $asunto, $mensaje, $adjuntos);

            if ($mailResult['success']) {
                $response = [
                    'status' => true,
                    'error'  => false,
                    'message' => 'Cuestionario Word enviado por correo a ' . $emailDestinatario,
                    'email_sent' => true,
                    'file_path_sent' => str_replace(FCPATH, 'public/', $wordFilePath)
                ];
              
                return $this->response->setStatusCode(201)->setJSON($response);
            } else {
                $response = [
                    'status' => 500,
                    'error'  => true,
                    'message' => 'Hubo un error al enviar el cuestionario Word por correo: ' . $mailResult['message']
                ];
      
                return $this->response->setStatusCode(500)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error'  => true,
                'message' => 'Ocurrió un error al procesar la solicitud de envío del cuestionario Word: ' . $e->getMessage()
            ];
            log_message('error', 'Error general al enviar el cuestionario Word: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON($response);
        }
    }


    public function generatePreparacionPDF()
    {
        set_time_limit(300);

        try {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options); 
            $json = $this->request->getJSON();
            $emailDestinatario = $json->email_destinatario ?? null;
            $preparationsDataFromDB = $json->preparacion ?? [];

            if (empty($emailDestinatario) || !filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) {
                return $this->failValidationErrors(['email_destinatario' => 'Se requiere un correo electrónico de destinatario válido para enviar el PDF de preparación.']);
            }
            if (empty($preparationsDataFromDB)) {
                return $this->failNotFound('No se encontraron datos de preparación en el cuerpo de la solicitud.');
            }
            $preparationsById = [];
            foreach ($preparationsDataFromDB as $prep) {
                $preparationsById[(string)$prep->id_preparacion] = $prep;
            }

            $orderedTextObjects = [];
            $rootId = null;
            foreach ($preparationsById as $prep) {
                if ($prep->id_padre === null) {
                    $rootId = (string)$prep->id_preparacion;
                    break;
                }
            }

            if ($rootId === null) {
        
                return $this->failServerError('No se encontró un texto raíz para la preparación. No se puede generar el PDF.');
            }
            log_message('debug', 'Paso 2: Iniciando construcción de objetos de texto ordenados desde el ID raíz: ' . $rootId);

            $this->buildOrderedTextObjectsArray($rootId, $preparationsById, $orderedTextObjects);
    
            $logoPath = FCPATH . 'images/logo.png';
            $firmaPath = FCPATH . 'images/firma.png';

            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoType = mime_content_type($logoPath);
                $logoBase64 = 'data:' . $logoType . ';base64,' . base64_encode(file_get_contents($logoPath));
            } else {
                log_message('error', 'Error: El archivo de logo no se encontró en: ' . $logoPath);
            }

            $firmaBase64 = '';
            if (file_exists($firmaPath)) {
                $firmaType = mime_content_type($firmaPath);
                $firmaBase64 = 'data:' . $firmaType . ';base64,' . base64_encode(file_get_contents($firmaPath));
            } else {
                log_message('error', 'Error: El archivo de firma no se encontró en: ' . $firmaPath);
            }


            $preparacionContentHtml = '';
            if (!empty($orderedTextObjects)) {
                foreach ($orderedTextObjects as $prepObject) {
                    foreach ($prepObject as $fieldName => $text) {
                        $preparacionContentHtml .= "<p style='margin-top: 0px; margin-bottom: 0px; white-space: pre-line;'>" . nl2br(htmlspecialchars($text)) . "</p>";
                    }
                }
            } else {
                $preparacionContentHtml = "<p>No se encontraron textos para esta preparación.</p>";
            }

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

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $tempDir = WRITEPATH . 'uploads/preparaciones_temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
                log_message('debug', 'Directorio temporal creado: ' . $tempDir);
            } else {
                log_message('debug', 'Directorio temporal ya existe: ' . $tempDir);
            }

            $pdfFileName = 'preparacion_' . date('Ymd_His') . '.pdf';
            $pdfFilePath = $tempDir . $pdfFileName;
            file_put_contents($pdfFilePath, $dompdf->output());
            $asunto = 'Instrucciones de Preparación para su Estudio Médico';
            $mensaje = 'Estimado/a paciente,<br><br>';
            $mensaje .= 'Adjunto a este correo, encontrará las instrucciones detalladas para la preparación de su estudio médico.<br><br>';
            $mensaje .= 'Por favor, lea la información cuidadosamente y siga todas las indicaciones.<br><br>';
            $mensaje .= 'Saludos cordiales,<br>Clínica Santa Isabel.';

            $adjuntos = [$pdfFilePath];

            $mailResult = $this->enviarCorreoPHPMailer($emailDestinatario, $asunto, $mensaje, $adjuntos);
            if (file_exists($pdfFilePath)) {
                unlink($pdfFilePath);
             
            }
            if ($mailResult['success']) {
                return $this->respondCreated([
                    'success' => true,
                    'message' => 'PDF de preparación generado y enviado por correo a ' . $emailDestinatario,
                    'email_sent' => true,
                    'file_path_sent' => str_replace(WRITEPATH, '', $pdfFilePath)
                ]);
            } else {
                return $this->failServerError('PDF generado, pero hubo un error al enviar el correo: ' . $mailResult['message']);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ocurrió un error al generar o enviar el PDF: ' . $e->getMessage());
        }
    }

    protected function buildOrderedTextObjectsArray(string $currentId, array $preparationsById, array &$orderedTextObjects)
    {
        if (!isset($preparationsById[$currentId])) {
            return;
        }

        $currentPrep = $preparationsById[$currentId];

        $orderedTextObjects[] = [
            $currentPrep->nombre_campo => $currentPrep->texto
        ];

        $children = [];
        foreach ($preparationsById as $prep) {

            if ($prep->id_padre === $currentPrep->id_preparacion) {
                $children[] = $prep;
            }
        }

        usort($children, function ($a, $b) {
            return (int)$a->id_preparacion <=> (int)$b->id_preparacion;
        });
        foreach ($children as $child) {
            $this->buildOrderedTextObjectsArray((string)$child->id_preparacion, $preparationsById, $orderedTextObjects);
        }
    }

    public function getByIdPreparaciones($id)
    {
        try {
            $preparacion = $this->preparacionesModel->find($id);

            if (!$preparacion) {
                return $this->failNotFound('Preparación no encontrada.');
            }
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