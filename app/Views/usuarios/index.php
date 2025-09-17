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
      <div class="col-md-6 d-flex align-items-center justify-content-start">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Listar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <a href="/usuarios/create" class="btn btn-outline-primary btn-sm">
          <!-- <i class="bi bi-plus"></i> -->Registrar
        </a>
      </div>
    </div>
  </div>
  <!-- <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex">

        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
          <li class="breadcrumb-item active" aria-current="page">Listar</li>
        </ol>

      </div>
      <div class="col-md-6 text-end">
        <a href="/usuarios/create" class="">[ Registrar ]</a>
        <a href="/usuarios/create" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-plus"></i>Registrar
        </a>
      </div>
    </div>
  </div> -->

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
                <th>Aréa</th>
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
                  <td><?= htmlspecialchars($u['idcolaborador']) ?></td>
                  <td><?= htmlspecialchars($u['apellidos']) ?></td>
                  <td><?= htmlspecialchars($u['nombres']) ?></td>
                  <td><?= htmlspecialchars($u['area']) ?></td>
                  <td><?= htmlspecialchars($u['cargo']) ?></td>
                  <td><?= htmlspecialchars($u['fecha_inicio']) ?></td>
                  <td><?= htmlspecialchars($u['fecha_fin']) ?></td>

                  <!-- Usuario con restricción horaria -->
                  <td>
                    <?= htmlspecialchars($u['usuario']) ?>
                    <?php if (!empty($u['restriccionhoraria']) && $u['restriccionhoraria'] === 'S'): ?>
                      <span class="clock-emoji" role="img" aria-label="Usuario con restricción horaria"
                        data-bs-toggle="tooltip" data-bs-title="Restricción horaria">
                        🕜
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="text-center">
                    <!-- Editar -->
                    <a href="/usuarios/edit/<?= $u['idcolaborador'] ?>" class="btn btn-sm btn-outline-primary"
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

                    <!-- Restricción horaria  -->
                    <form method="POST" action="/usuarios/toggleRestriccion" class="d-inline-block toggle-restr-form">
                      <input type="hidden" name="idcolaborador" value="<?= (int) $u['idcolaborador'] ?>">
                      <?php $isRestr = (!empty($u['restriccionhoraria']) && strtoupper($u['restriccionhoraria']) === 'S'); ?>
                      <button type="submit"
                        class="btn btn-sm <?= $isRestr ? 'btn-outline-secondary' : 'btn-outline-info' ?>"
                        title="<?= $isRestr ? 'Quitar restricción horaria' : 'Poner restricción horaria' ?>"
                        data-idcolab="<?= $u['idcolaborador'] ?>">
                        <?php if ($isRestr): ?>
                          <i class="fa-solid fa-clock"></i>
                        <?php else: ?>
                          <i class="fa-regular fa-clock"></i>
                        <?php endif; ?>
                      </button>
                    </form>

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
        <button type="button" class="btn btn-sm btn-primary" id="btnAceptarCambiarClave">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>

  function attachCambiarClaveConfirm() {
    const btnAceptar = document.getElementById('btnAceptarCambiarClave');
    if (!btnAceptar) return;

    btnAceptar.addEventListener('click', async (e) => {
      e.preventDefault();

      let confirmado;
      if (typeof ask === 'function') {
        confirmado = await ask('¿Desea confirmar el cambio de contraseña?', '¿Cambiar contraseña?');
      } else {
        confirmado = confirm('¿Desea confirmar el cambio de contraseña?');
      }

      if (!confirmado) return;

      const form = document.getElementById('formCambiarClave');
      const formData = new FormData(form);

      // Deshabilitar botón durante el proceso
      btnAceptar.disabled = true;
      btnAceptar.innerHTML = 'Cambiando...';

      try {
        const res = await fetch('/api/usuarios/changePassword', {
          method: 'POST',
          body: formData
        });

        const json = await res.json();

        // Cerrar modal
        const modalEl = document.getElementById('modalCambiarClave');
        bootstrap.Modal.getInstance(modalEl).hide();
        form.reset();

        window.location.reload();
      } catch (err) {
        console.error(err);
        alert('Error de conexión.');

        // Restaurar botón
        btnAceptar.disabled = false;
        btnAceptar.innerHTML = 'Aceptar';
      }
    });
  }

  function attachEliminarUsuarioConfirm() {
    document.querySelectorAll('.btn-borrar').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const id = btn.getAttribute('data-idcolab');

        let confirmado;
        if (typeof ask === 'function') {
          confirmado = await ask('¿Desea confirmar la eliminación de este usuario?', '¿Eliminar usuario?');
        } else {
          confirmado = confirm('¿Desea confirmar la eliminación de este usuario?');
        }

        if (!confirmado) return;

        // Deshabilitar botón durante el proceso
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        // Enviar formulario POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/usuarios/disabled/${id}`;
        document.body.appendChild(form);
        form.submit();
      });
    });
  }

  function attachRestriccionHorariaConfirm() {
    document.querySelectorAll('.toggle-restr-form').forEach(form => {
      form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const id = btn.getAttribute('data-idcolab');
        const isActive = btn.classList.contains('btn-outline-secondary');

        const mensaje = isActive
          ? '¿Desea confirmar quitar la restricción horaria de este usuario?'
          : '¿Desea confirmar poner restricción horaria a este usuario?';
        const titulo = isActive ? '¿Quitar restricción?' : '¿Poner restricción?';

        let confirmado;
        if (typeof ask === 'function') {
          confirmado = await ask(mensaje, titulo);
        } else {
          confirmado = confirm(mensaje);
        }

        if (!confirmado) return;

        // Deshabilitar botón durante el proceso
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        form.submit();
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Configurar modal cambiar clave
    document.querySelectorAll('.btn-cambiar-clave').forEach(btn => {
      btn.addEventListener('click', () => {
        const idColab = btn.getAttribute('data-idcolab');
        const usuario = btn.getAttribute('data-usuario');

        document.getElementById('cc-idcolaborador').value = idColab;
        document.getElementById('cc-usuario').value = usuario;

        bootstrap.Modal.getOrCreateInstance('#modalCambiarClave').show();
      });
    });

    // Adjuntar confirmaciones
    attachCambiarClaveConfirm();
    attachEliminarUsuarioConfirm();
    attachRestriccionHorariaConfirm();
  });

</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>