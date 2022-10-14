<?php
    include_once("../../clases.php");
    include_once("../../funciones.php");
    include_once("../../basededatos.php");

    $datos = file_get_contents('php://input');
    $datos_usuario = json_decode($datos);

    $nombre = $datos_usuario->nombre;
    
    sleep(1);

    $respuesta = buscarUsuario($nombre);

    $json = TransformarEnJSON($respuesta);
    MostrarJSON($json); 
    

    function buscarUsuario($nombre){
        $bdd = CrearConexion();
        $respuesta = new Respuesta();

        if ( strstr($bdd->estado,"OK") ) {
            $conexion = $bdd->conexion;

            $consulta = 
            "SELECT nombre,ci,fecha_nac,email from usuario where nombre like ?";
            $termino = "%".$nombre."%";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->bind_param("s",$termino);
            $sentencia->execute();

            $resultado = $sentencia->get_result();
            $respuesta->estado = "OK";
            $respuesta->mensaje = "OK";

            if ( $resultado->num_rows > 0 ) {
                $respuesta->datos = array();
                
                foreach($resultado as $fila){
                    $usuario = new Usuario();
                    
                    $usuario->nombre = $fila['nombre'];
                    $usuario->ci = $fila['ci'];
                    $usuario->fecha_nac = $fila['fecha_nac'];
                    $usuario->email = $fila['email'];

                    array_push($respuesta->datos, $usuario);
                }

            } else {
                $respuesta->mensaje = "No hubieron resultados";
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