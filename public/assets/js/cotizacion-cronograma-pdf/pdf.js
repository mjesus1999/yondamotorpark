
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

    const logo = window.logoBase64;
    const nombre = document.getElementById('nombres').value || '';
    const dni = document.getElementById('documento').value || '';
    const telefono = document.getElementById('telprimario').value || '';
    const direccion = document.getElementById('direccion').value || '';

    const descripcionVehiculo = document.getElementById('descripcion').value || '';
    // const descripcionVehiculoFormateada = descripcionVehiculo.split(' ').join('/');
    const precioDolar = formatNumber(document.getElementById('valor').value || '0.00');
    const precioSoles = formatNumber(document.getElementById('inputValorConvertido').value || '0.00');
    const montoFinanciar = formatNumber(document.getElementById('inputValorFinanciar').value || '0.00'); // error
    const tasaAnual = document.getElementById('tasaAnual').value || '0.00'; // error
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
