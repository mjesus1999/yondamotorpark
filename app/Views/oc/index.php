<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav
                    style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                    aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Órdenes de compra</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/oc/create" class="btn btn-outline-primary btn-sm">Registrar</a>
            </div>
        </div>
    </div>


    <div class="card">

        <div class="card-header">
            <?php $estadoActual = $estado ?? ''; ?>
            <div class="btn-group m-1" id="botones-filtro">
                <a href="/oc/listar/emitido"
                    class="btn btn-sm  <?= $estadoActual === 'emitido' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Emitido
                </a>

                <a href="/oc/listar/proceso"
                    class="btn btn-sm <?= $estadoActual === 'proceso' ? 'btn-warning' : 'btn-outline-warning' ?>">
                    Proceso
                </a>


                <a href="/oc/listar/pagado"
                    class="btn btn-sm <?= $estadoActual === 'pagado' ? 'btn-success' : 'btn-outline-success' ?>">
                    Pagado
                </a>

                <a href="/oc/listar/anulado"
                    class="btn btn-sm <?= $estadoActual === 'anulado' ? 'btn-danger' : 'btn-outline-danger' ?>">
                    Anulado
                </a>
            </div>

        </div>
        
        <div class="card-body" id="lista-oc">

            <!-- Vista de ESCRITORIO-->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-sm table-hover" id="tabla-oc">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número</th>
                            <th>Concesionario</th>
                            <th>Fecha</th>
                            <th>Moneda</th>

                            <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                <th>Total</th>
                                <th>Amortización</th>
                                <th>Saldo</th>
                            <?php endif; ?>

                            <?php if ($estadoActual == 'emitido'): ?>
                                <th>Operaciones</th>
                            <?php elseif ($estadoActual == 'proceso'): ?>
                                <th>Pagar</th>
                            <?php elseif ($estadoActual == 'pagado'): ?>
                                <th>Detalles</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordenCompras)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay ordénes de compras registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($ordenCompras as $ordenCompra): ?>
                                <tr>
                                    <td><?= $numeroFila++ ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['serie']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['razonsocial']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['emision']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['moneda']) ?></td>

                                    <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                        <td><?= number_format($ordenCompra['totalOC'] ?? 0, 2) ?></td>
                                        <td><?= number_format($ordenCompra['totalPagado'] ?? 0, 2) ?></td>
                                        <td><?= number_format($ordenCompra['saldoRestante'] ?? 0, 2) ?></td>
                                    <?php endif; ?>

                                    <!-- Acciones según estado -->
                                    <td>
                                        <?php include 'acciones_oc.php'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista de MÓVIL (Acordeón) -->
            <div class="d-block d-md-none">
                <?php if (!empty($ordenCompras)): ?>
                    <?php $numeroFila = 1; ?>
                    <?php foreach ($ordenCompras as $ordenCompra): ?>
                        <div class="card mb-2 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapse-oc-<?= $ordenCompra['idordencompra'] ?>" style="cursor: pointer;">
                                <span><i class="bi bi-receipt me-2 text-primary fw-bold"></i><?= htmlspecialchars($ordenCompra['serie']) ?> - <?= htmlspecialchars($ordenCompra['razonsocial']) ?></span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div id="collapse-oc-<?= $ordenCompra['idordencompra'] ?>" class="collapse">
                                <div class="card-body">
                                    <p><strong>#:</strong> <?= $numeroFila++ ?></p>
                                    <p><strong>Fecha:</strong> <?= htmlspecialchars($ordenCompra['emision']) ?></p>
                                    <p><strong>Moneda:</strong> <?= htmlspecialchars($ordenCompra['moneda']) ?></p>

                                    <?php if ($estadoActual == 'proceso' || $estadoActual == 'pagado'): ?>
                                        <p><strong>Total:</strong> <?= number_format($ordenCompra['totalOC'] ?? 0, 2) ?></p>
                                        <p><strong>Amortización:</strong> <?= number_format($ordenCompra['totalPagado'] ?? 0, 2) ?></p>
                                        <p><strong>Saldo:</strong> <?= number_format($ordenCompra['saldoRestante'] ?? 0, 2) ?></p>
                                    <?php endif; ?>

                                    <!-- Acciones -->
                                    <div><strong>Acciones:</strong><br>
                                        <?php include 'acciones_oc.php'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center">No hay ordénes de compras registradas.</div>
                <?php endif; ?>
            </div>

        </div>



        <div class="card-footer" id="detalle-oc" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div style="padding: 1rem;">

                        <h3 id="detail-concesionario-razon-social"></h3>
                        <h5 id="detail-oc-summary"></h5>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end" style="padding-right: 1.5rem;">
                    <button type="button" id="btn-volver" class="btn btn-outline-primary btn-sm">Volver</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabla-detalles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Versión</th>
                            <th>Combustible</th>
                            <th>Año</th>
                            <th>Chasis</th>
                            <th>Serie</th>
                            <th>Placa</th>
                            <th>Placa Rotativa</th>
                            <th>Color</th>
                            <th>Moneda</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div> <!-- ./table-responsive -->
        </div> <!-- ./card-footer -->

        <!-- Zona modales -->
        <div class="modal fade" id="modal-oc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-oc" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="" autocomplete="off" id="formulario-oc">
                    <div class="modal-content">
                        <div class="modal-header bg-yonda">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Estado de los vehículos</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="tabla-autos-modal">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Auto</th>
                                            <th>Cantidad</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se llenarán los autos dinámicamente -->
                                    </tbody>
                                </table>

                                <div class="mt-3 text-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="escorrecto" value="S" id="escorrecto">
                                        <label class="form-check-label">SI</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="escorrecto" value="N" checked id="escorrecto">
                                        <label class="form-check-label">NO</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="modal fade" id="modal-proceso" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="form-proceso">
                    <div class="modal-content">
                        <div class="modal-header bg-yonda">
                            <h5 class="modal-title" id="modal-proceso-titulo">Observaciones</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="id-oc-proceso">
                            <input type="hidden" id="modal-proceso-ruta">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea id="observaciones" class="form-control" rows="4" placeholder=""></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div> <!-- ./card -->

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const botonesFiltro = document.querySelectorAll("#botones-filtro .btn")
            const enlacesDetalle = document.querySelectorAll(".show-details")
            const botonVolver = document.querySelector("#btn-volver")
            const speedAnimation = 750;

            // Referencias a los contenedores principales
            const listaOc = document.getElementById('lista-oc');
            const ocDetailView = document.getElementById('detalle-oc');

            // Referencias a elementos dentro de la vista de detalle
            const detailConcesionarioRazonSocial = document.getElementById('detail-concesionario-razon-social');
            const detailOcSummary = document.getElementById('detail-oc-summary');
            const tablaDetallesBody = document.getElementById('tabla-detalles').querySelector('tbody');
            const btnVolver = document.getElementById('btn-volver');

            // Selecciona todos los enlaces con la clase 'show-details'
            const detailLinks = document.querySelectorAll('.show-details');

            // Variables para el modal de verificar si los autos llegaron bien
            const tablaAutosModalBody = document.querySelector("#tabla-autos-modal tbody");
            const modalOc = new bootstrap.Modal(document.getElementById('modal-oc'));
            const formularioOc = document.getElementById("formulario-oc");

            // Campos para abrir el modal de proceso o anulado:
            const modalProceso = new bootstrap.Modal(document.getElementById("modal-proceso"));
            const formProceso = document.getElementById("form-proceso");
            const inputIdOc = document.getElementById("id-oc-proceso");
            const inputRuta = document.getElementById("modal-proceso-ruta");
            const inputObs = document.getElementById("observaciones");
            const modalTitulo = document.getElementById("modal-proceso-titulo");


            let idOC = null; // Para identifcar el idoc a actualizar desde el modal para verificar si los autos llegarón de acuerdo a la OC

            formularioOc.addEventListener("submit", async (e) => {
                e.preventDefault();

                const valorSeleccionado = document.querySelector('input[name="escorrecto"]:checked').value;

                if (confirm('¿Actualizar el detalle?')) {
                    formData = new FormData();
                    formData.append('escorrecto', valorSeleccionado);

                    try {
                        const res = await fetch(`/oc/update/${idOC}`, {
                            method: 'POST',
                            body: formData
                        })

                        const data = await res.json();

                        if (data.success) {
                            modalOc.hide();
                            showToast(data.message, 'SUCCESS', 1000);
                            // setTimeout(() => location.reload(), 1000);

                        } else {
                            modalOc.hide();
                            showToast(data.message, 'WARNING', 1000);
                        }

                    } catch (error) {
                        console.error(error);
                    }

                }

            });


            document.querySelectorAll(".btn-abrir-modal-estado").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const idOC = btn.dataset.id;
                    const accion = btn.dataset.accion;
                    const ruta = btn.dataset.ruta;

                    inputIdOc.value = idOC;
                    inputRuta.value = ruta;
                    inputObs.value = "";

                    // Cambiar el título dinámicamente
                    modalTitulo.textContent = (accion === "proceso") ?
                        "Observaciones - Confirmar OC" :
                        "Observaciones - Anular OC";

                    modalProceso.show();
                });
            });

            // Enviar el formulario
            formProceso.addEventListener("submit", async (e) => {
                e.preventDefault();
                const ruta = inputRuta.value;
                const obs = inputObs.value.trim();

                if (!obs) {
                    alert("Por favor ingrese el motivo antes de continuar.");
                    return;
                }

                if (confirm('¿Esta seguro de actualizar el estado de la OC?'))

                {
                    try {
                        const res = await fetch(ruta, {
                            method: "POST",
                            body: new URLSearchParams({
                                observaciones: obs
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            modalProceso.hide();
                            showToast(data.message, "SUCCESS", 1200);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast(data.message, "WARNING", 1200);
                        }
                    } catch (error) {
                        console.error(error);
                        alert("Hubo un error al actualizar.");
                    }

                }

            });


            // Para el modal de check

            document.querySelectorAll("a[data-idocmodal]").forEach(icono => {
                icono.addEventListener("click", async (e) => {
                    e.preventDefault();
                    idOC = e.currentTarget.dataset.idocmodal;
                    if (!idOC) return;

                    try {
                        const response = await fetch(`/api/oc/infoAutos/${idOC}`);
                        if (!response.ok) throw new Error("Error al obtener los autos");

                        const autos = await response.json();
                        if (!autos || autos.length === 0) {
                            tablaAutosModalBody.innerHTML = `<tr><td colspan="4" class="text-center">No hay autos para esta orden</td></tr>`;
                        } else {
                            tablaAutosModalBody.innerHTML = "";
                            autos.sort((a, b) => a.auto.localeCompare(b.auto));

                            autos.forEach((item, index) => {
                                const row = document.createElement("tr");
                                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.auto}</td>
                            <td>${item.cantidad}</td>
                        `;
                                tablaAutosModalBody.appendChild(row);
                            });
                        }

                        modalOc.show();

                    } catch (error) {
                        console.error(error);
                        alert("No se pudieron cargar los datos.");
                    }
                });
            });


            function limpiarVistaDetalle() {
                // Limpiar el nombre del concesionario
                if (detailConcesionarioRazonSocial) {
                    detailConcesionarioRazonSocial.textContent = '';
                }

                // Limpiar el resumen de la OC (número, fecha, moneda, total)
                if (detailOcSummary) {
                    detailOcSummary.textContent = '';
                }

                // Limpiar la tabla de detalles (vehículos)
                if (tablaDetallesBody) {
                    tablaDetallesBody.innerHTML = '';
                }
            }

            // Evento para eventos clikc de detalles
            detailLinks.forEach(link => {
                link.addEventListener('click', async (event) => {
                    event.preventDefault();

                    const enlace = event.currentTarget || event.target.closest('a');
                    const ocId = enlace.dataset.idoc;

                    if (!ocId) {
                        console.warn('ID de Orden de Compra no encontrado en el enlace de detalle.');
                        return;
                    }

                    limpiarVistaDetalle();

                    const apiUrl = `/api/oc/${ocId}`;

                    try {
                        const response = await fetch(apiUrl);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        // Validar si el nuevo JSON tiene la estructura esperada
                        if (!data || !data.orden || !Array.isArray(data.vehiculos)) {
                            showToast('No hay datos válidos para la OC', 'WARNING', 1200);
                            return;
                        }

                        const {
                            orden,
                            vehiculos
                        } = data;

                        // Mostrar vista
                        $("#lista-oc").slideUp(speedAnimation);
                        $("#detalle-oc").slideDown(speedAnimation);

                        // Llenar encabezado
                        if (detailConcesionarioRazonSocial && detailOcSummary) {
                            detailConcesionarioRazonSocial.textContent = orden.concesionario.razon_social || 'N/A';

                            const numeroOc = orden.numero_oc_formateado || 'N/A';
                            const fechaEmision = orden.fecha_emision_oc || 'N/A';
                            const moneda = orden.moneda_oc || 'N/A';
                            const total = orden.totales.total ? parseFloat(orden.totales.total).toFixed(2) : '0.00';

                            detailOcSummary.textContent = `${numeroOc} | ${fechaEmision} | ${moneda} ${total}`;
                        }

                        // Llenar tabla
                        if (tablaDetallesBody) {
                            tablaDetallesBody.innerHTML = '';

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
                        }

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



    <?php include __DIR__ . '/../layout/footer.php'; ?>