<?php

namespace App\Models;

use CodeIgniter\Model;

class PreparacionesModel extends Model
{
    protected $table         = 'preparaciones';
    protected $primaryKey    = 'id_preparacion';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nombre_campo',
        'es_editable',
        'texto',
        'id_padre',
        'tipo_preparacion'
    ];

    protected $useTimestamps = false; // Si no usas 'created_at' y 'updated_at'

    protected $validationRules = [
        'nombre_campo'     => 'permit_empty|max_length[25]',
        'es_editable'      => 'required|in_list[0,1]',
        'texto'            => 'permit_empty',
        'id_padre'         => 'permit_empty|integer',
        'tipo_preparacion' => 'permit_empty|max_length[10]',
    ];
}