<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorpark</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/login-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>

<body>

    <style>
        .yonda {
            background-color: #FF5F00;
        }
    </style>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <!-- Imagen de fondo -->
                        <div class="img" style="background-image: url('/assets/images/motorpark-login.jpg');">
                        </div>

                        <!-- Formulario de Recuperación de Contraseña -->
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-2">Recuperar contraseña</h3>
                                </div>
                            </div>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                            <?php endif; ?>

                            <!-- Formulario de Recuperación -->
                            <form method="POST" action="/recoverAccount" id="form-recover" autocomplete="OFF">
                                <div class="form-group mb-2">
                                    <label class="label">Nombre Usuario</label>
                                    <input name="usernick" id="usernick" class="form-control"
                                        value="<?= isset($usernick) ? htmlspecialchars($usernick) : '' ?>"
                                        placeholder="Nombre de Usuario" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="label">Email</label>
                                    <input name="email" id="email" type="email" class="form-control"
                                        value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" placeholder="Email"
                                        required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="label">Telefono Primario</label>
                                    <input name="telprimario" id="telprimario" type="telprimario" class="form-control"
                                        value="<?= isset($telprimario) ? htmlspecialchars($telprimario) : '' ?>" placeholder="Telefono Primario"
                                        required>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="label">Nueva contraseña</label>
                                    <input name="password" id="password" type="password" class="form-control"
                                        minlength="8" placeholder="Nueva Contraseña" required>
                                    <div class="form-text small text-muted">Mínimo 8 caracteres.</div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="label">Confirmar contraseña</label>
                                    <input name="password_confirm" id="password_confirm" type="password"
                                        class="form-control" placeholder="Confirmar Contraseña" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit"
                                        class=" mt-1 form-control btn yonda text-light rounded submit px-3">Cambiar
                                        contraseña</button>
                                    <a href="/login"
                                        class="form-control mt-2 btn btn-outline-secondary rounded text-center px-3 d-inline-block">Volver
                                        al login</a>
                                </div>
                            </form>

                        </div> <!-- /.login-wrap -->
                    </div> <!-- /.wrap -->
                </div> <!-- /.col-md-12 col-lg-10 -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
    </section>

    <script>
        // Validación cliente: evitar envíos con datos inválidos
        document.getElementById('form-recover').addEventListener('submit', function (e) {
            const u = document.getElementById('usernick').value.trim();
            const em = document.getElementById('email').value.trim();
            const tel = document.getElementById('tel').value.trim();
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('password_confirm').value;

            if (!u || !em || !tel) {
                e.preventDefault();
                alert('Introduce tu usuario y email registrados.');
                return;
            }
            if (p1.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres.');
                return;
            }
            if (p1 !== p2) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                return;
            }
        });
    </script>

</body>

</html>