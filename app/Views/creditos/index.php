<!-- app/views/credito/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    .card-gradient {
        background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    }

    .border-4 {
        border-width: 4px !important;
    }

    .moroso-item {
        transition: all 0.3s ease;
    }

    .upload-area {
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background-color: #020f1dff;
    }

    .badge.custom-warning {
        background: #ffc107;
        color: #212529;
    }

    .direccion-info {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<?php
$estadisticas = $estadisticas ?? [
    'total_morosos' => 0,
    'deuda_total' => 0,
    'seguimientos_hoy' => 0,
    'dias_promedio' => 0
];
$morososRaw = $morosos ?? [];

$morososClasificados = [
    '5-dias' => [],
    '2-semanas' => [],
    '1-mes' => []
];

if (!empty($morososRaw)) {
    // Si ya viene clasificado por claves
    if (isset($morososRaw['5-dias']) || isset($morososRaw['2-semanas']) || isset($morososRaw['1-mes'])) {
        $morososClasificados['5-dias'] = $morososRaw['5-dias'] ?? [];
        $morososClasificados['2-semanas'] = $morososRaw['2-semanas'] ?? [];
        $morososClasificados['1-mes'] = $morososRaw['1-mes'] ?? [];
    } else {
        foreach ($morososRaw as $m) {
            $dias = (int) ($m['dias_atraso'] ?? $m['dias_max_vencido'] ?? 0);
            if ($dias <= 5) {
                $morososClasificados['5-dias'][] = $m;
            } elseif ($dias <= 14) {
                $morososClasificados['2-semanas'][] = $m;
            } else {
                $morososClasificados['1-mes'][] = $m;
            }
        }
    }
}

$counts = [
    '5-dias' => count($morososClasificados['5-dias']),
    '2-semanas' => count($morososClasificados['2-semanas']),
    '1-mes' => count($morososClasificados['1-mes'])
];

function fmtMoney($val)
{
    return 'S/. ' . number_format((float) $val, 2, ',', '.');
}
?>

<div class="container-fluid">
    <!-- Encabezado-->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Área de Crédito</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gestión de morosos y seguimiento de pagos
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h3 class="card-title"><?= (int) $estadisticas['total_morosos'] ?></h3>
                    <p class="card-text mb-0">Total Morosos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card  h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h3 class="card-title"><?= fmtMoney($estadisticas['deuda_total']) ?></h3>
                    <p class="card-text mb-0">Deuda Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card  h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h3 class="card-title"><?= (int) $estadisticas['seguimientos_hoy'] ?></h3>
                    <p class="card-text mb-0">Seguimientos Hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card  h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar fa-2x mb-2"></i>
                    <h3 class="card-title"><?= round($estadisticas['dias_promedio'], 1) ?></h3>
                    <p class="card-text mb-0">Días Promedio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de clasificación -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Clasificación por Morosidad</h6>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro5dias" autocomplete="off"
                        checked>
                    <label class="btn btn-sm btn-outline-warning" for="filtro5dias" onclick="filtrarPor('5-dias')">
                        <i class="fas fa-clock me-1"></i>5 Días
                        <span class="badge bg-warning text-dark ms-1"><?= $counts['5-dias'] ?></span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro2semanas" autocomplete="off">
                    <label class="btn btn-sm btn-outline-warning" for="filtro2semanas" onclick="filtrarPor('2-semanas')">
                        <i class="fas fa-calendar-week me-1"></i>2 Semanas
                        <span class="badge bg-warning text-dark ms-1"><?= $counts['2-semanas'] ?></span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro1mes" autocomplete="off">
                    <label class="btn btn-sm btn-outline-danger" for="filtro1mes" onclick="filtrarPor('1-mes')">
                        <i class="fas fa-calendar-times me-1"></i>+1 Mes
                        <span class="badge bg-danger ms-1"><?= $counts['1-mes'] ?></span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtroTodos" autocomplete="off">
                    <label class="btn btn-sm     btn-outline-primary" for="filtroTodos" onclick="filtrarPor('todos')">
                        <i class="fas fa-list me-1"></i>Ver Todos
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de morosos -->
    <div class="row" id="listaMoresos">
        <?php
        $map = [
            '5-dias' => ['badge' => 'bg-warning text-dark', 'border' => 'border-warning'],
            '2-semanas' => ['badge' => 'bg-warning text-dark', 'border' => 'border-4'],
            '1-mes' => ['badge' => 'bg-danger', 'border' => 'border-danger']
        ];

        // Renderizamos por categoría: si está vacía mostramos alerta específica; si tiene datos mostramos las tarjetas
        foreach (['5-dias', '2-semanas', '1-mes'] as $categoria):
            $morososCategoria = $morososClasificados[$categoria] ?? [];
            $noMorososDisplay = empty($morososCategoria) ? 'block' : 'none';
            ?>
            <div class="col-12 no-morosos" id="noMorosos-<?= $categoria ?>" data-categoria="<?= $categoria ?>"
                style="display: <?= $noMorososDisplay ?>;">
                <div class="alert alert-secondary text-center mb-0">
                    <strong>No hay morosos en la categoría <?= ucfirst(str_replace('-', ' ', $categoria)) ?>.</strong>
                </div>
            </div>

            <?php if (!empty($morososCategoria)): ?>
                <?php foreach ($morososCategoria as $m):
                    $idcontrato = (int) ($m['idcontrato'] ?? 0);
                    $cliente = htmlspecialchars($m['cliente'] ?? 'Sin nombre');
                    $ndoc = htmlspecialchars($m['ndocumento'] ?? ($m['nrodoc'] ?? ''));
                    $telefono = htmlspecialchars($m['telefono'] ?? $m['telprimario'] ?? '');
                    $dias = (int) ($m['dias_atraso'] ?? $m['dias_max_vencido'] ?? 0);
                    $deuda = $m['saldo_pendiente'] ?? $m['deuda_total'] ?? 0;
                    $fecha_venc = $m['fecha_vencimiento'] ?? ($m['fechapago'] ?? '');
                    $direccion_persona = htmlspecialchars($m['direccion_persona'] ?? 'Sin dirección');
                    $direccion_local = htmlspecialchars($m['direccion'] ?? ($m['direccion_completa'] ?? $m['direccion_local'] ?? 'Sin dirección del local'));
                    /* $direccion = htmlspecialchars($m['direccion'] ?? ($m['direccion_completa'] ?? '')); */
                    $deudaFmt = fmtMoney($deuda);
                    ?>
                    <div class="col-md-6 mb-3 moroso-item" data-categoria="<?= $categoria ?>" id="cliente-<?= $idcontrato ?>">
                        <div class="card border-start <?= $map[$categoria]['border'] ?> h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-user me-2"></i><?= $cliente ?></h6>
                                        <p class="text-muted small mb-1"><strong>DNI:</strong> <?= $ndoc ?></p>
                                        <?php if ($telefono): ?>
                                            <p class="text-muted small mb-0"><strong>Teléfono:</strong> <?= $telefono ?></p>
                                        <?php endif; ?>
                                        <p class="direccion-info mb-0">
                                            <!-- <i class="fas fa-home me-1"></i> --><strong>Dirección:</strong>
                                            <?= $direccion_persona ?>
                                        </p>
                                    </div>
                                    <span class="badge <?= $map[$categoria]['badge'] ?>"><?= $dias ?> días</span>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><strong class="text-danger">Deuda: <?= $deudaFmt ?></strong></p>
                                    <?php if ($fecha_venc): ?>
                                        <p class="text-muted small mb-1"><strong>Vencimiento:</strong>
                                            <?= htmlspecialchars($fecha_venc) ?></p>
                                    <?php endif; ?>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-store me-1"></i><strong>Local:</strong> <?= $direccion_local ?>
                                    </p>
                                    <!-- <?php if ($direccion): ?>
                                        <p class="text-muted small mb-0"><strong>Dirección:</strong> <?= $direccion ?></p>
                                    <?php endif; ?> -->
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-success btn-sm flex-grow-1"
                                        onclick="iniciarSeguimiento(<?= $idcontrato ?>, '<?= addslashes($cliente) ?>', '<?= number_format((float) $deuda, 2, '.', '') ?>')">
                                        <i class="fas fa-camera me-1"></i>Hacer Seguimiento
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="verHistorial(<?= $idcontrato ?>)"
                                        title="Ver historial">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <!-- Si no hay morosos en ninguna categoría mostramos mensaje general -->
        <?php if ($counts['5-dias'] + $counts['2-semanas'] + $counts['1-mes'] === 0): ?>
            <div class="col-12" id="noMorososTodosGlobal">
                <div class="alert alert-secondary text-center">No hay morosos para mostrar.</div>
            </div>
        <?php else: ?>
            <div class="col-12" id="noMorososTodosGlobal" style="display:none;">
                <div class="alert alert-secondary text-center">No hay morosos para mostrar.</div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal para seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSeguimientoLabel">
                    <i class="fas fa-user-check me-2"></i>Registrar Seguimiento de Cobranza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimiento" method="POST" action="/creditos/seguimiento" enctype="multipart/form-data">
                    <input type="hidden" name="idcontrato" id="hiddenIdContrato" value="">
                    <!-- Información del cliente -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label"><strong><i
                                            class="fas fa-user me-1"></i>Cliente:</strong></label>
                                <p id="clienteNombre" class="mb-0 text-primary fw-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><strong><i
                                            class="fas fa-dollar-sign me-1"></i>Deuda:</strong></label>
                                <p id="clienteDeuda" class="mb-0 text-danger fw-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><strong><i class="fas fa-clock me-1"></i>Fecha y
                                        Hora:</strong></label>
                                <p id="fechaHora" class="mb-0"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de seguimiento -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clipboard-check me-2"></i>Tipo de Seguimiento:
                        </label>
                        <div class="row mt-2">
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoSeguimiento"
                                                id="documentoFirmado" value="documento" checked>
                                            <label class="form-check-label w-100" for="documentoFirmado">
                                                <i class="fas fa-file-signature text-success fa-2x mb-2 d-block"></i>
                                                <strong>Documento Firmado</strong><br>
                                                <small class="text-muted">El cliente firmó el documento de
                                                    notificación</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoSeguimiento"
                                                id="evidenciaVisita" value="evidencia">
                                            <label class="form-check-label w-100" for="evidenciaVisita">
                                                <i class="fas fa-camera text-warning fa-2x mb-2 d-block"></i>
                                                <strong>Evidencia de Visita</strong><br>
                                                <small class="text-muted">Foto de que se realizó la visita</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-2"></i>Observaciones del Seguimiento
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="4"
                            placeholder="Describe detalladamente lo ocurrido durante la visita"></textarea>
                    </div>

                    <!-- Subir evidencia -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Subir Evidencia
                        </label>
                        <div class="card border-2 border-dashed upload-area" id="uploadArea" style="cursor: pointer;"
                            onclick="document.getElementById('evidenciaFile').click()">
                            <div class="card-body text-center py-4">
                                <div id="uploadDefault">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h6 class="mb-2">Haz clic aquí para subir la evidencia</h6>
                                    <p class="text-muted small mb-0">
                                        <strong>Formatos permitidos:</strong> JPG, PNG, PDF<br>
                                        <strong>Tamaño máximo:</strong> 5MB
                                    </p>
                                </div>
                                <div id="uploadSuccess" style="display: none;">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h6 class="text-success mb-2">Archivo cargado exitosamente</h6>
                                    <p class="text-muted small mb-2" id="nombreArchivo"></p>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="cambiarArchivo()">
                                        <i class="fas fa-exchange-alt me-1"></i>Cambiar archivo
                                    </button>
                                </div>
                                <input type="file" id="evidenciaFile" name="evidenciaFile" accept="image/*,.pdf"
                                    style="display: none;" onchange="mostrarArchivo(this)">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-save me-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">
                <i class="fas fa-check-circle text-success me-2"></i>Sistema de Crédito
            </strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Seguimiento registrado correctamente
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Función para filtrar morosos por categoría
    function filtrarPor(categoria) {
        const items = document.querySelectorAll('.moroso-item');
        const noMorososAlerts = document.querySelectorAll('.no-morosos');
        const globalNo = document.getElementById('noMorososTodosGlobal');

        if (categoria === 'todos') {
            // Mostrar todas las tarjetas
            items.forEach(item => item.style.display = 'block');

            // Ocultar alertas por categoria
            noMorososAlerts.forEach(alert => alert.style.display = 'none');

            // Mostrar mensaje global solo si no hay ninguna tarjeta
            globalNo.style.display = (items.length === 0) ? 'block' : 'none';
            return;
        }

        // Mostrar solo las tarjetas de la categoría solicitada
        items.forEach(item => {
            item.style.display = (item.dataset.categoria === categoria) ? 'block' : 'none';
        });

        // Para las alertas por categoría
        noMorososAlerts.forEach(alert => {
            if (alert.dataset.categoria === categoria) {
                const count = document.querySelectorAll('.moroso-item[data-categoria="' + categoria + '"]').length;
                alert.style.display = (count === 0) ? 'block' : 'none';
            } else {
                alert.style.display = 'none';
            }
        });

        // Ocultar mensaje global
        if (globalNo) globalNo.style.display = 'none';
    }


    // Función para iniciar seguimiento (poblamos modal y hidden input)
    function iniciarSeguimiento(id, nombre, deuda) {
        document.getElementById('clienteNombre').textContent = nombre;
        document.getElementById('clienteDeuda').textContent = 'S/. ' + deuda;
        document.getElementById('fechaHora').textContent = new Date().toLocaleString('es-PE');
        document.getElementById('hiddenIdContrato').value = id;

        // Reset visual de upload
        document.getElementById('uploadDefault').style.display = 'block';
        document.getElementById('uploadSuccess').style.display = 'none';
        document.getElementById('evidenciaFile').value = '';

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalSeguimiento'));
        modal.show();
    }

    // Función para mostrar archivo seleccionado
    function mostrarArchivo(input) {
        if (input.files && input.files[0]) {
            const archivo = input.files[0];
            const tamaño = (archivo.size / 1024 / 1024).toFixed(2); // MB

            if (tamaño > 5) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                input.value = '';
                return;
            }

            document.getElementById('uploadDefault').style.display = 'none';
            document.getElementById('uploadSuccess').style.display = 'block';
            document.getElementById('nombreArchivo').textContent = `${archivo.name} (${tamaño} MB)`;
        }
    }

    function cambiarArchivo() {
        document.getElementById('evidenciaFile').click();
    }

    // Ver historial abre la vista del historial
    function verHistorial(id) {
        window.location.href = '/creditos/historial/' + id;
    }

    // Toast helper
    function showToast(mensaje, tipo = 'success') {
        const toastEl = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastHeader = toastEl.querySelector('.toast-header strong');

        toastMessage.textContent = mensaje;

        if (tipo === 'success') {
            toastHeader.innerHTML = '<i class="fas fa-check-circle text-success me-2"></i>Sistema de Crédito';
        } else if (tipo === 'info') {
            toastHeader.innerHTML = '<i class="fas fa-info-circle text-info me-2"></i>Sistema de Crédito';
        } else if (tipo === 'warning') {
            toastHeader.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Sistema de Crédito';
        }

        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    // Manejar envío del formulario (enviar al endpoint real /credito/seguimiento)
    document.getElementById('formSeguimiento').addEventListener('submit', function (e) {

        const observaciones = document.getElementById('observaciones').value;
        const evidencia = document.getElementById('evidenciaFile').files[0];

        if (!observaciones.trim()) {
            e.preventDefault();
            alert('Por favor, ingresa las observaciones del seguimiento.');
            return;
        }

        if (!evidencia) {
            e.preventDefault();
            alert('Por favor, sube la evidencia del seguimiento.');
            return;
        }

        showToast('Guardando seguimiento...', 'info');
    });

    // Inicializar
    document.addEventListener('DOMContentLoaded', function () {
        filtrarPor('5-dias');
    });

    // Animación simple al cargar tarjetas
    window.addEventListener('load', function () {
        const cards = document.querySelectorAll('.moroso-item');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>