<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table            = 'grupos';

    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules    = [
        //'id'           => 'max_length[19]|is_natural_no_zero',
        'id'           => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA    
        'nome'        => 'required|max_length[120]|is_unique[grupos.nome,id,{id}]', // Não pode ter espaços
        'descricao'     => 'required|max_length[440]',
    ];

    protected $validationMessages = [
        'nome'        => [
            'required' => 'O campo Nome é obrigatório.',
            'max_length' => 'O campo Nome não pode ser maior que 120 caractéres.',
            'is_unique' => 'Esse Grupo já foi escolhido. Por favor informe outro.'
        ],
        'descricao'        => [
            'required' => 'O campo Descrição é obrigatório.',
            'max_length' => 'O campo Nome não pode ser maior que 430 caractéres.',

        ],
    ];
}
