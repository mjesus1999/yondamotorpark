<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cobranza Atrasado - Vista Previa PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }

        #loading-indicator {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border: 2px solid #d32f2f;
            border-radius: 15px;
            z-index: 2000;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        #loading-indicator::before {
            content: "";
            display: block;
            width: 40px;
            height: 40px;
            margin: 0 auto 15px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #d32f2f;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #download-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            max-width: 350px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .modal button {
            background: white;
            color: black;
            border: 1px solid #ccc;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
            border-radius: 4px;
        }

        .modal button:hover {
            background: #f5f5f5;
        }
    </style>
</head>

<body>
    <!-- Indicador de carga -->
    <div id="loading-indicator">
        <div style="color: #d32f2f; font-weight: bold; margin-bottom: 15px; font-size: 18px;">
            Generando PDF...
        </div>
        <div style="font-size: 14px; color: #666;">Por favor espere...</div>
    </div>

    <!-- PDFMake -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const isPreview = urlParams.get('preview') === '1';

        // Variables globales para mantener la consistencia
        let globalPdfBlob = null;
        let globalFilename = '';

        function formatDateSpanish() {
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
            const dt = new Date();
            const day = dt.getDate();
            const month = months[dt.getMonth()];
            const year = dt.getFullYear();
            return `Chincha Alta ${day} de ${month} del ${year}`;
        }

        function downloadPdfFromBlob(blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function showDownloadModal(filename) {
            const modalOverlay = document.createElement('div');
            modalOverlay.id = 'download-modal-overlay';
            modalOverlay.innerHTML = `
                <div class="modal">
                    <p style="margin: 0 0 20px 0; font-size: 14px;">PDF generado correctamente. ¿Desea descargarlo?</p>
                    <div>
                        <button id="btn-download-pdf">Descargar</button>
                        <button id="btn-cancel-download">Cancelar</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modalOverlay);

            const btnDownload = modalOverlay.querySelector('#btn-download-pdf');
            const btnCancel = modalOverlay.querySelector('#btn-cancel-download');

            function closeModal() {
                if (modalOverlay && modalOverlay.parentNode) {
                    modalOverlay.parentNode.removeChild(modalOverlay);
                }
            }

            btnDownload.addEventListener('click', () => {
                try {
                    if (globalPdfBlob) {
                        downloadPdfFromBlob(globalPdfBlob, filename);
                        closeModal();
                        setTimeout(() => {
                            try { window.close(); } catch (e) { }
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error al descargar:', error);
                    alert('Error al descargar el PDF');
                }
            });

            btnCancel.addEventListener('click', () => {
                closeModal();
                setTimeout(() => {
                    try { window.close(); } catch (e) { }
                }, 100);
            });

            // Cerrar con ESC
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escHandler);
                    setTimeout(() => {
                        try { window.close(); } catch (e) { }
                    }, 100);
                }
            });

            // Cerrar al hacer clic fuera
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    closeModal();
                    setTimeout(() => {
                        try { window.close(); } catch (e) { }
                    }, 100);
                }
            });

            setTimeout(() => btnDownload.focus(), 100);
        }

        function showPDFPreview(filename) {
            const previewHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #f5f5f5; z-index: 9999;">
                    <div style="height: 60px; background: white; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0; color: #333; font-size: 18px;">Vista Previa - ${filename}</h3>
                        <div>
                            <button id="btn-download-preview" style="background: #d32f2f; color: white; border: none; padding: 10px 20px; margin-right: 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                Descargar PDF
                            </button>
                            <button id="btn-close-preview" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                                Cerrar
                            </button>
                        </div>
                    </div>
                    <div id="pdf-preview-container" style="height: calc(100% - 60px); overflow: auto; padding: 20px;">
                        <div id="pdf-loading" style="text-align: center; padding: 50px; color: #666;">
                            <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #d32f2f; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
                            <div>Generando vista previa...</div>
                        </div>
                        <iframe id="pdf-frame" style="width: 100%; height: 100%; border: none; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: none;"></iframe>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', previewHTML);

            const btnDownload = document.getElementById('btn-download-preview');
            const btnClose = document.getElementById('btn-close-preview');
            const previewContainer = document.querySelector('[style*="position: fixed"]');
            const pdfFrame = document.getElementById('pdf-frame');
            const pdfLoading = document.getElementById('pdf-loading');

            // Usar el blob global para la vista previa
            if (globalPdfBlob) {
                const url = URL.createObjectURL(globalPdfBlob);
                pdfFrame.src = url;
                pdfFrame.style.display = 'block';
                pdfLoading.style.display = 'none';

                function cleanup() {
                    URL.revokeObjectURL(url);
                }

                btnClose.addEventListener('click', cleanup);
                window.addEventListener('beforeunload', cleanup);
            }

            // El botón de descarga usa el mismo blob global y cierra la preview
            btnDownload.addEventListener('click', () => {
                try {
                    if (globalPdfBlob) {
                        downloadPdfFromBlob(globalPdfBlob, filename);
                        // Cerrar la vista previa después de descargar
                        previewContainer.remove();
                        setTimeout(() => {
                            try { window.close(); } catch (e) { }
                        }, 500);
                    }
                } catch (error) {
                    console.error('Error al descargar:', error);
                    alert('Error al descargar el PDF');
                }
            });

            btnClose.addEventListener('click', () => {
                previewContainer.remove();
                setTimeout(() => {
                    try { window.close(); } catch (e) { }
                }, 100);
            });

            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    previewContainer.remove();
                    document.removeEventListener('keydown', escHandler);
                    setTimeout(() => {
                        try { window.close(); } catch (e) { }
                    }, 100);
                }
            });
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
                        const dataURL = canvas.toDataURL('image/jpeg', 0.85);
                        resolve(dataURL);
                    } catch (error) {
                        reject(error);
                    }
                };

                img.onerror = function () {
                    console.warn(`No se pudo cargar la imagen: ${imagePath}`);
                    resolve(null);
                };

                img.src = imagePath;
            });
        }

        function createNotificacionPDF(headerImageBase64) {
            return {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [60, 80, 60, 80],

                header: function (currentPage, pageCount, pageSize) {
                    if (headerImageBase64) {
                        return {
                            image: headerImageBase64,
                            width: 500,
                            alignment: 'center',
                            margin: [0, 20, 0, 0]
                        };
                    }
                    return null;
                },

                footer: function (currentPage, pageCount, pageSize) {
                    return {
                        stack: [
                            // Información de contacto alineada a la derecha
                            {
                                text: [
                                    'Contacto: Área de cobranza\n',
                                    '987454555\n',
                                    'cobranza@yondaperu.com'
                                ],
                                style: 'contactInfo',
                                alignment: 'right',
                                margin: [0, 0, 40, 10]
                            },
                            // Línea naranja centrada
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
                                alignment: 'center',
                                margin: [0, 0, 0, 0]
                            }
                        ]
                    };
                },

                content: [
                    // Fecha
                    {
                        text: formatDateSpanish(),
                        style: 'fecha',
                        alignment: 'right',
                        margin: [0, 0, 0, 20]
                    },

                    // Título principal
                    {
                        text: 'CONSTANCIA DE RECOJO VEHICULAR',
                        style: 'titulo',
                        alignment: 'center',
                        margin: [0, 0, 0, 15]
                    },

                    // Presentación del cliente
                    {
                        text: [
                            { text: 'Sra. ', style: 'normal', bold: true },
                            { text: 'Aná Lucia Torres', style: 'normal', bold: '' }
                        ],
                        margin: [0, 0, 0, 5]
                    },
                    {
                        text: [
                            { text: 'DNI: ', style: 'normal', bold: true },
                            { text: '78956232', style: 'normal', bold: '' }
                        ],
                        margin: [0, 0, 0, 5]
                    },
                    {
                        text: [
                            { text: 'Direccion: ', style: 'normal', bold: true },
                            { text: 'Chincha Alta', style: 'normal', bold: '' }
                        ],
                        margin: [0, 0, 0, 5]
                    },
                    {
                        text: [
                            { text: 'Celular: ', style: 'normal', bold: true },
                            { text: '999888777', style: 'normal', bold: '' }
                        ],
                        margin: [0, 0, 0, 10]
                    },

                    // Párrafo explicativo
                    {
                        text: [
                            'Mediante el presente: YHON KENNIDEY MENDOZA HUARACA. Representante General de YONDA & GRUPO HUARACA E.I.R.L hace de su conocimiento',
                            { text: 'EL RECOJO DEL VEHÍCULO CON LAS SIGUIENTES CARACTERISTICAS:', bold: true }
                        ],
                        style: 'normal',
                        alignment: 'justify',
                        margin: [0, 0, 0, 10]
                    },

                    // Detalles del vehículo en tabla
                    {
                        table: {
                            widths: [80, 15, '*'],
                            body: [
                                [
                                    { text: 'Modelo', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: 'SCH1111', style: 'detalleValue' }
                                ],
                                [
                                    { text: 'Marca', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: 'Suzuki Swift', style: 'detalleValue' }
                                ],
                                [
                                    { text: 'Chasis', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: 'L3HMCKBE3PA000111', style: 'detalleValue' }
                                ],
                                [
                                    { text: 'Motor', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: '209000111', style: 'detalleValue' }
                                ],
                                [
                                    { text: 'Color', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: 'Gris', style: 'detalleValue' }
                                ],
                                [
                                    { text: 'Placa', style: 'detalleLabel' },
                                    { text: ':', style: 'detalleSeparador' },
                                    { text: 'Z7L-111', style: 'detalleValue' }
                                ]
                            ]
                        },
                        layout: {
                            hLineWidth: function () { return 0; },
                            vLineWidth: function () { return 0; },
                            paddingLeft: function () { return 0; },
                            paddingRight: function () { return 0; },
                            paddingTop: function () { return 2; },
                            paddingBottom: function () { return 2; }
                        },
                        margin: [0, 0, 0, 10]
                    },

                    // Párrafo de incumplimiento
                    {
                        text: [
                            { text: 'Por Incumplimiento de pago', bold: true },
                            'ya que según registros de cobranza de nuestra empresa Usted adeuda',
                            { text: ', LA CUOTA DEL MES DE SETIEMBRE DE S/ 1,540.00, LA CUOTA DE OCTUBRE DE S/ 1,540.00.00 Y LA CUOTA DE NOVIEMBRE DE S/ 1400.00, SIENDO ASI SU DEUDA TOTAL EL MONTO DE S/ 4,480.00 SOLES.', bold: true },
                            'y habiendo usted comprometido según el contrato notarial firmado el 2/27/2023 . TODOS LOS 27 DE CADA MES Y NO CUMPLIENDOLO CON SU CRONOGRAMA DE PAGO.',
                            { text: '(CADA NOTIFICACIÓN LLEGADA AL DOMICILIO SE HARA EL COBRO ADICIONAL DE S/50 SOLES).', bold: true }
                        ],
                        /* text: [
                            'Por Incumplimiento de pago ya que según registros de cobranza de nuestra empresa Usted adeuda, POR LA MORA DE FEBRERO DE S/ 389.00 SOLES, DE LA CUOTA DE MARZO CON MORA DE S/ 1,556.50, ',
                            { text: 'CUYO MONTO TOTAL A PAGAR ES DE S/ 1,945.50 SOLES', bold: true },
                            ' Y HABIENDO USTED COMPROMETIDO SEGÚN EL CONTRATO NOTARIAL FIRMADO EL 05 DE OCTUBRE DEL 2023. (CADA NOTIFICACIÓN LLEGADA AL DOMICILIO SE HARA EL COBRO ADICIONAL DE S/50 SOLES).'
                        ], */
                        style: 'normal',
                        alignment: 'justify',
                        margin: [0, 0, 0, 15]
                    },

                    // Párrafo de denuncia
                    {
                        text: 'A su vez se procederá a realizar la denuncia correspondiente mediante instancias legales y judiciales que amerita el caso.',
                        style: 'normal',
                        alignment: 'justify',
                        margin: [0, 0, 0, 15]
                    },

                    // Atentamente
                    {
                        text: 'Atte: Gerencia', bold: true,
                        style: 'normal',
                        alignment: 'left',
                        margin: [0, 0, 0, 40]
                    },

                    // Sección de firma
                    {
                        table: {
                            widths: ['*'],
                            body: [
                                [{
                                    canvas: [
                                        {
                                            type: 'line',
                                            x1: 0,
                                            y1: 0,
                                            x2: 200,
                                            y2: 0,
                                            lineWidth: 1,
                                            lineColor: 'black'
                                        }
                                    ],
                                    alignment: 'center',
                                    border: [false, false, false, false],
                                    margin: [0, 0, 0, 5]
                                }],
                                [{
                                    text: 'YHON MENDOZA HUARACA',
                                    style: 'firma',
                                    alignment: 'center',
                                    border: [false, false, false, false],
                                    margin: [0, 0, 0, 2]
                                }],
                                [{
                                    text: 'GERENTE GENERAL',
                                    style: 'cargo',
                                    alignment: 'center',
                                    border: [false, false, false, false]
                                }]
                            ]
                        },
                        layout: 'noBorders',
                        margin: [0, 0, 0, 0]
                    }
                ],

                styles: {
                    fecha: {
                        fontSize: 12,
                        color: '#333'
                    },
                    titulo: {
                        fontSize: 15,
                        bold: true,
                        color: '#000'
                    },
                    normal: {
                        fontSize: 12,
                        lineHeight: 1.3,
                        color: '#000'
                    },
                    detalleLabel: {
                        fontSize: 12,
                        bold: true,
                        color: '#000'
                    },
                    detalleSeparador: {
                        fontSize: 12,
                        bold: true,
                        color: '#000',
                        alignment: 'center'
                    },
                    detalleValue: {
                        fontSize: 12,
                        bold: true,
                        color: '#000'
                    },
                    firma: {
                        fontSize: 12,
                        bold: true,
                        color: '#000'
                    },
                    cargo: {
                        fontSize: 12,
                        bold: true,
                        color: '#000'
                    },
                    contactInfo: {
                        fontSize: 12,
                        color: '#333333',
                        lineHeight: 1.2
                    }
                }
            };
        }

        async function generatePDFNotificacion() {
            const loadingIndicator = document.getElementById('loading-indicator');

            try {
                const headerImageBase64 = await convertImageToBase64('/assets/images/logos/cabecera-yondaa.png');

                const docDefinition = createNotificacionPDF(headerImageBase64);
                const pdfDocGenerator = pdfMake.createPdf(docDefinition);
                globalFilename = 'notificacion-cobranza-ana-torres.pdf';

                // Generar el blob UNA SOLA VEZ y guardarlo globalmente
                pdfDocGenerator.getBlob((blob) => {
                    globalPdfBlob = blob;

                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }

                    if (isPreview || !urlParams.has('preview')) {
                        showPDFPreview(globalFilename);
                    } else {
                        showDownloadModal(globalFilename);
                    }
                });

            } catch (error) {
                console.error('Error al generar PDF:', error);
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
                alert('Error al generar el PDF. La ventana se cerrará.');
                setTimeout(() => window.close(), 50);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => generatePDFNotificacion(), 100);
        });
    </script>

</body>

</html>