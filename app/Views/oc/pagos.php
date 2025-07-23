<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">

        
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white">
                    <h6 class="mb-0">
                        Pagos Realizados - <?= htmlspecialchars($ordenCompra['concesionario'] ?? '---') ?>
                    </h6>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-striped">
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
                                     <td>
                                        <?php if (!empty($pago['comprobante'])): ?>
                                            <?php
                                            $ruta = htmlspecialchars($pago['comprobante']);
                                            $esPdf = strtolower(pathinfo($ruta, PATHINFO_EXTENSION)) === 'pdf';
                                            ?>
                                            <?php if ($esPdf): ?>
                                                <a href="/<?= $ruta ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    Ver PDF
                                                </a>
                                            <?php else: ?>
                                                <a href="javascript:void(0)" 
                                                class="ver-comprobante" 
                                                data-img="<?= $ruta ?>">
                                                Comprobante
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
                                    <td colspan="4" class="text-center text-muted">
                                        Sin pagos registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white">
                    <h6 class="mb-0">
                        Registrar Pago (OC #<?= htmlspecialchars($ordenCompra['idordencompra'] ?? '---') ?>)
                    </h6>
                </div>
                <div class="card-body">

                    <form id="formPago" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="idorden" value="<?= htmlspecialchars($ordenCompra['idordencompra'] ?? 0) ?>">
                        <input type="hidden" name="idlogistica" value="2">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="amortizacion" id="amortizacion"
                                           class="form-control" placeholder="Monto a pagar" required >
                                    <label for="amortizacion">Monto a pagar</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    
                                    <input type="text" id="saldo" name="saldo" class="form-control" 
                                           value="<?= number_format($saldoRestante ?? 0, 2) ?>" readonly>
                                    <label for="saldo">Saldo restante</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="file" name="comprobante" id="comprobante" class="form-control" 
                                           accept="application/pdf,image/*" required>
                                    <label for="comprobante">Comprobante</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-circle-check"></i> Registrar Pago
                            </button>
                        </div>
                    </form>

                    <!-- Mensaje de resultado -->
                    <div id="mensaje" class="mt-3"></div>
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



document.addEventListener('click', function(e) {
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




document.getElementById('formPago').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('/oc/pagos/store', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('mensaje').innerHTML = 
                `<div class="alert alert-success">${data.message}</div>`;

            
            
        } else {
            document.getElementById('mensaje').innerHTML = 
                `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        document.getElementById('mensaje').innerHTML = 
            `<div class="alert alert-danger">Error: ${error}</div>`;
    }
});


</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
