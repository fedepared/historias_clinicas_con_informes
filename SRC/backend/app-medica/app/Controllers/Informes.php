<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InformesModel;
use App\Controllers\Coberturas;
use App\Models\CoberturasModel;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\HTTP\ResponseInterface;
use ZipArchive;
use DateTime;
use CodeIgniter\Files\File; // Necesario para trabajar con rutas de archivos
class Informes extends BaseController
{
    protected $InformesModel;
    protected $CoberturasModel;

    public function __construct()
    {
        $this->InformesModel = new InformesModel();
        $this->CoberturasModel = new CoberturasModel();
    }
    function enviarCorreoPrueba()
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';  // Verificá que sea el correcto
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Remitente (debe coincidir con Username para evitar bloqueos)
            $mail->setFrom('estudio@dianaestrin.com', 'Diana Estrin');

            // Destinatario (probá primero con otro que no sea Gmail si sigue sin funcionar)
            $mail->addAddress('agustin.moya.4219@gmail.com', 'Agustin');

            // Cabecera opcional
            $mail->addReplyTo('estudio@dianaestrin.com', 'No responder');

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Correo de prueba desde PHPMailer';
            $mail->Body    = '<b>¡Hola!</b><br>Este es un correo de prueba enviado desde <i>PHPMailer</i> con autenticación SMTP.';
            $mail->AltBody = '¡Hola! Este es un correo de prueba enviado desde PHPMailer con autenticación SMTP.';

            // Enviar
            $mail->send();
            return '✅ Correo de prueba enviado correctamente.';
        } catch (Exception $e) {
            return "❌ Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }

    /**
     * Obtiene todos los informes con coberturas.
     */
    public function getInformes()
    {
        $nombre = $this->request->getGet('nombre');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');

        if ($nombre !== null || $fecha_desde !== null || $fecha_hasta !== null) {
            // Si algún parámetro de filtro está presente, aplicamos el filtro
            $resultado = $this->InformesModel->getInformesWithFiltros($nombre, $fecha_desde, $fecha_hasta);
        } else {
            // Si no hay parámetros de filtro, obtenemos todos los informes con coberturas
            $resultado = $this->InformesModel->getInformesWithCoberturas();
        }

        return $this->response->setJSON($resultado);
    }

    public function getInformesPaginado()
    {
        $nombre = $this->request->getGet('nombre');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        $cobertura = $this->request->getGet('cobertura'); 
        $page = (int) $this->request->getGet('page') ?: 1;
        $perPage = (int) $this->request->getGet('per_page') ?: 10;
        $offset = ($page - 1) * $perPage;

        // Obtener resultados y total de registros filtrados
        $resultado = $this->InformesModel->getInformesPaginado($nombre, $fecha_desde, $fecha_hasta, $cobertura, $perPage, $offset);
        $total = $this->InformesModel->countInformesFiltrados($nombre, $fecha_desde, $fecha_hasta, $cobertura);
        $totalPaginas = ceil($total / $perPage);

        return $this->response->setJSON([
            'data' => $resultado,
            'meta' => [
                'pagina_actual' => $page,
                'por_pagina' => $perPage,
                'total_paginas' => $totalPaginas,
                'total_registros' => $total,
            ]
        ]);
    }


    /**
     * Obtiene un informe por su ID junto con su cobertura.
     */
    public function getByIdInformes($id)
    {
        $resultado = $this->InformesModel->getInformeByIdWithCobertura($id);
        echo !empty($resultado) ? '<pre>' . print_r($resultado, true) . '</pre>' : 'No se encontró el informe.';
    }

    public function descargarInformeCompleto()
    {
        echo "FCPATH: " . FCPATH . "<br>";

        $rutaRelativa = $this->request->getGet('ruta');
        echo "Ruta GET: " . $rutaRelativa . "<br>";

        log_message('debug', 'Ruta recibida: ' . $rutaRelativa);

        if (empty($rutaRelativa)) {
            log_message('error', 'Ruta no proporcionada');
            return $this->response->setStatusCode(400)->setBody('Falta la ruta del archivo');
        }

        $rutaSegura = str_replace(['..', './', '\\'], '', $rutaRelativa);
        $rutaCompleta = FCPATH . $rutaSegura;
        echo "Ruta completa generada: " . $rutaCompleta . "<br>";

        if (!file_exists($rutaCompleta)) {
            log_message('error', 'El archivo no existe en: ' . $rutaCompleta);
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado: ' . $rutaSegura);
        }

        $nombreArchivo = basename($rutaCompleta);

        try {
            return $this->response->download($rutaCompleta, null)->setFileName($nombreArchivo);
        } catch (\Exception $e) {
            log_message('critical', 'Error en descarga: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error al descargar el archivo');
        }
    }


 public function postInforme()
    {
        // Captura todos los datos del POST al inicio para devolverlos en caso de error
        $requestData = $this->request->getPost();
        $uploadedFiles = $this->request->getFileMultiple('archivo'); 

        // Información sobre los archivos recibidos
        $filesReceivedInfo = [];
        if ($uploadedFiles !== null) {
            foreach ($uploadedFiles as $file) {
                // Captura información antes de cualquier intento de mover/procesar que pueda fallar
                $filesReceivedInfo[] = [
                    'name' => $file->getName(),
                    'type_reported' => $file->getClientMimeType(), // Tipo MIME reportado por el cliente
                    'size' => $file->getSizeByUnit('kb') . ' KB',
                    'isValid' => $file->isValid(),
                    'hasMoved' => $file->hasMoved(),
                    'error' => $file->getErrorString(),
                    'errorCode' => $file->getError(),
                    'temp_path' => $file->getTempName() // Ruta temporal del archivo
                ];
            }
        }
        log_message('debug', 'Info de archivos recibidos: ' . json_encode($filesReceivedInfo));

        // Variable para almacenar el ID del informe por si necesitamos eliminarlo en caso de fallo
        $idInforme = null;

        try {
            // ... (Resto del código para obtener y limpiar datos, validaciones de campos obligatorios,
            // y obtener nombre de la cobertura - ¡Sin cambios aquí!) ...

            $nombrePaciente = trim($requestData['nombre_paciente'] ?? '');
            $fecha = $requestData['fecha'] ?? null;
            $tipoInforme = trim($requestData['tipo_informe'] ?? '');
            $mailPaciente = trim($requestData['mail_paciente'] ?? '');
            $dniPaciente = trim($requestData['dni_paciente'] ?? '');
            $idCobertura = $requestData['id_cobertura'] ?? null;
            $fechaNacimiento = $requestData['fecha_nacimiento_paciente'] ?? null;
            $afiliado = $requestData['numero_afiliado'] ?? null;
            $medico = trim($requestData['medico_envia_estudio'] ?? '');
            $motivo = trim($requestData['motivo_estudio'] ?? '');
            $estomago = trim($requestData['estomago'] ?? '');
            $duodeno = trim($requestData['duodeno'] ?? '');
            $esofago = trim($requestData['esofago'] ?? '');
            $conclusion = trim($requestData['conclusion'] ?? '');
            $terapeutico = (int) ($requestData['efectuo_terapeutica'] ?? 0); 
            $cual = trim($requestData['tipo_terapeutica'] ?? '');
            $biopsia = (int) ($requestData['efectuo_biopsia'] ?? 0); 
            $frascos = $requestData['fracos_biopsia'] ?? null;
            $informeContenido = trim($requestData['informe'] ?? '');
            $edad = $requestData['edad'] ?? null;
            // Validar campos obligatorios
            $requiredFields = ['nombre_paciente', 'dni_paciente', 'fecha', 'mail_paciente', 'tipo_informe', 'id_cobertura'];
            foreach ($requiredFields as $field) {
                if (empty($requestData[$field])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Falta el campo obligatorio: ' . $field,
                        'data_post_received' => $requestData,
                        'files_received_info' => $filesReceivedInfo
                    ]);
                }
            }

            // Obtener nombre de la cobertura
            $coberturaData = $this->CoberturasModel->find($idCobertura);
            if (!$coberturaData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cobertura no encontrada para ID: ' . $idCobertura,
                    'data_post_received' => $requestData,
                    'files_received_info' => $filesReceivedInfo
                ]);
            }
            $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';

            // --- PASO CLAVE 1: Insertar el informe en la base de datos para obtener el ID ---
            // ... (código para insertar en DB, obtener idInforme) ...
            $dataToInsert = [
                'nombre_paciente' => $nombrePaciente, 'fecha' => $fecha, 'tipo_informe' => $tipoInforme,
                'mail_paciente' => $mailPaciente, 'dni_paciente' => $dniPaciente, 'id_cobertura' => $idCobertura,
                'fecha_nacimiento_paciente' => $fechaNacimiento, 'numero_afiliado' => $afiliado,
                'medico_envia_estudio' => $medico, 'motivo_estudio' => $motivo, 'estomago' => $estomago,
                'duodeno' => $duodeno, 'esofago' => $esofago, 'conclusion' => $conclusion,
                'efectuo_terapeutica' => $terapeutico, 'tipo_terapeutica' => $cual,
                'efectuo_biopsia' => $biopsia, 'fracos_biopsia' => $frascos,
                'informe' => $informeContenido, 'edad' => $edad, 'url_archivo' => ''
            ];

            if (!$this->InformesModel->insert($dataToInsert, false)) {
                $errors = $this->InformesModel->errors();
                $dbError = $this->db->error();
                log_message('error', 'Fallo la insercion en DB. Errores del modelo: ' . json_encode($errors));
                log_message('error', 'Fallo la insercion en DB. Errores de la DB: ' . json_encode($dbError));
                return $this->response->setJSON([
                    'success' => false, 'message' => 'Error al guardar el informe en la base de datos.',
                    'model_errors' => $errors, 'db_error_code' => $dbError['code'] ?? null,
                    'db_error_message' => $dbError['message'] ?? 'Desconocido',
                    'data_post_received' => $requestData, 'files_received_info' => $filesReceivedInfo
                ]);
            }
            $idInforme = $this->InformesModel->getInsertID();

            log_message('debug', 'ID del informe insertado: ' . $idInforme);
            if (empty($idInforme)) {
                log_message('error', 'CRÍTICO: Insercion exitosa, pero getInsertID() devolvio un ID vacio o 0. Eliminando registro...');
                try { $this->InformesModel->delete($idInforme); } catch (\Exception $e) { log_message('error', 'Fallo al eliminar el registro recien insertado con ID nulo/vacio.'); }
                return $this->response->setJSON([
                    'success' => false, 'message' => 'Error crítico: El informe se guardó, pero no se pudo obtener su ID para la gestión de archivos.',
                    'data_post_received' => $requestData, 'inserted_id_raw' => $idInforme,
                    'files_received_info' => $filesReceivedInfo
                ]);
            }

            // --- PASO CLAVE 2: Usar el ID para crear el nombre de la carpeta del informe y guardar archivos ---
            // ... (código para crear carpetas de paciente e informe) ...
            $nombreCarpetaBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombrePaciente));
            $dniCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dniPaciente);
            $carpetaPaciente = $nombreCarpetaBase . '_' . $dniCarpeta;
            $uploadPathBasePaciente = FCPATH . 'uploads/' . $carpetaPaciente . '/';

            if (!is_dir($uploadPathBasePaciente)) {
                if (!mkdir($uploadPathBasePaciente, 0777, true)) {
                    $this->InformesModel->delete($idInforme);
                    return $this->response->setJSON([
                        'success' => false, 'message' => 'No se pudo crear carpeta del paciente: ' . $uploadPathBasePaciente,
                        'data_post_received' => $requestData, 'files_received_info' => $filesReceivedInfo
                    ]);
                }
            }

            $fechaCarpeta = date('Ymd_His');
            $carpetaInformeConId = $fechaCarpeta . '_informeN_' . $idInforme;
            $uploadPathInforme = $uploadPathBasePaciente . $carpetaInformeConId . '/';

            if (!mkdir($uploadPathInforme, 0777, true)) {
                $this->InformesModel->delete($idInforme);
                return $this->response->setJSON([
                    'success' => false, 'message' => 'No se pudo crear carpeta para el informe: ' . $uploadPathInforme,
                    'data_post_received' => $requestData, 'files_received_info' => $filesReceivedInfo
                ]);
            }

            // --- Guardar imágenes en la carpeta del informe Y preparar para PDF ---
            $imagenesGuardadasPaths = [];
            $imagenesBase64ParaPDF = []; 
            
            if ($uploadedFiles !== null) {
                $imgIndex = 0;
                foreach ($uploadedFiles as $archivo) {
                    // *** CAMBIO AQUÍ: Validar directamente el tipo de archivo sin usar getMimeType() que causa el error finfo_file ***
                    // Usamos getClientMimeType() que es el MIME que el navegador reporta, o validamos por extensión.
                    // Idealmente, se valida con $archivo->getMimeType() pero si eso falla, esta es una alternativa.
                    // Si el error persiste, el problema está en la config de PHP/XAMPP o permisos de la carpeta tmp.
                    $clientMimeType = $archivo->getClientMimeType();
                    $allowedMimeTypes = ['image/jpeg', 'image/png'];
                    $isValidFileType = in_array($clientMimeType, $allowedMimeTypes);

                    // Si isValid() falla o el tipo de archivo no es permitido, registrar y manejar
                    if (!$archivo->isValid() || !$isValidFileType) {
                        $errorMsg = $archivo->getErrorString();
                        if ($archivo->getError() === UPLOAD_ERR_NO_FILE) {
                            $errorMsg = "No se seleccionó ningún archivo para subir.";
                        } elseif ($archivo->getError() !== UPLOAD_ERR_OK) {
                            $errorMsg = "Error de subida: " . $errorMsg;
                        } elseif (!$isValidFileType) {
                            $errorMsg = "Tipo de archivo no permitido: " . $clientMimeType;
                        }
                        
                        log_message('error', 'Fallo de validacion de imagen: ' . $errorMsg . ' para archivo: ' . $archivo->getName());
                        $this->InformesModel->delete($idInforme);
                        $this->recursivelyDeleteDirectory($uploadPathInforme);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Error al procesar imagen "' . $archivo->getName() . '": ' . $errorMsg,
                            'data_post_received' => $requestData,
                            'files_received_info' => $filesReceivedInfo // Incluye la info de los archivos
                        ]);
                    }

                    // Si la validación pasa, continuamos con el movimiento y procesamiento
                    $extension = $archivo->getClientExtension();
                    $newFileName = 'imagen_informeN_' . $idInforme . '_' . $imgIndex . '.' . $extension;
                    
                    log_message('debug', 'Intentando mover archivo: ' . $archivo->getTempName() . ' a ' . $uploadPathInforme . $newFileName);
                    if ($archivo->move($uploadPathInforme, $newFileName)) {
                        log_message('debug', 'Archivo movido exitosamente: ' . $newFileName);
                        $imagenesGuardadasPaths[] = 'uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/' . $newFileName;
                        
                        $fullImagePath = $uploadPathInforme . $newFileName;
                        if (file_exists($fullImagePath)) {
                            $contenido = file_get_contents($fullImagePath); 
                            $base64 = base64_encode($contenido);
                            $imagenesBase64ParaPDF[] = 'data:' . $clientMimeType . ';base64,' . $base64; // Usar $clientMimeType
                            log_message('debug', 'Imagen ' . $newFileName . ' convertida a Base64 para PDF.');
                        } else {
                            log_message('error', 'Error: La imagen ' . $fullImagePath . ' no se encontró después de moverla para convertir a Base64. Esto podría indicar un problema de permisos o escritura.');
                        }
                    } else {
                        // Error si move() falla
                        log_message('error', 'Fallo al mover el archivo "' . $archivo->getName() . '". Error: ' . $archivo->getErrorString());
                        $this->InformesModel->delete($idInforme);
                        $this->recursivelyDeleteDirectory($uploadPathInforme);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Error al mover el archivo de imagen "' . $archivo->getName() . '".',
                            'data_post_received' => $requestData,
                            'files_received_info' => $filesReceivedInfo
                        ]);
                    }
                    $imgIndex++;
                }
            }


            // Datos para PDF (ahora incluyendo $imagenesBase64ParaPDF)
            $dataPdf = [
                'fecha' => $fecha, 'tipo_informe' => $tipoInforme, 'nombre_paciente' => $nombrePaciente,
                'fecha_nacimiento' => $fechaNacimiento, 'dni_paciente' => $dniPaciente,
                'nombre_cobertura' => $nombreCobertura, 'mail_paciente' => $mailPaciente,
                'medico' => $medico, 'motivo' => $motivo, 'informe' => $informeContenido,
                'estomago' => $estomago, 'duodeno' => $duodeno, 'esofago' => $esofago,
                'conclusion' => $conclusion, 'terapeutico' => $terapeutico, 'cual' => $cual,
                'biopsia' => $biopsia, 'frascos' => $frascos, 'edad' => $edad, 'afiliado' => $afiliado,
                'imagenes' => $imagenesBase64ParaPDF,
                'id_informe' => $idInforme
            ];

            // Generar el PDF con el nombre deseado
            $pdfFileName = 'informeN_' . $idInforme . '_' . $fechaCarpeta . '.pdf';
            $pdfPath = $this->generatePDF($dataPdf, $nombreCobertura, $uploadPathInforme, $pdfFileName); 

            if (!file_exists($pdfPath)) {
                $this->InformesModel->delete($idInforme);
                $this->recursivelyDeleteDirectory($uploadPathInforme);
                return $this->response->setJSON([
                    'success' => false, 'message' => 'El PDF no fue generado o no se encontró en la ruta esperada.',
                    'data_post_received' => $requestData, 'expected_pdf_path' => $pdfPath,
                    'files_received_info' => $filesReceivedInfo
                ]);
            }

            // --- PASO CLAVE 3: Actualizar la URL del archivo en la base de datos ---
            $updateData = [
                'url_archivo' => 'uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/' . $pdfFileName
            ];
            $this->InformesModel->update($idInforme, $updateData);

            $fechaFormateada = \DateTime::createFromFormat('Y-m-d', $fecha)->format('d-m-Y');

            // Enviar correo
            $asunto = 'Informe Médico - ' . $tipoInforme . ' - ' . $fechaFormateada;
            $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>Se adjunta su informe médico.</p>';
            $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, [$pdfPath]);

            if ($resultadoEnvio['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Informe guardado, PDF y imágenes almacenadas, y correo enviado correctamente.',
                    'id_informe' => $idInforme,
                    'url_archivo' => $updateData['url_archivo'],
                    'imagenes_guardadas' => $imagenesGuardadasPaths,
                    'files_received_info' => $filesReceivedInfo
                   
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Informe guardado y archivos almacenados, pero hubo un error al enviar el correo: ' . $resultadoEnvio['message'],
                    'id_informe' => $idInforme,
                    'url_archivo' => $updateData['url_archivo'],
                    'imagenes_guardadas' => $imagenesGuardadasPaths,
                    'email_error' => $resultadoEnvio['message'],
                    'data_post_received' => $requestData,
                    'files_received_info' => $filesReceivedInfo
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepcion en postInforme: ' . $e->getMessage() . ' en ' . $e->getFile() . ' linea ' . $e->getLine());

            if ($idInforme !== null) {
                try {
                    $this->InformesModel->delete($idInforme);
                    log_message('debug', 'Informe con ID ' . $idInforme . ' eliminado de la DB debido a un fallo posterior.');
                } catch (\Exception $deleteE) {
                    log_message('error', 'Error al intentar eliminar informe ID ' . $idInforme . ' después de una excepción: ' . $deleteE->getMessage());
                }
            }
            if (isset($uploadPathInforme) && is_dir($uploadPathInforme)) {
                 $this->recursivelyDeleteDirectory($uploadPathInforme);
                 log_message('debug', 'Carpeta de informe ' . $uploadPathInforme . ' eliminada debido a un fallo.');
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor al procesar el informe: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data_post_received' => $requestData,
                'files_received_info' => $filesReceivedInfo,
                'last_inserted_id' => $idInforme
            ]);
        }
    }



    private function generatePDF($data, $cobertura, $outputPath, $fileName)
    {
       log_message('debug', 'Iniciando generatePDF.');
        log_message('debug', 'outputPath recibido: ' . $outputPath);
        log_message('debug', 'fileName recibido: ' . $fileName);
       
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true); // Necesario si usas URLs absolutas para imágenes externas
        $options->set('isHtml5ParserEnabled', true); // Recomendado para mejor parsing de HTML5
        $dompdf = new \Dompdf\Dompdf($options);

        // Es crucial que base_url() genere una URL ACCESIBLE por Dompdf.
        // Para imágenes locales, a veces es mejor usar la ruta física si Dompdf no las carga bien por URL.
        // Si tus imágenes están en la carpeta 'public', deberías poder acceder a ellas con base_url().
        $logo = base_url('images/logo.png');
        $firma = base_url('images/firma.png');

        // Logging para verificar las URLs de las imágenes
        log_message('debug', 'URL Logo: ' . $logo);
        log_message('debug', 'URL Firma: ' . $firma);

     
        $terapeuticaDisplay = (isset($data['terapeutico']) && $data['terapeutico'] == 1) ? 'SI' : 'NO';
        $biopsiaDisplay = (isset($data['biopsia']) && $data['biopsia'] == 1) ? 'SI' : 'NO';

        $imagenesHtml = '';
        if (!empty($data['imagenes'])) {
            $imagenesHtml .= "<div class='section'><div class='section-title1'><br><br>IMÁGENES DEL ESTUDIO</div>";
            foreach ($data['imagenes'] as $imgBase64) {
                // Asegúrate de que $imgBase64 ya incluye 'data:image/jpeg;base64,' o similar
                $imagenesHtml .= "<div style='margin: 10px 0; text-align: center;'>
                <img src='{$imgBase64}' style='max-width: 450px; max-height: 500px; border: 1px solid #ccc; padding: 4px;'>
            </div>";
                log_message('debug', 'Añadiendo imagen Base64 al PDF.');
            }
            $imagenesHtml .= "</div>";
        }

        // Armamos la sección de PATOLOGÍA solo si biopsia es "SI"
        $patologiaHtml = '';
        if ($biopsiaDisplay === 'SI') {
            $patologiaHtml = "
        <div class='section'>
            <div class='section-title1'>PATOLOGÍA</div>
            <p><strong>Patóloga: Dra Polina Angélica.</strong> Resultados disponibles a partir de 15 días hábiles en Clínica Santa Isabel. Ingreso por calle Lautaro, 1er piso. No es trámite personal.</p>
        </div>";
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
    </style>
</head>
<body>
    <div class='header-box'>
        <div class='header-logo'>
            <img src='{$logo}' class='logo-img' alt='Logo Clínica Santa Isabel'>
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

    <div class='titulo-principal'><br>INFORME MÉDICO<br><br></div>

    <div class='section'>
        <div class='field'><strong>FECHA:</strong> " . date('d/m/Y', strtotime($data['fecha'])) . "</div>
        <div class='field'><strong>TIPO DE ESTUDIO:</strong> {$data['tipo_informe']}</div>
    </div>

    <div class='section'>
        <div class='section-title1'>DATOS DEL PACIENTE</div>
        <div class='field'><strong>NOMBRE:</strong> {$data['nombre_paciente']}</div>
        <div class='field'><strong>FECHA DE NACIMIENTO:</strong>" . date('d/m/Y', strtotime($data['fecha_nacimiento'])) . " </div>
        <div class='field'><strong>EDAD:</strong> {$data['edad']}</div>
        <div class='field'><strong>DNI:</strong> {$data['dni_paciente']}</div>
        <div class='field'><strong>COBERTURA:</strong> {$cobertura}</div>
        <div class='field'><strong>AFILIADO N°:</strong> {$data['afiliado']}</div>
        <div class='field'><strong>MAIL:</strong> {$data['mail_paciente']}</div>
        <div class='field'><strong>MÉDICO SOLICITANTE:</strong> {$data['medico']}</div>
        <div class='field'><strong>MOTIVO DEL ESTUDIO:</strong> {$data['motivo']}</div>
    </div>

    <div class='section'>
        <div class='section-title'>INFORME</div>" .
            (strtoupper($data['tipo_informe']) === 'VEDA' ? "
            <div class='field'><strong>Esófago:</strong> {$data['esofago']}</div>
            <div class='field'><strong>Estómago:</strong> {$data['estomago']}</div>
            <div class='field'><strong>Duodeno:</strong> {$data['duodeno']}</div>" :
                "<div class='field'><strong>Informe general:</strong> {$data['informe']}</div>") .
            "</div>

    <div class='section'>
        <div class='section-title'>CONCLUSIÓN</div>
        <div class='field'>{$data['conclusion']}</div>
    </div>

    <div class='section'>
        <div class='section-title1'>TERAPÉUTICA Y BIOPSIA</div>
        <div class='field'><strong>¿Se efectuó terapéutica?:</strong> {$terapeuticaDisplay}</div>
        <div class='field'><strong>¿Cuál?:</strong> {$data['cual']}</div>
        <div class='field'><strong>¿Se efectuó biopsia?:</strong> {$biopsiaDisplay}</div>
        <div class='field'><strong>Frascos:</strong> {$data['frascos']}</div>
    </div>

    {$patologiaHtml}
    <br><br>
    {$imagenesHtml}

    <div class='instrucciones'><br><br>INSTRUCCIONES POSTERIORES AL ESTUDIO</div>
    <ol style='font-size: 11px;'>
        <li>El estudio de colon puede provocar retención de gases...</li>
        <li>Debe regresar acompañado a su domicilio...</li>
        <li>Comience con su dieta habitual...</li>
        <li>Comience con su medicación habitual...</li>
        <li>Si se le ha efectuado terapéutica endoscópica...</li>
    </ol>

    <div class='section'>
        <div class='section-title1'>CONTACTOS</div>
        <ul style='font-size: 11px;'>
            <li>Dra Estrin Diana – 1134207000 – dianajudit@hotmail.com</li>
            <li>Secretaría – Belén Chapuis – 1151825634 – secretariaendoscopias@gmail.com</li>
        </ul>
    </div>

    <div class='footer-box'>
        <img src='{$firma}' style='width: 150px;' alt='Firma digital'><br>
        <p><strong>FIRMA DIGITAL Y SELLO</strong></p>
        <p><strong class='instrucciones'>IMPORTANTE:</strong> Lleve este informe a su próxima consulta médica.</p>
    </div>
</body>
</html>";

  
        log_message('debug', 'HTML del PDF generado. Longitud: ' . strlen($html));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        log_message('debug', 'Dompdf ha renderizado el HTML.');

        // La ruta completa donde se guardará el archivo
        $filePath = $outputPath . $fileName;

        try {
            file_put_contents($filePath, $dompdf->output());
            log_message('debug', 'PDF guardado exitosamente en: ' . $filePath);
            return $filePath; // Devolvemos la RUTA COMPLETA del archivo
        } catch (\Exception $e) {
            log_message('error', 'ERROR al guardar el PDF: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            return ''; // Retornar una cadena vacía para indicar un fallo
        }
    }





    private function enviarCorreoPHPMailer($destinatario, $asunto, $mensaje, $adjuntos = [])
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'c0170053.ferozo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'estudio@dianaestrin.com';
            $mail->Password   = '@Wurst2024@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
     
           /* $mail->setFrom('estudio@dianaestrin.com', 'Estudio Diana Estrin');
             $mail->addCC('dianajudit@hotmail.com', "Se envió a: $destinatario");
            $mail->addCC('adege2000@yahoo.com.ar', "Se envió a: $destinatario");
            $mail->addCC('quirofanosi@santaisabel.com.ar', "Se envió a: $destinatario"); */
            $mail->addAddress($destinatario); // Este es el destinatario principal dinámico

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            // Adjuntos
            foreach ($adjuntos as $adjunto) {
                if (file_exists($adjunto)) {
                    $mail->addAttachment($adjunto);
                }
            }

            $mail->send();
            return ['success' => true, 'message' => 'Correo enviado correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo];
        }
    }


    // ... (función descargarCarpeta) ...



    public function descargarCarpeta()
    {
        $carpetaRelativa = $this->request->getGet('url');

        if (!$carpetaRelativa) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionó la URL de la carpeta.'
            ]);
        }

        $rutaAbsolutaCarpeta = FCPATH . str_replace(['\\', '//'], '/', $carpetaRelativa);

        if (!is_dir($rutaAbsolutaCarpeta)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'La carpeta no se encontró: ' . $rutaAbsolutaCarpeta
            ]);
        }

        $archivos = scandir($rutaAbsolutaCarpeta);
        $archivosParaZip = [];

        foreach ($archivos as $archivo) {
            if ($archivo !== '.' && $archivo !== '..') {
                $archivosParaZip[] = $rutaAbsolutaCarpeta . '/' . $archivo;
            }
        }

        if (empty($archivosParaZip)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'La carpeta está vacía.'
            ]);
        }

        $zip = new ZipArchive();
        $nombreZip = basename(dirname($carpetaRelativa)) . '_' . basename($carpetaRelativa) . '_archivos.zip';
        $rutaZip = WRITEPATH . 'temp/' . $nombreZip;

        if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($archivosParaZip as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }
            $zip->close();

            // Forzar descarga
            return $this->response
                ->setHeader('Content-Type', 'application/zip')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $nombreZip . '"')
                ->setHeader('Content-Length', filesize($rutaZip))
                ->setBody(file_get_contents($rutaZip));

            // Opcional: eliminar ZIP temporalmente después de servirlo
            // unlink($rutaZip);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al crear el archivo ZIP.'
            ]);
        }
    }


    /**
     * Elimina un informe por su ID.
     */
    public function deleteInforme($id)
    {
        $this->InformesModel->delete($id);
        echo 'Informe eliminado';
    }

    public function updateInforme($id)
    {
        // Obtener datos del body JSON
        $data = $this->request->getJSON(true); // true para obtener un array asociativo

        // Verificar si se recibieron datos
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se recibieron datos para actualizar. El cuerpo de la solicitud está vacío o no es JSON válido.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Crear array con los datos a actualizar, solo incluyendo los que se reciben
        $updateData = [];

        // Campos existentes en tu función original
        if (isset($data['nombre_paciente'])) {
            $updateData['nombre_paciente'] = trim($data['nombre_paciente']);
        }
        if (isset($data['dni_paciente'])) {
            $updateData['dni_paciente'] = trim($data['dni_paciente']);
        }
        if (isset($data['fecha'])) {
            $updateData['fecha'] = $data['fecha'];
        }
        if (isset($data['url_archivo'])) { // Ten cuidado si esto cambia al actualizar un archivo real
            $updateData['url_archivo'] = $data['url_archivo'];
        }
        if (isset($data['mail_paciente'])) {
            $updateData['mail_paciente'] = trim($data['mail_paciente']); // Añadido trim por consistencia
        }
        if (isset($data['tipo_informe'])) {
            $updateData['tipo_informe'] = trim($data['tipo_informe']);
        }
        if (isset($data['id_cobertura'])) {
            $updateData['id_cobertura'] = $data['id_cobertura'];
        }

        // --- CAMPOS NUEVOS/ADICIONALES ---
        if (isset($data['fecha_nacimiento_paciente'])) {
            $updateData['fecha_nacimiento_paciente'] = $data['fecha_nacimiento_paciente'];
        }
        if (isset($data['numero_afiliado'])) {
            $updateData['numero_afiliado'] = trim($data['numero_afiliado']);
        }
        if (isset($data['medico_envia_estudio'])) {
            $updateData['medico_envia_estudio'] = trim($data['medico_envia_estudio']);
        }
        if (isset($data['motivo_estudio'])) {
            $updateData['motivo_estudio'] = trim($data['motivo_estudio']);
        }
        if (isset($data['estomago'])) {
            $updateData['estomago'] = trim($data['estomago']);
        }
        if (isset($data['duodeno'])) {
            $updateData['duodeno'] = trim($data['duodeno']);
        }
        if (isset($data['esofago'])) {
            $updateData['esofago'] = trim($data['esofago']);
        }
        if (isset($data['conclusion'])) {
            $updateData['conclusion'] = trim($data['conclusion']);
        }
        if (isset($data['efectuo_terapeutica'])) {
            $updateData['efectuo_terapeutica'] = (int)$data['efectuo_terapeutica'];
        }
        if (isset($data['tipo_terapeutica'])) {
            $updateData['tipo_terapeutica'] = (empty(trim($data['tipo_terapeutica']))) ? null : trim($data['tipo_terapeutica']);
        }
        if (isset($data['efectuo_biopsia'])) {
            $updateData['efectuo_biopsia'] = (int)$data['efectuo_biopsia'];
        }
        if (isset($data['fracos_biopsia'])) {
            $updateData['fracos_biopsia'] = (empty($data['fracos_biopsia']) && $data['fracos_biopsia'] !== 0) ? null : (int)$data['fracos_biopsia'];
        }
        if (isset($data['informe'])) {
            $updateData['informe'] = trim($data['informe']);
        }
        if (isset($data['edad'])) {
            $updateData['edad'] = $data['edad'];
        }

        // Si no hay datos válidos para actualizar, retornar un error
        if (empty($updateData)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionaron campos válidos para actualizar.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Intentar actualizar el informe
        // Asegúrate de que $this->InformesModel esté disponible.
        // Si no estás usando ResourceController de la forma más estricta con $modelName,
        // o si tienes lógica personalizada, podrías necesitar instanciarlo.
        $informeModel = $this->model ?? new \App\Models\InformesModel(); // Usa $this->model si está configurado, sino instancialo

        $informe = $informeModel->find($id);

        if (!$informe) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Informe no encontrado con ID: ' . $id
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($informeModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Informe actualizado exitosamente',
                'data_updated' => $updateData // Renombrado de 'data' a 'data_updated' para mayor claridad
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } else {
            // Error al actualizar (puede ser por reglas de validación en el modelo, etc.)
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar el informe',
                'errors' => $informeModel->errors() // Esto es muy útil para depurar errores de validación del modelo
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    public function reenviarInformePorId($idInforme)
    {
        $informe = $this->InformesModel->find($idInforme);

        if (!$informe) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Informe no encontrado con el ID: ' . $idInforme,
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $mailPaciente = trim($informe['mail_paciente']);
        $nombrePaciente = trim($informe['nombre_paciente']);

        // ✅ Usar ruta exacta desde la base de datos
        $rutaPdfAbsoluta = FCPATH . str_replace('\\', '/', $informe['url_archivo']);

        if (!file_exists($rutaPdfAbsoluta)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se encontró el archivo PDF en la ruta esperada.',
                'ruta_pdf' => $rutaPdfAbsoluta,
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $asunto = 'Reenvío de Informe Médico para ' . $nombrePaciente;
        $mensaje = '<p>Estimado/a ' . $nombrePaciente . ',</p><p>Se le reenvía su informe médico.</p>';

        $resultadoEnvio = $this->enviarCorreoPHPMailer($mailPaciente, $asunto, $mensaje, [$rutaPdfAbsoluta]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Correo reenviado para el informe con ID: ' . $idInforme,
            'ruta_pdf' => $rutaPdfAbsoluta,
            'email_status' => $resultadoEnvio,
        ]);
    }
    public function descargarArchivo()
    {
        $urlRelativa = $this->request->getGet('url');

        if (!$urlRelativa) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se proporcionó la URL del archivo.'
            ]);
        }

        // Construir la ruta completa al archivo
        $rutaCompleta = FCPATH . str_replace('\\', '/', $urlRelativa);

        // Verificar si el archivo existe
        if (!file_exists($rutaCompleta)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'El archivo no se encontró.'
            ]);
        }

        // Obtener información del archivo
        $nombreArchivo = basename($rutaCompleta);
        $mime = mime_content_type($rutaCompleta);

        // Preparar la respuesta para la descarga
        $this->response->setHeader('Content-Type', $mime);
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
        $this->response->setHeader('Content-Length', filesize($rutaCompleta));
        readfile($rutaCompleta);
        return $this->response;
        /* http://localhost:8080/descargar-archivo?url=pdfs\agus_123456\agus_123456_iosfa_2025_03_22_1744038309.pdf */
    }


    
      /**
     * Recupera las URLs de todas las imágenes de un informe específico.
     *
     * @param int|string $idInforme El ID del informe.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getImagenesByInformeId($idInforme)
    {
        // Validar que el ID del informe sea un número válido
        if (!is_numeric($idInforme) || $idInforme <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de informe inválido.'
            ])->setStatusCode(400); // Bad Request
        }

        // 1. Obtener los datos del informe de la base de datosc
        $informe = $this->InformesModel->find($idInforme);

        if (!$informe) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Informe no encontrado.'
            ])->setStatusCode(404); // Not Found
        }

        // 2. Construir la ruta base de la carpeta del informe
        // Reutilizamos la lógica de construcción de ruta de `postInforme`
        $nombrePacienteSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($informe['nombre_paciente']));
        $dniPacienteSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', $informe['dni_paciente']);
        $carpetaPaciente = $nombrePacienteSanitized . '_' . $dniPacienteSanitized;

        // La URL del archivo PDF ya guarda la subcarpeta del informe (ej: 20231026_153045_informeN_123)
        // La URL completa es algo como: uploads/nombre_dni/FECHA_ID/informeN_ID_FECHA.pdf
        // Necesitamos extraer solo la parte de "FECHA_ID"
        $urlArchivo = $informe['url_archivo'];
        
        // Extraemos la parte de la carpeta del informe (ej: 20231026_153045_informeN_123)
        // Esto asume que la estructura siempre es uploads/paciente_dni/carpeta_informe/nombre_archivo.pdf
        $segments = explode('/', $urlArchivo);
        if (count($segments) < 3) {
             return $this->response->setJSON([
                'success' => false,
                'message' => 'Formato de URL de archivo inesperado en la base de datos.',
                'url_db' => $urlArchivo
            ])->setStatusCode(500); // Internal Server Error
        }
        $carpetaInformeConId = $segments[count($segments) - 2]; // La penúltima parte es la carpeta del informe

        $directorioImagenes = FCPATH . 'uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/';

        // 3. Verificar si la carpeta existe
        if (!is_dir($directorioImagenes)) {
            return $this->response->setJSON([
                'success' => true, // Es éxito, simplemente no hay imágenes o la carpeta fue eliminada
                'message' => 'No se encontró la carpeta de imágenes para este informe o está vacía.',
                'imagenes' => []
            ]);
        }

        // 4. Buscar archivos de imagen dentro de la carpeta
        $imagenesEncontradas = [];
        // scandir() lista todos los archivos y directorios, incluyendo '.' y '..'
        $files = array_diff(scandir($directorioImagenes), ['.', '..']); 

        foreach ($files as $file) {
            $filePath = $directorioImagenes . $file;
            $fileInfo = new File($filePath); // Usamos la clase File de CodeIgniter para obtener información

            // Verificar si es un archivo y si es una imagen permitida
            if ($fileInfo->isFile() && in_array($fileInfo->getMimeType(), ['image/jpeg', 'image/png'])) {
                // Generar la URL pública de la imagen
                $publicUrl = base_url('uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/' . $file);
                $imagenesEncontradas[] = $publicUrl;
            }
        }

        // 5. Devolver las URLs de las imágenes
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Imágenes recuperadas exitosamente.',
            'imagenes' => $imagenesEncontradas
        ]);
    }

    public function updateInformeImages($idInforme)
    {
        // El bloque try comienza AQUI, encapsulando toda la lógica de la función
        try {
            // 1. Validaciones iniciales
            if (!is_numeric($idInforme) || $idInforme <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de informe inválido.'
                ])->setStatusCode(400); // Bad Request
            }

            $informe = $this->InformesModel->find($idInforme);
            if (!$informe) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Informe no encontrado.'
                ])->setStatusCode(404); // Not Found
            }

            $uploadedFiles = $this->request->getFileMultiple('archivo'); // Los nuevos archivos de imagen
            
            // --- Información sobre los archivos recibidos para depuración ---
            $filesReceivedInfo = [];
            if ($uploadedFiles !== null) {
                foreach ($uploadedFiles as $file) {
                    $filesReceivedInfo[] = [
                        'name' => $file->getName(),
                        'type_reported' => $file->getClientMimeType(),
                        'size' => $file->getSizeByUnit('kb') . ' KB',
                        'isValid' => $file->isValid(),
                        'hasMoved' => $file->hasMoved(),
                        'error' => $file->getErrorString(),
                        'errorCode' => $file->getError(),
                        'temp_path' => $file->getTempName()
                    ];
                }
            }
            log_message('debug', 'Info de archivos recibidos para update: ' . json_encode($filesReceivedInfo));
            // --- FIN NUEVO ---

            // 2. Reconstruir la ruta de la carpeta del informe
            $nombrePacienteSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($informe['nombre_paciente']));
            $dniPacienteSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', $informe['dni_paciente']);
            $carpetaPaciente = $nombrePacienteSanitized . '_' . $dniPacienteSanitized;

            $urlArchivo = $informe['url_archivo'];
            $segments = explode('/', $urlArchivo);
            if (count($segments) < 3) {
                 return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Formato de URL de archivo inesperado en la base de datos para reconstruir la ruta de la carpeta.',
                    'url_db' => $urlArchivo
                ])->setStatusCode(500);
            }
            $carpetaInformeConId = $segments[count($segments) - 2];
            $uploadPathInforme = FCPATH . 'uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/';

            if (!is_dir($uploadPathInforme)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La carpeta del informe no existe: ' . $uploadPathInforme
                ])->setStatusCode(500);
            }

            // 3. Eliminar imágenes existentes (y solo imágenes, NO el PDF)
            $filesInFolder = array_diff(scandir($uploadPathInforme), ['.', '..']);
            $pdfFileName = $segments[count($segments) - 1]; // Nombre del PDF

            foreach ($filesInFolder as $file) {
                $filePath = $uploadPathInforme . $file;
                // Asegurarse de que no sea el PDF y que sea un archivo
                if (is_file($filePath) && $file !== $pdfFileName) {
                    if (unlink($filePath)) {
                        log_message('debug', 'Imagen existente eliminada: ' . $filePath);
                    } else {
                        log_message('error', 'Error al eliminar imagen existente: ' . $filePath);
                    }
                }
            }
            log_message('debug', 'Imágenes antiguas eliminadas de ' . $uploadPathInforme);


            // 4. Procesar y guardar las nuevas imágenes
            $imagenesGuardadasPaths = [];
            $imagenesBase64ParaPDF = []; 
            
            if ($uploadedFiles !== null) {
                $imgIndex = 0;
                foreach ($uploadedFiles as $archivo) {
                    $clientMimeType = $archivo->getClientMimeType();
                    $allowedMimeTypes = ['image/jpeg', 'image/png'];
                    $isValidFileType = in_array($clientMimeType, $allowedMimeTypes);

                    if (!$archivo->isValid() || !$isValidFileType) {
                        $errorMsg = $archivo->getErrorString();
                        if ($archivo->getError() === UPLOAD_ERR_NO_FILE) {
                            $errorMsg = "No se seleccionó ningún archivo para subir.";
                        } elseif ($archivo->getError() !== UPLOAD_ERR_OK) {
                            $errorMsg = "Error de subida: " . $errorMsg;
                        } elseif (!$isValidFileType) {
                            $errorMsg = "Tipo de archivo no permitido: " . $clientMimeType;
                        }
                        
                        log_message('error', 'Fallo de validacion de imagen en update: ' . $errorMsg . ' para archivo: ' . $archivo->getName());
                        $this->recursivelyDeleteDirectoryContents($uploadPathInforme, $pdfFileName);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Error al procesar nueva imagen "' . $archivo->getName() . '": ' . $errorMsg . '. No se realizó la actualización.',
                            'files_received_info' => $filesReceivedInfo
                        ]);
                    }

                    $extension = $archivo->getClientExtension();
                    $newFileName = 'imagen_informeN_' . $idInforme . '_' . $imgIndex . '.' . $extension;
                    
                    log_message('debug', 'Intentando mover nuevo archivo: ' . $archivo->getTempName() . ' a ' . $uploadPathInforme . $newFileName);
                    if ($archivo->move($uploadPathInforme, $newFileName)) {
                        log_message('debug', 'Nuevo archivo movido exitosamente: ' . $newFileName);
                        $imagenesGuardadasPaths[] = 'uploads/' . $carpetaPaciente . '/' . $carpetaInformeConId . '/' . $newFileName;
                        
                        $fullImagePath = $uploadPathInforme . $newFileName;
                        if (file_exists($fullImagePath)) {
                            $contenido = file_get_contents($fullImagePath); 
                            $base64 = base64_encode($contenido);
                            $imagenesBase64ParaPDF[] = 'data:' . $clientMimeType . ';base64,' . $base64;
                            log_message('debug', 'Nueva imagen ' . $newFileName . ' convertida a Base64 para PDF.');
                        } else {
                            log_message('error', 'Error: La nueva imagen ' . $fullImagePath . ' no se encontró después de moverla para convertir a Base64.');
                        }
                    } else {
                        log_message('error', 'Fallo al mover el nuevo archivo "' . $archivo->getName() . '". Error: ' . $archivo->getErrorString());
                        $this->recursivelyDeleteDirectoryContents($uploadPathInforme, $pdfFileName);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Error al mover el nuevo archivo de imagen "' . $archivo->getName() . '". No se realizó la actualización.',
                            'files_received_info' => $filesReceivedInfo
                        ]);
                    }
                    $imgIndex++;
                }
            } else {
                log_message('info', 'No se enviaron nuevas imágenes para el informe ' . $idInforme . '. Se eliminaron las antiguas si existían.');
            }

            // 5. Regenerar el PDF con las nuevas (o ninguna) imágenes
            $coberturaData = $this->CoberturasModel->find($informe['id_cobertura']);
            $nombreCobertura = $coberturaData['nombre_cobertura'] ?? 'No especificada';

            $dataPdf = [
                'fecha' => $informe['fecha'], 'tipo_informe' => $informe['tipo_informe'],
                'nombre_paciente' => $informe['nombre_paciente'], 'fecha_nacimiento' => $informe['fecha_nacimiento_paciente'],
                'dni_paciente' => $informe['dni_paciente'], 'nombre_cobertura' => $nombreCobertura,
                'mail_paciente' => $informe['mail_paciente'], 'medico' => $informe['medico_envia_estudio'],
                'motivo' => $informe['motivo_estudio'], 'informe' => $informe['informe'],
                'estomago' => $informe['estomago'], 'duodeno' => $informe['duodeno'],
                'esofago' => $informe['esofago'], 'conclusion' => $informe['conclusion'],
                'terapeutico' => $informe['efectuo_terapeutica'], 'cual' => $informe['tipo_terapeutica'],
                'biopsia' => $informe['efectuo_biopsia'], 'frascos' => $informe['fracos_biopsia'],
                'edad' => $informe['edad'], 'afiliado' => $informe['numero_afiliado'],
                'imagenes' => $imagenesBase64ParaPDF,
                'id_informe' => $idInforme
            ];
            
            $pdfFileName = $pdfFileName;
            $pdfPath = $this->generatePDF($dataPdf, $nombreCobertura, $uploadPathInforme, $pdfFileName); 

            if (!file_exists($pdfPath)) {
                log_message('error', 'CRÍTICO: El PDF no se pudo regenerar para el informe ' . $idInforme . ' después de actualizar las imágenes.');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Imágenes actualizadas, pero el PDF no pudo ser regenerado. Contacte a soporte.',
                    'expected_pdf_path' => $pdfPath,
                    'files_received_info' => $filesReceivedInfo
                ])->setStatusCode(500);
            }

            // 6. Éxito
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Imágenes del informe ' . $idInforme . ' actualizadas y PDF regenerado correctamente.',
                'id_informe' => $idInforme,
                'imagenes_guardadas_nuevas' => $imagenesGuardadasPaths,
                'files_received_info' => $filesReceivedInfo
            ]);

        // El bloque catch termina AQUI, dentro de la función
        } catch (\Exception $e) {
            log_message('error', 'Excepcion en updateInformeImages: ' . $e->getMessage() . ' en ' . $e->getFile() . ' linea ' . $e->getLine());

            if (isset($uploadPathInforme) && is_dir($uploadPathInforme)) {
                 $this->recursivelyDeleteDirectoryContents($uploadPathInforme, $pdfFileName ?? null);
                 log_message('debug', 'Contenido de la carpeta de informe (excepto PDF) eliminado debido a un fallo en update.');
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar las imágenes del informe: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'files_received_info' => $filesReceivedInfo
            ])->setStatusCode(500);
        }
    }

        /**
     * Helper para eliminar directorios y su contenido recursivamente.
     * Útil para limpiar después de errores.
     */
    private function recursivelyDeleteDirectory(string $dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recursivelyDeleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
    
    // --- Helper para eliminar solo el contenido (imágenes) de un directorio sin tocar el PDF ---
    private function recursivelyDeleteDirectoryContents(string $dir, ?string $excludeFileName = null)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            $filePath = $dir . $file;
            if (is_file($filePath) && ($excludeFileName === null || $file !== $excludeFileName)) {
                @unlink($filePath);
            } elseif (is_dir($filePath)) {
                $this->recursivelyDeleteDirectory($filePath);
            }
        }
        return true;
    }

    
     public function downloadPdfsByDateRange()
    {
        try {
            $fechaInicioStr = $this->request->getVar('fecha_inicio');
            $fechaFinStr = $this->request->getVar('fecha_fin');

            // 1. Validar las fechas
            if (empty($fechaInicioStr) || empty($fechaFinStr)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ambas fechas (fecha_inicio y fecha_fin) son requeridas.'
                ])->setStatusCode(400); // Bad Request
            }

            // Intentar crear objetos DateTime para validación y comparación
            try {
                $fechaInicio = new \DateTime($fechaInicioStr);
                $fechaFin = new \DateTime($fechaFinStr);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Formato de fecha inválido. Use YYYY-MM-DD.'
                ])->setStatusCode(400);
            }

            if ($fechaInicio > $fechaFin) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La fecha de inicio no puede ser posterior a la fecha de fin.'
                ])->setStatusCode(400);
            }

            // Formatear las fechas para la consulta a la base de datos (si tu columna fecha es DATE)
            $fechaInicioDB = $fechaInicio->format('Y-m-d');
            $fechaFinDB = $fechaFin->format('Y-m-d');

            log_message('debug', "Buscando PDFs entre {$fechaInicioDB} y {$fechaFinDB}");

            // 2. Consultar la base de datos
            $informes = $this->InformesModel
                             ->where('fecha >=', $fechaInicioDB)
                             ->where('fecha <=', $fechaFinDB)
                             ->findAll();

            if (empty($informes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron informes en el rango de fechas especificado.'
                ])->setStatusCode(404); // Not Found
            }

            // 3. Preparar el archivo ZIP
            $zip = new ZipArchive();
            $zipFileName = 'informes_' . $fechaInicio->format('Ymd') . '_' . $fechaFin->format('Ymd') . '.zip';
            $tempZipPath = tempnam(sys_get_temp_dir(), 'informes_zip_') . '.zip'; // Crea un nombre de archivo temporal seguro

            log_message('debug', 'Ruta temporal del ZIP: ' . $tempZipPath);

            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo crear el archivo ZIP.'
                ])->setStatusCode(500);
            }

              $filesAddedCount = 0;
            foreach ($informes as $informe) {
                $urlArchivo = $informe['url_archivo'];
                $physicalFilePath = FCPATH . $urlArchivo;
                $primaryKeyName = $this->InformesModel->primaryKey; // Asegúrate de tener esta línea

                if (file_exists($physicalFilePath) && is_file($physicalFilePath)) {
                    // Sanitizar el nombre del paciente para que sea seguro en un nombre de archivo
                    // Reemplaza caracteres no alfanuméricos (excepto espacios, guiones y guiones bajos)
                    // Luego, reemplaza los espacios con guiones bajos y limpia múltiples guiones bajos
                    $pacienteNombreLimpio = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $informe['nombre_paciente']);
                    $pacienteNombreLimpio = str_replace(' ', '_', $pacienteNombreLimpio);
                    $pacienteNombreLimpio = trim($pacienteNombreLimpio, '_');
                    $pacienteNombreLimpio = preg_replace('/_+/', '_', $pacienteNombreLimpio);

                    // Nombre del archivo dentro del ZIP con el nombre del paciente
                    // Formato propuesto: NombreApellido_DNI_TipoInforme_Fecha.pdf
                    $fileInZipName = $pacienteNombreLimpio . '_' .
                                     $informe['dni_paciente'] . '_' .
                                     preg_replace('/[^a-zA-Z0-9_-]/', '_', $informe['tipo_informe']) . '_' .
                                     $informe['fecha'] . '.pdf';

                    // Si hay duplicados en el nombre, añadir el ID para hacerlo único
                    if ($zip->locateName($fileInZipName) !== false) {
                        $fileInZipName = $pacienteNombreLimpio . '_' .
                                         $informe['dni_paciente'] . '_' .
                                         preg_replace('/[^a-zA-Z0-9_-]/', '_', $informe['tipo_informe']) . '_' .
                                         $informe['fecha'] . '_ID' . $informe[$primaryKeyName] . '.pdf';
                    }

                    if ($zip->addFile($physicalFilePath, $fileInZipName)) {
                        $filesAddedCount++;
                        log_message('debug', 'Añadido al ZIP: ' . $physicalFilePath . ' como ' . $fileInZipName);
                    } else {
                        log_message('error', 'Error al añadir archivo al ZIP: ' . $physicalFilePath);
                    }
                } else {
                    log_message('warning', 'PDF no encontrado en la ruta física: ' . $physicalFilePath . ' para informe ID: ' . $informe[$primaryKeyName]);
                }
            }

            $zip->close(); // Cierra y guarda el archivo ZIP

            if ($filesAddedCount === 0) {
                // Si no se añadió ningún archivo al ZIP (aunque se hayan encontrado informes en DB)
                // Eliminar el archivo ZIP vacío
                if (file_exists($tempZipPath)) {
                    unlink($tempZipPath);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron archivos PDF válidos en el rango de fechas para crear el ZIP.'
                ])->setStatusCode(404);
            }

            // 4. Enviar el archivo ZIP para descarga
            if (file_exists($tempZipPath)) {
                $this->response->setStatusCode(200)
                               ->setContentType('application/zip')
                               ->setHeader('Content-Disposition', 'attachment; filename="' . $zipFileName . '"')
                               ->setBody(file_get_contents($tempZipPath))
                               ->send();

                // 5. Eliminar el archivo temporal después de enviarlo
                unlink($tempZipPath);
                log_message('info', 'Archivo ZIP descargado y temporal eliminado: ' . $tempZipPath);
                exit(); // Es importante hacer exit() después de enviar un archivo binario

            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error inesperado: el archivo ZIP no se encontró después de la creación.'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Excepcion en downloadPdfsByDateRange: ' . $e->getMessage() . ' en ' . $e->getFile() . ' linea ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor al procesar la descarga: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ])->setStatusCode(500);
        }
    }
    public function getInformesByCobertura($nombreCobertura) // El parámetro ya lo renombraste a $cobertura en la ruta, así que úsalo aquí
    {
        // Si quieres que el filtro sea insensible a mayúsculas/minúsculas
        // $nombreCobertura = strtoupper($nombreCobertura); // Si en tu DB siempre es MAYÚSCULAS

        // Validación básica
        if (empty($nombreCobertura)) {
            return $this->response->setJSON([
                'status' => 400,
                'error' => 'El nombre de la cobertura no puede estar vacío.'
            ])->setStatusCode(400);
        }

        // Llama al nuevo método corregido de tu modelo
        $informes = $this->InformesModel->getInformesByNombreCobertura($nombreCobertura);

        if (empty($informes)) {
            return $this->response->setJSON([
                'status' => 404,
                'error' => 'No se encontraron informes para la cobertura "' . esc($nombreCobertura) . '".'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 200,
            'error' => null,
            'messages' => ['success' => 'Informes encontrados exitosamente.'],
            'data' => $informes
        ]);
    }
    public function downloadPdfsByDateRangeAndCoverage()
    {
        try {
            $fechaInicioStr = $this->request->getVar('fecha_inicio');
            $fechaFinStr = $this->request->getVar('fecha_fin');
            $cobertura = $this->request->getVar('cobertura'); 

            // 1. Validación de fechas
            if (empty($fechaInicioStr) || empty($fechaFinStr)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ambas fechas (fecha_inicio y fecha_fin) son requeridas para la descarga.'
                ])->setStatusCode(400); // Bad Request
            }

            try {
                $fechaInicio = new \DateTime($fechaInicioStr);
                $fechaFin = new \DateTime($fechaFinStr);
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Formato de fecha inválido. Use AAAA-MM-DD.'
                ])->setStatusCode(400);
            }

            if ($fechaInicio > $fechaFin) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La fecha de inicio no puede ser posterior a la fecha de fin.'
                ])->setStatusCode(400);
            }

            // Formatear fechas para la consulta a la base de datos
            $fechaInicioDB = $fechaInicio->format('Y-m-d');
            $fechaFinDB = $fechaFin->format('Y-m-d');

            log_message('debug', "Buscando PDFs entre {$fechaInicioDB} y {$fechaFinDB} para cobertura: " . ($cobertura ?: 'Todas'));

            // 2. Consulta a la base de datos con filtros
            // Construimos la consulta usando el Query Builder de CodeIgniter
            $informesQueryBuilder = $this->InformesModel
                                         ->select('informes.*, coberturas.nombre_cobertura') // Seleccionar el nombre de la cobertura
                                         ->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura', 'left') // LEFT JOIN para incluir informes sin cobertura
                                         ->where('informes.fecha >=', $fechaInicioDB)
                                         ->where('informes.fecha <=', $fechaFinDB);

            // Aplicar filtro por cobertura si se proporcionó un valor y no está vacío
            if (!empty($cobertura)) {
                $informesQueryBuilder->like('coberturas.nombre_cobertura', $cobertura, 'both'); // 'both' busca la subcadena en cualquier posición
            }

            $informes = $informesQueryBuilder->findAll(); // Ejecutar la consulta

            if (empty($informes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron informes en el rango de fechas y/o cobertura especificados.'
                ])->setStatusCode(404); // Not Found
            }

            // 3. Preparar el archivo ZIP
            $zip = new ZipArchive();
            $zipFileName = 'informes_' . $fechaInicio->format('Ymd') . '_' . $fechaFin->format('Ymd');
            
            // Añadir la cobertura al nombre del archivo ZIP si se usó para filtrar
            if (!empty($cobertura)) {
                // Limpiar el nombre de la cobertura para que sea seguro en el nombre del archivo ZIP
                $zipFileName .= '_' . preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $cobertura));
            }
            $zipFileName .= '.zip';

            // Crea un archivo temporal para el ZIP en el sistema
            $tempZipPath = tempnam(sys_get_temp_dir(), 'informes_zip_') . '.zip'; 

            log_message('debug', 'Ruta temporal del ZIP: ' . $tempZipPath);

            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo crear el archivo ZIP.'
                ])->setStatusCode(500); // Internal Server Error
            }

            $filesAddedCount = 0;
            foreach ($informes as $informe) {
                $urlArchivo = $informe['url_archivo'];
                // FCPATH es una constante de CodeIgniter que apunta a la raíz pública del proyecto
                $physicalFilePath = FCPATH . $urlArchivo; 
                $primaryKeyName = $this->InformesModel->primaryKey; // Obtiene el nombre de la clave primaria del modelo

                if (file_exists($physicalFilePath) && is_file($physicalFilePath)) {
                    // Sanitizar el nombre del paciente para que sea un nombre de archivo seguro
                    $pacienteNombreLimpio = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $informe['nombre_paciente'] ?? 'Sin_Nombre');
                    $pacienteNombreLimpio = str_replace(' ', '_', $pacienteNombreLimpio);
                    $pacienteNombreLimpio = trim($pacienteNombreLimpio, '_');
                    $pacienteNombreLimpio = preg_replace('/_+/', '_', $pacienteNombreLimpio);

                    // Construir el nombre del archivo dentro del ZIP
                    $fileInZipName = $pacienteNombreLimpio . '_' .
                                     ($informe['dni_paciente'] ?? 'N_A_DNI') . '_' .
                                     preg_replace('/[^a-zA-Z0-9_-]/', '_', ($informe['tipo_informe'] ?? 'Informe')) . '_' ;
                    
                    // Añadir el nombre de la cobertura al nombre del archivo si está disponible en el informe
                    if (!empty($informe['nombre_cobertura'])) {
                        $fileInZipName .= preg_replace('/[^a-zA-Z0-9_-]/', '_', $informe['nombre_cobertura']) . '_';
                    }

                    $fileInZipName .= ($informe['fecha'] ?? 'FechaDesconocida') . '.pdf';

                    // Si hay duplicados en el nombre dentro del ZIP, añadir el ID del informe para hacerlo único
                    if ($zip->locateName($fileInZipName) !== false) {
                        $fileInZipName = str_replace('.pdf', '', $fileInZipName) . '_ID' . ($informe[$primaryKeyName] ?? uniqid()) . '.pdf';
                    }

                    if ($zip->addFile($physicalFilePath, $fileInZipName)) {
                        $filesAddedCount++;
                        log_message('debug', 'Añadido al ZIP: ' . $physicalFilePath . ' como ' . $fileInZipName);
                    } else {
                        log_message('error', 'Error al añadir archivo al ZIP: ' . $physicalFilePath);
                    }
                } else {
                    log_message('warning', 'PDF no encontrado en la ruta física: ' . $physicalFilePath . ' para informe ID: ' . ($informe[$primaryKeyName] ?? 'Desconocido'));
                }
            }

            $zip->close(); // Cierra y guarda el archivo ZIP

            if ($filesAddedCount === 0) {
                // Si no se añadió ningún archivo al ZIP (p. ej., porque los PDFs no existían físicamente)
                // Eliminar el archivo ZIP vacío creado
                if (file_exists($tempZipPath)) {
                    unlink($tempZipPath);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron archivos PDF válidos en el rango de fechas y/o cobertura para crear el ZIP.'
                ])->setStatusCode(404); // Not Found
            }

            // 4. Enviar el archivo ZIP para descarga al navegador
            if (file_exists($tempZipPath)) {
                $this->response->setStatusCode(200)
                               ->setContentType('application/zip')
                               ->setHeader('Content-Disposition', 'attachment; filename="' . $zipFileName . '"')
                               ->setBody(file_get_contents($tempZipPath))
                               ->send();

                // 5. Eliminar el archivo temporal del servidor después de enviarlo
                unlink($tempZipPath);
                log_message('info', 'Archivo ZIP descargado y temporal eliminado: ' . $tempZipPath);
                exit(); // Es crucial llamar a exit() después de enviar un archivo binario directamente
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error inesperado: el archivo ZIP no se encontró después de la creación.'
                ])->setStatusCode(500); // Internal Server Error
            }

        } catch (Exception $e) {
            // Manejo de cualquier excepción inesperada
            log_message('error', 'Excepcion en downloadPdfsByDateRangeAndCoverage: ' . $e->getMessage() . ' en ' . $e->getFile() . ' linea ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor al procesar la descarga. Por favor, inténtalo de nuevo más tarde.',
                'error_detail' => $e->getMessage(), // Solo para depuración en desarrollo, no en producción
                'file' => $e->getFile(),             // Solo para depuración
                'line' => $e->getLine()              // Solo para depuración
            ])->setStatusCode(500); // Internal Server Error
        }
    }
}
