import { crearBloqueFirma } from "./helpers.js";

function getFechaActual() {
    const date = new Date();
    const day = date.getDate();
  
    const month = date.toLocaleString('es-PE', { month: 'long' });
    const year = date.getFullYear();
    return `Chincha Alta, ${day} de ${month} del ${year}`;
}



export function getPagina7(data) {
    const tipoDoc = data.cliente.tipocliente == 'P' ? 'DNI':'RUC';

    const contenido = [
        // --- Textos Legales ---
        {
            text: 'Ley de Títulos Valores, Ley N° 27287.',
            style: 'parrafoLegal',
            margin: [0, 25, 0, 0] 
        },
        {
            text: 'Circular N° G-0090-2001 de la SBS (Título valor emitido en forma incompleta)',
            style: 'parrafoLegal',
            margin: [0, 5, 0, 0]
        },
        {
            text: 'Código Civil Peruano.',
            style: 'parrafoLegal',
            margin: [0, 5, 0, 0]
        },
        {
            text: [
                'Las Partes se someten a la jurisdicción de los jueces y tribunales del Distrito Judicial que corresponda a la oficina de ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                '.'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

        // --- Cláusula VIGESIMA ---
        {
            text: [
                { text: 'VIGESIMA: INTERVENCIÓN DEL CONYUGE\n', bold: true }, 
                'De ser el caso, interviene en este Contrato, el/la cónyuge del ',
                { text: 'CLIENTE/CONSTITUYENTE', bold: true },
                ' a fin de prestar su conformidad a los términos y condiciones del presente contrato, las condiciones particulares del crédito, garantías, letra de cambio y las obligaciones que El ',
                { text: 'CLIENTE/CONSTITUYENTE', bold: true },
                ' asume frente a ',
                { text: 'YONDA & GRUPO HUARACA', bold: true },
                ', asumiendo la calidad de obligado solidario con el ',
                { text: 'CLIENTE/EL CONSTITUYENTE', bold: true },
                '.'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

        // --- Cláusula VIGESIMA PRIMERA ---
        {
            text: [
                { text: 'VIGESIMA PRIMERA: ACUERDO ', bold: true },
                'Ambas partes contratantes tanto ',
                { text: 'Yonda & Grupo Huaraca', bold: true },
                ' y el ',
                { text: 'CLIENTE', bold: true },
                ', encontrándose en el uso de todas sus facultades mentales y sin intimidación alguna se someterán a las cláusulas penales y obligaciones que se deriven de este contrato si en algún momento hubiera conflicto.'
            ],
            style: 'parrafo',
            margin: [0, 15, 0, 10]
        },

        // --- Fecha ---
        {
            text: getFechaActual(),
            alignment: 'right',
            style: 'parrafo',
            margin: [0, 40, 0, 40] 
        },
    ];

    
  

    const bloquesDeFirmas = [];

    // FIRMA 1: TITULAR (Siempre)
    bloquesDeFirmas.push(
        crearBloqueFirma(
            'FIRMA DEL TITULAR',
            data.cliente.nombre,
            `${tipoDoc}: ${data.cliente.documento}`
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

    
    // distribuimos las firmas en filas de 2 columnas
    for (let i = 0; i < bloquesDeFirmas.length; i += 2) {
        
        // Verifica si es la última firma y está sola (impar)
        if (i + 1 === bloquesDeFirmas.length) {
       
            const bloqueFirmaUnica = bloquesDeFirmas[i];
            

            delete bloqueFirmaUnica.width; 
            
            // La agregamos en una columna única 
            contenido.push({
                columns: [ bloqueFirmaUnica ],
                margin: [0, 20, 0, 20]
            });

        } else {
            // Es una fila normal de dos firmas
            contenido.push({
                columns: [
                    bloquesDeFirmas[i],     // Firma de la izquierda
                    bloquesDeFirmas[i + 1]  // Firma de la derecha
                ],
                margin: [0, 20, 0, 40] 
            });
        }
    }

 
    return contenido;
}