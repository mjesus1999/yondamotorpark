<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Compras</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/compras/create">Registrar</a>
            </div>
        </div>
    </div>
<!-- Lista principal -->
<div id="lista-oc">

    <!-- Vista de ESCRITORIO -->
    <div class="d-none d-md-block">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="tabla-compras">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Concesionario</th>
                                <th>Fecha entrega</th>
                                <th>Fecha recepción</th>
                                <th>Tipo doc</th>
                                <th>Serie</th>
                                <th>Número</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($compras)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay compras registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($compras as $compra): ?>
                                    <tr>
                                        <td><?= $numeroFila++ ?></td>
                                        <td><?= htmlspecialchars($compra['razon_concesionario']) ?></td>
                                        <td><?= htmlspecialchars($compra['fechacompra']) ?></td>
                                        <td><?= htmlspecialchars($compra['fecharecepcion'] ?? 'N/A') ?></td>
                                        <td><?= $compra['tipodoc'] == 'B' ? 'Boleta' : 'Factura' ?></td>
                                        <td><?= htmlspecialchars($compra['serie']) ?></td>
                                        <td><?= htmlspecialchars($compra['numdocumento']) ?></td>
                                        <td>
                                            <a href="#" class="show-details" data-idoc="<?= $compra['idorden'] ?>" title="Ver detalle">
                                                <i class="bi bi-info-circle text-primary fs-5"></i>
                                            </a>
                                            <?php if (!empty($compra['rutadoc'])): ?>
                                                <a href="#" class="ver-documento" data-url="/archivos/<?= htmlspecialchars($compra['rutadoc']) ?>" title="Ver Documento">
                                                    <i class="fa-solid fa-file-invoice  fs-5" style="color: #ff0000;"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- VISTA MÓVIL: Acordeón colapsable -->
    <div class="d-block d-md-none">
        <?php if (!empty($compras)) : ?>
            <?php $numeroFila = 1; ?>
            <?php foreach ($compras as $compra): ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center"
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseCompra<?= $compra['idorden'] ?>"
                         aria-expanded="false"
                         aria-controls="collapseCompra<?= $compra['idorden'] ?>"
                         style="cursor:pointer;">
                        <span><i class="bi bi-truck me-2 text-primary fw-bold"></i><?= htmlspecialchars($compra['razon_concesionario']) ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div id="collapseCompra<?= $compra['idorden'] ?>" class="collapse">
                        <div class="card-body">
                            <p><strong>#:</strong> <?= $numeroFila++ ?></p>
                            <p><strong>Fecha entrega:</strong> <?= htmlspecialchars($compra['fechacompra']) ?></p>
                            <p><strong>Fecha recepción:</strong> <?= htmlspecialchars($compra['fecharecepcion'] ?? 'N/A') ?></p>
                            <p><strong>Tipo documento:</strong> <?= $compra['tipodoc'] == 'B' ? 'Boleta' : 'Factura' ?></p>
                            <p><strong>Serie:</strong> <?= htmlspecialchars($compra['serie']) ?></p>
                            <p><strong>Número:</strong> <?= htmlspecialchars($compra['numdocumento']) ?></p>
                            <p>
                                <strong>Acciones:</strong><br>
                                <a href="#" class="show-details" data-idoc="<?= $compra['idorden'] ?>" title="Ver detalle">
                                    <i class="bi bi-info-circle text-primary fs-5 me-2"></i>
                                </a>
                                <?php if (!empty($compra['rutadoc'])): ?>
                                    <a href="#" class="ver-documento" data-url="/archivos/<?= htmlspecialchars($compra['rutadoc']) ?>" title="Ver Documento">
                                        <i class="fa-solid fa-file-invoice fa-beat fs-5" style="color: #ff0000;"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">N/A</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-muted p-3">No hay compras registradas.</div>
        <?php endif; ?>
    </div>
</div>



</div>

    <!-- Detalle OC (inicialmente oculto) -->
    <div id="detalle-oc" style="display: none;">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 id="detalle-concesionario-razon"></h5>
                    <h6 id="detalle-oc-summary" class="text-muted"></h6>
                </div>
                <button id="btn-volver-detalle" class="btn btn-outline-secondary btn-sm">Volver</button>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm" id="tabla-detalle-oc">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Versión</th>
                            <th>Combustible</th>
                            <th>Año</th>
                            <th>Chasis</th>
                            <th>Motor</th>
                            <th>Placa</th>
                            <th>Rotativa</th>
                            <th>Color</th>
                            <th>Moneda</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light">
                    <h5 class="modal-title fw-bold" id="modalFacturaLabel">Factura PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" style="height: 80vh;">
                    <iframe id="visorFactura" src="" width="100%" height="100%" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>


    <?php include __DIR__ . '/../layout/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const detailLinks = document.querySelectorAll('.show-details');
            const detailConcesionarioRazonSocial = document.getElementById('detalle-concesionario-razon');
            const detailOcSummary = document.getElementById('detalle-oc-summary');
            const tablaDetallesBody = document.querySelector('#tabla-detalle-oc tbody');
            const speedAnimation = 850;
            const botonVolver = document.getElementById('btn-volver-detalle');

            // Variables para poder visualizar la factura:            
            const links = document.querySelectorAll('.ver-documento');
            const modal = new bootstrap.Modal(document.getElementById('modalFactura'));
            const iframe = document.getElementById('visorFactura');


            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pdfUrl = this.dataset.url;
                    if (pdfUrl) {
                        iframe.src = pdfUrl;
                        modal.show();
                    }
                });
            });



            function limpiarVistaDetalle() {
                if (tablaDetallesBody) tablaDetallesBody.innerHTML = '';
                if (detailConcesionarioRazonSocial) detailConcesionarioRazonSocial.textContent = '';
                if (detailOcSummary) detailOcSummary.textContent = '';
            }

            detailLinks.forEach(link => {
                link.addEventListener('click', async (event) => {
                    event.preventDefault();
                    const ocId = event.currentTarget.dataset.idoc;

                    if (!ocId) {
                        console.warn('ID de Orden de Compra no encontrado.');
                        return;
                    }

                    limpiarVistaDetalle();

                    try {
                        const response = await fetch(`/api/oc/${ocId}`);
                        if (!response.ok) throw new Error(`Error ${response.status}`);
                        const data = await response.json();

                        if (!data || !data.orden || !Array.isArray(data.vehiculos)) {
                            showToast('No hay datos válidos para la OC', 'WARNING', 1200);
                            return;
                        }

                        const {
                            orden,
                            vehiculos
                        } = data;

                        $("#lista-oc").slideUp(speedAnimation);
                        $("#detalle-oc").slideDown(speedAnimation);

                        detailConcesionarioRazonSocial.textContent = orden.concesionario.razon_social || 'N/A';
                        const numeroOc = orden.numero_oc_formateado || 'N/A';
                        const fechaEmision = orden.fecha_emision_oc || 'N/A';
                        const moneda = orden.moneda_oc || 'N/A';
                        const total = orden.totales.total ? parseFloat(orden.totales.total).toFixed(2) : '0.00';
                        detailOcSummary.textContent = `${numeroOc} | ${fechaEmision} | ${moneda} ${total}`;

                        vehiculos.forEach((vehiculo, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${vehiculo.marca || 'N/A'}</td>
                            <td>${vehiculo.modelo || 'N/A'}</td>
                            <td>${vehiculo.version || 'N/A'}</td>
                            <td>${vehiculo.combustible || 'N/A'}</td>
                            <td>${vehiculo.anio_modelo || 'N/A'}</td>
                            <td>${vehiculo.chasis || 'N/A'}</td>
                            <td>${vehiculo.serie_motor || 'N/A'}</td>
                            <td>${vehiculo.placa || 'N/A'}</td>
                            <td>${vehiculo.placa_rotativa || 'N/A'}</td>
                            <td>${vehiculo.color || 'N/A'}</td>
                            <td>${orden.moneda_oc || 'N/A'}</td>
                            <td>${vehiculo.precio_unitario ? parseFloat(vehiculo.precio_unitario).toFixed(2) : '0.00'}</td>
                        `;
                            tablaDetallesBody.appendChild(row);
                        });

                    } catch (error) {
                        console.error(error);
                        showToast('No se ha podido cargar los datos', 'WARNING', 1200);
                    }
                });
            });

            if (botonVolver) {
                botonVolver.addEventListener('click', () => {
                    limpiarVistaDetalle();
                    $("#detalle-oc").slideUp(speedAnimation);
                    $("#lista-oc").slideDown(speedAnimation);
                });
            }



        });
    </script>