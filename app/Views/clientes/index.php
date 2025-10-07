<?php

include __DIR__ . '/../layout/header.php';
?>

<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        <li class="breadcrumb-item"><a href="#">Clientes (Personas)</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/clientes/empresas">Jurídicas (Empresas)</a></li>
                    </ol>
                </nav>
                <a class="btn btn-sm btn-outline-primary" href="/clientes/createpersonclient">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- VISTA DE ESCRITORIO (tabla) -->
                    <div class="table-responsive d-none d-md-block">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input

                                    type="text"
                                    id="busqueda-global"
                                    class="form-control"
                                    placeholder="Buscar ....">
                            </div>
                        </div>
                        <div" id="tabla-clientes-personas">
                            <!-- <thead>
                                <tr class="text-primary">
                                    <th>#</th>
                                    <th><span class="text-body badge">Ubicación</span></th>
                                    <th> <span class="text-body badge">Dirección</span></th>
                                    <th><span class="text-body badge">Nombre completo</span> </th>
                                    <th> <span class="text-body badge">Documento</span></th>
                                    <th><span class="text-body badge">N° documento</span></th>
                                    <th><span class="text-body badge">Teléfono</span></th>
                                    <th><span class="text-body badge">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($personClientes)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay clientes personas registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($personClientes as $personCliente): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><span class="text-body badge"><?= htmlspecialchars($personCliente['ubicacion']) ?></span></td>
                                            <td> <span class="badge text-body"><?= htmlspecialchars($personCliente['direccion'] ?? 'N/A') ?></span></td>
                                            <td> <span class="badge text-body"><?= htmlspecialchars($personCliente['nombrecompleto']) ?></td>
                                            <td> <span class="badge text-body"><?= htmlspecialchars($personCliente['tipodoc']) ?></span></td>
                                            <td><span class="badge text-body"><?= htmlspecialchars($personCliente['nrodoc']) ?></span> </td>
                                
                                            <td><span class="badge text-body"><?= htmlspecialchars($personCliente['telprimario']) ?></span></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <div class="">

                                                        <a href="/personaCliente/edit/<?= htmlspecialchars($personCliente['idpersona']) ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>

                                                    </div>
                                                    <form action="/personaCliente/delete/<?= htmlspecialchars($personCliente['idcliente']) ?>" method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete" title="Eliminar">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody> -->
                            </table>
                    </div>

                    <!-- VISTA MÓVIL (ACORDEÓN) -->
                    <div class="d-block d-md-none">
                        <?php if (!empty($personClientes)): ?>
                            <div class="accordion" id="acordeonClientes">
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($personClientes as $personCliente): ?>
                                    <div class="accordion-item mb-2 shadow-sm">
                                        <h2 class="accordion-header" id="heading-<?= $personCliente['idcliente'] ?>">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseCliente<?= $personCliente['idcliente'] ?>"
                                                aria-expanded="false"
                                                aria-controls="collapseCliente<?= $personCliente['idcliente'] ?>">
                                                <span><i class="bi bi-person-fill me-2 text-primary fw-bold"></i><?= htmlspecialchars($personCliente['nombrecompleto']) ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapseCliente<?= $personCliente['idcliente'] ?>"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="heading-<?= $personCliente['idcliente'] ?>"
                                            data-bs-parent="#acordeonClientes">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>#:</strong> <?= $numeroFila++ ?></li>
                                                    <li class="list-group-item"><strong>Ubicación:</strong> <?= htmlspecialchars($personCliente['ubicacion']) ?></li>
                                                    <li class="list-group-item"><strong>Dirección:</strong> <?= htmlspecialchars($personCliente['direccion'] ?? 'No asignado') ?></li>
                                                    <li class="list-group-item"><strong>Tipo documento:</strong> <?= htmlspecialchars($personCliente['tipodoc']) ?></li>
                                                    <li class="list-group-item"><strong>N° documento:</strong> <?= htmlspecialchars($personCliente['nrodoc']) ?></li>
                                                    <li class="list-group-item"><strong>Correo:</strong> <?= htmlspecialchars($personCliente['email'] ?? 'No asignado') ?></li>
                                                    <li class="list-group-item"><strong>Teléfono:</strong> <?= htmlspecialchars($personCliente['telprimario']) ?></li>
                                                </ul>
                                                <div class="mt-3">
                                                    <strong>Acciones:</strong>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <a href="/personaCliente/edit/<?= htmlspecialchars($personCliente['idpersona']) ?>"
                                                            class="btn btn-sm btn-outline-primary" title="Editar">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>
                                                        <form action="/personaCliente/delete/<?= htmlspecialchars($personCliente['idcliente']) ?>" method="POST"
                                                            onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');" class="m-0 p-0">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger delete" title="Eliminar">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted p-3">No hay clientes personas registradas.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>

<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const datos = <?= json_encode($personClientes) ?>;
        console.log('PERSONAS: ', datos)

        const tabla = new Tabulator("#tabla-clientes-personas", {
            data: datos,
            layout: "fitColumns",
            pagination: "local",
            paginationSize: 15,
             paginationSizeSelector:[5, 10, 20],
            movableRows: true,
            columns: [{
                    title: "#",
                    formatter: "rownum",
                    width: 50
                },
                {
                    title: "Ubicación",
                    field: "ubicacion",
                    widthGrow: 2,
                      tooltip: true
                },
                {
                    title: "Dirección",
                    field: "direccion",
                    widthGrow: 3,
                      tooltip: true
                },
                {
                    title: "Nombre Completo",
                    field: "nombrecompleto",
                    widthGrow: 2,
                      tooltip: true
                },
                {
                    title: "Documento",
                    field: "tipodoc",
                      tooltip: true
                },
                {
                    title: "N° documento",
                    field: "nrodoc",
                      tooltip: true
                },
                {
                    title: "Teléfono",
                    field: "telprimario",
                      tooltip: true
                },
                {
                    title: "Acciones",
                    field: "acciones",
                    headerSort: false,
                    formatter: function(cell, formatterParams) {
                        const data = cell.getRow().getData();
                        return `
                        
                            <a href="/personaCliente/edit/${data.idpersona}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="/personaCliente/delete/${data.idcliente}" method="POST" class="d-inline"
                                onsubmit="return confirm('¿Estás seguro?');">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                      
                    `;
                    }
                }
            ],
            locale: "es-es",
            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Registros por página",
                        "first": "Primero",
                        "last": "Último",
                        "prev": "Anterior",
                        "next": "Siguiente",
                    }
                }
            },

        });

        const searchInput = document.getElementById("busqueda-global");
        if (searchInput) {
            searchInput.addEventListener("keyup", function(e) {
                const value = e.target.value;
                if (value === "") {
                    tabla.clearFilter();
                } else {
                    tabla.setFilter([
                        [{
                                field: "nombrecompleto",
                                type: "like",
                                value: value
                            },
                            {
                                field: "nrodoc",
                                type: "like",
                                value: value
                            },

                        ]
                    ]);
                }
            });
        }


    });
</script>