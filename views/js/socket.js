//establecemos el socket
//var socket = io('http://' + document.domain + ':2020');
var socket = io('http://' + document.domain + ':8080');

//cuando el documento esté listo
$(document).ready(function () 
{
    //al hacer submit del formulario de login
    $("form").on("submit", function (e) {
        e.preventDefault();
        var username = $(".username").val();
        //emitimos el evento add user pasando el username en forma de argumento
        //este evento podrá ser capturado en el servidor
        socket.emit('add user', username);
        console.log("logged");
    })
    
    //al hacer click en el botón de logout emitimos el evento user logout
    $("#logout").on("click", function () {
        socket.emit('user logout');
        $("#logout").hide();
        $(".msg").attr("disabled", true);
        $("form").show();
    })

    //cuando se pulse la tecla enter en la aplicación emitimos el evento new message
    //si ponemos el código donde escribimos mensajes en un form no es necesario
    $(document).keypress(function(e)
    {
        if(e.which == 13) 
        {
            var message = $(".msg").val();
            if(message.length > 2)
            {
                socket.emit("new message", message);
                $(".msg").val("");
            }
            else
            {
                alert("error");
            }
        }
    })

    //cuando el servidor emita el evento new message lo recibimos y lo mostramos
    socket.on('new message', function(data)
    {
        console.log(data)
        if(data.action == "yo")
            $(".containerMessages").append("<p>" + data.message + "</p>");
        else if(data.action == "chat")
        {
            $(".containerMessages").append("<p>" + data.message + "</p>");
        }
    })

    //cuando el servidor emita el eveno login
    socket.on('login', function (data) {
        connected = true;
        // Display the welcome message
        $(".titleChat").html('Welcome to Socket.IO Chat');
        
        registerUsersSidebar(data.usernames)

        $("#logout").show();
        $(".msg").attr("disabled", false);
        $("form").hide();
        $(".username").val('');
    });

    //cuando el servidor emita el evento user joined
    socket.on('user joined', function (data) {
        registerUsersSidebar(data.usernames)
    });

    //cuando el servidor emita el evento user left
    socket.on('user left', function (data) {
        registerUsersSidebar(data.usernames)

        //si no eres tú
        if(typeof data.username != 'undefined')
            $(".containerMessages").append("<p>El usuario " + data.username + " se ha desconectado</p>");
    })
})

//función ayudante para mostrar los usuarios en el sidebar
function registerUsersSidebar(usernames)
{
    var users = [];
    $.each(usernames, function (i, item) {
        users.push('<li>' + item + '</li>');
    });
    $('#users').html('').append(users.join(''));
}