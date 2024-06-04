<?php

namespace App\Models;

use CodeIgniter\Model;

class OrcamentoModel extends Model
{
    protected $table            = 'orcamentos';

    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id',
        'numero',
        'data',
        'total',
        'cliente_id'
        // Não colocaremos o campo ativo.... Pois existe a manipulação de formulário

    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';


    // Regras de validação (opcional)
    protected $validationRules = [
        //'id'           => 'max_length[19]|is_natural_no_zero',
        'id'           => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA
        'numero' => 'required|min_length[3]|max_length[10]',
        'data' => 'required|valid_date',
        'total' => 'required|decimal',
    ];
    protected $validationMessages = [
        'numero' => [
            'required' => 'O número do orçamento é obrigatório',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não pode ser maior que 125 caractéres.',
        ],
        'data' => [
            'required' => 'A data é obrigatória',
            'valid_date' => 'A data deve ser válida',
        ],
        'total' => [
            'required' => 'O total é obrigatório',
            'decimal' => 'O total deve ser um valor decimal',
        ],

    ];
}
