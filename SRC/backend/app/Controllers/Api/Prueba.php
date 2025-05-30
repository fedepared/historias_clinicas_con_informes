<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Prueba extends ResourceController
{
    public function index()
    {
        return $this->respond([
            'status' => 'ok',
            'mensaje' => 'El backend funciona correctamente ✅'
        ]);
    }
}
