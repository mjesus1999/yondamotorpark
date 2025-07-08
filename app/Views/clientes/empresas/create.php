<?php

use function App\Helpers\persistirDatosFormulario;

require_once __DIR__ . '/../../../Helpers/functions.php'; ?>
<?php include __DIR__ . '/../../layout/header.php'; ?>
<?php include __DIR__ . '/../../components/mapa-includes.php'; ?>
<?php include __DIR__ . '/../../components/mapa-modal.php'; ?>

<?php if (isset($error) && !empty($error)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" id="errorToast">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $error ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <?php persistirDatosFormulario($data) ?>
<?php endif; ?>

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
                                    placeholder="Número de telefóno" maxlength="9" pattern="[0-9]+">
                                <label for="telsecundario">Telefóno 2 (Opcional)</label>
                            </div>

                        </div>
                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" name="direccion" id="direccion" class="form-control"
                                    placeholder="Ingrese una dirección">
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

<script src="/assets/js/ubigeo.js"></script>
<script>
    const formRegistroClienteEmpresa = document.querySelector('#form-registro-cliente-empresa');

    formRegistroClienteEmpresa.addEventListener('submit', (event) => {
        event.preventDefault();

        if (confirm("¿Desea registrar este nuevo cliente?")) {
            formRegistroClienteEmpresa.submit()
        }
    });
</script>

<?php include __DIR__ . '/../../layout/footer.php'; ?>