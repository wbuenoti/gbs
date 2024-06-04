<?php echo $this->extend('Layout/principal'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<!-- Aqui coloco os estilos da view -->

<?php echo $this->endSection() ?>



<?php echo $this->section('conteudo') ?>


<div class="row">


    <div class="col-lg-12">

        <div class="user-block block">

            <!-- <div class="text-center">

                <?php if ($orcamento->imagem == null) : ?>

                    <img src="<?php echo site_url('recursos/img/usuario_sem_imagem.png'); ?>" class="card-img-top" style="width: 90%" alt="Usuário sem imagem">


                <?php else : ?>

                    <img src="<?php echo site_url("usuarios/imagem/$orcamento->imagem"); ?>" class="card-img-top" style="width: 90%" alt="<?php echo esc($orcamento->nome); ?>">


                <?php endif; ?>


                <a href="<?php echo site_url("usuarios/editarimagem/$orcamento->id") ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>


            </div> -->

            <!-- <hr class="border-secondary"> -->


            <h5 class="card-title mt-2"><?php echo esc($orcamento->numero); ?></h5>
            <p class="card-text"><strong>Data:</strong> <?php echo esc($orcamento->data); ?></p>
            <p class="card-text"><strong>Orcamento:</strong> <?php echo $orcamento->exibeSituacao(); ?></p>
            <!-- <strong>Usuário:</strong>
            <p class="contributions mt-0"><?php echo $orcamento->exibeSituacao(); ?></p> -->
            <p class="card-text"><strong>Criado em:</strong> <?php echo $orcamento->criado_em->humanize(); ?></p>
            <p class="card-text"><strong>Atualizado em:</strong> <?php echo $orcamento->atualizado_em->humanize(); ?></p>
            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">

                    <?php if ($orcamento->deletado_em == null) : ?>
                        <a class="dropdown-item" href="<?php echo site_url("orcamentos/editar/$orcamento->id"); ?>">Editar Orçamento</a>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>

                    <?php if ($orcamento->deletado_em == null) : ?>

                        <a class="dropdown-item" href="<?php echo site_url("orcamentos/excluir/$orcamento->id"); ?>">Excluir
                            Orçamento</a>

                    <?php else : ?>

                        <a class="dropdown-item" href="<?php echo site_url("orcamentos/desfazerexclusao/$orcamento->id"); ?>">Restaurar Orçamento</a>

                    <?php endif; ?>


                </div>
            </div>

            <a href="<?php echo site_url("orcamentos") ?>" class="btn btn-outline-secondary ml-2">Voltar</a>

        </div> <!-- ./ block -->

    </div>




</div>


<?php echo $this->endSection() ?>




<?php echo $this->section('scripts') ?>

<!-- Aqui coloco os scripts da view -->

<?php echo $this->endSection() ?>