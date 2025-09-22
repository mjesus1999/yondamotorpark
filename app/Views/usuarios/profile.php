<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #6f42c1;
        --accent-color: #36b9cc;
        --light-bg: #f8f9fc;
        --dark-bg: #2e59d9;
        --text-dark: #5a5c69;
        --text-light: #858796;
        --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    body {
        background-color: var(--light-bg);
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }


    .alert-gradient {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 10px;
        box-shadow: var(--card-shadow);
    }


    .breadcrumb-item a:hover {
        color: white !important;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-bottom: none;
    }

    .profile-avatar-container {
        position: relative;
    }

    .avatar-wrapper {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        position: relative;
        transition: all 0.3s ease;
    }

    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 4px solid white;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
        cursor: pointer;
        border-radius: 50%;
    }

    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }

    .avatar-wrapper:hover .profile-avatar {
        transform: scale(1.05);
    }

    .profile-info {
        margin-top: 20px;
    }

    .profile-info .badge {
        font-size: 0.9rem;
        padding: 8px 12px;
        border-radius: 20px;
        margin-bottom: 5px;
    }

    .profile-details-card {
        border: none;
        border-radius: 15px;
        background: white;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .info-item {
        padding: 12px 15px;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s;
        margin-bottom: 15px;
        border-left: 4px solid var(--primary-color);
    }

    .info-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--text-light);
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-value {
        color: var(--text-dark);
        font-weight: 500;
        margin-bottom: 0;
    }

    .btn-save-avatar {
        position: relative;
        margin-top: 15px;
        background: linear-gradient(135deg, var(--accent-color), #2c9faf);
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        z-index: 10;
    }

    .btn-save-avatar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .modal-content.bg-dark {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
    }


    .user-status {
        display: inline-block;
        width: 12px;
        height: 12px;
        background-color: #1cc88a;
        border-radius: 50%;
        margin-right: 5px;
    }



    /* Toast notification */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 250px;
        background: white;
        border-left: 4px solid;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 6px;
        padding: 15px;
        display: flex;
        align-items: center;
        transform: translateX(110%);
        transition: transform 0.3s ease;
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-notification.success {
        border-left-color: #1cc88a;
    }

    .toast-notification.error {
        border-left-color: #e74a3b;
    }

    .toast-icon {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .toast-success .toast-icon {
        color: #1cc88a;
    }

    .toast-error .toast-icon {
        color: #e74a3b;
    }

    @media (max-width: 768px) {
        .profile-details-card {
            margin-top: 20px;
        }

        .avatar-wrapper {
            width: 150px;
            height: 150px;
        }

        .info-item {
            padding: 10px;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Perfil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cuenta</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                    aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item text-primary"><a href="#">Perfil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cuenta</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div> -->


    <form action="#" id="profile" autocomplete="off">
        <?php if (!empty($usuario)): ?>
            <div class="card profile-card mb-4">
                <div class="card-header text-white py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Perfil de Usuario</h5>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="profile-avatar-container position-relative mx-auto">
                                <div class="mt-3 avatar-wrapper">
                                    <img src="<?= htmlspecialchars($usuario['avatar'] ?? '/assets/images/profile.jpg') ?>"
                                        class="profile-avatar rounded-circle" alt="Avatar" id="profile-avatar"
                                        data-bs-toggle="modal" data-bs-target="#avatarModal">

                                    <div id="avatar-overlay" class="avatar-overlay">
                                        <i class="fas fa-camera text-white fs-4"></i>
                                    </div>

                                    <input type="file" id="avatar-input" accept="image/*" class="d-none">
                                </div>

                                <a href="#" class="d-block mt-3 text-primary fw-bold small" data-bs-toggle="modal"
                                    data-bs-target="#avatarModal">
                                    <i class="fas fa-eye me-1"></i>ver foto
                                </a>

                                <button type="button" class="btn btn-save-avatar text-white" id="btnSaveAvatar"
                                    style="display:none;">
                                    <i class="fas fa-save me-1"></i>Guardar foto
                                </button>

                                <div class="profile-info mt-3">
                                    <p>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($usuario['usernick']) ?>
                                        </span>
                                    </p>
                                    <p>
                                        <span class="badge bg-warning text-dark">
                                            <i
                                                class="fas fa-envelope me-1"></i><?= htmlspecialchars($usuario['email'] ?? '—') ?>
                                        </span>
                                    </p>
                                    <p class="mt-2">
                                        <span class="user-status"></span>
                                        <small class="text-muted">En línea</small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="profile-details-card card h-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">Nombre completo</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars("{$usuario['nombres']} {$usuario['apellidos']}") ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Tipo / Número doc.</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars("{$usuario['tipodoc']} {$usuario['nrodoc']}") ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Género</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['genero']) ?></p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Fecha de nacimiento</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['fechanac']) ?></p>
                                            </div>

                                            <?php
                                            $estadoCivilLabels = [
                                                'SOL' => 'Soltero',
                                                'CAS' => 'Casado',
                                                'VDO' => 'Viudo',
                                                'DVC' => 'Divorciado',
                                                'CNV' => 'Conviviente',
                                            ];
                                            ?>
                                            <div class="info-item">
                                                <label class="info-label">Estado civil</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars(
                                                        $estadoCivilLabels[$usuario['estadocivil']] ?? $usuario['estadocivil']
                                                    ) ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Email</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['email'] ?? '—') ?></p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">Distrito</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars($usuario['nombre_distrito'] ?? '—') ?></p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Dirección</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['direccion'] ?? '—') ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Referencia</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['referencia'] ?? '—') ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Teléfono</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['telprimario']) ?></p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Teléfono alt.</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars($usuario['telalternativo'] ?? '—') ?></p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Inicio de contrato</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['fechainicio']) ?></p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Fin de contrato</label>
                                                <p class="info-value"><?= htmlspecialchars($usuario['fechafin'] ?? '—') ?>
                                                </p>
                                            </div>

                                            <div class="info-item">
                                                <label class="info-label">Cargo / Área</label>
                                                <p class="info-value">
                                                    <?= htmlspecialchars("{$usuario['cargo']} / {$usuario['area']}") ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Usuario no encontrado.
            </div>
        <?php endif; ?>
    </form>
</div>

<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white" id="avatarModalLabel">Foto de perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="<?= htmlspecialchars($usuario['avatar'] ?? '/assets/images/profile.jpg') ?>"
                    alt="Avatar Grande" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<div class="toast-notification" id="toast">
    <i class="toast-icon fas fa-check-circle"></i>
    <div class="toast-content">
        <h6 class="toast-title mb-1">Éxito</h6>
        <p class="toast-message mb-0">Operación realizada correctamente</p>
    </div>
</div>

<script>
    const avatarImg = document.getElementById('profile-avatar');
    const overlay = document.getElementById('avatar-overlay');
    const fileInput = document.getElementById('avatar-input');
    const btnSave = document.getElementById('btnSaveAvatar');
    const toast = document.getElementById('toast');
    let avatarDirty = false;

    // Mostrar overlay al hacer hover
    const container = avatarImg.parentElement;
    container.addEventListener('mouseenter', () => overlay.style.opacity = 1);
    container.addEventListener('mouseleave', () => overlay.style.opacity = 0);

    // Abrir selector de archivos al hacer clic
    [avatarImg, overlay].forEach(el => {
        el.addEventListener('click', () => fileInput.click());
    });

    // Manejar cambio de archivo
    fileInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        // Validación de tipo y tamaño (desde el primer código)
        if (!file.type.match('image.*')) {
            showToastPersnoalizado('Por favor, seleccione una imagen válida', 'error');
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            showToastPersnoalizado('La imagen no debe superar los 2MB', 'error');
            return;
        }

        avatarDirty = true;
        avatarImg.src = URL.createObjectURL(file);
        btnSave.style.display = 'block';
        showToastPersnoalizado('Imagen cargada. Haga clic en Guardar para confirmar', 'success');
    });

    // Guardar foto por AJAX
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
                showToastPersnoalizado('Foto de perfil actualizada correctamente', 'success', 2000);
                avatarDirty = false;
                btnSave.style.display = 'none';

                // Actualiza la imagen con bust-cache
                avatarImg.src = json.avatarUrl + '?t=' + Date.now();
            } else {
                showToast('Error al guardar: ' + json.error, 'error');
            }
        } catch (err) {
            showToast('Error de red al guardar la foto', 'error');
        }
    });

    // Función para mostrar notificaciones toast 
    function showToastPersnoalizado(message, type = 'success', duration = 3000) {
        const toast = document.getElementById('toast');
        const toastIcon = toast.querySelector('.toast-icon');
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');

        // Configurar según el tipo
        if (type === 'success') {
            toast.className = 'toast-notification success';
            toastIcon.className = 'toast-icon fas fa-check-circle';
            toastTitle.textContent = 'Éxito';
        } else {
            toast.className = 'toast-notification error';
            toastIcon.className = 'toast-icon fas fa-exclamation-circle';
            toastTitle.textContent = 'Error';
        }

        toastMessage.textContent = message;
        toast.classList.add('show');


        setTimeout(() => {
            toast.classList.remove('show');
        }, duration);
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>