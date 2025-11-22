<?php include __DIR__ . '/../layout/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    :root {
        /* Modo Claro */
        --ts-bg: #fff;
        --ts-border: #dee2e6;
        --ts-text: #212529;
        --ts-dropdown-bg: #f8f9fa;
        --ts-option-hover: #e9ecef;
        --ts-active-bg: #0d6efd;
        --ts-active-text: #fff;
        --ts-selected-item-bg: #e9ecef;
        --ts-selected-item-text: #212529;
    }

    /* * Sobrescribe las variables de color cuando el sistema
    * está en modo oscuro.
    */
    @media (prefers-color-scheme: dark) {
        :root {
            --ts-bg: #212529;
            --ts-border: #495057;
            --ts-text: #adb5bd;
            --ts-dropdown-bg: #343a40;
            --ts-option-hover: #495057;
            --ts-active-bg: #0d6efd;
            --ts-active-text: #fff;
            --ts-selected-item-bg: #495057;
            --ts-selected-item-text: #fff;
        }
    }

    /* * Aplica las variables de color a los selectores de Tom-Select.
    */
    .ts-wrapper .ts-control {
        background-color: var(--ts-bg);
        border-color: var(--ts-border);
        color: var(--ts-text);
    }

    .ts-wrapper.focus .ts-control {
        border-color: #86b7fe;
        /* Color de foco por defecto de Bootstrap */
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .ts-wrapper .ts-dropdown {
        background-color: var(--ts-dropdown-bg);
        border-color: var(--ts-border);
    }

    .ts-wrapper .ts-dropdown .option {
        color: var(--ts-text);
    }

    .ts-wrapper .ts-dropdown .option:hover,
    .ts-wrapper .ts-dropdown .active {
        background-color: var(--ts-active-bg);
        color: var(--ts-active-text);
    }

    .ts-wrapper .ts-dropdown .option.selected {
        background-color: var(--ts-selected-item-bg);
        color: var(--ts-selected-item-text);
    }

    /* Estilo para el item seleccionado en el control */
    .ts-wrapper .ts-control .item {
        background-color: transparent;
        border: none;
        color: gray;
        padding: 0;

        font-weight: 650;
    }
</style>

<div class="container-fluid">
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
        <div class="col-12 col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card card-header fs-3 bg-primary text-white">
                    <i class="bi bi-file-earmark-plus me-2"> Registrar Egreso</i>
                </div>
                <div class="card-body p-3">
                    <form id="egresoForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="concepto" class="form-label fw-semibold">Concepto de Egreso <span class="text-danger">*</span></label>
                                <select id="concepto" name="idconceptoegreso" class="form-select" required>
                                    <option value="">Seleccione un concepto</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="colaborador" class="form-label fw-semibold">Colaborador que Solicita <span class="text-danger">*</span></label>
                                <select id="colaborador" name="idsolicitante" class="form-select" required></select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label fw-semibold">Monto (S/) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="text" id="monto" name="monto" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4 pt-2">
                                    <input class="form-check-input" type="checkbox" id="requiereComprobante" name="requiereComprobante">
                                    <label class="form-check-label fw-semibold" for="requiereComprobante">
                                        ¿Requiere Comprobante?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="comprobanteFormContainer" style="display: none;" class="card border-primary p-4 mt-4">
                            <h4 class="text-primary fw-bold p-3 mb-3 "><i class="bi bi-receipt"></i> Datos del Comprobante</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipodoc" class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                    <select id="tipodoc" name="tipodoc" class="form-select">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="B">Boleta</option>
                                        <option value="F">Factura</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="proovedor" class="form-label fw-semibold">Proveedor <span class="text-danger">*</span></label>
                                    <select id="proovedor" name="idproovedor" class="form-select"></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="serie" class="form-label fw-semibold">Serie <span class="text-danger">*</span></label>
                                    <input type="text" id="serie" name="serie" class="form-control" placeholder="Ej: B001">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="numdocumento" class="form-label fw-semibold">Número de Documento <span class="text-danger">*</span></label>
                                    <input type="text" id="numdocumento" name="numdocumento" class="form-control" placeholder="Ej: 00001234">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="monto_comprobante" class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input type="text" id="monto_comprobante" name="monto_comprobante" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="rutacomprobante" class="form-label fw-semibold">Subir Archivo <span class="text-danger">*</span></label>
                                    <input type="file" id="rutacomprobante" name="rutacomprobante" class="form-control" accept=".pdf">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                            <textarea id="observaciones" name="comentario" rows="3" class="form-control" placeholder="Ingrese observaciones adicionales (opcional)..."></textarea>
                        </div>



                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
                            <button type="submit" class="btn btn-outline-primary">
                                Registrar Egreso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const egresoForm = document.getElementById('egresoForm');
        const requiereComprobanteCheckbox = document.getElementById('requiereComprobante');
        const comprobanteFormContainer = document.getElementById('comprobanteFormContainer');
        const montoEgresoInput = document.getElementById('monto');
        const montoComprobanteInput = document.getElementById('monto_comprobante');
        const tomSelects = {};

        requiereComprobanteCheckbox.addEventListener('change', () => {
            comprobanteFormContainer.style.display = requiereComprobanteCheckbox.checked ? 'block' : 'none';
            if (requiereComprobanteCheckbox.checked) {
                montoComprobanteInput.value = montoEgresoInput.value;
            }
        });

        montoEgresoInput.addEventListener('input', () => {
            if (requiereComprobanteCheckbox.checked) {
                montoComprobanteInput.value = montoEgresoInput.value;
            }
        });

        async function loadAndInitTomSelect(url, selectId, valueField, labelField, searchField, customRender = null) {
            try {
                const response = await fetch(url);
                const result = await response.json();
                if (result.status) {
                    const selectElement = document.getElementById(selectId);
                    const config = {
                        create: false,
                        sortField: {
                            field: "$score",
                            direction: "desc"
                        },
                        valueField: valueField,
                        labelField: labelField,
                        searchField: searchField,
                        options: result.data
                    };
                    if (customRender) {
                        config.render = customRender;
                    }
                    tomSelects[selectId] = new TomSelect(selectElement, config);
                } else {
                    console.error(`Error al cargar datos de ${url}:`, result.message);
                }
            } catch (error) {
                console.error(`Error en la solicitud para ${url}:`, error);
            }
        }

        loadAndInitTomSelect('/api/egreso/colaboradores', 'colaborador', 'idcolaborador', 'colaborador', 'colaborador');

        const proveedorRenderOptions = {
            option: (data, escape) => {
                return `<div>${escape(data.nombrecomercial)} <span class="text-white small">(${escape(data.ruc)})</span></div>`;
            },
            item: (data, escape) => {
                return `<div>${escape(data.nombrecomercial)} - <span class="text-gray p-1 small"> (${escape(data.ruc)})</span></div>`;
            }
        };

        loadAndInitTomSelect(
            '/api/egreso/proovedores',
            'proovedor',
            'idproovedor',
            'nombrecomercial',
            ['nombrecomercial', 'ruc'],
            proveedorRenderOptions
        );

        async function loadConceptos() {
            try {
                const response = await fetch('/api/egreso/conceptos');
                const result = await response.json();
                if (result.status) {
                    const selectConcepto = document.getElementById('concepto');
                    result.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.idconceptoegreso;
                        option.textContent = item.descripcion;
                        selectConcepto.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error al cargar conceptos:', error);
            }
        }

        loadConceptos();

        egresoForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const conceptoId = document.getElementById('concepto').value;
            const colaboradorId = document.getElementById('colaborador').value;
            const monto = montoEgresoInput.value.trim();
            const observaciones = document.getElementById('observaciones').value.trim();
            const requiereComprobante = requiereComprobanteCheckbox.checked;

            if (!conceptoId || !colaboradorId || !monto || isNaN(monto) || parseFloat(monto) <= 0) {
                showToast('Por favor, complete los campos principales de egreso.', 'WARNING', 1300);
                return;
            }

            const formData = new FormData();
            formData.append('idconceptoegreso', conceptoId);
            formData.append('idsolicitante', colaboradorId);
            formData.append('monto', parseFloat(monto).toFixed(2));
            formData.append('comentario', observaciones);
            formData.append('requierecomprobante', requiereComprobante ? 'S' : 'N');

            if (requiereComprobante) {
                const tipoDoc = document.getElementById('tipodoc').value;
                const proovedorId = document.getElementById('proovedor').value;
                const serie = document.getElementById('serie').value.trim();
                const numDocumento = document.getElementById('numdocumento').value.trim();
                const montoComprobante = montoComprobanteInput.value.trim();
                const archivoComprobante = document.getElementById('rutacomprobante').files[0];

                if (!tipoDoc || !proovedorId || !serie || !numDocumento || !montoComprobante || isNaN(montoComprobante) || !archivoComprobante) {
                    showToast('Por favor, complete todos los campos del comprobante y adjunte el archivo.', 'WARNING', 2000);
                    return;
                }

                formData.append('idproovedor', proovedorId);
                formData.append('tipodoc', tipoDoc);
                formData.append('serie', serie);
                formData.append('numdocumento', numDocumento);
                formData.append('monto_comprobante', parseFloat(montoComprobante).toFixed(2));
                formData.append('rutacomprobante', archivoComprobante);
            }

            if (await ask('¿Registrar egreso?', 'Confirmar')) {
                try {
                    const response = await fetch('/egreso/store', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message, 'SUCCESS', 1200);
                        if (tomSelects['colaborador']) tomSelects['colaborador'].clear();
                        if (tomSelects['proovedor']) tomSelects['proovedor'].clear();
                        egresoForm.reset();
                        comprobanteFormContainer.style.display = 'none';
                    } else {
                        showToast(result.message || 'Error en la solicitud. Intente nuevamente.', 'ERROR', 2000);
                    }
                } catch (error) {
                    showToast('Error en la solicitud. Intente nuevamente.', 'ERROR', 2000);
                    console.error('Error:', error);
                }
            }
        });

        document.getElementById('monto').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        document.getElementById('monto_comprobante').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
    });
</script>