import { formatNumber } from "./helpers.js";
import { getMonedaSymbol } from "./helpers.js";
// function formatNumber(numStr) {
//     if (!numStr) return '0.00';
//     const num = parseFloat(numStr);
//     if (isNaN(num)) return '0.00';

//     return new Intl.NumberFormat('es-PE', {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2
//     }).format(num);
// }

/**
 * Obtiene el símbolo de la moneda
 */
// function getMonedaSymbol(monedaCode) {
//     return (monedaCode === 'PEN') ? 'S/ ' : '$ ';
// }


function crearCeldaVehiculo(label, value) {
   return {
        // Usa un array de texto para formatear
        text: [
            { text: `${label} `, bold: false }, // La etiqueta en texto normal
            { text: (value || '').toUpperCase(), bold: true }  // El valor en negrita
        ],
        style:'cellText' // Aplica el estilo base (sin negrita)
    };
}




/**
 * Convierte un número en formato string (ej: "1234.56") a texto en español.
 * @param {string} numeroString - El número como string.
 * @param {string} codigoMoneda - 'PEN' o 'USD'.
 * @returns {string} - El número en letras (ej: "MIL DOSCIENTOS TREINTA Y CUATRO CON 56/100 SOLES")
 */
function convertirMonedaALetras(numeroString, codigoMoneda) {
    
    const numero = parseFloat(numeroString);
    if (isNaN(numero)) return "MONTO INVÁLIDO";

    let nombreMoneda, nombreMonedaPlural;

    if (codigoMoneda === 'PEN') {
        nombreMoneda = "SOL";
        nombreMonedaPlural = "SOLES";
    } else if (codigoMoneda === 'USD') {
        nombreMoneda = "DÓLAR AMERICANO";
        nombreMonedaPlural = "DÓLARES AMERICANOS";
    } else {
        nombreMoneda = "";
        nombreMonedaPlural = "";
    }

    const [parteEntera, parteDecimal] = numero.toFixed(2).split('.');
    
    let letras = numeroALetras(parseInt(parteEntera));

    if (parseInt(parteEntera) === 1) {
        letras = `${letras} ${nombreMoneda}`;
    } else {
        letras = `${letras} ${nombreMonedaPlural}`;
    }

    return `${letras} CON ${parteDecimal}/100`;
}


function numeroALetras(num) {
    const unidades = ["", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE"];
    const decenas = ["", "DIEZ", "VEINTE", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA"];
    const especiales = ["DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISÉIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE"];
    const centenas = ["", "CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS"];

    function convertirGrupo(n) {
        if (n === 0) return "";
        if (n === 100) return "CIEN";

        let salida = "";

        const c = Math.floor(n / 100);
        const d = Math.floor((n % 100) / 10);
        const u = n % 10;

        salida = centenas[c];

        if (n % 100 > 0) {
            const duo = n % 100;
            if (duo > 10 && duo < 20) {
                salida += (salida ? " " : "") + especiales[duo - 10];
            } else {
                let dec = decenas[d];
                let uni = unidades[u];

                if (duo > 0) {
                    if (duo < 10) {
                        salida += (salida ? " " : "") + uni;
                    } else if (duo === 10) {
                        salida += (salida ? " " : "") + dec;
                    } else if (duo > 19 && duo < 30) {
                        salida += (salida ? " " : "") + "VEINTI" + (u === 0 ? "E" : uni);
                    } else if (duo > 29) {
                        salida += (salida ? " " : "") + dec + (u > 0 ? " Y " + uni : "");
                    }
                }
            }
        }
        return salida;
    }

    if (num === 0) return "CERO";
    if (num === 1) return "UN"; // Especial para "UN SOL"

    let str = "";
    const millones = Math.floor(num / 1000000);
    const restoMillones = num % 1000000;

    if (millones > 0) {
        if (millones === 1) {
            str += "UN MILLÓN ";
        } else {
            str += convertirGrupo(millones) + " MILLONES ";
        }
    }

    const miles = Math.floor(restoMillones / 1000);
    const restoMiles = restoMillones % 1000;

    if (miles > 0) {
        if (miles === 1) {
            str += "MIL ";
        } else {
            str += convertirGrupo(miles) + " MIL ";
        }
    }

    if (restoMiles > 0) {
        str += convertirGrupo(restoMiles);
    }
    
    return str.trim().replace(/^UNO$/, "UN");
}



export function getPagina2(data) {
    const v = data.vehiculo;
    const f = data.financiamiento;
    // console.log('Datos para página 2: ', f);
    const symbol = getMonedaSymbol(f.moneda); 

    
    const cuotaInicialLetras = convertirMonedaALetras(f.cuotaInicial, f.moneda);
    const valorCuotaLetras = convertirMonedaALetras(f.valorCuota, f.moneda);

    const contenido = [
      
        {
            text: 'Que suscriben y que forman parte de este Contrato; bajo los términos y condiciones que se indican en las cláusulas siguientes:',
            style: 'parrafo',
            margin: [0, 25, 0, 10] 
        },

        {
            text: 'CONTRATO de CRÉDITO VEHICULAR', 
            style: 'tituloContrato',
            alignment: 'left', 
            margin: [0, 10, 0, 10]
        },

        {
            text: [
                { text: 'PRIMERA: ', bold: true },
                'Yonda & Grupo Huaraca, a solicitud del ',
                { text: 'CLIENTE', bold: true },
                ', ha aprobado en su favor el otorgamiento de un crédito bajo las características y condiciones que se detallan en la Hoja Resumen y en el presente formulario contractual, con la finalidad de financiar la adquisición de la propiedad del vehículo a su elección (en adelante, ',
                { text: 'Vehículo', bold: true },
                '):'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

      
        {
            table: {
                widths: ['*', '*'], 
                body: [
                   
                    [
                        crearCeldaVehiculo('MARCA:', v.marca),
                        crearCeldaVehiculo('N° MOTOR:', v.serie_motor)
                    ],
                    
                    [
                        crearCeldaVehiculo('MODELO:', v.modelo),
                        crearCeldaVehiculo('N° SERIE:', v.serie_motor)
                    ],
                 
                    [
                        crearCeldaVehiculo('COLOR:', v.color),
                        crearCeldaVehiculo('AÑO DE MODELO:', v.anio)
                    ],
                
                    [
                        crearCeldaVehiculo('PLACA:', v.placa || 'EN TRAMITE'),
                        crearCeldaVehiculo('', '')
                    ]
                ]
            },
            layout: {
                hLineWidth: (i, node) => 1, // Borde horizontal
                vLineWidth: (i, node) => 1, // Borde vertical
                paddingTop: () => 3,
                paddingBottom: () => 3,
            },
            margin: [0, 10, 0, 10] // margen inferior
        },


        {
            text: [
                'El mismo que, quedará en poder de ',
                { text: 'EL CLIENTE', bold: true },
                ', reservándose el derecho de propiedad del bien a ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ' quien podrá transferir de manera temporal de ser necesario a una tercera persona sin que esto genere ningún tipo de perjuicio al cliente hasta cuando el ',
                { text: 'CLIENTE', bold: true },
                ' haya cancelado todo el crédito o costo total del bien, aunque este haya sido entregado al ',
                { text: 'CLIENTE', bold: true },
                ', quien asume el riesgo total de su perdida y deterioro desde el momento de la entrega, tal y como lo dispone el Artículo 1583° del Código Civil Peruano, así como también asume la ',
                { text: 'inscripción y el pago del impuesto vehicular, papeletas o multas por infracción de tránsito.', bold: true }
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 5]
        },
        {
            text: 'El importe del crédito financiado, el tipo de moneda, plazo, intereses, las comisiones, los gastos, entre otros detalles del crédito se encuentran establecidos en la Hoja Resumen.',
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

        // --- Cláusula SEGUNDA ---
        {
            text: [
                { text: 'SEGUNDA: ', bold: true },
                { text: 'EL CLIENTE', bold: true },
                ' declara expresamente que (i) ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ' ha cumplido con proporcionarle, previo a la suscripción del presente Contrato, toda la información necesaria sobre las características, términos y condiciones del Crédito, la misma que le ha permitido tomar una decisión adecuadamente informada respecto del Crédito, (ii)) ha recibido de ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ' la Hoja Resumen y copia de la Letra de cambio en blanco que ha emitido a la orden de ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ', como parte de este Contrato, (iv) conoce y ha prestado su consentimiento sobre los intereses, comisiones y gastos de su cargo aplicables al Crédito que se detallan en la Hoja Resumen, incluyendo los gastos, que se generen por cualquier incumplimiento en el pago del saldo deudor del Crédito.'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

        {
            text: [
                { text: 'TERCERA: ', bold: true },
                { text: 'EL CLIENTE', bold: true },
                ' se obliga a pagar como CUOTA INICIAL el importe de ',
                { text: `${symbol}${formatNumber(f.cuotaInicial)} (${cuotaInicialLetras})`, bold: true },
                ', así como también se obliga a pagar el Crédito mediante cuotas mensuales de ',
                { text: `${symbol}${formatNumber(f.valorCuota)} (${valorCuotaLetras})`, bold: true },
                ', por un periodo de ',
                { text: `${f.numCuotas} meses`, bold: true },
                ', tal como consta en la Hoja Resumen, más los intereses, tributos, comisiones y gastos a que hubiere lugar, en la forma y plazos establecidos en el Cronograma de Pagos (en adelante Cronograma) que integra la Hoja Resumen, el cual ',
                { text: 'EL CLIENTE', bold: true },
                ' declara conocer. ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ' podrá eventualmente conceder al ',
                { text: 'CLIENTE', bold: true },
                ' períodos de gracia para el pago del Crédito, ante la previa solicitud de éste.'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        }
    ];

    
    return contenido;
}