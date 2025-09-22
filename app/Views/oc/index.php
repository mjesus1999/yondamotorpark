<?php include __DIR__ . '/../layout/header.php'; ?>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Ordenes de compra</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/oc/create" class="btn btn-outline-primary btn-sm">Registrar</a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <?php $estadoActual = $estado ?? ''; ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="btn-group m-1 mb-2" id="botones-filtro">
                    <a href="/oc/listar/emitido"
                        class="btn btn-sm <?= $estadoActual === 'emitido' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        Emitido
                    </a>

                    <a href="/oc/listar/proceso"
                        class="btn btn-sm <?= $estadoActual === 'proceso' ? 'btn-warning' : 'btn-outline-warning' ?>">
                        Proceso
                    </a>

                    <a href="/oc/listar/pagado"
                        class="btn btn-sm <?= $estadoActual === 'pagado' ? 'btn-success' : 'btn-outline-success' ?>">
                        Pagado
                    </a>

                    <a href="/oc/listar/anulado"
                        class="btn btn-sm <?= $estadoActual === 'anulado' ? 'btn-danger' : 'btn-outline-danger' ?>">
                        Anulado
                    </a>
                </div>

                <div class="btn-group m-1 ms-auto">
                    <?php if ($estadoActual === 'proceso'): ?>
                        <button title="Exportar a PDF" class="btn btn-sm btn-outline-danger" id="btn-exportar-pdf">
                            <i class="bi bi-filetype-pdf"></i> PDF
                        </button>
                        <button title="Exportar a EXCEL" class="btn btn-sm btn-outline-success" id="btn-exportar-excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                        <a href="/oc/reporte-concesionario" class="btn btn-sm btn-outline-secondary" id="btn-reporte-concesionario">
                            <i class="bi bi-bar-chart"></i> Reporte por Concesionario

                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body" id="lista-oc">

            <!-- Vista de ESCRITORIO-->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-sm table-hover" id="tabla-oc">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Serie</th>
                            <th>Concesionario</th>
                            <th>Ubicación</th>
                            <th>Fecha</th>
                            <th>Moneda</th>

                            <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                <th>Total</th>
                                <th>Amortización</th>
                                <th>Saldo</th>
                            <?php endif; ?>

                            <?php if ($estadoActual == 'emitido'): ?>
                                <th>Operaciones</th>
                            <?php elseif ($estadoActual == 'proceso'): ?>
                                <th>Pagar</th>
                            <?php elseif ($estadoActual == 'pagado'): ?>
                                <th>Detalles</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordenCompras)): ?>
                            <tr>
                                <td colspan="12" class="text-center">No hay ordénes de compras registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($ordenCompras as $ordenCompra): ?>
                                <tr>
                                    <td><?= $numeroFila++ ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['numeroOCIdentificador']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['razonsocial']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['ubicacion']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['emision']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['moneda']) ?></td>

                                    <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                        <td><?= number_format($ordenCompra['totalOC'] ?? 0, 2) ?></td>
                                        <td><?= number_format($ordenCompra['totalPagado'] ?? 0, 2) ?></td>
                                        <td><?= number_format($ordenCompra['saldoRestante'] ?? 0, 2) ?></td>
                                    <?php endif; ?>

                                    <!-- Acciones según estado -->
                                    <td>
                                        <?php include 'acciones_oc.php'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista de MÓVIL (Acordeón) -->
            <div class="d-block d-md-none">
                <?php if (empty($ordenCompras)): ?>
                    <div class="text-center text-muted p-3">No hay órdenes de compra registradas.</div>
                <?php else: ?>
                    <div class="accordion" id="acordeonOrdenesCompra">
                        <?php $numeroFila = 1; ?>
                        <?php foreach ($ordenCompras as $ordenCompra): ?>
                            <div class="accordion-item mb-2 shadow-sm">
                                <h2 class="accordion-header" id="heading-oc-<?= $ordenCompra['idordencompra'] ?>">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-oc-<?= $ordenCompra['idordencompra'] ?>"
                                        aria-expanded="false"
                                        aria-controls="collapse-oc-<?= $ordenCompra['idordencompra'] ?>">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <span class="fw-bold">
                                                <i class="bi bi-receipt me-2 text-primary"></i>
                                                OC-<?= htmlspecialchars($ordenCompra['numeroOCIdentificador']) ?>
                                            </span>
                                            <span class="text-end text-muted small">
                                                <?= htmlspecialchars($ordenCompra['razonsocial']) ?>
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-oc-<?= $ordenCompra['idordencompra'] ?>"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="heading-oc-<?= $ordenCompra['idordencompra'] ?>"
                                    data-bs-parent="#acordeonOrdenesCompra">
                                    <div class="accordion-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>#:</strong> <?= $numeroFila++ ?></li>
                                            <li class="list-group-item"><strong>Ubicación:</strong> <?= htmlspecialchars($ordenCompra['ubicacion']) ?></li>
                                            <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($ordenCompra['emision']) ?></li>
                                            <li class="list-group-item"><strong>Moneda:</strong> <?= htmlspecialchars($ordenCompra['moneda']) ?></li>
                                            <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                                <li class="list-group-item"><strong>Total:</strong> <?= number_format($ordenCompra['totalOC'] ?? 0, 2) ?></li>
                                                <li class="list-group-item"><strong>Amortización:</strong> <?= number_format($ordenCompra['totalPagado'] ?? 0, 2) ?></li>
                                                <li class="list-group-item"><strong>Saldo:</strong> <?= number_format($ordenCompra['saldoRestante'] ?? 0, 2) ?></li>
                                            <?php endif; ?>
                                        </ul>
                                        <div class="mt-3">
                                            <strong>Acciones:</strong><br>
                                            <?php include 'acciones_oc.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>



        <div class="card-footer" id="detalle-oc" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div style="padding: 1rem;">

                        <h3 id="detail-concesionario-razon-social"></h3>
                        <h5 id="detail-oc-summary"></h5>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end" style="padding-right: 1.5rem;">
                    <button type="button" id="btn-volver" class="btn btn-outline-primary btn-sm">Volver</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabla-detalles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Versión</th>
                            <th>Combustible</th>
                            <th>Año</th>
                            <th>Chasis</th>
                            <th>Serie</th>
                            <th>Placa</th>
                            <th>Placa Rotativa</th>
                            <th>Color</th>
                            <th>Moneda</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div> <!-- ./table-responsive -->
        </div> <!-- ./card-footer -->

        <!-- Zona modales -->
        <div class="modal fade" id="modal-oc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-oc" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="" autocomplete="off" id="formulario-oc">
                    <div class="modal-content">
                        <div class="modal-header bg-yonda">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Estado de los vehículos</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="tabla-autos-modal">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Auto</th>
                                            <th>Cantidad</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                                <div class="mt-3 text-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="escorrecto" value="S" id="escorrecto">
                                        <label class="form-check-label">SI</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="escorrecto" value="N" checked id="escorrecto">
                                        <label class="form-check-label">NO</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="modal fade" id="modal-proceso" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="form-proceso">
                    <div class="modal-content">
                        <div class="modal-header bg-yonda">
                            <h5 class="modal-title" id="modal-proceso-titulo">Observaciones</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="id-oc-proceso">
                            <input type="hidden" id="modal-proceso-ruta">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea id="observaciones" class="form-control" rows="4" placeholder=""></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div> <!-- ./card -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
    <script src="/assets/js/logoBase64.js"></script>
    <script src="/assets/js/oc-proceso-reporte/pdf.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const botonesFiltro = document.querySelectorAll("#botones-filtro .btn")
            const enlacesDetalle = document.querySelectorAll(".show-details")
            const botonVolver = document.querySelector("#btn-volver")
            const speedAnimation = 750;

            // Referencias a los contenedores principales
            const listaOc = document.getElementById('lista-oc');
            const ocDetailView = document.getElementById('detalle-oc');

            // Referencias a elementos dentro de la vista de detalle
            const detailConcesionarioRazonSocial = document.getElementById('detail-concesionario-razon-social');
            const detailOcSummary = document.getElementById('detail-oc-summary');
            const tablaDetallesBody = document.getElementById('tabla-detalles').querySelector('tbody');
            const btnVolver = document.getElementById('btn-volver');

            // Selecciona todos los enlaces con la clase 'show-details'
            const detailLinks = document.querySelectorAll('.show-details');

            // Variables para el modal de verificar si los autos llegaron bien
            const tablaAutosModalBody = document.querySelector("#tabla-autos-modal tbody");
            const modalOc = new bootstrap.Modal(document.getElementById('modal-oc'));
            const formularioOc = document.getElementById("formulario-oc");

            // Campos para abrir el modal de proceso o anulado:
            const modalProceso = new bootstrap.Modal(document.getElementById("modal-proceso"));
            const formProceso = document.getElementById("form-proceso");
            const inputIdOc = document.getElementById("id-oc-proceso");
            const inputRuta = document.getElementById("modal-proceso-ruta");
            const inputObs = document.getElementById("observaciones");
            const modalTitulo = document.getElementById("modal-proceso-titulo");


            let idOC = null; // Para identifcar el idoc a actualizar desde el modal para verificar si los autos llegarón de acuerdo a la OC

            const btnExportarExcel = document.getElementById("btn-exportar-excel");

            if (btnExportarExcel) {
                btnExportarExcel.addEventListener('click', async () => {
                    try {
                        // Obtener los datos de la API
                        const response = await fetch('/api/ocproceso/reporte');
                        if (!response.ok) {
                            throw new Error('Error al obtener los datos del reporte');
                        }
                        const data = await response.json();

                        const {
                            totalOCProceso,
                            totalVehiculos,
                            Deudaglobal,
                            totalPagosGlobal,
                            saldoPendienteGlobal,
                            porcentajeAvanceGlobal
                        } = data.resumenEjecutivo;

                        const detallesOrdenes = data.detallesOrdenes;

                        // Crear el libro y la hoja de trabajo de Excel
                        const workbook = new ExcelJS.Workbook();
                        const worksheet = workbook.addWorksheet('Resumen Ejecutivo');

                        // Agregar el título principal
                        worksheet.mergeCells('A1:H1');
                        const titleCell = worksheet.getCell('A1');
                        titleCell.value = 'Resumen Ejecutivo de Órdenes en Proceso';
                        titleCell.font = {
                            size: 16,
                            bold: true
                        };
                        titleCell.alignment = {
                            horizontal: 'center'
                        };

                        // Construir la tabla de Totales Globales
                        worksheet.getCell('A3').value = 'Totales Globales';
                        worksheet.getCell('A3').font = {
                            bold: true,
                            size: 12,
                            underline: true
                        };

                        const headerTotales = ['Métricas', 'Valor', 'Descripción'];
                        const headerTotalesRow = worksheet.addRow(headerTotales);

                        // Aplicar estilos a la fila de encabezado de totales
                        headerTotalesRow.font = {
                            bold: true
                        };
                        headerTotalesRow.eachCell(cell => {
                            cell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: {
                                    argb: 'FFD3D3D3'
                                }
                            };
                            cell.border = {
                                top: {
                                    style: 'thin'
                                },
                                left: {
                                    style: 'thin'
                                },
                                bottom: {
                                    style: 'thin'
                                },
                                right: {
                                    style: 'thin'
                                }
                            };
                        });

                        // Formatear los datos de totales y agregar a la hoja
                        const resumenData = [
                            ['Total de OCs', parseInt(totalOCProceso), 'Órdenes de compra en proceso'],
                            ['Total de Vehículos', parseInt(totalVehiculos), 'Vehículos entre todas las OCs'],
                            ['Deuda Global', parseFloat(Deudaglobal), 'Monto total adeudado entre todas las OCs'],
                            ['Total Pagos Global', parseFloat(totalPagosGlobal), 'Suma de todos los pagos realizados'],
                            ['Saldo Pendiente Global', parseFloat(saldoPendienteGlobal), 'Suma de los saldos por pagar'],
                            ['Porcentaje de Avance Global', parseFloat(porcentajeAvanceGlobal) / 100, 'Avance promedio entre todas las OCs']
                        ];

                        const firstTotalesRow = 5;
                        resumenData.forEach((row, index) => {
                            const newRow = worksheet.getRow(firstTotalesRow + index);
                            newRow.values = row;
                            newRow.eachCell(cell => {
                                cell.border = {
                                    top: {
                                        style: 'thin'
                                    },
                                    left: {
                                        style: 'thin'
                                    },
                                    bottom: {
                                        style: 'thin'
                                    },
                                    right: {
                                        style: 'thin'
                                    }
                                };
                            });
                        });


                        worksheet.getCell('B7').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
                        worksheet.getCell('B8').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
                        worksheet.getCell('B9').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';


                        worksheet.getCell('B10').numFmt = '0.00%';

                        // Construir la tabla de Detalle Completo de Órdenes
                        const startRowDetalle = worksheet.rowCount + 2;
                        worksheet.getCell(`A${startRowDetalle}`).value = 'Detalle Completo de Órdenes';
                        worksheet.getCell(`A${startRowDetalle}`).font = {
                            bold: true,
                            size: 12,
                            underline: true
                        };

                        const headersDetalle = [
                            'OC Identificador',
                            'Concesionario',
                            'Ubicación',
                            'Total OC',
                            'Pagado',
                            'Saldo',
                            '% Avance',
                            'Cant. Vehículos'
                        ];

                        const headerDetalleRow = worksheet.addRow(headersDetalle);
                        headerDetalleRow.font = {
                            bold: true
                        };
                        headerDetalleRow.eachCell(cell => {
                            cell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: {
                                    argb: 'FFD3D3D3'
                                }
                            };
                            cell.border = {
                                top: {
                                    style: 'thin'
                                },
                                left: {
                                    style: 'thin'
                                },
                                bottom: {
                                    style: 'thin'
                                },
                                right: {
                                    style: 'thin'
                                }
                            };
                        });

                        // Llenar la tabla de detalles con los datos
                        detallesOrdenes.forEach(oc => {
                            const row = worksheet.addRow([
                                oc.OCIdentificador,
                                oc.Concesionario,
                                oc.ubicacionConcesionario,
                                parseFloat(oc.totalOC),
                                parseFloat(oc.pagado),
                                parseFloat(oc.saldo),
                                parseFloat(oc.avancePorcentaje) / 100,
                                oc.totalVehiculos
                            ]);

                            // Aplicar bordes a las celdas de datos
                            row.eachCell(cell => {
                                cell.border = {
                                    top: {
                                        style: 'thin'
                                    },
                                    left: {
                                        style: 'thin'
                                    },
                                    bottom: {
                                        style: 'thin'
                                    },
                                    right: {
                                        style: 'thin'
                                    }
                                };
                            });
                        });


                        // Aplicar formato de moneda y porcentaje a las columnas de la segunda tabla
                        worksheet.getColumn('D').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
                        worksheet.getColumn('E').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
                        worksheet.getColumn('F').numFmt = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
                        worksheet.getColumn('G').numFmt = '0.00%';

                        // Ajustar el ancho de las columnas
                        worksheet.columns.forEach(column => {
                            let maxLength = 0;
                            column.eachCell({
                                includeEmpty: true
                            }, cell => {
                                const columnLength = cell.value ? cell.value.toString().length : 10;
                                if (columnLength > maxLength) {
                                    maxLength = columnLength;
                                }
                            });
                            column.width = Math.min(30, maxLength < 10 ? 10 : maxLength + 2);
                        });


                        const observacionesCol = worksheet.getColumn('C');
                        if (observacionesCol) {
                            observacionesCol.width = 40;
                        }

                        // Generar el archivo y descargarlo
                        const buffer = await workbook.xlsx.writeBuffer();
                        const blob = new Blob([buffer], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'Resumen_Ejecutivo.xlsx';
                        a.click();
                        window.URL.revokeObjectURL(url);

                    } catch (error) {
                        console.error('Error al exportar a Excel:', error);
                        alert('Hubo un error al exportar el reporte. Por favor, inténtelo de nuevo.');
                    }
                });

            }



            document.querySelectorAll(".btn-abrir-modal-estado").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const idOC = btn.dataset.id;
                    const accion = btn.dataset.accion;
                    const ruta = btn.dataset.ruta;

                    inputIdOc.value = idOC;
                    inputRuta.value = ruta;
                    inputObs.value = "";

                    // Cambiar el título dinámicamente
                    modalTitulo.textContent = (accion === "proceso") ?
                        "Observaciones - Confirmar OC" :
                        "Observaciones - Anular OC";

                    modalProceso.show();
                });
            });



            // Enviar el formulario
            formProceso.addEventListener("submit", async (e) => {
                e.preventDefault();
                const ruta = inputRuta.value;
                const obs = inputObs.value.trim();

                if (!obs) {
                    alert("Por favor ingrese el motivo antes de continuar.");
                    return;
                }

                if (confirm('¿Esta seguro de actualizar el estado de la OC?'))

                {
                    try {
                        const res = await fetch(ruta, {
                            method: "POST",
                            body: new URLSearchParams({
                                observaciones: obs
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            modalProceso.hide();
                            showToast(data.message, "SUCCESS", 1200);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast(data.message, "WARNING", 1200);
                        }
                    } catch (error) {
                        console.error(error);
                        alert("Hubo un error al actualizar.");
                    }

                }

            });



            // Para el modal de check

            document.querySelectorAll("a[data-idocmodal]").forEach(icono => {
                icono.addEventListener("click", async (e) => {
                    e.preventDefault();
                    idOC = e.currentTarget.dataset.idocmodal;
                    if (!idOC) return;

                    try {
                        const response = await fetch(`/api/oc/infoAutos/${idOC}`);
                        if (!response.ok) throw new Error("Error al obtener los autos");

                        const autos = await response.json();
                        if (!autos || autos.length === 0) {
                            tablaAutosModalBody.innerHTML = `<tr><td colspan="4" class="text-center">No hay autos para esta orden</td></tr>`;
                        } else {
                            tablaAutosModalBody.innerHTML = "";

                            // Crear descripción concatenada para ordenar
                            autos.forEach(item => {
                                item.descripcion = `${item.marca} ${item.modelo} ${item.tipovehiculo} ${item.version} ${item.combustible} ${item.color} ${item.anio}`;
                            });

                            autos.sort((a, b) => a.descripcion.localeCompare(b.descripcion));

                            autos.forEach((item, index) => {
                                const row = document.createElement("tr");
                                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.marca} / ${item.modelo} / ${item.tipovehiculo} / ${item.version} / ${item.combustible} / ${item.color} / ${item.anio}</td>
                            <td>1</td>
                        `;
                                tablaAutosModalBody.appendChild(row);
                            });
                        }

                        modalOc.show();

                    } catch (error) {
                        console.error(error);
                        alert("No se pudieron cargar los datos.");
                    }
                });
            });


            function limpiarVistaDetalle() {
                // Limpiar el nombre del concesionario
                if (detailConcesionarioRazonSocial) {
                    detailConcesionarioRazonSocial.textContent = '';
                }

                // Limpiar el resumen de la OC (número, fecha, moneda, total)
                if (detailOcSummary) {
                    detailOcSummary.textContent = '';
                }

                // Limpiar la tabla de detalles (vehículos)
                if (tablaDetallesBody) {
                    tablaDetallesBody.innerHTML = '';
                }
            }

            // Evento para eventos clikc de detalles
            detailLinks.forEach(link => {
                link.addEventListener('click', async (event) => {
                    event.preventDefault();

                    const enlace = event.currentTarget || event.target.closest('a');
                    const ocId = enlace.dataset.idoc;

                    if (!ocId) {
                        console.warn('ID de Orden de Compra no encontrado en el enlace de detalle.');
                        return;
                    }

                    limpiarVistaDetalle();

                    const apiUrl = `/api/oc/${ocId}`;

                    try {
                        const response = await fetch(apiUrl);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        // Validar si el nuevo JSON tiene la estructura esperada
                        if (!data || !data.orden || !Array.isArray(data.vehiculos)) {
                            showToast('No hay datos válidos para la OC', 'WARNING', 1200);
                            return;
                        }

                        const {
                            orden,
                            vehiculos
                        } = data;

                        // Mostrar vista
                        $("#lista-oc").slideUp(speedAnimation);
                        $("#detalle-oc").slideDown(speedAnimation);

                        // Llenar encabezado
                        if (detailConcesionarioRazonSocial && detailOcSummary) {
                            detailConcesionarioRazonSocial.textContent = orden.concesionario.razon_social || 'N/A';

                            const numeroOc = orden.numero_oc_formateado || 'N/A';
                            // console.log(numeroOc);
                            const fechaEmision = orden.fecha_emision_oc || 'N/A';
                            const moneda = orden.moneda_oc || 'N/A';
                            const total = orden.totales.total ? parseFloat(orden.totales.total).toFixed(2) : '0.00';

                            detailOcSummary.textContent = `${numeroOc} | ${fechaEmision} | ${moneda} ${total}`;
                        }

                        // Llenar tabla
                        if (tablaDetallesBody) {
                            tablaDetallesBody.innerHTML = '';

                            vehiculos.forEach((vehiculo, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${vehiculo.marca || 'N/A'}</td>
                        <td>${vehiculo.modelo || 'N/A'}</td>
                        <td>${vehiculo.version || 'N/A'}</td>
                        <td>${vehiculo.combustible || 'N/A'}</td>
                        <td>${vehiculo.anio_modelo || 'N/A'}</td>
                        <td>${vehiculo.chasis || 'N/A'}</td>
                        <td>${vehiculo.serie_motor || 'N/A'}</td>
                        <td>${vehiculo.placa || 'N/A'}</td>
                        <td>${vehiculo.placa_rotativa || 'N/A'}</td>
                        <td>${vehiculo.color || 'N/A'}</td>
                        <td>${orden.moneda_oc || 'N/A'}</td>
                        <td>${vehiculo.precio_unitario ? parseFloat(vehiculo.precio_unitario).toFixed(2) : '0.00'}</td>
                    `;
                                tablaDetallesBody.appendChild(row);
                            });
                        }

                    } catch (error) {
                        console.error(error);
                        showToast('No se ha podido cargar los datos', 'WARNING', 1200);
                    }
                });
            });



            if (botonVolver) {
                botonVolver.addEventListener('click', () => {

                    limpiarVistaDetalle();
                    $("#detalle-oc").slideUp(speedAnimation);
                    $("#lista-oc").slideDown(speedAnimation);
                });
            }



        });

        const btnPdfOcs = document.querySelectorAll('.btn-pdf-oc-class');

        btnPdfOcs.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const ocId = btn.getAttribute('data-idoc');

                if (!ocId) {
                    console.error('No se encontró el ID de la orden de compra en el atributo data-idoc.');
                    return;
                }

                try {


                    //  Obtener los datos de la API
                    const response = await fetch(`/api/oc/${ocId}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (!data || !data.orden || !Array.isArray(data.vehiculos)) {
                        throw new Error('No se encontraron datos válidos en la respuesta de la API.');
                    }

                    const ocOrden = data.orden;
                    const ocDetalles = data.vehiculos;
                    const logoBase64 = window.logoBase64;


                    const documento = {
                        pageSize: 'A4',
                        pageOrientation: 'portrait',
                        pageMargins: [40, 25, 25, 25],
                        defaultStyle: {
                            fontSize: 7.2,
                            alignment: 'center'
                        },
                        content: [
                            // Header
                            {
                                columns: [{
                                        image: logoBase64,
                                        width: 90,
                                        alignment: 'left'
                                    },
                                    {
                                        text: '',
                                        width: '*'
                                    },
                                    {
                                        table: {
                                            widths: ['auto'],
                                            body: [
                                                [{
                                                    text: 'ORDEN DE COMPRA',
                                                    alignment: 'center',
                                                    fontSize: 14,
                                                    bold: true,
                                                    margin: [0, 5, 0, 0],
                                                    color: '#000'
                                                }],
                                                [{
                                                    text: ocOrden.numero_oc_formateado || '-',
                                                    alignment: 'center',
                                                    fontSize: 14,
                                                    bold: true,
                                                    fillColor: 'yellow',
                                                    margin: [0, 8, 0, 5],
                                                    border: [true, false, true, true]
                                                }],
                                            ],
                                        },
                                        layout: 'noBorders',
                                        alignment: 'right',
                                        width: 150,
                                    },
                                ],
                            },
                            {
                                text: '',
                                margin: [0, 10]
                            },

                            // Sección de Concesionario
                            {
                                table: {
                                    widths: ['*'],
                                    body: [
                                        [{
                                            text: 'CONCESIONARIO',
                                            alignment: 'center',
                                            bold: true,
                                            fillColor: '#E0E0E0'
                                        }]
                                    ]
                                },
                                layout: 'noBorders',
                                margin: [0, 5, 0, 5],
                            },
                            {
                                table: {
                                    widths: ['auto', '*', 'auto', '*'],
                                    body: [
                                        [{
                                            text: 'Punto de venta:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.ubigeo || '-',
                                            alignment: 'left'
                                        }, {
                                            text: 'Banco:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: '-',
                                            alignment: 'left'
                                        }],
                                        [{
                                            text: 'Razón Social:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.razon_social || '-',
                                            alignment: 'left'
                                        }, {
                                            text: 'N° Oper:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: '-',
                                            alignment: 'left'
                                        }],
                                        [{
                                            text: 'RUC:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.ruc || '-',
                                            alignment: 'left'
                                        }, {
                                            text: 'Fecha:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.fecha_emision_oc || '-',
                                            alignment: 'left'
                                        }],
                                        [{
                                            text: 'Dirección:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.direccion || '-',
                                            alignment: 'left',
                                            colSpan: 3
                                        }, {}, {}],
                                        [{
                                            text: 'Vendedor:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.vendedor_contacto || '-',
                                            alignment: 'left'
                                        }, {
                                            text: 'Teléfono:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: ocOrden.concesionario.telefono || '-',
                                            alignment: 'left'
                                        }],
                                    ],
                                },
                                layout: 'lightHorizontalLines',
                                margin: [0, 0, 0, 10],
                            },

                            // Sección de Asociado
                            {
                                table: {
                                    widths: ['*'],
                                    body: [
                                        [{
                                            text: 'ASOCIADO',
                                            alignment: 'center',
                                            bold: true,
                                            fillColor: '#E0E0E0'
                                        }]
                                    ]
                                },
                                layout: 'noBorders',
                                margin: [0, 5, 0, 5],
                            },
                            {
                                table: {
                                    widths: ['auto', '*', 'auto', '*'],
                                    body: [
                                        [{
                                            text: 'Titular:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: 'YONDA & GRUPO HUARACA EIRL',
                                            alignment: 'left'
                                        }, {
                                            text: 'Teléfono:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: '926743607',
                                            alignment: 'left'
                                        }],
                                        [{
                                            text: 'DNI o RUC:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: '20609396866',
                                            alignment: 'left'
                                        }, {
                                            text: 'Correo:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: 'asistentecontable@yondaperu.com',
                                            alignment: 'left'
                                        }],
                                        [{
                                            text: 'Dirección:',
                                            bold: true,
                                            alignment: 'left'
                                        }, {
                                            text: 'PANAMERICANA SUR KM PUERTA 201',
                                            alignment: 'left',
                                            colSpan: 3
                                        }, {}, {}],
                                    ],
                                },
                                layout: 'lightHorizontalLines',
                                margin: [0, 0, 0, 10],
                            },

                            // Sección de Vehículos
                            {
                                table: {
                                    widths: ['*'],
                                    body: [
                                        [{
                                            text: 'DESCRIPCIÓN DE VEHÍCULOS',
                                            alignment: 'center',
                                            bold: true,
                                            fillColor: '#E0E0E0'
                                        }]
                                    ]
                                },
                                layout: 'noBorders',
                                margin: [0, 5, 0, 5],
                            },
                            {
                                table: {
                                    headerRows: 1,
                                    widths: ['auto', 'auto', 'auto', 'auto', 'auto', '*', 'auto', 'auto', 'auto', 'auto'],
                                    body: [
                                        ['MARCA', 'MODELO', 'VERSIÓN', 'AÑO', 'COLOR', 'CHASIS', 'PLACA', 'PLACA R.', 'SERIE MOTOR', 'PRECIO INDIVIDUAL'].map(text => ({
                                            text,
                                            bold: true,
                                            alignment: 'center'
                                        })),
                                        ...ocDetalles.map(v => [{
                                                text: v.marca || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.modelo || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.version || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.anio_modelo || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.color || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.chasis || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.placa || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.placa_rotativa || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: v.serie_motor || '',
                                                alignment: 'center'
                                            },
                                            {
                                                text: `$ ${parseFloat(v.precio_unitario || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`,
                                                alignment: 'right'
                                            },
                                        ]),
                                    ],
                                },
                                layout: 'lightHorizontalLines',
                            },
                            {
                                text: '',
                                margin: [0, 10]
                            },

                            // Resumen Financiero y Observaciones
                            {
                                columns: [{
                                        text: 'Observaciones:',
                                        width: 'auto',
                                        bold: true,
                                        alignment: 'left',
                                        margin: [0, 0, 0, 5],
                                    },
                                    {
                                        text:   ocOrden.observaciones_oc || 'Sin observaciones',
                                        width: '*',
                                        alignment: 'left',
                                        margin: [0, 0, 0, 5],
                                    },
                                    {
                                        table: {
                                            widths: ['auto', 'auto'],
                                            body: [
                                                [{
                                                    text: 'RESUMEN FINANCIERO',
                                                    colSpan: 2,
                                                    bold: true,
                                                    alignment: 'center',
                                                    fillColor: '#E0E0E0'
                                                }, {}],
                                                [{
                                                    text: 'Valor Venta:',
                                                    bold: true,
                                                    alignment: 'left'
                                                }, {
                                                    text: `$ ${parseFloat(ocOrden.totales.valor_venta || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`,
                                                    alignment: 'right'
                                                }],
                                                [{
                                                    text: 'IGV:',
                                                    bold: true,
                                                    alignment: 'left'
                                                }, {
                                                    text: `$ ${parseFloat(ocOrden.totales.igv || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`,
                                                    alignment: 'right'
                                                }],
                                                [{
                                                    text: 'TOTAL:',
                                                    bold: true,
                                                    alignment: 'left'
                                                }, {
                                                    text: `$ ${parseFloat(ocOrden.totales.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`,
                                                    alignment: 'right'
                                                }],
                                            ],
                                        },
                                        width: 'auto',
                                    },
                                ],
                            },
                            {
                                text: '',
                                margin: [0, 10]
                            },

                            // Sección de Aprobaciones
                            {
                                table: {
                                    widths: ['*'],
                                    body: [
                                        [{
                                            text: 'APROBACIONES',
                                            alignment: 'center',
                                            bold: true,
                                            fillColor: '#E0E0E0'
                                        }]
                                    ]
                                },
                                layout: 'noBorders',
                                margin: [0, 5, 0, 5],
                            },
                            {
                                table: {
                                    widths: ['*', '*', '*', '*'],
                                    body: [
                                        [{
                                            text: 'PREPARADO',
                                            bold: true,
                                            alignment: 'center'
                                        }, {
                                            text: 'APROBADO',
                                            bold: true,
                                            alignment: 'center'
                                        }, {
                                            text: 'AUTORIZADO',
                                            bold: true,
                                            alignment: 'center'
                                        }, {
                                            text: 'PROCESADO',
                                            bold: true,
                                            alignment: 'center'
                                        }],
                                        [{
                                            text: ' ',
                                            margin: [0, 35]
                                        }, {
                                            text: ' ',
                                            margin: [0, 35]
                                        }, {
                                            text: ' ',
                                            margin: [0, 35]
                                        }, {
                                            text: ' ',
                                            margin: [0, 35]
                                        }],
                                    ],
                                },
                                layout: {
                                    hLineWidth: function(i, node) {
                                        return 1;
                                    },
                                    vLineWidth: function(i, node) {
                                        return 1;
                                    },
                                    hLineColor: function(i, node) {
                                        return '#000';
                                    },
                                    vLineColor: function(i, node) {
                                        return '#000';
                                    },
                                    paddingLeft: function(i, node) {
                                        return 4;
                                    },
                                    paddingRight: function(i, node) {
                                        return 4;
                                    },
                                    paddingTop: function(i, node) {
                                        return 4;
                                    },
                                    paddingBottom: function(i, node) {
                                        return 4;
                                    },
                                },
                            },
                        ],
                    };


                    pdfMake.createPdf(documento).open();

                } catch (error) {
                    console.error('Error al generar el PDF:', error);
                    alert('Ocurrió un error al generar el PDF. Por favor, inténtelo de nuevo.');
                }
            });





        });
    </script>



    <?php include __DIR__ . '/../layout/footer.php'; ?>