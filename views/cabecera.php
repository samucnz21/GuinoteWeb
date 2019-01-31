<?php 
    require_once '../controller/funciones.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guiñote Online</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Samuel Sancho">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Select 2 -->
    <link rel="stylesheet" href="/plugins/select2/select2.min.css">
    <!-- Bootstrap Slider -->
    <link rel="stylesheet" href="/plugins/bootstrap-slider/slider.css">
    <!-- DatePicker -->
    <link rel="stylesheet" href="/plugins/datepicker/css/bootstrap-datepicker.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/bootstrap/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/bootstrap/css/propios.css">
    <link rel="stylesheet" href="/bootstrap/css/ionicons.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.2.3 -->
    <script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/bootstrap/js/app.min.js"></script>
    <!-- Bootbox -->
    <script src="/bootstrap/js/bootbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>

    <?php 
    /******************** SCRIPTS SOCKET.IO ******************/
    ?>
    
    <!-- <script type="text/javascript" src="http://192.168.0.128:8080/socket.io/socket.io.js"></script> -->
    <!-- <script type="text/javascript" src="http://85.251.91.132:8080/socket.io/socket.io.js"></script> -->
    <!-- <script src="/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script> -->
    <!-- <script type="text/javascript" src="https://guinotesamunode.herokuapp.com:8080/socket.io/socket.io.js"></script> -->

    <script type="text/javascript">

        function moverScroll()
        {
            $('#mensajes').animate({scrollTop: ($('#ultimo').first().offset().top)},500);
        }
        
        function nuevoMensaje()
        {
            $(".msg_container").each(function(){
                if($(this).attr("id")=="ultimo")
                {
                    $(this).removeAttr("id");
                }
            });
        }

        var data = { "id_user" : <?php echo $_SESSION['id_user']; ?> , "nombre_user": "<?php echo $_SESSION['usuario'] ?>" };
        //var socket = io.connect('http://192.168.0.128:8080');
        //var socket = io.connect('http://85.251.91.132:8080');
        var socket = io.connect('https://guinotesamunode.herokuapp.com:8080');
        //console.log("conexion");
        //console.log(socket);

        socket.emit('conectarUsuario', data );

        socket.on("usuario_conectado",function(data)
        {
            html = "<tr class='listaConectados' data-id='"+data.id+"'>";
            html += "<td><i class='fa fa-circle' aria-didden='true' style='color:green'></i></td>";
            html += "<td>"+data.nombre+"</td>";
            html += "</tr>"
            $("#lista_usuarios").append(html);
            $("#numConectados").html(data.numConectados)
        });

        socket.on("usuario_desconectado",function(data)
        {

            $(".listaConectados").each(function(){
                if($(this).attr("data-id")==data.id)
                {
                    $(this).remove();
                }
            });
            $("#numConectados").html(data.numConectados);
        });

        /*socket.on("chatMensaje",function(data)
        {
            nuevoMensaje();
            html = "<div id='ultimo' class='row msg_container base_receive'>";
            html += "<div class='col-md-10 col-xs-10'>";
            html += "<div class='messages msg_receive'>";
            html += "<p>" + data.mensaje + "</p>";
            html += "<span>" + data.usuario + "</span>";
            html += "</div>";
            html += "</div>";
            html += "</div>";
            $("#mensajes").append(html);
            moverScroll();
        });*/

        $(window).on("load",function()
        {

            $('body').on("keyup",function(e)
            {
                if(e.keyCode == 13 && $("#btn-input").val()!="")
                {
                    nuevoMensaje();
                    html = "<div id='ultimo' class='row msg_container base_sent'>";
                    html += "<div class='col-md-10 col-xs-10'>";
                    html += "<div class='messages msg_sent'>";
                    html += "<p>" + $("#btn-input").val() + "</p>";
                    html += "<span>" + $("#btn-name").val() + "</span>";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                    $("#mensajes").append(html);
                    var data = { "usuario" : $("#btn-name").val() , "mensaje" : $("#btn-input").val() };
                    socket.emit('enviarMensaje', data );
                    $("#btn-input").val("");
                    moverScroll();  
                }
            });

            $("#btn-chat").on("click",function()
            {   
                if($("#btn-input").val()!="")
                {   
                    nuevoMensaje();          
                    html = "<div id='ultimo' class='row msg_container base_sent'>";
                    html += "<div class='col-md-10 col-xs-10'>";
                    html += "<div class='messages msg_sent'>";
                    html += "<p>" + $("#btn-input").val() + "</p>";
                    html += "<span>" + $("#btn-name").val() + "</span>";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                    $("#mensajes").append(html);
                    var data = { "usuario" : $("#btn-name").val() , "mensaje": $("#btn-input").val() };
                    socket.emit('enviarMensaje', data );
                    $("#btn-input").val("");
                    moverScroll();
                }
            });

            /*$("#crearPartida").on("click",function()
            {
                $.ajax({
                    url:"./controller/ajaxController.php",
                    method:"POST",
                    data:{accion:"crearSala"},
                    dataType: "json",
                    success:function(data)
                    {
                        if(data.exito)
                        {
                                                        
                        }
                        else
                        {
                            
                        }
                    }
                });
                //location.replace("partida/1");
            });*/
        });
    </script>
    
    <?php
    /*************************************************************************/
    ?>    

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body id="body">
    <div class="col-lg-12 main-header navbar">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 pull-left margen-cabecera ">
                <a class="navbar-brand logo-md" href="/principal"><span class="texto-cabecera"><b>Guiñote Online</b></span></a>
            </div>
            <div class="col-lg-1">
            </div>
            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-5">
                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3" style="padding-right: 0px">
                    <div class="nivel"><div class="numero-nivel"><?php obtener_nivel(); ?></div></div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-8 col-xs-6" style="padding-left: 0px;padding-right: 0px">
                    <div class="progress active">
                        <div class="experiencia-nivel"><?php echo siguiente_nivel(); ?>XP</div>
                        <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="<?php echo progreso_nivel(); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo progreso_nivel(); ?>%"></div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3" style="padding-left: 0px">
                    <div class="nivel-derecha"></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 pull-right">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <img class="avatar" src="/img/avatar/defecto/avatar1.png">
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <span class="texto-cabecera">USUARIO</span>
                </div>
            </div>
        </div>
    </div>
