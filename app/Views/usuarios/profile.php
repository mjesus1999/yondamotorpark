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
                    class="rounded-circle"
                    style="width:100%; height:100%; object-fit:cover; cursor:pointer;"
                    alt="Avatar"
                    id="profile-avatar"
                    data-bs-toggle="modal"
                    data-bs-target="#avatarModal">

                    <a href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">Ver foto</a>

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

                <h5 class="mt-2"> <span class="badge bg-info"><?= htmlspecialchars($usuario['usernick']) ?></span></h5>
                <p class="text-white  badge bg-primary mb-0"><?= htmlspecialchars($usuario['email'] ?? '—') ?></p>
              </div>
              <div class="col-md-8 mt-2">
                <div class="card shadow-sm rounded-3 p-4 ">
                  <dl class="row mb-0">
                    <dt class="col-12 col-md-4 text-primary fw-bold">Nombre completo</dt>
                    <dd class="col-12 col-md-8 text-md-end">
                      <?= htmlspecialchars("{$usuario['nombres']} {$usuario['apellidos']}") ?>
                    </dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Tipo / Número doc.</dt>
                    <dd class="col-12 col-md-8 text-md-end">
                      <?= htmlspecialchars("{$usuario['tipodoc']} {$usuario['nrodoc']}") ?>
                    </dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Género</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['genero']) ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Fecha de nacimiento</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['fechanac']) ?></dd>

                    <?php
                    $estadoCivilLabels = [
                      'SOL' => 'Soltero',
                      'CAS' => 'Casado',
                      'VDO' => 'Viudo',
                      'DVC' => 'Divorciado',
                      'CNV' => 'Conviviente',
                    ];
                    ?>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Estado civil</dt>
                    <dd class="col-12 col-md-8 text-md-end">
                      <?= htmlspecialchars(
                        $estadoCivilLabels[$usuario['estadocivil']]
                          ?? $usuario['estadocivil']
                      ) ?>
                    </dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Email</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['email'] ?? '—') ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Distrito</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['nombre_distrito'] ?? '—') ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Dirección</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['direccion'] ?? '—') ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Referencia</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['referencia'] ?? '—') ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Teléfono</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['telprimario']) ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Teléfono alt.</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['telalternativo'] ?? '—') ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Inicio de contrato</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['fechainicio']) ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Fin de contrato</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars($usuario['fechafin']) ?></dd>

                    <dt class="col-12 col-md-4 text-primary fw-bold">Cargo / Área</dt>
                    <dd class="col-12 col-md-8 text-md-end"><?= htmlspecialchars("{$usuario['cargo']} / {$usuario['area']}") ?></dd>
                  </dl>
                </div>
              </div>


            </div>
          <?php else: ?>
            <p>Usuario no encontrado.</p>
          <?php endif; ?>
        </div> <!-- ./card-body -->
      </div><!-- ./card -->

    </form>
  </div>

  <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-transparent border-0">
        <div class="modal-body p-0">
          <img src="<?= htmlspecialchars($usuario['avatar'] ?? '/assets/images/profile.jpg') ?>" alt="Avatar Grande" class="img-fluid rounded">
        </div>
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
    </div>
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
      const resp = await fetch('/api/usuarios/profile/avatar', {
        method: 'POST',
        body: form
      });
      const json = await resp.json();

      if (json.success) {
        showToast('Foto de perfil actualizado', 'SUCCESS', 1200);
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