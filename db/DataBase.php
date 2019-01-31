<?php
/**
 * Description of DataBase
 *
 * @author SAMUEL
 */
class DataBase {
    
    //** ATRIBUTOS **//
    private $host = "localhost";
    private $user = "samuel";
    private $pass = "Samuel_1";
    private $bd = "guinote";
    private $port = "3306";
    public $conexion;
    
    //** METODOS **//
    
    public function __construct() {
        $this -> conexion = mysqli_connect($this->host, $this->user, $this->pass, $this->bd, $this->port) or die('No se pudo conectar: ' . mysql_error());
        return $this->conexion;
    }
    
    public function validarUsuario($user,$passwd){
        $sqlSELECT = "SELECT * FROM usuario WHERE usuario='".mysqli_escape_string($this->conexion, $user)."' AND passwd=md5('".mysqli_escape_string($this->conexion, $passwd)."');";
        $qrySELECT = mysqli_query($this->conexion, $sqlSELECT);
        $resSELECT = mysqli_fetch_assoc($qrySELECT);
        if($resSELECT)
        {
            return $resSELECT;
        }
        else 
        {
            return false;    
        }
    }

    public function actualizarEstado($user,$estado){
        $sqlUPDATE = "UPDATE usuario SET estado='".mysqli_escape_string($this->conexion, $estado)."' WHERE id_usuario=".$user;
        $qryUPDATE = mysqli_query($this->conexion, $sqlUPDATE);
    }

    public function obtenerDatosUsuario($id_usuario){
        $sqlSELECT = "SELECT * FROM usuario WHERE id_usuario='".mysqli_escape_string($this->conexion, $id_usuario)."';";
        $qrySELECT = mysqli_query($this->conexion, $sqlSELECT);
        $resSELECT = mysqli_fetch_assoc($qrySELECT);
        if($resSELECT)
        {
            return $resSELECT;
        }
        else 
        {
            return false;    
        }
    }
    
    public function obtenerNivel($experiencia){
        $sqlSELECT = "SELECT * FROM nivel WHERE exp_min<='".mysqli_escape_string($this->conexion, $experiencia)."' AND exp_max>='".mysqli_escape_string($this->conexion, $experiencia)."';";
        $qrySELECT = mysqli_query($this->conexion, $sqlSELECT);
        $resSELECT = mysqli_fetch_assoc($qrySELECT);
        if($resSELECT)
        {
            return $resSELECT;
        }
        else 
        {
            return false;    
        }
    }

    public function obtenerUsuariosConectados($id_user){
        $sqlSELECT = "SELECT * FROM usuario WHERE estado='".mysqli_escape_string($this->conexion, "conectado")."' AND id_usuario != ".$id_user.";";
        $qrySELECT = mysqli_query($this->conexion, $sqlSELECT);
        if($qrySELECT)
        {
            while($resSELECT = mysqli_fetch_assoc($qrySELECT))
            {
                $aUSUARIOS[] = $resSELECT;
            }
            return $aUSUARIOS;
        }
        else 
        {
            return false;    
        }
    }
    
}
