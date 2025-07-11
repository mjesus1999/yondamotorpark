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
      <div class="col-md-6 d-flex">
        <nav
          style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
          aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Listar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <a href="/usuarios/create" class="">[ Registrar ]</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <table class="table table-sm table-hover table-hover-yonda" id="tabla-usuarios">
            <thead>
              <tr>
                <th>#</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Area</th>
                <th>Cargo</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Usuario</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($Usuarios as $u): ?>
                <tr>
                  <td><?= htmlspecialchars($u['idpersona']) ?></td>
                  <td><?= htmlspecialchars($u['apellidos']) ?></td>
                  <td><?= htmlspecialchars($u['nombres']) ?></td>
                  <td><?= htmlspecialchars($u['area']) ?></td>
                  <td><?= htmlspecialchars($u['cargo']) ?></td>
                  <td><?= htmlspecialchars($u['fecha_inicio']) ?></td>
                  <td><?= htmlspecialchars($u['fecha_fin']) ?></td>
                  <td><?= htmlspecialchars($u['usuario']) ?></td>
                  <td>
                    <!-- Editar -->
                    <a href="<?= $path ?>/usuarios/editar/<?= $u['idpersona'] ?>" class="btn btn-sm btn-outline-primary"
                      title="Editar">
                      <i class="fa-solid fa-pen"></i>
                    </a>

                    <!-- Cambiar contraseña -->
                    <button type="button" class="btn btn-sm btn-outline-warning mx-1 btn-cambiar-clave"
                      title="Cambiar contraseña" data-bs-toggle="modal" data-bs-target="#modalCambiarClave"
                      data-idcolab="<?= $u['idcolaborador'] ?>" data-usuario="<?= htmlspecialchars($u['usuario']) ?>">
                      <i class="fa-solid fa-key"></i>
                    </button>

                    <!-- Eliminar -->
                    <button type="button" class="btn btn-sm btn-outline-danger btn-borrar" title="Eliminar"
                      data-idcolab="<?= $u['idcolaborador'] ?>">
                      <i class="fa-solid fa-trash"></i>
                    </button>
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

<!-- CAMBIO DE PASSWORD MODAL -->
<div class="modal fade" id="modalCambiarClave" tabindex="-1" aria-labelledby="modalCambiarClaveLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-yonda">
        <h5 class="modal-title" id="modalCambiarClaveLabel">Cambiar contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formCambiarClave" action="/usuarios/changePassword" method="POST" autocomplete="off">
          <input type="hidden" id="cc-idcolaborador" name="idcolaborador">

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="cc-usuario" name="usuario" readonly>
            <label for="cc-usuario">Usuario</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="cc-password1" name="password1"
              pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$"
              title="Mínimo 8 caracteres: incluye letra, número y símbolo" required>
            <label for="cc-password1">Nueva contraseña</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="cc-password2" name="password2" required>
            <label for="cc-password2">Repetir contraseña</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary" id="btnAceptarCambiarClave">Aceptar</button>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-cambiar-clave').forEach(btn => {
      btn.addEventListener('click', () => {
        const idColab = btn.getAttribute('data-idcolab');
        const usuario = btn.getAttribute('data-usuario');

        // Asegúrate de apuntar al mismo id que tu input hidden:
        document.getElementById('cc-idcolaborador').value = idColab;
        document.getElementById('cc-usuario').value = usuario;

        bootstrap.Modal.getOrCreateInstance('#modalCambiarClave').show();
      });
    });

    document.getElementById('btnAceptarCambiarClave').addEventListener('click', () => {
      const form = document.getElementById('formCambiarClave');
      const formData = new FormData(form);

      fetch('/api/usuarios/changePassword', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(json => {
          // cierro el modal
          const modalEl = document.getElementById('modalCambiarClave');
          bootstrap.Modal.getInstance(modalEl).hide();
          form.reset();

          window.location.reload();
        })
        .catch(err => {
          console.error(err);
          alert('Error de conexión.');
        });
    });
    document.querySelectorAll('.btn-borrar').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.getAttribute('data-idcolab');
        const { isConfirmed } = await Swal.fire({
          title: '¿Eliminar usuario?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
          reverseButtons: true
        });
        if (!isConfirmed) return;

        // Creamos y enviamos un formulario POST tradicional:
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/usuarios/disabled/${id}`;
        document.body.appendChild(form);
        form.submit();
      });
    });

  });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>