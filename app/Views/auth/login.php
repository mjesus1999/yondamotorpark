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
                        <div class="img" style="background-image: url('/assets/images/motorpark-login.jpg');">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Motorpark App 1.0</h3>
                                </div>
                            </div>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                            <?php endif; ?>
                            <form action="/login" method="POST" class="signin-form" id="formulario-login">
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Nombre de usuario</label>
                                    <input type="text" name="usernick" class="form-control"
                                        placeholder="Nombre de usuario" required
                                        value="<?= isset($old['usernick']) ? htmlspecialchars($old['usernick']) : '' ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">Contraseña</label>
                                    <input type="password" name="userpassword" class="form-control"
                                        placeholder="Contraseña" required>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit"
                                        class="form-control btn yonda text-light rounded submit px-3">Acceder</button>
                                </div>
                                <div class="form-group">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="checkbox-wrap checkbox-primary mb-0 mt-4">
                                            Recordar
                                            <input type="checkbox" name="remember" value="1" checked>
                                            <span class="checkmark"></span>
                                        </label>

                                        <?php $prefill = isset($old['usernick']) ? urlencode($old['usernick']) : ''; ?>
                                        <a href="/recoverAccount<?= $prefill ? '?usernick=' . $prefill : '' ?>">Recuperar
                                            contraseña</a>
                                    </div>

                                    <!-- <div class="mt-3 text-center">
                                        <span>¿No estás registrado? <a href="createAccountAuth"
                                                class="fw-semibold">Crear
                                                cuenta</a></span>
                                    </div> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.signin-form');
            form.addEventListener('submit', e => {
                //evitar el envío automático
                e.preventDefault();

                const usernick = document.querySelector('input[name="usernick"]').value;
                const password = document.querySelector('input[name="userpassword"]').value;

                /* console.log('Enviando login:', { usernick, password }); */

                form.submit();
            });
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 600); // Después de desvanecer
                    });
                }, 4000); // 4 segundos
            }
        });
    </script>

    <!-- imprime en consola -->
    <!-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.signin-form');
            form.addEventListener('submit', e => {
                e.preventDefault();

                const usernick = document.querySelector('input[name="usernick"]').value;
                const password = document.querySelector('input[name="userpassword"]').value;

                console.log('Enviando login:', { usernick, password });

                // Espera 1 segundo antes de enviar
                setTimeout(() => form.submit(), 1000);
            });
        });
    </script> -->

</body>

</html>