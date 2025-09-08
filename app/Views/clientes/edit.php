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

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    .form-control,
    .form-select {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .card {
        border-radius: 0.5rem;
    }

    .btn {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #0d6efd;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: #0a58ca;
    }

    .alert {
        border-radius: 0.5rem;
    }

    h5,
    h6 {
        font-weight: 600;
    }

    #btn-mapa {
        height: calc(3.5rem + 2px);
    }
</style>
<div class="container-fluid px-4">
    <!-- Encabezado con migas de pan y botón -->
    <div class="alert alert-info mt-3 rounded-3 shadow-sm py-2" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="bi bi-people me-1"></i>Clientes (Normales)</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-pencil-square me-1"></i>Actualizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/clientes" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta principal del formulario -->
    <div class="card border-0 shadow-lg mt-3">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Actualizar Cliente (Normal)</h5>
        </div>

        <div class="card-body p-4">
            <?php if (isset($personaCliente)): ?>
                <form action="/personaCliente/update/<?= htmlspecialchars($personaCliente['idpersona']) ?>" autocomplete="off" id="formulario-cliente-personas" method="POST">
                    <input type="hidden" name="idpersona" id="idpersona" value="<?= htmlspecialchars($personaCliente['idpersona']) ?>">

                    <!-- Sección 1: Información personal -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-person-vcard me-2"></i>Información Personal</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="apellidos" name="apellidos"
                                        placeholder="Apellidos" required value="<?= htmlspecialchars($personaCliente['apellidos']) ?>">
                                    <label for="apellidos"><i class="bi bi-person me-1"></i>Apellidos <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nombres" name="nombres"
                                        placeholder="Nombres" required value="<?= htmlspecialchars($personaCliente['nombres']) ?>">
                                    <label for="nombres"><i class="bi bi-person me-1"></i>Nombres <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Datos personales -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-info-circle me-2"></i>Datos Personales</h6>
                        <div class="row g-3">

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechanac" name="fechanac"
                                    placeholder="Fecha de nacimiento" value="<?= htmlspecialchars($personaCliente['fechanac'] ?? 'No asignado') ?>">
                                <label for="fechanac"><i class="bi bi-calendar me-1"></i>Fecha de nacimiento</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="estadocivil" id="estadocivil" class="form-select">
                                    <?php
                                        // Opción por defecto si no hay estado civil asignado
                                        $estadoCivilActual = $personaCliente['estadocivil'] ?? '';
                                        if (empty($estadoCivilActual)) {
                                            echo "<option value=\"\" selected>Seleccione</option>";
                                        }
                                        
                                        $estados = [
                                            'SOL' => 'Solter@',
                                            'CAS' => 'Casad@',
                                            'VDO' => 'Viud@',
                                            'DVC' => 'Divorciad@',
                                            'CNV' => 'Conviviente'
                                        ];
                                        foreach ($estados as $value => $label) {
                                            $selected = ($value == $estadoCivilActual) ? 'selected' : '';
                                            echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                                        }
                                        ?>
                                    </select>
                                    <label for="estadocivil"><i class="bi bi-heart me-1"></i>Estado civil</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Correo" value="<?= htmlspecialchars($personaCliente['email'] ?? '') ?>">
                                    <label for="email"><i class="bi bi-envelope me-1"></i>Correo</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Ubicación -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-geo-alt me-2"></i>Ubicación</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="departamento" id="departamento" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="departamento"><i class="bi bi-map me-1"></i>Departamento <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="provincia" id="provincia" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="provincia"><i class="bi bi-map me-1"></i>Provincia <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="iddistrito" id="distrito" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="distrito"><i class="bi bi-map me-1"></i>Distrito <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="direccion" name="direccion"
                                        placeholder="Dirección" value="<?= htmlspecialchars($personaCliente['direccion'] ?? '') ?>">
                                    <label for="direccion"><i class="bi bi-signpost me-1"></i>Dirección</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="telprimario" name="telprimario"
                                        placeholder="Teléfono" maxlength="9" pattern="[0-9]+" required
                                        value="<?= htmlspecialchars($personaCliente['telprimario'] ?? '') ?>">
                                    <label for="telprimario"><i class="bi bi-phone me-1"></i>Teléfono <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 4: Coordenadas -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3 border-bottom pb-2"><i class="bi bi-geo me-2"></i>Coordenadas</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="latitud" name="latitud"
                                        placeholder="Latitud" value="<?= htmlspecialchars($personaCliente['latitud'] ?? '') ?>">
                                    <label for="latitud"><i class="bi bi-globe me-1"></i>Latitud</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="longitud" name="longitud"
                                        placeholder="Longitud" value="<?= htmlspecialchars($personaCliente['longitud'] ?? '') ?>">
                                    <label for="longitud"><i class="bi bi-globe me-1"></i>Longitud</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-success btn-sm py-2" id="btn-mapa" style="height: calc(2.5rem + 2px);">
                                    <i class="bi bi-map me-1"></i> Mapa
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
                        <a href="/clientes" class="btn btn-outline-secondary">
                             Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                             Actualizar
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Persona cliente no encontrada.
                </div>
                <a href="/clientes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a la lista
                </a>
            <?php endif; ?>
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


        formularioClientePersonas.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (await ask("¿Desea actualizar este cliente?", "Actualizar cliente")) {

                event.target.submit();

            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>