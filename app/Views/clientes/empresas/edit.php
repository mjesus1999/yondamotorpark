<?php include __DIR__ . '/../../layout/header.php'; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .form-control, .form-select {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .card {
        border-radius: 0.5rem;
    }
    .btn {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }
    .breadcrumb-item a {
        color: #0d6efd;
        transition: all 0.2s;
    }
    .breadcrumb-item a:hover {
        color: #0a58ca;
    }
    .alert {
        border-radius: 0.5rem;
    }
    h5, h6 {
        font-weight: 600;
    }
</style>

<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-3 rounded-3 shadow-sm py-2" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="bi bi-buildings me-1"></i>Clientes (Empresas)</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-pencil-square me-1"></i>Actualizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/clientes/empresas" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta principal del formulario -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-building-gear me-2"></i>Actualizar Cliente (Empresa)</h5>
        </div>
        
        <div class="card-body p-4">
            <?php if (isset($empresaCliente)): ?>
                <form action="/clientes/empresaCliente/update/<?= htmlspecialchars($empresaCliente['idempresa']) ?>" autocomplete="off" id="formulario-cliente-empresa" method="POST">
                    <!-- Sección 1: Información básica -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-card-text me-2"></i>Información Básica</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="razonsocial" name="razonsocial" 
                                        placeholder="Razón Social" required value="<?= htmlspecialchars($empresaCliente['razonsocial']) ?>">
                                    <label for="razonsocial"><i class="bi bi-building me-1"></i>Razón Social <span class="text-danger fw-bold">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nombrecomercial" name="nombrecomercial"
                                        placeholder="Nombre Comercial" required value="<?= htmlspecialchars($empresaCliente['nombrecomercial']) ?>">
                                    <label for="nombrecomercial"><i class="bi bi-shop me-1"></i>Nombre Comercial <span class="text-danger fw-bold">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Datos legales -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-file-earmark-text me-2"></i>Datos Legales</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ruc" name="ruc" 
                                        placeholder="RUC" maxlength="11" required value="<?= htmlspecialchars($empresaCliente['ruc']) ?>">
                                    <label for="ruc"><i class="bi bi-file-text me-1"></i>RUC <span class="text-danger fw-bold">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="representante" name="representante" 
                                        required placeholder="Representante Legal" value="<?= htmlspecialchars($empresaCliente['representante']) ?>">
                                    <label for="representante"><i class="bi bi-person-vcard me-1"></i>Representante <span class="text-danger fw-bold">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Contacto -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-telephone me-2"></i>Contacto</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Correo electrónico" value="<?= htmlspecialchars($empresaCliente['email'] ?? '') ?>">
                                    <label for="email"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="telprimario" maxlength="9" 
                                        name="telprimario" required placeholder="Teléfono principal" 
                                        value="<?= htmlspecialchars($empresaCliente['telprimario']) ?>">
                                    <label for="telprimario"><i class="bi bi-phone me-1"></i>Teléfono <span class="text-danger fw-bold">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
                        <a href="/clientes/empresas" class="btn btn-outline-secondary">
                             Cancelar
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            Actualizar
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Empresa cliente no encontrada.
                </div>
                <a href="/clientes/empresas" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a la lista
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {

        const formularioClienteEmpresa = document.querySelector('#formulario-cliente-empresa');

        formularioClienteEmpresa.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (await ask("¿Desea actualizar este cliente?", "Actualizar cliente")) {

                event.target.submit();

            }
        });
    });
</script>



<?php include __DIR__ . '/../../layout/footer.php'; ?>