<?php

namespace App\Models;

use CodeIgniter\Model;

class OrcamentoItemModel extends Model
{
    protected $table            = 'orcamentomodelitems';

    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'orcamento_id',
        'descricao',
        'quantidade',
        'valor_unitario',
        'montante'

    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Regras de validação (opcional)
    protected $validationRules = [
        'descricao' => 'required|string|max_length[255]',
        'quantidade' => 'required|integer',
        'valor_unitario' => 'required|decimal',
        'montante' => 'required|decimal',
    ];
    protected $validationMessages = [
        'descricao' => [
            'required' => 'A descrição é obrigatória',
            'string' => 'A descrição deve ser uma string',
            'max_length' => 'A descrição deve ter no máximo 400 caracteres',
        ],
        'quantidade' => [
            'required' => 'A quantidade é obrigatória',
            'integer' => 'A quantidade deve ser um número inteiro',
        ],
        'valor_unitario' => [
            'required' => 'O valor unitário é obrigatório',
            'decimal' => 'O valor unitário deve ser um valor decimal',
        ],
        'montante' => [
            'required' => 'O montante é obrigatório',
            'decimal' => 'O montante deve ser um valor decimal',
        ],
    ];
}
