<div class="form-group">
    <label class="form-control-label">Número do Orçamento:</label>
    <input type="text" name="numero" placeholder="Insira o número do orçamento" class="form-control" value="<?php echo esc($orcamento->numero); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Nome do Cliente:</label>
    <input type="text" name="cliente_nome" placeholder="Insira o nome do cliente" class="form-control" value="<?php echo esc($orcamento->cliente_nome); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Endereço do Cliente:</label>
    <input type="text" name="cliente_endereco" placeholder="Insira o endereço do cliente" class="form-control" value="<?php echo esc($orcamento->cliente_endereco); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Telefone do Cliente:</label>
    <input type="text" name="cliente_telefone" placeholder="Insira o telefone do cliente" class="form-control" value="<?php echo esc($orcamento->cliente_telefone); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Validade da Orçamento:</label>
    <input type="date" name="validade" placeholder="Insira a data de validade da Orçamento" class="form-control" value="<?php echo esc($orcamento->validade); ?>">
</div>

<div class="form-group">
    <label class="form-control-label">Comentário:</label>
    <textarea name="comentario" placeholder="Insira um comentário" class="form-control"><?php echo esc($orcamento->comentario); ?></textarea>
</div>

<div class="form-group">
    <label class="form-control-label">Descrição dos Itens:</label>
    <textarea name="descricao_itens" placeholder="Insira a descrição dos itens" class="form-control"><?php echo esc($orcamento->descricao_itens); ?></textarea>
</div>

<div class="custom-control custom-checkbox">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" name="ativo" value="1" class="custom-control-input" id="ativo" <?php if ($orcamento->ativo == true) : ?> checked <?php endif; ?>>
    <label class="custom-control-label" for="ativo">Orçamento ativo</label>
</div>