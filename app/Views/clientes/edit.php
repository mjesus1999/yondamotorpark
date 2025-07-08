<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Incluir librerías y JavaScript del mapa -->
<?php include __DIR__ . '/../components/mapa-includes.php'; ?>

<!-- Incluir modal del mapa -->
<?php include __DIR__ . '/../components/mapa-modal.php'; ?>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>


<div class="container-fluid">

    <div class="container-fluid">

        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Clientes (Normales)</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <a class="btn btn-sm btn-outline-primary" href="/clientes" class="">Mostrar
                        lista</a>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white">
                    <h5 class="mb-0">Actualizar Cliente (Normales)</h5>
                </div>
                
                <div class="card-body">

                    <?php if (isset($personaCliente)): ?>
                        <form action="/personaCliente/update/<?= htmlspecialchars($personaCliente['idpersona']) ?>" autocomplete="off" id="formulario-cliente-personas" method="POST">
                            <input type="hidden" name="idpersona" id="idpersona" value="<?= htmlspecialchars($personaCliente['idpersona']) ?>">
                            <div class="modal-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos"
                                                required value="<?= htmlspecialchars($personaCliente['apellidos']) ?>">
                                            <label for="apellidos">Apellidos</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required value="<?= htmlspecialchars($personaCliente['nombres']) ?>">
                                            <label for="nombres">Nombres</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select name="estadocivil" id="estadocivil" class="form-select" required>
                                                <?php
                                                $estados = [
                                                    'SOL' => 'Solter@',
                                                    'CAS' => 'Casad@',
                                                    'VDO' => 'Viud@',
                                                    'DVC' => 'Divorciad@',
                                                    'CNV' => 'Conviviente'
                                                ];
                                                foreach ($estados as $value => $label) {
                                                    $selected = ($value == $personaCliente['estadocivil']) ? 'selected' : '';
                                                    echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                                                }
                                                ?>
                                            </select>
                                            <label for="estadocivil">Estado civil</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Correo" value="<?= htmlspecialchars($personaCliente['email'] ?? 'No asignado') ?>">
                                            <label for="email">Correo</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="departamento" id="departamento" class="form-select" required>
                                                <option value="">Seleccione</option>
                                            </select>
                                            <label for="departamento">Departamento</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="provincia" id="provincia" class="form-select" required>
                                                <option value="">Seleccione</option>
                                            </select>
                                            <label for="provincia">Provincia</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="iddistrito" id="distrito" class="form-select" required>
                                                <option value="">Seleccione</option>
                                            </select>
                                            <label for="distrito">Distrito</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="<?= htmlspecialchars($personaCliente['direccion'] ?? '') ?>">
                                            <label for="direccion">Dirección</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="telprimario" name="telprimario" placeholder="Teléfono"
                                                maxlength="9" pattern="[0-9]+" required value="<?= htmlspecialchars($personaCliente['telprimario'] ?? '') ?>">
                                            <label for="telprimario">Teléfono</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="latitud" name="latitud" placeholder="Latitud" value="<?= htmlspecialchars($personaCliente['latitud'] ?? '') ?>">
                                            <label for="latitud">Latitud</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="longitud" name="longitud" placeholder="Longitud" value="<?= htmlspecialchars($personaCliente['longitud'] ?? '') ?>">
                                            <label for="longitud">Longitud</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-sm btn-success" id="btn-mapa">Ver mapa</button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <!-- <button type="button" class="btn btn-sm btn-outline-secondary m-2"
                                    data-bs-dismiss="modal">Cancelar</button> -->
                                <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Persona cliente no encontrada.
                        </div>
                        <a href="/clientes" class="btn btn-secondary">Volver a la lista</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const departamentosSelect = document.querySelector('#departamento');
            const provinciasSelect = document.querySelector('#provincia');
            const distritosSelect = document.querySelector('#distrito');
            const formularioClientePersonas = document.getElementById('formulario-cliente-personas');

            const initialDepartamentoId = "<?= htmlspecialchars($personaCliente['iddepartamento'] ?? '') ?>";
            const initialProvinciaId = "<?= htmlspecialchars($personaCliente['idprovincia'] ?? '') ?>";
            const initialDistritoId = "<?= htmlspecialchars($personaCliente['iddistrito'] ?? '') ?>";
            const initialEstadoCivil = "<?= htmlspecialchars($personaCliente['estadocivil'] ?? '') ?>";


            async function getAllDepartamentos(selectedId = '') {
                try {
                    const response = await fetch(`/api/ubigeo/departamentos`, {
                        method: 'GET'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    departamentosSelect.innerHTML = `<option value=''>Seleccione</option>`;
                    if (data.length > 0) {
                        data.forEach(element => {
                            const selectedAttr = (element.iddepartamento == selectedId) ? 'selected' : '';
                            departamentosSelect.innerHTML += `
                            <option value='${element.iddepartamento}' ${selectedAttr}>${element.departamento}</option>
                        `;
                        });
                    }
                    if (selectedId) {
                        getProvinciasByDepartamento(selectedId, initialProvinciaId);
                    }
                } catch (e) {
                    console.error("Error al obtener departamentos:", e);
                }
            }

            async function getProvinciasByDepartamento(iddepartamento, selectedId = '') {
                provinciasSelect.innerHTML = `<option value=''>Seleccione</option>`;
                distritosSelect.innerHTML = `<option value=''>Seleccione</option>`;
                if (!iddepartamento) return;

                try {
                    const response = await fetch(`/api/ubigeo/provincias/${iddepartamento}`, {
                        method: 'GET'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.length > 0) {
                        data.forEach(element => {
                            const selectedAttr = (element.idprovincia == selectedId) ? 'selected' : '';
                            provinciasSelect.innerHTML += `
                            <option value='${element.idprovincia}' ${selectedAttr}>${element.provincia}</option>
                        `;
                        });
                    }
                    if (selectedId) {
                        getDistritosByProvincia(selectedId, initialDistritoId);
                    }
                } catch (e) {
                    console.error("Error al obtener provincias:", e);
                }
            }

            async function getDistritosByProvincia(idprovincia, selectedId = '') {
                distritosSelect.innerHTML = `<option value=''>Seleccione</option>`;
                if (!idprovincia) return;

                try {
                    const response = await fetch(`/api/ubigeo/distritos/${idprovincia}`, {
                        method: 'GET'
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.length > 0) {
                        data.forEach(element => {
                            const selectedAttr = (element.iddistrito == selectedId) ? 'selected' : '';
                            distritosSelect.innerHTML += `
                            <option value='${element.iddistrito}' ${selectedAttr}>${element.distrito}</option>
                        `;
                        });
                    }
                } catch (e) {
                    console.error("Error al obtener distritos:", e);
                }
            }

            departamentosSelect.addEventListener('change', (event) => {
                const iddepartamento = event.target.value;
                getProvinciasByDepartamento(iddepartamento);
            });

            provinciasSelect.addEventListener('change', (event) => {
                const idprovincia = event.target.value;
                getDistritosByProvincia(idprovincia);
            });

            // Cargar datos al inicio de la página y pre-seleccionar
            getAllDepartamentos(initialDepartamentoId);
            document.getElementById('estadocivil').value = initialEstadoCivil;


            formularioClientePersonas.addEventListener('submit', (event) => {
                event.preventDefault();
                if (confirm("¿Desea actualizar este cliente?")) {

                    event.target.submit();

                }
            });
        });
    </script>

    <?php include __DIR__ . '/../layout/footer.php'; ?>