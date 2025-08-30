<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
$cliente = $cliente ?? null;
$historial = $historial ?? [];

// seguridad / sanitizar
function esc($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
?>

<div class="container-fluid p-4">
    <!-- Encabezado-->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Área de Crédito</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Historial de Observaciones
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/creditos" class="">[ Volver ]</a>
                <!-- <button class="btn btn-primary me-2" onclick="generarReporte()">
                    <i class="fas fa-file-pdf me-1"></i>Generar Reporte
                </button> -->
            </div>
        </div>
    </div>


    <?php if ($cliente): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= esc($cliente['cliente'] ?? ($cliente['nombre'] ?? 'Sin nombre')) ?></h5>
                <p class="mb-1"><strong>DNI:</strong> <?= esc($cliente['nrodoc'] ?? '') ?></p>
                <?php if (!empty($cliente['celular'] ?? '') || !empty($cliente['telprimario'] ?? '')): ?>
                    <p class="mb-1"><strong>Teléfono:</strong>
                        <?= esc($cliente['celular'] ?? ($cliente['telprimario'] ?? '')) ?></p>
                <?php endif; ?>
                <?php if (!empty($cliente['direccion'] ?? '')): ?>
                    <p class="mb-0"><strong>Dirección:</strong> <?= esc($cliente['direccion']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <?php if (count($historial) === 0): ?>
                <div class="p-3">
                    <div class="alert alert-secondary mb-0">No hay seguimientos registrados para este contrato.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <!-- <?php
                    if (!empty($historial[0]['evidencia'])) {
                        $rutaRel = ltrim($historial[0]['evidencia'], '/');
                        $rutaEnDisco = realpath(__DIR__ . '/../../storage/' . $rutaRel);
                        echo "<pre style='background:#fff;padding:10px;border:1px solid #ddd;'>DEBUG rutaRel: {$rutaRel}\n";
                        echo "Ruta en disco (realpath): " . ($rutaEnDisco ?: 'NO EXISTE') . "\n";
                        if ($rutaEnDisco)
                            echo "Tamaño (bytes): " . filesize($rutaEnDisco) . "\n";
                        echo "</pre>";
                    }
                    ?> -->

                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Observaciones</th>
                                <th>Evidencia</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($historial as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc(ucfirst($row['tipo'] ?? '')) ?></td>
                                    <td style="min-width:300px;"><?= nl2br(esc($row['observaciones'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($row['evidencia'])): ?>
                                            <?php
                                            // ejemplo en DB: "seguimientos/seguimiento_xxx.jpg"
                                            $rutaRel = ltrim($row['evidencia'], '/');

                                            // separar carpeta y nombre
                                            $parts = explode('/', $rutaRel, 2);
                                            $tipo = $parts[0] ?? 'seguimientos';
                                            $nombre = $parts[1] ?? ($parts[0] ?? '');

                                            // URL que llama al Controller: /archivos/{tipo}/{nombre}
                                            $urlController = '/archivos/' . rawurlencode($tipo) . '/' . rawurlencode($nombre);

                                            // Fallback a archivos.php (si tu router no captura nombres con puntos)
                                            $urlFallback = '/archivos.php?f=' . rawurlencode($rutaRel);

                                            // Decide cuál mostrar en la vista (mostramos controller URL; JS/usuario puede usar el fallback si hay problema)
                                            $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                                            $isPdf = $ext === 'pdf';
                                            ?>
                                            <?php if ($isPdf): ?>
                                                <!-- Para PDFs abrimos en otra pestaña -->
                                                <a href="<?= esc($urlController) ?>" target="_blank" class="btn btn-sm btn-danger"
                                                    title="Ver evidencia PDF">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </a>
                                            <?php else: ?>
                                                <!-- Botón que abre modal con data-img apuntando primero al controller -->
                                                <button type="button" class="btn btn-sm btn-primary ver-evidencia-img"
                                                    data-img-controller="<?= esc($urlController) ?>"
                                                    data-img-fallback="<?= esc($urlFallback) ?>"
                                                    data-filename="<?= esc(basename($nombre)) ?>" title="Ver evidencia">
                                                    <i class="fas fa-image me-1"></i>Ver
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin evidencia</span>
                                        <?php endif; ?>
                                    </td>



                                    <td><?= esc($row['fecha_seguimiento'] ?? $row['fecha'] ?? '') ?></td>
                                    <td><?= esc($row['usuario_registro'] ?? 'Desconocido') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para visualizar evidencia (imagen o PDF) -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-image me-2"></i>Vista de Evidencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 60vh;">
                <!-- para imagen -->
                <div id="contenedorImagen" class="text-center" style="display:none; padding:10px;">
                    <img id="imgEvidencia" src="" alt="Evidencia" class="img-fluid" style="max-height:70vh;">
                </div>
                <!-- para PDF -->
                <div id="contenedorPDF" style="display:none; min-height:60vh;">
                    <iframe id="iframeEvidencia" src="" style="width:100%; height:70vh; border:0;"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a id="descargarEvidencia" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ver-evidencia-img').forEach(btn => {
            btn.addEventListener('click', function () {
                const urlCtrl = this.dataset.imgController;
                const urlFb = this.dataset.imgFallback;
                const filename = this.dataset.filename || 'evidencia';

                const contImg = document.getElementById('contenedorImagen');
                const contPdf = document.getElementById('contenedorPDF');
                const imgEl = document.getElementById('imgEvidencia');
                const iframeEl = document.getElementById('iframeEvidencia');
                const downloadBtn = document.getElementById('descargarEvidencia');

                // Reset UI
                contImg.style.display = 'none';
                contPdf.style.display = 'none';
                imgEl.src = '';
                iframeEl.src = '';
                downloadBtn.href = '#';
                downloadBtn.removeAttribute('download');

                // Intentamos cargar la URL del controller primero
                testImage(urlCtrl, function (success) {
                    const finalUrl = success ? urlCtrl : urlFb;
                    // asignar al modal (imagen)
                    imgEl.src = finalUrl;
                    downloadBtn.href = finalUrl;
                    downloadBtn.setAttribute('download', filename);
                    contImg.style.display = 'block';
                    const modal = new bootstrap.Modal(document.getElementById('modalEvidencia'));
                    modal.show();
                });
            });
        });

        // función que verifica si una imagen está disponible (HEAD + fallback a load)
        function testImage(url, cb) {
            // Primera opción: usar HEAD vía fetch (si el servidor lo permite)
            if (window.fetch) {
                fetch(url, { method: 'HEAD' })
                    .then(resp => {
                        if (resp.ok) return cb(true);
                        // si viene 405 o bloqueo del HEAD, probamos con la carga de imagen
                        loadImg(url, cb);
                    })
                    .catch(() => {
                        loadImg(url, cb);
                    });
            } else {
                loadImg(url, cb);
            }
        }

        function loadImg(url, cb) {
            const img = new Image();
            let called = false;
            img.onload = function () { if (!called) { called = true; cb(true); } };
            img.onerror = function () { if (!called) { called = true; cb(false); } };
            img.src = url + '?_=' + Date.now(); // bust cache
            // timeout en caso de que quede colgado
            setTimeout(() => { if (!called) { called = true; cb(false); } }, 3000);
        }

    });
</script>



<?php include __DIR__ . '/../layout/footer.php'; ?>