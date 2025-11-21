document.body.addEventListener('click', (e) => {
   
    const button = e.target.closest('.btn-download-pdf');

    if (button) {
        e.preventDefault(); 
        const id = button.getAttribute('data-id');
        window.cotId = id;
        generatePDFCotizacion();
    }
});


    function formatDateSpanish(dateInput, ciudad = 'Chincha') {
        const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
        let dt;

        if (!dateInput) {
            dt = new Date();
        } else if (dateInput instanceof Date) {
            dt = dateInput;
        } else {
            const normalized = String(dateInput).replace(' ', 'T');
            dt = new Date(normalized);
            if (isNaN(dt.getTime())) {
                const parts = String(dateInput).split(/[-T ]/);
                if (parts.length >= 3) {
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10) - 1;
                    const d = parseInt(parts[2], 10);
                    dt = new Date(y, m, d);
                } else {
                    dt = new Date();
                }
            }
        }

        const day = dt.getDate();
        const month = months[dt.getMonth()] || '';
        const year = dt.getFullYear();
        return `${ciudad}, ${day} de ${month.toLowerCase()} de ${year}`;
    }

    function formatCurrency(amount, currency = 'PEN') {
        const symbol = (currency === 'USD') ? '$' : 'S/';
        const numAmount = parseFloat(amount) || 0;
        return `${symbol} ${numAmount.toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    async function convertImageToBase64(imagePath) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';

            img.onload = function () {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;

                ctx.drawImage(img, 0, 0);

                try {
                    // Usar un formato más eficiente
                    const dataURL = canvas.toDataURL('image/jpeg', 0.85); 
                    resolve(dataURL);
                } catch (error) {
                    reject(error);
                }
            };

            img.onerror = function () {
                console.warn(`No se pudo cargar la imagen: ${imagePath}`);
                resolve(null); // Continuar sin la imagen si falla
            };

            img.src = imagePath;
        });
    }

    
    async function fetchCotizacionData() {
        try {
           
            const response = await fetch(`/api/cotizacion/${cotId}`); 
            if (!response.ok) {
              
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            const payload = await response.json();
            
            return payload.cotizacion ?? payload; 
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            throw error; 
        }
    }


    function createPDFDefinition(cotizacion, headerImageBase64) {
        const cotizacionId = String(cotizacion.id || cotId || '').padStart(7, '0');
        const fechaRaw = cotizacion.fechareactivacion ?? cotizacion.fecha ?? cotizacion.fecha_registro ?? new Date().toISOString();
        const fecha = formatDateSpanish(fechaRaw, 'Chincha');

        // Procesar opciones de financiamiento
        const opcionesFinanciamiento = cotizacion.opciones_financiamiento || [];
        const opcionesOrdenadas = [...opcionesFinanciamiento].sort((a, b) => {
            return (parseInt(a.numcuotas) || 0) - (parseInt(b.numcuotas) || 0);
        });

        // Procesar requisitos con gastos administrativos dinámicos
        const gastosAdmin = parseFloat(cotizacion.gastosadministrativos || 1500);
        const requisitos = (cotizacion.requisitos || []).map(req => {
            const textoRequisito = req.requisito || req.texto || req;
            const textoLower = String(textoRequisito).toLowerCase();

            if (textoLower.includes('gastos administrativos') ||
                textoLower.includes('pago único') ||
                textoLower.includes('pago unico')) {
                return `Pago único por gastos administrativos S/ ${gastosAdmin.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
            return textoRequisito;
        });

        // Construir tabla de financiamiento
        const tablaFinanciamiento = opcionesOrdenadas.length > 0 ?
            opcionesOrdenadas.map((opcion, index) => {
                const meses = parseInt(opcion.numcuotas) || 0;
                const inicial = parseFloat(opcion.inicial) || 0;
                const cuota = parseFloat(opcion.valorcuota) || 0;
                const monedaTexto = (cotizacion.moneda === 'USD') ? 'DÓLARES' : 'SOLES';

                return [
                    { text: (index + 1).toString(), style: 'tableCell' },
                    { text: meses.toString(), style: 'tableCell' },
                    { text: formatCurrency(inicial, cotizacion.moneda), style: 'tableCell' },
                    { text: monedaTexto, style: 'tableCell' },
                    { text: formatCurrency(cuota, cotizacion.moneda), style: 'tableCellRight' }
                ];
            }) :
            [[{ text: 'Sin opciones de financiamiento disponibles', colSpan: 5, style: 'tableCellEmpty' }]];

        // Preparar asesor info
        const asesorNombre = cotizacion.asesor?.nombre_completo || cotizacion.asesor?.nombre || '';
        const asesorCargo = cotizacion.asesor?.cargo || '';
        let asesorTelefono = cotizacion.asesor?.telefono || '';

        if (asesorTelefono && !asesorTelefono.includes('056') && asesorTelefono.length === 9) {
            asesorTelefono = `(056) ${asesorTelefono}`;
        }

        return {
            pageSize: 'A4',
            pageOrientation: 'portrait',
            pageMargins: [70, 65, 40, 80],

            header: function (currentPage) {
                if (currentPage === 1) {
                    return {
                        image: window.cabeceraYonda || headerImageBase64,
                        width: 520,
                        alignment: 'center',
                        margin: [0, 10, 0, 0]
                    };
                }
                return null;
            },

            footer: function () {
                return {
                    stack: [
                        {
                            text: `${asesorNombre}\n${asesorCargo}\nTELÉFONO: ${asesorTelefono}`,
                            style: 'footerAsesor',
                            alignment: 'right',
                            margin: [0, 0, 0, 5]
                        },
                        {
                            canvas: [
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 0,
                                    x2: 525,
                                    y2: 0,
                                    lineWidth: 4,
                                    lineColor: '#ff6600'
                                }
                            ],
                            margin: [0, 0, 0, 0]
                        }
                    ],
                    margin: [40, 10, 40, 10]
                };
            },

            content: [
                {
                    text: fecha.replace(/ /g, '\u00A0'),
                    style: 'fecha',
                    alignment: 'right',
                    margin: [0, 0, 0, 2]
                },
                ...(cotizacion.fechareactivacion ? [{
                    text: '(REACTIVADA: ' + formatDateSpanish(cotizacion.fechareactivacion, '') + ')',
                    style: 'fecha',
                    alignment: 'right',
                    margin: [0, 0, 0, 8],
                    italics: true
                }] : []),
                {
                    text: `COTIZACIÓN VEHICULAR - ${cotizacionId}`,
                    style: 'titulo',
                    alignment: 'center',
                    fontSize: 15,
                    margin: [0, 0, 0, 12]
                },
                {
                    text: 'Presente, atte. Yonda & Grupo Huaraca E.I.R.L.',
                    style: 'normal',
                    margin: [0, 0, 0, 4]
                },
                {
                    text: 'RUC: 20609396866',
                    style: 'normal',
                    margin: [0, 0, 0, 8]
                },
                {
                    text: 'De nuestra consideración, nos es grato dirigirnos a usted para brindarle una cotización vehicular de acuerdo al siguiente detalle:',
                    style: 'normal',
                    alignment: 'justify',
                    margin: [0, 0, 0, 12]
                },
                {
                    table: {
                        widths: [140, '*'],
                        body: [
                            [{ text: 'Nombre del cliente', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.nombre || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Dni', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.dni || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Celular', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.celular || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Marca del vehículo', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.marca || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Modelo', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.modelo || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Año', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.anio || ''}`, style: 'tableClienteValue' }],
                            [{ text: 'Color', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.color || 'Por definir'}`, style: 'tableClienteValue' }],
                            [{ text: 'Precio', style: 'tableClienteLabel' }, { text: `: ${formatCurrency(cotizacion.precios?.precio_original || 0, cotizacion.moneda)}`, style: 'tableClienteValue' }]
                        ]
                    },
                    layout: {
                        hLineWidth: function () { return 0; },
                        vLineWidth: function () { return 0; },
                        paddingLeft: function () { return 0; },
                        paddingRight: function () { return 0; },
                        paddingTop: function () { return 1; },
                        paddingBottom: function () { return 1; }
                    },
                    margin: [0, 0, 0, 12]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: ['8%', '14%', '26%', '26%', '26%'],
                        body: [
                            [
                                { text: '#', style: 'tableHeader' },
                                { text: 'Meses', style: 'tableHeader' },
                                { text: 'Inicial', style: 'tableHeader' },
                                { text: 'Moneda', style: 'tableHeader' },
                                { text: 'Monto', style: 'tableHeader' }
                            ],
                            ...tablaFinanciamiento
                        ]
                    },
                    layout: {
                        hLineWidth: function () { return 0.5; },
                        vLineWidth: function () { return 0.5; },
                        hLineColor: function () { return '#000000'; },
                        vLineColor: function () { return '#000000'; }
                    },
                    margin: [0, 0, 0, 15]
                },
                {
                    text: 'Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro ejecutivo de ventas la siguiente documentación:',
                    style: 'normal',
                    margin: [0, 0, 0, 8]
                },
                ...(requisitos.length > 0 ? [{
                    ol: requisitos,
                    style: 'lista',
                    margin: [30, 0, 0, 12] 
                }] : []),
                {
                    table: {
                        widths: ['*'],
                        body: [
                            [{
                                text: 'ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES',
                                fontSize: 11,
                                bold: true,
                                alignment: 'center',
                                fillColor: '#fff9c4',
                                noWrap: true
                            }]
                        ]
                    },
                    layout: {
                        hLineWidth: function () { return 0; },
                        vLineWidth: function () { return 0; },
                        paddingLeft: function () { return 8; },
                        paddingRight: function () { return 8; },
                        paddingTop: function () { return 4; },
                        paddingBottom: function () { return 4; }
                    },
                    alignment: 'center',
                    margin: [20, 8, 20, 12]
                },
                {
                    text: 'Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar cumplimiento a todos los procesos señalados.',
                    style: 'normal',
                    alignment: 'justify',
                    margin: [0, 0, 0, 8]
                },
                {
                    text: 'Atte.',
                    style: 'normal',
                    bold: true,
                    margin: [0, 0, 0, 15]
                }
            ],

            styles: {
                fecha: { fontSize: 11 },
                titulo: { fontSize: 15, bold: true },
                normal: { fontSize: 11, lineHeight: 1.15 },
                tableClienteLabel: { fontSize: 10, bold: true, lineHeight: 0.95, margin: [0, 1, 0, 1] },
                tableClienteValue: { fontSize: 10, bold: true, lineHeight: 0.95, margin: [0, 1, 0, 1] },
                tableHeader: { fontSize: 10, bold: true, fillColor: '#e9ecef', alignment: 'center' },
                tableCell: { fontSize: 10, alignment: 'center' },
                tableCellRight: { fontSize: 10, alignment: 'right', bold: true },
                tableCellEmpty: { fontSize: 10, alignment: 'center', color: '#666', italics: true },
                lista: { fontSize: 11, lineHeight: 1.15, bold: true },
                footerAsesor: { fontSize: 11, color: '#333333', lineHeight: 1.2, bold: true },
            }
        };
    }

  
    async function generatePDFCotizacion() {
      

        try {
            
            const cotizacion = await fetchCotizacionData();
            const headerImageBase64 = await convertImageToBase64('/assets/images/logos/cabecera-yonda.png'); 
            const docDefinition = createPDFDefinition(cotizacion, headerImageBase64);
            const pdfDocGenerator = pdfMake.createPdf(docDefinition);

            pdfDocGenerator.open();
            
          

        } catch (error) {
            console.error('Error al generar PDF:', error);
           
        }
    }

  
