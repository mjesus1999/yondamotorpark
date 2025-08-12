<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Crear cuenta — Motorpark</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
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
                                <div class="col">Contratos</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-hover contracts-table" id="tabla-contratos">
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
                        </div>
                    </div>
                </div>

                <!-- Formulario de creación de cuenta -->
                <div class="col-md-6">
                    <!-- <h6>Crear cuenta</h6> -->
                    <form method="POST" action="/createFromContract" id="form-create" autocomplete="off">
                        <input type="hidden" name="idcontrato" id="idcontrato" value="<?= $old['idcontrato'] ?? '' ?>">

                        <div class="mb-2">
                            <label class="form-label small">Nombre</label>
                            <input id="nombreSel" class="form-control" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Área</label>
                            <input id="areaSel" class="form-control" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Cargo</label>
                            <input id="cargoSel" class="form-control" readonly>
                        </div>

                        <!-- Hidden inputs que SÍ se enviarán al servidor -->
                        <input type="hidden" name="nombres" id="nombresSel" value="<?= $old['nombres'] ?? '' ?>">
                        <input type="hidden" name="apellidos" id="apellidosSel" value="<?= $old['apellidos'] ?? '' ?>">

                        <hr>

                        <div class="mb-2">
                            <label class="form-label small">Usuario (usernick)</label>
                            <input name="usernick" id="usernick" class="form-control"
                                value="<?= $old['usernick'] ?? '' ?>" required>
                            <div class="form-text small text-muted">Sugerencia: pulsa el nombre en la lista para
                                autocompletar.</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small">Contraseña</label>
                            <input name="password1" type="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Confirmar contraseña</label>
                            <input name="password2" type="password" class="form-control" minlength="8" required>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn yonda text-light">Crear cuenta</button>
                            <a href="/login" class="btn btn-outline-secondary mt-2">Volver al login</a>
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

                document.getElementById('idcontrato').value = id;
                document.getElementById('nombreSel').value = (nombres + ' ' + apellidos).trim();
                document.getElementById('areaSel').value = area;
                document.getElementById('cargoSel').value = cargo;

                // rellenar hidden inputs para enviar al servidor
                document.getElementById('nombresSel').value = nombres;
                document.getElementById('apellidosSel').value = apellidos;

                // sugerir usernick
                const base = (nombres && apellidos) ? (nombres + '.' + apellidos) : (nombres || apellidos || '');
                let s = base.toLowerCase().replace(/\s+/g, '.').replace(/[^a-z0-9\.\-]/g, '');
                const inputUser = document.getElementById('usernick');
                if (!inputUser.value) inputUser.value = s;
                inputUser.focus();
            });
        });

        document.getElementById('form-create').addEventListener('submit', function (e) {
            if (!document.getElementById('idcontrato').value) {
                e.preventDefault();
                alert('Selecciona primero el contrato de la lista.');
                return;
            }
            //verificacion de que las contraseñas coinciden y longitud
            const p1 = this.password1.value;
            const p2 = this.password2.value;
            if (p1.length < 8) { e.preventDefault(); alert('Contraseña mínima 8 caracteres.'); return; }
            if (p1 !== p2) { e.preventDefault(); alert('Las contraseñas no coinciden.'); return; }
        });
    </script>
</body>

</html>