<!doctype html>
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

    <section class="ftco-section pt-5">
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
                            <button type="submit"
                                class="mt-1 form-control btn yonda text-light rounded submit px-3">Crear cuenta</button>
                            <a href="/login"
                                class="form-control mt-2 btn btn-outline-secondary rounded text-center px-3 d-inline-block">Volver
                                al login</a>
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

                // Actualiza los campos de selección
                document.getElementById('idcontrato').value = id;
                document.getElementById('nombreSel').value = (nombres + ' ' + apellidos).trim();
                document.getElementById('areaSel').value = area;
                document.getElementById('cargoSel').value = cargo;

                // Rellenar hidden inputs para enviar al servidor
                document.getElementById('nombresSel').value = nombres;
                document.getElementById('apellidosSel').value = apellidos;

                // Limpiar y sugerir usernick
                const base = (nombres && apellidos) ? (nombres + '.' + apellidos) : (nombres || apellidos || '');
                let s = base.toLowerCase().replace(/\s+/g, '.').replace(/[^a-z0-9\.\-]/g, '');
                const inputUser = document.getElementById('usernick');

                // Limpia el campo antes de asignar un nuevo valor
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
            // Verificación de que las contraseñas coinciden y longitud
            const p1 = this.password1.value;
            const p2 = this.password2.value;
            if (p1.length < 8) { e.preventDefault(); alert('Contraseña mínima 8 caracteres.'); return; }
            if (p1 !== p2) { e.preventDefault(); alert('Las contraseñas no coinciden.'); return; }
        });
    </script>
</body>

</html>