<?php
    require_once('../db/DataBase.php');
    require_once('./redirectController.php');
    session_start();
    $conexion = new DataBase();

    if((isset($_POST['user']) && !empty($_POST['user'])) && (isset($_POST['passwd']) && !empty($_POST['passwd'])) && (isset($_POST['nombre']) && !empty($_POST['nombre'])) && (isset($_POST['apellidos']) && !empty($_POST['apellidos'])) && (isset($_POST['email']) && !empty($_POST['email'])))
    {
        if($conexion -> registrarUsuario($_POST['user'],$_POST['passwd'],$_POST['nombre'],$_POST['apellidos'],$_POST['email']))
        {
            header("Location: /exito-registro");
        }
        else
        {
            header("Location: /error-registro");
        }
    }
    else
    {
        header("Location: /error-registro");
    }
    
?>

