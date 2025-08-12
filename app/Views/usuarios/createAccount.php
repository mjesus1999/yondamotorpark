<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<style>
    .yonda {
        background-color: #FF5F00;
    }
</style>

<div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex">
        <nav
          style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
          aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Registrar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cuentas</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

<section class="ftco-section pt-2">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="w-100 text-center">
                <h3 class="mb-4">Crear Cuenta</h3>
            </div>
        </div>
        <div class="row">

            <!-- Tabla de contratos -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">Lista de Contratos</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-hover table-hover-yonda contracts-table"
                            id="tabla-contratos">
                            <colgroup>
                                <col style="width: 5%;">
                                <col style="width: 45%;">
                                <col style="width: 25%;">
                                <col style="width: 20%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Área</th>
                                    <th>Cargo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($contracts)): ?>
                                    <?php foreach ($contracts as $c): ?>
                                        <tr data-id="<?= $c['idcontratolaboral'] ?>"
                                            data-nombres="<?= htmlspecialchars($c['nombres']) ?>"
                                            data-apellidos="<?= htmlspecialchars($c['apellidos']) ?>"
                                            data-area="<?= htmlspecialchars($c['area']) ?>"
                                            data-cargo="<?= htmlspecialchars($c['cargo']) ?>">
                                            <td><?= htmlspecialchars($c['idcontratolaboral']) ?></td>
                                            <td><?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></td>
                                            <td><?= htmlspecialchars($c['area']) ?></td>
                                            <td><?= htmlspecialchars($c['cargo']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center small text-muted py-3">No hay contratos sin
                                            cuenta.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <span style="font-style: italic;">Seleccione un contrato</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de creación de cuenta -->
            <div class="col-md-6 mt-2">
                <h6>Formulario de sesión</h6>
                <form method="POST" action="/createFromContract" id="form-create" autocomplete="off">
                    <input type="hidden" name="idcontrato" id="idcontrato" value="<?= $old['idcontrato'] ?? '' ?>">

                    <div class="mb-2">
                        <label class="form-label small">Nombres</label>
                        <input id="nombreSel" class="form-control" placeholder="Nombres de la persona" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Área Asignada</label>
                        <input id="areaSel" class="form-control" placeholder="Área Asignada" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Cargo</label>
                        <input id="cargoSel" class="form-control" placeholder="Cargo Asignado" readonly>
                    </div>

                    <!-- Hidden inputs que SÍ se enviarán al servidor -->
                    <input type="hidden" name="nombres" id="nombresSel" value="<?= $old['nombres'] ?? '' ?>">
                    <input type="hidden" name="apellidos" id="apellidosSel" value="<?= $old['apellidos'] ?? '' ?>">

                    <hr>

                    <div class="mb-2">
                        <label class="form-label small">Nombre de Usuario</label>
                        <input name="usernick" id="usernick" class="form-control" placeholder="Nombre de Usuario"
                            value="<?= $old['usernick'] ?? '' ?>" required>
                        <div class="form-text small text-muted">Sugerencia: pulsa el nombre en la lista para
                            autocompletar.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Contraseña</label>
                        <input name="password1" type="password" class="form-control" minlength="8"
                            placeholder="Contraseña" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Confirmar contraseña</label>
                        <input name="password2" type="password" class="form-control" minlength="8"
                            placeholder="Confirmar Contraseña" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="mt-1 form-control btn yonda text-light rounded submit px-3">Crear
                            cuenta</button>
                        <button type="reset"
                            class="mt-1 form-control btn btn-outline-secondary rounded submit px-3">Cancelar</button>

                    </div>
                </form>
            </div>
        </div> <!-- ./row -->
    </div>
</section>

<script>
    document.querySelectorAll('.contracts-table tbody tr[data-id]').forEach(function (row) {
        row.addEventListener('click', function () {
            const id = this.dataset.id || '';
            const nombres = this.dataset.nombres || '';
            const apellidos = this.dataset.apellidos || '';
            const area = this.dataset.area || '';
            const cargo = this.dataset.cargo || '';

            //Actualizar los campos de selección
            document.getElementById('idcontrato').value = id;
            document.getElementById('nombreSel').value = (nombres + ' ' + apellidos).trim();
            document.getElementById('areaSel').value = area;
            document.getElementById('cargoSel').value = cargo;

            //Rellenar hidden inputs para enviarlos al servidor
            document.getElementById('nombresSel').value = nombres;
            document.getElementById('apellidosSel').value = apellidos;

            //Limpiar y sugerir usernick
            const base = (nombres && apellidos) ? (nombres + '.' + apellidos) : (nombres || apellidos || '');
            let s = base.toLowerCase().replace(/\s+/g, '.').replace(/[^a-z0-9\.\-]/g, '');
            const inputUser = document.getElementById('usernick');

            //Limpiar los campos cantes de asignar un nuevo valor
            inputUser.value = s;
            inputUser.focus();
        });
    });

    document.getElementById('form-create').addEventListener('submit', function (e) {
        if (!document.getElementById('idcontrato').value) {
            e.preventDefault();
            alert('Selecciona primero el contrato de la lista.');
            return;
        }
        //verificación de que las contraseñas coinciden y longitud
        const p1 = this.password1.value;
        const p2 = this.password2.value;
        if (p1.length < 8) { e.preventDefault(); alert('Contraseña mínima 8 caracteres.'); return; }
        if (p1 !== p2) { e.preventDefault(); alert('Las contraseñas no coinciden.'); return; }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>