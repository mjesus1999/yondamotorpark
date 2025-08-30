<?php
// phpinfo();
include __DIR__ . '/../layout/header.php';
?>
<div class="container-fluid">

   <div class="alert alert-info mt-2" role="alert">
    <div class="row align-items-center">
        <!-- Barra de navegación responsiva -->
        <div class="col-12 col-md-6 d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Listar</li>
                </ol>
            </nav>
        </div>
        <!-- Botones responsivos -->
        <div class="col-12 col-md-6 d-flex flex-column flex-md-row justify-content-end">
            <a href="/caja/reporte/by/fecha" class="btn btn-sm btn-outline-primary mb-2 mb-md-0 w-100 w-md-auto">
                <i class="bi bi-calendar3"></i> Reporte por fecha
            </a>
            <button class="btn btn-danger btn-sm ms-2 mb-2 mb-md-0 w-100 w-md-auto" id="btn-pdf" title="Generar reporte de pagos del día">
                <i class="fa-regular fa-file-pdf"></i> Reporte diario
            </button>

            <button class="btn btn-success btn-sm ms-2 mb-2 mb-md-0 w-100 w-md-auto" id="btn-excel" title="Generar reporte de pagos del día en Excel">
                <i class="fa-regular fa-file-excel"></i> Reporte diario
            </button>
        </div>
    </div>
</div>



    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- SOLO ESCRITORIO -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-contratos">
                            <thead>
                                <tr>
                                    <th><span class="text-body fw-bold badge">#</span></th>
                                    <th><span class="text-body fw-bold badge">Cliente</span></th>
                                    <th><span class="text-body fw-bold badge">Documento</span></th>
                                    <th><span class="text-body fw-bold badge">N° Documento</span></th>
                                    <th> <span class="text-body fw-bold badge">Tienda</span></th>
                                    <th><span class="text-body fw-bold badge">Vehículo</span></th>
                                    <th><span class="text-body fw-bold badge">Meses</span></th>
                                    <th><span class="text-body fw-bold badge">Cuota</span></th>
                                    <th><span class="text-body fw-bold badge">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contratos)) : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay datos para mostrar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($contratos as $contrato) : ?>
                                        <tr>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($numeroFila++) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['cliente']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['documento']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['ndocumento']) ?></span></td>
                                            <td><span class="badge fw-bold text-body"><?= htmlspecialchars($contrato['tienda']) ?></span></td>
                                            <td><span class="badge fw-bold text-body"><?= htmlspecialchars($contrato['vehiculo']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['meses']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['cuota']) ?></span></td>
                                            <td>
                                                <a href="/caja/cronograma/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info"></i>
                                                </a>
                                                <a href="/caja/historial/pagos/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver historial de pagos">
                                                    <i class="bi bi-clock-history fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                    <!-- SOLO MÓVIL (ACORDEÓN) -->
                    <div class="d-block d-md-none">
                        <?php if (!empty($contratos)) : ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($contratos as $contrato): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapse<?= $contrato['idcontrato'] ?>" style="cursor:pointer;">
                                        <span><i class="bi bi-person-circle me-2 text-primary  fw-bold"></i><?= htmlspecialchars($contrato['cliente']) ?></span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div id="collapse<?= $contrato['idcontrato'] ?>" class="collapse">
                                        <div class="card-body">
                                            <p><strong>#:</strong> <?= $numeroFila++ ?></p>
                                            <p><strong>Documento:</strong> <?= htmlspecialchars($contrato['documento']) ?></p>
                                            <p><strong>N° Documento:</strong> <?= htmlspecialchars($contrato['ndocumento']) ?></p>
                                            <p><strong>Tienda:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['tienda']) ?></span></p>
                                            <p><strong>Vehículo:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['vehiculo']) ?></span></p>
                                            <p><strong>Meses:</strong> <?= htmlspecialchars($contrato['meses']) ?></p>
                                            <p><strong>Cuota:</strong> <?= htmlspecialchars($contrato['cuota']) ?></p>
                                            <p>
                                                <strong>Acciones:</strong><br>
                                                <a href="/caja/cronograma/<?= $contrato['idcontrato'] ?>" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info me-2"></i>
                                                </a>
                                                <a href="/caja/historial/pagos/<?= $contrato['idcontrato'] ?>" title="Ver historial de pagos">
                                                    <i class="bi bi-clock-history fs-5"></i>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center">No hay datos para mostrar.</div>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js" defer></script>

<script>
    const btnPdf = document.getElementById('btn-pdf');
    const btnExcel = document.querySelector('#btn-excel');
    document.addEventListener('DOMContentLoaded', async () => {

         $('#tabla-contratos').DataTable({
             responsive: true,
             language: {
                 emptyTable: "No hay datos disponibles en la tabla",
                 info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                 infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                 infoFiltered: "(filtrado de _MAX_ entradas totales)",
                 lengthMenu: "Mostrar _MENU_ registros",
                 loadingRecords: "Cargando...",
                 processing: "Procesando...",
                 search: "Buscar:",
                 zeroRecords: "No se encontraron registros coincidentes",
                 paginate: {
                     first: "Primero",
                     last: "Último",
                     next: "Siguiente",
                     previous: "Anterior"
                 },
                 aria: {
                     sortAscending: ": activar para ordenar la columna ascendente",
                     sortDescending: ": activar para ordenar la columna descendente"
                 }
             }
         });


        if (btnPdf) {
            btnPdf.addEventListener('click', () => {
                generarReportePDFConJsPDF();
            });
        }

        if (btnExcel) {
            btnExcel.addEventListener('click', () => {
                generarReporteExcel();
            });
        }

        // FUNCION PARA CREAR EL EXCEL.
        async function generarReporteExcel() {
            const textoOriginal = btnExcel.innerHTML;
            btnExcel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando Excel...';
            btnExcel.disabled = true;

            try {
                const response = await fetch('/api/reporte/hoy');

                if (!response.ok) {
                    throw new Error('Error al obtener los datos del reporte');
                }

                const data = await response.json();

                if (!data.success || !data.data) {
                    throw new Error('No se pudieron obtener los datos del reporte');
                }


                const transaccionesFlattened = [];
                let contador = 1;
                data.data.forEach(item => {
                    item.transacciones.forEach(transaccion => {
                        transaccionesFlattened.push({
                            '#': contador++,
                            'Medio de Pago': transaccion.metodo_pago,
                            'Fecha': new Date(transaccion.fecha).toLocaleDateString('es-PE'),
                            'N° Operación': transaccion.numero_operacion || 'N/A',
                            'Entidad Bancaria': transaccion.entidad_bancaria || 'N/A',
                            'N° Cuenta': transaccion.numero_cuenta || 'N/A',
                            'Monto (S/)': parseFloat(transaccion.monto).toFixed(2)
                        });
                    });
                });

                // Crear una hoja de cálculo a partir del array de objetos
                const worksheet = XLSX.utils.json_to_sheet(transaccionesFlattened);

                // Crear un nuevo libro de trabajo
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte de Pagos');

                // Generar y descargar el archivo
                const fechaHoy = new Date().toLocaleDateString('es-PE').replace(/\//g, '-');
                XLSX.writeFile(workbook, `reporte-pagos-diario-${fechaHoy}.xlsx`);

                mostrarNotificacion('Reporte Excel generado y descargado correctamente', 'success');

            } catch (error) {
                console.error('Error al generar el reporte de Excel:', error);
                mostrarNotificacion('Error al generar el Excel: ' + error.message, 'error');
            } finally {
                setTimeout(() => {
                    btnExcel.innerHTML = textoOriginal;
                    btnExcel.disabled = false;
                }, 2000);
            }
        }


        async function generarReportePDFConJsPDF() {
            const btnPdf = document.getElementById('btn-pdf');
            const textoOriginal = btnPdf.innerHTML;
            btnPdf.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
            btnPdf.disabled = true;

            try {

                const response = await fetch('/api/reporte/hoy');

                if (!response.ok) {
                    throw new Error('Error al obtener los datos del reporte');
                }

                const data = await response.json();

                if (!data.success || !data.data) {
                    throw new Error('No se pudieron obtener los datos del reporte');
                }

                await generarPDFConDatos(data);

                mostrarNotificacion('PDF generado y descargado correctamente', 'success');

            } catch (error) {
                console.error('Error al generar PDF:', error);
                mostrarNotificacion('Error al generar el PDF: ' + error.message, 'error');
            } finally {
                setTimeout(() => {
                    btnPdf.innerHTML = textoOriginal;
                    btnPdf.disabled = false;
                }, 2000);
            }
        }

        async function generarPDFConDatos(datosReporte) {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');

            // Configuración inicial
            let yPos = 20;
            const fechaHoy = new Date().toLocaleDateString('es-PE');
            const horaGeneracion = new Date().toLocaleTimeString('es-PE');
            const margen = 20;
            const altoPagina = doc.internal.pageSize.height;

            const totalGeneral = datosReporte.total_general || 0;
            const datosTransacciones = datosReporte.data || [];

            // Colores
            const azulPrincipal = [0, 123, 255];
            const verdePrincipal = [40, 167, 69];
            const grisTexto = [108, 117, 125];

            // HEADER
            doc.setFillColor(...azulPrincipal);
            doc.rect(0, 0, 210, 35, 'F');

            doc.setTextColor(255, 255, 255);
            doc.setFontSize(24);
            doc.setFont('helvetica', 'bold');
            doc.text('REPORTE DIARIO DE PAGOS', 105, 15, {
                align: 'center'
            });

            doc.setFontSize(12);
            doc.setFont('helvetica', 'normal');
            doc.text(`Generado el ${fechaHoy} a las ${horaGeneracion}`, 105, 22, {
                align: 'center'
            });
            doc.text('Sistema de Gestión de Caja', 105, 28, {
                align: 'center'
            });

            yPos = 45;

            // TOTAL RECAUDADO
            doc.setFillColor(...azulPrincipal);
            doc.rect(20, yPos, 170, 25, 'F');

            doc.setTextColor(255, 255, 255);
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('TOTAL RECAUDADO HOY', 105, yPos + 8, {
                align: 'center'
            });

            doc.setFontSize(20);
            doc.text(`S/ ${formatearMoneda(totalGeneral)}`, 105, yPos + 16, {
                align: 'center'
            });

            const totalTransacciones = calcularTotalTransacciones(datosTransacciones);
            doc.setFontSize(12);
            doc.text(`Total de transacciones: ${totalTransacciones}`, 105, yPos + 22, {
                align: 'center'
            });

            yPos += 40;

            // RESUMEN EJECUTIVO
            doc.setTextColor(...grisTexto);
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('RESUMEN EJECUTIVO', 20, yPos);

            doc.setDrawColor(...verdePrincipal);
            doc.setLineWidth(2);
            doc.line(20, yPos + 2, 190, yPos + 2);

            yPos += 10;

            // Estadísticas por método de pago
            let xPos = 23;
            const anchoCard = 36.2;
            const altoCard = 25;

            for (const item of datosTransacciones) {
                if (item.metodo_pago && item.transacciones) {
                    const metodo = item.metodo_pago;
                    const cantidad = item.transacciones.length;
                    const subtotal = item.subtotal || 0;
                    const porcentaje = totalGeneral > 0 ? ((subtotal / totalGeneral) * 100).toFixed(1) : 0;

                    doc.setFillColor(248, 249, 250);
                    doc.rect(xPos, yPos, anchoCard, altoCard, 'F');

                    doc.setDrawColor(222, 226, 230);
                    doc.setLineWidth(0.5);
                    doc.rect(xPos, yPos, anchoCard, altoCard, 'S');

                    doc.setTextColor(...azulPrincipal);
                    doc.setFontSize(18);
                    doc.setFont('helvetica', 'bold');
                    doc.text(cantidad.toString(), xPos + anchoCard / 2, yPos + 8, {
                        align: 'center'
                    });

                    doc.setTextColor(...grisTexto);
                    doc.setFontSize(10);
                    doc.setFont('helvetica', 'normal');
                    doc.text(metodo, xPos + anchoCard / 2, yPos + 13, {
                        align: 'center'
                    });

                    doc.setTextColor(...verdePrincipal);
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'bold');
                    doc.text(`S/ ${formatearMoneda(subtotal)}`, xPos + anchoCard / 2, yPos + 18, {
                        align: 'center'
                    });
                    doc.text(`(${porcentaje}%)`, xPos + anchoCard / 2, yPos + 22, {
                        align: 'center'
                    });

                    xPos += anchoCard + 5;
                    if (xPos > 150) {
                        xPos = 25;
                        yPos += altoCard + 5;
                    }
                }
            }

            yPos += 30;

            // DETALLES POR MÉTODO DE PAGO
            for (const item of datosTransacciones) {
                if (item.metodo_pago && item.transacciones) {
                    const metodo = item.metodo_pago;
                    const transacciones = item.transacciones;
                    const subtotal = item.subtotal || 0;


                    const rowsPorPagina = Math.floor((altoPagina - yPos - 30) / (6)); // 6 es la altura aproximada de una fila
                    const espacioRequerido = 12 + 15 + (transacciones.length * 6); // Altura del header + espacio + altura de todas las filas


                    if (yPos + espacioRequerido > altoPagina - 30 || transacciones.length > rowsPorPagina) {
                        doc.addPage();
                        yPos = 40;
                    }

                    // Header del método de pago
                    const colorMetodo = obtenerColorMetodo(metodo);
                    doc.setFillColor(...colorMetodo);
                    doc.rect(20, yPos, 170, 12, 'F');

                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(14);
                    doc.setFont('helvetica', 'bold');
                    doc.text(`${obtenerIconoMetodoTexto(metodo)} ${metodo.toUpperCase()}`, 25, yPos + 8);
                    doc.text(`S/ ${formatearMoneda(subtotal)}`, 185, yPos + 8, {
                        align: 'right'
                    });

                    // Se aumenta la posición para dibujar la tabla
                    yPos += 15;

                    // Tabla de transacciones
                    if (transacciones && transacciones.length > 0) {
                        const headers = ['ID', 'Fecha', 'N° Operación', 'Entidad', 'N° Cuenta', 'Monto'];
                        const data = transacciones.map(t => [
                            `#${t.idpago}`,
                            new Date(t.fecha).toLocaleDateString('es-PE'),
                            t.numero_operacion || 'N/A',
                            t.entidad_bancaria || 'N/A',
                            t.numero_cuenta || 'N/A',
                            `S/ ${formatearMoneda(t.monto)}`
                        ]);

                        // Agregar fila de subtotal
                        data.push(['', '', '', '', 'SUBTOTAL', `S/ ${formatearMoneda(subtotal)}`]);

                        doc.autoTable({
                            head: [headers],
                            body: data,
                            startY: yPos,
                            margin: {
                                left: 20,
                                right: 20
                            },
                            styles: {
                                fontSize: 8,
                                cellPadding: 3,
                            },
                            headStyles: {
                                fillColor: [248, 249, 250],
                                textColor: [73, 80, 87],
                                fontStyle: 'bold',
                            },
                            bodyStyles: {
                                textColor: [33, 37, 41],
                            },
                            alternateRowStyles: {
                                fillColor: [248, 249, 250],
                            },
                            columnStyles: {
                                5: {
                                    halign: 'right',
                                    fontStyle: 'bold',
                                    textColor: [40, 167, 69]
                                }
                            },
                            didParseCell: function(data) {
                                // Estilo para la fila de subtotal
                                if (data.row.index === data.table.body.length - 1) {
                                    data.cell.styles.fillColor = [227, 242, 253];
                                    data.cell.styles.fontStyle = 'bold';
                                    data.cell.styles.textColor = [25, 118, 210];
                                }
                            },
                            didDrawPage: function(data) {
                                yPos = 40;
                            }
                        });

                        // Actualiza yPos con la posición final de la tabla para el siguiente elemento
                        yPos = doc.lastAutoTable.finalY + 15;
                    } else {
                        // Si no hay transacciones
                        doc.setTextColor(...grisTexto);
                        doc.setFontSize(10);
                        doc.text('No hay transacciones registradas para este método de pago', 105, yPos + 5, {
                            align: 'center'
                        });
                        yPos += 15;
                    }
                }
            }

            // FOOTER
            const totalPages = doc.internal.getNumberOfPages();
            for (let i = 1; i <= totalPages; i++) {
                doc.setPage(i);
                // Línea superior del footer
                doc.setDrawColor(...grisTexto);
                doc.setLineWidth(0.5);
                doc.line(20, 280, 190, 280);

                doc.setTextColor(...grisTexto);
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.text('Reporte generado automáticamente', 105, 285, {
                    align: 'center'
                });
                doc.text(`© ${new Date().getFullYear()} Sistema de Gestión de Caja`, 105, 289, {
                    align: 'center'
                });
                doc.text(`Página ${i} de ${totalPages}`, 190, 289, {
                    align: 'right'
                });
            }

            // Descargar el PDF
            const nombreArchivo = `reporte-pagos-${fechaHoy.replace(/\//g, '-')}.pdf`;
            doc.save(nombreArchivo);
        }



        function obtenerColorMetodo(metodo) {
            const colores = {
                'Yape': [114, 47, 144],
                'Plin': [0, 166, 81],
                'Transferencia Bancaria': [30, 58, 138],
                'Efectivo': [5, 150, 105]
            };
            return colores[metodo] || [108, 117, 125];
        }

        function obtenerIconoMetodoTexto(metodo) {
            const iconos = {
                'Yape': '',
                'Plin': '',
                'Transferencia Bancaria': '',
                'Efectivo': ''
            };
            return iconos[metodo] || '';
        }

        function calcularTotalTransacciones(datosReporte) {
            let total = 0;
            for (const item of datosReporte) {
                if (item.transacciones && Array.isArray(item.transacciones)) {
                    total += item.transacciones.length;
                }
            }
            return total;
        }

        function formatearMoneda(monto) {
            return new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(monto) || 0);
        }

        function mostrarNotificacion(mensaje, tipo = 'info') {
            const notificacion = document.createElement('div');
            notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} position-fixed`;
            notificacion.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
            const icono = tipo === 'success' ? 'check-circle' : 'exclamation-triangle';
            notificacion.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${icono} me-2"></i>
            <span>${mensaje}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
            document.body.appendChild(notificacion);
            setTimeout(() => {
                if (notificacion.parentNode) {
                    notificacion.remove();
                }
            }, 5000);
        }

    });
</script>