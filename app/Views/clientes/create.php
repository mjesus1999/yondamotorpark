<?php

use function App\Helpers\persistirDatosFormulario;

require_once __DIR__ . '/../../Helpers/functions.php'; ?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../components/mapa-includes.php'; ?>
<?php include __DIR__ . '/../components/mapa-modal.php'; ?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive"
            aria-atomic="true" id="errorToast">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $error ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
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

    /* Estilos para el wizard de pasos */
    .step-indicator {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        min-width: 120px;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
    }

    .step-title {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
        text-align: center;
        transition: all 0.3s ease;
    }

    .step-indicator.active .step-number {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        transform: scale(1.1);
    }

    .step-indicator.active .step-title {
        color: #0d6efd;
        font-weight: 600;
    }

    .step-indicator.completed .step-number {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }

    .step-indicator.completed .step-title {
        color: #198754;
    }

    .step-indicator.completed .step-number::before {
        content: "✓";
        font-size: 14px;
    }

    .step-line {
        width: 60px;
        height: 2px;
        background-color: #e9ecef;
        margin: 0 10px;
        margin-top: -12px;
        transition: all 0.3s ease;
    }

    .step-line.completed {
        background-color: #198754;
    }

    /* Estilos para los pasos del formulario */
    .form-step {
        display: none;
        animation: fadeIn 0.4s ease-in-out;
    }

    .form-step.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Mejoras visuales para las cards internas */
    .form-step .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-step .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    /* Estilos para searchable select */
    .searchable-select-container {
        position: relative;
    }

    .searchable-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .searchable-select-dropdown.show {
        display: block;
    }

    .searchable-select-dropdown .dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s;
    }

    .searchable-select-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .searchable-select-dropdown .dropdown-item.selected {
        background-color: #0d6efd;
        color: white;
    }

    .searchable-select-dropdown .dropdown-item.hidden {
        display: none;
    }

    .searchable-select-input.has-value {
        background-color: #f8f9fa;
    }

    /* Indicadores de validación */
    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.86 1.86 3.75-3.75.94.94-4.69 4.69z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4m0-2.4L5.8 7'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .step-indicator {
            min-width: 80px;
        }

        .step-number {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .step-title {
            font-size: 11px;
        }

        .step-line {
            width: 40px;
        }
    }
</style>


<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-3 rounded-3 shadow-sm" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i
                                    class="bi bi-people me-1"></i>Clientes (Normales)</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i
                                class="bi bi-person-plus me-1"></i>Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/clientes/" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Formulario con diseño de pasos -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Registrar Cliente (Persona)</h6>
                <div class="progress" style="width: 200px; height: 8px;">
                    <div class="progress-bar bg-secondary" role="progressbar" id="form-progress" style="width: 33%"></div>
                </div>
            </div>
        </div>

        <!-- Indicador de pasos -->
        <div class="card-body p-0">
            <div class="d-flex justify-content-center py-3 bg-body border-bottom">
                <div class="d-flex align-items-center">
                    <div class="step-indicator active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-title">Identificación</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-indicator" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-title">Ubicación</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-indicator" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-title">Detalles</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="/storepersonclient/store" id="form-registro-cliente-persona" autocomplete="off" method="POST">
                <input type="hidden" name="idcliente" id="idcliente">

                <!-- PASO 1: Identificación y Datos Básicos -->
                <div class="form-step active" id="step-1">
                    <div class="row">
                        <!-- Columna izquierda: Documento -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-card-text me-2"></i>Documento de Identidad</h6>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <select name="tipodocumento" id="tipodocumento" class="form-select" required>
                                                    <option value="DNI" selected>DNI</option>
                                                    <option value="CEX">Carnet de extranjería</option>
                                                    <option value="PAS">Pasaporte</option>
                                                </select>
                                                <label for="tipodocumento">Tipo de documento <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="input-group">
                                                <div class="form-floating flex-grow-1">
                                                    <input type="text" name="nrodoc" id="ndocumento" class="form-control"
                                                        placeholder="Ingrese el número" maxlength="12" required>
                                                    <label for="ndocumento">Número de documento <span class="text-danger">*</span></label>
                                                </div>
                                                <button type="button" id="btnBuscarCliente" class="btn btn-success px-3"
                                                    title="Buscar en RENIEC/Base de datos">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </div>
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Presiona el botón para buscar automáticamente los datos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Datos personales -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Datos Personales</h6>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" id="apellidos" name="apellidos" class="form-control"
                                                    placeholder="Apellidos completos" required>
                                                <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" name="nombres" id="nombres" class="form-control"
                                                    placeholder="Nombres completos" required>
                                                <label for="nombres">Nombres <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <select name="genero" id="genero" class="form-select" required>
                                                    <option value="">Seleccione</option>
                                                    <option value="M">Masculino</option>
                                                    <option value="F">Femenino</option>
                                                </select>
                                                <label for="genero">Género <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Ubicación y Contacto -->
                <div class="form-step" id="step-2">
                    <div class="row">
                        <!-- Columna izquierda: Ubicación -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-geo-alt me-2"></i>Ubicación Geográfica</h6>

                                    <div class="mb-4">

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <select name="departamento" id="departamento" class="form-select" required>
                                                        <option value="">Seleccione</option>
                                                    </select>
                                                    <label for="departamento"><i class="bi bi-map me-1"></i>Departamento</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <select name="provincia" id="provincia" class="form-select" required>
                                                        <option value="">Seleccione</option>
                                                    </select>
                                                    <label for="provincia"><i class="bi bi-map me-1"></i>Provincia</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <select name="distrito" id="distrito" class="form-select" required>
                                                        <option value="">Seleccione</option>
                                                    </select>
                                                    <label for="distrito"><i class="bi bi-map me-1"></i>Distrito</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Contacto -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-telephone me-2"></i>Información de Contacto</h6>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="tel" name="telprimario" id="telprimario" class="form-control"
                                                    maxlength="9" pattern="[0-9]+" placeholder="Teléfono principal" required>
                                                <label for="telprimario">Teléfono principal <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Ingrese solo números (9 dígitos)
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="tel" name="telalternativo" id="telalternativo" class="form-control"
                                                    maxlength="9" pattern="[0-9]+" placeholder="Teléfono alternativo">
                                                <label for="telalternativo">Teléfono alternativo (Opcional)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Dirección y Coordenadas -->
                <div class="form-step" id="step-3">
                    <div class="row">
                        <!-- Columna izquierda: Dirección -->
                        <div class="col-lg-8">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-house me-2"></i>Dirección de Residencia</h6>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" name="direccion" id="direccion" class="form-control"
                                                    placeholder="Ej: Av. Los Pinos 123, Urb. San Carlos">
                                                <label for="direccion">Dirección completa (Opcional)</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" name="referencia" id="referencia" class="form-control"
                                                    placeholder="Ej: Frente al parque, al lado de la farmacia">
                                                <label for="referencia">Referencia (Opcional)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Coordenadas -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 bg-body">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="bi bi-geo me-2"></i>Coordenadas GPS</h6>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" name="latitud" id="latitud" class="form-control"
                                                    placeholder="Latitud">
                                                <label for="latitud">Latitud</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" name="longitud" id="longitud" class="form-control"
                                                    maxlength="40" placeholder="Longitud">
                                                <label for="longitud">Longitud</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-success w-100" id="btn-mapa">
                                                <i class="bi bi-map me-2"></i>Seleccionar en Mapa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de navegación -->
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" id="btn-anterior" style="display: none;">
                        <i class="bi bi-arrow-left me-1"></i> Anterior
                    </button>

                    <div class="ms-auto d-flex gap-2">
                        <button type="reset" class="btn btn-outline-secondary" id="btn-cancelar">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-siguiente">
                            Siguiente <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-outline-primary" id="btn-registrar" style="display: none;">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/ubigeo.js" defer></script>
<script>
    // Variables globales para el wizard
    let currentStep = 1;
    const totalSteps = 3;

    // Referencias a elementos del DOM
    const formRegistroClientePersona = document.getElementById('form-registro-cliente-persona');
    const btnSiguiente = document.getElementById('btn-siguiente');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnRegistrar = document.getElementById('btn-registrar');
    const progressBar = document.getElementById('form-progress');

    // Función para actualizar el progreso visual
    function updateProgress() {
        const progress = (currentStep / totalSteps) * 100;
        progressBar.style.width = progress + '%';
    }

    // Función para actualizar los indicadores de pasos
    function updateStepIndicators() {
        const stepIndicators = document.querySelectorAll('.step-indicator');
        const stepLines = document.querySelectorAll('.step-line');

        stepIndicators.forEach((indicator, index) => {
            const stepNumber = index + 1;
            indicator.classList.remove('active', 'completed');

            if (stepNumber < currentStep) {
                indicator.classList.add('completed');
            } else if (stepNumber === currentStep) {
                indicator.classList.add('active');
            }
        });

        stepLines.forEach((line, index) => {
            line.classList.remove('completed');
            if (index + 1 < currentStep) {
                line.classList.add('completed');
            }
        });
    }

    // Función para mostrar el paso actual
    function showStep(step) {
        // Ocultar todos los pasos
        document.querySelectorAll('.form-step').forEach(stepElement => {
            stepElement.classList.remove('active');
        });

        // Mostrar el paso actual
        const currentStepElement = document.getElementById(`step-${step}`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }

        // Actualizar botones
        updateButtons();
        updateProgress();
        updateStepIndicators();
    }

    // Función para actualizar la visibilidad de los botones
    function updateButtons() {
        // Botón anterior
        if (currentStep === 1) {
            btnAnterior.style.display = 'none';
        } else {
            btnAnterior.style.display = 'inline-block';
        }

        // Botón siguiente y registrar
        if (currentStep === totalSteps) {
            btnSiguiente.style.display = 'none';
            btnRegistrar.style.display = 'inline-block';
        } else {
            btnSiguiente.style.display = 'inline-block';
            btnRegistrar.style.display = 'none';
        }
    }

    // Función para validar el paso actual
    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            // Limpiar clases previas
            field.classList.remove('is-valid', 'is-invalid');

            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.add('is-valid');
            }
        });

        // Validaciones específicas por paso
        if (currentStep === 1) {
            // Validar documento
            const tipoDoc = document.getElementById('tipodocumento').value;
            const nroDoc = document.getElementById('ndocumento').value;

            if (tipoDoc === 'DNI' && nroDoc.length !== 8) {
                document.getElementById('ndocumento').classList.add('is-invalid');
                isValid = false;
            }
        }

        if (currentStep === 2) {
            // Validar teléfono
            const telefono = document.getElementById('telprimario').value;
            if (telefono && telefono.length !== 9) {
                document.getElementById('telprimario').classList.add('is-invalid');
                isValid = false;
            }
        }

        return isValid;
    }

    // Event listeners para navegación
    btnSiguiente.addEventListener('click', () => {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        } else {
            // Mostrar mensaje de error
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'warning',
                title: 'Por favor, complete todos los campos obligatorios'
            });
        }
    });

    btnAnterior.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Validación en tiempo real
    document.addEventListener('input', (e) => {
        if (e.target.matches('input[required], select[required]')) { // matchea los campos que tengan el atributo required
            const field = e.target;
            field.classList.remove('is-valid', 'is-invalid');

            if (field.value.trim()) {
                field.classList.add('is-valid');
            }
        }
    });

    // Inicializar el wizard
    document.addEventListener('DOMContentLoaded', () => {
        showStep(1);
    });

    // Manejo del envío del formulario
    formRegistroClientePersona.addEventListener('submit', async (event) => {
        event.preventDefault();

        // Validar todos los pasos
        let allValid = true;
        for (let step = 1; step <= totalSteps; step++) {
            const tempCurrentStep = currentStep;
            currentStep = step;
            if (!validateCurrentStep()) {
                allValid = false;
                showStep(step); // Ir al primer paso con errores
                break;
            }
            currentStep = tempCurrentStep;
        }

        if (allValid) {
            if (await ask("¿Registrar cliente?", 'Confirmar cliente')) {
                formRegistroClientePersona.submit();
            }
        } else {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'error',
                title: 'Hay errores en el formulario. Por favor, revise todos los campos.'
            });
        }
    });

    // funciones para realizar la busqueda por DNI
    const btnBuscarCliente = document.getElementById('btnBuscarCliente');
    const ndocumento = document.getElementById('ndocumento');
    const apellidos = document.getElementById('apellidos');
    const nombres = document.getElementById('nombres');

    // Función para mostrar loading en el botón
    function setLoadingButton(loading = true) {
        if (loading) {
            btnBuscarCliente.disabled = true;
            btnBuscarCliente.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        } else {
            btnBuscarCliente.disabled = false;
            btnBuscarCliente.innerHTML = '<i class="bi bi-search"></i>';
        }
    }

    // Función para limpiar campos
    function limpiarCampos() {
        apellidos.value = '';
        nombres.value = '';
    }

    // Función para llenar campos
    function llenarCampos(data) {
        if (data.apellidos) apellidos.value = data.apellidos;
        if (data.nombres) nombres.value = data.nombres;

        apellidos.focus();
    }

    // Función para buscar por DNI
    async function buscarPorDNI() {
        const dni = ndocumento.value.trim();
        //console.log('Iniciando búsqueda para DNI:', dni);

        if (!dni) {
            //showToast('Por favor, ingrese un DNI', 'warning');
            alert('Por favor, ingrese un DNI');
            ndocumento.focus();
            return;
        }

        setLoadingButton(true);
        limpiarCampos();

        try {
            // Hacer petición AJAX
            const response = await fetch(`/persona/searchByDNIApi?dni=${dni}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            //console.log('Response status:', response.status); // Debug

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const textResponse = await response.text();
            //console.log('Raw response:', textResponse); // Debug

            let data;
            try {
                data = JSON.parse(textResponse);
            } catch (parseError) {
                //console.error('Error parsing JSON:', parseError);
                //console.log('Response text:', textResponse);
                throw new Error('Respuesta inválida del servidor');
            }

            //console.log('Parsed data:', data); // Debug

            if (data && data.success) {
                llenarCampos(data);

                if (data.source === 'local') {
                    //showToast('Persona encontrada en base de datos local', 'info');
                    console.log('Persona encontrada en base de datos local');
                    if (data.idpersona) {
                        setTimeout(() => {
                            //showToast('Esta persona ya está registrada', 'warning');
                            alert('Esta persona ya está registrada');
                        }, 2000);
                    }
                } else if (data.source === 'api') {
                    //showToast('Datos obtenidos de RENIEC ✓', 'success');
                    console.log('Datos obtenidos de RENIEC');
                }
            } else {
                const errorMessage = data && data.message ? data.message : 'No se encontró información para este DNI';
                console.log('Error message to show:', errorMessage);
                //showToast(errorMessage, 'danger');
                alert(errorMessage);
            }

        } catch (error) {
            console.error('Error en la búsqueda:', error);
            //showToast('Error de conexión. Intente nuevamente', 'danger');
            alert('Error de conexión. Intente nuevamente');
        } finally {
            setLoadingButton(false);
        }
    }

    btnBuscarCliente.addEventListener('click', buscarPorDNI);

    ndocumento.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarPorDNI();
        }
    });

    ndocumento.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');

        if (this.value.length > 8) {
            this.value = this.value.substring(0, 8);
        }
    });

    // Capturar DNI de la URL y prellenar el campo
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const dniFromUrl = urlParams.get('dni');

        if (dniFromUrl) {
            const ndocumentoField = document.getElementById('ndocumento');
            if (ndocumentoField) {
                ndocumentoField.value = dniFromUrl;

            }
        }
    });

    //PARAMETRO DE RETORNO PARA LA COTIZACION
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay parámetros en la URL para prellenar datos
        const urlParams = new URLSearchParams(window.location.search);
        const dniParam = urlParams.get('dni');
        const tipoParam = urlParams.get('tipo');
        const returnTo = urlParams.get('return_to');

        // Si viene de cotización, prellenar el documento
        if (dniParam && tipoParam) {
            const tipoDocSelect = document.getElementById('tipodocumento');
            const nrodocInput = document.getElementById('nrodoc');

            if (tipoDocSelect && nrodocInput) {
                tipoDocSelect.value = tipoParam.toUpperCase();
                nrodocInput.value = dniParam;

                // Trigger change event para actualizar placeholders si es necesario
                const changeEvent = new Event('change', {
                    bubbles: true
                });
                tipoDocSelect.dispatchEvent(changeEvent);

                // Focus en el campo nombres
                const nombresInput = document.getElementById('nombres');
                if (nombresInput) {
                    setTimeout(() => nombresInput.focus(), 100);
                }
            }
        }

        // Si viene de cotización, mostrar un mensaje informativo
        if (returnTo === 'cotizacion') {
            showToast('Complete el registro del cliente para continuar con la cotización', 'INFO', 3000);
        }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>