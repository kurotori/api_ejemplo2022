/* Declaración de objetos correspondientes a elementos específicos de
*   la página. 
*/
const div_resultados = document.getElementById("contenido");
const input_nombre_usuario = document.getElementById("nombre_usuario");
const btn_buscar = document.getElementById("boton_buscar"); 

/* EJECUCIÓN
*   1 - Limpiamos la estructura básica presente en la página dejando
*   ese espacio pronto para los resultados
*/  
limpiarResultados();


/**
 * Permite limpiar la sección de resultados
 */
function limpiarResultados() {
    Array.from(
        document.getElementsByClassName("resultado")).forEach(element => {
        element.parentElement.removeChild( element );
    });
}


/**
 * Envía la solicitud, espera el resultado e inicia el proceso del mismo
 */
async function enviarSolicitud() {
    //1 - Limpiamos la sección de resultados...
    limpiarResultados();

    //... y agregamos la clase 'espere' para mostrar la animación de espera
    div_resultados.classList.add("espere");

    //Obtenemos el nombre del usuario que buscamos del input
    let nombre_usuario = input_nombre_usuario.value;

    //2 - Creamos un objeto para contener la respuesta del servidor
    // y le asignamos el resultado de la función de búsqueda
    // El comando 'await' indica que se debe esperar a que se obtenga una respuesta
    const respuesta = await buscarUsuario(nombre_usuario);
    
    //Auxiliar: Control de la respuesta
    console.log(respuesta);

    //3 - Una vez recibida la respueta removemos la animación de espera
    div_resultados.classList.remove("espere");

    //4 - Chequeamos la naturaleza de la respuesta recibida. 
    // En este caso, si el elemento 'respuesta.Respuesta.datos' es un array
    // (usando la función 'Array.isArray') procesamos sus datos
    if ( Array.isArray(respuesta.Respuesta.datos) ) {

        //Procesamos los datos de la respuesta con un 'forEach'...
        respuesta.Respuesta.datos.forEach(dato => {
            //...pasando cada dato de la respuesta por la función de proceso de datos.
            crearResultado(dato);
        });

    } else {
        //Si 'datos' no es un array (indicando que hubo un error o no hay resultados)
        // creamos un objeto, le cargamos el mensaje del servidor y lo entregamos a la
        // función de proceso de datos.
        const dato = {};
        dato.nombre = respuesta.Respuesta.mensaje;
        crearResultado(dato);
    }
    
}


/**
 * Envía una solicitud de búsqueda de datos de usuario al servidor
 * @param {string} nombre_usuario El nombre del usuario que buscamos 
 * @returns un objeto con la respuesta del servidor 
 */
async function buscarUsuario(nombre_usuario) {
    
    //1 - Pasamos el nombre del usuario a un objeto con estructura JSON
    let datos = JSON.stringify({nombre: nombre_usuario});
    
    //Auxiliar: control del resultado de la operación anterior
    console.log(datos);

    //2 - Creamos un objeto para contener la respuesta del servidor
    // y le asignamos el resultado de la función 'fetch'
    const respuesta = await fetch("verusuario.php",
        {
            method:"POST", //Método de comunicación
            headers: { //Encabezados, necesarios para identificar correctamente los datos enviados
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: datos //Datos de la consulta enviada.
        }
    );

    //3 - Una vez recibida, la función entrega la respuesta obtenida en formato JSON
    return respuesta.json();
}


/**
 * Procesa los datos recibidos del servidor y los coloca en elementos en la página
 * @param {array} datosResultado Los datos contenidos en la respuesta del servidor
 */
function crearResultado(datosResultado) {
    //1 - Creamos los elementos necesarios para ubicar los datos de la respuesta en la página
    // mediante el método 'document.createElement', asignándolos a objetos para identificarlos mejor.
    const div_resultado = document.createElement("div");
    const div_encabezado = document.createElement("div");
    const h3_nombreU = document.createElement("h3");
    const div_datos = document.createElement("div");
    const table_datos = document.createElement("table");
    
    //2 - Agregamos las clases correspondientes a los elementos creados
    div_resultado.classList.add("resultado");
    div_encabezado.classList.add("encabezado");
    div_datos.classList.add("datos");
    table_datos.classList.add("tb_datos");

    //3 - Le agregamos el nombre del usuario al encabezado de los resultados
    h3_nombreU.innerText = datosResultado.nombre;

    //4 - Procesamos el array de datos con un 'forEach', 
    // excluyendo el nombre, que ya fue ubicado
    Object.entries(datosResultado).forEach(dato=>{
        
        //Separamos cada dato en una estructura clave/valor para poder procesar ambos
        const [clave, valor] = dato;

        //Si la clave NO ES 'nombre', la agregamos a la tabla de datos
        if (clave != "nombre") {

            //Auxiliar: control de datos
            console.log(clave,valor);

            //Creamos la fila y las celdas para el dato...
            const tr_ =  document.createElement("tr");
            const td_dato = document.createElement("td");
            const td_info = document.createElement("td");
            
            //...y les agregamos las clases correspondientes a las celdas
            td_info.classList.add("info");
            td_dato.classList.add("dato");
            
            //Agregamos el dato y su valor a las celdas correspondientes.
            // Al nombre del dato lo pasamos a mayúsculas con la función 'primeraLetrMayus'
            td_dato.innerText = primeraLetraMayus(clave);
            td_info.innerText = valor;

            //Agregamos las celdas a la fila y la fila a la tabla
            tr_.appendChild(td_dato);
            tr_.appendChild(td_info);
            table_datos.appendChild(tr_);
        }
    });

    //5 - Agregamos cada elemento a su posición correspondiente en la página.
    div_encabezado.appendChild(h3_nombreU);
    div_datos.appendChild(table_datos);
    div_resultado.appendChild(div_encabezado);
    div_resultado.appendChild(div_datos);
    div_resultados.appendChild(div_resultado);
}

/**
 * Toma un texto y pasa a mayúsculas la primera letra del mismo
 * @param {string} texto Texto a modificar
 * @returns El texto modificado
 */
function primeraLetraMayus(texto) {

    //Creamos una variable para el resultado y le asignamos...
    let resultado =    texto.charAt(0).toUpperCase()     +     texto.slice(1);
        //...la primera letra del texto ( método 'charAt' ) en mayúscula ( método 'toUpperCase')...
        //... y le concatenamos el resto del texto sin modificar (método 'slice')
        
    
    //Finalmente la función entrega el resultado obtenido.
    return resultado;
}

