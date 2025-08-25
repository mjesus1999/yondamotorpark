<?php include __DIR__ . '/../layout/header.php'; ?>

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

        <!-- Vista de escritorio -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-sm table-hover" id="tabla-concesionarios">
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
                  <td colspan="5" class="text-center">No hay concesionarios registrados.</td>
                </tr>
              <?php else: ?>
                <?php $numeroFila = 1; ?>
                <?php foreach ($concesionarios as $concesionario): ?>
                  <tr>
                    <td><?= htmlspecialchars($numeroFila++) ?></td>
                    <td><?= htmlspecialchars($concesionario['nombrecomercial']) ?></td>
                    <td><?= htmlspecialchars($concesionario['razonsocial']) ?></td>
                    <td><?= htmlspecialchars($concesionario['ruc']) ?></td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="#" title="Editar nombre comercial"
                          data-idconcesionario="<?= htmlspecialchars($concesionario['idconcesionario']) ?>"
                          data-nombrecomercial="<?= htmlspecialchars($concesionario['nombrecomercial']) ?>"
                          class="btn btn-sm btn-outline-primary edit">
                          <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="#" title="Eliminar"
                          data-idconcesionario="<?= htmlspecialchars($concesionario['idconcesionario']) ?>"
                          class="btn btn-sm btn-outline-danger delete">
                          <i class="fa-solid fa-trash"></i>
                        </a>
                        <a href="/concesionarios/gestionar/<?= htmlspecialchars($concesionario['ruc']) ?>"
                          title="Ver tiendas"
                          class="btn btn-sm btn-outline-secondary">
                          <i class="fa-solid fa-shop"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Vista móvil (acordeón) -->
        <div class="d-block d-md-none">
          <?php if (!empty($concesionarios)) : ?>
            <?php $numeroFila = 1; ?>
            <?php foreach ($concesionarios as $concesionario): ?>
              <div class="card mb-2 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseConcesionario<?= $concesionario['idconcesionario'] ?>"
                  aria-expanded="false"
                  aria-controls="collapseConcesionario<?= $concesionario['idconcesionario'] ?>"
                  style="cursor: pointer;">
                  <span><i class="bi bi-truck me-2"></i><?= htmlspecialchars($concesionario['nombrecomercial']) ?></span>
                  <i class="bi bi-chevron-down"></i>
                </div>
                <div id="collapseConcesionario<?= $concesionario['idconcesionario'] ?>" class="collapse">
                  <div class="card-body">
                    <p><strong>#:</strong> <?= htmlspecialchars($numeroFila++) ?></p>
                    <p><strong>Razón social:</strong> <?= htmlspecialchars($concesionario['razonsocial']) ?></p>
                    <p><strong>RUC:</strong> <?= htmlspecialchars($concesionario['ruc']) ?></p>
                    <p><strong>Acciones:</strong></p>
                   <div class="d-flex gap-1">
                        <a href="#" title="Editar nombre comercial"
                          data-idconcesionario="<?= htmlspecialchars($concesionario['idconcesionario']) ?>"
                          data-nombrecomercial="<?= htmlspecialchars($concesionario['nombrecomercial']) ?>"
                          class="btn btn-sm btn-outline-primary edit">
                          <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="#" title="Eliminar"
                          data-idconcesionario="<?= htmlspecialchars($concesionario['idconcesionario']) ?>"
                          class="btn btn-sm btn-outline-danger delete">
                          <i class="fa-solid fa-trash"></i>
                        </a>
                        <a href="/concesionarios/gestionar/<?= htmlspecialchars($concesionario['ruc']) ?>"
                          title="Ver tiendas"
                          class="btn btn-sm btn-outline-secondary">
                          <i class="fa-solid fa-shop"></i>
                        </a>
                      </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted p-3">No hay concesionarios registrados.</div>
          <?php endif; ?>
        </div>

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
      const cardBody = document.querySelector('.card-body');
      const modalConcesionario = new bootstrap.Modal(document.getElementById('modal-concesionario'));
      const formulario = document.querySelector('#formulario-concesionario');
      const inputNombreComercial = document.querySelector('#nombre-comercial');

      let idActual = null;


      // Cargar datos al modal
      const abrirModalEdicion = (id, nombre) => {
        idActual = id;
        inputNombreComercial.value = nombre;
        modalConcesionario.show();
      };

      // Actualizar concesionario
      const actualizarConcesionario = async () => {
        const nombrecomercial = inputNombreComercial.value.trim();

        if (!nombrecomercial) {
          mostrarMensaje("El nombre comercial es obligatorio", "WARNING");
          return;
        }

        try {
          const formData = new FormData();
          formData.append('nombrecomercial', nombrecomercial);

          const res = await fetch(`/concesionarios/update/${idActual}`, {
            method: 'POST',
            body: formData
          });

          const data = await res.json();

          showToast(data.message, data.success ? 'SUCCESS' : 'WARNING', 1000);
          if (data.success) {
            modalConcesionario.hide();
            setTimeout(() => location.reload(), 1000);
          }
        } catch (error) {
          console.error("Error al actualizar:", error);
          showToast("Error inesperado", "ERROR", 1000);
        }
      };

      // Eliminar concesionario
      const eliminarConcesionario = async (id) => {
        if (!confirm("¿Seguro que desea eliminar este concesionario?")) return;

        try {
          const res = await fetch(`/concesionarios/delete/${id}`, {
            method: 'POST'
          });

          const data = await res.json();
          showToast(data.message, data.success ? 'SUCCESS' : 'INFO', 1000);

          if (data.success) setTimeout(() => location.reload(), 1000);
        } catch (error) {
          console.error("Error al eliminar:", error);
          showToast('Error inesperado', 'WARNING', 1000);
        }
      };

      // Eventos: editar o eliminar
      cardBody.addEventListener('click', async (e) => {
        const btnEdit = e.target.closest('.edit');
        const btnDelete = e.target.closest('.delete');

        if (btnEdit) {
          e.preventDefault();
          abrirModalEdicion(
            btnEdit.dataset.idconcesionario,
            btnEdit.dataset.nombrecomercial
          );
        }

        if (btnDelete) {
          e.preventDefault();
          eliminarConcesionario(btnDelete.dataset.idconcesionario);
        }
      });

      // Evento: guardar cambios desde modal
      formulario.addEventListener('submit', async (e) => {
        e.preventDefault();
        await actualizarConcesionario();
      });
    });
  </script>
  <?php include __DIR__ . '/../layout/footer.php'; ?>