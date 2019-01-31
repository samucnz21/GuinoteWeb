<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Porras 1x2</title>
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
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="index.php"><b>Porras</b>1x2</a>
            </div>
          <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Registrate y empieza a jugar</p>
                <form action="./controller/ValidarForms.php" name="registro" method="post">
                    <input type="hidden" name="accion" value="registro"> 
                    <div class="form-group">
                        <input id="user" type="text" name="user" class="form-control" placeholder="Nombre de Usuario" required="">
                    </div>
                    <div class="form-group">
                        <input id="passwd" type="password" name="passwd" class="form-control" placeholder="Contraseña" required="">
                    </div>
                    <div class="form-group">
                        <input id="nombre" type="text" name="nombre" class="form-control" placeholder="Nombre" required="">
                    </div>
                    <div class="form-group">
                        <input id="apellidos" type="text" name="apellidos" class="form-control" placeholder="Apellidos" required="">
                    </div>
                    <div class="form-group">
                        <input id="email" type="mail" name="email" class="form-control" placeholder="Correo Electronico" required="">
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-xs-5">
                            <input type="submit" id="registro" class="btn btn-primary btn-block btn-flat" name="registraro" value="Registrarse">
                        </div>

                        <div class="col-xs-4 pull-right">
                            <a class="btn btn-primary btn-block btn-flat" href="/">Volver</a>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
          <!-- /.login-box-body -->
        </div>
    </body>
</html>
