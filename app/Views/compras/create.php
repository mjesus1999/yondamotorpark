<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    :root {
        --card-bg: #ffffff;
        --card-border: #dee2e6;
        --text-primary: #212529;
        --text-secondary: #6c757d;
        --bg-light: #f8f9fa;
        --border-color: #dee2e6;
        --hover-bg: rgba(13, 110, 253, 0.05);
        --modal-bg: #ffffff;
        --input-bg: #ffffff;
        --table-bg: #f8f9fa;
    }

    [data-bs-theme="dark"] {
        --card-bg: #2d3748;
        --card-border: #4a5568;
        --text-primary: #e2e8f0;
        --text-secondary: #a0aec0;
        --bg-light: #1a202c;
        --border-color: #4a5568;
        --hover-bg: rgba(13, 110, 253, 0.15);
        --modal-bg: #2d3748;
        --input-bg: #2d3748;
        --table-bg: #1a202c;
    }

    .card-orden {
        transition: all 0.3s ease;
        border: 1px solid var(--card-border);
        cursor: pointer;
        background-color: var(--card-bg);
        color: var(--text-primary);
    }

    .card-orden:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        transform: translateY(-2px);
    }

    .card-orden.seleccionada {
        border-color: #0d6efd;
        background-color: var(--hover-bg);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }

    .badge-orden {
        font-size: 0.75rem;
    }

    .table-modal {
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .table-modal th {
        background-color: var(--table-bg) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }

    .table-modal td {
        border-color: var(--border-color) !important;
        color: var(--text-primary) !important;
    }

    .bg-light-custom {
        background-color: var(--bg-light) !important;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
    }

    .card {
        background-color: var(--card-bg);
        border-color: var(--card-border);
        color: var(--text-primary);
    }

    .form-control, .form-select {
        background-color: var(--input-bg);
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    .form-control:focus, .form-select:focus {
        background-color: var(--input-bg);
        border-color: #0d6efd;
        color: var(--text-primary);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .modal-content {
        background-color: var(--modal-bg);
        border-color: var(--border-color);
    }

    .modal-body {
        color: var(--text-primary);
    }

    .alert-info {
        background-color: rgba(13, 202, 240, 0.1);
        border-color: rgba(13, 202, 240, 0.2);
        color: var(--text-primary);
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    /* Animaciones mejoradas */
    .card-orden {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

  
    .resumen-card {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
        border-left: 4px solid #0d6efd;
    }

    .icon-animated {
        transition: transform 0.2s ease;
    }

    .card-orden:hover .icon-animated {
        transform: scale(1.1);
    }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Encabezado-->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h4 class="mb-0 text-primary">
                <i class="bi bi-cart-plus me-2 icon-animated"></i>Órdenes de compra > Registrar
            </h4>
        </div>
        <div>
            <a href="/compras" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <!-- Contenedor principal -->
    <div class="row">
        <!--  Órdenes de compra -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="bi bi-list-check me-2 icon-animated"></i>Órdenes Disponibles
                    </h5>

                    <div class="mb-4">
                        <label for="concesionario" class="form-label fw-bold">Concesionario</label>
                        <select name="concesionario" id="concesionario" class="form-select">
                            <option value="">Seleccione un concesionario</option>
                        </select>
                    </div>

                    <div id="detalle-container" style="display:none;">
                        <div id="detalle-content" class="row g-3"></div>
                    </div>

                    <div id="sin-ordenes" class="text-center py-4">
                        <i class="bi bi-info-circle fs-4 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Seleccione un concesionario para ver las órdenes</p>
                    </div>
                </div>
            </div>
        </div>

        <!--  Formulario y resumen -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-4">
                        <i class="bi bi-file-earmark-text me-2 icon-animated"></i>Documento de Compra
                    </h5>

                    <form action="" id="form-registro-compra" autocomplete="off" method="POST" enctype="multipart/form-data">
                        <!-- Información de la orden seleccionada -->
                        <div id="resumen-container" class="d-none mb-4">
                            <div class="alert alert-info py-3 resumen-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><i class="bi bi-check-circle text-success me-2"></i>Orden seleccionada:</strong>
                                    <span id="resumen-orden-numero" class="badge bg-primary fs-6">#112</span>
                                </div>
                            </div>

                            <div class="mb-3 p-3 bg-light-custom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="resumen-subtotal" class="fw-bold">USD 35,650.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>IGV (18%):</span>
                                    <span id="resumen-igv" class="fw-bold">USD 6,417.00</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold fs-5">
                                    <span>Total:</span>
                                    <span id="resumen-total" class="text-success">USD 42,067.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Documento -->
                        <div class="bg-light-custom p-3 mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fechacompra" class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>Fecha compra
                                    </label>
                                    <input type="datetime-local" id="fechacompra" name="fechacompra"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="tipodoc" class="form-label">
                                        <i class="bi bi-file-text me-1"></i>Tipo documento
                                    </label>
                                    <select name="tipodoc" id="tipodoc" class="form-select" required>
                                        <option value="F" selected>Factura</option>
                                        <option value="B">Boleta</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="serie" class="form-label">
                                        <i class="bi bi-hash me-1"></i>Serie
                                    </label>
                                    <input type="text" id="serie" name="serie" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="numero" class="form-label">
                                        <i class="bi bi-123 me-1"></i>Número
                                    </label>
                                    <input type="text" id="numero" name="numero" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="rutadoc" class="form-label">
                                        <i class="bi bi-paperclip me-1"></i>Adjuntar PDF
                                    </label>
                                    <input type="file" name="rutadoc" id="rutadoc" class="form-control"
                                        accept="application/pdf,image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalle de orden -->
<div class="modal fade" id="ordenCompraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Detalle de Orden #<span id="modal-orden-numero"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalOrdenContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="confirmarSeleccion">
                    <i class="bi bi-check-lg me-1"></i> Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const concesionarios = document.querySelector('#concesionario');
        const detalleContainer = document.querySelector('#detalle-container');
        const detalleContent = document.querySelector('#detalle-content');
        const sinOrdenes = document.querySelector('#sin-ordenes');
        const resumenContainer = document.querySelector('#resumen-container');
        const form = document.querySelector('#form-registro-compra');
        const fechaCompra = document.querySelector('#fechacompra');
        const tipodoc = document.querySelector('#tipodoc');
        const serie = document.querySelector('#serie');
        const numero = document.querySelector('#numero');
        const rutadoc = document.querySelector('#rutadoc');

        let idOCCompra = null;
        const ordenCompraModal = new bootstrap.Modal(document.getElementById('ordenCompraModal'));
        let ordenActual = null;
        let data = null;

        // Cargar concesionarios al iniciar
        async function getConcesionarios() {
            try {
                const res = await fetch(`/api/concesionariosDB`);
                const data = await res.json();
                concesionarios.innerHTML = '<option value="">Seleccione un concesionario</option>';
                data.forEach(element => {
                    concesionarios.innerHTML += `<option value="${element.idconcesionario}">${element.razonsocial}</option>`;
                });
            } catch (error) {
                console.error(error);
                showToast('Error al cargar concesionarios', 'ERROR', 3000);
            }
        }

        concesionarios.addEventListener('change', async (e) => {
            const id = e.target.value;
            if (!id) {
                detalleContainer.style.display = 'none';
                sinOrdenes.classList.remove('d-none');
                resumenContainer.classList.add('d-none');
                return;
            }

            try {
                const res = await fetch(`/api/detOCConcesionario/${id}`);
                data = await res.json();

                if (!data.length) {
                    detalleContainer.style.display = 'none';
                    sinOrdenes.classList.remove('d-none');
                    sinOrdenes.querySelector('p').textContent = 'No se encontraron órdenes para este concesionario';
                    showToast('No se encontraron órdenes para este concesionario', 'INFO', 2000);
                    return;
                }

                let html = '';
                data.forEach((orden, index) => {
                    const agrupados = {};
                    orden.detalle.forEach(item => {
                        const key = `${item.marca}|${item.modelo}|${item.version}|${item.combustible}|${item.anio}|${item.color}|${item.condicion}|${item.moneda}|${item.preciocompra}`;
                        agrupados[key] = agrupados[key] || { ...item, cantidad: 0 };
                        agrupados[key].cantidad += 1;
                    });

                    const totalItems = Object.values(agrupados).reduce((sum, item) => sum + item.cantidad, 0);
                    const subtotal = Object.values(agrupados).reduce((sum, item) => sum + (parseFloat(item.preciocompra) * item.cantidad), 0);
                    const igv = subtotal * 0.18;
                    const total = subtotal + igv;

                    // Verificar si esta orden está seleccionada
                    const isSelected = idOCCompra === orden.idordencompra.toString();

                    html += `
                <div class="col-md-12" style="animation-delay: ${index * 0.1}s">
                    <div class="card card-orden mb-3 ${isSelected ? 'seleccionada' : ''}" data-id="${orden.idordencompra}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary badge-orden fs-6">#${orden.idordencompra}</span>
                                <span class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>${orden.emision}
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">
                                    <i class="bi bi-car-front me-1 text-primary"></i>${totalItems} vehículos
                                </span>
                                <span class="text-success fw-bold fs-5">${orden.detalle[0].moneda} ${total.toFixed(2)}</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-primary ver-detalle" data-id="${orden.idordencompra}">
                                    <i class="bi bi-eye me-1 icon-animated"></i> Ver detalle
                                </button>
                                <button class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-secondary'} seleccionar-orden" data-id="${orden.idordencompra}">
                                    <i class="bi ${isSelected ? 'bi-check-circle-fill' : 'bi-check-circle'} me-1"></i>
                                    ${isSelected ? 'Seleccionada' : 'Seleccionar'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
                });

                detalleContent.innerHTML = html;
                detalleContainer.style.display = 'block';
                sinOrdenes.classList.add('d-none');
            } catch (error) {
                console.error('Error al cargar el detalle:', error);
                showToast('Error al cargar órdenes de compra', 'ERROR', 3000);
            }
        });

        // Mostrar detalle en modal
        detalleContent.addEventListener('click', function (e) {
            if (e.target.classList.contains('ver-detalle') || e.target.closest('.ver-detalle')) {
                const btn = e.target.classList.contains('ver-detalle') ? e.target : e.target.closest('.ver-detalle');
                const idOrden = btn.dataset.id;
                const orden = data.find(o => o.idordencompra.toString() === idOrden);

                if (orden) {
                    ordenActual = orden;
                    mostrarDetalleEnModal(orden);
                    ordenCompraModal.show();
                }
            }

            if (e.target.classList.contains('seleccionar-orden') || e.target.closest('.seleccionar-orden')) {
                const btn = e.target.classList.contains('seleccionar-orden') ? e.target : e.target.closest('.seleccionar-orden');
                const idSeleccionado = btn.dataset.id;
                seleccionarOrden(idSeleccionado, btn);
            }
        });

        function mostrarDetalleEnModal(orden) {
            const agrupados = {};
            orden.detalle.forEach(item => {
                const key = `${item.marca}|${item.modelo}|${item.version}|${item.combustible}|${item.anio}|${item.color}|${item.condicion}|${item.moneda}|${item.preciocompra}`;
                agrupados[key] = agrupados[key] || { ...item, cantidad: 0 };
                agrupados[key].cantidad += 1;
            });

            const totalItems = Object.values(agrupados).reduce((sum, item) => sum + item.cantidad, 0);
            const subtotal = Object.values(agrupados).reduce((sum, item) => sum + (parseFloat(item.preciocompra) * item.cantidad), 0);
            const igv = subtotal * 0.18;
            const total = subtotal + igv;

            let html = `
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <span class="badge bg-primary fs-6">#${orden.idordencompra}</span>
                        <span class="badge bg-secondary ms-2">
                            <i class="bi bi-calendar3 me-1"></i>${orden.emision}
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-car-front me-1"></i> ${totalItems} vehículos
                        </span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modal table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th><i class="bi bi-tag me-1"></i>Marca</th>
                                <th><i class="bi bi-car-front me-1"></i>Modelo</th>
                                <th><i class="bi bi-gear me-1"></i>Versión</th>
                                <th class="text-end"><i class="bi bi-currency-dollar me-1"></i>Precio</th>
                                <th class="text-center"><i class="bi bi-123 me-1"></i>Cant.</th>
                                <th class="text-end"><i class="bi bi-calculator me-1"></i>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;

            Object.values(agrupados).forEach(item => {
                const precio = parseFloat(item.preciocompra);
                const itemSubtotal = precio * item.cantidad;
                html += `
                <tr>
                    <td class="fw-semibold">${item.marca}</td>
                    <td>${item.modelo}</td>
                    <td>${item.version}</td>
                    <td class="text-end">${item.moneda} ${precio.toFixed(2)}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">${item.cantidad}</span>
                    </td>
                    <td class="text-end fw-bold">${item.moneda} ${itemSubtotal.toFixed(2)}</td>
                </tr>`;
            });

            html += `
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal:</th>
                                <th class="text-end">${orden.detalle[0].moneda} ${subtotal.toFixed(2)}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">IGV (18%):</th>
                                <th class="text-end">${orden.detalle[0].moneda} ${igv.toFixed(2)}</th>
                            </tr>
                            <tr class="table-success">
                                <th colspan="5" class="text-end fs-5">TOTAL:</th>
                                <th class="text-end fs-5">${orden.detalle[0].moneda} ${total.toFixed(2)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;

            document.getElementById('modal-orden-numero').textContent = orden.idordencompra;
            document.getElementById('modalOrdenContent').innerHTML = html;
        }

        function seleccionarOrden(idOrden, btnElement = null) {
            idOCCompra = idOrden;
            const orden = data.find(o => o.idordencompra.toString() === idOrden);

            // Mostrar resumen
            if (orden) {
                const agrupados = {};
                orden.detalle.forEach(item => {
                    const key = `${item.marca}|${item.modelo}|${item.version}|${item.combustible}|${item.anio}|${item.color}|${item.condicion}|${item.moneda}|${item.preciocompra}`;
                    agrupados[key] = agrupados[key] || { ...item, cantidad: 0 };
                    agrupados[key].cantidad += 1;
                });

                const totalItems = Object.values(agrupados).reduce((sum, item) => sum + item.cantidad, 0);
                const subtotal = Object.values(agrupados).reduce((sum, item) => sum + (parseFloat(item.preciocompra) * item.cantidad), 0);
                const igv = subtotal * 0.18;
                const total = subtotal + igv;

                document.getElementById('resumen-orden-numero').textContent = `#${orden.idordencompra}`;
                document.getElementById('resumen-subtotal').textContent = `${orden.detalle[0].moneda} ${subtotal.toFixed(2)}`;
                document.getElementById('resumen-igv').textContent = `${orden.detalle[0].moneda} ${igv.toFixed(2)}`;
                document.getElementById('resumen-total').textContent = `${orden.detalle[0].moneda} ${total.toFixed(2)}`;

                resumenContainer.classList.remove('d-none');
            }

            // Actualizar botones y cards - 
            setTimeout(() => {
                document.querySelectorAll('.card-orden').forEach(card => {
                    const cardId = card.dataset.id;
                    const seleccionarBtn = card.querySelector('.seleccionar-orden');

                    if (cardId === idOrden) {
                        card.classList.add('seleccionada');
                        if (seleccionarBtn) {
                            seleccionarBtn.classList.remove('btn-outline-secondary');
                            seleccionarBtn.classList.add('btn-success');
                            seleccionarBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Seleccionada';
                        }
                    } else {
                        card.classList.remove('seleccionada');
                        if (seleccionarBtn) {
                            seleccionarBtn.classList.remove('btn-success');
                            seleccionarBtn.classList.add('btn-outline-secondary');
                            seleccionarBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Seleccionar';
                        }
                    }
                });
            }, 100);

            showToast(`Orden #${idOCCompra} seleccionada correctamente`, 'SUCCESS', 1200);
        }

        // Confirmar selección desde el modal
        document.getElementById('confirmarSeleccion').addEventListener('click', () => {
            if (ordenActual) {
                seleccionarOrden(ordenActual.idordencompra.toString());
                ordenCompraModal.hide();
            }
        });

        // Manejar el envío del formulario
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!idOCCompra) {
                showToast('Debe seleccionar una orden de compra primero', 'WARNING', 1200);
                return;
            }

            const formData = new FormData(form);
            formData.append('idorden', idOCCompra);
            formData.append('fechacompra', fechaCompra.value);
            formData.append('tipodoc', tipodoc.value);
            formData.append('serie', serie.value);
            formData.append('numdocumento', numero.value);
            if (rutadoc.files[0]) {
                formData.append('rutadoc', rutadoc.files[0]);
            }

            if (confirm('¿Está seguro de registrar esta compra?')) {
                try {
                    const response = await fetch('/compras/store', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error('Error al registrar la compra');
                    }

                    const result = await response.json();
                    showToast(result.message, 'SUCCESS', 1200);
                    form.reset();
                    resumenContainer.classList.add('d-none');
                    idOCCompra = null;
                    detalleContainer.style.display = 'none';
                    concesionarios.value = '';
                    detalleContent.innerHTML = '';
                    sinOrdenes.classList.remove('d-none');
                    sinOrdenes.querySelector('p').textContent = 'Seleccione un concesionario para ver las órdenes';
                } catch (error) {
                    console.error(error);
                    showToast(error.message || 'Error al registrar la compra', 'WARNING', 1200);
                }
            }
        });

        await getConcesionarios();
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>