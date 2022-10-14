const cookies = procesarCookies(document.cookie);
//const datosCookies = cookies.split(";");

let clavePublica = "";

console.log(cookies);

let formulario = document.getElementById('iniciarSesion');

 /**
     * Detalle de datos de una solicitud de inicio de sesión:
     * Fase 1: Confirmación
     *      Datos que se envían: CI usuario - modo: login_1
     *      Servidor: Búsqueda del usuario y sus datos
     *      Datos que se reciben: Confirmación del usuario - Clave pública/hash_1 (Si el usuario existe)//Error de usuario
     * Fase 2: Credenciales
     *      Datos que ingresan: CI usuario - modo: login_2 - hash de contraseña
     *      Servidor: Recreación de la clave privada con los datos recibidos y comparación
     *      Datos que se envían: Confirmación de login - Datos de la sesión (Si las credenciales son correctas)//Error de credenciales
     *      
     */


async function iniciarSesion() {
    let modo = "";
    try {
        
        //Creamos un nuevo objeto con los datos del formulario
        const formDatos = new FormData(formulario);

        if (clavePublica.length < 1) {
            modo = "login_1";
        }

        

    } catch (error) {
        console.error(error);
    }
}


function chequearCookies() {
    let resultado = "";
    if ("sesion" in cookies) {
        resultado = "Se encontraron cookies";
    } else {
        resultado = "No se encontraron cookies";
    }
    console.log(resultado);
}

function procesarCookies(cookies) {
    const datos = {};
    let datosCookies = cookies.split(";");
    datosCookies.forEach(dato => {
        let info = dato.split("=");
        datos[info[0]] = info[1];
    });

    return datos;
}