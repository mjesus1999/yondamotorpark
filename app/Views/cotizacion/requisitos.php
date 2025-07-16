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
                <a href="/cotizacion/create" class="">[ Registrar ]</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="" id="formCotizacion">
            <!-- Requisitos -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Formato Cotización</strong> <span class="fst-italic">
                    </span>
                </div>
                <div class="card-body">
                    <!-- CAMPOS -->
                    <div class="row g-1 align-items-center">
                        <!-- Cuadro izquierdo -->
                        <div class="col-md-5">
                            <div class="border rounded p-2 MostrarRequisitos"
                                style="min-height: 620px; overflow-y: auto;">
                                <?php if (!empty($requisitos)): ?>
                                    <ul class="list-group list-group-flush" id="lista-disponibles">
                                        <?php foreach ($requisitos as $req): ?>
                                            <li class="list-group-item">
                                                <input type="checkbox" class="form-check-input me-2 requisito-checkbox"
                                                    value="<?= $req['idrequisito'] ?>">
                                                <?= htmlspecialchars($req['requisito']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">No hay requisitos disponibles.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Botones en el centro -->
                        <div class="col-md-2 d-flex flex-column align-items-center">
                            <button type="button" id="btnAgregar"
                                class="btn btn-sm btn-outline-primary mb-2">&gt;</button>
                            <button type="button" id="btnQuitar" class="btn btn-sm btn-outline-primary">&lt;</button>
                        </div>

                        <!-- Cuadro derecho -->
                        <div class="col-md-5">
                            <div class="border rounded p-2 RequisitosAgregados"
                                style="min-height: 620px; overflow-y: auto;">
                                <ul class="list-group list-group-flush" id="lista-seleccionados"></ul>
                            </div>
                        </div>
                    </div>
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

        btnAdd.addEventListener('click', () => moveItems(listaDisp, listaSel));
        btnRemove.addEventListener('click', () => moveItems(listaSel, listaDisp));

        function moveItems(fromList, toList) {
            const checkedItems = fromList.querySelectorAll('input:checked');
            checkedItems.forEach(cb => {
                const li = cb.closest('li').cloneNode(true);
                li.querySelector('input').checked = false;
                toList.appendChild(li);
                cb.closest('li').remove();
            });
        }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>