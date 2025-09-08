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
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/create" class="">[ Registrar ]</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda" id="tabla-cotizacion">
                        <colgroup>
                            <col style="width: 3%;">
                            <col style="width: 20%;">
                            <col style="width: 8%;">
                            <col style="width: 7%;">
                            <col style="width: 18%;">
                            <col style="width: 15%;">
                            <col style="width: 10%;"> <!-- Inicial -->
                            <?php if ($puede_ver_todas): ?>
                                <col style="width: 15%;"> <!-- Registrado por -->
                            <?php endif; ?>
                            <col style="width: 5%;"> <!-- Opciones -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>N° de Doc</th>
                                <th>Teléfono</th>
                                <th>Vehículo</th>
                                <th>Modalidad</th>
                                <th>Inicial</th>
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
                                    <td><?= htmlspecialchars($c['documento']) ?></td>
                                    <td><?= htmlspecialchars($c['telefono']) ?></td>

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
                <!-- Antes: Descargar Cotización -->
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
                <!-- Antes: Descargar PDF -->
                <button type="button" class="btn btn-primary btn-sm" id="confirmDownload">Ver PDF</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
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
            const target = withPreview(currentUrl); // <--- fuerza modo visor PDF

            if (openInNewTab) {
                window.open(target, '_blank', 'noopener,noreferrer');
            } else {
                window.location.href = target; // misma pestaña, también visor PDF
            }

            downloadModal.hide();
        });
    });
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>