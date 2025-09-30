<?php
include __DIR__ . '/../layout/header.php';
// Lógica para obtener el saldo inicial del día anterior (estático por ahora)
$monto_inicial_previo = 1700.00;
// Lógica para obtener el total de ingresos y egresos del día (estático por ahora)
$total_ingresos = 1500.00;
$total_egresos = 300.00;
$saldo_teorico_esperado = $monto_inicial_previo + $total_ingresos - $total_egresos;
?>

<style>
    /* Estilos personalizados del formulario */
    body { background-color: #f8f9fa; }
    .card { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
    .card-header { background-color: #2196F3; color: white; text-align: center; }
    .info-card { background-color: #e3f2fd; border: 1px solid #bbdefb; }
    .card-footer { background-color: #f1f1f1; }
</style>

<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="/arqueoCaja/" class="text-primary">Arqueo Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/arqueoCaja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>

    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header">
            <h2><i class="fas fa-check-double me-2"></i>Arqueo de Caja</h2>
            <small class="text-white-50">Confirma el saldo final de hoy.</small>
        </div>
        <div class="card-body p-4">
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="info-card p-3 rounded">
                        <h6 class="text-muted">Saldo Inicial</h6>
                        <h4 class="fw-bold text-primary" id="montoInicial">S/ <?= number_format($monto_inicial_previo, 2); ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card p-3 rounded">
                        <h6 class="text-muted">Total Ingresos</h6>
                        <h4 class="fw-bold text-success" id="totalIngresos">+S/ <?= number_format($total_ingresos, 2); ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card p-3 rounded">
                        <h6 class="text-muted">Total Egresos</h6>
                        <h4 class="fw-bold text-danger" id="totalEgresos">-S/ <?= number_format($total_egresos, 2); ?></h4>
                    </div>
                </div>
            </div>
            <hr>
            <div class="mb-3 text-center">
                <h4 class="text-muted">Saldo Teórico Esperado</h4>
                <h2 class="fw-bold" id="montoTeorico">S/ <?= number_format($saldo_teorico_esperado, 2); ?></h2>
            </div>
            <form id="arqueoForm" method="POST" action="save.php">
                <input type="hidden" name="monto_inicial" value="<?= $monto_inicial_previo ?>">
                <input type="hidden" name="total_ingresos" value="<?= $total_ingresos ?>">
                <input type="hidden" name="total_egresos" value="<?= $total_egresos ?>">
                <input type="hidden" name="monto_teorico" value="<?= $saldo_teorico_esperado ?>">
                <div class="mb-3">
                    <label for="montoFisico" class="form-label fw-bold">Monto Físico Contado</label>
                    <div class="input-group">
                        <span class="input-group-text">S/</span>
                        <input type="number" class="form-control form-control-lg" id="montoFisico" name="monto_fisico" placeholder="Ingresa el monto que contaste" required>
                    </div>
                    <div class="form-text">Billetes y monedas en tu caja.</div>
                </div>
                <div class="alert alert-info text-center mt-4" id="alert-diferencia">
                    <h5 class="mb-0">Diferencia: <span id="diferencia">S/ 0.00</span></h5>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Finalizar Arqueo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('arqueoForm');
        const montoFisicoInput = document.getElementById('montoFisico');
        const montoTeorico = parseFloat(document.getElementById('montoTeorico').textContent.replace('S/ ', '').replace(',', ''));
        const diferenciaSpan = document.getElementById('diferencia');
        const alertDiv = document.getElementById('alert-diferencia');

        // Escucha el evento 'input' para actualizar la diferencia en tiempo real
        montoFisicoInput.addEventListener('input', function() {
            const montoFisico = parseFloat(this.value) || 0;
            const diferencia = montoFisico - montoTeorico;

            diferenciaSpan.textContent = `S/ ${diferencia.toFixed(2)}`;

            alertDiv.classList.remove('alert-info', 'alert-warning', 'alert-danger', 'alert-success');
            if (diferencia > 0) {
                alertDiv.classList.add('alert-warning');
            } else if (diferencia < 0) {
                alertDiv.classList.add('alert-danger');
            } else {
                alertDiv.classList.add('alert-success');
            }
        });

        // Escucha el evento 'submit' del formulario
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Evita el envío automático del formulario
            
            const montoFisico = parseFloat(montoFisicoInput.value) || 0;
            const diferencia = montoFisico - montoTeorico;
            
            let mensaje = `Diferencia: S/ ${diferencia.toFixed(2)}\n\n`;

            if (diferencia > 0) {
                mensaje += 'Hay un sobrante. ¿Deseas guardar el arqueo de todos modos?';
            } else if (diferencia < 0) {
                mensaje += 'Hay un faltante. ¿Deseas guardar el arqueo de todos modos?';
            } else {
                mensaje += 'La caja está cuadrada. ¿Deseas guardar el arqueo?';
            }

            if (confirm(mensaje)) {
                // Si el usuario confirma, envía el formulario
                form.submit();
            }
        });
    });
</script>