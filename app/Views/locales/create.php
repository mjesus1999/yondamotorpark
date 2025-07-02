<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>


<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Locales</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="locales/" class="">Mostrar
                    lista</a>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Registrar Local</h5>
            </div>
            <div class="card-body">
                <form action="/locales/store" id="form-registro-local" autocomplete="off" method="POST">
                    
                    <!-- Ubicación -->
                    <div class="row g-3">
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
                    </div>

                    <!-- Datos de tienda -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idmotorpark" id="idmotorpark" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="idmotorpark">Tienda</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="principal" id="principal" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="S">Sí</option>
                                    <option value="N">No</option>
                                </select>
                                <label for="principal">¿Es principal?</label>
                            </div>
                        </div>
                    </div>


                    <!-- Contacto -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" id="telefono" name="telefono" maxlength="12" pattern="[0-9]+"
                                    class="form-control" placeholder="Teléfono" required>
                                <label for="telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="email" name="correo" id="correo" class="form-control"
                                    placeholder="Correo electrónico" required>
                                <label for="correo">Correo electrónico</label>
                            </div>
                        </div>
                    </div>

                    <!-- Responsable y nombre del local -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="responsable" id="responsable" class="form-control"
                                    maxlength="100" placeholder="Responsable" required>
                                <label for="responsable">Responsable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="tienda" id="tienda" class="form-control" maxlength="40"
                                    placeholder="Nombre del local" required>
                                <label for="tienda">Nombre del Local</label>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="mt-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea name="direccion" id="direccion" rows="3" class="form-control" maxlength="300"
                            placeholder="Dirección" required></textarea>
                    </div>



                    <!-- Botón -->
                    <div class="text-end mt-4">
                        <button type="reset" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary">Guardar Local</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>






<script>
    document.addEventListener("DOMContentLoaded", async () => {

        const departamentosSelect = document.querySelector('#departamento');
        const provinciasSelect = document.querySelector('#provincia');
        const distritosSelect = document.querySelector('#distrito');
        const motorparkSelect = document.querySelector('#idmotorpark'); // Selector para Motorpark

        // Función para cargar departamentos
        async function getAllDepartamentos() {
            try {
                const response = await fetch(`/api/ubigeo/departamentos`, { method: 'GET' });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log("Departamentos recibidos:", data);

                departamentosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;

                if (data.length > 0) {
                    data.forEach(element => {
                        departamentosSelect.innerHTML += `
                            <option value='${element.iddepartamento}'>${element.departamento}</option>
                        `;
                    });
                } else {
                    console.log("No se recibieron departamentos o el array está vacío.");
                }
            } catch (e) {
                console.error("Error al obtener departamentos:", e);
            }
        }

        // Función para cargar provincias
        async function getProvinciasByDepartamento(iddepartamento) {
            provinciasSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            if (!iddepartamento) return;

            try {
                const response = await fetch(`/api/ubigeo/provincias/${iddepartamento}`, { method: 'GET' });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log(`Provincias para departamento ${iddepartamento}:`, data);

                if (data.length > 0) {
                    data.forEach(element => {
                        provinciasSelect.innerHTML += `
                            <option value='${element.idprovincia}'>${element.provincia}</option>
                        `;
                    });
                } else {
                    console.log(`No se recibieron provincias para el departamento ${iddepartamento}.`);
                }
            } catch (e) {
                console.error("Error al obtener provincias:", e);
            }
        }

        // Función para cargar distritos
        async function getDistritosByProvincia(idprovincia) {
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            if (!idprovincia) return;

            try {
                const response = await fetch(`/api/ubigeo/distritos/${idprovincia}`, { method: 'GET' });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log(`Distritos para provincia ${idprovincia}:`, data);

                if (data.length > 0) {
                    data.forEach(element => {
                        distritosSelect.innerHTML += `
                            <option value='${element.iddistrito}'>${element.distrito}</option>
                        `;
                    });
                } else {
                    console.log(`No se recibieron distritos para la provincia ${idprovincia}.`);
                }
            } catch (e) {
                console.error("Error al obtener distritos:", e);
            }
        }

        // NUEVA FUNCIÓN: Para cargar Motorpark
        async function getMotorParkData() {
            try {
                const response = await fetch(`/api/motorpark`, { method: 'GET' });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json(); // La respuesta es un objeto con 'success' y 'motorpark'
                console.log("Motorpark data recibida:", result);

                motorparkSelect.innerHTML = `<option value='' selected>Seleccione</option>`;

                if (result.success && result.motorpark && result.motorpark.length > 0) {
                    result.motorpark.forEach(element => {
                        motorparkSelect.innerHTML += `
                            <option value='${element.idmotorpark}'>${element.nombrecomercial}</option>
                        `;
                    });
                } else {
                    console.log("No se recibieron datos de motorpark o el array está vacío.");
                }
            } catch (e) {
                console.error("Error al obtener datos de motorpark:", e);
            }
        }


        // Event Listeners para los selectores de Ubigeo
        departamentosSelect.addEventListener('change', (event) => {
            const iddepartamento = event.target.value;
            getProvinciasByDepartamento(iddepartamento);
        });

        provinciasSelect.addEventListener('change', (event) => {
            const idprovincia = event.target.value;
            getDistritosByProvincia(idprovincia);
        });


        // Cargar datos al inicio de la página
        getAllDepartamentos();
        getMotorParkData(); // ¡Llamada para cargar Motorpark!


        const form = document.querySelector("#form-registro-local")
        form.addEventListener("submit", (event) => {
            event.preventDefault()

            if (confirm("¿Registramos un nuevo local?")) {
                form.submit()
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>