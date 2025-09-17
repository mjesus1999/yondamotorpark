<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid px-4">
    <div class="alert alert-primary mt-3 border-0 shadow-sm" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Egresos
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end mt-2 mt-md-0">
                <a class="btn btn-outline-primary btn-sm shadow-sm" href="/egreso">
                    <i class="fas fa-list me-1"></i>Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-lg my-4">
                <div class="card-header bg-gradient text-body text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h2 class="card-title mb-0 fw-bold">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Registro de Egreso
                    </h2>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form id="egresoForm">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="concepto" class="form-label fw-semibold text-muted mb-2">
                                    <i class="fas fa-tag me-1"></i>Concepto de Egreso
                                </label>
                                <select id="concepto" name="concepto" class="form-select form-select-lg shadow-sm border-0 bg-light" required>
                                    <option value="">Seleccione un concepto</option>
                                    <option value="1"> Sueldos y Salarios</option>
                                    <option value="2">Compra de Insumos</option>
                                    <option value="3"> Pago de Servicios</option>
                                    <option value="4"> Gastos de Viaje</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="colaborador" class="form-label fw-semibold text-muted mb-2">
                                    <i class="fas fa-user me-1"></i>Colaborador que Solicita
                                </label>
                                <select id="colaborador" name="colaborador" class="form-select form-select-lg shadow-sm border-0 bg-light" required>
                                    <option value="">Seleccione un colaborador</option>
                                    <option value="101"> Juan Pérez</option>
                                    <option value="102"> María Rodríguez</option>
                                    <option value="103"> Carlos Gómez</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="monto" class="form-label fw-semibold text-muted mb-2">
                                    <i class="fas fa-dollar-sign me-1"></i>Monto (S/)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 shadow-sm">S/</span>
                                    <input type="text" id="monto" name="monto"  class="form-control form-control-lg shadow-sm border-0 bg-light" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch mt-4 mt-md-0">
                                    <input class="form-check-input" type="checkbox" id="requiereComprobante" name="requiereComprobante" style="transform: scale(1.3);">
                                    <label class="form-check-label fw-semibold ms-2" for="requiereComprobante">
                                        <i class="fas fa-receipt me-1"></i>¿Requiere Comprobante?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="comprobanteSection" class="d-none">
                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-invoice me-2"></i>Información del Comprobante
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tipoDoc" class="form-label fw-semibold text-muted">Tipo de Documento</label>
                                            <select id="tipoDoc" name="tipoDoc" class="form-select shadow-sm border-0 bg-light">
                                                <option value="B">Boleta</option>
                                                <option value="F">Factura</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rucProveedor" class="form-label fw-semibold text-muted">RUC del Proveedor</label>
                                            <input type="text" id="rucProveedor" name="rucProveedor" class="form-control shadow-sm border-0 bg-light" pattern="[0-9]{11}" placeholder="12345678901">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="serie" class="form-label fw-semibold text-muted">Serie</label>
                                            <input type="text" id="serie" name="serie" class="form-control shadow-sm border-0 bg-light" placeholder="B001">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="numDocumento" class="form-label fw-semibold text-muted">Número de Documento</label>
                                            <input type="text" id="numDocumento" name="numDocumento" class="form-control shadow-sm border-0 bg-light" placeholder="00001234">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivoComprobante" class="form-label fw-semibold text-muted">
                                            <i class="fas fa-upload me-1"></i>Subir Comprobante
                                        </label>
                                        <input type="file" id="archivoComprobante" name="archivoComprobante" class="form-control shadow-sm border-0 bg-light">
                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-sticky-note me-1"></i>Observaciones
                            </label>
                            <textarea id="observaciones" name="observaciones" rows="4" class="form-control shadow-sm border-0 bg-light" placeholder="Ingrese observaciones adicionales (opcional)..."></textarea>
                        </div>

                        <div class="d-flex align-items-end gap-1 justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary btn-sm ">Cancelar</button>
                            <button type="submit" class="btn btn-outline-primary btn-sm shadow-lg">
                                Registrar Egreso
                            </button>
                        </div>
                    </form>
                    <div id="messageBox" class="alert mt-4 d-none shadow-sm border-0" role="alert"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requiereComprobanteCheckbox = document.getElementById('requiereComprobante');
        const comprobanteSection = document.getElementById('comprobanteSection');
        const egresoForm = document.getElementById('egresoForm');
        const messageBox = document.getElementById('messageBox');

        // Animación suave para mostrar/ocultar la sección de comprobante
        requiereComprobanteCheckbox.addEventListener('change', function() {
            if (this.checked) {
                comprobanteSection.classList.remove('d-none');
                comprobanteSection.style.opacity = '0';
                comprobanteSection.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    comprobanteSection.style.transition = 'all 0.3s ease-in-out';
                    comprobanteSection.style.opacity = '1';
                    comprobanteSection.style.transform = 'translateY(0)';
                }, 10);
            } else {
                comprobanteSection.style.transition = 'all 0.3s ease-in-out';
                comprobanteSection.style.opacity = '0';
                comprobanteSection.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    comprobanteSection.classList.add('d-none');
                }, 300);
            }
        });

        // Efectos hover para los elementos del formulario
        const formElements = document.querySelectorAll('.form-control, .form-select');
        formElements.forEach(element => {
            element.addEventListener('focus', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.2s ease-in-out';
                this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.15)';
            });

            element.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            });
        });

        // Simular el envío del formulario con animación
        egresoForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Animación del botón de envío
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

            // Recopilar los datos del formulario
            const formData = {
                concepto: document.getElementById('concepto').value,
                colaborador: document.getElementById('colaborador').value,
                monto: document.getElementById('monto').value,
                requiereComprobante: requiereComprobanteCheckbox.checked,
                observaciones: document.getElementById('observaciones').value,
            };

            if (requiereComprobanteCheckbox.checked) {
                formData.comprobante = {
                    tipoDoc: document.getElementById('tipoDoc').value,
                    rucProveedor: document.getElementById('rucProveedor').value,
                    serie: document.getElementById('serie').value,
                    numDocumento: document.getElementById('numDocumento').value,
                };
            }

            // Simular delay de procesamiento
            setTimeout(() => {
                console.log("Datos a enviar:", formData);
                showMessage("✅ Egreso registrado con éxito", "success");
                egresoForm.reset();

                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                // Ocultar sección de comprobante si estaba visible
                if (!comprobanteSection.classList.contains('d-none')) {
                    comprobanteSection.classList.add('d-none');
                }
            }, 2000);
        });

        // Función para mostrar mensajes con animación
        function showMessage(message, type) {
            messageBox.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>${message}</span>
                </div>
            `;
            messageBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-info');
            messageBox.classList.add(`alert-${type}`);

            // Animación de entrada
            messageBox.style.opacity = '0';
            messageBox.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                messageBox.style.transition = 'all 0.3s ease-in-out';
                messageBox.style.opacity = '1';
                messageBox.style.transform = 'translateY(0)';
            }, 10);

            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                messageBox.style.opacity = '0';
                messageBox.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    messageBox.classList.add('d-none');
                }, 300);
            }, 5000);
        }

        // Validación en tiempo real para RUC
        const rucInput = document.getElementById('rucProveedor');
        if (rucInput) {
            rucInput.addEventListener('input', function() {
                const ruc = this.value;
                if (ruc && !/^\d{11}$/.test(ruc)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>