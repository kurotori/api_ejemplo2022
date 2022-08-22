<?php

    echo( crearSal() );

    function crearSal(){
        $sal = "";
        $alfabeto = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!#$%&()";
        for ($num=0; $num < 100; $num++) { 
            $numero = random_int(0, strlen($alfabeto)-1 );
            $letra = $alfabeto[$numero];
            $sal = $sal.$letra;
        }
        return $sal;
    }

?>