<?php
    include_once("../../clases.php");
    include_once("../../funciones.php");
    include_once("../../basededatos.php");


    function esJson($dato){
        json_decode($dato);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    
    function validarPost($dato){
        if ( ! empty($dato) 
            and 
            isset($dato)
        ) 
        {
            //$dato = ValidarDatos($dato);
            if (esJson($dato)) {
                return json_decode($dato);
            }
            else{
                return "ERROR";
            }
        }
    }

    $datos = validarPost(file_get_contents('php://input'));
    
    $usuario=new Usuario();
    if (! strstr($datos,"ERROR")) {
        
        $usuario->nombre = $datos->nombre;
        $usuario->ci = $datos->ci;
        $usuario->fecha_nac = $datos->fecha_nac;
        $usuario->email = $datos->email;
        
    }

        
        
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