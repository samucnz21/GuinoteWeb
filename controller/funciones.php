<?php
    require_once('../db/DataBase.php');
    session_start();
    
    function obtener_nivel()
    {
        $conexion = new DataBase();
        $aUSUARIO = $conexion ->obtenerDatosUsuario($_SESSION['id_user']);
        $nivel = $conexion->obtenerNivel($aUSUARIO['exp']);
        echo $nivel['id_nivel'];
    }
    
    function progreso_nivel()
    {
        $conexion = new DataBase();
        $aUSUARIO = $conexion ->obtenerDatosUsuario($_SESSION['id_user']);
        $nivel = $conexion->obtenerNivel($aUSUARIO['exp']);
        $progreso = ($aUSUARIO['exp']*100)/($nivel['exp_max']);
        echo $progreso;
    }
    
    function siguiente_nivel()
    {
        $conexion = new DataBase();
        $aUSUARIO = $conexion ->obtenerDatosUsuario($_SESSION['id_user']);
        $nivel = $conexion->obtenerNivel($aUSUARIO['exp']);
        echo $aUSUARIO['exp']." / ".$nivel['exp_max'];
    }

    function listar_usuarios( $id_user )
    {
        $conexion = new DataBase();
        $aUSUARIOS = $conexion ->obtenerUsuariosConectados( $id_user );
        ?>
        <tr>
            <th>ESTADO</th>
            <th>USUARIO</th>
        </tr>
        <?php
        foreach($aUSUARIOS as $usuario)
        {
            ?>
                <tr class="listaConectados" data-id="<?php echo $usuario['id_usuario']; ?>">
                    <td><i class="fa fa-circle" aria-didden="true" style="color:green"></i></td>
                    <td><?php echo $usuario['usuario']; ?></td>
                </tr>
            <?php
        }
    }

    function contar_conectados()
    {
        $conexion = new DataBase();
        $aUSUARIOS = $conexion ->obtenerUsuariosConectados();
        echo sizeof($aUSUARIOS);
    }
?>
