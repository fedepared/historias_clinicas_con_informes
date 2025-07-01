<?php

namespace App\Models;


use CodeIgniter\Model;

class CoberturasModel extends Model
{
    protected $table      = 'coberturas';
    protected $primaryKey = 'id_cobertura';

    protected $returnType     = 'array';
    protected $allowedFields = ['nombre_cobertura'];
 
}

?>