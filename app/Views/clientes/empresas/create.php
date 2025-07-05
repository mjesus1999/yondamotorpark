<?php

include __DIR__ . '/../../layout/header.php';
include __DIR__ . '/../../components/mapa-includes.php';
include __DIR__ . '/../../components/mapa-modal.php'; 

?>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>
  
  <?php endif; ?>

<?php if (isset($success)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('<?= addslashes($success) ?>', 'SUCCESS', 1000);
            
            setTimeout(function() {
                window.location.href = '/clientes/empresas';
            }, 1500);
        });
    </script>
<?php endif;?>

<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Empresas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/clientes/empresas/" class="">Mostrar
                    lista</a>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Registrar Cliente (Empresa)</h5>
            </div>
            <div class="card-body">
                <form action="/clientes/empresas/storeempresaclient" id="form-registro-cliente-empresa" autocomplete="off" method="POST">

                    <div class="row g-3 mt-1 mb-3">


                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="ruc" id="ruc" class="form-control"
                                    placeholder="Ingrese el N° de RUC" maxlength="11" required>
                                <label for="ruc">N° RUC</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" id="razonsocial" name="razonsocial" class="form-control"
                                    placeholder="Ingrese la razón social" required>
                                <label for="razonsocial">Razón social</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="nombrecomercial" id="nombrecomercial" class="form-control"
                                    placeholder="Ingrese el nombre comercial" required>
                                <label for="nombrecomercial">Nombre comercial</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="representante" id="representante" class="form-control"
                                    placeholder="Ingrese el nombre del representante" required>
                                <label for="representante">Representante</label>
                            </div>
                        </div>


                    </div>
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



                    <div class="row g-3 mt-1">


                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="exmple@gmail.com">
                                <label for="email">Correo</label>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telprimario" id="telprimario" class="form-control"
                                    placeholder="Número de telefóno" maxlength="9" pattern="[0-9]+" required>
                                <label for="telprimario">Telefóno</label>
                            </div>

                        </div>


                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="tel" name="telsecundario" id="telsecundario" class="form-control"
                                    placeholder="Número de telefóno" maxlength="0" pattern="[0-9]+">
                                <label for="telsecundario">Telefóno 2 (Opcional)</label>
                            </div>

                        </div>


                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="direccion" id="direccion" class="form-control"
                                    placeholder="Ingrese una dirección" required>
                                <label for="direccion">Direccion(Opcional)</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="referencia" id="referencia" class="form-control"
                                    placeholder="Ingrese una dirección">
                                <label for="referencia">Referencia(Opcional)</label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="latitud" id="latitud" class="form-control"
                                    placeholder="Ingrese la latitud">
                                <label for="latitud">Latitud</label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" name="longitud" id="longitud" class="form-control"
                                    placeholder="Ingrese la longitud">
                                <label for="longitud">Longitud</label>
                            </div>
                        </div>

                        <div class="col-md-2">

                            <button type="button" class="btn btn-sm btn-success" id="btn-mapa">Ver mapa</button>
                        </div>
                    </div>


                    <!-- Botón -->
                    <div class="text-end mt-4">
                        <button type="reset" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"
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
        const formRegistroClienteEmpresa = document.getElementById('form-registro-cliente-empresa');


        async function getAllDepartamentos() {
            try {
                const response = await fetch(`/api/ubigeo/departamentos`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log(data);
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

        formRegistroClienteEmpresa.addEventListener('submit', (event) => {
            event.preventDefault();

            if (confirm("¿Desea registrar este nuevo cliente?")) {
                formRegistroClienteEmpresa.submit()
            }
        });
    });

   
</script>

<?php include __DIR__ . '/../../layout/footer.php'; ?>