
function generarReporteResumenEjecutivo(data, logo) {

    const {
        totalOCProceso,
        totalVehiculos,
        Deudaglobal,
        totalPagosGlobal,
        saldoPendienteGlobal,
        porcentajeAvanceGlobal
    } = data.resumenEjecutivo;


    const detallesOrdenes = data.detallesOrdenes;


    function formatNumber(num) {
        if (typeof num === 'string') {
            num = num.replace(/[^\d.-]/g, '');
        }
        if (!num || isNaN(num)) {
            return '0.00';
        }
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // --- TABLA DE DETALLE POR ORDEN DE COMPRA ---
    const headersDetalle = [
        { text: 'OC Identificador', style: 'tableHeader', alignment: 'center' },
        { text: 'Emisión', style: 'tableHeader', alignment: 'center' },
        { text: 'Concesionario', style: 'tableHeader', alignment: 'center' },
        { text: 'Ubicación', style: 'tableHeader', alignment: 'center' },
        { text: 'Total OC', style: 'tableHeader', alignment: 'center' },
        { text: 'Pagado', style: 'tableHeader', alignment: 'center' },
        { text: 'Saldo', style: 'tableHeader', alignment: 'center' },
        { text: '% Avance', style: 'tableHeader', alignment: 'center' },
        { text: 'Cant. Vehículos', style: 'tableHeader', alignment: 'left' }
    ];

    const bodyDetalle = [headersDetalle];

    if (detallesOrdenes) {
        detallesOrdenes.forEach(oc => {
            bodyDetalle.push([
                { text: oc.OCIdentificador },
                { text: `${new Date(oc.emision).toLocaleDateString('es-ES')}`, alignment: 'center' },
                { text: oc.Concesionario },
                { text: oc.ubicacionConcesionario },
                { text: `$${formatNumber(oc.totalOC)}`, alignment: 'right' },
                { text: `$${formatNumber(oc.pagado)}`, alignment: 'right' },
                { text: `$${formatNumber(oc.saldo)}`, alignment: 'right' },
                { text: `${formatNumber(oc.avancePorcentaje)}%`, alignment: 'center' },
                { text: oc.totalVehiculos, alignment: 'left' }
            ]);
        });
    } else {
        console.error("Error: 'detalleOrdenes' no se encontró o no es un array.");
        bodyDetalle.push([{ text: 'No se encontraron datos detallados.', colSpan: 8, alignment: 'center' }]);
    }

    //  Estructura del documento PDF
    const documento = {
        pageSize: 'A4',
        pageOrientation: 'portait',
        pageMargins: [40, 25, 25, 25],
        defaultStyle: { fontSize: 7.2 },
        content: [
            {
                columns: [
                    { image: logo, width: 80, alignment: 'left', margin: [0, 0, 0, 0] },
                    {
                        stack: [
                            { text: 'YONDA & GRUPO HUARACA E.I.R.L', fontSize: 12, bold: true, color: '#2c3e50', alignment: 'right' },
                            { text: 'RUC: 20609396866', fontSize: 10, bold: true, alignment: 'right' },

                        ],
                        margin: [10, 0, 0, 0]
                    }
                ],
                margin: [0, 0, 0, 20]
            },
            {
                text: 'Resumen Ejecutivo de Órdenes en Proceso',
                style: 'header',
                bold: true,
                fontSize: 13,
                alignment: 'center',
                margin: [0, 0, 0, 20]
            },
            {
                text: 'Totales Globales',
                decoration: 'underline',
                fontSize: 12,
                style: 'subheader',
                margin: [0, 0, 0, 10]
            },
            {
                // --- PRIMERA TABLA: RESUMEN EJECUTIVO GENERAL ---
                table: {

                    widths: ['*', 'auto', '*'],
                    body: [
                        [
                            { text: 'Métricas', style: 'tableHeader' },
                            { text: 'Valor', style: 'tableHeader' },
                            { text: 'Descripción', style: 'tableHeader' }
                        ],
                        ['Total de OCs', `${totalOCProceso}`, 'Órdenes de compra en proceso'],
                        ['Total de Vehículos', `${totalVehiculos}`, 'Vehículos en todas las órdenes'],
                        ['Deuda Global', `$${formatNumber(Deudaglobal)}`, 'Monto total adeudado en todas las OCs'],
                        ['Total Pagos Global', `$${formatNumber(totalPagosGlobal)}`, 'Suma de todos los pagos realizados'],
                        ['Saldo Pendiente Global', `$${formatNumber(saldoPendienteGlobal)}`, 'Suma de los saldos por pagar'],
                        ['Porcentaje de Avance Global', `${formatNumber(porcentajeAvanceGlobal)}%`, 'Avance promedio de todas las OCs']
                    ]
                },
                layout: 'lightHorizontalLines',
                margin: [0, 0, 0, 30]
            },
            // --- SEGUNDA TABLA: DETALLE DE TODAS LAS ÓRDENES ---
            {
                text: 'Detalle Completo de Órdenes',
                decoration: 'underline',
                fontSize: 12,
                style: 'subheader',
                margin: [0, 20, 0, 10]
            },
            {
                table: {
                    // Anchos de columna optimizados para ser compactos
                    widths: ['*', '*', 'auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    body: bodyDetalle
                },
                layout: 'lightHorizontalLines'
            }
        ],
        styles: {
            header: { bold: true },
            subheader: { bold: true },
            tableHeader: { bold: true, color: 'black' }
        }
    };

    return documento;
}


async function fetchAndGenerateReport() {
    try {
        const logoBase64 = window.logoBase64;

        const response = await fetch('/api/ocproceso/reporte');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        const pdfDoc = generarReporteResumenEjecutivo(data, logoBase64);

        pdfMake.createPdf(pdfDoc).open();

    } catch (error) {
        console.error("Error al obtener datos o generar el reporte:", error);
    }
}

if (document.getElementById('btn-exportar-pdf')) {

    document.getElementById('btn-exportar-pdf').addEventListener('click', fetchAndGenerateReport);

}