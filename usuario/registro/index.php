<?php

    include_once "../../funciones.php";
    include_once "../../basededatos.php";
    include_once "../../clases.php";

    //Obtenemos los datos que recibimos del lado del cliente
    $datos = file_get_contents('php://input');
    $datos_usuario = json_decode($datos);

    //Esta respuesta será la que devolveremos al usuario
    $respuesta = new Respuesta();
    
    //Creamos un objeto para guardar los datos del usuario
    $usuario = new Usuario();

    //Cargamos los datos del usuario en el objeto
    $usuario->nombre = $datos_usuario->nombre;
    $usuario->ci = $datos_usuario->ci;
    $usuario->fecha_nac = $datos_usuario->fecha_nac;
    $usuario->email = $datos_usuario->email;
    $usuario->hash = $datos_usuario->hash;
    
    //Comienzo del registro: se recibe una solicitud SIN hash
    if( empty( $usuario->hash ) ){
        sleep(1);

        //Se chequea si existe el usuario en el sistema
        if ( usuarioExiste($usuario->ci) ) {
            
            $respuesta->estado = "ERROR";
            $respuesta->mensaje = "Este usuario ya está registrado";
        } 
        else {
          //Si el usuario NO EXISTE se procede a generar el registro intermedio  
            $marcaTiempo = time();
            $sal = crearSal();
                //El valor hash_1 es llamado 'clave pública', ya que se envía
                //al lado del cliente.
            $hash_1 = hash_hmac("sha256",$marcaTiempo,$sal);
            $usuario->hash = $hash_1;
            
            //Se realiza el registro intermedio
            $intermedio = registroIntermedio($usuario);

            //Si NO hubo errores en el proceso, se transmite el valor de la clave pública al cliente
            if (strstr($intermedio->estado,"OK")) {
                $respuesta->estado = "OK";
                $respuesta->mensaje = "sal";
                $respuesta->datos = $hash_1;
            }
            else{
                $respuesta->estado = $intermedio->estado;
                $respuesta->mensaje = $intermedio->mensaje;
                $respuesta->datos = $intermedio->datos;
            }          
        }
    }
    else{
        //Si la solicitud CONTIENE un hash, se procede a completar su registro

        //Creamos una marca de tiempo y una sal nueva
        $marcaTiempo = time();
        $sal = crearSal();

        //Creamos el hash_2, la clave privada, con el hasheo provisto desde el cliente
        $hash_2 = hash_hmac("sha256", $marcaTiempo.$usuario->hash, $sal);
        
        //Agregamos los datos al objeto Usuario
        $usuario->sal = $sal;
        $usuario->marcaTiempo = $marcaTiempo;
        $usuario->hash_2 = $hash_2;
        
        //Completamos el registro del usuario
        $registroFinal = registrarUsuario($usuario);

        //Chequeamos la presencia de errores y enviamos el resultado al cliente 
        if ( strstr($registroFinal->estado,"OK") ) {
            $respuesta->estado = "OK";
            $respuesta->mensaje = $registroFinal->mensaje; 
        } else {
            $respuesta->estado = $registroFinal->estado;;
            $respuesta->mensaje = $registroFinal->mensaje;
            $respuesta->datos = $registroFinal->datos;
        }
        

        

    }


$json = TransformarEnJSON($respuesta);
MostrarJSON($json); 


/**
 * Permite generar un registro inicial del usuario y el hash_1, 
 * que será la clave pública
 */
function registroIntermedio(Usuario $usuario){
    
    $resultado = new Respuesta();
    $bdd = CrearConexion();
    
    if ( strstr($bdd->estado,"OK") ) {
        
        $conexion = $bdd->conexion;
        $consulta = "INSERT into usuario(ci, hash_1) values (?,?)";
        $ci = $usuario->ci;
        $hash_1 = $usuario->hash;
        
        $sentencia = $conexion->prepare($consulta);
        $sentencia->bind_param("is",$ci,$hash_1);
        $sentencia->execute();

        if( $sentencia->affected_rows == 1){
            $resultado->estado = "OK";
            $resultado->mensaje = "Registro Intermedio Exitoso";
        }
        else{
            $resultado->estado = "ERROR";
            $resultado->mensaje = "No fue posible realizar el registro";
            $resultado->datos = $sentencia->error;
        }
        $sentencia->close();
        $conexion->close();
    }
    else{
        $resultado->estado = "ERROR";
        $resultado->mensaje = "Problemas de conexión a la base de datos";
    }

    return $resultado;
}

/**
 * Permite completar el registro de un usuario en el sistema
 *
 * @param Usuario $usuario
 * @return void
 */
function registrarUsuario(Usuario $usuario){
        $resultado = new Respuesta(); //Modificado
        $bdd = CrearConexion();
        
        if ( strstr($bdd->estado,"OK") ) {
            
            $conexion = $bdd->conexion;
            $consulta = "UPDATE usuario 
                         SET nombre=?, fecha_nac=?, email=?, hash_2=?,
                         sal=?, marcaTiempo=? WHERE ci=? ";
            $nombre = $usuario->nombre;
            $fecha_nac = $usuario->fecha_nac;
            $email = $usuario->email;
            $hash_2 = $usuario->hash_2;
            $sal = $usuario->sal;
            $marcaTiempo = $usuario->marcaTiempo;
            $ci = $usuario->ci;

            $sentencia = $conexion->prepare($consulta);
            $sentencia->bind_param("sssssii",$nombre,$fecha_nac,$email,
                                    $hash_2,$sal,$marcaTiempo,$ci);
            $sentencia->execute();

            if( $sentencia->affected_rows == 1){
                $resultado->estado = "OK";
                $resultado->mensaje = "Registro Exitoso";
            }
            else{
                $resultado->estado = "ERROR";
                $resultado->mensaje = "No fue posible realizar el registro def.";
                $resultado->datos = $sentencia->error;
            }
            $sentencia->close();
        }
        else{
            $resultado = "Problemas de conexión a la base de datos";
        }

        return $resultado;
    }
    

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

    /**
     * Permite crear una sal para usarse en la creación de claves públicas y privadas
     *
     * @return sal secuencia alfanumérica
     */
    function crearSal(){
        $sal = "";
        $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!#$%&()";
        for ($num=0; $num < 100; $num++) { 
            $numero = random_int(0, strlen($alfabeto)-1 );
            $letra = $alfabeto[$numero];
            $sal = $sal.$letra;
        }
        return $sal;
    }

?>