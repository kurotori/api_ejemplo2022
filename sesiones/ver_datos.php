<?php
    session_start();
    if ( isset($_GET) && !empty($_GET) ) {
        $tiempo = time();

        $_SESSION["C".$tiempo] = $_GET["dato"];
    } else {
        echo("No se recibieron datos <br>");
    }

    foreach ($_SESSION as $clave => $valor) {
        echo("$clave : $valor");
        echo("<br>");
    }

?>