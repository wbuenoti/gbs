<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';

    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true; // Explicar essa caracteristica
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset',
        'reset_expira_em',
        'imagem',
        // Não colocaremos o campo ativo.... Pois existe a manipulação de formulário

    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules    = [
        //'id'           => 'max_length[19]|is_natural_no_zero',
        'id'           => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]', // Não pode ter espaços
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]'
    ];

    protected $validationMessages = [
        'nome'        => [
            'required' => 'O campo Nome é obrigatório.',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não pode ser maior que 125 caractéres.',
        ],
        'email'        => [
            'required' => 'O campo E-mail é obrigatório.',
            'max_length' => 'O campo Nome não pode ser maior que 230 caractéres.',
            'is_unique' => 'Esse e-mail já foi escolhido. Por favor informe outro.'
        ],
        'password_confirmation'        => [
            'required_with' => 'Por favor confirme a sua senha.',
            'matches' => 'As senhas precisam combinar.',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            // Removemos dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    /**
     * Método que recupera o usuário para logar na aplicação
     *
     * @param string $email
     * @return null|object
     */
    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }
}
