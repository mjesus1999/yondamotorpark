<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorpark Yonda - Recuperar Contraseña</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            background-attachment: fixed;
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        body>* {
            position: relative;
            z-index: 1;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 95, 0, 0.15) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            animation: pulse 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 95, 0, 0.1) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation: pulse 10s ease-in-out infinite reverse;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .image-section {
            background: linear-gradient(135deg, rgba(255, 94, 0, 0) 0%, rgba(200, 60, 0, 0) 100%),
                url('/assets/images/motorpark-login.jpg');
            background-size: cover;
            background-position: center;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .brand-section {
            position: relative;
            z-index: 1;
        }

        .logo-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 3em;
            font-weight: 800;
            color: white;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .logo-subtitle {
            font-size: 1.3em;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 300;
            letter-spacing: 2px;
        }

        .feature {
            display: flex;
            align-items: center;
            color: white;
            margin-bottom: 20px;
            animation: fadeInLeft 0.8s ease-out forwards;
            opacity: 0;
        }

        .feature:nth-child(1) {
            animation-delay: 0.2s;
        }

        .feature:nth-child(2) {
            animation-delay: 0.4s;
        }

        .feature:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature i {
            font-size: 1.8em;
            margin-right: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .feature-text h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .feature-text p {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .form-section {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2em;
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 1em;
        }

        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            transition: all 0.3s;
        }

        .step-item.active .step-circle {
            background: #FF5F00;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 95, 0, 0.4);
        }

        .step-item.completed .step-circle {
            background: #4CAF50;
            color: white;
        }

        .step-item.completed .step-circle i {
            font-size: 1.2em;
        }

        .step-label {
            font-size: 0.85em;
            color: #999;
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: #FF5F00;
            font-weight: 600;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
            font-size: 0.95em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1em;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #FF5F00;
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 95, 0, 0.1);
        }

        .form-control:focus+i {
            color: #FF5F00;
        }

        .input-wrapper.focused i {
            color: #FF5F00;
        }

        .form-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
            margin-left: 50px;
        }

        .code-input {
            text-align: center;
            font-size: 1.5em;
            letter-spacing: 8px;
            font-weight: 600;
            padding: 20px;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #FF5F00 0%, #E55500 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(255, 95, 0, 0.3);
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            /* background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); */
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(255, 95, 0, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-secondary {
            width: 100%;
            padding: 18px;
            background: white;
            color: #666;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #FF5F00;
            color: #FF5F00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .image-section {
                padding: 40px;
                min-height: 300px;
            }

            .logo-title {
                font-size: 2.2em;
            }

            .form-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            .form-section {
                padding: 30px 20px;
            }

            .form-header h2 {
                font-size: 1.8em;
            }

            .logo-title {
                font-size: 1.8em;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .step-circle {
                width: 35px;
                height: 35px;
            }

            .step-label {
                font-size: 0.75em;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="login-container">
            <div class="image-section">
                <!-- Imagen de fondo -->
            </div>

            <div class="form-section">
                <div class="form-header">
                    <h2>Recuperar contraseña</h2>
                    <p>Sigue los pasos para restablecer tu contraseña</p>
                </div>

                <!-- Indicador de pasos -->
                <div class="steps-indicator">
                    <div class="step-item <?= ($step ?? 1) >= 1 ? (($step ?? 1) == 1 ? 'active' : 'completed') : '' ?>">
                        <div class="step-circle">
                            <?= ($step ?? 1) > 1 ? '<i class="fas fa-check"></i>' : '1' ?>
                        </div>
                        <div class="step-label">Verificar datos</div>
                    </div>
                    <div class="step-item <?= ($step ?? 1) >= 2 ? (($step ?? 1) == 2 ? 'active' : 'completed') : '' ?>">
                        <div class="step-circle">
                            <?= ($step ?? 1) > 2 ? '<i class="fas fa-check"></i>' : '2' ?>
                        </div>
                        <div class="step-label">Código SMS</div>
                    </div>
                    <div class="step-item <?= ($step ?? 1) == 3 ? 'active' : '' ?>">
                        <div class="step-circle">3</div>
                        <div class="step-label">Nueva contraseña</div>
                    </div>
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

                <!-- PASO 1: Validar datos -->
                <?php if (($step ?? 1) == 1): ?>
                    <form action="/recoverAccount" method="POST" id="form-step1" autocomplete="off">
                        <input type="hidden" name="action" value="send_code">

                        <div class="form-group">
                            <label for="usernick">Nombre de usuario</label>
                            <div class="input-wrapper">
                                <input type="text" id="usernick" name="usernick" class="form-control"
                                    value="<?= isset($usernick) ? htmlspecialchars($usernick) : (isset($_GET['usernick']) ? htmlspecialchars($_GET['usernick']) : '') ?>"
                                    placeholder="Ingresa tu usuario" required>
                                <i class="fas fa-user"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" placeholder="tu@email.com"
                                    required>
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telprimario">Teléfono primario</label>
                            <div class="input-wrapper">
                                <input type="tel" id="telprimario" name="telprimario" class="form-control"
                                    value="<?= isset($telprimario) ? htmlspecialchars($telprimario) : '' ?>"
                                    placeholder="Número de teléfono" required>
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="form-text">Recibirás un código de verificación por SMS</div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Enviar código al teléfono</span>
                        </button>

                        <a href="/login" class="btn-secondary">
                            Volver al login
                        </a>
                    </form>
                <?php endif; ?>

                <!-- PASO 2: Verificar código SMS -->
                <?php if (($step ?? 1) == 2): ?>
                    <form action="/recoverAccount" method="POST" id="form-step2" autocomplete="off">
                        <input type="hidden" name="action" value="verify_code">
                        <input type="hidden" name="usernick" value="<?= htmlspecialchars($usernick ?? '') ?>">

                        <div class="form-group">
                            <label for="code">Código de verificación</label>
                            <div class="input-wrapper">
                                <input type="text" id="code" name="code" class="form-control code-input" maxlength="6"
                                    placeholder="000000" required pattern="[0-9]{6}">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="form-text">Ingresa el código de 6 dígitos enviado a tu teléfono</div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Verificar código</span>
                        </button>

                        <a href="/recoverAccount" class="btn-secondary">
                            Solicitar nuevo código
                        </a>
                    </form>
                <?php endif; ?>

                <!-- PASO 3: Cambiar contraseña -->
                <?php if (($step ?? 1) == 3): ?>
                    <form action="/recoverAccount" method="POST" id="form-step3" autocomplete="off">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="usernick" value="<?= htmlspecialchars($usernick ?? '') ?>">

                        <div class="form-group">
                            <label for="password">Nueva contraseña</label>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" class="form-control" minlength="8"
                                    placeholder="Mínimo 8 caracteres" required>
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmar contraseña</label>
                            <div class="input-wrapper">
                                <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                                    placeholder="Repite tu contraseña" required>
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Cambiar contraseña</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Validación paso 1
            const form1 = document.getElementById('form-step1');
            if (form1) {
                form1.addEventListener('submit', function (e) {
                    const u = document.getElementById('usernick').value.trim();
                    const em = document.getElementById('email').value.trim();
                    const tel = document.getElementById('telprimario').value.trim();

                    if (!u || !em || !tel) {
                        e.preventDefault();
                        alert('Por favor, completa todos los campos.');
                        return;
                    }
                });
            }

            // Validación paso 2
            const form2 = document.getElementById('form-step2');
            if (form2) {
                const codeInput = document.getElementById('code');

                // Solo permitir números
                codeInput.addEventListener('input', function (e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                form2.addEventListener('submit', function (e) {
                    const code = document.getElementById('code').value.trim();

                    if (code.length !== 6) {
                        e.preventDefault();
                        alert('El código debe tener 6 dígitos.');
                        return;
                    }
                });
            }

            // Validación paso 3
            const form3 = document.getElementById('form-step3');
            if (form3) {
                form3.addEventListener('submit', function (e) {
                    const p1 = document.getElementById('password').value;
                    const p2 = document.getElementById('password_confirm').value;

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
            }

            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.6s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 600);
                    });
                }, 5000);
            }

            // Animación en inputs
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