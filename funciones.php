<?php

    function ValidarDatos($datos){
        $datos = trim($datos);
        $datos = stripslashes($datos);
        $datos = htmlspecialchars($datos);
        return $datos;
    }

    function TransformarEnJSON($objeto){
        $jsonDatos = new stdClass(); //
        $nombreClase = get_class($objeto);//
        $jsonDatos = array("$nombreClase"=>$objeto);//
        $jsonDatos = json_encode($jsonDatos);
        return $jsonDatos;
    }

    function MostrarJSON($datos){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(200);
        echo($datos);   
    }

    function generarSal(){
        $alfabeto = "ABCDEFGHIJKLMNÑOPQRSTUVWYZabcdefghijklmnñopqrstuvwxyz1234567890|°¬!#$%&/()=?¡{}[].,;:-_";
        $sal="";
        for ($num=0; $num < 101; $num++) {
            $posicion = random_int(0,strlen($alfabeto));
            $sal = $sal.$alfabeto[$posicion];
        }
        return $sal;
    }

?>