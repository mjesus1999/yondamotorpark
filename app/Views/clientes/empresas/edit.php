<?php include __DIR__ . '/../../layout/header.php'; ?>



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
                            <li class="breadcrumb-item"><a href="#">CLientes (Empresas)</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <a class="btn btn-sm btn-outline-primary" href="/clientes/empresas" class="">Mostrar
                        lista</a>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-yonda text-white">
                    <h5 class="mb-0">Actualizar Cliente (Empresa)</h5>
                </div>

                <div class="card-body">
                    <?php if (isset($empresaCliente)): ?>
                        <form action="/clientes/empresaCliente/update/<?= htmlspecialchars($empresaCliente['idempresa']) ?>" autocomplete="off" id="formulario-cliente-empresa" method="POST">

                            <div class="modal-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="razonsocial" name="razonsocial" placeholder="Razón Social"
                                                required value="<?= htmlspecialchars($empresaCliente['razonsocial']) ?>">
                                            <label for="razonsocial">Razón Social</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="nombrecomercial" name="nombrecomercial"
                                                placeholder="Nombre Comercial" required value="<?= htmlspecialchars($empresaCliente['nombrecomercial']) ?>">
                                            <label for="nombrecomercial">Nombre Comercial</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="ruc" name="ruc" placeholder="RUC" maxlength="11"
                                                required value="<?= htmlspecialchars($empresaCliente['ruc']) ?>">
                                            <label for="ruc">RUC</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="representante" name="representante" required
                                                placeholder="Representante Legal" value="<?= htmlspecialchars($empresaCliente['representante']) ?>">
                                            <label for="representante">Representante</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Correo electrónico" value="<?= htmlspecialchars($empresaCliente['email'] ?? '') ?>">
                                            <label for="email">Correo electrónico</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="telprimario" maxlength="9" name="telprimario" required
                                                placeholder="Teléfono principal" value="<?= htmlspecialchars($empresaCliente['telprimario']) ?>">
                                            <label for="telprimario">Teléfono</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer mt-2">

                                
                                <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                            </div>
                </div>

                </form>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    Empresa cliente no encontrada.
                </div>
                <a href="/clientes/empresas" class="btn btn-secondary">Volver a la lista</a>
            <?php endif; ?>

            </div>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const formularioClienteEmpresa = document.querySelector('#formulario-cliente-empresa');

        formularioClienteEmpresa.addEventListener('submit', (event) => {
            event.preventDefault();
            if (confirm("¿Desea actualizar este cliente?")) {

                event.target.submit();

            }
        });
    });
</script>



<?php include __DIR__ . '/../../layout/footer.php'; ?>