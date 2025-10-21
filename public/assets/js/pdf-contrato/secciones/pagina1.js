


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

    return {
        table: {
            widths: ['*', '*', '*'], 
            body: [
                // Fila 1: Nombre
                [{ text: 'NOMBRE / RAZON SOCIAL: ' + p.nombre.toUpperCase(), colSpan: 3, style: 'cellText', border: [true, true, true, true] }, {}, {}],
                // Fila 2: Documento
                [{ text: 'DOC. DE IDENTIDAD / RUC: ' + p.documento, colSpan: 3, style: 'cellText', border: [true, true, true, true] }, {}, {}],
                // Fila 3: Dirección
                [{ text: 'DIRECCIÓN: ' + p.direccion.toUpperCase(), colSpan: 3, style: 'cellText', border: [true, true, true, true] }, {}, {}],
                // Fila 4: Ubigeo
                [
                    { text: 'DIST.: ' + p.distrito.toUpperCase(), style: 'cellText', border: [true, true, true, true] },
                    { text: 'PROV.: ' + p.provincia.toUpperCase(), style: 'cellText', border: [true, true, true, true] },
                    { text: 'DPTO.: ' + p.departamento.toUpperCase(), style: 'cellText', border: [true, true, true, true] }
                ],
                // Fila 5: Contacto
                [
                    { text: 'E-MAIL: ' + p.email.toUpperCase(), colSpan: 2, style: 'cellText', border: [true, true, true, true] },
                    {},
                    { text: 'CEL.: ' + p.telefono.toUpperCase(), style: 'cellText', border: [true, true, true, true] }
                ]
            ]
        },
        // Layout para que todas las celdas tengan bordes
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

    // --- TÍTULO DEL CONTRATO ---
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

    // --- PÁRRAFO DE INTRODUCCIÓN ---
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