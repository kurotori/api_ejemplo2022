<?php
    include_once "../../funciones.php";
    include_once "../../basededatos.php";
    include_once "../../clases.php";

    $solicitud = file_get_contents('php://input');
    $datos_solicitud = json_decode($solicitud);
    
    /**
     * Detalle de datos de una solicitud de inicio de sesión:
     * Fase 1: Datos del usuario
     *      Datos que ingresan: CI usuario - modo: login
     *      Datos que se envían: Confirmación del usuario
     * 
     */


    $respuesta = new Respuesta();



    
    
    function horaDelSistema(){
        $respuesta = new Respuesta();
    }


?>