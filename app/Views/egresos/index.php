<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Egresos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/egreso/create" class="btn btn-outline-primary btn-sm">Registrar</a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <?php $estadoActual = $estado ?? '' ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="btn-group m-1 mb-2" id="botones-filtro">
                    <a href="/egreso/listar/N"
                        class="btn btn-sm <?= $estadoActual === 'N' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        Sin comprobantes
                    </a>

                    <a href="/egreso/listar/S"
                        class="btn btn-sm <?= $estadoActual === 'S' ? 'btn-warning' : 'btn-outline-warning' ?>">
                        Por validar
                    </a>

                    <a href="/egreso/listar/validados"
                        class="btn btn-sm <?= $estadoActual === 'validados' ? 'btn-success' : 'btn-outline-success' ?>">
                        Validados
                    </a>
                </div>

            </div>

        </div>

        <div class="card-body" id="lista-oc">

            <div class="table-responsive d-none d-md-block">
                <table class="table table-sm table-hover" id="tabla-egresos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <?php if ($estado === 'validados'): ?>
                                <th>Validado</th>
                                <th>Solicitante</th>
                                <th>Proveedor</th>
                                <th>Tip doc.</th>
                                <th>N° doc</th>
                                <!-- <th>Monto egreso</th> -->
                                <th>Monto validado</th>

                                <th>Comprobante</th>
                            <?php else: ?>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th>Solicitante</th>
                                <th>Monto</th>
                                <th>Comentario</th>
                                <?php if ($estado === 'S'): ?>
                                    <th>Validar comprobante</th>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($egresos)): ?>
                            <tr>
                                <td colspan="10" class="text-center">No hay egresos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($egresos as $egreso): ?>
                                <tr>
                                    <td><?= $numeroFila++ ?></td>
                                    <?php if ($estado === 'validados'): ?>

                                        <td><?= htmlspecialchars($egreso['modificado']) ?></td>
                                        <td><?= htmlspecialchars($egreso['solicitante']) ?></td>
                                        <td><?= htmlspecialchars($egreso['proovedor']) ?></td>
                                        <td>
                                            <span class="badge <?= ($egreso['tipodoc'] == 'F') ? 'bg-danger' : (($egreso['tipodoc'] == 'B') ? 'bg-primary' : 'bg-secondary') ?>">
                                                <?= ($egreso['tipodoc'] == 'F') ? 'Factura' : (($egreso['tipodoc'] == 'B') ? 'Boleta' : 'N/A') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($egreso['numdocumento']) ?></td>
                                        <!-- <td><?= htmlspecialchars(number_format($egreso['monto'], 2)) ?></td> -->
                                        <td><?= htmlspecialchars(number_format($egreso['monto_validado'], 2)) ?></td>

                                        <td>
                                            <?php if (!empty($egreso['rutacomprobante'])): ?>
                                                <a href="#" class="btn btn-link text-body btn-outline-info ver-comprobante" data-url="/archivos/<?= htmlspecialchars($egreso['rutacomprobante']) ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalComprobanteValidado" title="Ver Comprobante" style="font-size: small">
                                                    <i class="bi bi-eye"> Comprobante</i>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php else: ?>
                                        <td><?= htmlspecialchars($egreso['fecha']) ?></td>
                                        <td><?= htmlspecialchars($egreso['concepto']) ?></td>
                                        <td><?= htmlspecialchars($egreso['solicitante']) ?></td>
                                        <td><?= htmlspecialchars(number_format($egreso['monto'], 2)) ?></td>
                                        <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($egreso['comentario'] ?? '') ?>">
                                            <?php if (!empty($egreso['comentario'])): ?>
                                                <a href="#" class="btn btn-link btn-outline-info text-body" data-bs-toggle="modal" data-bs-target="#modalComentario" data-comentario="<?= htmlspecialchars($egreso['comentario']) ?>" style="font-size: small;">

                                                    <i class="bi bi-chat-dots"> Comentario</i>

                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin observación</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($estado === 'S'): ?>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-comprobante" data-id="<?= $egreso['idegreso'] ?>" id="btn-modal-validacion">
                                                    <i class="bi bi-eye"> Validar</i>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>



        <div class="d-md-none">
            <?php if (empty($egresos)): ?>
                <p class="text-center text-muted">No hay egresos registrados.</p>
            <?php else: ?>
                <div class="accordion" id="accordionEgresos">
                    <?php foreach ($egresos as $index => $egreso): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $index ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                                    <i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>
                                    <?php if ($estado === 'validados'): ?>
                                        <strong class="me-2">#<?= $index + 1 ?></strong>
                                        <span class="badge bg-success me-2">Validado</span>
                                        <?= htmlspecialchars($egreso['solicitante']) ?>
                                    <?php else: ?>
                                        <strong class="me-2">#<?= $index + 1 ?></strong>
                                        <span class="text-primary me-2"><?= htmlspecialchars($egreso['concepto']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($egreso['fecha']) ?></small>
                                    <?php endif; ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionEgresos">
                                <div class="accordion-body">
                                    <?php if ($estado === 'validados'): ?>
                                        <p><strong>Validado:</strong> <?= htmlspecialchars($egreso['modificado']) ?></p>
                                        <p><strong>Solicitante:</strong> <?= htmlspecialchars($egreso['solicitante']) ?></p>
                                        <p><strong>Proveedor:</strong> <?= htmlspecialchars($egreso['proovedor']) ?></p>
                                        <p><strong>Tipo Doc:</strong>
                                            <span class="badge <?= ($egreso['tipodoc'] == 'F') ? 'bg-danger' : (($egreso['tipodoc'] == 'B') ? 'bg-primary' : 'bg-secondary') ?>">
                                                <?= ($egreso['tipodoc'] == 'F') ? 'Factura' : (($egreso['tipodoc'] == 'B') ? 'Boleta' : 'N/A') ?>
                                            </span>
                                        </p>
                                        <p><strong>N° Documento:</strong> <?= htmlspecialchars($egreso['numdocumento']) ?></p>
                                        <p><strong>Monto Validado:</strong> S/ <?= htmlspecialchars(number_format($egreso['monto_validado'], 2)) ?></p>
                                        <?php if (!empty($egreso['rutacomprobante'])): ?>
                                            <p><strong>Comprobante:</strong>
                                                <a href="#" class="btn btn-link text-body btn-outline-info ver-comprobante" data-url="/archivos/<?= htmlspecialchars($egreso['rutacomprobante']) ?>" data-bs-toggle="modal" data-bs-target="#modalComprobanteValidado" title="Ver Comprobante">
                                                    <i class="bi bi-eye"> Ver Comprobante</i>
                                                </a>
                                            </p>
                                        <?php else: ?>
                                            <p><strong>Comprobante:</strong> <span class="badge bg-light text-muted">N/A</span></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p><strong>Fecha:</strong> <?= htmlspecialchars($egreso['fecha']) ?></p>
                                        <p><strong>Concepto:</strong> <?= htmlspecialchars($egreso['concepto']) ?></p>
                                        <p><strong>Solicitante:</strong> <?= htmlspecialchars($egreso['solicitante']) ?></p>
                                        <p><strong>Monto:</strong> S/ <?= htmlspecialchars(number_format($egreso['monto'], 2)) ?></p>
                                        <p><strong>Comentario:</strong>
                                            <?php if (!empty($egreso['comentario'])): ?>
                                                <a href="#" class="btn btn-link btn-outline-info text-body" data-bs-toggle="modal" data-bs-target="#modalComentario" data-comentario="<?= htmlspecialchars($egreso['comentario']) ?>">
                                                    <i class="bi bi-chat-dots"> Ver Comentario</i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin observación</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($estado === 'S'): ?>
                                            <p><strong>Validar:</strong>
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-comprobante" data-id="<?= $egreso['idegreso'] ?>" id="btn-modal-validacion">
                                                    <i class="bi bi-eye"> Validar</i>
                                                </button>
                                            </p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>



    </div>



    <div class="modal fade" id="modal-comprobante" tabindex="-1" aria-labelledby="title-modal" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h1 class="modal-title fs-5" id="title-modal"><i class="bi bi-file-earmark-plus me-2"> Validar Comprobante</i></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0 h-100">

                        <input type="hidden" id="idcomprobante" name="idcomprobante" value="">
                        <!-- Panel izquierdo con datos del comprobante -->
                        <div class="col-md-5 d-flex flex-column border-end">
                            <div class="p-3 flex-grow-1 d-flex flex-column">
                                <h5 class="mb-3 border-bottom pb-2 fw-bold">Datos del Comprobante</h5>

                                <!-- Card Tipo de Documento y Serie -->
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-danger text-white py-2">
                                        <h6 class="mb-0">Tipo de Documento</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1 small text-muted">Tipo</p>
                                                <p class="mb-0 fw-semibold" id="tipoDoc">-</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1 small text-muted">Serie</p>
                                                <p class="mb-0 fw-semibold" id="serie">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Proveedor -->
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0">Proveedor</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <p class="mb-1 small text-muted">Nombre y RUC</p>
                                        <p class="mb-2 fw-semibold" id="proovedorInfo">-</p>
                                        <p class="mb-1 small text-muted">Número de Documento</p>
                                        <p class="mb-0 fw-semibold" id="numDocumento">-</p>
                                    </div>
                                </div>

                                <!-- Card Montos -->
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0">Montos</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1 small text-muted">Monto Egreso</p>
                                                <p class="mb-0 fw-semibold">S/ <span id="montoEgreso">0.00</span></p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1 small text-muted">Monto Comprobante</p>
                                                <p class="mb-0 fw-semibold">S/ <span id="montoComprobante">0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="button" class="btn btn-outline-primary" id="validarBtn">
                                            Validar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel derecho con visualización del comprobante -->
                        <div class="col-md-7 d-flex flex-column" style="min-height: 500px;">
                            <div class="p-3 border-bottom ">
                                <h5 class="mb-0 fw-bold">Vista Previa del Comprobante</h5>
                            </div>
                            <div id="comprobanteViewer" class="flex-grow-1 d-flex justify-content-center align-items-center bg-light">
                                <p class="text-muted">Cargando comprobante...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="modalComprobanteValidado" tabindex="-1" aria-labelledby="modalComprobanteValidadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light">
                    <h5 class="modal-title fw-bold" id="modalComprobanteValidado">Comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" style="height: 80vh;">
                    <iframe id="visorComprobanteValidado" src="" width="100%" height="100%" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalComentario" tabindex="-1" aria-labelledby="modalComentarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalComentarioLabel"><i class="fas fa-info-circle me-2"></i>Detalle de Observación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="textoComentario" class="mb-0" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>



</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const modalComentario = document.getElementById('modalComentario');
        const textoComentario = document.getElementById('textoComentario');
        const modalComprobanteValidado = document.getElementById('modalComprobanteValidado');
        const visorComprobanteValidado = document.getElementById('visorComprobanteValidado');

        if (modalComentario) {
            modalComentario.addEventListener('show.bs.modal', function(event) {

                const button = event.relatedTarget;

                const comentario = button.getAttribute('data-comentario');

                textoComentario.textContent = comentario;
            });


            modalComentario.addEventListener('hidden.bs.modal', function() {
                textoComentario.textContent = '';
            });
        }



        modalComprobanteValidado.addEventListener('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            const url = button.getAttribute('data-url');

            visorComprobanteValidado.src = url;
        });

        modalComprobanteValidado.addEventListener('hidden.bs.modal', function() {
            visorComprobanteValidado.src = '';
        });


        const comprobanteModal = document.getElementById('modal-comprobante');
        const modalTitle = document.getElementById('title-modal');
        const comprobanteViewer = document.getElementById('comprobanteViewer');
        const comprobanteForm = document.getElementById('comprobanteForm');

        comprobanteModal.addEventListener('show.bs.modal', async (event) => {
            const button = event.relatedTarget;
            const egresoId = button.getAttribute('data-id');

            comprobanteViewer.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Cargando comprobante...</p></div>`;

            try {
                const response = await fetch(`/api/egreso/detalle/${egresoId}`);
                const result = await response.json();

                if (result.status && result.data) {
                    const data = result.data;

                    modalTitle.innerHTML = '<i class="bi bi-file-earmark-plus me-2"> Validar Comprobante</i>';
                    document.getElementById('idcomprobante').value = data.idcomprobante || '';
                    document.getElementById('tipoDoc').textContent = data.tipodoc === 'F' ? 'Factura' : (data.tipodoc === 'B' ? 'Boleta' : data.tipodoc);
                    document.getElementById('proovedorInfo').textContent = data.proovedor;
                    document.getElementById('serie').textContent = data.serie;
                    document.getElementById('numDocumento').textContent = data.numdocumento;
                    document.getElementById('montoEgreso').textContent = parseFloat(data.montoegreso).toFixed(2);
                    document.getElementById('montoComprobante').textContent = parseFloat(data.montocomprobante).toFixed(2);

                    const rutaCompleta = data.rutacomprobante;
                    const partesRuta = rutaCompleta.split('/');
                    // console.log('RUTA: ', partesRuta)
                    const tipo = partesRuta[0];

                    // console.log('TIPO: ', tipo);
                    const nombreArchivo = partesRuta[1];

                    const urlComprobante = `/archivos/${tipo}/${nombreArchivo}`;

                    renderDocument(urlComprobante);

                } else {
                    comprobanteViewer.innerHTML = `<p class="text-danger p-3">No se encontraron detalles del comprobante.</p>`;
                }
            } catch (error) {
                comprobanteViewer.innerHTML = `<p class="text-danger p-3">Error al cargar los datos. Verifique la conexión o el servidor.</p>`;
            }
        });



        function renderDocument(url) {
            const extension = url.split('.').pop().toLowerCase();            
            // console.log('EXTENSION DEL COMPROBANTE: ', extension);
            comprobanteViewer.innerHTML = '';

            const container = document.createElement('div');
            container.className = 'w-100 h-100 d-flex flex-column';

            if (extension === 'pdf') {
                const embedElement = document.createElement('embed');
                embedElement.src = url;
                embedElement.type = 'application/pdf';
                embedElement.className = 'flex-grow-1';
                embedElement.style.minHeight = '400px';
                container.appendChild(embedElement);
            } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'd-flex justify-content-center align-items-center h-100';

                const imgElement = document.createElement('img');
                imgElement.src = url;
                imgElement.alt = 'Comprobante';
                imgElement.className = 'img-fluid';
                imgElement.style.maxHeight = '100%';
                imgElement.style.maxWidth = '100%';
                imgElement.style.objectFit = 'contain';

                imgContainer.appendChild(imgElement);
                container.appendChild(imgContainer);
            } else {
                container.innerHTML = `<div class="text-center p-5"><p class="text-danger">Formato de archivo no soportado.</p></div>`;
            }

            comprobanteViewer.appendChild(container);
        }

        const validarBtn = document.getElementById('validarBtn');
        validarBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const idComprobante = document.getElementById('idcomprobante')?.value;

            if (!idComprobante) {
                showToast('No se encontró un comprobante para validar.', 'ERROR', 2000);
                return;
            }

            if (await ask('¿Desea validar este comprobante?', 'Confirmar Validación')) {
                try {
                    const response = await fetch(`/egreso/validarComprobante/${idComprobante}`, {
                        method: 'POST'
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast(result.message, 'SUCCESS', 1500);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modal-comprobante'));
                        modal.hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(result.message || 'Error al validar el comprobante.', 'ERROR', 2000);
                    }
                } catch (error) {
                    showToast('Error de red al intentar validar.', 'ERROR', 2000);
                }
            }
        });
    });
</script>