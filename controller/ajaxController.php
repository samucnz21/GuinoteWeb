<?php

require_once('../db/DataBase.php');
require_once 'funciones.php';
session_start();
$conexion = new DataBase();
$accion = $_POST['accion'];
$resultado = array();
switch ($accion)
{
    case "login":
    {
        if((isset($_POST['user']) && !empty($_POST['user'])) && (isset($_POST['passwd']) && !empty($_POST['passwd'])))
        {
            $aUSUARIO = $conexion ->validarUsuario($_POST['user'],$_POST['passwd']);
            if(!empty($aUSUARIO))
            {
                $resultado['id_user']=$aUSUARIO['id_usuario'];
                $_SESSION['id_user']=$aUSUARIO['id_usuario'];
                $_SESSION['usuario']=$aUSUARIO['usuario'];
                $_SESSION['nombre']=$aUSUARIO['nombre']." ".$aUSUARIO['apellidos'];
                $conexion -> actualizarEstado($aUSUARIO['id_usuario'],"conectado");
                $resultado['exito']=true;
            }
            else 
            {
                $resultado['exito']=false;
            }
        }
        else
        {
            $resultado['exito']=false;
        }
        echo(json_encode($resultado));
        break;
    }
    
    case "cambiarEstado":
    {
        $user = $_POST['usuario'];
        $estado = $_POST['estado'];
        $conexion->actualizarEstado($user, $estado);
        break;
    }

    case "crearSala":
    {

    }
}