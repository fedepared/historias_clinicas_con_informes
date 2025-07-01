<?php

namespace App\Models;


use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $returnType     = 'array';
    protected $allowedFields = ['nombre_usuario','pass','mail','pidio_cambio','pass_aux'];
    

       public function getByMail(string $mail): ?array
    {
        return $this->where('mail', $mail)->first();
    }


}

?>