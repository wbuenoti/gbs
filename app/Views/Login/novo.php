<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GBS Serviços | <?php echo $titulo ?> </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>vendor/font-awesome/css/font-awesome.min.css">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>css/font.css">
    <!-- Google fonts - Muli-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>css/style.blue.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo site_url('recursos/'); ?>css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->


    <!-- Espaço reservado para renderizar os estilos de cada view ue estender esse layout-->
    <?php echo $this->renderSection('estilos') ?>

</head>

<body>
    <div class="login-page">
        <div class="container d-flex align-items-center">
            <div class="form-holder has-shadow">
                <div class="row">
                    <!-- Logo & Information Panel-->
                    <div class="col-lg-6">
                        <div class="info d-flex align-items-center">
                            <div class="content">
                                <div class="logo">
                                    <h1><?php echo $titulo ?></h1>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Panel    -->
                    <div class="col-lg-6 bg-white">
                        <div class="form d-flex align-items-center">
                            <div class="content">

                                <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>

                                <div id="response">

                                </div>


                                <div class="form-group">
                                    <input id="login-username" type="text" name="email" required data-msg="Por favor informe seu e-mail" class="input-material">
                                    <label for="login-username" class="label-material">Seu e-mail e acesso</label>
                                </div>
                                <div class="form-group">
                                    <input id="login-password" type="password" name="password" required data-msg="Por favor informe sua senha" class="input-material">
                                    <label for="login-password" class="label-material">Sua Senha</label>

                                </div>
                                <input id="btn-login" type="submit" class="btn btn-primary" value="Entrar">


                                <?php echo form_close(); ?>

                                <a href="#" class="forgot-pass mt-2">Esqueceu a sua senha?</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyrights text-center">
            <p><?php echo date('Y'); ?> &copy; Your company. Download From <a target="_blank" href="https://templateshub.net">Templates Hub</a>.</p>
        </div>
    </div>
    <!-- JavaScript files-->
    <script src="<?php echo site_url('recursos/'); ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo site_url('recursos/'); ?>vendor/popper.js/umd/popper.min.js"> </script>
    <script src="<?php echo site_url('recursos/'); ?>vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo site_url('recursos/'); ?>vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo site_url('recursos/'); ?>js/front.js"></script>




    <script>
        $(document).ready(function() {

            $("#form").on('submit', function(e) {


                e.preventDefault();

                $.ajax({

                    type: 'POST',
                    url: '<?php echo site_url('login/criar'); ?>',
                    data: new FormData(this),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {

                        $("#response").html('');
                        $("#btn-login").val('Por favor aguarde...');

                    },
                    success: function(response) {

                        $("#btn-login").val('Salvar');
                        $("#btn-login").removeAttr("disabled");

                        $('[name=csrf_ordem]').val(response.token);


                        if (!response.erro) {

                            // Tudo certo com a atualização do usuário
                            // Podemos agora redirecioná-lo tranquilamente

                            window.location.href = "<?php echo site_url(); ?>" + response.redirect;

                        }

                        if (response.erro) {

                            // Exitem erros de validação


                            $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');


                            if (response.erros_model) {


                                $.each(response.erros_model, function(key, value) {

                                    $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');

                                });

                            }
                        }


                    },
                    error: function() {

                        alert(
                            'Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.'
                        );
                        $("#btn-login").val('Salvar');
                        $("#btn-login").removeAttr("disabled");

                    }



                });


            });


            $("#form").submit(function() {

                $(this).find(":submit").attr('disabled', 'disabled');

            });


        });
    </script>


</body>

</html>