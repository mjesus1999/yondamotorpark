<?php
use function App\Helpers\persistirDatosFormulario;
require_once __DIR__ . '/../../Helpers/functions.php'; ?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../components/mapa-includes.php'; ?>
<?php include __DIR__ . '/../components/mapa-modal.php'; ?>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>

  <?php persistirDatosFormulario($data) ?>
<?php endif; ?>
<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Clientes (Normales)</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/clientes/" class="">Mostrar
                    lista</a>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Registrar Cliente (Normales)</h5>
            </div>
            <div class="card-body">
                
                <form action="/storepersonclient/store" id="form-registro-cliente-persona" autocomplete="off" method="POST">
                    <input type="hidden" name="idcliente" id="idcliente">
                    <div class="row g-3 mt-1 mb-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="tipodocumento" id="tipodocumento" class="form-select" required>
                                    <option value="DNI" selected>DNI</option>
                                    <option value="CEX">Carnet de extranjeria</option>
                                    <option value="PAS">Pasaporte</option>
                                </select>
                                <label for="tipodocumento">Tipo documento</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nrodoc" id="ndocumento" class="form-control"
                                    placeholder="Ingrese el N° de documento" maxlength="12" required>
                                <label for="ndocumento">N° documento</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" id="apellidos" name="apellidos" class="form-control"
                                    placeholder="Ingrese los apellidos" required>
                                <label for="apellidos">Apellidos</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nombres" id="nombres" class="form-control"
                                    placeholder="Ingrese los nombres" required>
                                <label for="nombres">Nombres</label>
                            </div>
                        </div>
                    </div>
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
                                <select name="distrito" id="distrito" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="distrito">Distrito</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                <label for="genero">Género</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="estadocivil" id="estadocivil" class="form-select" required>
                                    <option value="SOL">Solter@</option>
                                    <option value="CAS">Casad@</option>
                                    <option value="VDO">Viud@</option>
                                    <option value="DVC">Divorciad@</option>
                                    <option value="CNV">Conviviente</option>
                                </select>
                                <label for="estadocivil">Estado civil</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" name="fechanac" id="fechanacimiento" class="form-control"
                                    placeholder="Fecha nacimiento" required>
                                <label for="fechanacimiento">Fecha nacimiento</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="exmaple@gmail.com">
                                <label for="email">Correo</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telprimario" id="telprimario" class="form-control" maxlength="9"
                                    pattern="[0-9]+" placeholder="N° telefóno" required>
                                <label for="telprimario">Teléfono</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telalternativo" id="telalternativo" class="form-control"
                                    maxlength="9" pattern="[0-9]+" placeholder="N° telefóno">
                                <label for="telalternativo">Teléfono 2 (Opcional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="direccion" id="direccion" class="form-control"
                                    placeholder="Ingrese una dirección">
                                <label for="direccion">Direccion(Opcional)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="referencia" id="referencia" class="form-control"
                                    placeholder="Ingrese una dirección">
                                <label for="referencia">Referencia(Opcional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="latitud" id="latitud" class="form-control"
                                    placeholder="Latitud">
                                <label for="latitud">Latitud</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="longitud" id="longitud" class="form-control" maxlength="40"
                                    placeholder="Longitud">
                                <label for="longitud">Longitud</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-sm btn-success" id="btn-mapa">Ver mapa</button>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="reset" class="btn btn-sm btn-outline-secondary"
                            id="btn-cancelar">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-registrar">Guardar Cliente</button>
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
        const formRegistroClientePersona = document.getElementById('form-registro-cliente-persona');

        async function getAllDepartamentos() {
            try {
                const response = await fetch(`/api/ubigeo/departamentos`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                departamentosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
                if (data.length > 0) {
                    data.forEach(element => {
                        departamentosSelect.innerHTML += `
                            <option value='${element.iddepartamento}'>${element.departamento}</option>
                        `;
                    });
                }
            } catch (e) {
                console.error("Error al obtener departamentos:", e);
            }
        }

        async function getProvinciasByDepartamento(iddepartamento) {
            provinciasSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
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
                        provinciasSelect.innerHTML += `
                            <option value='${element.idprovincia}'>${element.provincia}</option>
                        `;
                    });
                }
            } catch (e) {
                console.error("Error al obtener provincias:", e);
            }
        }

        async function getDistritosByProvincia(idprovincia) {
            distritosSelect.innerHTML = `<option value='' selected>Seleccione</option>`;
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
                        distritosSelect.innerHTML += `
                            <option value='${element.iddistrito}'>${element.distrito}</option>
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

        getAllDepartamentos();

        formRegistroClientePersona.addEventListener('submit', (event) => {
            event.preventDefault(); 

            if (confirm("¿Desea registrar este nuevo cliente?")) {
                formRegistroClientePersona.submit(); 
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>