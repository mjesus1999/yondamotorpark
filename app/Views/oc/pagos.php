<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    
    .card-pagos {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header-pagos {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }

    .table-pagos {
        margin-bottom: 0;
    }

    .table-pagos thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
        padding: 0.75rem 1rem;
    }

    .table-pagos tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f3f5;
    }

    .table-pagos tbody tr:hover {
        background-color: rgba(58, 123, 213, 0.05);
    }

    .btn-outline-primary {
        border-color: #3a7bd5;
        color: #3a7bd5;
    }

    .btn-outline-primary:hover {
        background-color: #3a7bd5;
        color: white;
    }

    .badge-pill {
        border-radius: 50px;
        padding: 5px 10px;
        font-weight: 500;
    }

    /* Estilos para el modal */
    .modal-pago {
        border-radius: 12px;
        overflow: hidden;
        border: none;
    }

    .modal-header-pago {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .modal-title-pago {
        font-weight: 600;
    }

    
    .form-floating-pago label {
        color: #6c757d;
    }

    .form-floating-pago .form-control {
        border-radius: 8px;
        padding: 1rem 0.75rem;
        border: 1px solid #e0e0e0;
    }

    .form-floating-pago .form-control:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
    }

    /* Estilos para el mensaje de no hay pagos */
    .empty-state {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>


<div class="container-fluid mt-4">

    <?php if (!empty($autos)): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-car me-2"></i>INVENTARIO DE VEHÍCULOS</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Marca / Modelo</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Versión</th>
                                <th class="text-center">Combustible</th>
                                <th class="text-center">Color</th>
                                <th class="text-center">Año</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($autos as $auto):
                                $datos = explode(' / ', $auto['auto']);
                            ?>
                                <tr>
                                    <td class="fw-bold text-center"><?= htmlspecialchars($datos[0] ?? '') ?> <?= htmlspecialchars($datos[1] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($datos[2] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($datos[3] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($datos[4] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($datos[5] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($datos[6] ?? '') ?></td>
                                    <td class="text-center"><span class="badge bg-<?= ($datos[7] ?? '') == 'Nuevo' ? 'success' : 'warning' ?>"><?= htmlspecialchars($datos[7] ?? '') ?></span></td>
                                    <td class="text-center fw-bold"><?= htmlspecialchars($auto['cantidad']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No hay vehículos disponibles en este momento.
        </div>
    <?php endif; ?>


    <div class="row">
        <div class="col-md-12">
            <div class="card card-pagos">

                <div class="card-header card-header-pagos d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-money-check-alt me-2"></i> Pagos Realizados -
                        <?php if (isset($concesionario['concesionario'])): ?>
                            Concesionario: <?= htmlspecialchars($concesionario['concesionario']) ?>
                        <?php endif; ?>
                        <?php if (isset($concesionario['idordencompra'])): ?>
                            <span class="badge bg-white text-primary ms-2">OC #<?= htmlspecialchars($concesionario['idordencompra']) ?></span>
                        <?php endif; ?>
                    </h6>
                    <div>
                        <span class="badge bg-white text-primary badge-pill">
                            <i class="fas fa-dollar-sign me-1"></i>
                            Total Pagado: $<?= isset($totalAmortizado) ? number_format($totalAmortizado, 2) : (isset($pagos) ? number_format(array_sum(array_column($pagos, 'amortizacion')), 2) : '0.00') ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-pagos table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Monto Pagado</th>
                                    <th>Saldo Restante</th>
                                    <th>Fecha Real Pago</th>
                                    <th>Comprobante</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-pagos">
                                <?php if (!empty($pagos)): ?>
                                    <?php $numFila = 1; ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td class="ps-4"><?= $numFila++ ?></td>
                                            <td class="fw-semibold text-success">$<?= number_format($pago['amortizacion'] ?? 0, 2) ?></td>
                                            <td>$<?= number_format($pago['saldo'] ?? 0, 2) ?></td>
                                            <td>
                                                <?php if (!empty($pago['fecharealpago'])): ?>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($pago['fecharealpago'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                    <?php if ($esPdf): ?>
                                                        <a href="<?= htmlspecialchars($pago['comprobante']) ?>" target="_blank"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary ver-comprobante-img"
                                                            data-img="<?= htmlspecialchars($pago['comprobante']) ?>">
                                                            <i class="fas fa-image me-1"></i> Ver Imagen
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted">Sin archivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= htmlspecialchars($pago['logistica'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <h5 class="mt-3">No hay pagos registrados</h5>
                                                <p class="mb-0">No se han encontrado pagos para esta orden de compra.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                    <div class="card-footer d-flex justify-content-end align-items-center border-top gap-3">
                        <a href="<?= $saldoRestante > 0 ? '/oc/listar/proceso' : '/oc/listar/pagado' ?>"
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <?php if ($saldoRestante > 0): ?>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPago">
                                <i class="fas fa-plus-circle me-1"></i> Registrar Pago
                            </button>
                        <?php endif; ?>
                        <?php if ($saldoRestante == 0): ?>
                            <button class="btn btn-outline-danger btn-sm" id="btn-generar-pdf">
                                <i class="fas fa-file-pdf me-1"></i> Generar PDF
                            </button>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-pago">
                <div class="modal-header modal-header-pago">
                    <h5 class="modal-title modal-title-pago" id="modalPagoLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        Registrar Pago (OC #<?= htmlspecialchars($concesionario['idordencompra'] ?? '---') ?>)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="idorden" value="<?= htmlspecialchars($concesionario['idordencompra'] ?? 0) ?>">

                        <div class="form-floating form-floating-pago mb-4">
                            <input type="number" min="500" step="500" name="amortizacion" id="amortizacion"
                                class="form-control" placeholder="0.00" required>
                            <label for="amortizacion"><i class="fas fa-dollar-sign me-1"></i> Monto a pagar</label>
                        </div>

                        <div class="form-floating form-floating-pago mb-4">
                            <input type="text" id="saldo" name="saldo" class="form-control bg-light"
                                value="<?= number_format($saldoRestante ?? 0, 2) ?>" disabled>
                            <label for="saldo" class="text-success"><i class="fas fa-wallet me-1"></i> Saldo restante</label>
                        </div>

                        <div class="form-floating form-floating-pago mb-4">
                            <input type="datetime-local" id="fecharealpago" name="fecharealpago" class="form-control">
                            <label for="fecharealpago"><i class="far fa-calendar-alt me-1"></i> Fecha de pago</label>
                        </div>

                        <div class="mb-4">
                            <label for="comprobante" class="form-label text-info fw-semibold mb-3">
                                <i class="fas fa-file-upload me-1"></i> Comprobante de pago
                            </label>
                            <input type="file" name="comprobante" id="comprobante" class="form-control"
                                accept="application/pdf,image/*" required>
                            <div class="form-text">Formatos aceptados: PDF, JPG, PNG (Máx. 5MB)</div>
                        </div>

                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm px-4" id="btn-agregar-pago">
                                <i class="fas fa-save me-1"></i> Guardar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Visualizar Imagen -->
    <div class="modal fade" id="modalImagenComprobante" tabindex="-1" aria-labelledby="modalImagenComprobanteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalImagenComprobanteLabel">
                        <i class="fas fa-image me-2"></i> Comprobante de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img id="imagenAmpliada" src="" class="img-fluid w-100" alt="Comprobante de Pago" style="max-height: 80vh; object-fit: contain;">
                </div>
               
            </div>
        </div>
    </div>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modalImagen = new bootstrap.Modal(document.getElementById('modalImagenComprobante'));
            document.querySelectorAll('.ver-comprobante-img').forEach(button => {
                button.addEventListener('click', function() {
                    const imgSrc = this.dataset.img;
                    document.getElementById('imagenAmpliada').src = imgSrc;
                    modalImagen.show();
                });
            });

            const amortizacionInput = document.querySelector("#amortizacion");
            const saldoInput = document.querySelector("#saldo");
            const btnGuardar = document.querySelector("#btn-agregar-pago");

            const saldo = parseFloat(saldoInput.value.replace(/,/g, "")) || 0;
            
            amortizacionInput.addEventListener("input", () => {
                const amortizacion = parseFloat(amortizacionInput.value);

                if (isNaN(amortizacion) || amortizacion <= 0) {
                    amortizacionInput.classList.add("is-invalid");
                    amortizacionInput.classList.remove("is-valid");
                    btnGuardar.disabled = true;
                } else if (amortizacion > saldo) {
                    amortizacionInput.classList.add("is-invalid");
                    amortizacionInput.classList.remove("is-valid");
                    btnGuardar.disabled = true;
                } else {
                    amortizacionInput.classList.remove("is-invalid");
                    amortizacionInput.classList.add("is-valid");
                    btnGuardar.disabled = false;
                }
            });
            
            document.getElementById("formPago").addEventListener("submit", async function(e) {
                e.preventDefault();
                const amortizacion = parseFloat(amortizacionInput.value);
                
                if (isNaN(amortizacion) || amortizacion <= 0) {
                    alert("Ingrese un monto válido mayor a 0.");
                    return;
                }
                
                if (amortizacion > saldo) {
                    alert(`La amortización no debe ser mayor al saldo (${saldo.toFixed(2)}).`);
                    return;
                }

                if (confirm("¿Desea registrar el pago?")) {
                    try {
                        const formData = new FormData(this);
                        const response = await fetch("/oc/pagos/store", {
                            method: "POST",
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            console.log('DATA:', data.message);
                            showToast(data.message, "SUCCESS", 1200);
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            console.error('ERROR:', data.message);
                            showToast(data.message, "WARNING", 1200);
                            
                        }
                    } catch (error) {
                        console.error('FETCH ERROR:', error);
                        showToast(error, "ERROR", 1200);
                        
                    }
                }
            });


            const btnGenerarPdf = document.getElementById('btn-generar-pdf');
            if (btnGenerarPdf) {
                btnGenerarPdf.addEventListener('click', () => {
                    const element = document.createElement('div');
                    element.style.padding = '20px';
                    element.style.fontFamily = 'Arial, sans-serif';
                    element.style.color = '#333';

                    // 1. Encabezado del reporte
                    const header = document.createElement('div');
                    header.style.borderBottom = '2px solid #3a7bd5';
                    header.style.marginBottom = '20px';
                    header.style.paddingBottom = '10px';

                    const title = document.createElement('h2');
                    title.textContent = `Reporte de Pagos - OC #${document.querySelector('[name="idorden"]')?.value || 'N/A'}`;
                    title.style.textAlign = 'center';
                    title.style.color = '#3a7bd5';
                    title.style.marginBottom = '5px';
                    title.style.fontWeight = 'bold';
                    
                    const subtitle = document.createElement('h3');
                    subtitle.textContent = 'DETALLE DE VEHÍCULOS';
                    subtitle.style.textAlign = 'center';
                    subtitle.style.color = '#555';
                    subtitle.style.marginTop = '0';
                    subtitle.style.fontSize = '18px';
                    
                    header.appendChild(title);
                    header.appendChild(subtitle);
                    element.appendChild(header);

                    // 2. Tabla de vehículos con mejor formato
                    const autosTable = document.querySelector('.table');
                    if (autosTable) {
                        const clonedAutosTable = autosTable.cloneNode(true);

                        // Aplicar estilos para PDF
                        clonedAutosTable.style.width = '100%';
                        clonedAutosTable.style.borderCollapse = 'collapse';
                        clonedAutosTable.style.marginBottom = '25px';
                        clonedAutosTable.style.fontSize = '12px';

                        // Estilos para celdas
                        clonedAutosTable.querySelectorAll('th, td').forEach(cell => {
                            cell.style.border = '1px solid #ddd';
                            cell.style.padding = '4px';
                            cell.style.textAlign = 'center';
                        });

                        // Estilos para encabezados
                        clonedAutosTable.querySelectorAll('th').forEach(th => {
                            th.style.backgroundColor = '#3a7bd5';
                            th.style.color = 'white';
                            th.style.fontWeight = 'bold';
                        });

                        // Resaltar filas alternas
                        clonedAutosTable.querySelectorAll('tbody tr:nth-child(even)').forEach(row => {
                            row.style.backgroundColor = '#f9f9f9';
                        });

                        // Manejar "COLOR NO ESPECIFICADO"
                        clonedAutosTable.querySelectorAll('td').forEach(td => {
                            if (td.textContent.trim() === 'COLOR NO ESPECIFICADO') {
                                td.textContent = 'N/A';
                                td.style.color = '#999';
                            }
                        });

                        element.appendChild(clonedAutosTable);
                    }
                    
                    // 3. Sección de pagos
                    const pagosSection = document.createElement('div');
                    pagosSection.style.marginTop = '30px';
                    pagosSection.style.borderTop = '2px solid #3a7bd5';
                    pagosSection.style.paddingTop = '15px';

                    const pagosTitle = document.createElement('h3');
                    pagosTitle.textContent = 'DETALLES DE PAGOS';
                    pagosTitle.style.color = '#3a7bd5';
                    pagosTitle.style.marginBottom = '15px';
                    pagosSection.appendChild(pagosTitle);

                    const pagosTable = document.querySelector('.table-pagos');
                    if (pagosTable) {
                        const clonedPagosTable = pagosTable.cloneNode(true);

                        // Ajustes para el PDF
                        clonedPagosTable.style.width = '100%';
                        clonedPagosTable.style.borderCollapse = 'collapse';
                        clonedPagosTable.style.marginBottom = '20px';
                        clonedPagosTable.style.fontSize = '12px';

                        clonedPagosTable.querySelectorAll('th, td').forEach(cell => {
                            cell.style.border = '1px solid #ddd';
                            cell.style.padding = '6px';
                            cell.style.textAlign = 'center';
                        });

                        clonedPagosTable.querySelectorAll('th').forEach(th => {
                            th.style.backgroundColor = '#3a7bd5';
                            th.style.color = 'white';
                            th.style.fontWeight = 'bold';
                        });

                        // Ocultar columna de comprobante y registrado por para PDF
                        clonedPagosTable.querySelectorAll('th:nth-child(5), td:nth-child(5), th:nth-child(6), td:nth-child(6)').forEach(el => {
                            el.style.display = 'none';
                        });
                        
                        pagosSection.appendChild(clonedPagosTable);
                    }

                    element.appendChild(pagosSection);
                    
                    // 4. Totales con mejor formato
                    const totalesDiv = document.createElement('div');
                    totalesDiv.style.marginTop = '30px';
                    totalesDiv.style.textAlign = 'right';
                    totalesDiv.style.fontWeight = 'bold';
                    totalesDiv.style.borderTop = '2px solid #3a7bd5';
                    totalesDiv.style.paddingTop = '15px';

                    // Obtener total pagado del badge
                    const totalPagadoBadge = document.querySelector('.card-header-pagos .badge');
                    let totalPagadoText = '0.00';
                    if (totalPagadoBadge) {
                        const badgeText = totalPagadoBadge.textContent || '';
                        const match = badgeText.match(/\$([\d,]+\.\d{2})/);
                        if (match) {
                            totalPagadoText = match[1];
                        }
                    }

                    const saldoRestante = <?= json_encode($saldoRestante ?? 0) ?>;

                    const totalPagado = document.createElement('p');
                    totalPagado.textContent = `TOTAL PAGADO: $<?= isset($totalAmortizado) ? number_format($totalAmortizado, 2) : (isset($pagos) ? number_format(array_sum(array_column($pagos, 'amortizacion')), 2) : '0.00') ?>`;
                    totalPagado.style.color = '#28a745';
                    totalPagado.style.fontSize = '16px';
                    totalPagado.style.marginBottom = '10px';
                    
                    const saldoRestanteP = document.createElement('p');
                    saldoRestanteP.textContent = `SALDO RESTANTE: $${saldoRestante.toFixed(2)}`;
                    saldoRestanteP.style.color = '#dc3545';
                    saldoRestanteP.style.fontSize = '16px';
                    saldoRestanteP.style.marginBottom = '10px';

                    const fechaPago = document.createElement('p');
                    fechaPago.textContent = `FECHA DE GENERACIÓN: ${new Date().toLocaleDateString()}`;
                    fechaPago.style.color = '#6c757d';
                    fechaPago.style.fontSize = '14px';
                    
                    totalesDiv.appendChild(totalPagado);
                    totalesDiv.appendChild(saldoRestanteP);
                    totalesDiv.appendChild(fechaPago);
                    element.appendChild(totalesDiv);

                    // Opciones de html2pdf
                    const opt = {
                        margin: [10, 10, 10, 10],
                        filename: `Reporte_Pagos_OC_${document.querySelector('[name="idorden"]')?.value || ''}_${new Date().toISOString().slice(0, 10)}.pdf`,
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            letterRendering: true,
                            useCORS: true,
                            logging: false
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait',
                            compress: true
                        },
                        pagebreak: {
                            mode: ['avoid-all', 'css', 'legacy']
                        }
                    };

                    // Generar PDF
                    setTimeout(() => {
                        html2pdf().set(opt).from(element).save();
                    }, 200);
                });
            }
        });
    </script>

 <?php include __DIR__ . '/../layout/footer.php'; ?>