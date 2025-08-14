<!-- resources/views/auth/recoverAccount.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorpark - Recuperar contraseña</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/login-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <style>
        .yonda {
            background-color: #FF5F00;
        }

        .hidden {
            display: none !important;
        }

        .mask {
            font-weight: 600;
        }

        .small-muted {
            font-size: .85rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <section class="ftco-section">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <!-- Imagen de fondo (mantenida) -->
                        <div class="img" style="background-image: url('/assets/images/motorpark-login.jpg');"></div>

                        <!-- Tarjeta / formulario -->
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-2">Recuperar contraseña</h3>
                                </div>
                            </div>

                            <!-- Mensajes server-side (si existen) -->
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                            <?php endif; ?>

                            <!-- ====== BLOQUE PRINCIPAL (JS: flujo en 3 pasos). Mantiene el estilo original ====== -->
                            <div id="recover-widget">
                                <div id="alerts"></div>

                                <!-- STEP 1: enviar email o teléfono (mantener diseño parecido a inputs anteriores) -->
                                <div id="step-1" class="step">
                                    <div class="mb-2">
                                        <label class="form-label">Email o teléfono</label>
                                        <input id="identifier" class="form-control"
                                            placeholder="<?= isset($email) && $email ? htmlspecialchars($email) : 'usuario@dominio.com o 919629135' ?>"
                                            value="<?= isset($telprimario) && $telprimario ? htmlspecialchars($telprimario) : (isset($email) ? htmlspecialchars($email) : '') ?>"
                                            autocomplete="off">
                                        <div class="form-text small-muted">Introduce tu email o teléfono registrado. Te
                                            enviaremos un código de 6 dígitos.</div>
                                    </div>

                                    <div class="d-grid gap-2 mt-2">
                                        <button id="btn-send" class="btn yonda text-light">Enviar código</button>
                                        <a href="/login" class="btn btn-outline-secondary">Volver al login</a>
                                    </div>
                                </div>

                                <!-- STEP 2: verificar código -->
                                <div id="step-2" class="step hidden">
                                    <p class="mb-1">Se envió el código a: <span id="sent-to" class="mask"></span></p>
                                    <div class="mb-2">
                                        <label class="form-label">Ingresa el código</label>
                                        <input id="code" class="form-control" placeholder="123456" autocomplete="off">
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button id="btn-verify" class="btn btn-success flex-fill">Verificar
                                            código</button>
                                        <button id="btn-resend" class="btn btn-outline-secondary">Reenviar</button>
                                    </div>
                                </div>

                                <!-- STEP 3: cambiar contraseña -->
                                <div id="step-3" class="step hidden">
                                    <div class="mb-2">
                                        <label class="form-label">Nueva contraseña</label>
                                        <input id="password" type="password" class="form-control" minlength="8"
                                            autocomplete="new-password">
                                        <div class="form-text small-muted">Mínimo 8 caracteres.</div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Confirmar nueva contraseña</label>
                                        <input id="password_confirm" type="password" class="form-control"
                                            autocomplete="new-password">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button id="btn-reset" class="btn btn-warning">Cambiar contraseña</button>
                                        <a href="/login" class="btn btn-outline-secondary">Volver al login</a>
                                    </div>
                                </div>
                            </div>
                            <!-- ====== FIN widget JS ====== -->

                            <!-- ====== FORMULARIO CLÁSICO (fallback si JS está deshabilitado) ====== -->
                            <noscript>
                                <hr>
                                <form method="POST" action="/recoverAccount" id="form-recover-nocs">
                                    <div class="form-group mb-2">
                                        <label class="label">Nombre Usuario</label>
                                        <input name="usernick" id="usernick_ns" class="form-control"
                                            value="<?= isset($usernick) ? htmlspecialchars($usernick) : '' ?>"
                                            placeholder="Nombre de Usuario" required>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="label">Email</label>
                                        <input name="email" id="email_ns" type="email" class="form-control"
                                            value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                                            placeholder="Email" required>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="label">Telefono Primario</label>
                                        <input name="telprimario" id="telprimario_ns" class="form-control"
                                            value="<?= isset($telprimario) ? htmlspecialchars($telprimario) : '' ?>"
                                            placeholder="Telefono Primario" required>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="label">Nueva contraseña</label>
                                        <input name="password" id="password_ns" type="password" class="form-control"
                                            minlength="8" placeholder="Nueva Contraseña" required>
                                        <div class="form-text small-muted">Mínimo 8 caracteres.</div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="label">Confirmar contraseña</label>
                                        <input name="password_confirm" id="password_confirm_ns" type="password"
                                            class="form-control" placeholder="Confirmar Contraseña" required>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="mt-1 form-control btn yonda text-light rounded submit px-3">Cambiar
                                            contraseña</button>
                                        <a href="/login"
                                            class="form-control mt-2 btn btn-outline-secondary rounded text-center px-3 d-inline-block">Volver
                                            al login</a>
                                    </div>
                                </form>
                            </noscript>
                            <!-- ====== FIN fallback ====== -->

                        </div> <!-- /.login-wrap -->
                    </div> <!-- /.wrap -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
    </section>

    <script>
        (function () {
            const byId = id => document.getElementById(id);
            const step1 = byId('step-1'), step2 = byId('step-2'), step3 = byId('step-3');
            const alerts = byId('alerts');

            function showAlert(msg, type = 'danger', timeout = 5000) {
                alerts.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
                if (timeout) setTimeout(() => alerts.innerHTML = '', timeout);
            }

            function toggleSteps(n) {
                step1.classList.toggle('hidden', n !== 1);
                step2.classList.toggle('hidden', n !== 2);
                step3.classList.toggle('hidden', n !== 3);
            }

            function maskForDisplay(value) {
                if (!value) return '';
                if (value.indexOf('@') !== -1) {
                    const parts = value.split('@');
                    const name = parts[0];
                    const domain = parts[1];
                    const visible = name.length > 1 ? name[0] : '*';
                    return visible + '***@' + domain;
                }
                const digits = value.replace(/\D/g, '');
                if (digits.length <= 4) return digits;
                return '*****' + digits.slice(-4);
            }

            function normalizePhoneInput(v) {
                v = v.trim();
                if (v.startsWith('+')) {
                    return '+' + v.slice(1).replace(/\D/g, '');
                }
                return v.replace(/\D/g, '');
            }

            // --- SEND CODE ---
            byId('btn-send').addEventListener('click', async () => {
                let identifier = byId('identifier').value.trim();
                if (!identifier) { showAlert('Ingresa email o teléfono.'); return; }

                if (identifier.includes(':')) {
                    const parts = identifier.split(':');
                    identifier = parts[0].trim();
                }

                if (/^\+?\d[\d\-\s()]+$/.test(identifier) || /^\d+$/.test(identifier)) {
                    identifier = normalizePhoneInput(identifier);
                }

                const fd = new FormData();
                fd.append('identifier', identifier);

                try {
                    console.log('Sending recover request to /recoverAccount/sendCode with', identifier);
                    const res = await fetch('/recoverAccount/sendCode', {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin' // <- envía cookie de sesión
                    });

                    const text = await res.text();
                    let data = null;
                    try { data = text ? JSON.parse(text) : null; } catch (e) { console.warn('Non-JSON response', text); }

                    if (res.ok && data && data.ok) {
                        byId('sent-to').textContent = maskForDisplay(identifier);
                        toggleSteps(2);
                        showAlert(data.message || 'Código enviado.', 'success');
                    } else {
                        const m = (data && data.message) || text || `HTTP ${res.status} ${res.statusText}`;
                        console.error('Error sendCode:', res.status, m);
                        showAlert(m || 'Error enviando código.');
                    }
                } catch (err) {
                    console.error('Fetch error sendCode:', err);
                    showAlert('Error de conexión.');
                }
            });

            byId('btn-resend').addEventListener('click', () => {
                byId('btn-send').click();
            });

            // --- VERIFY CODE ---
            byId('btn-verify').addEventListener('click', async () => {
                const code = byId('code').value.trim();
                if (!code) { showAlert('Ingresa el código.'); return; }

                const fd = new FormData();
                fd.append('code', code);

                try {
                    const res = await fetch('/recoverAccount/verifyCode', {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin' // <- envía cookie de sesión
                    });

                    // si el backend devuelve JSON esperado:
                    const data = await res.json();

                    if (res.ok && data.ok) {
                        toggleSteps(3);
                        showAlert(data.message || 'Código verificado.', 'success');
                    } else {
                        showAlert(data.message || 'Código incorrecto.');
                    }
                } catch (err) {
                    console.error('Fetch error verifyCode:', err);
                    showAlert('Error de conexión.');
                }
            });

            // --- RESET PASSWORD ---
            byId('btn-reset').addEventListener('click', async () => {
                const password = byId('password').value;
                const password_confirm = byId('password_confirm').value;

                if (!password || !password_confirm) { showAlert('Rellena las contraseñas.'); return; }
                if (password.length < 8) { showAlert('La contraseña debe tener al menos 8 caracteres.'); return; }
                if (password !== password_confirm) { showAlert('Las contraseñas no coinciden.'); return; }

                const fd = new FormData();
                fd.append('password', password);
                fd.append('password_confirm', password_confirm);

                try {
                    const res = await fetch('/recoverAccount/resetPassword', {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin' // <- envía cookie de sesión
                    });
                    const data = await res.json();
                    if (res.ok && data.ok) {
                        showAlert(data.message || 'Contraseña cambiada correctamente.', 'success');
                        setTimeout(() => window.location.href = '/login', 1300);
                    } else {
                        showAlert(data.message || 'No se pudo cambiar la contraseña.');
                    }
                } catch (err) {
                    console.error('Fetch error resetPassword:', err);
                    showAlert('Error de conexión.');
                }
            });

            // init
            toggleSteps(1);
        })();
    </script>

</body>

</html>