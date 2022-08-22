<?php
include_once("../../clases.php");
include_once("../../funciones.php");
include_once("../../basededatos.php");


$datos = file_get_contents('php://input');
$datos_usuario = json_decode($datos);

$usuario = new Usuario();
$usuario->nombre = ValidarDatos($datos_usuario->nombre);
$usuario->ci = $datos_usuario->ci;
$usuario->fecha_nac = $datos_usuario->fecha_nac;
$usuario->email = $datos_usuario->email;

$respuesta = guardarUsuario($usuario);

$json = TransformarEnJSON($respuesta);
MostrarJSON($json); 

function guardarUsuario($usuario){
    $bdd = CrearConexion();
    $respuesta = new Respuesta();

    if ( strstr($bdd->estado,"OK") ) {
        $conexion = $bdd->conexion;

        $consulta = 
        "INSERT into usuario(nombre,ci,fecha_nac,email) 
        values(?,?,?,?)";
        
        $sentencia = $conexion->prepare($consulta);
        $sentencia->bind_param("siss",
            $usuario->nombre,
            $usuario->ci,
            $usuario->fecha_nac,
            $usuario->email
        );
        

        if ($sentencia->execute()) {
            $respuesta->estado = "OK";
            $respuesta->mensaje = "Se guardaron los datos correctamente";
        }
        else{
            $respuesta->estado = "ERROR";
            $respuesta->mensaje = $sentencia->error;
        }

        $sentencia->close();
        $conexion->close();
        
    }
    else{
        $respuesta->estado = $bdd->estado;
        $respuesta->mensaje = $bdd->mensaje;
    }
    return $respuesta;
}


?>