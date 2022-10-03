<?php
    session_start();
    
    $_SESSION["nombre"]="Sebastián";
//html:5  a:link
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

    <form action="ver_datos.php" method="get">
        <label for="dato">Dato:</label>
        <input type="text" name="dato" id="dato">
        <br>
        <input type="submit" value="Enviar Dato">
    </form>


    <a href="ver_datos.php">Ver los datos de la cookie</a>
</body>
</html>