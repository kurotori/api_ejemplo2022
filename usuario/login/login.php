<?php
    include_once "../../funciones.php";
    include_once "../../basededatos.php";
    include_once "../../clases.php";

    $solicitud = file_get_contents('php://input');
    $datos_solicitud = json_decode($solicitud);
    
    /**
     * Detalle de datos de una solicitud de inicio de sesión:
     * Fase 1: Confirmación
     *      Datos que ingresan: CI usuario - modo: login_1
     *      Servidor: Búsqueda del usuario y sus datos
     *      Datos que se envían: Confirmación del usuario - Clave pública/hash_1 (Si el usuario existe)//Error de usuario
     * Fase 2: Credenciales
     *      Datos que ingresan: CI usuario - modo: login_2 - hash de contraseña
     *      Servidor: Recreación de la clave privada con los datos recibidos y comparación
     *      Datos que se envían: Confirmación de login - Datos de la sesión (Si las credenciales son correctas)//Error de credenciales
     *      
     */


    $respuesta = new Respuesta();

    //Creamos un objeto para guardar los datos del usuario
    $usuario = new Usuario();

    //Cargamos los datos del usuario en el objeto
    $usuario->ci = $datos_solicitud->ci;
    $usuario->hash = $datos_solicitud->hash;
    $usuario->modo = $datos_solicitud->modo;

    // Ejecución //

    //1 - Busco el usuario en el sistema
    if ( usuarioExiste($usuario->ci) ) {
        //2 - Si el usuario existe, analizamos la presencia de un hash
        //      NOTA: el chequeo sobre si la contraseña se ingresó o no debe realizarse del lado del cliente 
        
        if ( empty($usuario->hash) ) {
            //Si el hash es vacío entonces buscamos la clave pública del usuario
            $respuesta->estado = "OK";
            $claves = buscarClavesDeUsuario($usuario->ci);
            $respuesta->datos = $claves->clavePublica;
            $respuesta->mensaje = "Clave pública";
        }
        else{
            //Si el hash no es vacío, entonces buscamos los datos y regeneramos la clave privada
            $claves = buscarClavesDeUsuario($usuario->ci);
           
            $clavePrivada = $claves->clavePrivada;
            $sal = $claves->sal;
            $marcaTiempo = $claves->marcaTiempo;

            $claveRegenerada = hash_hmac("sha256", $marcaTiempo.$usuario->hash, $sal);

            if( strstr($clavePrivada, $claveRegenerada) ){
                //Si las claves coinciden, se genera una sesión nueva
                $respuesta->estado = "OK";
                $respuesta->mensaje = "Contraseña correcta";
            }
            else{
                //Si las claves no coinciden, se genera un error
                $respuesta->estado = "ERROR";
                $respuesta->mensaje = "Usuario o contraseña erróneos";
                $respuesta->datos = "";
            }
            
            
        }
        
    } else {
        $respuesta->estado = "ERROR";
        $respuesta->mensaje = "Usuario o contraseña erróneos";
        $respuesta->datos = "";
    }
    


    $json = TransformarEnJSON($respuesta);
    MostrarJSON($json); 

    // --------- //

    /**
     * Busca cuantos usuarios registrados bajo una determinada CI existen en el sistema.
     *
     * @param [type] $ci
     * @return void
     */
    function usuarioExiste($ci){
        $resultado = "";
        $bdd = CrearConexion();
        
        if ( strstr($bdd->estado,"OK") ) {
            
            $conexion = $bdd->conexion;
            //Hacemos un conteo sobre el hash_2, ya que eso nos indicará un usuario con registro completo 
            $consulta = "SELECT count(hash_2) as 'conteo' from usuario where ci = ?";

            $termino = $ci;
            $sentencia = $conexion->prepare($consulta);
            $sentencia->bind_param("i",$termino);
            $sentencia->execute();
            $resultado = $sentencia->get_result();

            if ( $resultado->num_rows > 0 ) {
                foreach($resultado as $fila){
                    $conteo = $fila['conteo'];
                    if ($conteo == 0) {       
                        $resultado = false;
                    }
                    else {
                        $resultado = true;
                    }
                }   
            }
        }
        return $resultado;
    }


    /***
     * 
     */
    function buscarClavesDeUsuario($ci){
        $datos = new ClavesUsuario();
        $bdd = CrearConexion();
        
        if ( strstr($bdd->estado,"OK") ) {
            
            $conexion = $bdd->conexion;
            
            $consulta = "SELECT sal as 'sal', hash_1 as 'clavePub', marcaTiempo as 'marcaTiempo'
                        hash_2 as 'clavePr' from usuario where ci = ?";

            $termino = $ci;
            $sentencia = $conexion->prepare($consulta);
            $sentencia->bind_param("i",$termino);
            $sentencia->execute();
            $resultado = $sentencia->get_result();

            if ( $resultado->num_rows > 0 ) {
                foreach($resultado as $fila){
                    $datos->marcaTiempo = $fila['marcaTiempo'];
                    $datos->clavePublica = $fila['clavePub'];
                    $datos->clavePrivada = $fila['clavePr'];
                    $datos->sal = $fila['sal'];
                }   
            }
        }
        return $clavesUsuario;
    }

?>