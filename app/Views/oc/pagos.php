<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid mt-4">

    <?php if (!empty($autos)): ?>
        <div class="alert alert-success mt-5 alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="fas fa-car me-2"></i>¡DETALLES DE LOS AUTOS!</h4>
            <ul class="list-unstyled mb-0">
                <?php foreach ($autos as $auto): ?>
                    <li><strong><?= htmlspecialchars($auto['auto']) ?></strong> - Cantidad: <?= htmlspecialchars($auto['cantidad']) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i> Pagos Realizados -
                        <?php if (isset($concesionario['concesionario'])): ?>
                            Concesionario: <?= htmlspecialchars($concesionario['concesionario']) ?>
                        <?php endif; ?>
                        <?php if (isset($concesionario['idordencompra'])): ?>
                            (OC #<?= htmlspecialchars($concesionario['idordencompra']) ?>)
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Monto Pagado</th>
                                    <th>Saldo Restante</th>
                                    <th>Fecha Real Pago</th>
                                    <th>Comprobante</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-pagos">
                                <?php $totalAmortizado = 0; ?>
                                <?php if (!empty($pagos)): ?>
                                    <?php $numFila = 1; ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <?php $totalAmortizado += $pago['amortizacion']; ?>
                                        <tr>
                                            <td><?= $numFila++ ?></td>
                                            <td>$<?= number_format($pago['amortizacion'] ?? 0, 2) ?></td>
                                            <td>$<?= number_format($pago['saldo'] ?? 0, 2) ?></td>

                                            <td><?= !empty($pago['fecharealpago']) ? date('d/m/Y H:i', strtotime($pago['fecharealpago'])) : '<span class="text-muted">N/A</span>' ?></td>
                                            <td class="text-center">
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <?php
                                                    $ruta = htmlspecialchars($pago['comprobante']);
                                                    $esPdf = strtolower(pathinfo($ruta, PATHINFO_EXTENSION)) === 'pdf';
                                                    ?>
                                                    <?php if ($esPdf): ?>
                                                        <a href="<?= $ruta ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Ver PDF">
                                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-info ver-comprobante-img"
                                                            data-img="<?= $ruta ?>" title="Ver Comprobante de Imagen">
                                                            <i class="fas fa-image me-1"></i> Ver Comprobante
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin archivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars($pago['logistica'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No hay pagos registrados para esta Orden de Compra.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total Pagado:</strong></td>
                                    <td colspan="5" class="text-start text-success">
                                        <strong>$<?= number_format($totalAmortizado ?? 0, 2) ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-1 mt-1 btn-controles">
                        <a href="<?= $saldoRestante > 0 ? '/oc/listar/proceso' :'/oc/listar/pagado' ?>" class="btn btn-outline-secondary btn-sm m-1">
                            Volver
                        </a>
                        <?php if ($saldoRestante > 0): ?>
                            <button class="btn btn-outline-primary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalPago">
                                Registrar Pago
                            </button>
                        <?php endif ?>

                        <?php if ($saldoRestante == 0): ?>
                            <button class="btn btn-outline-danger btn-sm m-1" id="btn-generar-pdf">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-yonda text-white">
                    <h5 class="modal-title" id="modalPagoLabel">
                        Registrar Pago (OC #<?= htmlspecialchars($concesionario['idordencompra'] ?? '---') ?>)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="idorden" value="<?= htmlspecialchars($concesionario['idordencompra'] ?? 0) ?>">
                        <div class="form-floating mb-3">
                            <input type="number" min="1" name="amortizacion" id="amortizacion"
                                class="form-control" placeholder="0.00" required>
                            <label for="amortizacion">Monto a pagar</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" id="saldo" name="saldo" class="form-control"
                                value="<?= number_format($saldoRestante ?? 0, 2) ?>" disabled>
                            <label for="saldo">Saldo restante</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="datetime-local" id="fecharealpago" name="fecharealpago" class="form-control">
                            <label for="fecharealpago">Fecha de pago</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="file" name="comprobante" id="comprobante" class="form-control"
                                accept="application/pdf,image/*" required>
                            <label for="comprobante">Comprobante</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="btn-agregar-pago">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImagenComprobante" tabindex="-1" aria-labelledby="modalImagenComprobanteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalImagenComprobanteLabel">Comprobante de Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenAmpliada" src="" class="img-fluid rounded" alt="Comprobante de Pago" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

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
                    showToast(error, "DANGER", 1200);

                }
            }
        });

        const btnGenerarPdf = document.getElementById('btn-generar-pdf');
        if (btnGenerarPdf) {
            btnGenerarPdf.addEventListener('click', () => {
                // Crear un elemento div temporal para el contenido del PDF
                const pdfContainer = document.createElement('div');
                pdfContainer.style.fontFamily = 'Arial, sans-serif';
                pdfContainer.style.fontSize = '12px';
                pdfContainer.style.color = '#333';
                pdfContainer.style.padding = '10mm'; 

                // 1. Agregar el encabezado de los detalles de los autos
                const autosDiv = document.querySelector('.alert.alert-success');
                if (autosDiv) {
                    const autosClone = autosDiv.cloneNode(true);
                    // Eliminar el botón de cerrar del alerta para el PDF
                    const closeButton = autosClone.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.remove();
                    }
                    autosClone.style.backgroundColor = '#d1e7dd'; 
                    autosClone.style.borderColor = '#badbcc';
                    autosClone.style.color = '#0f5132';
                    autosClone.style.padding = '10px';
                    autosClone.style.marginBottom = '10mm'; 
                    pdfContainer.appendChild(autosClone);
                }

                // 2. Agregar la tarjeta de pagos realizados
                const cardElement = document.querySelector('.card.shadow-sm');
                if (cardElement) {
                    const cardClone = cardElement.cloneNode(true);

                    // Eliminar los botones de control
                    const buttonsSection = cardClone.querySelector('.btn-controles');
                    if (buttonsSection) {
                        buttonsSection.remove();
                    }

                    // Ajustar el header de la tarjeta para el PDF
                    const cardHeader = cardClone.querySelector('.card-header');
                    if (cardHeader) {
                        cardHeader.style.backgroundColor = '#ec8b0bff'; 
                        cardHeader.style.color = 'white';
                        cardHeader.style.padding = '10px 15px';
                    }

                    // Ocultar la columna de "Comprobante" en el PDF
                    cardClone.querySelectorAll('th:nth-child(5), td:nth-child(5)').forEach(el => {
                        el.style.display = 'none';
                    });
                    

                    // Ajustar estilos de la tabla para el PDF
                    const table = cardClone.querySelector('table');
                    if (table) {
                        table.style.width = '100%';
                        table.style.borderCollapse = 'collapse';
                        table.style.marginTop = '10px';
                        table.querySelectorAll('th, td').forEach(cell => {
                            cell.style.border = '1px solid #dee2e6';
                            cell.style.padding = '8px';
                            cell.style.textAlign = 'center';
                        });
                        table.querySelectorAll('thead th').forEach(th => {
                            th.style.backgroundColor = '#f8f9fa'; 
                        });
                        const tfootTotalCell = cardClone.querySelector('tfoot .text-success strong');
                        if(tfootTotalCell){
                            tfootTotalCell.style.fontWeight = 'bold';
                            tfootTotalCell.style.fontSize = '1.1em';
                            tfootTotalCell.style.color = '#198754'; 
                        }
                    }

                    // Asegurarse de que los valores de "Monto Pagado", "Saldo Restante" y "Total Pagado" tengan el símbolo de dólar en el PDF
                    cardClone.querySelectorAll('#tabla-pagos td:nth-child(2), #tabla-pagos td:nth-child(3)').forEach(cell => {
                        if (!cell.textContent.startsWith('$')) {
                            cell.textContent = '$' + cell.textContent;
                        }
                    });
                    const totalPaidCell = cardClone.querySelector('tfoot .text-success strong');
                    if (totalPaidCell && !totalPaidCell.textContent.startsWith('$')) {
                        totalPaidCell.textContent = '$' + totalPaidCell.textContent;
                    }


                    pdfContainer.appendChild(cardClone);
                }

                html2pdf(pdfContainer, { 
                    margin: 10,
                    filename: `reporte_pagos_OC_<?= htmlspecialchars($concesionario['idordencompra'] ?? 'REPORTE') ?>.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, logging: false, dpi: 192, letterRendering: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                });
            });
        }
    });
</script>

<?php ?>