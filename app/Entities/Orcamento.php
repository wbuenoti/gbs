<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Orcamento extends Entity
{
    protected $dates = [
        'criado_em',
        'atualizado_em',
        'deletado_em',
        'validade',
    ];

    public function exibeSituacao()
    {
        if ($this->deletado_em != null) {
            // Orçamento excluído
            $icone = '<span class="text-white"><strong>Excluído</strong></span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';
            $situacao = anchor("orcamentos/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);
            return $situacao;
        }

        if ($this->ativo == true) {
            return '<i class="fa fa-check-circle text-success"></i>&nbsp;Válido';
        }

        if ($this->ativo == false) {
            return '<i class="fa fa-times-circle text-warning"></i>&nbsp;Inválido';
        }
    }
}
