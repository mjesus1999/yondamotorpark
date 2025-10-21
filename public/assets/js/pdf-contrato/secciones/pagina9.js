
import { crearBloqueFirma } from "./helpers.js";

export function getPagina9(data) {

    const contenido = [
       
        {
            text: 'INFORMACION IMPORTANTE',
            style: 'tituloImportante', 
            alignment: 'center',
            margin: [0, 25, 0, 15]
        },

        // --- Párrafo 1 ---
        {
            text: [
                'Los pagos se podrán realizar en las agencias del Banco de Crédito del Perú, Banco Continental, Banco Scotiabank o Interbank. Asimismo, en la oficina de ',
                { text: 'Yonda Motors.', bold: true }
            ],
            style: 'parrafo',
            alignment: 'center',
            margin: [0, 0, 0, 10]
        },

        // --- IMAGEN DE BANCOS ---
        {
            image: window.bancosYonda, 
            width: 300, 
            height:250,
            alignment: 'center',
            margin: [0, 10, 0, 7]
        },

     
        {
            text: [
                'Las Pre – cancelaciones y Cancelaciones se realizarán exclusivamente previo acuerdo con ',
                { text: 'Yonda & Grupo Huaraca E.I.R.L.', bold: true }
            ],
            style: 'parrafo',
            alignment: 'center',
            margin: [0, 0, 0, 15]
        },

        // --- LISTA DE PUNTOS ---
        {
            
            ul: [
                {
                    text: [
                        'Los fiadores solidarios y/o avalistas respaldan la operación de crédito, y cualquier otra obligación presente o futura, directa o indirecta que el cliente haya contraído o asuma con ',
                        { text: 'Yonda & Grupo Huaraca', bold: true },
                        ' durante la vigencia del Préstamo. La vigencia de la fianza/aval será indefinida, y sólo quedará liberada cuando el cliente cumpla con todas las obligaciones garantizadas.'
                    ],
                    style: 'listaItem'
                },
                {
                    text: [
                        { text: 'Yonda & Grupo Huaraca', bold: true },
                        ' sólo podrá modificar, unilateralmente, comisiones, gastos y otras estipulaciones contractuales distintas a las tasas de interés.'
                    ],
                    style: 'listaItem'
                },
                {
                    text: [
                        'Ante el incumplimiento de pago según las condiciones pactadas (2cuotas), se procederá a realizar el reporte a las Centrales de Riesgo de ',
                        { text: 'Yonda & Grupo Huaraca', bold: true },
                        ' para la realización de la notificación de cobranza correspondiente, costo asumido por el ',
                        { text: 'CLIENTE', bold: true },
                        ', de persistir en el incumplimiento se procederá con al recojo del vehículo, teniendo como plazo máximo para la cancelación de lo adeudado 72 horas, caso contrario se dará por resuelto el contrato, pudiendo la unidad entrar en remate sin previo aviso al cliente.'
                    ],
                    style: 'listaItem'
                },
                {
                    text: 'La entrega de tarjeta y placa será dentro de los 25 a 30 días hábiles.',
                    style: 'listaItem'
                }
            ]
        }
    ];

    

    

    contenido.push({ text: '', margin: [0, 10, 0, 0] }); 

    const bloquesDeFirmas = [];

    // FIRMA 1: TITULAR (Siempre)
    bloquesDeFirmas.push(
        crearBloqueFirma(
            'FIRMA DEL TITULAR',
            data.cliente.nombre,
            `DNI: ${data.cliente.documento}`
        )
    );

    // FIRMA 2: CÓNYUGE (Condicional)
    if (data.conyuge && data.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL CONYUGE',
                data.conyuge.nombre,
                `DNI N° ${data.conyuge.documento}`
            )
        );
    }

    // FIRMA 3: AVAL (Condicional)
    if (data.aval && data.aval.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL AVAL',
                data.aval.nombre,
                `DNI N° ${data.aval.documento}`
            )
        );
    }

    // FIRMA 4: CÓNYUGE DEL AVAL (Condicional)
    if (data.aval && data.aval.conyuge && data.aval.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL CONYUGE AVAL',
                data.aval.conyuge.nombre,
                `DNI N° ${data.aval.conyuge.documento}`
            )
        );
    }

    // FIRMA 5: REPRESENTANTE LEGAL (Siempre)
    bloquesDeFirmas.push(
        crearBloqueFirma(
            'FIRMA DEL REPRESENTANTE LEGAL',
            window.nombreEmpresa || 'YONDA & GRUPO HUARACA E.I.R.L',
            `RUC N° ${window.rucEmpresa || '20609396866'}`
        )
    );

    

    for (let i = 0; i < bloquesDeFirmas.length; i += 2) {
        
        if (i + 1 === bloquesDeFirmas.length) {
           
            const bloqueFirmaUnica = bloquesDeFirmas[i];
            delete bloqueFirmaUnica.width; 
            
            contenido.push({
                columns: [ bloqueFirmaUnica ],
                margin: [0, 15, 0, 15]
            });
        } else {
            // Fila normal de dos firmas
            contenido.push({
                columns: [
                    bloquesDeFirmas[i],
                    bloquesDeFirmas[i + 1]
                ],
                margin: [0, 15, 0,12]
            });
        }
    }
    
    return contenido;
}