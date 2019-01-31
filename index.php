<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Guiñote Online</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="author" content="Samuel Sancho">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="bootstrap/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="bootstrap/css/skins/_all-skins.min.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- jQuery 2.2.3 -->
        <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="bootstrap/js/app.min.js"></script>
        <!-- Sparkline -->
        <script src="plugins/sparkline/jquery.sparkline.min.js"></script>
        <!-- SlimScroll 1.3.0 -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- ChartJS 1.0.1 -->
        <script src="plugins/chartjs/Chart.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#acceder").click(function(){
                    $.ajax({
                        url:"./controller/ajaxController.php",
                        method:"POST",
                        data:$("#login").serialize(),
                        dataType: "json",
                        success:function(data)
                        {
                            if(data.exito)
                            {
                                id_user = data.id_user;
                                $("#error").prop("hidden",true);
                                location.replace("principal");                            
                            }
                            else
                            {
                                $("#user").trigger("reset");
                                $("#passwd").attr("value","");
                                $("#error").prop("hidden",false);
                            }
                        }
                    });
                });
                $("#close").click(function(){
                    $("#error").prop("hidden",true);
                    $("#exito").prop("hidden",true);
                });
            });
        </script>
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
          <div class="login-logo">
            <a href="/login"><b>Guiñote Online</b></a>
          </div>
          <!-- /.login-logo -->
          <?php
            /*
                if($_GET['exito'])
                {
                    ?>
                        <div id="exito" class="alert alert-success">¡Exito! Te has registrado correctamente <button type="button" id="close" class="close" >&times;</button></div>
                    <?php
                }
                elseif($_GET['error']) 
                {
                    ?>
                        <div id="error" class="alert alert-danger">¡Error! Se ha producido un error al registrarte, vuelve a intentarlo <button type="button" id="close" class="close" >&times;</button></div>
                    <?php
                }
            */
          ?>
          <div class="login-box-body">
            <p class="login-box-msg">Registrate y empieza a jugar</p>
            <form id="login" name="login" method="post">
              <div id="error" class="alert alert-danger" hidden>¡Error! Usuario o contraseña incorrectos <button type="button" id="close" class="close" >&times;</button></div> 
              <input type="hidden" name="accion" value="login"> 
              <div class="form-group has-feedback">
                <input id="user" type="text" name="user" class="form-control" placeholder="Usuario" required=""><i class="fa fa-user form-control-feedback" aria-hidden="true"></i>
              </div>
              <div class="form-group has-feedback">
                    <input id="passwd" type="password" name="passwd" class="form-control" placeholder="Contraseña" required=""><i class="fa fa-lock form-control-feedback" aria-hidden="true"></i>
              </div>
              <div class="row">
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="button" id="acceder" class="btn btn-primary btn-block btn-flat">Acceder</button>
                </div>
                <?php 
                    /*
                        <div class="col-xs-4 pull-right">
                            <a class="btn btn-primary btn-block btn-flat" href="/">Volver</a>
                        </div>
                    */
                ?> 
                <!-- /.col -->
              </div>
            </form>
            <hr>
            <!-- /.social-auth-links -->
            <a href="/registro" class="text-center">Registrate si no tienes cuenta</a>
          </div>
          <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
    </body>
</html>
