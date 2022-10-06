const cookies = procesarCookies(document.cookie);
//const datosCookies = cookies.split(";");

console.log(cookies);



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