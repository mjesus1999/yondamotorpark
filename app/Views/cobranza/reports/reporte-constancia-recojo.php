<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de recojo - PDF</title>
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
            return `Chincha Alta, ${day} de ${month} del ${year}`;
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

        function formatMesEspanol(numeroMes) {
            const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO',
                'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            return meses[parseInt(numeroMes) - 1] || '';
        }

        function detalleToParrafo(detalleStr) {
            if (!detalleStr) return '';

            const parts = detalleStr.split(' || ').map(s => {
                // Reemplazar "MES:X" con el nombre del mes
                let texto = s.replace(/MES:(\d+)/g, (match, num) => formatMesEspanol(num));
                // Reemplazar "MONTO:" con "DE S/ "
                texto = texto.replace(/MONTO:/g, 'DE S/ ');
                return texto;
            });

            return parts.join(', ');
        }

        function rawValueAsString(value) {
            if (value === null || value === undefined) return '0.00';
            if (typeof value === 'string') return value.trim();
            if (typeof value === 'number') return value.toFixed(2);
            return String(value);
        }

        async function getDatosReporteRecojoVehicular(idContrato) {
            try {
                const resp = await fetch(`/Cobranza/getDatosReporteRecojoVehicular?contrato=${encodeURIComponent(idContrato)}`);
                const json = await resp.json();
                if (!resp.ok || !json.success) {
                    const msg = json && json.message ? json.message : `Error HTTP ${resp.status}`;
                    throw new Error(msg);
                }
                return json.data;
            } catch (error) {
                console.error("Error al obtener los datos:", error);
                alert("Error al obtener los datos del reporte: " + (error.message || error));
                return null;
            }
        }

        function createNotificacionPDF(headerImageBase64, datos) {
            // Valores seguros
            const nombre = rawValueAsString(datos.nombre_cliente || '');
            const tipo_doc = rawValueAsString(datos.tipo_documento || '');
            const nro_doc = rawValueAsString(datos.numero_documento || '');
            const direccion = rawValueAsString(datos.direccion_completa || '');
            const telefono = rawValueAsString(datos.telefono || '');
            const marca = rawValueAsString(datos.marca || '');
            const modelo = rawValueAsString(datos.modelo || '');
            const anio = rawValueAsString(datos.vehiculo_anio || '');
            const color = (datos.color === null || datos.color === undefined || String(datos.color).trim() === '') ? 'N/A' : rawValueAsString(datos.color);
            const placa = (datos.placa === null || datos.placa === undefined || String(datos.placa).trim() === '') ? 'N/A' : rawValueAsString(datos.placa);
            const chasis = (datos.numero_chasis === null || datos.numero_chasis === undefined || String(datos.numero_chasis).trim() === '') ? 'N/A' : rawValueAsString(datos.numero_chasis);
            const motor = (datos.numero_motor === null || datos.numero_motor === undefined || String(datos.numero_motor).trim() === '') ? 'N/A' : rawValueAsString(datos.numero_motor);

            const totalDeuda = rawValueAsString(datos.total_deuda_vencida);
            const detalleParrafo = detalleToParrafo(datos.detalle_cuotas_vencidas || '');
            const fechaContrato = datos.fecha_contrato ? 
                new Date(datos.fecha_contrato).toLocaleDateString('es-PE', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric' 
                }) : '';
            const diaPago = datos.dia_pago_mensual ? Math.floor(Number(datos.dia_pago_mensual)) : '';
            /* const diaPago = rawValueAsString(datos.dia_pago_mensual || ''); */

            const parrafoIncumplimiento = [
                { text: 'Por Incumplimiento de pago ', bold: true },
                'ya que según registros de cobranza de nuestra empresa Usted adeuda, ',
                { text: detalleParrafo ? `${detalleParrafo}, ` : '', bold: true },
                { text: `SIENDO ASÍ SU DEUDA TOTAL EL MONTO DE S/ ${totalDeuda} SOLES. `, bold: true },
                { text: `Y HABIENDO USTED COMPROMETIDO SEGÚN EL CONTRATO NOTARIAL FIRMADO EL ${fechaContrato}. `, bold: true },
                { text: `TODOS LOS ${diaPago} DE CADA MES `, bold: true },
                'Y NO CUMPLIÉNDOLO CON SU CRONOGRAMA DE PAGO. ',
                { text: '(CADA NOTIFICACIÓN LLEGADA AL DOMICILIO SE HARÁ EL COBRO ADICIONAL DE S/50 SOLES).', bold: true }
            ];
            /* const parrafoIncumplimiento = [
                { text: 'Por Incumplimiento de pago ', bold: true },
                'ya que según registros de cobranza de nuestra empresa Usted adeuda, ',
                { text: detalleParrafo ? `${detalleParrafo}, ` : '', bold: true },
                { text: `SIENDO ASÍ SU DEUDA TOTAL EL MONTO DE S/ ${totalDeuda} SOLES. `, bold: true },
                'Y habiendo usted comprometido según el contrato notarial firmado. Todos los días de pago de cada mes y no cumpliéndolo con su cronograma de pago. ',
                { text: '(CADA NOTIFICACIÓN LLEGADA AL DOMICILIO SE HARÁ EL COBRO ADICIONAL DE S/50 SOLES).', bold: true }
            ]; */

            return {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [60, 80, 60, 80],

                header: function () {
                    if (headerImageBase64) {
                        return { image: headerImageBase64, width: 500, alignment: 'center', margin: [0, 20, 0, 0] };
                    }
                    return null;
                },

                footer: function () {
                    return {
                        stack: [
                            { text: ['Contacto: Área de cobranza\n', '987454555\n', 'cobranza@yondaperu.com'], style: 'contactInfo', alignment: 'right', margin: [0, 0, 40, 10] },
                            { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 525, y2: 0, lineWidth: 4, lineColor: '#ff6600' }], alignment: 'center' }
                        ]
                    };
                },

                content: [
                    { text: formatDateSpanish(), style: 'fecha', alignment: 'right', margin: [0, 0, 0, 20] },
                    { text: 'CONSTANCIA DE RECOJO VEHICULAR', style: 'titulo', alignment: 'center', margin: [0, 0, 0, 15] },

                    // DATOS CLIENTE
                    { text: [{ text: 'Señor(a): ', style: 'normal', bold: true }, { text: nombre, style: 'normal' }], margin: [0, 0, 0, 5] },
                    { text: [{ text: `${tipo_doc}: `, style: 'normal', bold: true }, { text: nro_doc, style: 'normal' }], margin: [0, 0, 0, 5] },
                    { text: [{ text: 'Dirección: ', style: 'normal', bold: true }, { text: direccion, style: 'normal' }], margin: [0, 0, 0, 5] },
                    { text: [{ text: 'Celular: ', style: 'normal', bold: true }, { text: telefono, style: 'normal' }], margin: [0, 0, 0, 10] },

                    { text: ['Mediante el presente: YHON KENNIDEY MENDOZA HUARACA. Representante General de', { text: ' YONDA & GRUPO HUARACA E.I.R.L', bold: true }, ' hace de su conocimiento', { text: ' EL RECOJO DEL VEHÍCULO CON LAS SIGUIENTES CARACTERÍSTICAS:', bold: true }], style: 'normal', alignment: 'justify', margin: [0, 0, 0, 10] },

                    // TABLA VEHÍCULO
                    {
                        table: {
                            widths: [70, 15, '*'], body: [
                                [{ text: 'Marca', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: marca, style: 'detalleValue' }],
                                [{ text: 'Modelo', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: `${modelo} ${anio}`, style: 'detalleValue' }],
                                [{ text: 'Chasis', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: chasis, style: 'detalleValue' }],
                                [{ text: 'Motor', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: motor, style: 'detalleValue' }],
                                [{ text: 'Color', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: color, style: 'detalleValue' }],
                                [{ text: 'Placa', style: 'detalleLabel' }, { text: ':', style: 'detalleSeparador' }, { text: placa, style: 'detalleValue' }]
                            ]
                        }, layout: { hLineWidth: () => 0, vLineWidth: () => 0, paddingLeft: () => 0, paddingRight: () => 0, paddingTop: () => 2, paddingBottom: () => 2 }, margin: [0, 0, 0, 15]
                    },

                    { text: parrafoIncumplimiento, style: 'normal', alignment: 'justify', margin: [0, 0, 0, 15] },
                    { text: 'A su vez se procederá a realizar la denuncia correspondiente mediante instancias legales y judiciales que amerita el caso.', style: 'normal', alignment: 'justify', margin: [0, 0, 0, 15] },

                    { text: 'Atte: Gerencia', bold: true, style: 'normal', alignment: 'left', margin: [0, 0, 0, 40] },
                    {
                        table: {
                            widths: ['*'], body: [
                                [{ canvas: [{ type: 'line', x1: 0, y1: 0, x2: 200, y2: 0, lineWidth: 1, lineColor: 'black' }], alignment: 'center', border: [false, false, false, false], margin: [0, 0, 0, 5] }],
                                [{ text: 'YHON MENDOZA HUARACA', style: 'firma', alignment: 'center', border: [false, false, false, false], margin: [0, 0, 0, 2] }],
                                [{ text: 'GERENTE GENERAL', style: 'cargo', alignment: 'center', border: [false, false, false, false] }]
                            ]
                        }, layout: 'noBorders', margin: [0, 0, 0, 0]
                    }
                ],

                styles: {
                    fecha: { fontSize: 12, color: '#333' },
                    titulo: { fontSize: 15, bold: true, color: '#000' },
                    normal: { fontSize: 12, lineHeight: 1.3, color: '#000' },
                    detalleLabel: { fontSize: 12, bold: true, color: '#000' },
                    detalleSeparador: { fontSize: 12, bold: true, color: '#000', alignment: 'center' },
                    detalleValue: { fontSize: 12, bold: true, color: '#000' },
                    firma: { fontSize: 12, bold: true, color: '#000' },
                    cargo: { fontSize: 12, bold: true, color: '#000' },
                    contactInfo: { fontSize: 12, color: '#333333', lineHeight: 1.2 }
                }
            };
        }

        async function generatePDFNotificacion() {
            const loadingIndicator = document.getElementById('loading-indicator');
            try {
                const idContrato = new URLSearchParams(window.location.search).get('contrato');
                if (!idContrato) throw new Error("Falta el parámetro 'contrato' en la URL. Ej: ?contrato=8");

                const datos = await getDatosReporteRecojoVehicular(idContrato);
                if (!datos) throw new Error("No se encontraron datos para el contrato especificado.");

                const headerImageBase64 = await convertImageToBase64('/assets/images/logos/cabecera-yondaa.png').catch(() => null);

                const docDefinition = createNotificacionPDF(headerImageBase64, datos);
                const pdfDocGenerator = pdfMake.createPdf(docDefinition);

                const safeName = (datos.nombre_cliente || 'cliente').replace(/\s+/g, '-').replace(/[^a-zA-Z0-9\-]/g, '').toLowerCase();
                globalFilename = `constancia-recojo-${safeName}-${idContrato}.pdf`;

                pdfDocGenerator.getBlob((blob) => {
                    globalPdfBlob = blob;
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (isPreview || !urlParams.has('preview')) showPDFPreview(globalFilename);
                    else showDownloadModal(globalFilename);
                });

            } catch (error) {
                console.error('Error al generar PDF:', error);
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                alert('Error al generar el PDF: ' + (error.message || error));
                setTimeout(() => window.close(), 50);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => generatePDFNotificacion(), 100);
        });
    </script>

</body>

</html>