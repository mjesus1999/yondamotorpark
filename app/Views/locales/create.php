<?php

use function App\Helpers\persistirDatosFormulario;

require_once __DIR__ . '/../../Helpers/functions.php';
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

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
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    .form-control,
    .form-select,
    textarea {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
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

    h5,
    h6 {
        font-weight: 600;
    }

    .form-floating>label {
        padding: 0.5rem 0.75rem;
    }

    .form-floating>textarea.form-control {
        height: auto;
        padding-top: 1.625rem;
    }

    .form-floating>textarea.form-control~label {
        height: auto;
    }
</style>


<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-2 " role="alert">
        <div class="row ">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="bi bi-shop me-1"></i>Locales</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-plus-circle me-1"></i>Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/locales" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta principal del formulario -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-shop-window me-2"></i>Registrar Local</h5>
        </div>
        <div class="card-body p-4">
            <form action="/locales/store" id="form-registro-local" autocomplete="off" method="POST">

                <!-- Sección 1: Ubicación -->
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

                <!-- Sección 2: Datos de tienda -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-building me-2"></i>Datos de Tienda</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idmotorpark" id="idmotorpark" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="idmotorpark"><i class="bi bi-shop me-1"></i>Tienda</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="principal" id="principal" class="form-select" required>
                                    <option value="S">Sí</option>
                                    <option value="N">No</option>
                                </select>
                                <label for="principal"><i class="bi bi-star me-1"></i>¿Es principal?</label>
                            </div>
                        </div>
                    </div>
                </div>




                <!-- Sección 4: Responsable -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-person-badge me-2"></i>Responsable</h6>
                    <div class="row g-3">

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="tienda" id="tienda" class="form-control" maxlength="40"
                                    placeholder="Nombre del local" required>
                                <label for="tienda"><i class="bi bi-tag me-1"></i>Nombre del Local</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="responsable" id="responsable" class="form-control"
                                    maxlength="100" placeholder="Responsable" required>
                                <label for="responsable"><i class="bi bi-person me-1"></i>Responsable</label>
                            </div>
                        </div>

                         <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="tel" id="telefono" name="telefono" maxlength="9" pattern="[0-9]+"
                                        class="form-control" placeholder="Teléfono" required>
                                    <label for="telefono"><i class="bi bi-phone me-1"></i>Teléfono</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="email" name="correo" id="correo" class="form-control"
                                        placeholder="Correo electrónico">
                                    <label for="correo"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
                                </div>
                            </div>


                    </div>

                   


                </div>

                <!--  Dirección -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-signpost me-2"></i>Dirección</h6>
                    <div class="form-floating">
                        <textarea name="direccion" id="direccion" rows="3" class="form-control" maxlength="300"
                            placeholder="Dirección" style="height: auto"></textarea>
                        <label for="direccion">Dirección</label>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="/assets/js/ubigeo.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", async () => {

        const motorparkSelect = document.querySelector('#idmotorpark');
        //  Para cargar Motorpark(Tienda)
        async function getMotorParkData() {
            try {
                const response = await fetch(`/api/motorpark`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                motorparkSelect.innerHTML = `<option value='' selected>Seleccione</option>`;

                if (result.success && result.motorpark && result.motorpark.length > 0) {
                    result.motorpark.forEach(element => {
                        motorparkSelect.innerHTML += `
                            <option value='${element.idmotorpark}'>${element.nombrecomercial}</option>
                        `;
                    });
                } else {
                    console.log("No se recibieron datos de motorpark o el array está vacío.");
                }
            } catch (e) {
                console.error("Error al obtener datos de motorpark:", e);
            }
        }

        getMotorParkData();

        const form = document.querySelector("#form-registro-local")
        form.addEventListener("submit", (event) => {
            event.preventDefault()

            if (confirm("¿Registramos un nuevo local?")) {
                form.submit()
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>