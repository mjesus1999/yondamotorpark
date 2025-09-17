<?php include __DIR__ . '/../layout/header.php'; ?>


<style>
    .highlight-row {
        background: #ffeaa7 !important;
        border: 3px solid #fdcb6e !important;
        transition: all 0.3s ease !important;
        font-weight: bold !important;
        box-shadow: 0 0 10px rgba(253, 203, 110, 0.5) !important;
    }

    @keyframes blink {

        0%,
        100% {
            background-color: #ffeaa7;
            border-color: #fdcb6e;
        }

        50% {
            background-color: #55a3ff;
            border-color: #2980b9;
        }
    }

    .blink-animation {
        scale: 1.03;
        animation: blink 1.2s ease-in-out 4;
    }

    .highlight-row:hover {
        background-color: #ffeaa7 !important;
    }
</style>

<div class="container-fluid">

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/vehiculos/create" class="btn btn-outline-primary btn-sm">
                    <!-- <i class="bi bi-plus"></i> -->Registrar
                </a>
            </div>
        </div>
    </div>

    <!-- <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/vehiculos/create" class="">[ Registrar ]</a>
            </div>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-22">
            <div class="card">

                <div class="card-header">
                    <?php $estadoActual = $estadoActual ?? ''; ?>

                    <div class="btn-group m-1" id="botones-filtro">

                        <a href="/vehiculos?estado=proceso"
                            class="btn btn-sm <?= $estadoActual === 'proceso' ? 'btn-warning text-white' : 'btn-outline-warning' ?>">
                            Proceso
                        </a>
                        <a href="/vehiculos?estado=libre"
                            class="btn btn-sm <?= $estadoActual === 'libre' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Libre
                        </a>
                        <a href="/vehiculos?estado=separado"
                            class="btn btn-sm <?= $estadoActual === 'separado' ? 'btn-success' : 'btn-outline-success' ?>">
                            Separado
                        </a>
                        <a href="/vehiculos?estado=vendido"
                            class="btn btn-sm <?= $estadoActual === 'vendido' ? 'btn-danger' : 'btn-outline-danger' ?>">
                            Pagado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-sm " id="tabla-vehiculos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Marca</th>
                                <th>Tipo Vehiculo</th>
                                <th>Modelo</th>
                                <th>Version</th>
                                <th>Condicion</th>
                                <th>Color</th>
                                <th>Disponibilidad</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($vehiculos as $v): ?>
                                <tr>
                                    <td><?= htmlspecialchars($v['idvehiculo']) ?></td>
                                    <td><?= htmlspecialchars($v['marca']) ?></td>
                                    <td><?= htmlspecialchars($v['tipovehiculo']) ?></td>
                                    <td><?= htmlspecialchars($v['modelo']) ?></td>
                                    <td><?= htmlspecialchars($v['version']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($v['condicion'])) ?></td>
                                    <td><?= !empty($v['color']) ? htmlspecialchars($v['color']) : 'N/A' ?></td>
                                    <td><?= htmlspecialchars(ucfirst($v['disponibilidad'])) ?></td>
                                    <td class="text-center">
                                        <!-- Editar -->
                                        <a href="/vehiculos/edit/<?= $v['idvehiculo'] ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <!-- Eliminar -->
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-borrar"
                                            title="Eliminar" data-id="<?= htmlspecialchars($v['idvehiculo']) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const table = $('#tabla-vehiculos').DataTable({
            order: [
                [0, 'desc']
            ],
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Todos"]
            ],
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json",
                paginate: {
                    first: '«',
                    previous: '‹',
                    next: '›',
                    last: '»'
                }
            },
            initComplete: function () {
                const params = new URLSearchParams(window.location.search);
                const nuevoId = params.get('vehiculo_nuevo');

                if (nuevoId) {

                    setTimeout(() => {
                        resaltarVehiculo(this.api(), nuevoId);
                    }, 500);
                }
            }
        });

        // Función principal para resaltar vehículo
        function resaltarVehiculo(api, targetId) {
            console.log('Buscando vehículo ID:', targetId);

            //  Buscar usando jQuery directamente en el DOM
            const $filaEncontrada = $('#tabla-vehiculos tbody tr').filter(function () {
                const idEnFila = $(this).find('td:first').text().trim();
                return idEnFila === targetId.toString();
            });

            if ($filaEncontrada.length > 0) {
                console.log('Fila encontrada con jQuery');
                aplicarResaltadoDirecto($filaEncontrada[0]);
                return;
            }

            //  Buscar en todas las páginas si no se encuentra en la actual
            let filaEncontrada = null;
            let paginaDestino = -1;

            // Obtener información de paginación
            const info = api.page.info();
            const totalPaginas = Math.ceil(info.recordsDisplay / info.length);

            // Buscar en todas las páginas
            for (let pagina = 0; pagina < totalPaginas; pagina++) {
                api.page(pagina).draw('page');

                // Buscar en la página actual
                $('#tabla-vehiculos tbody tr').each(function () {
                    const idEnFila = $(this).find('td:first').text().trim();
                    if (idEnFila === targetId.toString()) {
                        filaEncontrada = this;
                        paginaDestino = pagina;
                        return false; // Salir del each
                    }
                });

                if (filaEncontrada) break;
            }

            if (filaEncontrada && paginaDestino !== -1) {
                console.log(`Fila encontrada en página: ${paginaDestino}`);
                // Asegurar que estamos en la página correcta
                api.page(paginaDestino).draw('page');

                setTimeout(() => {
                    aplicarResaltadoDirecto(filaEncontrada);
                }, 200);
            } else {
                console.warn('No se encontró el vehículo con ID:', targetId);
            }
        }

        // Función para aplicar resaltado directo
        function aplicarResaltadoDirecto(fila) {
            const $fila = $(fila);
            // Remover clases previas
            $fila.removeClass('highlight-row blink-animation');

            // Forzar reflow
            fila.offsetHeight;

            // Aplicar resaltado inmediato
            $fila.addClass('highlight-row');

            // Scroll hacia la fila
            fila.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Aplicar animación después de un momento
            setTimeout(() => {
                $fila.addClass('blink-animation');
            }, 200);

            // Remover resaltado después de 6 segundos
            setTimeout(() => {
                $fila.removeClass('highlight-row blink-animation');
            }, 6000);

            // Limpiar URL para evitar que se repita el resaltado
            setTimeout(() => {
                const url = new URL(window.location);
                url.searchParams.delete('vehiculo_nuevo');
                window.history.replaceState({}, '', url);
            }, 1000);
        }

        // Eventos de eliminación - usando delegación de eventos
        $(document).on('click', '.btn-borrar', async function () {
            const id = $(this).data('id');
            if (!id) return;

            try {
                const result = await ask('¿Está seguro de eliminar el vehículo?', 'Confirmar eliminación');

                if (!result) return;

                // Mostrar loading
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const response = await fetch('/vehiculos/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        idvehiculo: id
                    }),
                    credentials: 'same-origin'
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {


                    const params = new URLSearchParams(window.location.search);
                    window.location.href = '/vehiculos?' + params.toString();
                } else {
                    let errorMessage = 'No se pudo eliminar el vehículo.';

                    if (data.error) {
                        const errorLower = data.error.toLowerCase();
                        if (errorLower.includes('1451') || errorLower.includes('foreign key')) {
                            errorMessage = 'No se puede eliminar este vehículo porque está referenciado en otras tablas.';
                        } else {
                            errorMessage = data.error;
                        }
                    }

                    Swal.fire('Error', errorMessage, 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>