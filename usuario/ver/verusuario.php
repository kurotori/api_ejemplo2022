<?php
    include_once("../../clases.php");
    include_once("../../funciones.php");
    include_once("../../basededatos.php");

    /**
     * EJECUCIÓN
     */

    //1 - Recepción de los datos de la solicitud
    $datos = file_get_contents('php://input');

    //2 - Decodificación de los datos recibidos de JSON a un objeto
    $datos_usuario = json_decode($datos);

    //3 - Extracción del nombre a buscar.
    $nombre = $datos_usuario->nombre;
    
    //Pausa para forzar un retraso en la respuesta. DEBE ELIMINARSE
    sleep(1);

    //4 - Se crea un objeto para almacenar la respuesta y se ejecuta la función de búsqueda
    $respuesta = buscarUsuario($nombre);

    //5 - Transformamos el objeto con la respuesta en JSON
    $json = TransformarEnJSON($respuesta);

    //6 - Entregamos la respuesta
    MostrarJSON($json); 
    

    /**
     * FUNCIONES
     */

    /**
     * Permite obtener los datos de usuarios buscándolos por su nombre
     * @param string $nombre El nombre, o parte del nombre, del usuario a buscar
     * @return $respuesta Un listado con los datos de los usuarios encontrados o un mensaje de error
     */
    function buscarUsuario($nombre){

        //1 - Declaramos un objeto con la conexión a la base de datos
        $bdd = CrearConexion();

        //2 - Declaramos un objeto para la respuesta
        $respuesta = new Respuesta();

        //3 - Chequeamos que la conexión a la base de datos fue exitosa
        if ( strstr($bdd->estado,"OK") ) {

            //3.1 - Si la conexión fue exitosa, declaramos y obtenemos el objeto conexion
            $conexion = $bdd->conexion;

            //4 - Declaramos la consulta a ejecutar en la base de datos, 
            // con un parámetro para agregar el nombre a buscar
            $consulta = 
            "SELECT nombre,ci,fecha_nac,email from usuario where nombre like ?";
            
            //5 - Declaramos una variable y le asignamos el nombre a buscar con 
            // modificadores para agregarla al parámetro
            $termino = "%".$nombre."%";

            //6 - Declaramos una sentencia y la preparamos, agregando la consulta
            $sentencia = $conexion->prepare($consulta);

            //7 - Agregamos el término al parámetro en la sentencia, y la ejecutamos
            $sentencia->bind_param("s",$termino);
            $sentencia->execute();

            //8 - Declaramos una variable para el resultado y lo obtenemos de la sentencia
            $resultado = $sentencia->get_result();

            //8.1 - Se asume que el estado de la operación es correcta
            $respuesta->estado = "OK";
            $respuesta->mensaje = "OK";

            //9 - Si el resultado contiene al menos una fila de registros...
            if ( $resultado->num_rows > 0 ) {
                
                //9.1 - ...Inicializamos el atributo 'datos' de la respuesta como un array
                $respuesta->datos = array();
                
                //Mediante un 'foreach' recorremos las filas de registros del resultado...
                foreach($resultado as $fila){
                    //...Por cada fila, creamos un nuevo objeto de clase usuario...
                    $usuario = new Usuario();
                    
                    //...y le asignamos los datos contenidos en la fila
                    $usuario->nombre = $fila['nombre'];
                    $usuario->ci = $fila['ci'];
                    $usuario->fecha_nac = $fila['fecha_nac'];
                    $usuario->email = $fila['email'];

                    //Finalmente agregamos el objeto de clase usuario al array de datos
                    array_push($respuesta->datos, $usuario);
                }

            } else {
                //9.2 - Si no hay resultados, agregamos ese mensaje a la respuesta
                $respuesta->mensaje = "No hubieron resultados";
            }

            //10 - Terminada la operación, cerramos las conexiones de la sentencia y la base de datos
            $sentencia->close();
            $conexion->close();
            
        }
        else{
            //3.2 - Si hubo algún error en la conexión a la base de datos, agregamos esos datos 
            // al estado y el mensaje de la respuesta.
            $respuesta->estado = $bdd->estado;
            $respuesta->mensaje = $bdd->mensaje;
        }

        //11 - Finalmente, la función entrega la respuesta obtenida.
        return $respuesta;
    }
?>