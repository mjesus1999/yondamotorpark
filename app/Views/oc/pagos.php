<?php include __DIR__ . '/../layout/header.php'; ?>


<div class="container-fluid">


    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Registrar Pago de Orden de Compra</h5>
            </div>
            <div class="card-body">

                <form action="/pagosoc/store" id="form-pago-oc" autocomplete="off" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="idpagooc" id="idpagooc">

                    <div class="row g-3 mt-1 mb-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="amortizacion" id="amortizacion" class="form-control" placeholder="Monto amortizado" required>
                                <label for="amortizacion">Amortización</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="saldo" id="saldo" class="form-control" placeholder="Saldo pendiente" required>
                                <label for="saldo">Saldo</label>
                            </div>
                        </div>

                        
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="file" name="comprobante" id="comprobante" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <label for="comprobante">Comprobante de pago</label>
                            </div>
                            <small class="text-muted">Formatos permitidos: PDF, JPG, PNG.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="datetime-local" name="fecha" id="fecha" class="form-control" required>
                                <label for="fecha">Fecha y hora del pago</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="reset" class="btn btn-outline-secondary btn-sm me-2">Limpiar</button>
                        <button type="submit" class="btn btn-primary btn-sm">Registrar Pago</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


</div>




















<?php include __DIR__ . '/../layout/footer.php'; ?>