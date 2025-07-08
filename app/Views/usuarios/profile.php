<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

  <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex">
        <nav
          style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
          aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Perfil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cuenta</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <!-- Campos -->
  <div class="mb-2">
    <form action="#" id="profile" autocomplete="off">

      <!-- DATOS DEL USUARIO -->
      <div class="card mb-4">

        <div class="card-body">

          <!-- MOSTRAR LOS DATOS DEL USUARIO -->
          <?php if (!empty($usuario)): ?>
            <div class="row mb-3">
              <div class="col-md-4 text-center">

                <!-- Contenedor del avatar con overlay -->
                <div class="position-relative d-inline-block" style="width:200px; height:200px;">
                  <!-- La imagen de perfil -->
                  <img src="<?= htmlspecialchars($usuario['avatar'] ?? '/assets/images/profile.jpg') ?>"
                    class="rounded-circle" style="width:100%; height:100%; object-fit:cover; cursor:pointer;" alt="Avatar"
                    id="profile-avatar">

                  <!-- Overlay oculto que aparece al hover -->
                  <div id="avatar-overlay" class="position-absolute top-0 start-0 w-100 h-100 rounded-circle"
                    style="background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .2s; cursor:pointer;">
                    <i class="fa fa-camera text-white fs-4"></i>
                  </div>

                  <!-- Input oculto para seleccionar fichero -->
                  <input type="file" id="avatar-input" accept="image/*" style="display:none;">
                </div>
                
                <button type="button" class="btn btn-primary" id="btnSaveAvatar" style="display:none;">
                  Guardar foto
                </button>

                <h5 class="mt-2"><?= htmlspecialchars($usuario['usernick']) ?></h5>
                <p class="text-muted mb-0"><?= htmlspecialchars($usuario['email'] ?? '—') ?></p>
              </div>
              <div class="col-md-8">
                <dl class="row">
                  <dt class="col-sm-4">Nombre completo</dt>
                  <dd class="col-sm-8">
                    <?= htmlspecialchars("{$usuario['nombres']} {$usuario['apellidos']}") ?>
                  </dd>

                  <dt class="col-sm-4">Tipo / Número doc.</dt>
                  <dd class="col-sm-8">
                    <?= htmlspecialchars("{$usuario['tipodoc']} {$usuario['nrodoc']}") ?>
                  </dd>

                  <dt class="col-sm-4">Género</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['genero']) ?></dd>

                  <dt class="col-sm-4">Fecha de nacimiento</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['fechanac']) ?></dd>

                  <?php
                  $estadoCivilLabels = [
                    'SOL' => 'Soltero',
                    'CAS' => 'Casado',
                    'VDO' => 'Viudo',
                    'DVC' => 'Divorciado',
                    'CNV' => 'Conviviente',
                  ];
                  ?>

                  <dt class="col-sm-4">Estado civil</dt>
                  <dd class="col-sm-8">
                    <?= htmlspecialchars(
                      $estadoCivilLabels[$usuario['estadocivil']]
                      ?? $usuario['estadocivil']
                    )
                      ?>
                  </dd>

                  <dt class="col-sm-4">Email</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['email'] ?? '—') ?></dd>

                  <dt class="col-sm-4">Distrito</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['nombre_distrito'] ?? '—') ?></dd>

                  <dt class="col-sm-4">Dirección</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['direccion'] ?? '—') ?></dd>

                  <dt class="col-sm-4">Referencia</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['referencia'] ?? '—') ?></dd>

                  <dt class="col-sm-4">Teléfono</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['telprimario']) ?></dd>

                  <dt class="col-sm-4">Teléfono alt.</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['telalternativo'] ?? '—') ?></dd>

                  <dt class="col-sm-4">Inicio de contrato</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['fechainicio']) ?></dd>

                  <dt class="col-sm-4">Fin de contrato</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars($usuario['fechafin']) ?></dd>

                  <dt class="col-sm-4">Cargo / Área</dt>
                  <dd class="col-sm-8"><?= htmlspecialchars("{$usuario['cargo']} / {$usuario['area']}") ?></dd>
                </dl>
              </div>
            </div>
          <?php else: ?>
            <p>Usuario no encontrado.</p>
          <?php endif; ?>

        </div> <!-- ./card-body -->

      </div><!-- ./card -->

    </form>
  </div>

</div>

<script>
  const avatarImg = document.getElementById('profile-avatar');
  const overlay = document.getElementById('avatar-overlay');
  const fileInput = document.getElementById('avatar-input');
  const btnSave = document.getElementById('btnSaveAvatar');

  let avatarDirty = false;

  // 1. Hover: mostrar/ocultar overlay
  const container = avatarImg.parentElement;
  container.addEventListener('mouseenter', () => overlay.style.opacity = 1);
  container.addEventListener('mouseleave', () => overlay.style.opacity = 0);

  // 2. Click en imagen o overlay: disparar selector de fichero
  [avatarImg, overlay].forEach(el =>
    el.addEventListener('click', () => fileInput.click())
  );

  // 3. Cambio de fichero: previsualizar y marcar dirty + mostrar botón
  fileInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    avatarDirty = true;
    avatarImg.src = URL.createObjectURL(file);
    btnSave.style.display = 'inline-block';
  });

  // 4. Antes de salir: advertir si hay cambios sin guardar
  window.addEventListener('beforeunload', e => {
    if (!avatarDirty) return;
    e.preventDefault();
    e.returnValue = '';
  });

  // 5. Guardar foto por AJAX
  btnSave.addEventListener('click', async () => {
    if (!avatarDirty) return;

    const form = new FormData();
    form.append('avatar', fileInput.files[0]);

    try {
      const resp = await fetch('/usuarios/profile/avatar', {
        method: 'POST',
        body: form
      });
      const json = await resp.json();

      if (json.success) {
        // Resetea estado y oculta botón
        avatarDirty = false;
        btnSave.style.display = 'none';

        // Actualiza la imagen con bust-cache
        avatarImg.src = json.avatarUrl + '?t=' + Date.now();
      } else {
        alert('Error al guardar: ' + json.error);
      }
    } catch (err) {
      alert('Error de red al guardar la foto');
    }
  });
</script>



<?php include __DIR__ . '/../layout/footer.php'; ?>