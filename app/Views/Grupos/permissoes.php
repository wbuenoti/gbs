<?php echo $this->extend('Layout/principal'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<!-- Aqui coloco os estilos da view -->

<?php echo $this->endSection() ?>



<?php echo $this->section('conteudo') ?>


<div class="row">

    <div class="col-lg-8">

    </div>


    <div class="col-lg-4">

        <div class="user-block block">


            <?php if (empty($grupo->permissoes)) : ?>

                <p class="contributions text-warning mt-0">Esse grupo ainda não possui permissões de acesso!</p>

            <?php else : ?>

                <div class="table-responsive">

                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Permissão</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grupo->permissoes as $permissao) : ?>
                                <tr>
                                    <td><?php echo esc($permissao->nome); ?></td>
                                    <td>

                                        <a href="#" class="btn btn-sm btn-danger">Exclluir</a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>

                    <div class="mt-3 ml-1">
                        <?php echo $grupo->pager->links(); ?>
                    </div>

                </div> <!-- ./ block -->

            <?php endif; ?>

        </div>




    </div>


    <?php echo $this->endSection() ?>




    <?php echo $this->section('scripts') ?>

    <!-- Aqui coloco os scripts da view -->

    <?php echo $this->endSection() ?>