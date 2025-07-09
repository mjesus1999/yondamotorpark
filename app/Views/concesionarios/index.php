<?php

include __DIR__ . '/../layout/header.php';
?>
<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Concesionarios</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/concesionarios/create"
                    class="">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda" id="tabla-concesionarios">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre comercial</th>
                                <th>Razón social</th>
                                <th>RUC</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($concesionarios)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay concesionarios registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>

                                <?php foreach ($concesionarios as $concesionario): ?>

                                    <tr>
                                        <td><?= htmlspecialchars($numeroFila++) ?></td>
                                        <td><?= htmlspecialchars($concesionario['nombrecomercial']) ?> </td>
                                        <td><?= htmlspecialchars($concesionario['razonsocial']) ?></td>
                                        <td><?= htmlspecialchars($concesionario['ruc']) ?></td>
                                        <td>
                                            <a href='#' title='Editar nombre comercial' data-idconcesionario='<?= htmlspecialchars($concesionario['idconcesionario'])?>' data-nombrecomercial='<?=htmlspecialchars($concesionario['nombrecomercial']) ?>' class='btn btn-sm btn-outline-primary edit'><i class="fa-solid fa-pen"></i></a>
                                            <a href='#' title='Eliminar' data-idconcesionario='${element.idconcesionario}' class='btn btn-sm btn-outline-danger delete'><i class="fa-solid fa-trash"></i></a>
                                            <a href='/concesionario/tiendas/<?= htmlspecialchars($concesionario['ruc']) ?>' title='Ver tiendas' data-idtienda='${element.idconcesionario}' class='btn btn-sm btn-outline-secondary'><i class="fa-solid fa-shop"></i></a>
                                        </td>
                                    </tr>


                                <?php endforeach; ?>


                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <!-- Zona modales -->
    <div class="modal fade" id="modal-concesionario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-concesionario" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" autocomplete="off" id="formulario-concesionario">
                <div class="modal-content">
                    <div class="modal-header bg-yonda">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar concesionario</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombre-comercial" placeholder="Nombre comercial" required>
                            <label for="nombre-comercial">Nombre comercial</label>
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

    <?php include __DIR__ . '/../layout/footer.php'; ?>