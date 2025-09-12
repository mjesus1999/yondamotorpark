<?php include __DIR__ . '/../layout/header.php'; ?>



<style>
    .yonda {
        background-color: #FF5F00;
    }

    /* cursor mano para filas clicables */
    .contracts-table tbody tr[data-id] {
        cursor: pointer;
    }
</style>

<div class="container-fluid">

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex">

                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Registrar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cuentas</li>
                </ol>

            </div>
            <!-- <div class="col-md-6 text-end">
                <a href="/usuarios" class="">[ Mostrar lista ]</a>
            </div> -->
        </div>
    </div>

    <div class="row">

        <!-- Tabla contratos disponibles -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">Lista de Contratos</div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda contracts-table" id="tabla-contratos">
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 45%;">
                            <col style="width: 25%;">
                            <col style="width: 20%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres y Apellidos</th>
                                <th>Área</th>
                                <th>Cargo</th>
                            </tr>
                        </thead>
                        <tbody>
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
                        </tbody>
                    </table>
                    <div class="text-end">
                        <span style="font-style: italic;">Seleccione un Contrato</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de contratos disponible -->

        <!-- Formulario de creación de cuenta -->
        <div class="mb-2 col-md-6">
            <form method="POST" action="/createFromContract" id="form-create" autocomplete="OFF">
                <input type="hidden" name="idcontrato" id="idcontrato" value="<?= $old['idcontrato'] ?? '' ?>">

                <!-- FORMULARIO DE SESION -->
                <div class="card mb-4">

                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-center justify-content-start">
                                <strong id="marca-activa">Formulario de Crear sesión</strong>
                            </div>
                        </div>
                    </div> <!-- ./card-header -->

                    <div class="card-body">
                        <!-- CAMPOS -->
                        <div class="row g-2">

                            <!-- NOMBRE Y APELLIDOS -->
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input id="nombreSel" class="form-control" placeholder="Nombres y Apellidos"
                                        readonly>
                                    <label for="form-label">Nombres y Apellidos <span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>

                            <!-- ÁREA ASIGNADA -->
                            <div class="col-md-4 mb-2">
                                <div class="form-floating">
                                    <input id="areaSel" class="form-control" placeholder="Área Asignada" readonly>
                                    <label for="form-label">Área Asignada <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <!-- CARGO -->
                            <div class="col-md-3 mb-2">
                                <div class="form-floating">
                                    <input id="cargoSel" class="form-control" placeholder="Cargo Asignado" readonly>
                                    <label for="form-label">Cargo <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <!-- Inputs para tomar y registrar -->
                            <input type="hidden" name="nombres" id="nombresSel" value="<?= $old['nombres'] ?? '' ?>">
                            <input type="hidden" name="apellidos" id="apellidosSel"
                                value="<?= $old['apellidos'] ?? '' ?>">

                            <hr>

                            <!-- NOMBRE DE USUARIO -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input name="usernick" id="usernick" class="form-control"
                                        placeholder="Nombre de Usuario" value="<?= $old['usernick'] ?? '' ?>" required>
                                    <label for="form-label">Nombres de Usuario <span
                                            class="text-danger">*</span></label>
                                    <div class="form-text small text-muted">Sugerencia: pulsa el nombre en la lista para
                                        autocompletar.</div>
                                </div>
                            </div>

                            <!-- PRIMERA CONTRASEÑA -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input name="password1" type="password" class="form-control" minlength="8"
                                        placeholder="Contraseña" required>
                                    <label for="form-label">Contraseña <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <!-- SEGUNDA CONTRASEÑA -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input name="password2" type="password" class="form-control" minlength="8"
                                        placeholder="Confirmar Contraseña" required>
                                    <label for="form-label">Comfirmar contraseña <span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>

                            <?php
                            $start = getenv('STARTIME') ?: '07:30';
                            $end = getenv('ENDTIME') ?: '19:30';
                            ?>
                            <!-- RESTRICCIÓN HORARIA -->
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Restricción horaria - Lunes a Sábado
                                    <strong><?= htmlspecialchars($start) ?></strong> -
                                    <strong><?= htmlspecialchars($end) ?></strong> <span class="text-danger">*</span>
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="restriccionhoraria" id="rest_si"
                                        value="S" required <?= (isset($old['restriccionhoraria']) && $old['restriccionhoraria'] === 'N') ? '' : 'checked' ?>>
                                    <label class="form-check-label" for="rest_si">Sí</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="restriccionhoraria" id="rest_no"
                                        value="N" required <?= (isset($old['restriccionhoraria']) && $old['restriccionhoraria'] === 'N') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="rest_no">No</label>
                                </div>
                                <div class="form-text small text-muted">Selecciona si este usuario tendrá restricción
                                    horaria.</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-sm yonda text-light rounded">Crear cuenta</button>
                        <button type="reset" class="btn btn-sm btn-outline-secondary rounded">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.contracts-table tbody tr[data-id]').forEach(function (row) {
            row.setAttribute('role', 'button');
            row.tabIndex = 0; // permite foco con TAB

            //llenar los campos 
            row.addEventListener('click', function () {
                // remover seleccion previa
                document.querySelectorAll('.contracts-table tbody tr.table-active').forEach(r => r.classList.remove('table-active'));
                // marcar esta fila
                this.classList.add('table-active');

                const id = this.dataset.id || '';
                const nombres = this.dataset.nombres || '';
                const apellidos = this.dataset.apellidos || '';
                const area = this.dataset.area || '';
                const cargo = this.dataset.cargo || '';

                //Actualizar los campos de seleccion
                document.getElementById('idcontrato').value = id;
                document.getElementById('nombreSel').value = (nombres + ' ' + apellidos).trim();
                document.getElementById('areaSel').value = area;
                document.getElementById('cargoSel').value = cargo;

                //Rellenar hidden inputs para enviarlos al servidor
                document.getElementById('nombresSel').value = nombres;
                document.getElementById('apellidosSel').value = apellidos;

                // Generar sugerencia usernick 
                const nombresStr = (nombres || '').trim();
                const apellidosStr = (apellidos || '').trim();

                let firstName = '';
                let apellidosIniciales = '';

                if (nombresStr) {
                    firstName = nombresStr.split(/\s+/u)[0];
                }
                if (apellidosStr) {
                    const partes = apellidosStr.split(/\s+/u).filter(Boolean);
                    const primerasDos = partes.slice(0, 2);
                    apellidosIniciales = primerasDos.map(p => p.charAt(0)).join('');
                }

                const base = firstName
                    ? (firstName + (apellidosIniciales ? apellidosIniciales : ''))
                    : (apellidosIniciales || apellidosStr || '');

                let sumpr = base
                    .normalize('NFKC')
                    .toLowerCase()
                    .replace(/\s+/g, '.')
                    .replace(/[^\p{L}0-9.\-]/gu, '')
                    .replace(/\.{2,}/g, '.')
                    .replace(/^\.|\.$/g, '');

                const inputUser = document.getElementById('usernick');
                inputUser.value = sumpr;
                inputUser.focus();
            });

            // permitir seleccionar con teclado (Enter / Space)
            row.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        const formCreate = document.getElementById('form-create');
        if (!formCreate) return;

        formCreate.addEventListener('submit', async (e) => {
            e.preventDefault();

            // validations client-side
            const idContrato = document.getElementById('idcontrato').value || '';
            if (!idContrato) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un contrato',
                    text: 'Debes seleccionar primero el contrato de la lista.',
                    confirmButtonText: 'OK',
                    width: 420
                });
                return;
            }

            const p1 = formCreate.password1.value || '';
            const p2 = formCreate.password2.value || '';

            if (p1.length < 8) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Contraseña inválida',
                    text: 'La contraseña debe tener al menos 8 caracteres.',
                    confirmButtonText: 'OK',
                    width: 420
                });
                return;
            }

            if (p1 !== p2) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Contraseñas no coinciden',
                    text: 'Por favor verifica que ambas contraseñas coincidan.',
                    confirmButtonText: 'OK',
                    width: 420
                });
                return;
            }

            // también permitir que el navegador muestre errores 'required' si existen
            if (!formCreate.checkValidity()) {
                formCreate.reportValidity();
                return;
            }

            const { isConfirmed } = await Swal.fire({
                title: '¿Crear cuenta?',
                text: '¿Deseas crear la cuenta para este contrato?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            });

            if (!isConfirmed) return;

            // Evitar doble envio: deshabilitar boton mostrar spinner pequeño
            const submitBtn = formCreate.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.origText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Creando...';
            }

            formCreate.submit();
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>