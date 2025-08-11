<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorpark</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/public/assets/css/login-style.css">
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
                <div class="col-md-12 col-lg-5">
                    <div class="wrap d-md-flex">
                        <div class="img" style="background-image: url('/public/images/motorpark-login.jpg');"></div>

                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Recuperar contraseña</h3>
                                </div>
                            </div>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                            <?php endif; ?>

                            <!-- FORMULARIO DIRECTO: usernick + email + nueva contraseña -->
                            <form method="POST" action="/recoverAccount" id="form-recover">
                                <div class="form-group mb-3">
                                    <label class="label">Nombre Usuario</label>
                                    <input name="usernick" id="usernick" class="form-control"
                                        value="<?= isset($usernick) ? htmlspecialchars($usernick) : '' ?>" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label">Email</label>
                                    <input name="email" id="email" type="email" class="form-control"
                                        value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label">Nueva contraseña</label>
                                    <input name="password" id="password" type="password" class="form-control"
                                        minlength="8" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label">Confirmar contraseña</label>
                                    <input name="password_confirm" id="password_confirm" type="password"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit"
                                        class="form-control btn yonda text-light rounded submit px-3">Cambiar
                                        contraseña</button>
                                    <a href="/login"
                                        class="form-control mt-2 btn btn-outline-secondary rounded text-center px-3 d-inline-block">Volver</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Validación cliente: evitar envíos con datos inválidos
        document.getElementById('form-recover').addEventListener('submit', function (e) {
            const u = document.getElementById('usernick').value.trim();
            const em = document.getElementById('email').value.trim();
            const p1 = document.getEleme ntById('password').value;
            const p2 = document.getElementById('password_confirm').value;

            if (!u || !em) {
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