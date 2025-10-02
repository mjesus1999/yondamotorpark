<?php include __DIR__ . '/../layout/header.php'; ?>
<?php
// var_dump($estadoActual) ;
// var_dump($puede_ver_todas);
?>
<div class="container-fluid">

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/historial" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-clock-history"></i> Historial
                </a>
                <a href="/cotizacion/create" class="btn btn-outline-primary btn-sm">
                    Registrar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <?php

                    $estadoActual = $estadoActual ?? 'P';
                    ?>
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="btn-group m-1 mb-2" id="botones-filtro">

                            <a href="/cotizacion/P"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'P' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                <i class="bi bi-clock"></i> Pendientes
                            </a>

                            <a href="/cotizacion/A"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'A' ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="bi bi-check-circle"></i> Aprobadas
                            </a>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($cotizaciones) > 0): ?>
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-cotizacion">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Modalidad</th>
                                    <th>Inicial</th>
                                    <th class="text-start">N° de Doc</th>
                                    <th class="text-start">Teléfono</th>
                                    <?php if ($puede_ver_todas): ?>
                                        <th>Registrado por</th>
                                    <?php endif; ?>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($cotizaciones as $index => $c): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($c['nombrecliente']) ?></td>

                                        <td>
                                            <?php
                                            $vehiculo = $c['vehiculo'] ?? ($c['marcaVehiculo'] ?? '') . '/' . ($c['modeloVehiculo'] ?? '') . '/' . ($c['anio'] ?? '');
                                            echo htmlspecialchars($vehiculo);
                                            ?>
                                        </td>

                                        <td><?= htmlspecialchars($c['tipocotizacion'] ?? '') ?></td>

                                        <td>
                                            <?php
                                            if (isset($c['inicial']) && $c['inicial'] !== '') {
                                                $mon = $c['moneda'] ?? 'PEN';
                                                $symbol = ($mon === 'USD') ? '$' : 'S/';
                                                $formatted = number_format((float) $c['inicial'], 2, '.', ',');
                                                echo htmlspecialchars($symbol . ' ' . $formatted);
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-start"><?= htmlspecialchars($c['documento']) ?></td>
                                        <td class="text-start"><?= htmlspecialchars($c['telefono']) ?></td>

                                        <?php if ($puede_ver_todas): ?>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <small class="fw-bold text-primary">
                                                        <?= htmlspecialchars($c['asesor_nombre'] ?? 'Sin asignar') ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($c['asesor_cargo'] ?? '') ?>
                                                    </small>
                                                    <?php if (!empty($c['asesor_usuario'])): ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>

                                        <td class="text-end">
                                            <a href="/cotizacion/reporte/<?= $c['idcotizacion'] ?>" class="p-1 btn-download-pdf"
                                                data-id="<?= $c['idcotizacion'] ?>"
                                                data-cliente="<?= htmlspecialchars($c['nombrecliente']) ?>"
                                                title="PDF Cotización">
                                                <i class="bi bi-filetype-pdf text-danger fs-5"></i>
                                            </a>

                                            <?php
                                            if ($estadoActual == 'P'):
                                            ?>

                                            
                                                <a href="/fichasolicitud/<?= htmlspecialchars($c['idcotizacion'])?>"><i class="bi bi-file-earmark-plus fs-5 text-warning fw-bold" title="Adjuntar Ficha"></i></a>
                                                <a
                                                    title="Aprobar Cotización"
                                                    data-id="<?= $c['idcotizacion'] ?>"
                                                    data-nombre-cliente="<?= htmlspecialchars($c['nombrecliente']) ?>"
                                                    data-inicial="<?= htmlspecialchars(number_format((float) $c['inicial'], 2, '.', ',')); ?>"
                                                    data-vehiculo="<?= htmlspecialchars($c['vehiculo']) ?>"
                                                    data-marca="<?= htmlspecialchars($c['marcaVehiculo']) ?>"
                                                    data-modelo="<?= htmlspecialchars($c['modeloVehiculo']) ?>"
                                                    data_anio="<?= htmlspecialchars($c['anio']) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#aprobarModal">
                                                    <i class="bi bi-check2-circle text-primary fs-5"></i>
                                                </a>

                                                <?php

                                                ?>

                                            <?php endif; ?>


                                            <?php if ($c['estadocotizacion'] == 'A'): ?>
                                                <a
                                                    class="text-info fw-bold btnCrearContrato"
                                                    title="Crear Contrato"
                                                    data-id="<?= $c['idcotizacion'] ?>"
                                                    data-cliente="<?= htmlspecialchars($c['nombrecliente']) ?>"
                                                    data-numcuotas="<?= $c['numcuotas'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#contratoModal">
                                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                                </a>
                                            <?php endif; ?>




                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">
                                No hay cotizaciones
                                <?php
                                if (isset($estadoActual) && strtoupper($estadoActual) === 'A') {
                                    echo 'Aprobadas';
                                } else {
                                    echo 'Pendientes/Activas';
                                }
                                ?>
                            </h4>
                            <p class="text-muted">No se encontraron cotizaciones con el estado seleccionado. Las cotizaciones vencidas se
                                pueden ver en el historial.</p>
                            <div class="mt-3">
                                <a href="/cotizacion/create" class="btn btn-primary me-2">
                                    <i class="bi bi-plus"></i> Nueva Cotización
                                </a>
                                <a href="/cotizacion/historial" class="btn btn-outline-secondary">
                                    <i class="bi bi-clock-history"></i> Ver Historial
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">Ver PDF de la Cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea ver la cotización de <strong id="cliente-name"></strong>?</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="openInNewTab" checked>
                    <label class="form-check-label" for="openInNewTab">
                        Abrir en nueva pestaña
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmDownload">Ver PDF</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aprobarModalLabel"><i class="bi bi-check-circle-fill me-2"></i> Confirmar Aprobación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea <strong>"Aprobar"</strong> la cotización de <strong id="cliente-aprobar-nombre"></strong>?</p>
                <p class="text-danger fw-bold small">Esta acción no se puede deshacer fácilmente.</p>
                <input type="hidden" id="cotizacion-aprobar-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="confirmarAprobarBtn">
                    Aprobar
                </button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="contratoModal" tabindex="-1" aria-labelledby="contratoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCrearContrato">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="contratoModalLabel"><i class="bi bi-file-earmark-text me-2"></i> Nuevo Contrato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idcotizacion" id="contrato-idcotizacion" value="">

                    <p>Creando contrato para cotización ID: <strong id="contrato-cotizacion-id-display"></strong></p>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="idlocal" class="form-label">Local</label>
                            <select class="form-control" id="idlocal" name="idlocal" required>
                                <option value="">Seleccione Local</option>
                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="fechainicio" class="form-label">Fecha Inicio Contrato</label>
                            <input type="date" class="form-control" id="fechainicio" name="fechainicio" required value="<?= date('Y-m-d') ?>">
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="diapago" class="form-label">Día de Pago (Mensual)</label>
                            <input type="number" class="form-control" id="diapago" name="diapago" min="1" max="31" required value="<?= date('d') ?>">
                            <small class="form-text text-muted">Día del mes en el que se efectuará cada pago.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="fechainicio" class="form-label">Fecha revisión</label>
                            <input type="date" class="form-control" id="fecharevision" name="fechainicio" required value="<?= date('Y-m-d') ?>">
                        </div>

                    </div>




                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarContrato">
                        <i class="bi bi-save"></i> Guardar y Generar Cronograma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {


        const aprobarModalElement = document.getElementById('aprobarModal');
        const cotizacionAprobarIdInput = document.getElementById('cotizacion-aprobar-id');
        const clienteAprobarNombreStrong = document.getElementById('cliente-aprobar-nombre');
        const confirmarAprobarBtn = document.getElementById('confirmarAprobarBtn');

        const selectLocal = document.getElementById('idlocal');



        async function getLocalesActivos() {
            try {
                const response = await fetch('/api/localesActivos', {
                    method: 'GET'
                });
                const locales = await response.json();
                // console.log(locales);

                locales.forEach(el => {
                    selectLocal.innerHTML += `<option value="${el.idlocal}">${el.local}</option>`;
                });

            } catch (error) {
                console.log('Error al obtener locales activos:', error);
            }
        }
        getLocalesActivos();




        const inputIdCotizacion = document.getElementById('contrato-idcotizacion');
        const displayIdCotizacion = document.getElementById('contrato-cotizacion-id-display');

        document.querySelectorAll('.btnCrearContrato').forEach(btn => {
            btn.addEventListener('click', function() {
                const idCotizacion = this.dataset.id;
                const cliente = this.dataset.cliente;
                const numCuotas = this.dataset.numcuotas;

                inputIdCotizacion.value = idCotizacion;
                displayIdCotizacion.textContent = `${idCotizacion} - ${cliente} (${numCuotas} cuotas)`;
            });
        });

        document.getElementById('formCrearContrato').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("/contrato/store", {
                    method: "POST",
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert("Contrato creado con ID " + data.idcontrato);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(err => console.error("Error:", err));
        });















        const aprobarModal = new bootstrap.Modal(aprobarModalElement);

        aprobarModalElement.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const clienteNombre = button.getAttribute('data-nombre-cliente');

            cotizacionAprobarIdInput.value = id;
            clienteAprobarNombreStrong.textContent = clienteNombre;
        });


        confirmarAprobarBtn.addEventListener('click', function() {
            const idcotizacion = cotizacionAprobarIdInput.value;
            aprobarModal.hide();
            fetch(`/cotizacion/aprobar/${idcotizacion}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al aprobar la cotización. Código: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {

                        showToast(data.message, 'SUCCESS', 1200);
                        setTimeout(() => {
                            window.location.reload();

                        }, 1260);
                    } else {
                        showToast('Error: ' + data.message, 'ERROR', 1200);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error de conexión al aprobar la cotización.');
                });
        });












        // Inicializar DataTable
        initDataTableCotizacion();

        // Modal functionality
        const downloadModal = new bootstrap.Modal(document.getElementById('downloadModal'));
        let currentUrl = '';
        let currentClientName = '';


        // Manejar clicks en los botones de descarga (icono PDF en la tabla)
        document.querySelectorAll('.btn-download-pdf').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const cotizacionId = this.getAttribute('data-id');
                const clienteName = this.getAttribute('data-cliente');

                currentUrl = `/cotizacion/reporte/${cotizacionId}`;
                currentClientName = clienteName;
                document.getElementById('cliente-name').textContent = clienteName;

                downloadModal.show();
            });
        });

        // Utilidad para agregar ?preview=1 de forma robusta
        function withPreview(url) {
            try {
                const u = new URL(url, window.location.origin);
                u.searchParams.set('preview', '1');
                return u.toString();
            } catch (e) {
                return url + (url.includes('?') ? '&' : '?') + 'preview=1';
            }
        }

        // Confirmar (abrir vista previa del PDF)
        document.getElementById('confirmDownload').addEventListener('click', function() {
            const openInNewTab = document.getElementById('openInNewTab').checked;
            const target = withPreview(currentUrl);

            if (openInNewTab) {
                window.open(target, '_blank', 'noopener,noreferrer');
            } else {
                window.location.href = target;
            }

            downloadModal.hide();
        });
    });

    function initDataTableCotizacion() {
        if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            console.warn('jQuery or DataTables not loaded yet');
            return;
        }

        const table = document.getElementById('tabla-cotizacion');
        if (!table) {
            console.warn('Table tabla-cotizacion not found');
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable('#tabla-cotizacion')) {
                $('#tabla-cotizacion').DataTable().clear().destroy();
            }

            setTimeout(() => {
                const tableElement = document.getElementById('tabla-cotizacion');
                if (tableElement && tableElement.parentNode) {
                    $('#tabla-cotizacion').DataTable({
                        order: [
                            [0, 'desc']
                        ], // Ordenar por # descendente
                        pagingType: 'full_numbers',
                        pageLength: 10,
                        lengthMenu: [
                            [5, 10, 25, 50, -1],
                            [5, 10, 25, 50, "Todos"]
                        ],
                        /* scrollX: true, */
                        destroy: true,
                        responsive: true,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json",
                            paginate: {
                                first: '«',
                                previous: '‹',
                                next: '›',
                                last: '»'
                            }
                        },
                        columnDefs: [{
                                targets: -1, // Última columna (Opciones)
                                orderable: false, // No permitir ordenamiento
                                searchable: false // No incluir en búsqueda
                            },
                            {
                                targets: 0, // # 
                                width: "3%"
                            },
                            {
                                targets: 1, // Cliente
                                width: "20%"
                            },
                            {
                                targets: 2, // Vehículo
                                width: "18%"
                            },
                            {
                                targets: 3, // Modalidad
                                width: "12%"
                            },
                            {
                                targets: 4, // Inicial
                                width: "10%"
                            },
                            {
                                targets: 5, // N° Doc
                                width: "8%"
                            },
                            {
                                targets: 6, // Teléfono
                                width: "8%"
                            }
                            <?php if ($puede_ver_todas): ?>, {
                                    targets: 7, // Registrado por
                                    width: "15%"
                                },
                                {
                                    targets: 8, // Opciones
                                    width: "5%"
                                }
                            <?php else: ?>, {
                                    targets: 7, // Opciones
                                    width: "5%"
                                }
                            <?php endif; ?>
                        ]
                    });
                }
            }, 100);
        } catch (error) {
            console.error('Error initializing cotizacion table:', error);
        }
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>