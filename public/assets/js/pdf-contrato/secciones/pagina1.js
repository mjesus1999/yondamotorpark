


/**
 * Función auxiliar para crear la caja de datos de una persona (cliente, conyuge, etc.)
 */
/**
 * Función auxiliar para crear la caja de datos de una persona (cliente, conyuge, etc.)
 */
function crearBloquePersona(persona) {

    const p = {
        nombre: persona.nombre || '',
        documento: persona.documento || '',
        direccion: persona.direccion || '',
        distrito: persona.distrito || '',
        provincia: persona.provincia || '',
        departamento: persona.departamento || '',
        email: persona.email || '',
        telefono: persona.telefono || ''
    };

    // Define un estilo base para las celdas, sin negrita
    const cellStyle = {
        fontSize: 7.5,
        margin: [2, 2, 2, 2]
    };

    return {
        table: {
            widths: ['*', '*', '*'], 
            body: [
       
                [{ 
                    text: [
                        { text: 'NOMBRE / RAZON SOCIAL: ' },
                        { text: p.nombre.toUpperCase(), bold: true } 
                    ],
                    colSpan: 3, 
                    style: cellStyle, 
                    border: [true, true, true, true] 
                }, {}, {}],
                
           
                [{ 
                    text: [
                        { text: 'DOC. DE IDENTIDAD / RUC: ' },
                        { text: p.documento, bold: true }
                    ],
                    colSpan: 3, 
                    style: cellStyle, 
                    border: [true, true, true, true] 
                }, {}, {}],
                
             
                [{ 
                    text: [
                        { text: 'DIRECCIÓN: ' },
                        { text: p.direccion.toUpperCase(), bold: true }
                    ],
                    colSpan: 3, 
                    style: cellStyle, 
                    border: [true, true, true, true] 
                }, {}, {}],
                
             
                [
                    { 
                        text: [
                            { text: 'DIST.: ' },
                            { text: p.distrito.toUpperCase(), bold: true }
                        ],
                        style: cellStyle, 
                        border: [true, true, true, true] 
                    },
                    { 
                        text: [
                            { text: 'PROV.: ' },
                            { text: p.provincia.toUpperCase(), bold: true }
                        ],
                        style: cellStyle, 
                        border: [true, true, true, true] 
                    },
                    { 
                        text: [
                            { text: 'DPTO.: ' },
                            { text: p.departamento.toUpperCase(), bold: true }
                        ],
                        style: cellStyle, 
                        border: [true, true, true, true] 
                    }
                ],
                
            
                [
                    { 
                        text: [
                            { text: 'E-MAIL: ' },
                            { text: p.email.toUpperCase(), bold: true }
                        ],
                        colSpan: 2, 
                        style: cellStyle, 
                        border: [true, true, true, true] 
                    },
                    {},
                    { 
                        text: [
                            { text: 'CEL.: ' },
                            { text: p.telefono.toUpperCase(), bold: true }
                        ],
                        style: cellStyle, 
                        border: [true, true, true, true] 
                    }
                ]
            ]
        },
      
        layout: {
            hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 1 : 1,
            vLineWidth: (i, node) => (i === 0 || i === node.table.widths.length) ? 1 : 1,
            paddingTop: () => 3,
            paddingBottom: () => 3,
        }
    };
}

/**
 * Función principal que construye el array de contenido para la página 1
 */
export function getPagina1(data) {
    
  
    const contenido = [];


    contenido.push({
        table: {
            widths: ['*'],
            body: [
                [{ text: 'CONTRATO DE CRÉDITO VEHICULAR CON RETENCION DE DOMINIO DE VEHICULO', style: 'tituloContrato' }],
                [{ text: `${data.codigo_contrato} — SEDE ${data.sede.toUpperCase()}`, style: 'subtituloContrato' }]
            ]
        },
        layout: {
            hLineWidth: (i) => (i === 0 || i === 2) ? 1.5 : 0, 
            vLineWidth: () => 1.5, 
            paddingTop: () => 4,
            paddingBottom: () => 4,
        },
        margin: [0, 20, 0, 10] 
    });

    
    contenido.push({
        text: [
            'Conste por el presente documento privado con firmas notarialmente legalizadas que suscriben de una parte, ',
            { text: window.nombreEmpresa || 'Yonda & Grupo Huaraca E.I.R.L.', bold: true },
           {text: ', identificado con RUC: ', bold: true },
            { text: rucEmpresa || '20609396866', bold: true },
            ', con domicilio en provincia de Chincha, Panamericana Sur Km 201 S/N (MOTORPARK) referencia bajada de la molina antes de llegar al colegio Santa María distrito de Chincha Alta, provincia de Chincha, departamento de Ica, al que en adelante se le denominará ',
            { text: 'Yonda & Grupo Huaraca', bold: true },
            '; quien procede en este contrato debidamente representado por el funcionario que suscribe según poderes que constan inscritos en la Partida Electrónica N°11083166, del Registro de Personas Jurídicas Libro De Empresas Individuales De Responsabilidad limitada y, de la otra parte:',
            '\n\nA quien en adelante se le denominará el',
            { text: ' CLIENTE:', bold: true}
        ],
        style: 'parrafo',
        margin: [0, 10, 0, 5] 
    });

    // --- BLOQUE CLIENTE (Siempre se muestra) ---
    contenido.push(crearBloquePersona(data.cliente));


    // --- BLOQUE CÓNYUGE (Condicional) ---

    if (data.conyuge && data.conyuge.nombre) {
        contenido.push({ 
            text: [
                'Interviene como ',
                { text: 'CÓNYUGE:', bold: true }
                
            ],
            style: 'parrafo', 
            margin: [0, 10, 0, 5] 
        });
        contenido.push(crearBloquePersona(data.conyuge));
    }

    // --- BLOQUE AVAL (Condicional) ---
    if (data.aval && data.aval.nombre) {
        contenido.push({ 
            text: [ 
                'Interviene como ',
                { text: 'AVAL:', bold: true }
            ],
            style: 'parrafo', 
            margin: [0, 10, 0, 5] 
        });
        contenido.push(crearBloquePersona(data.aval));
    }

    // --- BLOQUE CÓNYUGE DEL AVAL (Condicional) ---

    if (data.aval && data.aval.conyuge && data.aval.conyuge.nombre) {
        contenido.push({ 
            text: 'Interviene como CÓNYUGE DEL AVAL:', 
            style: 'parrafo', 
            margin: [0, 10, 0, 5] 
        });
        contenido.push(crearBloquePersona(data.aval.conyuge));
    }

  
    return contenido;
}