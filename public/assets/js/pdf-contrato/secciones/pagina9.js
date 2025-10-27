
import { crearBloqueFirma } from "./helpers.js";

export function getPagina9(data) {
    const tipoDoc = data.cliente.tipocliente == 'P' ? 'DNI' : 'RUC'

    const contenido = [

        {
            text: 'INFORMACIÓN IMPORTANTE',
            style: 'tituloImportante',
            alignment: 'center',
            margin: [0, 8, 0, 15]
        },


        {
            text: [
                'Los pagos se podrán realizar en las agencias del Banco de Crédito del Perú, Banco Continental, Banco Scotiabank o Interbank. Asimismo, en la oficina de ',
                { text: 'Yonda Perú.', bold: true }
            ],
            style: 'parrafo',
            alignment: 'center',
            margin: [0, 0, 0, 10]
        },

        {

            stack: [
             
               {
                  
                    table: {
                        widths: ['*'],
                        body: [
                            [
                               
                                {
                                    text: 'NÚMEROS DE CUENTA - RUC: 20609396866',
                                    style: 'tituloCuenta',
                                    alignment: 'center',
                                    color: 'white', 
                                    bold: true,
                                    fillColor: '#F58220', 
                                    border: [false, false, false, false] // Sin bordes
                                }
                            ]
                        ]
                    },
                    margin: [0, 0, 0, 5] 
                },

        
                {
                    alignment: 'center',
                    columnGap: 10, 
                    columns: [
                      
                        {
                            image: window.interbankLogo,
                            width: 80, 
                            height:45,
                            alignment: 'center'
                        },
                 
                        {
                            width: '*', 
                            table: {
                                widths: [75, '*', '*'],
                                body: [
                                    [
                                        { text: '', border: [false, false, false, false], fillColor: '#F58220' }, // Celda vacía sobre SOLES
                                        { text: 'N° CTA.', style: 'headerCuenta', fillColor: '#F58220' },
                                        { text: 'INTERBANCARIO - CCI', style: 'headerCuenta', fillColor: '#F58220' }
                                    ],
                                    [
                                        { text: 'SOLES', style: 'monedaSoles' },
                                        { text: '402 - 30041193 - 55', style: 'textoCuenta' },
                                        { text: '00 - 340200300411935555', style: 'textoCuenta' }
                                    ],
                                    [
                                        { text: 'DÓLARES', style: 'monedaDolares' },
                                        { text: '402 - 300411936 - 2', style: 'textoCuenta' },
                                        { text: '003-402- 003004119362-51', style: 'textoCuenta' }
                                    ]
                                ]
                            },
                            layout: 'lightHorizontalLines' 
                        }
                    ]
                },

             
                {
                    alignment: 'center',
                    columnGap: 10,
                    margin: [0, 5, 0, 0],
                    columns: [
                        {
                            image: window.scotiabankLogo,
                            width: 80,
                            height:45,
                            alignment: 'center'
                        },
                        {
                            width: '*',
                            table: {
                                widths: [75, '*', '*'],
                                body: [
                                    [
                                        { text: '', border: [false, false, false, false], fillColor: '#F58220' },
                                        { text: 'N° CTA.', style: 'headerCuenta', fillColor: '#F58220' },
                                        { text: 'INTERBANCARIO - CCI', style: 'headerCuenta', fillColor: '#F58220' }
                                    ],
                                    [
                                        { text: 'SOLES', style: 'monedaSoles' },
                                        { text: '000 - 2381999', style: 'textoCuenta' },
                                        { text: '00 - 930100000238199938', style: 'textoCuenta' }
                                    ],
                                    [
                                        { text: 'DÓLARES', style: 'monedaDolares' },
                                        { text: '000 - 5142842', style: 'textoCuenta' },
                                        { text: '009 -301- 000005142842-33', style: 'textoCuenta' }
                                    ]
                                ]
                            },
                            layout: 'lightHorizontalLines'
                        }
                    ]
                },

                
                {
                    alignment: 'center',
                    columnGap: 10,
                    margin: [0, 5, 0, 0],
                    columns: [
                        {
                            image: window.bbvaLogo,
                            width: 80,
                            height:45,
                            alignment: 'center'
                        },
                        {
                            width: '*',
                            table: {
                                widths: [75, '*', '*'],
                                body: [
                                    [
                                        { text: '', border: [false, false, false, false], fillColor: '#F58220' },
                                        { text: 'N° CTA.', style: 'headerCuenta', fillColor: '#F58220' },
                                        { text: 'INTERBANCARIO - CCI', style: 'headerCuenta', fillColor: '#F58220' }
                                    ],
                                    [
                                        { text: 'SOLES', style: 'monedaSoles' },
                                        { text: '00 - 1102150100057114', style: 'textoCuenta' },
                                        { text: '0 - 11215010005711419', style: 'textoCuenta' }
                                    ],
                                    [
                                        { text: 'DÓLARES', style: 'monedaDolares' },
                                        { text: '0200932381', style: 'textoCuenta' },
                                        { text: '01121500020093238113', style: 'textoCuenta' }
                                    ]
                                ]
                            },
                            layout: 'lightHorizontalLines'
                        }
                    ]
                },

               
                {
                    alignment: 'center',
                    columnGap: 10,
                    margin: [0, 5, 0, 0],
                    columns: [
                        {
                            image: window.bcpLogo,
                            width: 80,
                            alignment: 'center'
                        },
                        {
                            width: '*',
                            table: {
                                widths: [75, '*', '*'],
                                body: [
                                    [
                                        { text: '', border: [false, false, false, false], fillColor: '#F58220' },
                                        { text: 'N° CTA.', style: 'headerCuenta', fillColor: '#F58220' },
                                        { text: 'INTERBANCARIO - CCI', style: 'headerCuenta', fillColor: '#F58220' }],
                                    [
                                        { text: 'SOLES', style: 'monedaSoles' },
                                        { text: '1941467876028', style: 'textoCuenta' },
                                        { text: '00219400146787602892', style: 'textoCuenta' }
                                    ],
                                    [
                                        { text: 'DÓLARES', style: 'monedaDolares' },
                                        { text: '1941467964127', style: 'textoCuenta' },
                                        { text: '00219400146796412795', style: 'textoCuenta' }
                                    ]
                                ]
                            },
                            layout: 'lightHorizontalLines'
                        }
                    ]
                },

                {
                    columns: [
                      
                        {
                            width: '*', 
                            alignment: 'left',
                            margin: [4, 0, 0, 0], 
                           
                            columns: [
                                
                                {
                                    image: window.logoWsap, 
                                    width: 12, 
                                    margin: [0, 0, 0, 0] 
                                },
                              
                                {
                                    text: '950690394 - 908808538',
                                    style: 'contactoPie',
                                    margin: [5, 0, 0, 0] 
                                }
                            ]
                        },
                       
                        {
                            image: window.logoBase64,
                            width: 50,
                            alignment: 'right'
                        }
                    ],
                    margin: [0, 5, 0, 0]
                }
            ],
            alignment: 'center',
            margin: [0, 7, 0, 10]
        },
       
        {
            text: [
                'Las Pre - cancelaciones y Cancelaciones se realizarán exclusivamente previo acuerdo con ',
                { text: 'Yonda & Grupo Huaraca E.I.R.L.', bold: true }
            ],
            style: 'parrafo',
            alignment: 'center',
            margin: [0, 0, 0, 8]
        },

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




    contenido.push({ text: '', margin: [0, 7.5, 0, 0] });

    const bloquesDeFirmas = [];


    bloquesDeFirmas.push(
        crearBloqueFirma(
            'FIRMA DEL TITULAR',
            data.cliente.nombre,
            `${tipoDoc}: ${data.cliente.documento}`
        )
    );


    if (data.conyuge && data.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL CONYUGE',
                data.conyuge.nombre,
                `DNI N° ${data.conyuge.documento}`
            )
        );
    }



    if (data.aval && data.aval.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL AVAL',
                data.aval.nombre,
                `DNI N° ${data.aval.documento}`
            )
        );
    }


    if (data.aval && data.aval.conyuge && data.aval.conyuge.nombre) {
        bloquesDeFirmas.push(
            crearBloqueFirma(
                'FIRMA DEL CONYUGE AVAL',
                data.aval.conyuge.nombre,
                `DNI N° ${data.aval.conyuge.documento}`
            )
        );
    }

 
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
                columns: [bloqueFirmaUnica],
                margin: [0, 15, 0, 15]
            });
        } else {
            // Fila normal de dos firmas
            contenido.push({
                columns: [
                    bloquesDeFirmas[i],
                    bloquesDeFirmas[i + 1]
                ],
                margin: [0, 15, 0, 12]
            });
        }
    }

    return contenido;
}