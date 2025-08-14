<?php

namespace App\Models;

use CodeIgniter\Model;

class InformesModel extends Model
{
    protected $table      = 'informes';
    protected $primaryKey = 'id_informe'; // Correcto: define la clave primaria

    protected $returnType     = 'array';
    protected $useAutoIncrement = true; // Agrega esto si tu ID es auto-incremental (que es lo más probable)

    // ¡IMPORTANTE! Quita 'id_informe' de $allowedFields
    protected $allowedFields = [
        'nombre_paciente',
        'fecha',
        'url_archivo',
        'mail_paciente',
        'id_cobertura',
        'dni_paciente',
        'tipo_informe',
        'fecha_nacimiento_paciente',
        'numero_afiliado',
        'medico_envia_estudio',
        'motivo_estudio',
        'estomago',
        'duodeno',
        'esofago',
        'conclusion',
        'efectuo_terapeutica',
        'tipo_terapeutica',
        'efectuo_biopsia',
        'fracos_biopsia',
        'informe', // Este es el campo 'informe' de la DB que contiene el texto del informe
        'edad'
    ];
    

    public function getInformesWithFiltros($nombre = null, $fecha_desde = null, $fecha_hasta = null)
    {
        $builder = $this->db->table($this->table);
    
        $builder->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura');
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre, 'both');
        }
    
        if ($fecha_desde && $fecha_hasta) {
            $builder->where('fecha >=', $fecha_desde);
            $builder->where('fecha <=', $fecha_hasta);
        } elseif ($fecha_desde) {
            $builder->where('fecha >=', $fecha_desde);
        } elseif ($fecha_hasta) {
            $builder->where('fecha <=', $fecha_hasta);
        }
    
        return $builder->get()->getResultArray();
    }

    public function countInformesFiltrados($nombre = null, $desde = null, $hasta = null)
    {
        $builder = $this->select('COUNT(*) as total');
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre);
        }
    
        if ($desde) {
            $builder->where('fecha >=', $desde);
        }
    
        if ($hasta) {
            $builder->where('fecha <=', $hasta);
        }
    
        return $builder->get()->getRow()->total;
    }
    
    public function getInformesPaginado($nombre = null, $desde = null, $hasta = null,$cobertura = null, $limit = 10, $offset = 0)
    {
        $builder = $this->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura', 'left');
    
        if ($nombre) {
            $builder->like('nombre_paciente', $nombre);
        }
    
        if ($desde) {
            $builder->where('fecha >=', $desde);
        }
        if ($cobertura){
            $builder->like('nombre_cobertura',$cobertura);
        }
        if ($hasta) {
            $builder->where('fecha <=', $hasta);
        }
    
        $limit = (int) $limit;
        $offset = (int) $offset;
    
        return $builder
            ->orderBy('fecha', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->getResult();
    }
    
    public function getInformesWithCoberturas()
    {
        return $this->db->table('informes')
            ->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'coberturas.id_cobertura = informes.id_cobertura', 'left')
            ->get()
            ->getResultArray();
    }

    public function getInformeByIdWithCobertura($id)
    {
        return $this->db->table('informes')
            ->select('informes.*, coberturas.nombre_cobertura')
            ->join('coberturas', 'coberturas.id_cobertura = informes.id_cobertura', 'left')
            ->where('informes.id_informe', $id)
            ->get()
            ->getRowArray();
    }
    public function getInformesByNombreCobertura($nombreCobertura)
    {
        // Construye la consulta:
        // 1. Selecciona todos los campos de 'informes' y el 'nombre_cobertura' de la tabla 'coberturas'.
        // 2. Realiza un JOIN entre 'informes' y 'coberturas' usando 'id_cobertura'.
        // 3. Filtra los resultados donde el 'nombre_cobertura' de la tabla 'coberturas' coincida con el parámetro.
        // 4. Obtiene todos los resultados que coincidan.
        return $this->select('informes.*, coberturas.nombre_cobertura')
                    ->join('coberturas', 'informes.id_cobertura = coberturas.id_cobertura')
                    ->where('coberturas.nombre_cobertura', $nombreCobertura)
                    ->findAll();
    }
}