<?php

namespace App\Controllers;

use App\Controllers\BaseController;


class Login extends BaseController
{
    public function novo()
    {

        $data = [
            'titulo' => 'Realize o login',
        ];

        return view('Login/novo', $data);
    }

    public function criar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Recuperano a instancia do serviço autenticão
        $autenticacao = service('autenticacao');



        if ($autenticacao->login($email, $password) === false) {

            // Credenciais inválidas

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['credenciais' => 'Verifique suas credencias de acesso e tente novamente'];
            return $this->response->setJSON($retorno);
        }


        // Credenciais Validadas

        exit("Validado");
    }
}
