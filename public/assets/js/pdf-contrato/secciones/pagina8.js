
import { crearBloqueFirma } from "./helpers.js";


function formatNumber(numStr) {
    if (!numStr) return '0.00';
    const num = parseFloat(numStr);
    if (isNaN(num)) return '0.00';
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}
function getMonedaSymbol(monedaCode) {
    return (monedaCode === 'PEN') ? 'S/ ' : '$ ';
}


// --- Layout para la tabla ---
const tablaLayout = {
    hLineWidth: (i, node) => 1, // Dibuja todas las líneas horizontales
    vLineWidth: (i, node) => 1, // Dibuja todas las líneas verticales
    paddingLeft: () => 4,
    paddingRight: () => 4,
    paddingTop: () => 3,
    paddingBottom: () => 3
};


export function getPagina8(data) {
    
    const f = data.financiamiento;

    const symbol = getMonedaSymbol(f.moneda);
    const monedaNombre = (f.moneda === 'PEN') ? 'SOLES' : 'DOLARES';
    const tipoDoc = data.cliente.tipocliente == 'P' ? 'DNI':'RUC';

    const headerStyle = 'resumenHeader';
    const cellStyle = 'resumenCell';
    const cellStyleRight = { style: 'resumenCell', alignment: 'right' };
    const emptyCell = { text: '' }; 

    const contenido = [
     
        {
            text: 'HOJA DE RESUMEN',
            style: 'tituloContrato',
            alignment: 'center',
            decoration:'underline',
            margin: [0, 25, 0, 15]
        },

        
        {
            table: {
            
                widths: ['*', '*', '*', '*'],
                body: [
              
                    [
                        { text: 'TASAS POR GASTO ADMINISTRATIVO', colSpan: 2, style: headerStyle, alignment: 'center' }, {},
                        { text: 'PRESTAMO', colSpan: 2, style: headerStyle, alignment: 'center' }, {}
                    ],
                    
                    [
                        { text: 'T.A. POR G.A.', style: cellStyle }, { text: `${f.tasaAnual}%`, ...cellStyleRight },
                        { text: 'Monto financiado', style: cellStyle }, { text: `${symbol} ${formatNumber(f.montoFinanciado)}`, ...cellStyleRight }
                    ],
                 
                    [
                        { text: 'T.M. POR G.A.', style: cellStyle }, { text: `${f.tasaMensual}%`, ...cellStyleRight },
                        { text: 'Moneda', style: cellStyle }, { text: monedaNombre, ...cellStyleRight }
                    ],
               
                    [
                        emptyCell, emptyCell,
                        { text: 'Número de cuotas', style: cellStyle }, { text: f.numCuotas, ...cellStyleRight }
                    ],
                    
                    [
                        emptyCell, emptyCell,
                        { text: 'Fecha de pago de cuotas', style: cellStyle }, { text: f.diaPago, ...cellStyleRight }
                    ],
              
                    [
                        emptyCell, emptyCell,
                        { text: 'Cantidad total a pagar', style: cellStyle }, { text: `${symbol} ${formatNumber(f.totalPagar)}`, ...cellStyleRight }
                    ],
                  
                    [
                        { text: 'PENALIDADES POR INCUMPLIMIENTO', colSpan: 2, style: headerStyle, alignment: 'center' }, {},
                        { text: 'GASTOS', colSpan: 2, style: headerStyle, alignment: 'center' }, {}
                    ],
                  
                    [
                        { text: 'Interés moratorio mensual', style: cellStyle }, { text: '10% de la cuota mensual', ...cellStyleRight },
                        { text: 'Servicio de toma de firmas y Delivery de Documentos', style: cellStyle }, { text: 'Según tarifario', ...cellStyleRight }
                    ],
                  
                    [
                        { text: 'Gastos Judiciales y/o Conciliatorios', style: cellStyle }, { text: 'Según tarifario', ...cellStyleRight },
                        { text: 'Gastos Notariales', style: cellStyle }, { text: 'Según tarifario', ...cellStyleRight }
                    ],
                
                    [
                        { text: 'Gastos de Notificación', style: cellStyle }, { text: 'S/. 50.00', ...cellStyleRight },
                        emptyCell, emptyCell
                    ],
                 
                    [
                        { text: 'Gastos de recojo del vehículo Chincha', style: cellStyle }, { text: 'S/. 350.00', ...cellStyleRight },
                        emptyCell, emptyCell
                    ],
                  
                    [
                        { text: 'Gastos de recojo del vehículo Otras provincias (dependiendo la zona)', style: cellStyle }, { text: 'S/. 400.00 a S/. 650.00', ...cellStyleRight },
                        emptyCell, emptyCell
                    ],
                  
                    [
                        { text: 'OTROS', colSpan: 2, style: headerStyle, alignment: 'center' }, {},
                        { text: 'GPS', colSpan: 2, style: headerStyle, alignment: 'center' }, {}
                    ],
                  
                    [
                        { text: 'Duplicado de contrato/ tarjeta de propiedad/ llaves', style: cellStyle }, { text: 'Según tarifario', ...cellStyleRight },
                        { text: 'GPS cuota mensual', style: cellStyle }, { text: 'S/. 10.00', ...cellStyleRight }
                    ]
                ]
            },
            layout: tablaLayout 
        },
      

      
        {
            text: 'La tasa de gastos administrativos por mora se aplica sobre el importe de la cuota vencida, desde el primer día de atraso.',
            style: 'parrafo',
            fontSize: 8,
            bol:true,
            margin: [0, 15, 0, 10]
        },
        {
            text: 'El Cliente declara que la Hoja Resumen Informativa y Cronograma, así como el Contrato, le fueron entregados para su lectura y se absolvieron sus dudas y suscribe el presente documento en señal de aceptación y conformidad de toda la información y condiciones consignadas en la presente HR y en los antes referidos documentos.',
            style: 'parrafo',
            fontSize: 8.5,
            alignment: 'justify',
            margin: [0, 10, 0, 20]
        }
    ];


    const bloquesDeFirmas = [];
    bloquesDeFirmas.push(
        crearBloqueFirma('FIRMA DEL TITULAR', data.cliente.nombre, `${tipoDoc}: ${data.cliente.documento}`)
    );
    if (data.conyuge && data.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma('FIRMA DEL CONYUGE', data.conyuge.nombre, `DNI N° ${data.conyuge.documento}`)
        );
    }
    if (data.aval && data.aval.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma('FIRMA DEL AVAL', data.aval.nombre, `DNI N° ${data.aval.documento}`)
        );
    }
    if (data.aval && data.aval.conyuge && data.aval.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma('FIRMA DEL CONYUGE AVAL', data.aval.conyuge.nombre, `DNI N° ${data.aval.conyuge.documento}`)
        );
    }
    bloquesDeFirmas.push(
        crearBloqueFirma('FIRMA DEL REPRESENTANTE LEGAL', window.nombreEmpresa || 'YONDA & GRUPO HUARACA E.I.R.L', `RUC N° ${window.rucEmpresa || '20609396866'}`)
    );

 
    for (let i = 0; i < bloquesDeFirmas.length; i += 2) {
        if (i + 1 === bloquesDeFirmas.length) {
            const bloqueFirmaUnica = bloquesDeFirmas[i];
            delete bloqueFirmaUnica.width;
            contenido.push({
                columns: [ bloqueFirmaUnica ],
                margin: [0, 20, 0, 20]
            });
        } else {
            contenido.push({
                columns: [ bloquesDeFirmas[i], bloquesDeFirmas[i + 1] ],
                margin: [0, 20, 0, 20]
            });
        }
    }
    
    return contenido;
}