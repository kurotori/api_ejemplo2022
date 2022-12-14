
//Obtenemos el formulario donde están los datos
let formulario = document.getElementById('formulario');
//Creamos una variable para contener la sal
let sal = "";


/**
* Función ASÍNCRONA que toma los datos del formulario y los envía al servidor
*/
async function enviarFormulario() {
    let modo="";

    //Usamos una estructura 'try' para manejar posibles errores de comunicación con el servidor
    try {
        //Creamos un nuevo objeto con los datos del formulario
        const formDatos = new FormData(formulario);
        
        //Auxiliar
        console.log(sal.length);
        
        //Si la sal es de longitud 0, establecemos el modo en "soloci", que solo envía la CI del usuario
        if (sal.length<1) {
            modo = "soloci";
        } 
        
        //Auxiliar
        console.log(modo);

        const respuesta = await prepararDatos(formDatos,modo);
        
        //Auxiliares
        console.log("llegó");
        console.log(respuesta);

        //Si la respuesta contiene una sal, hasheamos la contraseña
        //y enviamos todos los datos
        if (respuesta.Respuesta.mensaje=="sal") {
            //Auxiliar
            console.log("hay sal");
            
            //Obtenemos la sal (clave pública) de los datos recibidos
            const sal = respuesta.Respuesta.datos;
            
            //Auxiliar
            console.log("Sal:"+sal);

            //Encriptamos la contraseña con la clave pública y re-enviamos
            const respuesta2 = await prepararDatos(formDatos,"");
            
            //Evaluamos si el servidor nos devuelve un error
            if (respuesta2.Respuesta.estado == "ERROR") {
                alert(respuesta2.Respuesta.estado);
                alert(respuesta2.Respuesta.mensaje);
            }
            else{
                alert("Registro exitoso");
            }
            
        } else {
            console.log(respuesta.Respuesta.estado);
            console.log(respuesta.Respuesta.mensaje);
        }

        if (respuesta.Respuesta.estado == "ERROR") {
            alert(respuesta.Respuesta.estado);
            alert(respuesta.Respuesta.mensaje);
        }

    } catch (error) {
        console.error(error);
    } 
}


/**
 * Prepara los datos a ser enviados al servidor de acuerdo al modo de envío.
 * */
async function prepararDatos(datos, modo) {

    const datosEnBruto = Object.fromEntries(datos.entries());
    //Auxiliar
    console.log(datosEnBruto);
    
    
    //Extraigo la contraseña en una variable auxiliar y la quito de los datos a enviarse
    const contrasenia = datosEnBruto.contrasenia;
    datosEnBruto.contrasenia = "";
    
    if (modo=="soloci") {
       //Si el modo es "soloci", se borra la información de todos los otros datos del usuario,
       // dejando solamente el dato "CI"
        datosEnBruto.nombre = "";
        datosEnBruto.fecha_nac = "";
        datosEnBruto.email = "";
        
        datosEnBruto.hash = "";
    }
    else{
        //Si el modo no es "soloci", se mantiene la información de todos los datos, 
        //y generamos el hasheo de la contraseña, y agregamos el hash_2 a los datos
        console.log("hasheando contraseña");
        const hash_2 = await sha256(sal+contrasenia);
        
        console.log("hash_2");
        console.log(hash_2);
        
        datosEnBruto.hash = hash_2;
    }
    
    
    //Auxiliar
    //datosEnBruto.nombre = "modificado";

    //Pasamos los datos en bruto del formulario a formato JSON
    let datosJSON = JSON.stringify(datosEnBruto);
    

    //Auxiliares
    console.log("datos");
    console.log(datosJSON);
    
    //Enviamos los datos al servidor y esperamos una respuesta.
    const respuesta = await fetch("index.php",
        {
            method:"POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: datosJSON
        }
    );
    
    //Analizamos la respuesta buscando un posible error
    if ( ! respuesta.ok) {
        const errorMessage = await response.text();
        throw new Error(errorMessage);

    }

    //Entregamos el contenido de la respuesta del servidor
    return respuesta.json();
}


/**
 * Función asíncrona que permite encriptar un dato con el algoritmo sha256
 */
 async function sha256(dato) {
    // Pasamos el dato proporcionado a codificación UTF-8
    const msgBuffer = new TextEncoder().encode(dato);                    

    // Generamos un hash con el dato codificado
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);

    // Pasamos el dato del formato 'ArrayBuffer' al formato 'Array'
    const hashArray = Array.from(new Uint8Array(hashBuffer));

    // Pasamos los datos a un formato hexadecimal                  
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    
    //Entregamos la secuencia obtenida.
    return hashHex;
}
