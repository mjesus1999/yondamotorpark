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

    <div class="alert alert-warning mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/cotizacion">Cotización</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Historial</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion" class="btn btn-outline-primary btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Volver a Cotizaciones Activas
                </a>
                <!-- <a href="/cotizacion/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Registrar
                </a> -->
            </div>
        </div>
    </div>

    <!-- Información del historial -->
    <div class="alert alert-info" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2"></i>
            <span><strong>Historial de Cotizaciones:</strong> Mostrando cotizaciones vencidas (que han superado su
                período de vigencia).</span>
            <?php if (count($cotizaciones) > 0): ?>
                <span class="ms-2 badge bg-secondary"><?= count($cotizaciones) ?> cotizaciones encontradas</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php if (count($cotizaciones) > 0): ?>
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-cotizacion-historial">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Modalidad</th>
                                    <th>Inicial</th>
                                    <th class="text-start">N° de Doc</th>
                                    <th class="text-start">Teléfono</th>
                                    <th class="text-center">Fecha Vencimiento</th>
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

                                        <!-- Fecha de vencimiento con indicador visual -->
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                                <?php
                                                $fechaVenc = $c['fecha_vencimiento'] ?? '';
                                                if ($fechaVenc) {
                                                    $fecha = DateTime::createFromFormat('Y-m-d', $fechaVenc);
                                                    echo $fecha ? $fecha->format('d/m/Y') : $fechaVenc;
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </span>
                                        </td>

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
                                            <!-- <button type="button" class="btn btn-sm btn-outline-success P-1 btn-reactivate"
                                                data-id="<?= $c['idcotizacion'] ?>"
                                                data-cliente="<?= htmlspecialchars($c['nombrecliente']) ?>"
                                                title="Reactivar cotización">
                                                <i class="bi bi-arrow-clockwise fs-5"></i>
                                            </button> -->

                                            <!-- BOTON DE REACTIVAR LA FECHA DE COTIZACION -->
                                            <a href="#" class="p-1 btn-reactivate" role="button"
                                                data-id="<?= $c['idcotizacion'] ?>"
                                                data-cliente="<?= htmlspecialchars($c['nombrecliente']) ?>"
                                                title="Reactivar cotización">
                                                <i class="bi bi-arrow-clockwise fs-5 text-success"></i>
                                            </a>

                                            <!-- BOTON DE REPORTE COTIZACION -->
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
                            <i class="bi bi-archive display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No hay cotizaciones vencidas</h4>
                            <p class="text-muted">No se encontraron cotizaciones que hayan superado su período de vigencia.
                            </p>
                            <a href="/cotizacion" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Volver a Cotizaciones Activas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reactivateModal" tabindex="-1" aria-labelledby="reactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reactivar Cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Deseas reactivar la cotización de <strong id="reactivate-cliente-name"></strong>?</p>
                <p class="small text-muted">Se otorgarán <strong>7 días</strong> más de vigencia.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmReactivate">Reactivar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para confirmar descarga (igual que en index) -->
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
                <div class="alert alert-warning mt-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <small>Esta cotización está vencida (fuera de vigencia)</small>
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
        // Inicializar DataTable para historial
        initDataTableCotizacionHistorial();

        // Modal functionality (igual que en index.php)
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

        // Reactivar modal
        const reactivateModalEl = document.getElementById('reactivateModal');
        const reactivateModal = reactivateModalEl ? new bootstrap.Modal(reactivateModalEl) : null;
        let reactivateTargetId = null;

        document.querySelectorAll('.btn-reactivate').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                reactivateTargetId = this.getAttribute('data-id');
                const clienteName = this.getAttribute('data-cliente') || '';
                const elNombre = document.getElementById('reactivate-cliente-name');
                if (elNombre) elNombre.textContent = clienteName;
                if (reactivateModal) reactivateModal.show();
            });
        });

        document.getElementById('confirmReactivate')?.addEventListener('click', async function () {
            if (!reactivateTargetId) return;
            const btn = this;
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Procesando...';

            try {
                const resp = await fetch(`/cotizacion/reactivar/${encodeURIComponent(reactivateTargetId)}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        // 'X-CSRF-Token': token  <-- si usas CSRF, envíalo aquí
                    }
                });

                const j = await resp.json();

                if (resp.ok && j.success) {
                    if (typeof showToast === 'function') {
                        showToast(j.message || 'Reactivada correctamente', 'SUCCESS', 2000);
                    }

                    // Actualizar fila visualmente
                    const btnEl = document.querySelector(`.btn-reactivate[data-id="${reactivateTargetId}"]`);
                    if (btnEl) {
                        const tr = btnEl.closest('tr');
                        if (tr) {
                            // badge (fecha vencimiento)
                            const badge = tr.querySelector('.badge');
                            if (badge && j.fecha_vencimiento) {
                                badge.textContent = j.fecha_vencimiento;
                                badge.classList.remove('bg-danger');
                                badge.classList.add('bg-success');
                            }

                            // agregar texto de reactivación (si no existe, crearlo)
                            let small = tr.querySelector('small.text-muted.reactivate-info');
                            if (!small) {
                                small = document.createElement('small');
                                small.className = 'text-muted reactivate-info d-block';
                                if (tr.querySelector('td:nth-child(8)')) {
                                    // intentar colocarlo en la celda 8 (ajusta si tu estructura cambia)
                                    tr.querySelector('td:nth-child(8)').appendChild(small);
                                } else {
                                    tr.lastElementChild.appendChild(small);
                                }
                            }
                            if (j.fechareactivacion) {
                                const d = new Date(j.fechareactivacion);
                                small.textContent = 'Reactivado: ' + d.toLocaleString();
                            } else if (j.fecha_vencimiento) {
                                small.textContent = 'Vigencia hasta: ' + j.fecha_vencimiento;
                            }
                        }
                    }

                    if (reactivateModal) reactivateModal.hide();
                } else {
                    const msg = j.message || 'Error al reactivar';
                    if (typeof showToast === 'function') showToast(msg, 'ERROR', 2500); else alert(msg);
                }
            } catch (err) {
                console.error('Error reactivar:', err);
                if (typeof showToast === 'function') showToast('Error de conexión al reactivar', 'ERROR', 2500); else alert('Error de conexión');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
                reactivateTargetId = null;
            }
        });

    });

    function initDataTableCotizacionHistorial() {
        if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            console.warn('jQuery or DataTables not loaded yet');
            return;
        }

        const table = document.getElementById('tabla-cotizacion-historial');
        if (!table) {
            console.warn('Table tabla-cotizacion-historial not found');
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable('#tabla-cotizacion-historial')) {
                $('#tabla-cotizacion-historial').DataTable().clear().destroy();
            }

            setTimeout(() => {
                const tableElement = document.getElementById('tabla-cotizacion-historial');
                if (tableElement && tableElement.parentNode) {
                    $('#tabla-cotizacion-historial').DataTable({
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
                                width: "18%"
                            },
                            {
                                targets: 2, // Vehículo
                                width: "16%"
                            },
                            {
                                targets: 3, // Modalidad
                                width: "10%"
                            },
                            {
                                targets: 4, // Inicial
                                width: "8%"
                            },
                            {
                                targets: 5, // N° Doc
                                width: "8%"
                            },
                            {
                                targets: 6, // Teléfono
                                width: "6%"
                            },
                            {
                                targets: 7, // Fecha Vencimiento
                                width: "8%"
                            }
                            <?php if ($puede_ver_todas): ?>
                                , {
                                    targets: 8, // Registrado por
                                    width: "14%"
                                },
                                {
                                    targets: 9, // Opciones
                                    width: "6%"
                                }
                            <?php else: ?>
                                , {
                                    targets: 8, // Opciones
                                    width: "5%"
                                }
                            <?php endif; ?>
                        ]
                    });
                }
            }, 100);
        } catch (error) {
            console.error('Error initializing cotizacion historial table:', error);
        }
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>