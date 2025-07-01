<?php

namespace App\Controllers;

use App\Models\CoberturasModel;
use CodeIgniter\HTTP\ResponseInterface;

class Coberturas extends BaseController
{
    private $coberturasModel;

    public function __construct()
    {
        $this->coberturasModel = new CoberturasModel();
    }

    public function getCoberturas()
    {
        try {

            // $data['coberturas'] =  $this->coberturasModel->findAll();
            // return view('coberturas', $data);
            $coberturas = $this->coberturasModel->findAll(); // Obtiene todas las coberturas
            return $this->response->setJSON($coberturas);
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


        public function getCoberturasFilter()
    {
        try {
            // 1. Obtener el parámetro de búsqueda 'nombre' del query string
            // Si el parámetro 'nombre' no existe, $nombreFiltro será null
            $nombreFiltro = $this->request->getVar('nombre');

            $coberturas = [];

            if ($nombreFiltro) {
                // 2. Si se proporcionó un nombre, aplicar el filtro like
                // 'like' busca coincidencias parciales (ej. 'Osde' encontrará 'Osde 210', 'Osde 310')
                $coberturas = $this->coberturasModel
                                     ->like('nombre_cobertura', $nombreFiltro)
                                     ->findAll();
            } else {
                // 3. Si no se proporcionó un nombre, obtener todas las coberturas
                $coberturas = $this->coberturasModel->findAll();
            }

            // Si no se encontraron coberturas con el filtro, puedes enviar una respuesta 404 o un array vacío
            if (empty($coberturas) && $nombreFiltro) {
                return $this->response->setJSON([
                    'status' => 'success', // Consideramos un éxito que la búsqueda no haya devuelto resultados
                    'message' => 'No se encontraron coberturas que coincidan con el nombre proporcionado.',
                    'data' => []
                ])->setStatusCode(ResponseInterface::HTTP_OK); // 200 OK aunque el resultado sea vacío
            }


            // Devolver las coberturas en formato JSON
            return $this->response->setJSON($coberturas)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error 500
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener las coberturas: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getByIdCoberturas($id)
    {
        try {
            $resultado = $this->coberturasModel->find($id);

            if (!empty($resultado)) {
                return [
                    'status' => 'success',
                    'data' => $resultado
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No se encontró la cobertura con el ID proporcionado.'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function postCobertura()
    {
        try {
            $nombreCobertura = trim($this->request->getPost('nombre_cobertura'));

            $data = [
                'nombre_cobertura' => $nombreCobertura
            ];

            // Verifica si ya existe una cobertura con el mismo nombre
            $coberturaExistente = $this->coberturasModel
                ->where('nombre_cobertura', $nombreCobertura)
                ->first();

            if ($coberturaExistente) {
                return $this->response->setStatusCode(409)->setJSON(['status' => 'error', 'message' => 'Ya existe una cobertura con el mismo nombre.']);
            }

            $insertedId = $this->coberturasModel->insert($data);

            if ($insertedId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Cobertura creada exitosamente.'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al crear la cobertura.']);
            }

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al crear la cobertura: ' . $e->getMessage()]);
        }
    }

    public function deleteCobertura($id)
    {
        try {
            log_message('debug', 'ID recibido en deleteCobertura: ' . print_r($id, true));

            // Validar que el ID sea válido
            if (empty($id) || !is_numeric($id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido proporcionado.'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Verificar si la cobertura existe antes de eliminar
            $cobertura = $this->coberturasModel->find($id);

            if (!$cobertura) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cobertura no encontrada.'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Intentar eliminar la cobertura
            $resultado = $this->coberturasModel->delete($id);

            log_message('debug', 'Resultado de la eliminación: ' . print_r($resultado, true));

            // Verificar si la eliminación fue exitosa
            if ($resultado) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Cobertura eliminada exitosamente.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se pudo eliminar la cobertura.'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            // Capturar cualquier error inesperado y devolverlo
            log_message('error', 'Error al intentar eliminar cobertura: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hubo un error al procesar la solicitud: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCobertura($id)
    {
        try {
            // Verifica si la cobertura con el ID existe
            $coberturaExistentePorId = $this->coberturasModel->find($id);
            if (!$coberturaExistentePorId) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Cobertura no encontrada.']);
            }

            // Recibe los datos del formulario (para JSON)
            $data = $this->request->getJSON();
            $nombreCobertura = trim($data->nombre_cobertura ?? ''); // Asegúrate de que 'nombre_cobertura' exista

            // echo $nombreCobertura; // Para depuración

            // Verifica si ya existe otra cobertura con el mismo nombre (ignorando la cobertura actual)
            $coberturaExistentePorNombre = $this->coberturasModel
                ->where('nombre_cobertura', $nombreCobertura)
                ->where('id_cobertura !=', $id) // Excluye la cobertura que se está editando
                ->first();

            if ($coberturaExistentePorNombre) {
                return $this->response->setStatusCode(409)->setJSON(['error' => 'Ya existe una cobertura con el mismo nombre.']);
            }

            // Prepara los datos para la actualización
            $updateData = [
                'nombre_cobertura' => $nombreCobertura
            ];

            // Actualiza la cobertura
            $updated = $this->coberturasModel->update($id, $updateData);

            if ($updated) {
                return $this->response->setJSON(['message' => 'Cobertura modificada exitosamente.']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar la cobertura.']);
            }

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}