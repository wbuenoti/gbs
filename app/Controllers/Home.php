<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo' => 'Home'
        ];
        return view('Home/index', $data);
    }

    public function login()
    {


        $autenticacao = service('autenticacao');


        $autenticacao->login('pratashow@gmail.com', '123456');

        $usuario = $autenticacao->pegaUsuarioLogado();

        // dd($usuario->temPermissaoPara('criar-ordens'));

        dd($usuario);

        // dd($autenticacao->isCliente());

        // $autenticacao->logout();
        // return redirect()->to(site_url('/'));

        // dd($autenticacao->estaLogado());
    }
}
