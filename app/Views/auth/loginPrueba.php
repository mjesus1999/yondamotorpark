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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
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
            background: linear-gradient(135deg, rgba(255, 95, 0, 0.95) 0%, rgba(200, 60, 0, 0.95) 100%),
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

        .features {
            position: relative;
            z-index: 1;
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
            margin-bottom: 40px;
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

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
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
            margin-bottom: 25px;
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

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #FF5F00;
        }

        .checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #FF5F00 0%, #E55500 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(255, 95, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
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

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .recover-link {
            color: #FF5F00;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .recover-link:hover {
            color: #E55500;
            gap: 8px;
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

            .features {
                display: none;
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
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="image-section">
            <div class="brand-section">
                <h1 class="logo-title">MOTORPARK</h1>
                <p class="logo-subtitle">YONDA</p>
            </div>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-car"></i>
                    <div class="feature-text">
                        <h4>Autos Premium</h4>
                        <p>Las mejores marcas del mercado</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-motorcycle"></i>
                    <div class="feature-text">
                        <h4>Motos de Alta Gama</h4>
                        <p>Potencia y estilo en cada modelo</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <div class="feature-text">
                        <h4>Garantía Completa</h4>
                        <p>Tu inversión está protegida</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-header">
                <h2>Bienvenido</h2>
                <p>Accede a tu cuenta para continuar</p>
            </div>

            <!-- Alertas de ejemplo (en producción usar PHP) -->
            <!-- <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>Credenciales incorrectas</span>
            </div> -->

            <form action="/login" method="POST" class="signin-form" autocomplete="on">
                <div class="form-group">
                    <label for="usernick">Nombre de usuario</label>
                    <div class="input-wrapper">
                        <input type="text" id="usernick" name="usernick" class="form-control"
                            placeholder="Ingresa tu usuario" required>
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
                    <a href="/recoverAccount" class="recover-link">
                        <i class="fas fa-key"></i>
                        <span>¿Olvidaste tu contraseña?</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.signin-form');
            form.addEventListener('submit', e => {
                e.preventDefault();

                const usernick = document.querySelector('input[name="usernick"]').value;
                const password = document.querySelector('input[name="userpassword"]').value;

                // Aquí puedes agregar validación adicional si lo necesitas

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