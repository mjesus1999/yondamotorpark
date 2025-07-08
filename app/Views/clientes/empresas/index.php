<?php include __DIR__ . '/../../layout/header.php';?>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('<?= addslashes($_SESSION['success']) ?>', 'SUCCESS', 1000);
        });
    </script>
    <?php unset($_SESSION['success']); ?> 
<?php endif; ?>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Empresas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/clientes">Clientes (Normales)</a></li>
                    </ol>
                </nav>
                <a class="btn btn-sm btn-outline-primary" href="/clientes/empresas/createempresaclient">Registrar</a>
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-cliente-empresa">
                            
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ubicación</th>
                                    <th>Dirección</th>
                                    <th>Responsable</th>
                                    <th>Ruc</th>
                                    <th>Empresa</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            
                             <?php if (empty($empresasClientes)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay clientes empresas registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($empresasClientes as $empresaCliente): ?>
                                        <tr>
                                            <td class="align-middle"><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['ubicacion']) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['direccion'] ?? 'No asignado') ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['responsable']) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['ruc']) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['nombrecomercial']) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['email'] ?? 'No asignado') ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($empresaCliente['telprimario']) ?></td>
                                            <td class="align-middle">
                                            <div class="d-flex gap-1">
                                                <a href="/clientes/empresaCliente/edit/<?= htmlspecialchars($empresaCliente['idempresa']) ?>"
                                                    class="btn btn-sm btn-outline-primary"> <i class="fa-solid fa-pen"></i></a>

                                                <form action="/empresaCliente/delete/<?= htmlspecialchars($empresaCliente['idcliente']) ?>" method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                                    <button type="submit" class='btn btn-sm btn-outline-danger delete' title='Eliminar'>
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                                 </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

   



<?php include __DIR__ . '/../../layout/footer.php'; ?>