<?php

use function App\Helpers\persistirDatosFormulario;

require_once __DIR__ . '/../../../Helpers/functions.php'; ?>
<?php include __DIR__ . '/../../layout/header.php'; ?>
<?php include __DIR__ . '/../../components/mapa-includes.php'; ?>
<?php include __DIR__ . '/../../components/mapa-modal.php'; ?>


<style>
    .form-control,
    .form-select {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .card {
        border-radius: 0.5rem;
    }

    .btn {
        border-radius: 0.375rem;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #0d6efd;
    }

    .alert {
        border-radius: 0.5rem;
    }
</style>

<?php if (isset($error) && !empty($error)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" id="errorToast">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $error ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <?php persistirDatosFormulario($data) ?>
<?php endif; ?>

<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-3 rounded-3 shadow-sm" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="bi bi-building me-1"></i>Empresas</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-plus-circle me-1"></i>Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/clientes/empresas/" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta principal del formulario -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0"><i class="bi bi-building me-2"></i>Registrar Cliente (Empresa)</h6>
        </div>
        <div class="card-body p-4">
            <form action="/clientes/empresas/storeempresaclient" id="form-registro-cliente-empresa" autocomplete="off" method="POST">
                <!-- Sección 1: Información básica -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-card-text me-2"></i>Información Básica</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="ruc" id="ruc" class="form-control" placeholder="Ingrese el N° de RUC" maxlength="11" required>
                                <label for="ruc"><i class="bi bi-file-text me-1"></i>N° RUC</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" id="razonsocial" name="razonsocial" class="form-control" placeholder="Ingrese la razón social" required>
                                <label for="razonsocial"><i class="bi bi-building me-1"></i>Razón social</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nombrecomercial" id="nombrecomercial" class="form-control" placeholder="Ingrese el nombre comercial" required>
                                <label for="nombrecomercial"><i class="bi bi-shop me-1"></i>Nombre comercial</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="representante" id="representante" class="form-control" placeholder="Ingrese el nombre del representante" required>
                                <label for="representante"><i class="bi bi-person-badge me-1"></i>Representante</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Ubicación -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-geo-alt me-2"></i>Ubicación</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="departamento" id="departamento" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="departamento"><i class="bi bi-map me-1"></i>Departamento</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="provincia" id="provincia" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="provincia"><i class="bi bi-map me-1"></i>Provincia</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="iddistrito" id="distrito" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="distrito"><i class="bi bi-map me-1"></i>Distrito</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Contacto -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-telephone me-2"></i>Contacto</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control" placeholder="exmple@gmail.com">
                                <label for="email"><i class="bi bi-envelope me-1"></i>Correo</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telprimario" id="telprimario" class="form-control" placeholder="Número de telefóno" maxlength="9" pattern="[0-9]+" required>
                                <label for="telprimario"><i class="bi bi-phone me-1"></i>Telefóno</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telsecundario" id="telsecundario" class="form-control" placeholder="Número de telefóno" maxlength="9" pattern="[0-9]+">
                                <label for="telsecundario"><i class="bi bi-phone me-1"></i>Telefóno 2 (Opcional)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 4: Dirección y coordenadas -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-pin-map me-2"></i>Dirección y Coordenadas</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ingrese una dirección">
                                <label for="direccion"><i class="bi bi-signpost me-1"></i>Dirección (Opcional)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="referencia" id="referencia" class="form-control" placeholder="Ingrese una referencia">
                                <label for="referencia"><i class="bi bi-signpost-2 me-1"></i>Referencia (Opcional)</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="latitud" id="latitud" class="form-control" placeholder="Ingrese la latitud">
                                <label for="latitud"><i class="bi bi-globe me-1"></i>Latitud</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="longitud" id="longitud" class="form-control" placeholder="Ingrese la longitud">
                                <label for="longitud"><i class="bi bi-globe me-1"></i>Longitud</label>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-success w-60" id="btn-mapa">
                                <i class="bi bi-map me-1"></i> 
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
                    <button type="reset" class="btn btn-outline-secondary" id="btn-cancelar">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-registrar">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="/assets/js/ubigeo.js" defer></script>
<script>
    const formRegistroClienteEmpresa = document.querySelector('#form-registro-cliente-empresa');

    formRegistroClienteEmpresa.addEventListener('submit', (event) => {
        event.preventDefault();

        if (confirm("¿Desea registrar este nuevo cliente?")) {
            formRegistroClienteEmpresa.submit()
        }
    });
</script>

<?php include __DIR__ . '/../../layout/footer.php'; ?>