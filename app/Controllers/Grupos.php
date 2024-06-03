<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Entities\Grupo;

class Grupos extends BaseController
{

    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
    }

    public function index()
    {

        $data = [
            'titulo' => 'Listando os grupos de acesso ao sistema',
        ];

        return view('Grupos/index', $data);
    }

    public function recuperaGrupos()
    {

        if (!$this->request->isAJAX()) {

            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em'
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Receberá o array de objetos de usuários
        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' =>  anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir grupo - ' . esc($grupo->nome) . '"'),
                'descricao' => esc($grupo->descricao),
                'exibir'  => $grupo->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {

        $grupo = new Grupo();

        $data = [
            'titulo' => "Criando novo grupo de acesso",
            'grupo' => $grupo,
        ];

        return view('Grupos/criar', $data);
    }

    public function cadastrar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();


        // Recupero o post da requisição
        $post = $this->request->getPost();


        // Crio novo objeto da Entidade Grupo
        $grupo = new Grupo($post);

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT  
        //if ($this->GrupoModel->protect(false)->insert($grupo)) {
        if ($this->grupoModel->save($grupo)) {
            $btnCriar = anchor("Grupos/criar", 'Cadastrar novo Grupo de acesso', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            // Retornamos o último ID inserido na tabela de Grupos
            //Ou seja, o ID do usuário recém criado
            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();


        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);


        $data = [
            'titulo' => "Detalhando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {

            return redirect()
                ->back()
                ->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.');
        }


        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/editar', $data);
    }

    public function atualizar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();


        // Recupero o post da requisição
        $post = $this->request->getPost();


        // Validamos a existência do usuário
        $grupo = $this->buscaGrupoOu404($post['id']);


        // Garantimos que os grupos Admin e Clientes não possam ser editados
        if ($grupo->id < 3) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo' => 'O grupo <b class="text-white">' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.'];
            return $this->response->setJSON($retorno);
        }


        // Preenchemos os atributos do usuário com os valores do POST
        $grupo->fill($post);


        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'Não existem dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT
        if ($this->grupoModel->protect(false)->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();


        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        // Busca o grupo ou retorna um erro 404
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {

            return redirect()
                ->back()
                ->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.');
        }

        if ($grupo->deletado_em != null) {
            return redirect()->back()->with('info', "Esse grupo já encontra-se excluído");
        }


        if ($this->request->getMethod() === 'POST') {

            // Excluir o grupo
            $this->grupoModel->delete($grupo->id);


            // Redireciona com mensagem de sucesso
            return redirect()->to(site_url('grupos'))->with('sucesso', 'Grupo' . esc($grupo->nome) . ' excluído com sucesso!');
        }


        // Dados para a view
        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        // Carrega a view de exclusão
        return view('Grupos/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas grupos excluídos podem ser recuperados");
        }


        $grupo->deletado_em = null;
        $this->grupoModel->protect(false)->save($grupo);

        return redirect()->back()->with('sucesso', 'Grupo' . esc($grupo->nome) . ' Recuperado com sucesso!');
    }

    /**
     * Método que recupera o grupo de acesso
     * 
     * @param interger $id
     * @return Exceptions|object
     * 
     */
    private function buscaGrupoOu404(int $id = null)
    {

        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }

        return $grupo;
    }
}
