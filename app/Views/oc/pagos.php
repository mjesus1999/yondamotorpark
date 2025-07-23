<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid mt-4">

    <?php if (!empty($autos)): ?>


        <div class="alert alert-success mt-5" role="alert">
            <h4 class="alert-heading">¡DETALLES DE LOS AUTOS!</h4>
            <?php foreach($autos as $auto): ?>
                
                <p><?= htmlspecialchars($auto['auto'])?> - <?= htmlspecialchars($auto['cantidad'])?></p>
                <hr>
                <?php endforeach; ?>
        </div>


    <?php endif; ?>




    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        Pagos Realizados - <?= htmlspecialchars($concesionario['concesionario'] ?? '---') ?>
                    </h6>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-striped table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-pagos">
                            <?php if (!empty($pagos)): ?>
                                <?php foreach ($pagos as $pago): ?>
                                    <tr>
                                        <td><?= number_format($pago['amortizacion'] ?? 0, 2) ?></td>
                                        <td><?= number_format($pago['saldo'] ?? 0, 2) ?></td>
                                        <td><?= !empty($pago['fecha']) ? date('d/m/Y H:i', strtotime($pago['fecha'])) : '' ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($pago['comprobante'])): ?>
                                                <?php
                                                $ruta = htmlspecialchars($pago['comprobante']);
                                                $esPdf = strtolower(pathinfo($ruta, PATHINFO_EXTENSION)) === 'pdf';
                                                ?>
                                                <?php if ($esPdf): ?>
                                                    <a href="<?= $ruta ?>" target="_blank" class="">
                                                        Ver PDF
                                                    </a>
                                                <?php else: ?>
                                                    <a class=" ver-comprobante"
                                                        data-img="<?= $ruta ?>">
                                                        Ver Comprobante
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin archivo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin pagos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>


                    <!-- Botones -->
                    <div class="modal-footer">
                        <a href="/oc/listar/proceso" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid "></i> Cancelar
                        </a>
                        <button class="btn btn-outline-primary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalPago">
                            Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registrar Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-yonda text-white">
                    <h5 class="modal-title" id="modalPagoLabel">
                        Registrar Pago (OC #<?= htmlspecialchars($ordenCompra['idordencompra'] ?? '---') ?>)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="idorden" value="<?= htmlspecialchars($ordenCompra['idordencompra'] ?? 0) ?>">
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

</div>

<div id="modalImagen" style="
    display:none;
    position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.7);
    justify-content:center;
    align-items:center;
    z-index:9999;
">
    <img id="imagenAmpliada" src=""
        style="max-width:100%;max-height:90%;border:5px solid #fff;border-radius:5px;">
</div>

<script>
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('ver-comprobante')) {
            const modal = document.getElementById('modalImagen');
            const imgAmpliada = document.getElementById('imagenAmpliada');
            imgAmpliada.src = e.target.dataset.img;
            modal.style.display = 'flex';
        }
    });

    document.getElementById('modalImagen').addEventListener('click', function() {
        this.style.display = 'none';
    });

    document.addEventListener("DOMContentLoaded", () => {
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
                    // console.log(data)

                    if (data.success) {
                        console.log('DATA:', data.success);
                        showToast(data.message, "SUCCESS", 1200);
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast(data.message, "WARNING", 1200);
                    }
                } catch (error) {
                    showToast(error, "DANGER", 1200);
                }
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>