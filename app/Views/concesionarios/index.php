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
                                            <a href='#' title='Editar nombre comercial' data-idconcesionario='<?= htmlspecialchars($concesionario['idconcesionario']) ?>' data-nombrecomercial='<?= htmlspecialchars($concesionario['nombrecomercial']) ?>' class='btn btn-sm btn-outline-primary edit'><i class="fa-solid fa-pen"></i></a>
                                            <a href='#' title='Eliminar' data-idconcesionario='<?= $concesionario['idconcesionario'] ?>' class='btn btn-sm btn-outline-danger delete'>
  <i class="fa-solid fa-trash"></i>
</a>

                                            <a href="/concesionarios/gestionar/<?= $concesionario['ruc'] ?>" title="Ver tiendas" class="btn btn-sm btn-outline-secondary">
  <i class="fa-solid fa-shop"></i> 
</a>

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



    <script>
  document.addEventListener('DOMContentLoaded', () => {

    const tablaConcesionarios = document.querySelector('#tabla-concesionarios');
    const modalConcesionario = new bootstrap.Modal(document.getElementById('modal-concesionario'));
    const formulario = document.querySelector('#formulario-concesionario');
    const inputNombreComercial = document.querySelector('#nombre-comercial');

    let idActual = null;

    // Abrir modal y cargar datos al hacer clic en el botón de editar
    tablaConcesionarios.addEventListener('click', (e) => {
      const btnEdit = e.target.closest('.edit');
      if (btnEdit) {
        e.preventDefault();

        idActual = btnEdit.dataset.idconcesionario;
        const nombreActual = btnEdit.dataset.nombrecomercial;

        inputNombreComercial.value = nombreActual;

        modalConcesionario.show();
      }
    });

    // Enviar actualización al servidor
    formulario.addEventListener('submit', async (e) => {
      e.preventDefault();

      const nombrecomercial = inputNombreComercial.value.trim();

      if (!nombrecomercial) {
        showToast("El nombre comercial es obligatorio", "WARNING", 2000);
        return;
      }

      try {
        const formData = new FormData();
        formData.append('nombrecomercial', nombrecomercial);

        const response = await fetch(`/concesionarios/update/${idActual}`, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          showToast(data.message, 'SUCCESS', 1500);
          modalConcesionario.hide();
          setTimeout(() => {
            location.reload(); 
          }, 1500);
        } else {
          showToast(data.message, 'WARNING', 1500);
        }

      } catch (error) {
        console.error('Error al actualizar:', error);
        showToast("Error inesperado", "ERROR", 2000);
      }
    });

    tablaConcesionarios.addEventListener('click', async (e) => {
  const btnDelete = e.target.closest('.delete');
  if (btnDelete) {
    e.preventDefault();

    const id = btnDelete.dataset.idconcesionario;

    if (!confirm("¿Seguro que desea eliminar este concesionario?")) return;

    try {
      const response = await fetch(`/concesionarios/delete/${id}`, {
        method: 'POST'
      });

      const data = await response.json();

      showToast(data.message, data.success ? 'SUCCESS' : 'WARNING',1500);

      if (data.success) {
        setTimeout(() => location.reload(), 1500);
      }

    } catch (error) {
      console.error("Error al eliminar:", error);
      showToast("Error inesperado", "ERROR", 1500);
    }
  }
});


  });
</script>


    <?php include __DIR__ . '/../layout/footer.php'; ?>