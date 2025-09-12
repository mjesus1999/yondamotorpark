document.getElementById('btn-generar-pdf').addEventListener('click', () => {
    const dataPDF = document.querySelectorAll("#tabla-pagos-datos tbody tr");
    if (dataPDF.length === 0 || (dataPDF.length === 1 && dataPDF[0].querySelectorAll("td").length === 1)) {
        alert("No hay datos en la tabla para exportar el PDF.");
        return;
    }

    function formatNumber(num) {
        let cleanNum = String(num).replace(/[^\d.-]/g, '');
        if (!cleanNum || cleanNum === '' || isNaN(cleanNum)) {
            return '';
        }
        return Number(cleanNum).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    const logo = window.logoBase64;
    const date = new Date().toLocaleDateString();
    const numeroIdentificadorOC = document.getElementById('numeroIdentificadorOC') ? document.getElementById('numeroIdentificadorOC').textContent.trim() : 'N/A';
    const concesionario = document.getElementById('concesionario') ? document.getElementById('concesionario').textContent.trim() : 'N/A';

    // ----------------------------
    // Tabla Vehículos
    // ----------------------------
    const headersVehiculos = [
        { text: 'Marca / Modelo', style: 'tableHeader'},
        { text: 'Características', style: 'tableHeader', alignment: 'left' },
        { text: 'Chasis', style: 'tableHeader' },
        { text: 'Placa', style: 'tableHeader' },
        { text: 'P. Rotativa', style: 'tableHeader' },
        { text: 'S. Motor', style: 'tableHeader' },
        { text: 'Año', style: 'tableHeader' },
        { text: 'Estado', style: 'tableHeader' }
    ];
    const bodyVehiculos = [headersVehiculos];

    document.querySelectorAll('#tabla-vehiculos tbody tr').forEach(row => {
        const cols = row.querySelectorAll('td');

        const marcaModelo = cols[0].textContent.trim();
        const caracteristicas = [
            cols[1].textContent.trim(),
            cols[2].textContent.trim(),
            cols[3].textContent.trim(),
            cols[4].textContent.trim()
        ].filter(v => v !== '').join('/');

        const chasis = cols[5].textContent.trim();
        const placa = cols[6].textContent.trim();
        const placaRotativa = cols[7].textContent.trim();
        const serieMotor = cols[8].textContent.trim();
        const anio = cols[9].textContent.trim();
        const estado = cols[10].textContent.trim();

        const rowData = [
            { text: marcaModelo },
            { text: caracteristicas, alignment: 'left' },
            { text: chasis },
            { text: placa },
            { text: placaRotativa },
            { text: serieMotor },
            { text: anio },
            { text: estado }
        ];
        bodyVehiculos.push(rowData);
    });

    // ----------------------------
    // Tabla Pagos + Observaciones
    // ----------------------------
    const headersPagos = [
        { text: '#', style: 'tableHeader' },
        { text: 'Fecha Pago', style: 'tableHeader' },
        { text: 'Entidad', style: 'tableHeader' },
        { text: 'N° Transacción', style: 'tableHeader' },
        { text: 'Moneda', style: 'tableHeader' },
        { text: 'Monto Pagado', style: 'tableHeader' },
        { text: 'Restante', style: 'tableHeader' },
        { text: 'T.Cambio', style: 'tableHeader' },
        { text: 'Valor Dólares', style: 'tableHeader' },
    ];

    const bodyPagos = [headersPagos];
    const bodyObservaciones = [];

    document.querySelectorAll('#tabla-pagos-datos tbody tr').forEach(row => {
        const cols = row.querySelectorAll('td');
        const rowData = new Array(9).fill({ text: '' });

        for (let i = 0; i < Math.min(cols.length, 9); i++) {
            const td = cols[i];
            let textContent = td.textContent.trim();
            if ([5, 6, 7, 8].includes(i)) {
                let simbolo = '';
                if (textContent.startsWith('$')) simbolo = '$';
                else if (textContent.startsWith('S/')) simbolo = 'S/';
                const cleanValue = textContent.replace(/[^\d.-]/g, '');
                const formattedValue = formatNumber(cleanValue);
                rowData[i] = {
                    text: simbolo ? `${simbolo}  ${formattedValue}` : formattedValue,
                    alignment: 'right'
                }

            } else {
                rowData[i] = { text: textContent };
            }
        }
        bodyPagos.push(rowData);

        // Observaciones
        const obsIndex = 9;
        const obsTd = cols[obsIndex];
        const btnDetalle = obsTd.querySelector('button.ver-detalle-observacion');
        if (btnDetalle && btnDetalle.getAttribute('data-observacion')) {
            const numeroPago = cols[0].textContent.trim();
            const observacion = btnDetalle.getAttribute('data-observacion');
            bodyObservaciones.push([
                { text: numeroPago },
                { text: observacion, alignment: 'left' }
            ]);
        }
    });

    // Totales
    let totalAmortizado = 0;
    const totalAmortizadoEl = document.getElementById('total-pagado');
    if (totalAmortizadoEl) {
        const match = totalAmortizadoEl.textContent.match(/Total Pagado:\s*\$([\d,\.]+)/);
        if (match && match[1]) {
            totalAmortizado = parseFloat(match[1].replace(/,/g, ''));
        }
    }

    let saldoRestante = 0;
    const filasPagos = document.querySelectorAll('.table-pagos tbody tr');
    if (filasPagos.length > 0) {
        const lastRow = filasPagos[filasPagos.length - 1];
        if (lastRow && lastRow.cells && lastRow.cells.length >= 7) {
            const saldoStr = lastRow.cells[6].textContent.replace(/[^\d.]/g, '');
            saldoRestante = parseFloat(saldoStr) || 0;
        }
    }

    let totalDeuda = totalAmortizado + saldoRestante;
    let avancePorcentaje = totalDeuda > 0 ? (totalAmortizado / totalDeuda) * 100 : 0;

    const totalRow = [
        {
            text: `TOTAL DEUDA: $${formatNumber(totalDeuda)} | AVANCE: ${avancePorcentaje.toFixed(2)}% | TOTAL PAGADO: $${formatNumber(totalAmortizado)} | SALDO: $${formatNumber(saldoRestante)} | FECHA: ${date}`,
            colSpan: 9,
            alignment: 'center',
            bold: true,
            fillColor: '#fff59d',
            margin: [0, 5, 0, 5]
        }, {}, {}, {}, {}, {}, {}, {}, {}
    ];
    bodyPagos.push(totalRow);

    const documento = {
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [40, 25, 25, 25],
        defaultStyle: { fontSize: 7.2, alignment: 'center' },

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
                columns: [
                    { text: `OC-IDENTIFICADOR: #${numeroIdentificadorOC}`, style: 'subheader', bold: true, fontSize: 8, alignment: 'left' },
                    { text: `CONCESIONARIO: ${concesionario}`, style: 'subheader', bold: true, fontSize: 8, alignment: 'right' }
                ],
                margin: [0, 10, 0, 20]
            },
            {
                style: 'tableVehiculos',
                table: {
                    headerRows: 1,
                    widths: ['auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    body: bodyVehiculos
                },
                layout: 'lightHorizontalLines'
            },
            { text: 'PAGOS REALIZADOS', style: 'subheader', alignment: 'center', margin: [0, 20, 0, 10], decoration: 'underline', bold: true, fontSize: 10 },
            {
                style: 'tablePagos',
                table: {
                    headerRows: 1,
                    widths: ['auto', 'auto', '*', '*', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    body: bodyPagos
                },
                layout: 'lightHorizontalLines'
            },
            // Lógica condicional para mostrar u ocultar la sección de observaciones
            ...(bodyObservaciones.length > 0 ? [
                { text: 'OBSERVACIONES DETALLADAS', style: 'subheader', alignment: 'center', margin: [0, 20, 0, 10], decoration: 'underline', bold: true, fontSize: 10 },
                {
                    style: 'tableObservaciones',
                    table: {
                        headerRows: 1,
                        widths: ['auto', '*'],
                        body: [
                            [{ text: '# Pago', style: 'tableHeader' }, { text: 'Observación', style: 'tableHeader' }],
                            ...bodyObservaciones
                        ]
                    },
                    layout: 'lightHorizontalLines'
                }
            ] : []),
        ],
        styles: {
            subheader: { bold: true },
            tableVehiculos: { margin: [0, 5, 0, 5] },
            tablePagos: { margin: [0, 5, 0, 5] },
            tableObservaciones: { margin: [0, 5, 0, 5] },
            tableHeader: { bold: true, color: '#333333', alignment: 'center' }
        }
    };

    pdfMake.createPdf(documento).open();
});