
btnPDF.addEventListener('click', () => {

    function formatNumber(num) {
        // Limpiar el valor de entrada
        let cleanNum = String(num).replace(/[^\d.-]/g, '');

        // Si está vacío o es inválido, retornar 0.00
        if (!cleanNum || cleanNum === '' || isNaN(cleanNum)) {
            return '0.00';
        }

        return Number(cleanNum).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    const logo = "data:image/png;base64,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";
    const nombre = document.getElementById('nombres').value || '';
    const dni = document.getElementById('documento').value || '';
    const telefono = document.getElementById('telprimario').value || '';
    const direccion = document.getElementById('direccion').value || '';

    const descripcionVehiculo = document.getElementById('descripcion').value || '';
    // const descripcionVehiculoFormateada = descripcionVehiculo.split(' ').join('/');
    const precioDolar = formatNumber(document.getElementById('valor').value || '0.00');
    const precioSoles = formatNumber(document.getElementById('inputValorConvertido').value || '0.00');
    const montoFinanciar = formatNumber(document.getElementById('inputValorFinanciar').value || '0.00');
    const tasaAnual = document.getElementById('tasaAnual').value || '0.00';
    const tipoCambio = formatNumber(document.getElementById('tipoCambio').value || '0.00');
    const inicial = formatNumber(document.getElementById('inicial').value || '0.00');
    const cuotaMensual = formatNumber(document.getElementById('cuotaMensual').value || '0.00');
    const numCuotas = document.getElementById('numcuotas').value || '0';
    const cuotaDiaria = formatNumber((parseFloat(cuotaMensual.replace(/,/g, '') || 0) / 30));

    const tabla = $('#tablaCronograma').DataTable();
    const data = tabla.rows({ search: 'applied' }).data().toArray();

    const headers = [
        { text: 'ITEM', style: 'tableHeader', alignment: 'center' },
        { text: 'FECHA DE PAGO', style: 'tableHeader', alignment: 'center' },
        { text: 'INTERÉS DEL PERIODO', style: 'tableHeader', alignment: 'center' },
        { text: 'ABONO A CAPITAL', style: 'tableHeader', alignment: 'center' },
        { text: 'VALOR CUOTA', style: 'tableHeader', alignment: 'center' },
        { text: 'SALDO CAPITAL', style: 'tableHeader', alignment: 'center' }
    ];

    const body = [headers];

    data.forEach(row => {
        const cleanCells = Array.from(row).map((cell, index) => {
            const match = cell.toString().match(/S\/\s*([\d.,]+)/);
            let text = cell;
            if (match) {
                let numberValue = match[1].replace(/,/g, '');
                text = `S/ ${formatNumber(numberValue)}`;
            }

            if (index === 0 || index === 1) {
                return { text: text, alignment: 'center' };
            } else if (index >= 2 && index <= 5) {
                return { text: text, alignment: 'right' };
            }
            return text;
        });
        body.push(cleanCells);
    });

    const totalInteres = document.getElementById('totalInteres').innerText.replace(/,/g, '');
    const totalAbono = document.getElementById('totalAbono').innerText.replace(/,/g, '');
    const totalCuota = document.getElementById('totalCuota').innerText.replace(/,/g, '');

    body.push([
        { text: 'TOTAL', colSpan: 2, alignment: 'center', bold: true, fillColor: '#fce300' }, {},
        { text: `S/ ${formatNumber(totalInteres)}`, bold: true, fillColor: '#fce300', alignment: 'right' },
        { text: `S/ ${formatNumber(totalAbono)}`, bold: true, fillColor: '#fce300', alignment: 'right' },
        { text: `S/ ${formatNumber(totalCuota)}`, bold: true, fillColor: '#fce300', alignment: 'right' }, ''
    ]);
    const documento = {
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [40, 25, 25, 25],
        defaultStyle: {
            fontSize: 7.2,
        },
        content: [
            {
                columns: [
                    {
                        image: logo,
                        width: 80,
                        alignment: 'left'
                    },
                    {
                        stack: [
                            { text: 'YONDA & GRUPO HUARACA E.I.R.L', fontSize: 12, bold: true, color: '#2c3e50'},
                            { text: 'RUC: 20609396866', fontSize: 10, margin: [0, 2, 0, 0],bold:true },
            
                        ],
                        alignment: 'right',
                        margin: [10, 0, 0, 0]
                    }
                ],
                margin: [0, 0, 0, 10]
            },
            {
                text: 'CRONOGRAMA',
                style: 'subheader',
                alignment: 'center',
                margin: [0, 0, 0, 10], // izquierda/arriba/derecha/abajo
                decoration: 'underline',
                fontSize: 14,
                bold: true
            },
            {
                style: 'tableEmpresaCliente',
                table: {
                    widths: ['30%', '70%'],
                    body: [
                        [{ text: 'DATOS DEL CLIENTE', colSpan: 2, bold: true, fillColor: '#f1f8ff', alignment: 'center' }, {}],
                        [{ text: 'Nombre Completo', bold: true }, nombre],
                        [{ text: 'DNI', bold: true }, dni],
                        [{ text: 'Teléfono', bold: true }, telefono],
                        [{ text: 'Dirección', bold: true }, direccion]
                    ]
                },
                margin: [0, 0, 0, 5]
            },
            {
                style: 'tableResumen',
                table: {
                    widths: ['25%', '25%', '25%', '25%'],
                    body: [
                        [{ text: 'DESCRIPCIÓN', bold: true, colSpan: 4, alignment: 'center', fillColor: '#d4edda' }, {}, {}, {}],
                        [{ text: 'Vehículo', bold: true, fillColor: '#d1ecf1' }, { text: descripcionVehiculo, bold: true, colSpan: 3, fillColor: '#d1ecf1' }, {}, {}],
                        [{ text: 'PRECIO EN DÓLAR', bold: true }, { text: `$ ${precioDolar}`, alignment: 'right' }, { text: 'TIPO DE CAMBIO', bold: true }, { text: `S/ ${tipoCambio}`, alignment: 'right' }],
                        [{ text: 'PRECIO EN SOLES', bold: true }, { text: `S/ ${precioSoles}`, alignment: 'right' }, { text: 'INICIAL', bold: true, fillColor: '#fbe23b' }, { text: `S/ ${inicial}`, fillColor: '#fbe23b', alignment: 'right' }],
                        [{ text: 'MONTO A FINANCIAR', bold: true }, { text: `S/ ${montoFinanciar}`, alignment: 'right' }, { text: 'CUOTA', bold: true, fillColor: '#b7e4a4' }, { text: `S/ ${cuotaMensual}`, fillColor: '#b7e4a4', alignment: 'right' }],
                        [{ text: 'TASA ANUAL', bold: true }, { text: `${tasaAnual}%`, alignment: 'right' }, { text: 'N° DE CUOTAS', bold: true }, { text: numCuotas, alignment: 'right' }],
                        [{ text: 'TASA MENSUAL', bold: true }, { text: `${(parseFloat(tasaAnual) / 12).toFixed(2)}%`, alignment: 'right' }, { text: 'CUOTA DIARIA', bold: true }, { text: `S/ ${cuotaDiaria}`, alignment: 'right' }]
                    ]
                },
                margin: [0, 0, 0, 5]
            },
            {
                style: 'tableCronograma',
                table: {
                    headerRows: 1,
                    widths: [30, '*', '*', '*', '*', '*'],
                    body: body
                },
                layout: {
                    fillColor: function (rowIndex) {
                        return rowIndex === 0 ? '#e0e0e0' : null;
                    }
                    
                }
            }
        ],
        styles: {
            subheader: { bold: true, fontSize: 10 },
            tableEmpresaCliente: { margin: [0, 0, 0, 5] },
            tableResumen: { margin: [0, 5, 0, 5] },
            tableCronograma: { margin: [0, 5, 0, 0] }
        }
    };


    pdfMake.createPdf(documento).open();
});
