<?php

    session_start();
    if (isset($_GET) && !empty($_GET)) {
      
        foreach($_GET as $dato => $valor){
            $_SESSION["$dato"] = $_GET["$dato"];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Si hay datos de sesión, aparecerán acá</h2>
    <?php
        if (sizeof($_SESSION) > 0 ) {
            # code...
            foreach($_SESSION as $dato => $valor){
            echo("$dato: $valor");
            echo("<br>");
        }
        } else {
            echo("No hay datos en la sesión<br>");
        }
        
        
    ?>

    <h2>Agregar datos a la sesión</h2>
    <form action="index.php" method="get">
        <label for="dato">Dato: </label>
        <input type="text" id="dato" name="dato">
        <input type="submit" value="Agregar dato a la sesión">
    </form>
</body>
</html>