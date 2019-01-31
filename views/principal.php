    <?php 
        session_start();
        include("cabecera.php");
        if(!isset($_SESSION['id_sala']) || $_SESSION['id_sala']!=0)
        {
            $_SESSION['id_sala'] = 0;
        }
        if(!isset($_SESSION['id_user']) || empty($_SESSION['id_user']))
        {
            session_destroy();
            header("Location: /login");
        }
    ?>
    <script>
        socket.on("chatMensaje",function(data)
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
        });
        $(document).ready(function(){

            /******************************************** EVENTOS MOUSEOVER - MOUSEOUT BOTONES *****************************************/

            $(".content").on("mouseover",".bGGrupos",function(){
                $("body").css('cursor','pointer');
            });
            
            $(".content").on("mouseout",".bGGrupos",function(){
                $("body").css('cursor','default');
            });

            $(".content").on("click","#botonGGrupo",function(){
                sala = $(this).attr("data-id");
                location.replace("partida/"+sala);
                /*socket.emit('conectarSala',sala);
                socket.emit('comprobarUsuariosSala',sala);
                socket.on("limiteUsuarios",function(data)
                {
                    console.log(data);
                    if(data === "true")
                    {
                        alert("Numero maximo de usuarios en la sala");
                    }
                    else
                    {
                        location.replace("partida/1");
                    }
                });*/
                //socket.emit('conectarSala',sala);
                //location.replace("partida/"+sala);
            });

            /****************************************************************************************************************************/
            /*function cargarGrupos(){
                $.ajax({
                    url:"/ajaxController",
                    method:"POST",
                    data:{accion:"obtenerGrupos",id_user:"<?php echo($_SESSION['id_user']) ?>"},
                    success:function(data)
                    {
                        $("#dinamico").html(data);
                    }
                });
            }*/
            
            //$(".content").on("click","#botonGGrupo",cargarGrupos);
            
        });
    </script>
        <div class="content" style="height: 80%;">
            <div class="col-lg-2 col-md-1 col-sm-1">
            </div>
            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-12 lista-salas">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 menu_partida">
                    <?php
                    /*<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 boton_partida">
                        <button class="btn btn-lg btn-block btn-success" id="crearPartida">Crear Partida</button>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 boton_partida">
                        <button class="btn btn-lg btn-block btn-success">Partida Rapida</button>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>*/ 
                    ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                  <!-- small box -->
                    <div id="botonGGrupo" class="small-box bg-aqua bGGrupos" data-id="1">
                        <div class="inner height-box">
                            <p class="botonesMenu">SALA 1</p>
                        </div>
                        <?php /*
                        <div class="icon">
                          <i class="fa fa-users"></i>
                        </div>
                        */?>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="col-lg-3 col-md-5 col-sm-5 col-xs-12" style="height: 100%">
                <div class="panel-pimary usuarios-linea">
                    <div class="info-conectados">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            <div class=" txt-info-usuarios">Conectados:</div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div id="numConectados" class="txt-info-usuarios"></div>
                        </div>
                    </div>
                    <table id="lista_usuarios" class="table table-striped">
                        <?php
                            listar_usuarios($_SESSION['id_user']);
                        ?>
                    </table>
                </div>               
                <div class="panel-primary chat-general">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-comment"></span><span class="txt-info-usuarios">Chat</span>
                    </div>
                    <div id="mensajes" class="panel-body msg_container_base">
                        
                    </div>
                    <div class="panel-footer">
                        <div class="input-group">
                            <input id="btn-input" type="text" class="form-control input-sm chat_input" placeholder="Escribe aqui tu mensaje..." />
                            <input id="btn-name" type="hidden" value="<?php echo $_SESSION['usuario']; ?>"/>
                            <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" id="btn-chat">Send</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        include("footer.php");
    ?>
    </body>
</html>