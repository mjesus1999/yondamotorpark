<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Requisitos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <span>Desde este módulo podrá seleccionar los requisitos para el Formato Cotizacion</span>
                <!-- <a href="/formatoCotizacion" class="">[ Volver ]</a> -->
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="/formatoCotizacion/requisitos/storeRequisitos" method="POST" id="formCotizacion">
            <input type="hidden" name="idformato" value="<?= $formato['idformato'] ?>">
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Formato:</strong>
                    <span class="fst-italic"><?= htmlspecialchars($formato['tipocotizacion']) ?></span>
                </div>
                <div class="card-body">
                    <div class="row g-1 align-items-center">

                        <?php
                        // Preparo lookup de IDs ya asignados:
                        $idsAsignados = array_column($asignados, 'idrequisito');
                        ?>

                        <!-- Muestra los Requisitos -->
                        <div class="col-md-5">
                            <div class="border rounded p-2 MostrarRequisitos"
                                style="min-height:600px; overflow-y:auto;">
                                <ul id="lista-disponibles" class="list-group list-group-flush">
                                    <?php if (!empty($todosRequisitos)): ?>
                                        <?php foreach ($todosRequisitos as $req): ?>
                                            <?php if (!in_array($req['idrequisito'], $idsAsignados)): ?>
                                                <li class="list-group-item">
                                                    <input type="checkbox" class="form-check-input me-2 requisito-checkbox"
                                                        value="<?= $req['idrequisito'] ?>">
                                                    <?= htmlspecialchars($req['requisito']) ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item text-muted">No hay requisitos disponibles.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex flex-column align-items-center">
                            <button type="button" id="btnAgregar"
                                class="btn btn-sm btn-outline-primary mb-2">&gt;</button>
                            <button type="button" id="btnQuitar" class="btn btn-sm btn-outline-primary">&lt;</button>
                        </div>

                        <!-- Requisitos Seleccionados -->
                        <div class="col-md-5">
                            <div class="border rounded p-2 RequisitosAgregados"
                                style="min-height:600px; overflow-y:auto;">
                                <ul id="lista-seleccionados" class="list-group list-group-flush">
                                    <?php if (!empty($asignados)): ?>
                                        <?php foreach ($asignados as $req): ?>
                                            <li class="list-group-item" data-id="<?= $req['idrequisito'] ?>">
                                                <!-- checkbox para poder marcarlo y quitarlo -->
                                                <input type="checkbox" class="form-check-input me-2 requisito-checkbox-remover">
                                                <?= htmlspecialchars($req['requisito']) ?>
                                                <!-- hidden para el POST -->
                                                <input type="hidden" name="requisitos[]" value="<?= $req['idrequisito'] ?>">
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item text-muted">Aún no hay requisitos asignados.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="/formatoCotizacion" class="btn btn-sm btn-outline-secondary">Cancelar</a>
                    <!-- <button type="reset" id="btn-cancelar-registro"
                        class="btn btn-sm btn-outline-secondary">Cancelar</button> -->
                    <button type="submit" class="btn btn-primary btn-sm btnGuardarRequisito">Agregar</button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnAdd = document.getElementById('btnAgregar');
        const btnRemove = document.getElementById('btnQuitar');
        const listaDisp = document.getElementById('lista-disponibles');
        const listaSel = document.getElementById('lista-seleccionados');

        btnAdd.addEventListener('click', () => moveToSelected());
        btnRemove.addEventListener('click', () => moveToAvailable());

        function moveToSelected() {
            //Retiro del placeholder al recibir informacion
            const ph = listaSel.querySelector('li.text-muted');
            if (ph) ph.remove();

            listaDisp.querySelectorAll('input.requisito-checkbox:checked').forEach(cb => {
                const li = cb.closest('li');
                const id = cb.value;
                // 1) quito checkbox original
                cb.remove();
                // 2) añado checkbox de remover
                const rem = document.createElement('input');
                rem.type = 'checkbox';
                rem.className = 'form-check-input me-2 requisito-checkbox-remover';
                // 3) añado hidden para form
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'requisitos[]';
                hidden.value = id;
                // 4) atributo data-id
                li.dataset.id = id;
                // inserto al inicio
                li.prepend(rem, hidden);
                // muevo al cuadro derecho
                listaSel.appendChild(li);
            });
        }

        function moveToAvailable() {
            listaSel.querySelectorAll('input.requisito-checkbox-remover:checked').forEach(cb => {
                const li = cb.closest('li');
                // 1) quito el hidden y el checkbox de remover
                const hidden = li.querySelector('input[type="hidden"][name="requisitos[]"]');
                if (hidden) hidden.remove();
                cb.remove();
                // 2) creo de nuevo checkbox de origen
                const orig = document.createElement('input');
                orig.type = 'checkbox';
                orig.className = 'form-check-input me-2 requisito-checkbox';
                orig.value = li.dataset.id;
                li.prepend(orig);
                // 3) lo devuelvo al listado izquierdo
                listaDisp.appendChild(li);
            });
            // si lista-seleccionados ya quedó vacía, puedes reponer el mensaje:
            if (!listaSel.querySelector('li[data-id]')) {
                const li = document.createElement('li');
                li.className = 'list-group-item text-muted';
                li.textContent = 'Aún no hay requisitos asignados.';
                listaSel.appendChild(li);
            }
        }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>