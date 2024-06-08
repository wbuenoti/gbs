<?php

namespace App\Libraries;

use PhpParser\Node\Expr\Exit_;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;
    private $grupoUsuarioModel;


    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }

    /**
     * Método que realiza o login na aplicação
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login(string $email, string $password): bool
    {
        // Buscamos o usuário
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        // Validamos se o usuário foi encontrado
        if ($usuario === null) {

            exit('Usuario não encontrado');

            return false;
        }

        // Vericamos se a senha é válida
        if ($usuario->verificaPassword($password) == false) {

            exit('Sennha Invalida');

            return false;
        }

        // Verificamos se o usuário pode logar na aplicação
        if ($usuario->ativo == false) {

            exit('Usuario sem direito de acesso');

            return false;
        }

        // Logamos o usuario na aplicação
        $this->logaUsuario($usuario);

        // Retornamos true, ou seja, o usuário pode logar tranquilamente
        return true;
    }

    /**
     * Método de logout
     *
     * @return void
     */
    public function logout(): void
    {

        session()->destroy();
    }

    public function pegaUsuarioLogado()
    {

        if ($this->usuario === null) {


            $this->usuario = $this->pegaUsuarioDaSessao();
        }

        return $this->usuario;
    }

    /**
     * Método que verifica se o usuário está logado
     *
     * @return boolean
     */
    public function estaLogado(): bool
    {

        return $this->pegaUsuarioLogado() !== null;
    }

    //--------------------Métodos privados----------------//

    /**
     * Método que insere na sessão o ID do usuário
     *
     * @param object $usuario
     * @return void
     */
    private function logaUsuario(object $usuario): void
    {

        // Recuperamos a instância da sessão
        $session = session();

        // Antes de inserirmos o ID do usuario na sessão,
        // devemos gerar um novo ID  da sessão
        $session->regenerate();

        // Setamos na sessão o ID do usuário
        $session->set('usuario_id', $usuario->id);
    }

    /**
     * Método que recupera da sessão e valida o usuário logado
     *
     * @return null|object
     */
    private function pegaUsuarioDaSessao()
    {

        if (session()->has('usuario_id') == false) {

            return null;
        }

        // Busco usuário na base de dados
        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));


        // Validamos se o usuario existe e se tem permissão de login na aplicação
        if ($usuario == null || $usuario->ativo == false) {

            return null;
        }


        // Definimos as permissões do usuário logado
        // $usuario = $this->definePermissoesDoUsuarioLogado($usuario);


        // Retornamos o objeto $usuario
        return $usuario;
    }

    /**
     * Método que verifica se o usuário logado (session()->get('usuario_id')) está associado ao grupo de admin
     *
     * @return boolean
     */
    private function isAdmin(): bool
    {

        // Definimos o ID do grupo admin.
        // Não equeçam que esse ID jamais poderá ser alterado.
        // Por isso, nós defendemos no controller
        $grupoAdmin = 1;

        // Verificamos se o usuário logado está no grupo de admintrador
        $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));


        // Verificamos se foi encontrado o registro
        if ($administrador == null) {

            return false;
        }

        // Retornamos true, ou seja, o usuário logado faz parte do grupo admin
        return true;
    }

    /**
     * Método que verifica se o usuário logado (session()->get('usuario_id')) está associado ao grupo de clientes
     *
     * @return boolean
     */
    private function isCliente(): bool
    {

        // Definimos o ID do grupo cliente.
        // Não equeçam que esse ID jamais poderá ser alterado.
        // Por isso, nós defendemos no controller
        $grupoCliente = 2;

        // Verificamos se o usuário logado está no grupo de admintrador
        $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));


        // Verificamos se foi encontrado o registro
        if ($cliente == null) {

            return false;
        }

        // Retornamos true, ou seja, o usuário logado faz parte do grupo admin
        return true;
    }
}
