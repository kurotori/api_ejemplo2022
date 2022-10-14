<?php

    class Respuesta{
        public $estado = ""; 
        public $mensaje = "";
        public $datos = "";
    }

    class Usuario{
        public $nombre = "";
        public $ci = "";
        public $fecha_nac = "";
        public $email = "";
        public $hash = "";
        public $sal = "";
    }

    class Sesion{
        public $id = "";
        public $inicio = "";
        public $estado = "";
    }

    class ClavesUsuario{
        public $sal = "";
        public $marcaTiempo = "";
        public $clavePublica = "";
        public $clavePrivada = "";
    }
?>