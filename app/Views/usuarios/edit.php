<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Usuario</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/usuarios" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Campos -->
    <div class="mb-2">
        <!-- <?php
        echo '<pre style="background:#fff;padding:8px;">$usuario:' . PHP_EOL;
        print_r($usuario);
        echo PHP_EOL . '$areas:' . PHP_EOL;
        print_r($areas);
        echo PHP_EOL . '$cargos:' . PHP_EOL;
        print_r($cargos);
        echo '</pre>';
        ?> -->

        <form method="POST" action="/usuarios/update" id="formUpdateUsuario" autocomplete="OFF">
            <input type="hidden" name="idcolaborador" value="<?= htmlspecialchars($usuario['idcolaborador'] ?? '') ?>">

            <div class="card mb-3">
                <div class="card-header bg-info">
                    <strong>Paso 1:</strong> <span class="fst-italic">
                        Datos de la persona
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">

                        <!-- CAMPO DE DNI -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="nrodoc" class="form-control"
                                    value="<?= htmlspecialchars($usuario['nrodoc'] ?? '') ?>" required>
                                <label for="nrodoc">DNI</label>
                            </div>
                        </div>

                        <!-- CAMPO DE APELLIDOS Y NOMBRES -->
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" name="apellidos" class="form-control"
                                    value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" required>
                                <label for="form-label">Apellidos</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" name="nombres" class="form-control"
                                    value="<?= htmlspecialchars($usuario['nombres'] ?? '') ?>" required>
                                <label for="form-label">Nombres</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-info">
                    <strong>Paso 2:</strong> <span class="fst-italic">
                        Registrar del contrato
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <!-- Campo de área -->
                        <div class="col-md-4 mb-2">
                            <div class="form-floating">
                                <select name="idarea" id="idarea" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['idarea'] ?>" <?= (isset($usuario['idarea']) && $usuario['idarea'] == $a['idarea']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($a['area']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="area">Áreas</label>
                            </div>
                        </div>
                        <!-- Campo de cargos -->
                        <div class="col-md-5 mb-2">
                            <div class="form-floating">
                                <select name="idcargo" id="idcargo" class="form-select" required>
                                    <option value="">Seleccione un área primero</option>
                                    <?php foreach ($cargos as $c): ?>
                                        <option value="<?= $c['idcargo'] ?>" <?= (isset($usuario['idcargo']) && $usuario['idcargo'] == $c['idcargo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['cargo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="cargo">Cargo</label>
                            </div>
                        </div>
                        <!-- Fecha de inicio -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="fechainicio" id="fechainicio" class="form-control"
                                    value="<?= htmlspecialchars($usuario['fechainicio'] ?? '') ?>" required>
                                <label for="fechainicio">Fecha inicio</label>
                            </div>
                        </div>
                        <!-- LOCAL -->

                        <div class="col-md-12 mb-2">
                            <div class="form-floating">
                                <select name="idlocal" id="idlocal" class="form-select">
                                    <option value="">Sin asignar</option>
                                    <?php foreach ($locales as $l): ?>
                                        <option value="<?= (int) $l['idlocal'] ?>" <?= (isset($usuario['idlocal']) && (int) $usuario['idlocal'] === (int) $l['idlocal']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($l['local']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idlocal">Local</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-footer text-end">
                    <button type="reset" id="btn-cancelar-registro"
                        class="btn btn-sm btn-outline-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Registrar</button>
                </div>
            </div>

        </form>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const areaSel = document.getElementById('idarea');
        const cargoSel = document.getElementById('idcargo');

        const currentCargo = <?= isset($usuario['idcargo']) ? (int) $usuario['idcargo'] : 0 ?>;

        if (!areaSel || !cargoSel) return;

        areaSel.addEventListener('change', async () => {
            const idArea = areaSel.value;

            if (!idArea) {
                cargoSel.innerHTML = '<option value="">Seleccione un área primero</option>';
                cargoSel.disabled = true;
                return;
            }

            cargoSel.disabled = true;
            cargoSel.innerHTML = '<option>Cargando cargos...</option>';

            try {
                const res = await fetch('/api/usuarios/cargos?idarea=' + encodeURIComponent(idArea));
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                cargoSel.innerHTML = '<option value="">Seleccione</option>';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.idcargo;
                    opt.textContent = c.cargo;
                    cargoSel.appendChild(opt);
                });

                if (currentCargo) {
                    const exists = Array.from(cargoSel.options).some(o => parseInt(o.value) === currentCargo);
                    if (exists) cargoSel.value = currentCargo;
                    else cargoSel.value = '';
                }

                cargoSel.disabled = false;
            } catch (err) {
                console.error('Error al cargar cargos:', err);
                cargoSel.innerHTML = '<option value="">Error al cargar cargos</option>';
                cargoSel.disabled = true;
            }
        });

        (function initOnLoad() {
            const initialArea = areaSel.value;
            if (initialArea) {
                // disparar cambio para poblar cargos
                const evt = new Event('change', { bubbles: true });
                areaSel.dispatchEvent(evt);
            }
        })();
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>