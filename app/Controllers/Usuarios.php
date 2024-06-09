<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Entities\Usuario;
use Config\Services;

use function PHPUnit\Framework\throwException;

class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;


    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\grupoUsuarioModel();
        $this->grupoModel = new \App\Models\grupoModel();
    }

    public function index()
    {

        $data = [
            'titulo' => 'Listando os usuários do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {

        if (!$this->request->isAJAX()) {

            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
            'deletado_em'
        ];

        $usuarios = $this->usuarioModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Receberá o array de objetos de usuários
        $data = [];

        foreach ($usuarios as $usuario) {

            // Definimos o caminho da imagem do usuário
            if ($usuario->imagem != null) {

                // Tem imagem

                $imagem = [
                    'src' => site_url("usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => esc($usuario->nome),
                    'width' => '50',
                ];
            } else {

                // Não tem imagem

                $imagem = [
                    'src' => site_url("recursos/img/usuario_sem_imagem.png"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => 'Usuário sem imagem',
                    'width' => '50',
                ];
            }


            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
                'nome' =>  anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário - ' . esc($usuario->nome) . '"'),
                'email' => esc($usuario->email),
                'ativo'  => $usuario->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {

        $usuario = new Usuario();


        $data = [
            'titulo' => "Criando novo usuário ",
            'usuario' => $usuario,
        ];

        return view('Usuarios/criar', $data);
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


        // Crio novo objeto da Entidade Usuário
        $usuario = new Usuario($post);

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT  
        //if ($this->usuarioModel->protect(false)->insert($usuario)) {
        if ($this->usuarioModel->protect(false)->save($usuario)) {
            $btnCriar = anchor("usuarios/criar", 'Cadastrar Novo Usuário', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            // Retornamos o último ID inserido na tabela de usuarios
            //Ou seja, o ID do usuário recém criado
            $retorno['id'] = $this->usuarioModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();


        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);


        $data = [
            'titulo' => "Detalhando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "Usuário $usuario->nome encontra-se excluído, não poderá ser editado");
        }

        $data = [
            'titulo' => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar', $data);
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
        $usuario = $this->buscaUsuarioOu404($post['id']);


        // Se não foi informado a senha, removemos do $post
        // Se não fizermos dessa forma, o hashPassword fará o hash de um string vazia
        if (empty($post['password'])) {

            unset($post['password']);
            unset($post['password_confirmation']);
        }

        // Preenchemos os atributos do usuário com os valores do POST
        $usuario->fill($post);


        if ($usuario->hasChanged() == false) {
            $retorno['info'] = 'Não existem dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT
        if ($this->usuarioModel->protect(false)->save($usuario)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();


        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);


        $data = [
            'titulo' => "Alterando a imagem do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar_imagem', $data);
    }

    public function upload()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');


        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
        ];

        $mensagens = [   // Errors
            'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem',
                'max_size' => 'Por favor escolha uma imagem de no máximo 1024',
                'ext_in'   => 'Por favor escolha uma imagem png, jpg, jpeg, ou webp',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();


            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }


        // Recupero o post da requisição
        $post = $this->request->getPost();

        // Validamos a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);

        // Recuperamos a imagem que veio no post
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if ($largura < "300" || $altura < "300") {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels'];

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');

        //C:\xampp\htdocs\ordem\writable\uploads/usuarios/1717105790_76b748a245d5646a35d1.jpg
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        // Podemos manipular a imagem que está salva no diretório


        // Redimensionamos a imagem para 300 x 300 e para ficar no centro
        // e fazemos a marca dágua
        $this->manipulaImagem($caminhoImagem, $usuario->id);


        // A partir daqui podemos atualizar a tabela de usuários 


        // Recupero a possível imagem antiga
        $imagemAntiga = $usuario->imagem;


        $usuario->imagem = $imagem->getName();


        $this->usuarioModel->save($usuario);

        if ($imagemAntiga != null) {
            $this->removeImagemDoFileSystem($imagemAntiga);
        }


        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');




        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if ($imagem != null) {
            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    public function excluir(int $id = null)
    {
        // Busca o usuário ou retorna um erro 404
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "Esse usuário já encontra-se excluído");
        }


        if ($this->request->getMethod() === 'POST') {

            // Excluir o usuário
            $this->usuarioModel->delete($usuario->id);

            // Se o usuário tiver uma imagem, remove a imagem do sistema de arquivos
            if ($usuario->imagem != null) {
                $this->removeImagemDoFileSystem($usuario->imagem);
            }

            $usuario->imagem = null;
            $usuario->ativo = false;

            $this->usuarioModel->protect(false)->save($usuario);

            // Redireciona com mensagem de sucesso
            return redirect()->to(site_url('usuarios'))->with('sucesso', 'Usuário' . esc($usuario->nome) . ' excluído com sucesso!');
        }


        // Dados para a view
        $data = [
            'titulo' => "Excluindo o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        // Carrega a view de exclusão
        return view('Usuarios/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas usuários excluídos podem ser recuperados");
        }


        $usuario->deletado_em = null;
        $this->usuarioModel->protect(false)->save($usuario);

        return redirect()->back()->with('sucesso', "Usuário $usuario->nome Recuperado com sucesso!");
    }

    public function grupos(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);


        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);
        $usuario->pager = $this->grupoUsuarioModel->pager;


        //caso não funcione remover essa parte
        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "Usuário $usuario->nome encontra-se excluído, não poderá ser editado");
        }

        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        // Quando o usuário for um cliente, podemos retornar para a view de exibição do usuário informando
        // que ele é um cliente que não é possível adiconá-lo aos outros grupos ou removê-lo de um grupo existente ( clientes)
        $grupoCliente = 2;
        if (in_array($grupoCliente, array_column($usuario->grupos, 'grupo_id'))) {
            return redirect()->to(site_url("usuarios/exibir/$usuario->id"))
                ->with('info', "Esse usuário é um Cliente, portanto, não é necessário atribuí-lo ou removê-lo de outros grupos de acesso");
        }

        $grupoAdmin = 1;
        if (in_array($grupoAdmin, array_column($usuario->grupos, 'grupo_id'))) {
            $usuario->full_control = true; // está no grupo de admin. Portanto, já podemos retonar a views
            return view('Usuarios/grupos', $data);
        }

        $usuario->full_control = false; // não está no grupo admin. Podemos seguir com o processamento


        if (!empty($usuario->grupos)) {

            // Recuperamos os grupos que o usuário ainda não faz parte

            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');

            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2) // Não recuperamos o grupo de clientes
                ->whereNotIn('id', $gruposExistentes)
                ->findAll();
        } else {

            // Recuperamos todos os grupos, com exceção do grupo ID 2 que é o cliente

            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2) // Não recuperamos o grupo de clientes
                ->findAll();
        }

        return view('Usuarios/grupos', $data);
    }

    public function salvarGrupos()
    {

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();


        // Recupero o post da requisição
        $post = $this->request->getPost();


        // Validamos a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['grupo_id'])) {

            // Retornamos os erros de validação
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um ou mais grupos para salvar'];


            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }


        if (in_array(2, $post['grupo_id'])) {


            // Retornamos os erros de validação
            $retorno['erro'] = 'Por favor verifique os abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo de Clientes não poder ser atribuído de forma manual'];


            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }


        // Verificamos se no POST está vindo o grupo admin (ID 1)
        if (in_array(1, $post['grupo_id'])) {
            $grupoAdmin = [
                'grupo_id' => 1,
                'usuario_id' => $usuario->id
            ];

            // Associamos o usuário em questão apenas ao grupo admin
            $this->grupoUsuarioModel->insert($grupoAdmin);

            //Remove todos os demais grupos que estão associados ao usuário em questão
            $this->grupoUsuarioModel->where('grupo_id !=', 1)
                ->where('usuario_id', $usuario->id)
                ->delete();


            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'Notamos que o Grupo Administrador foi informado, portanto, não há necessidade de informar outros grupos, pois apenas o Administrador será associado ao usuário!');

            return $this->response->setJSON($retorno);
        }



        // Receberá as permissões do POST
        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo) {
            array_push($grupoPush, [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id
            ]);
        }



        $this->grupoUsuarioModel->insertBatch($grupoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

        return $this->response->setJSON($retorno);
    }

    public function removeGrupo(int $principal_id = null)
    {
        // if (! $this->usuarioLogado()->temPermissaoPara('editar-usuarios')) {
        //     return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        // }

        if ($this->request->getMethod() === 'POST') {
            $grupoUsuario = $this->buscaGrupoUsuarioOu404($principal_id);

            if ($grupoUsuario->grupo_id == 2) {
                return redirect()->to(site_url("usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não é permitida a exclusão do usuário do grupo de Clientes");
            }

            $this->grupoUsuarioModel->delete($principal_id);
            return redirect()->back()->with("sucesso", "Usuário removido do grupo de acesso com sucesso!");
        }

        // Não é post
        return redirect()->back();
    }

    /**
     * Método que recupera o usuário
     * 
     * @param interger $id
     * @return Exceptions|object
     * 
     */
    private function buscaUsuarioOu404(int $id = null)
    {

        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }

    /**
     * Método que recupera o registro do grupo associado ao usuário
     *
     * @param integer $principal_id
     * @return Exception|object
     */
    private function buscaGrupoUsuarioOu404(int $principal_id = null)
    {
        if (!$principal_id || !$grupoUsuario = $this->grupoUsuarioModel->find($principal_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $principal_id");
        }

        return $grupoUsuario;
    }

    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {
        // Podemos manipular a imagem que está salva no diretório
        // Redimensionamos a imagem para 300 x 300 e para ficar no centro
        // e fazemos a marca dágua
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);


        $anoAtual = date('Y');

        // Adicionar uma marca d´agua de texto
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - User-ID $usuario_id", [
                'color'      => '#fff',
                'opacity'    => 0.5,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 20,
            ])
            ->save($caminhoImagem);
    }

    private function removeImagemDoFileSystem(string $imagem)
    {
        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }
}
