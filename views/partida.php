    <?php 
        session_start();
        include("cabecera.php");
        if(!isset($_SESSION['id_sala']) || $_SESSION['id_sala']==0)
        {
            $_SESSION['id_sala'] = $_GET['partida'];
        }
        if(!isset($_SESSION['cartas']) || sizeof($_SESSION['cartas']) == 0)
        {
            $_SESSION['cartas'] = array();
        }
        if(!isset($_SESSION['id_user']) || empty($_SESSION['id_user']))
        {
            session_destroy();
            header("Location: /login");
        }
    ?>
        <script>
            cartas = new Array();
            todasCartasRepartidas = false;
            triunfo = 99;
            turno = false;
            puntosJugador = 0;
            puntosRival = 0;
            arrastre = false;

            for(i = 0; i <= 39 ; i++)
            {
                cartas[i]=i;
            }

            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev) {
                ev.dataTransfer.setData("text", ev.target.id);
            }

            function drop(ev) {
                try
                {
                    ev.preventDefault();
                    var data = ev.dataTransfer.getData("text");
                    ev.target.appendChild(document.getElementById(data));
                    carta = data.substr(5);
                    socket.emit("moverCarta",{carta:carta,sala:<?php echo $_SESSION['id_sala']; ?>});
                    $(".cartas_mano").children('img').removeAttr('draggable');
                    $(".cartas_mano").children('img').removeAttr('ondragstart');
                    $(".cartas_mano").children('img').removeClass('puedeLanzarse');
                    turno = false;
                    socket.emit("pasarTurno",{sala:<?php echo $_SESSION['id_sala']; ?>});
                    try
                    {
                        $("#botonCantar").remove();
                        $("#botonCambiarSiete").remove();
                    }
                    catch(err)
                    {}
                }
                catch(err)
                {
                    //alert("No es tu turno");
                }
            }

            function comprobarUsuariosSala()
            {
                socket.emit("comprobarUsuariosSala",<?php echo $_SESSION['id_sala']; ?>);
            }

            function limpiarTablero()
            {
                $("#miJugada").children('img').remove();
                $("#jugadaRival").children('img').remove();
            }

            function repartirCartas(z)
            {
                if(cartas.length > 1)
                {
                    for( i = 0; i < z ; i++ )
                    {
                        carta = Math.round(Math.random()*40)-1;
                        if(cartas.indexOf(carta) != -1 && carta >=0 && carta < 40)
                        {
                            if( i === 12)
                            {
                                $("#montonCartas").append("<div id='divMonton' style='height: 250px;width: 241px;'>");
                                $("#divMonton").append("<img src='/img/baraja/41.png' class='img-responsive carta_centro_oculta' alt=''>");
                                $("#divMonton").append("<img id='triunfo' src='/img/baraja/"+carta+".png' class='img-responsive carta_centro_visible' alt=''>");
                                $("#montonCartas").append("</div>");
                                cartas.splice(cartas.indexOf(carta),1);
                                triunfo = carta;       
                                socket.emit("colocarMonton",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta});
                            }
                            else if(i % 2 === 0)
                            {
                                for(j=1;j<=6;j++)
                                {
                                    if($("#tablero"+j).has('img').length == 0)
                                    {
                                        $("#tablero"+j).append('<img src="/img/baraja/41.png" class="img-responsive carta_mano" alt="">');
                                        cartas.splice(cartas.indexOf(carta),1);
                                        socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta,mostrar:true});
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                for(j=1;j<=6;j++)
                                {
                                    if($("#mano"+j).has('img').length == 0)
                                    {
                                        $("#mano"+j).append("<img id='carta"+carta+"' src='/img/baraja/"+carta+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                        cartas.splice(cartas.indexOf(carta),1);
                                        socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta});
                                        break;
                                    }
                                }
                            }
                        }
                        else
                        {
                            i--;
                        }
                    }
                }
                else if(cartas.length == 1)
                {
                    if(turno)
                    {
                        carta = numeroCarta($("#triunfo").attr('src'));
                        $("#divMonton").remove();
                        for(j=1;j<=6;j++)
                        {
                            //if($("#mano"+j).has('img').length == 0)
                            if($("#tablero"+j).has('img').length == 0)
                            {
                                $("#tablero"+j).append('<img src="/img/baraja/41.png" class="img-responsive carta_mano" alt="">');
                                //$("#mano"+j).append("<img id='carta"+carta+"' src='/img/baraja/"+carta+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                //cartas.splice(cartas.indexOf(carta),1);
                                //socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta});
                                socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta,mostrar:true});
                                break;
                            }
                        }
                        socket.emit("eliminarMonton",{sala:<?php echo $_SESSION['id_sala']; ?>})
                    }
                    carta = cartas[0];
                    for(j=1;j<=6;j++)
                    {
                        //if($("#tablero"+j).has('img').length == 0)
                        if($("#mano"+j).has('img').length == 0)
                        {
                            $("#mano"+j).append("<img id='carta"+carta+"' src='/img/baraja/"+carta+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                            //$("#tablero"+j).append('<img src="/img/baraja/41.png" class="img-responsive carta_mano" alt="">');
                            cartas.splice(cartas.indexOf(carta),1);
                            //socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta,mostrar:true});
                            socket.emit("repartircarta",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:carta});
                            break;
                        }
                    }
                    socket.emit("vaciarArray",{sala:<?php echo $_SESSION['id_sala']; ?>});
                    todasCartasRepartidas = true;
                }
            }

            function numeroCarta(carta)
            {
                switch(carta.length)
                {
                    case 17:
                        carta = carta.substring(12,13);
                        break;
                    case 18:
                        carta = carta.substring(12,14);
                        break;                    
                }
                return carta;
            }

            function comprobarCartaGanadora(miJugada, jugadaRival)
            {
                numCartaJug = (miJugada.length == 1 ? miJugada : miJugada.substring(1,2) );
                numCartaRival = (jugadaRival.length == 1 ? jugadaRival : jugadaRival.substring(1,2) );

                if((numCartaJug == 0 || numCartaJug == 2 || numCartaJug == 7 || numCartaJug == 8 || numCartaJug == 9) || (numCartaRival == 0 || numCartaRival == 2 || numCartaRival == 7 || numCartaRival == 8 || numCartaRival == 9))
                {
                    switch(parseInt(numCartaJug))
                    {
                        case 0:
                            //console.log("GANA JUGADOR");
                            return true;
                            break;
                        case 2:
                            if(numCartaRival != 0)
                            {
                                //console.log("GANA JUGADOR");
                                return true;
                            }
                            else
                            {
                                //console.log("GANA RIVAL");
                                return false;
                            }
                            break;
                        case 7:
                            if(numCartaRival != 0 && numCartaRival != 2 && numCartaRival != 9)
                            {
                                //console.log("GANA JUGADOR");
                                return true;
                            }
                            else
                            {
                                //console.log("GANA RIVAL");
                                return false;
                            }
                            break;
                        case 8:
                            if(numCartaRival != 0 && numCartaRival != 2 && numCartaRival != 7 && numCartaRival != 9)
                            {
                                //console.log("GANA JUGADOR");
                                return true;
                            }
                            else
                            {
                                //console.log("GANA RIVAL");
                                return false;
                            }
                            break;
                        case 9:
                            if(numCartaRival != 0 && numCartaRival != 2)
                            {
                                //console.log("GANA JUGADOR");
                                return true;
                            }
                            else
                            {
                                //console.log("GANA RIVAL");
                                return false;
                            }
                            break;
                    }
                }
                else
                {
                    if(numCartaJug > numCartaRival)
                    {
                        console.log("GANA JUGADOR");
                        return true;
                    }
                    else
                    {
                        console.log("GANA RIVAL");
                        return false;
                    }
                }
            }

            function comprobarGandaorRonda()
            {
                try{
                    triunfo = numeroCarta($("#triunfo").attr('src'));
                }
                catch(err)
                {}
                miJugada = numeroCarta($("#miJugada").children('img').attr('src'));
                jugadaRival = numeroCarta($("#jugadaRival").children('img').attr('src'));
                paloTriunfo = (triunfo.length > 1) ? (triunfo.substring(0,1)*10) : 0;
                paloMiJugada = (miJugada.length) > 1 ? (miJugada.substring(0,1)*10) : 0;
                paloJugadaRival = (jugadaRival.length) > 1 ? (jugadaRival.substring(0,1)*10) : 0;

                //UNA CARTA TRIUNFO
                if(paloMiJugada == paloTriunfo || paloJugadaRival == paloTriunfo)
                {
                    if(paloMiJugada == paloJugadaRival)
                    {
                        ganador = comprobarCartaGanadora(miJugada,jugadaRival);
                    }
                    else if(paloMiJugada != paloJugadaRival && paloJugadaRival == paloTriunfo)
                    {
                        ganador = false;
                    }
                    else if(paloMiJugada != paloJugadaRival && paloMiJugada == paloTriunfo)
                    {
                        ganador = true;
                    }
                }
                // MISMO PALO
                else if(paloMiJugada == paloJugadaRival)
                {
                    ganador = comprobarCartaGanadora(miJugada,jugadaRival);
                }
                // DISTINTOS PALOS
                else
                {
                    if(turno)
                    {
                        ganador = true;
                    }
                    else
                    {
                        ganador = false;
                    }
                }
                return ganador;
            }

            function puntuacionCarta(carta)
            {
                numCartaJug = (carta.length == 1 ? carta : carta.substring(1,2) );
                puntos = 0;
                switch(parseInt(numCartaJug))
                {
                    case 0:
                        puntos = 11;
                        break;
                    case 2:
                        puntos = 10;
                        break;
                    case 7:
                        puntos = 3;
                        break;
                    case 8:
                        puntos = 2;
                        break;
                    case 9:
                        puntos = 4;
                        break;
                }
                return puntos;
            }

            function mostrarTxtTurno(turno)
            {
                if(turno)
                {
                    $("#texto_turno").children("p").remove();
                    $("#texto_turno").attr("style","background-color:#008d4c");
                    $("#texto_turno").append("<p class='texto-cabecera txt_turno'>MI TURNO</p>");
                }
                else
                {
                    $("#texto_turno").children("p").remove();
                    $("#texto_turno").attr("style","background-color:#d33724");
                    $("#texto_turno").append("<p class='texto-cabecera txt_turno'>RIVAL</p>");
                }
            }

            function comprobarCantar()
            {
                cartasMano = new Array();
                cartasOros = new Array();
                cartasCopas = new Array();
                cartasEspadas = new Array();
                cartasBastos = new Array();
                cantar = false;
                for(i=1;i<=6;i++)
                {
                    if(!($("#mano"+i).children('img').hasClass('cantada')))
                    {
                        numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                        cartasMano.push(numCarta);
                        if(numCarta.length == 1)
                        {
                            cartasOros.push(numCarta);
                        }
                        else
                        {
                            switch(parseInt(numCarta.substring(0,1)))
                            {
                                case 1:
                                    cartasCopas.push(numCarta);
                                    break;
                                case 2:
                                    cartasEspadas.push(numCarta);
                                    break;
                                case 3:
                                    cartasBastos.push(numCarta);
                                    break;                    
                            }
                        }
                    }
                }

                if(cartasOros.length > 1)
                {
                    sota = 0;
                    rey = 0;
                    for(i = 0;i<cartasOros.length;i++)
                    {
                        switch(parseInt(cartasOros[i]))
                        {
                            case 7:
                                sota = cartasOros[i];
                                break;
                            case 9:
                                rey = cartasOros[i];
                                break;                  
                        }
                    }
                    if(sota != 0 && rey != 0)
                    {
                        cantar = true;
                    }
                }
                if(cartasCopas.length > 1)
                {
                    sota = 0;
                    rey = 0;
                    for(i = 0;i<cartasCopas.length;i++)
                    {
                        switch(parseInt(cartasCopas[i]))
                        {
                            case 17:
                                sota = cartasCopas[i];
                                break;
                            case 19:
                                rey = cartasCopas[i];
                                break;                  
                        }
                    }
                    if(sota != 0 && rey != 0)
                    {
                        cantar = true;
                    }
                }
                if(cartasEspadas.length > 1)
                {
                    sota = 0;
                    rey = 0;
                    for(i = 0;i<cartasEspadas.length;i++)
                    {
                        switch(parseInt(cartasEspadas[i]))
                        {
                            case 27:
                                sota = cartasEspadas[i];
                                break;
                            case 29:
                                rey = cartasEspadas[i];
                                break;                  
                        }
                    }
                    if(sota != 0 && rey != 0)
                    {
                        cantar = true;
                    }
                }
                if(cartasBastos.length > 1)
                {
                    sota = 0;
                    rey = 0;
                    for(i = 0;i<cartasBastos.length;i++)
                    {
                        switch(parseInt(cartasBastos[i]))
                        {
                            case 37:
                                sota = cartasBastos[i];
                                break;
                            case 39:
                                rey = cartasBastos[i];
                                break;                  
                        }
                    }
                    if(sota != 0 && rey != 0)
                    {
                        cantar = true;
                    }
                }
                console.log("cantar: "+ cantar);
                return cantar;
            }

            function comprobarSiete()
            {
                if(!todasCartasRepartidas)
                {
                    cambiarSiete = false;
                    try{
                        triunfo = numeroCarta($("#triunfo").attr('src'));
                    }
                    catch(err)
                    {}
                    paloTriunfo = (triunfo.length > 1) ? (triunfo.substring(0,1)*10) : 0;
                    for(i=1;i<=6;i++)
                    {
                        numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                        paloCarta = (numCarta.length) > 1 ? (numCarta.substring(0,1)*10) : 0;
                        if(parseInt(paloTriunfo) == parseInt(paloCarta))
                        {
                            switch(parseInt(paloTriunfo))
                            {
                                case 0:
                                    if(numCarta == 6)
                                    {
                                        cambiarSiete = true;
                                    }
                                    break;
                                case 10:
                                    if(numCarta == 16)
                                    {
                                        cambiarSiete = true;
                                    }
                                    break;
                                case 20:
                                    if(numCarta == 26)
                                    {
                                        cambiarSiete = true;
                                    }
                                    break;
                                case 30:
                                    if(numCarta == 36)
                                    {
                                        cambiarSiete = true;
                                    }
                                    break;
                            }
                        }
                    }
                    return cambiarSiete;
                }
                else
                {
                    return false;
                }
            }

            function comprobarCartasArrastre()
            {
                cartasGanan = new Array();
                cartasGananTriunfo = new Array();
                try
                {
                    triunfo = numeroCarta($("#triunfo").attr('src'));
                }
                catch(err)
                {}
                paloTriunfo = (triunfo.length > 1) ? (triunfo.substring(0,1)*10) : 0;
                jugadaRival = numeroCarta($("#jugadaRival").children('img').attr('src'));

                for(i=1;i<=6;i++)
                {
                    try
                    {
                        numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                        paloNumCarta = (numCarta.length > 1) ? (numCarta.substring(0,1)*10) : 0;
                        paloJugadaRival = (jugadaRival.length > 1) ? (jugadaRival.substring(0,1)*10) : 0;
                        if(paloNumCarta == paloJugadaRival)
                        {
                            cartasGanan.push(numCarta);
                        }
                        else if(paloNumCarta == paloTriunfo)
                        {
                            cartasGananTriunfo.push(numCarta);
                        }
                    }
                    catch(err)
                    {}
                }
                console.log(cartasGanan);
                console.log(cartasGananTriunfo);
                if(cartasGanan.length>0)
                {
                    cartaMayor = false;
                    for(i=0;i<cartasGanan.length;i++)
                    {
                        if(comprobarCartaGanadora(cartasGanan[i],jugadaRival))
                        {
                            cartaMayor = true;
                            break;
                        }
                    }
                    if(cartaMayor)
                    {
                        for(i=1;i<=6;i++)
                        {
                            try
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                for(z=0;z<cartasGanan.length;z++)
                                {
                                    if(numCarta == cartasGanan[z])
                                    {
                                        if(comprobarCartaGanadora(cartasGanan[z],jugadaRival))
                                        {
                                            $("#mano"+i).children('img').attr('draggable','true');
                                            $("#mano"+i).children('img').attr('ondragstart','drag(event)');
                                            $("#mano"+i).children('img').addClass('puedeLanzarse');
                                        }
                                    }
                                }
                            }
                            catch(err)
                            {}
                        }
                    }
                    else
                    {
                        for(i=1;i<=6;i++)
                        {
                            try
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                for(z=0;z<cartasGanan.length;z++)
                                {
                                    if(numCarta == cartasGanan[z])
                                    {
                                        $("#mano"+i).children('img').attr('draggable','true');
                                        $("#mano"+i).children('img').attr('ondragstart','drag(event)');
                                        $("#mano"+i).children('img').addClass('puedeLanzarse');
                                    }
                                }
                            }
                            catch(err)
                            {}
                        }
                    }
                }
                else if(cartasGananTriunfo.length > 0)
                {
                    for(i=1;i<=6;i++)
                    {
                        try
                        {
                            numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                            for(z=0;z<cartasGananTriunfo.length;z++)
                            {
                                if(numCarta == cartasGananTriunfo[z])
                                {
                                    $("#mano"+i).children('img').attr('draggable','true');
                                    $("#mano"+i).children('img').attr('ondragstart','drag(event)');
                                    $("#mano"+i).children('img').addClass('puedeLanzarse');
                                }
                            }
                        }
                        catch(err)
                        {}
                    }
                }
                else
                {
                    for(i=1;i<=6;i++)
                    {
                        $("#mano"+i).children('img').attr('draggable','true');
                        $("#mano"+i).children('img').attr('ondragstart','drag(event)');
                        $("#mano"+i).children('img').addClass('puedeLanzarse');
                    }
                }
            }

            comprobarUsuariosSala();

            socket.on("limiteUsuarios",function(data)
            {
                if(data === "true")
                {
                    location.replace("/principal");
                }
            });

            socket.on('jugadorNumero',function(data){
                if(data == 1)
                {
                    turno = true;
                }
                else
                {
                    turno = false;
                }
            });

            socket.on('colocarMonton',function(data)
            {
                $("#montonCartas").append("<div id='divMonton' style='height: 250px;width: 241px;'>");
                $("#divMonton").append("<img src='/img/baraja/41.png' class='img-responsive carta_centro_oculta' alt=''>");
                $("#divMonton").append("<img id='triunfo' src='/img/baraja/"+data.carta+".png' class='img-responsive carta_centro_visible' alt=''>");
                $("#montonCartas").append("</div>");
                triunfo = data.carta;
                cartas.splice(cartas.indexOf(data.carta),1);
            });

            socket.on('cartaRival',function(data){
                carta=data.carta;
                if(data.mostrar)
                {
                    for(i=1;i<=6;i++)
                    {
                        if($("#mano"+i).has('img').length == 0)
                        {
                            $("#mano"+i).append("<img id='carta"+carta+"' src='/img/baraja/"+carta+".png' class='img-responsive carta_mano' alt=''>")
                            cartas.splice(cartas.indexOf(carta),1);
                            break;
                        }
                    }
                }
                else
                {
                    for(i=1;i<=6;i++)
                    {
                        if($("#tablero"+i).has('img').length == 0)
                        {
                            $("#tablero"+i).append('<img src="/img/baraja/41.png" class="img-responsive carta_mano" alt="">')
                            cartas.splice(cartas.indexOf(carta),1);
                            break;
                        }
                    }
                }
            });

            socket.on('moverCartaRival',function(data){
                carta = Math.round(Math.random()*6)+1;
                while($("#tablero"+carta).has('img').length != 1)
                {
                    carta = Math.round(Math.random()*6)+1;
                }
                $("#jugadaRival").append('<img src="/img/baraja/'+data+'.png" class="img-responsive carta_mano" alt="">');
                $("#tablero"+carta).children('img').remove();
            });

            socket.on('cambioTurno',function(data){
                if($("#miJugada").has('img').length > 0 && $("#jugadaRival").has('img').length > 0)
                {
                    turno = true;
                    socket.emit("prepararRonda",{sala:<?php echo $_SESSION['id_sala']; ?>});
                }
                else
                {
                    turno = true;
                    if(todasCartasRepartidas)
                    {
                       comprobarCartasArrastre();
                    }
                    else
                    {
                        $(".cartas_mano").children('img').attr('draggable','true');
                        $(".cartas_mano").children('img').attr('ondragstart','drag(event)');
                    }
                    mostrarTxtTurno(turno);
                    socket.emit("mostrarTxtTurno",{sala:<?php echo $_SESSION['id_sala']; ?>,turno:false});
                }
            });

            socket.on('limpiarTablero',function(data){
                limpiarTablero();   
                if(turno)
                {
                    if(todasCartasRepartidas)
                    {
                        sinCartas = true;
                        for(j=1;j<=6;j++)
                        {
                            if($("#tablero"+j).has('img').length != 0)
                            {
                                sinCartas = false;
                                break;
                            }
                        }
                        if(sinCartas)
                        {
                            puntosJugador = puntosJugador + 10;
                            socket.emit("sumarDiezUltimas",{sala:<?php echo $_SESSION['id_sala']; ?>});
                            socket.emit("mostrarResultadosPartida",{sala:<?php echo $_SESSION['id_sala']; ?>});
                        }
                    }
                    else
                    {
                        repartirCartas(2);  
                        if(comprobarCantar())
                        {
                            $("#cantar").append('<button type="button" id="botonCantar" class="btn btn-block btn-lg btn-success">Cantar</button>');
                            alert("Puedes Cantar");
                        }
                        if(comprobarSiete())
                        {
                            $("#cambiarSiete").append('<button type="button" id="botonCambiarSiete" class="btn btn-block btn-lg btn-success">Cambiar 7</button>');
                            alert("puedes cambiar el 7");
                        }
                    }
                }
            });

            socket.on('eliminarMonton',function(data){
                $("#divMonton").remove();
            });

            socket.on('mostrarTextoTurno',function(data){
                mostrarTxtTurno(data.turno);
            });

            socket.on('iniciarNuevaRonda',function(data){
                try
                {
                    $("#botonCantar").remove();
                    $("#botonCambiarSiete").remove();
                }
                catch(err)
                {}
                // Comprobar quien gana la jugada
                ganador = comprobarGandaorRonda();
                if(ganador)
                {
                    miJugada = numeroCarta($("#miJugada").children('img').attr('src'));
                    puntosJugador = puntosJugador + puntuacionCarta(miJugada);
                    jugadaRival = numeroCarta($("#jugadaRival").children('img').attr('src'));
                    puntosJugador = puntosJugador + puntuacionCarta(jugadaRival);
                    console.log("Puntos Jugador: "+puntosJugador);
                }
                else
                {
                    miJugada = numeroCarta($("#miJugada").children('img').attr('src'));
                    puntosRival = puntosRival + puntuacionCarta(miJugada);
                    jugadaRival = numeroCarta($("#jugadaRival").children('img').attr('src'));
                    puntosRival = puntosRival + puntuacionCarta(jugadaRival);
                    console.log("Puntos Rival: "+puntosRival);
                }                    
                //Pasar el truno al jugador que corresponda
                if(ganador)
                {
                    turno = true;
                    $(".cartas_mano").children('img').attr('draggable','true');
                    $(".cartas_mano").children('img').attr('ondragstart','drag(event)');
                    mostrarTxtTurno(turno);
                    //Limpiar tablero
                    if(arrastre)
                    {
                        if(puntosJugador >= 101)
                        {
                            bootbox.dialog({
                                title: 'Enhorabuena! Has Ganado',
                                message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                            });
                            $(".cartas_mano").children('img').remove();
                            $(".cartas_tablero").children('img').remove();
                            $(".carta_centro_oculta").remove();
                            $(".carta_centro_visible").remove();
                            socket.emit("finalizarArrastre",{sala:<?php echo $_SESSION['id_sala']; ?>,victoria:false});
                        }
                        else if(puntosRival >= 101)
                        {
                            //alert("Ha ganado tu rival");
                            bootbox.dialog({
                                title: 'Tu rival ha ganado',
                                message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                            });
                            $(".cartas_mano").children('img').remove();
                            $(".cartas_tablero").children('img').remove();
                            $(".carta_centro_oculta").remove();
                            $(".carta_centro_visible").remove();
                            socket.emit("finalizarArrastre",{sala:<?php echo $_SESSION['id_sala']; ?>,victoria:true});
                        }
                        else
                        {
                            socket.emit("limpiarTablero",{sala:<?php echo $_SESSION['id_sala']; ?>});
                        }
                    }
                    else
                    {
                        socket.emit("limpiarTablero",{sala:<?php echo $_SESSION['id_sala']; ?>});
                    }
                }
                else
                {
                    turno = false;
                    $(".cartas_mano").children('img').removeAttr('draggable');
                    $(".cartas_mano").children('img').removeAttr('ondragstart');
                    mostrarTxtTurno(turno);
                }
            });

            socket.on('mostrarResultadosPartida',function(data){
                if(puntosJugador < 101 && puntosRival < 101)
                {
                    bootbox.dialog({
                        title: 'Ningun jugador ha ganado. Generando nueva partida...',
                        message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                    });
                    //alert("Ningun jugador ha ganado. Generando nueva partida...");
                    cartas = new Array();
                    todasCartasRepartidas = false;
                    triunfo = 99;
                    arrastre = true;
                    for(i = 0; i <= 39 ; i++)
                    {
                        cartas[i]=i;
                    }
                    if(turno)
                    {
                        repartirCartas(13);
                    }
                }
                else if(puntosJugador >= 101)
                {
                    bootbox.dialog({
                        title: 'Enhorabuena! Has Ganado',
                        message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                    });
                    //alert("Has ganado");
                }
                else if(puntosRival >= 101)
                {
                    bootbox.dialog({
                        title: 'Tu rival ha ganado',
                        message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                    });
                    //alert("Ha ganado tu rival");
                }
            });

            socket.on('vaciarArray',function(data)
            {
                console.log("vaciarArray");
                todasCartasRepartidas = true;
            });

            socket.on('cambiarSiete',function(data)
            {
                $("#triunfo").remove();
                $("#divMonton").append("<img id='triunfo' src='/img/baraja/"+data+".png' class='img-responsive carta_centro_visible' alt=''>");
            });

            socket.on('cantar',function(data)
            {
                puntosRival = puntosRival + data.puntos;
                alert(data.txt);
                console.log("puntos Rival "+puntosRival);
            });

            socket.on('finalizarArrastre',function(data)
            {
                $(".cartas_mano").children('img').remove();
                $(".cartas_tablero").children('img').remove();
                $(".carta_centro_oculta").remove();
                $(".carta_centro_visible").remove();
                if(data.victoria)
                {
                    bootbox.dialog({
                        title: 'Enhorabuena! Has Ganado',
                        message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                    });
                }
                else
                {
                    bootbox.dialog({
                        title: 'Tu rival ha ganado',
                        message: '<p>Puntuacion Jugador :'+( puntosJugador-50 > 0 ? (puntosJugador-50)+' Buenas ' : puntosJugador+' Malas' )+'</p>'+'<p>Puntuacion Rival :'+( puntosRival-50 > 0 ? (puntosRival-50)+' Buenas ' : puntosRival+' Malas' )+'</p>'
                    });
                }
            });

            socket.on('sumarDiezUltimas',function(data)
            {
                puntosRival = puntosRival+10;
            });

            $(document).ready(function(){   

                <?php
                    //if(sizeof($_SESSION['cartas']) == 0)
                    //{
                        ?>
                        function comprobarUsuariosListos()
                        {
                            socket.emit("comprobarUsuariosListos",<?php echo $_SESSION['id_sala']; ?>);
                        }

                        comprobarUsuariosListos();

                        intervalComprobarUsuarios = setInterval(comprobarUsuariosListos,5000);

                        socket.on("usuariosListos",function(data){
                            if(data == "true")
                            {
                                console.log("quitar interval");
                                clearInterval(intervalComprobarUsuarios);
                                if(turno)
                                {
                                    repartirCartas(13);
                                    mostrarTxtTurno(turno);
                                    socket.emit("mostrarTxtTurno",{sala:<?php echo $_SESSION['id_sala']; ?>,turno:false});
                                }
                            }
                        });
                        <?php
                    //}
                ?>

                $("#cambiarSiete").on("click","#botonCambiarSiete",function(){
                    $("#botonCambiarSiete").remove();
                    try
                    {
                        triunfo = numeroCarta($("#triunfo").attr('src'));
                    }
                    catch(err)
                    {}
                    paloTriunfo = (triunfo.length > 1) ? (triunfo.substring(0,1)*10) : 0;
                    for(i=1;i<=6;i++)
                    {
                        numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                        paloCarta = (numCarta.length) > 1 ? (numCarta.substring(0,1)*10) : 0;
                        if(parseInt(paloTriunfo) == parseInt(paloCarta))
                        {
                            switch(parseInt(paloTriunfo))
                            {
                                case 0:
                                    if(numCarta == 6)
                                    {
                                        $("#mano"+i).children('img').remove();
                                        $("#mano"+i).append("<img id='carta"+triunfo+"' src='/img/baraja/"+triunfo+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                        $("#triunfo").remove();
                                        $("#divMonton").append("<img id='triunfo' src='/img/baraja/6.png' class='img-responsive carta_centro_visible' alt=''>");
                                        socket.emit("cambioSiete",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:6});
                                    }
                                    break;
                                case 10:
                                    if(numCarta == 16)
                                    {
                                        $("#mano"+i).children('img').remove();
                                        $("#mano"+i).append("<img id='carta"+triunfo+"' src='/img/baraja/"+triunfo+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                        $("#triunfo").remove();
                                        $("#divMonton").append("<img id='triunfo' src='/img/baraja/16.png' class='img-responsive carta_centro_visible' alt=''>");
                                        socket.emit("cambioSiete",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:16});
                                    }
                                    break;
                                case 20:
                                    if(numCarta == 26)
                                    {
                                        $("#mano"+i).children('img').remove();
                                        $("#mano"+i).append("<img id='carta"+triunfo+"' src='/img/baraja/"+triunfo+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                        $("#triunfo").remove();
                                        $("#divMonton").append("<img id='triunfo' src='/img/baraja/26.png' class='img-responsive carta_centro_visible' alt=''>");
                                        socket.emit("cambioSiete",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:26});
                                    }
                                    break;
                                case 30:
                                    if(numCarta == 36)
                                    {
                                        $("#mano"+i).children('img').remove();
                                        $("#mano"+i).append("<img id='carta"+triunfo+"' src='/img/baraja/"+triunfo+".png' class='img-responsive carta_mano' alt='' draggable='true' ondragstart='drag(event)'>");
                                        $("#triunfo").remove();
                                        $("#divMonton").append("<img id='triunfo' src='/img/baraja/36.png' class='img-responsive carta_centro_visible' alt=''>");
                                        socket.emit("cambioSiete",{sala:<?php echo $_SESSION['id_sala']; ?>,carta:36});
                                    }
                                    break;
                            }
                        }
                    }
                    alert("cambiar 7");
                });

                $("#cantar").on("click","#botonCantar",function(){
                    $("#botonCantar").remove();
                    cartasMano = new Array();
                    cartasOros = new Array();
                    cartasCopas = new Array();
                    cartasEspadas = new Array();
                    cartasBastos = new Array();
                    try{
                        triunfo = numeroCarta($("#triunfo").attr('src'));
                    }
                    catch(err)
                    {}
                    paloTriunfo = (triunfo.length > 1) ? (triunfo.substring(0,1)*10) : 0;
                    for(i=1;i<=6;i++)
                    {
                        if(!($("#mano"+i).children('img').hasClass('cantada')))
                        {
                            numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                            cartasMano.push(numCarta);
                            if(numCarta.length == 1)
                            {
                                cartasOros.push(numCarta);
                            }
                            else
                            {
                                switch(parseInt(numCarta.substring(0,1)))
                                {
                                    case 1:
                                        cartasCopas.push(numCarta);
                                        break;
                                    case 2:
                                        cartasEspadas.push(numCarta);
                                        break;
                                    case 3:
                                        cartasBastos.push(numCarta);
                                        break;                    
                                }
                            }
                        }
                    }
                    if(cartasOros.length > 1)
                    {
                        sota = 0;
                        rey = 0;
                        for(i = 0;i<cartasOros.length;i++)
                        {
                            switch(parseInt(cartasOros[i]))
                            {
                                case 7:
                                    sota = cartasOros[i];
                                    break;
                                case 9:
                                    rey = cartasOros[i];
                                    break;                  
                            }
                        }
                        if(sota != 0 && rey != 0)
                        {
                            for(i=1;i<=6;i++)
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                switch(parseInt(numCarta))
                                {
                                    case 7:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                    case 9:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                }
                            }
                            if(parseInt(paloTriunfo) == 0)
                            {
                                puntosJugador = puntosJugador + 40;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:40,txt:"El rival ha cantado las 40"});
                            }
                            else
                            {
                                puntosJugador = puntosJugador + 20;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:20,txt:"El rival ha cantado 20 en oros"});
                            }
                        }
                    }
                    if(cartasCopas.length > 1)
                    {
                        sota = 0;
                        rey = 0;
                        for(i = 0;i<cartasCopas.length;i++)
                        {
                            switch(parseInt(cartasCopas[i]))
                            {
                                case 17:
                                    sota = cartasCopas[i];
                                    break;
                                case 19:
                                    rey = cartasCopas[i];
                                    break;                  
                            }
                        }
                        if(sota != 0 && rey != 0)
                        {
                            for(i=1;i<=6;i++)
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                switch(parseInt(numCarta))
                                {
                                    case 17:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                    case 19:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                }
                            }
                            if(parseInt(paloTriunfo) == 10)
                            {
                                puntosJugador = puntosJugador + 40;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:40,txt:"El rival ha cantado las 40"});
                            }
                            else
                            {
                                puntosJugador = puntosJugador + 20;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:20,txt:"El rival ha cantado 20 en copas"});
                            }
                        }
                    }
                    if(cartasEspadas.length > 1)
                    {
                        sota = 0;
                        rey = 0;
                        for(i = 0;i<cartasEspadas.length;i++)
                        {
                            switch(parseInt(cartasEspadas[i]))
                            {
                                case 27:
                                    sota = cartasEspadas[i];
                                    break;
                                case 29:
                                    rey = cartasEspadas[i];
                                    break;                  
                            }
                        }
                        if(sota != 0 && rey != 0)
                        {
                            for(i=1;i<=6;i++)
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                switch(parseInt(numCarta))
                                {
                                    case 27:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                    case 29:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                }
                            }
                            if(parseInt(paloTriunfo) == 20)
                            {
                                puntosJugador = puntosJugador + 40;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:40,txt:"El rival ha cantado las 40"});
                            }
                            else
                            {
                                puntosJugador = puntosJugador + 20;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:20,txt:"El rival ha cantado 20 en espadas"});
                            }
                        }
                    }
                    if(cartasBastos.length > 1)
                    {
                        sota = 0;
                        rey = 0;
                        for(i = 0;i<cartasBastos.length;i++)
                        {
                            switch(parseInt(cartasBastos[i]))
                            {
                                case 37:
                                    sota = cartasBastos[i];
                                    break;
                                case 39:
                                    rey = cartasBastos[i];
                                    break;                  
                            }
                        }
                        if(sota != 0 && rey != 0)
                        {
                            for(i=1;i<=6;i++)
                            {
                                numCarta = numeroCarta($("#mano"+i).children('img').attr('src'));
                                switch(parseInt(numCarta))
                                {
                                    case 37:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                    case 39:
                                        $("#mano"+i).children('img').addClass('cantada');
                                        break;
                                }
                            }
                            if(parseInt(paloTriunfo) == 30)
                            {
                                puntosJugador = puntosJugador + 40;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:40,txt:"El rival ha cantado las 40"});
                            }
                            else
                            {
                                puntosJugador = puntosJugador + 20;
                                socket.emit("cantar",{sala:<?php echo $_SESSION['id_sala']; ?>,puntos:20,txt:"El rival ha cantado 20 en bastos"});
                            }
                        }
                    }
                    console.log(puntosJugador);
                });
            });
        </script>
        <div style="height: 80%;">
            <div class="col-lg-1 col-md-1 col-sm-1">
            </div>
            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 tablero">
                <table style="height: 100%; width: 100%; border: 1px solid;">
                    <tr style="height: 25%; width: 100%; border: 1px solid;">
                        <td id="tablero1" class="cartas_tablero"></td>
                        <td id="tablero2" class="cartas_tablero"></td>
                        <td id="tablero3" class="cartas_tablero"></td>
                        <td id="tablero4" class="cartas_tablero"></td>
                        <td id="tablero5" class="cartas_tablero"></td>
                        <td id="tablero6" class="cartas_tablero"></td>
                    </tr>
                    <tr style="height: 25%; width: 100%; border: 1px solid;">
                        <td id="montonCartas" class="cartas_tablero" rowspan="2" colspan="2"></td>
                        <td id="jugadaRival" class="cartas_tablero" colspan="2"></td>
                        <td class="cartas_tablero"></td>
                        <td class="cartas_tablero"></td>
                    </tr>
                    <tr style="height: 25%; width: 100%; border: 1px solid;">
                        <td id="miJugada" class="cartas_tablero" colspan="2" ondrop="drop(event)" ondragover="allowDrop(event)"></td>
                        <td class="cartas_tablero"></td>
                        <td class="cartas_tablero"></td>
                    </tr>
                    <tr style="height: 25%; width: 100%; border: 1px solid;">
                        <td id="mano1" class="cartas_tablero cartas_mano">
                        </td>
                        <td id="mano2" class="cartas_tablero cartas_mano">
                        </td>
                        <td id="mano3" class="cartas_tablero cartas_mano">
                        </td>
                        <td id="mano4" class="cartas_tablero cartas_mano">
                        </td>
                        <td id="mano5" class="cartas_tablero cartas_mano">
                        </td>
                        <td id="mano6" class="cartas_tablero cartas_mano">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-3 col-md-5 col-sm-5 col-xs-12" style="height: 100%">
                <div class="panel-pimary acciones-partida">
                    <div id="texto_turno" class="col-lg-12 col-md-12 col-sm-12 col-xs-12"></div>
                    <div id="cantar" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 " style="margin-top:3%;"></div>
                    <div id="cambiarSiete" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 " style="margin-top:3%;"></div>
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