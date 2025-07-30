<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Compras</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/compras" class="">Mostrar
                    lista</a>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Registrar Compra</h5>
            </div>
            <div class="card-body">
                <form action="/locales/store" id="form-registro-local" autocomplete="off" method="POST">

                    <!-- Ubicación -->
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select name="concesionario" id="concesionario" class="form-select">
                                    <option value="">Seleccione</option>

                                </select>
                                <label for="concesionario">Concesionario</label>
                            </div>
                            <div class="mt-4" id="detalle-container" style="display:none;">
                                <h5>Detalle de órdenes de compra</h5>
                                <div id="detalle-content"></div>
                            </div>
                        </div>

                    </div>



                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="datetime-local" id="fechacompra" name="fechacompra"
                                    class="form-control" placeholder="Fecha de compra" required>
                                <label for="fechacompra">Fecha compra</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="tipodoc" id="tipodoc" class="form-select" required>
                                    <option value="F" selected>Factura</option>
                                    <option value="B">Boleta</option>
                                </select>
                                <label for="tipodoc">Tipo documento</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" id="serie" name="serie"
                                    class="form-control" placeholder="Serie" required>
                                <label for="serie">Serie</label>
                            </div>
                        </div>
                    </div>


                    <!-- Contacto -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" id="numero" name="numero"
                                    class="form-control" placeholder="Número" required>
                                <label for="numero">Número</label>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-floating">
                                <input type="file" name="rutadoc" id="rutadoc" class="form-control"
                                    placeholder="Adjuntar PDF" accept="application/pdf,image/*">
                                <label for="correo">Adjuntar PDF</label>
                            </div>
                        </div>
                    </div>



                    <!-- Botón -->
                    <div class="text-end mt-4">
                        <button type="reset" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                    </div>

                </form>



            </div>
        </div>
    </div>

</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const concesionarios = document.querySelector('#concesionario');
        const detalleContainer = document.querySelector('#detalle-container');
        const detalleContent = document.querySelector('#detalle-content');
        
        // VARIABLE GLOBAL PARA IDENTIFICAR LA OC QUE PASARA COMO COMPRA:

        let idOCCompra = null;
        // Obtener concesionarios
        async function getConcesionarios() {
            try {
                const res = await fetch(`/api/concesionariosDB`);
                const data = await res.json();

                if (data.length > 0) {
                    data.forEach((element) => {
                        concesionarios.innerHTML += `
                            <option value="${element.idconcesionario}">${element.razonsocial}</option>
                        `;
                    });
                } else {
                    showToast('No hay concesionarios', 'INFO', 1200);
                }
            } catch (error) {
                console.error(error);
            }
        }

        // Escucha cambios en el select
        concesionarios.addEventListener('change', async (e) => {
            const id = e.target.value;
            if (!id) return;

            try {
                const res = await fetch(`/api/detOCConcesionario/${id}`);
                const data = await res.json();

                if (data.length === 0) {
                    detalleContainer.style.display = 'none';
                    return;
                }

                let html = '';
                data.forEach((orden) => {
                    html += `
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Orden #${orden.idordencompra}</strong> — Emisión: ${orden.emision}
                                <button class="btn btn-sm btn-outline-primary float-end seleccionar-orden"
                                        data-id="${orden.idordencompra}">
                                    Seleccionar
                                </button>

                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th>Versión</th>
                                                <th>Combustible</th>
                                                <th>Año</th>
                                                <th>Color</th>
                                                <th>Condición</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                    `;

                    const agrupados = {};

                    orden.detalle.forEach(item => {
                        const key = `${item.marca}|${item.modelo}|${item.version}|${item.combustible}|${item.anio}|${item.color}|${item.condicion}|${item.moneda}|${item.preciocompra}`;

                        if (agrupados[key]) {
                            agrupados[key].cantidad += 1;
                        } else {
                            agrupados[key] = {
                                ...item,
                                cantidad: 1
                            };
                        }
                    });

                    Object.values(agrupados).forEach(item => {
                        html += `
        <tr>
            <td>${item.marca}</td>
            <td>${item.modelo}</td>
            <td>${item.version}</td>
            <td>${item.combustible}</td>
            <td>${item.anio}</td>
            <td>${item.color}</td>
            <td>${item.condicion}</td>
            <td>${item.moneda} ${item.preciocompra}</td>
            <td class="text-center">${item.cantidad}</td>
        </tr>
    `;
                    });

                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                });

                detalleContent.innerHTML = html;
                detalleContainer.style.display = 'block';

            } catch (error) {
                console.error('Error al cargar el detalle:', error);
            }
        });

        await getConcesionarios();

        // Escuchar clics en botones "Seleccionar"
        detalleContent.addEventListener('click', function(e) {
            if (e.target.classList.contains('seleccionar-orden')) {
                const idSeleccionado = e.target.dataset.id;
                idOCCompra = idSeleccionado;
                console.log('ID ORDEN SELECCIONADA:', idSeleccionado);

                // Guardar en un campo oculto .
                let inputHidden = document.querySelector('#idordencompra');
                if (!inputHidden) {
                    inputHidden = document.createElement('input');
                    inputHidden.type = 'hidden';
                    inputHidden.name = 'idordencompra';
                    inputHidden.id = 'idordencompra';
                    document.querySelector('#form-registro-local').appendChild(inputHidden);
                }

                inputHidden.value = idSeleccionado;

                showToast(`Orden #${idSeleccionado} seleccionada`, 'SUCCESS', 2000);
            }
        });

        console.log('ID OC SELECCIONADA: ', idOCCompra);

    });
</script>