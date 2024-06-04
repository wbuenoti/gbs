<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\OrcamentoModel;
use App\Models\OrcamentoItemModel;

use App\Entities\Orcamento;

class Orcamentos extends BaseController
{

    private $orcamentoModel;
    private $orcamentoItemModel;

    public function __construct()
    {
        $this->orcamentoModel = new OrcamentoModel();
        $this->orcamentoItemModel = new OrcamentoItemModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os orçamentos',
        ];

        return view('Orcamentos/index', $data);
    }

    public function recuperaOrcamentos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'cliente',
            'data',
            'valor_total',
            'status',
            'deletado_em'
        ];

        $orcamentos = $this->orcamentoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Receberá o array de objetos de orçamentos
        $data = [];

        foreach ($orcamentos as $orcamento) {

            $data[] = [
                'cliente' =>  anchor("orcamentos/exibir/$orcamento->id", esc($orcamento->cliente), 'title="Exibir orçamento - ' . esc($orcamento->cliente) . '"'),
                'data' => esc($orcamento->data),
                'valor_total' => 'R$ ' . number_format($orcamento->valor_total, 2, ',', '.'),
                'status'  => $orcamento->status,
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $data = [
            'titulo' => 'Criando novo orçamento',
        ];

        return view('orcamentos/criar', $data);
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

        // Crio novo objeto da Entidade Orcamento
        $orcamento = new Orcamentos($post);

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT  
        //if ($this->orcamentoModel->protect(false)->insert($orcamento)) {
        if ($this->orcamentoModel->protect(false)->save($orcamento)) {
            $btnCriar = anchor("orcamentos/criar", 'Cadastrar Novo Orçamento', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            // Retornamos o último ID inserido na tabela de orcamentos
            //Ou seja, o ID do orçamento recém criado
            $retorno['id'] = $this->orcamentoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->orcamentoModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        // Busque o orçamento pelo ID ou retorne 404 se não encontrado
        $orcamento = $this->buscaOrcamentoOu404($id);

        // Prepare os dados para a visualização
        $data = [
            'titulo' => "Detalhando o orçamento #" . esc($orcamento->id),
            'orcamento' => $orcamento,
        ];

        // Retorne a visualização com os dados do orçamento
        return view('Orcamentos/exibir', $data);
    }

    public function editar(int $id = null)
    {
        // Busque o orçamento pelo ID ou retorne 404 se não encontrado
        $orcamento = $this->buscaOrcamentoOu404($id);

        // Verifique se o orçamento foi deletado logicamente
        if ($orcamento->deletado_em != null) {
            return redirect()->back()->with('info', "Orçamento #$orcamento->id encontra-se excluído, não poderá ser editado");
        }

        // Prepare os dados para a visualização
        $data = [
            'titulo' => "Editando o orçamento #" . esc($orcamento->id),
            'orcamento' => $orcamento,
        ];

        // Retorne a visualização com os dados do orçamento
        return view('Orcamentos/editar', $data);
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

        // Validamos a existência do orçamento
        $orcamento = $this->buscaOrcamentoOu404($post['id']);

        // Preenchemos os atributos do orçamento com os valores do POST
        $orcamento->fill($post);

        if ($orcamento->hasChanged() == false) {
            $retorno['info'] = 'Não existem dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        // CASO NÃO SALVAR, OLHAR AQUI E MUDAR PARA INSERT
        if ($this->orcamentoModel->protect(false)->save($orcamento)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os campos abaixo e tente novamente';
        $retorno['erros_model'] = $this->orcamentoModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function adicionarItem()
    {
        $postData = $this->request->getPost();
        $orcamentoId = $postData['orcamento_id'];

        $item = [
            'orcamento_id' => $orcamentoId,
            'descricao' => $postData['descricao'],
            'quantidade' => $postData['quantidade'],
            'valor_unitario' => $postData['valor_unitario'],
            'montante' => $postData['quantidade'] * $postData['valor_unitario'],
        ];

        $this->orcamentoItemModel->save($item);
        $this->atualizarTotalOrcamento($orcamentoId);

        return redirect()->back()->with('sucesso', 'Item adicionado com sucesso!');
    }

    public function removerItemOrcamento($id = null)
    {
        $item = $this->orcamentoItemModel->find($id);

        if (!$item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Item não encontrado");
        }

        $orcamentoId = $item->orcamento_id;
        $this->orcamentoItemModel->delete($id);
        $this->atualizarTotalOrcamento($orcamentoId);

        return redirect()->back()->with('sucesso', 'Item removido com sucesso!');
    }

    private function atualizarTotalOrcamento($orcamentoId)
    {
        $itens = $this->orcamentoItemModel->where('orcamento_id', $orcamentoId)->findAll();
        $total = array_sum(array_column($itens, 'montante'));

        $this->orcamentoModel->update($orcamentoId, ['total' => $total]);
    }

    public function excluir(int $id = null)
    {
        // Busca o orçamento ou retorna um erro 404
        $orcamento = $this->buscaOrcamentoOu404($id);

        // Condição de exemplo, ajuste conforme necessário
        // Verificar se o orçamento pode ser excluído
        if ($orcamento->deletado_em != null) {
            return redirect()->back()->with('info', "Esse orçamento já encontra-se excluído");
        }

        if ($this->request->getMethod() === 'POST') {

            // Excluir o orçamento
            $this->orcamentoModel->delete($orcamento->id);

            // Redireciona com mensagem de sucesso
            return redirect()->to(site_url('orcamentos'))->with('sucesso', 'Orçamento ' . esc($orcamento->nome) . ' excluído com sucesso!');
        }

        // Dados para a view
        $data = [
            'titulo' => "Excluindo o orçamento " . esc($orcamento->nome),
            'orcamento' => $orcamento,
        ];

        // Carrega a view de exclusão
        return view('Orcamentos/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {

        $orcamento = $this->buscaOrcamentoOu404($id);

        if ($orcamento->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas orçamentos excluídos podem ser recuperados");
        }


        $orcamento->deletado_em = null;
        $this->orcamentoModel->protect(false)->save($orcamento);

        return redirect()->back()->with('sucesso', "Usuário $orcamento->nome Recuperado com sucesso!");
    }


    /**
     * Método que recupera o usuário
     * 
     * @param interger $id
     * @return Exceptions|object
     * 
     */
    private function buscaOrcamentoOu404(int $id)
    {
        // Tente encontrar o orçamento pelo ID
        $orcamento = $this->orcamentoModel->find($id);

        // Se não encontrar, mostre a página 404
        if ($orcamento === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Orçamento não encontrado: $id");
        }

        // Retorne o orçamento encontrado
        return $orcamento;
    }
}
