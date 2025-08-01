<?php

use function App\Helpers\persistirDatosFormulario;

require_once __DIR__ . '/../../Helpers/functions.php'; ?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../components/mapa-includes.php'; ?>
<?php include __DIR__ . '/../components/mapa-modal.php'; ?>

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


<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-3 rounded-3 shadow-sm" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="bi bi-people me-1"></i>Clientes (Normales)</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-person-plus me-1"></i>Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/clientes/" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta principal del formulario -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Registrar Cliente (Normal)</h5>
        </div>
        <div class="card-body p-4">
            <form action="/storepersonclient/store" id="form-registro-cliente-persona" autocomplete="off" method="POST">
                <input type="hidden" name="idcliente" id="idcliente">

                <!-- Sección 1: Información personal -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-person-vcard me-2"></i>Información Personal</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="tipodocumento" id="tipodocumento" class="form-select" required>
                                    <option value="DNI" selected>DNI</option>
                                    <option value="CEX">Carnet de extranjería</option>
                                    <option value="PAS">Pasaporte</option>
                                </select>
                                <label for="tipodocumento"><i class="bi bi-card-text me-1"></i>Tipo documento</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nrodoc" id="ndocumento" class="form-control" placeholder="Ingrese el N° de documento" maxlength="12" required>
                                <label for="ndocumento"><i class="bi bi-123 me-1"></i>N° documento</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Ingrese los apellidos" required>
                                <label for="apellidos"><i class="bi bi-person me-1"></i>Apellidos</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Ingrese los nombres" required>
                                <label for="nombres"><i class="bi bi-person me-1"></i>Nombres</label>
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
                                <select name="distrito" id="distrito" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="distrito"><i class="bi bi-map me-1"></i>Distrito</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Datos personales -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-info-circle me-2"></i>Datos Personales</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                <label for="genero"><i class="bi bi-gender-male me-1"></i>Género</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="estadocivil" id="estadocivil" class="form-select" required>
                                    <option value="SOL">Solter@</option>
                                    <option value="CAS">Casad@</option>
                                    <option value="VDO">Viud@</option>
                                    <option value="DVC">Divorciad@</option>
                                    <option value="CNV">Conviviente</option>
                                </select>
                                <label for="estadocivil"><i class="bi bi-heart me-1"></i>Estado civil</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" name="fechanac" id="fechanacimiento" class="form-control" placeholder="Fecha nacimiento" required>
                                <label for="fechanacimiento"><i class="bi bi-calendar me-1"></i>Fecha nacimiento</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 4: Contacto -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-telephone me-2"></i>Contacto</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control" placeholder="example@gmail.com">
                                <label for="email"><i class="bi bi-envelope me-1"></i>Correo</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telprimario" id="telprimario" class="form-control" maxlength="9" pattern="[0-9]+" placeholder="N° telefóno" required>
                                <label for="telprimario"><i class="bi bi-phone me-1"></i>Teléfono</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telalternativo" id="telalternativo" class="form-control" maxlength="9" pattern="[0-9]+" placeholder="N° telefóno">
                                <label for="telalternativo"><i class="bi bi-phone me-1"></i>Teléfono 2 (Opcional)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 5: Dirección -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-house me-2"></i>Dirección</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ingrese una dirección">
                                <label for="direccion"><i class="bi bi-signpost me-1"></i>Dirección (Opcional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="referencia" id="referencia" class="form-control" placeholder="Ingrese una referencia">
                                <label for="referencia"><i class="bi bi-signpost-2 me-1"></i>Referencia (Opcional)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 6: Coordenadas -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-geo me-2"></i>Coordenadas</h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="latitud" id="latitud" class="form-control" placeholder="Latitud">
                                <label for="latitud"><i class="bi bi-globe me-1"></i>Latitud</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="longitud" id="longitud" class="form-control" maxlength="40" placeholder="Longitud">
                                <label for="longitud"><i class="bi bi-globe me-1"></i>Longitud</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <button type="button" class="btn btn-success w-60" id="btn-mapa">
                                <i class="bi bi-map me-1"></i> Ver mapa
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
    const formRegistroClientePersona = document.getElementById('form-registro-cliente-persona');
    formRegistroClientePersona.addEventListener('submit', (event) => {
        event.preventDefault();

        if (confirm("¿Desea registrar este nuevo cliente?")) {
            formRegistroClientePersona.submit();
        }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>