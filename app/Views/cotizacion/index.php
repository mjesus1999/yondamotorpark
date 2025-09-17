<?php include __DIR__ . '/../layout/header.php'; ?>

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
                    <!-- <i class="bi bi-plus"></i> -->Registrar
                </a>
            </div>
        </div>
    </div>

    <!-- <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
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
                <a href="/cotizacion/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Registrar
                </a>
            </div>
        </div>
    </div> -->

    <!-- Información de cotizaciones activas -->
    <!-- <div class="alert alert-success" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i>
            <span><strong>Cotizaciones Activas:</strong> Mostrando cotizaciones vigentes (dentro del período de validez).</span>
            <?php if (count($cotizaciones) > 0): ?>
                <span class="ms-2 badge bg-success"><?= count($cotizaciones) ?> cotizaciones activas</span>
            <?php endif; ?>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
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
                                                        <!-- <small class="text-secondary">
                                                            @<?= htmlspecialchars($c['asesor_usuario']) ?>
                                                        </small> -->
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No hay cotizaciones activas</h4>
                            <p class="text-muted">No se encontraron cotizaciones vigentes. Las cotizaciones vencidas se
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

<!-- Modal para confirmar descarga -->
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar DataTable
        initDataTableCotizacion();

        // Modal functionality
        const downloadModal = new bootstrap.Modal(document.getElementById('downloadModal'));
        let currentUrl = '';
        let currentClientName = '';

        // Manejar clicks en los botones de descarga (icono PDF en la tabla)
        document.querySelectorAll('.btn-download-pdf').forEach(button => {
            button.addEventListener('click', function (e) {
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
        document.getElementById('confirmDownload').addEventListener('click', function () {
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
                        order: [[0, 'desc']], // Ordenar por # descendente
                        pagingType: 'full_numbers',
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
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
                        columnDefs: [
                            {
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
                            <?php if ($puede_ver_todas): ?>
                                , {
                                    targets: 7, // Registrado por
                                    width: "15%"
                                },
                                {
                                    targets: 8, // Opciones
                                    width: "5%"
                                }
                            <?php else: ?>
                                , {
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