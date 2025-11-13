<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorpark Yonda - Acceso</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/styleLogin.css">
</head>

<body>
    <div class="page-wrapper">
        <div class="login-container">
            <div class="image-section">
                <!-- Imagen -->
            </div>

            <div class="form-section">
                <div class="form-header">
                    <h2>Bienvenido</h2>
                    <p>Accede a tu cuenta para continuar</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($message) ?></span>
                    </div>
                <?php endif; ?>

                <form action="/login" method="POST" class="signin-form" autocomplete="on">
                    <div class="form-group">
                        <label for="usernick">Nombre de usuario</label>
                        <div class="input-wrapper">
                            <input type="text" id="usernick" name="usernick" class="form-control"
                                placeholder="Ingresa tu usuario" required
                                value="<?= isset($old['usernick']) ? htmlspecialchars($old['usernick']) : '' ?>">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="userpassword">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" id="userpassword" name="userpassword" class="form-control"
                                placeholder="Ingresa tu contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="remember" id="remember" value="1" checked>
                        <label for="remember">Recordar mi sesión</label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Acceder</span>
                    </button>

                    <div class="form-footer">
                        <?php $prefill = isset($old['usernick']) ? urlencode($old['usernick']) : ''; ?>
                        <a href="/recoverAccount<?= $prefill ? '?usernick=' . $prefill : '' ?>" class="recover-link">
                            <i class="fas fa-key"></i>
                            <span>¿Olvidaste tu contraseña?</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.signin-form');
            form.addEventListener('submit', e => {
                e.preventDefault();

                const usernick = document.querySelector('input[name="usernick"]').value;
                const password = document.querySelector('input[name="userpassword"]').value;

                form.submit();
            });

            // Auto-hide alerts después de 4 segundos
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.6s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 600);
                    });
                }, 4000);
            }

            // Animación en los inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.parentElement.classList.add('focused');
                });
                input.addEventListener('blur', function () {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
</body>

</html>