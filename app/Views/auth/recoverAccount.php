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
  <link rel="stylesheet" href="/assets/css/styleRecoverAccount.css">
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
                  value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" placeholder="tu@email.com" required>
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