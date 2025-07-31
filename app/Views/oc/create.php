<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <!-- <nav aria-label="breadcrumb"> -->
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                    aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Órdenes de compra</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/oc/" class="btn btn-sm btn-outline-primary">Mostrar lista</a>
            </div>
        </div>
    </div>

    <form action="" id="formulario-oc" autocomplete="off">
        <div class="card mb-2">
            <div class="card-header bg-info">
                <strong>Paso 1:</strong> <span class="fst-italic"> Datos generales de la orden de compra</span>
            </div>
            <div class="card-body">

                <div class="row g-2">
                    <div class="col-md-2 mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control text-center" name="serie" id="serie" maxlength="11"
                                value="2025">
                            <label for="form-label">Serie</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="form-floating">
                            <input type="date" class="form-control text-center" id="fechaemision">
                            <label for="form-label">Fecha emisión</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="form-floating">
                            <select name="concesionarios" id="concesionarios" class="form-select" required>
                                <option value="">Seleccione</option>
                            </select>
                            <label for="form-label">Concesionario</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control text-center" name="ruc" id="ruc" maxlength="11"
                                required readonly disabled>
                            <label for="form-label">RUC</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="form-floating">
                            <select name="idtienda" id="tiendas" class="form-select" required>
                                <option value="">Seleccione</option>
                            </select>
                            <label for="form-label">Tienda</label>
                        </div>
                    </div>

                </div> <!-- ./row -->

                <div class="row g-2">
                    <div class="col-md-3 mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="direccion" id="direccion" required readonly
                                disabled>
                            <label for="form-label">Dirección</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="asesor" id="asesor" required readonly
                                disabled>
                            <label for="form-label">Asesor</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="text" class="form-control text-center" name="telefono" id="telefono"
                                    required readonly disabled>
                                <label for="form-label">Teléfono</label>
                            </div>
                            <button class="btn btn-outline-success" type="button" id="abrir-wsp"
                                title="Contactar por WhatsApp"><i class="fa-brands fa-whatsapp"></i></button>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control text-center" name="numstock" id="stock">
                            <label for="form-label">Stock</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="form-floating">
                            <select name="moneda" id="moneda" class="form-select">
                                <option value="USD">Dólares</option>
                                <option value="PEN">Soles</option>
                            </select>
                            <label class="form-label" for="moneda">Moneda</label>
                        </div>
                    </div>
                </div> <!-- ./row -->

                <div class="row g-2">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" id="observaciones" name="observaciones" class="form-control">
                            <label for="observaciones">Observaciones</label>
                        </div>
                    </div>
                </div>

            </div> <!-- ./card-body -->

        </div><!-- ./card -->
    </form>



    <div class="card mt-2">
        <div class="card-header bg-info">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <strong>Paso 2: </strong> <span class="fst-italic"> Agregar elementos a la orden de compra</span>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-sm btn-dark" id="agregar-accesorio" type="button"
                            title="Agregar accesorio"><i class="fa-solid fa-box-open"></i> Accesorio</button>
                        <button class="btn btn-sm btn-dark" id="agregar-item" type="button" title="Agregar vehiculo"><i
                                class="fa-solid fa-car-side"></i> Vehículo</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm" id="tabla-detalle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>

                            <th>Precio</th>
                            <th>Descuento</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="reset" class="btn btn-sm btn-outline-secondary">Cancelar</button>
            <button type="submit" class="btn btn-sm btn-primary" id="registrar-OC">Registrar</button>
        </div>

    </div>



    <!-- Zona de modales -->
    <div class="modal fade" id="modal-vehiculo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modal-vehiculo" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-yonda">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Datos del vehículo a comprar</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-2">

                    <!-- Formulario para datos generales de/los vehiculos -->
                    <form action="" id="formulario-vehiculo" autocomplete="off">
                        <div class="row g-2">
                            <div class="col-md-2 mb-2">
                                <div class="form-floating">
                                    <input type="number" id="cantidad" value="1" min="1" max="20"
                                        class="form-control text-center">
                                    <label for="cantidad">Cantidad</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="form-floating">
                                    <select name="marcas" id="marcas" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="marcas" class="form-label">Marca <span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="form-floating">
                                    <select name="tipos" id="tipos" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="tipos">Tipo de vehículo <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="form-floating">
                                    <select name="modelos" id="modelos" class="form-select" required>
                                        <option value="">Seleccione</option>
                                    </select>
                                    <label for="modelos">Modelos disponibles <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <select name="anios" id="anios" class="form-select" required>
                                            <option value="">Seleccione</option>
                                        </select>
                                        <label for="anios">Año <span class="text-danger">*</span></label>
                                    </div>
                                    <button type="button" class="btn btn-outline-success"
                                        title="Incrementa el año del modelo y lo guarda en la base de datos">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-2 mb-2">

                                <!-- lista de versiones -->
                                <div class="form-floating" id="bloque-version-lista">
                                    <select name="version-ls" id="version-ls" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        <optgroup label="Prestaciones">
                                            <option value="Básico">Básico</option>
                                            <option value="Semi Full">Semi Full</option>
                                            <option value="Full">Full</option>
                                            <option value="Tope de gama">Tope de gama</option>
                                        </optgroup>
                                        <optgroup label="Otro">
                                            <option value="ESP">Especificar...</option>
                                        </optgroup>
                                    </select>
                                    <label for="version-ls">Versión <span class="text-danger">*</span></label>
                                </div>

                                <!-- input de versión (especificada por el usuario) -->
                                <div class="input-group d-none" id="bloque-version-input">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="version-in">
                                        <label for="version-in">Describa la versión</label>
                                    </div>
                                    <button type="button" id="mostrar-version-ls" class="btn btn-outline-secondary"
                                        title="Mostrar lista"><i class="fa-solid fa-bars-staggered"></i></button>
                                </div>

                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="form-floating">
                                    <select name="condicion" id="condicion" class="form-select" required>
                                        <option value="nuevo" selected>Nuevo</option>
                                        <option value="seminuevo">Seminuevo</option>
                                    </select>
                                    <label for="condicion">Condición <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="form-floating">
                                    <select name="combustible" id="combustible" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        <option value="1">Gasolina</option>
                                        <option value="2">Diesel</option>
                                        <option value="3">GLP</option>
                                        <option value="4">GNV</option>
                                        <option value="5">Dual: Gasolina, GLP</option>
                                    </select>
                                    <label for="combustible">Tipo de combustible <span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="form-floating">
                                    <input type="text" id="color" class="form-control" placeholder="Color">
                                    <label for="color">Color</label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="form-floating">
                                    <input type="text" id="precio" class="form-control text-end" pattern="[0-9]+"
                                        title="Solo se permiten números" placeholder="Precio" required>
                                    <label for="precio">Precio <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Fin formulario datos generales del vehículo -->

                    <hr>

                    <!-- Fila para agregar chasis, placa, placa rotativa y serie motor -->
                    <div class="row g-2">

                        <div class="row mt-2 g-2">
                            <div class="col-md-1 text-center">#</div>
                            <div class="col-md-4">Chasis</div>
                            <div class="col-md-2">Placa</div>
                            <div class="col-md-2">Placa rotativa</div>
                            <div class="col-md-3">Serie</div>
                        </div>

                        <!-- Se van a generar inputs para agregar los datos de los vehículos -->
                        <div class="content" id="inputs-dinamicos">
                            <!-- Contenido generado de forma dinámica -->
                        </div>

                        <!-- Fila para leyenda de campos obligatorios -->
                        <div class="row">
                            <div class="col-md-12">
                                <span class="fst-italic text-danger">* Campos obligatorios</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="formulario-vehiculo" id="registrar-vehiculo"
                            class="btn btn-sm btn-primary">Agregar</button>
                    </div>
                </div>
            </div> <!-- /.modal-dialog -->
        </div> <!-- ./modal -->
        <!-- Fin Zona de modales -->


        <script>
            document.addEventListener("DOMContentLoaded", async () => {

                const modalVehiculo = new bootstrap.Modal(document.getElementById("modal-vehiculo"))
                const concesionarios = document.querySelector("#concesionarios")

                //input form datos de la OC
                const ruc = document.querySelector("#ruc")
                const fechaEmision = document.querySelector("#fechaemision")
                const tiendas = document.querySelector("#tiendas")
                const direccion = document.querySelector("#direccion")
                const telefono = document.querySelector("#telefono")
                const numstock = document.querySelector("#stock")
                const asesor = document.querySelector("#asesor")
                const moneda = document.querySelector("#moneda")
                const observaciones = document.querySelector("#observaciones")

                //input form vehiculos (modal)
                const cantidad = document.querySelector("#cantidad")
                const tipos = document.querySelector("#tipos")
                const marcas = document.querySelector("#marcas")
                const modelos = document.querySelector("#modelos")
                const anios = document.querySelector("#anios")
                const versionLS = document.querySelector("#version-ls")
                const versionIN = document.querySelector("#version-in")
                const mostrarVersionLS = document.querySelector("#mostrar-version-ls")
                const registrarVehiculo = document.querySelector("#registrar-vehiculo")
                const combustible = document.querySelector('#combustible');
                const color = document.querySelector('#color');
                const precio = document.querySelector('#precio');

                //Formularios
                const formOC = document.querySelector("#formulario-oc")
                const formVehiculo = document.querySelector("#formulario-vehiculo")
                const btnRegistrarOC = document.querySelector('#registrar-OC');
                const btnAgregarItem = document.querySelector('#agregar-item');

                const abrirWsp = document.querySelector("#abrir-wsp")
                const agregarItem = document.querySelector("#agregar-item")

                // Tabla detalle

                const tablaDetalle = document.querySelector('#tabla-detalle tbody');

                // IDCONCESIONARIO
                const idconcesionario = null;

                let dataConcesionarios = []
                let dataTiendas = []
                let dataVehiculos = []
                let dataModelos = [] //Se utilizará para cargar y seleccionar un modelo y luego un año sin hacer doble consulta al backend

                // Función para verificar si se pueden habilitar los botones
                function verificarEstadoBotones() {
                    // Verificar si hay vehículos agregados
                    const hayVehiculos = dataVehiculos.length > 0;

                    // Habilitar/deshabilitar botón Registrar
                    if (hayVehiculos) {
                        btnRegistrarOC.disabled = false;
                        btnRegistrarOC.classList.remove('btn-secondary');
                        btnRegistrarOC.classList.add('btn-primary');
                    } else {
                        btnRegistrarOC.disabled = true;
                        btnRegistrarOC.classList.remove('btn-primary');
                        btnRegistrarOC.classList.add('btn-secondary');
                    }
                }

                function generadorInputsDinamicos(cantidad) {
                    const inputsDinamicos = document.querySelector("#inputs-dinamicos")

                    if (cantidad > 0) {
                        inputsDinamicos.innerHTML = ``
                        for (let i = 1; i <= cantidad; i++) {
                            inputsDinamicos.innerHTML += `
                                    <div class="row g-2">
                                    <div class="col-md-1 mb-2"><input type="text" class="form-control text-center" id="idvh${i}" value="${i}" disabled></div>
                                    <div class="col-md-4 mb-2"><input type="text" class="form-control" id="chas${i}"></div>
                                    <div class="col-md-2 mb-2"><input type="text" class="form-control" id="plac${i}"></div>
                                    <div class="col-md-2 mb-2"><input type="text" class="form-control" id="plar${i}"></div>
                                    <div class="col-md-3 mb-2"><input type="text" class="form-control" id="seri${i}"></div>
                                    </div>
                                    `;
                        }
                    } else {
                        inputsDinamicos.innerHTML = ``
                    }
                }

                //Se deberán generar input de forma dinámica para agregar: id, chasis, placa, placa rotativa, serie
                cantidad.addEventListener("change", function(event) {
                    generadorInputsDinamicos(parseInt(this.value))
                })

                //Se deberán generar input de forma dinámica para agregar: id, chasis, placa, placa rotativa, serie
                cantidad.addEventListener("keyup", function(event) {
                    if (this.value != "") {
                        generadorInputsDinamicos(parseInt(this.value))
                    }
                })

                //Abre Web WhatsApp con el número indicado
                abrirWsp.addEventListener("click", () => {
                    if (telefono.value.length >= 9) {
                        window.open(`https://web.whatsapp.com/send?phone=${telefono.value}`, '_blank')
                    }
                })

                agregarItem.addEventListener("click", () => {
                    modalVehiculo.show()
                    // Inicializar inputs dinámicos con la cantidad actual
                    generadorInputsDinamicos(parseInt(cantidad.value) || 1)
                })

                //Si elige VERSION (especificar...) debemos mostrar una caja de texto
                versionLS.addEventListener("change", (event) => {
                    const opcion = event.target.value

                    if (opcion == "ESP") {
                        document.querySelector("#bloque-version-lista").classList.add("d-none")
                        document.querySelector("#bloque-version-input").classList.remove("d-none")
                        versionIN.value = ``
                        versionIN.focus()
                    } else {
                        versionIN.value = versionLS.value
                    }
                })

                //   Cuando se especifica la VERSION manualmente (input) se puede volver a mostrar la lista
                mostrarVersionLS.addEventListener("click", () => {
                    versionIN.value = ``;
                    document.querySelector("#bloque-version-lista").classList.remove("d-none")
                    document.querySelector("#bloque-version-input").classList.add("d-none")
                    versionLS.value = ``
                })


                //Registra un vehículo (envía los datos a un arreglo) Y DE ESE ARREGLO GENERAR EL REPORTE DE OC.
                formVehiculo.addEventListener("submit", function(event) {
                    event.preventDefault();

                    // Validar campos requeridos
                    if (!marcas.value || !tipos.value || !modelos.value || !anios.value || 
                        !versionLS.value || !combustible.value || !precio.value) {
                        alert("Por favor complete todos los campos obligatorios");
                        return;
                    }

                    // Validar que si la versión es "ESP", el campo de texto no esté vacío
                    if (versionLS.value === "ESP" && !versionIN.value.trim()) {
                        alert("Por favor especifique la versión del vehículo");
                        versionIN.focus();
                        return;
                    }

                    let idVehiculo = dataVehiculos.length;
                    const pregunta = (cantidad.value == 1) ?
                        "¿Agregamos este vehículo?" :
                        `¿Agregamos los ${cantidad.value} vehículos de la lista?`;

                    if (!confirm(pregunta)) {
                        return;
                    }

                    for (let i = 1; i <= parseInt(cantidad.value); i++) {
                        idVehiculo++;

                        const vehiculo = {
                            idVehiculo,
                            idmodelo: modelos.value,
                            modelo_texto: modelos.options[modelos.selectedIndex].text,
                            marca: marcas.options[marcas.selectedIndex].text,
                            tipo: tipos.options[tipos.selectedIndex].text,
                            idcombustible: combustible.value,
                            combustible_texto: combustible.options[combustible.selectedIndex].text,
                            version: (versionLS.value === "ESP") ? versionIN.value : versionLS.value,
                            color: color.value,
                            condicion: condicion.value,
                            precio: precio.value,
                            chasis: document.querySelector(`#chas${i}`) ? document.querySelector(`#chas${i}`).value.trim() : '',
                            placa: document.querySelector(`#plac${i}`) ? document.querySelector(`#plac${i}`).value.trim() : '',
                            placa_rotativa: document.querySelector(`#plar${i}`) ? document.querySelector(`#plar${i}`).value.trim() : '',
                            serie_motor: document.querySelector(`#seri${i}`) ? document.querySelector(`#seri${i}`).value.trim() : ''
                        };


                        dataVehiculos.push(vehiculo);
                    }

                    renderizarTabla();

                    console.log("Vehículos agregados:", dataVehiculos);

                    // Puedes cerrar el modal y resetear el formulario
                    formVehiculo.reset();
                    generadorInputsDinamicos(0);
                    modalVehiculo.hide();
                });


                // Función para renderizar la tabla 

                function renderizarTabla() {
                    tablaDetalle.innerHTML = "";

                    dataVehiculos.forEach((veh, index) => {
                        tablaDetalle.innerHTML += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>
                                            <a href="#" class="btn-eliminar" data-index="${index}">
                                                <i class="fa-solid fa-trash text-danger"></i>
                                            </a> ::
                                            ${veh.marca} / ${veh.tipo} / ${veh.modelo_texto} ${veh.color || ""} /
                                            ${veh.version} / Chasis: ${veh.chasis || "-"}
                                        </td>
                                        <td>1</td>
                                        <td>${veh.precio}</td>
                                        <td>0</td>
                                        <td>${veh.precio}</td>
                                    </tr>
                                `;
                    });

                    console.log("Vehículos actuales:", dataVehiculos);
                    
                    // Verificar estado de botones después de renderizar
                    verificarEstadoBotones();
                }

                // Eliminar vehículo de la lista y del array
                tablaDetalle.addEventListener("click", function(event) {
                    if (event.target.closest(".btn-eliminar")) {
                        event.preventDefault();

                        const index = event.target.closest(".btn-eliminar").dataset.index;
                        if (confirm("¿Eliminar este vehículo de la lista?")) {
                            // Eliminar del array
                            dataVehiculos.splice(index, 1);

                            // Volver a renderizar la tabla
                            renderizarTabla();
                        }
                    }
                });


                // Obtener los concesionarios de la DB
                async function obtenerConcesionarios() {

                    try {
                        const res = await fetch(`/api/concesionariosDB`);
                        const data = await res.json();

                        if (data.length > 0) {
                            concesionarios.innerHTML = `<option value=''>Seleccione</option>`;
                            data.forEach(element => {
                                concesionarios.innerHTML += ` <option value="${element.idconcesionario}">${element.nombrecomercial}</option>`;
                            });
                            dataConcesionarios = data;
                        } else {
                            console.error('No hay datos a mostrar');
                        }

                    } catch (error) {
                        console.error(error);
                    }

                }

                async function obtenerTiendaByConcesionario(idconcesionario) {

                    try {

                        const res = await fetch(`/api/tiendasConcesionario/${idconcesionario}`);
                        const data = await res.json();
                        return data;
                    } catch (error) {
                        console.error(error);
                    }

                }


                concesionarios.addEventListener('change', async (event) => {
                    const idConcesionario = event.target.value;
                    const concesionarioSeleccionado = dataConcesionarios.find(item => item.idconcesionario == idConcesionario);

                    // Asignar RUC o limpiar si no hay
                    ruc.value = concesionarioSeleccionado ? concesionarioSeleccionado.ruc : '';

                    // impiar tiendas antes de cargarlas
                    tiendas.innerHTML = `<option value=''>Seleccione</option>`;

                    if (!idConcesionario) {
                        dataTiendas = [];
                        direccion.value = '';
                        asesor.value = '';
                        telefono.value = '';
                        return;
                    }

                    // 2️ Obtener las tiendas del concesionario
                    dataTiendas = await obtenerTiendaByConcesionario(idConcesionario);

                    if (dataTiendas.length === 0) {
                        tiendas.innerHTML = `<option value=''>No hay tiendas registradas</option>`;
                    } else {
                        dataTiendas.forEach(element => {
                            tiendas.innerHTML += `<option value='${element.idtienda}'>${element.ubigeo}</option>`;
                        });
                    }

                    // 3️ Limpiar los campos de dirección/teléfono/asesor
                    direccion.value = '';
                    asesor.value = '';
                    telefono.value = '';
                });

                // Cuando seleccione la tienda, me ocmplete con sus datos.
                tiendas.addEventListener('change', (event) => {
                    const idTiendaSeleccionada = event.target.value;

                    if (!idTiendaSeleccionada) {
                        direccion.value = '';
                        asesor.value = '';
                        telefono.value = '';
                        return;
                    }

                    const tiendaSeleccionada = dataTiendas.find(item => item.idtienda == idTiendaSeleccionada);
                    direccion.value = tiendaSeleccionada.direccion;
                    asesor.value = tiendaSeleccionada.contacto;
                    telefono.value = tiendaSeleccionada.telefono;
                });



                btnRegistrarOC.addEventListener("click", async () => {
                    if (!confirm("¿Registrar esta orden con sus vehículos?")) return;

                    const formDataOC = new FormData(formOC);

                    try {
                        // 1. Crear la Orden de Compra
                        const resOC = await fetch('/oc/store', {
                            method: 'POST',
                            body: formDataOC
                        });

                        const dataOC = await resOC.json();

                        if (!dataOC.success) {
                            showToast(dataOC.message, "WARNING", 2000);
                            return;
                        }

                        const idOC = dataOC.id;
                        console.log("Orden creada con ID:", idOC);

                        showToast("Registrando vehículos, espere...", "INFO", 3000);

                        // Esperamos 3 segundos antes de registrar los vehículos
                        setTimeout(async () => {
                            for (let vehiculo of dataVehiculos) {
                                try {
                                    // 2. Registrar cada vehículo en la tabla vehiculos
                                    const formVeh = new FormData();
                                    formVeh.append("idmodelo", vehiculo.idmodelo);
                                    formVeh.append("idcombustible", vehiculo.idcombustible);
                                    formVeh.append("version", vehiculo.version);
                                    formVeh.append("color", vehiculo.color);
                                    formVeh.append("chasis", vehiculo.chasis);
                                    formVeh.append("placa", vehiculo.placa);
                                    formVeh.append("placarotativa", vehiculo.placa_rotativa);
                                    formVeh.append("seriemotor", vehiculo.serie_motor);

                                    const resVeh = await fetch('/vehiculosOC/store', {
                                        method: 'POST',
                                        body: formVeh
                                    });

                                    const dataVeh = await resVeh.json();

                                    if (!dataVeh.success) {
                                        console.warn("Vehículo con error:", dataVeh.message);
                                        continue;
                                    }

                                    console.log("Vehículo creado con ID:", dataVeh.id);

                                    // 3. Registrar en detordencompra usando el ID del vehículo recién creado
                                    const formDetalle = new FormData();
                                    formDetalle.append("idordencompra", idOC);
                                    formDetalle.append("idvehiculo", dataVeh.id);
                                    formDetalle.append("preciocompra", vehiculo.precio);

                                    const resDetalle = await fetch('/detalleOC/store', {
                                        method: 'POST',
                                        body: formDetalle
                                    });

                                    const dataDetalle = await resDetalle.json();

                                    if (!dataDetalle.success) {
                                        console.warn("Detalle OC con error:", dataDetalle.message);
                                    } else {
                                        console.log(`Detalle OC registrado: Vehículo ${dataVeh.id} en Orden ${idOC}`);
                                    }

                                } catch (err) {
                                    console.error("Error registrando vehículo o detalle:", err);
                                }
                            }

                            showToast("¡OC y detalles registrados correctamente!", "SUCCESS", 2000);

                            setTimeout(() => {
                                window.location = '/oc/';
                            }, 2000);

                            //Limpiar
                            // dataVehiculos = [];
                            // renderizarTabla();
                            // formOC.reset();
                        }, 3000);



                    } catch (error) {
                        console.error("Error:", error);
                        showToast("Error en el registro", "WARNING", 2000);
                    }
                });


                // Obtener las Marcas de la DB
                async function obtenerMarcas() {
                    try {

                        const res = await fetch(`/api/marcas`);
                        const data = await res.json();

                        if (data.length > 0) {
                            marcas.innerHTML = '<option>Seleccione</option>'

                            data.forEach(element => {
                                marcas.innerHTML += `<option value="${element.idmarca}">${element.marca}</option>`;
                            });
                        } else {
                            marcas.innerHTML = '<option> No hay datos registrados</option>'
                        }

                    } catch (error) {
                        console.error(error);
                    }

                }

                // EVENTO CUANDO SELECCIONE UN MARCA, SE AGRGEUEN LOS TIPOS DE VEHICULOS DE ESA MARCA AL SELECT
                marcas.addEventListener('change', async (event) => {
                    const idmarca = event.target.value;
                    const res = await fetch(`/api/getTipoVehiculoByMarca/${idmarca}`);
                    const data = await res.json();


                    if (data.length > 0) {
                        tipos.innerHTML = '<option>Seleccione</option>';
                        data.forEach(element => {
                            tipos.innerHTML += `<option value="${element.idtipovehiculo}">${element.tipovehiculo}</option>`;
                        });
                    } else {
                        tipos.innerHTML = '<option> No hay datos</option>'
                    }

                });

                // EVENTO CUANDO SE SEECCIONE UN TIPO DE VEHICULO Y ME MUESTRE SUS MODELO
                tipos.addEventListener('change', async (event) => {
                    const idmarca = parseInt(marcas.value);
                    const idTipoVehiculo = parseInt(event.target.value);

                    const res = await fetch(`/api/getModeloByTipoMarca/${idmarca}/${idTipoVehiculo}`);
                    dataModelos = await res.json();

                    modelos.innerHTML = '<option>Seleccione</option>';
                    anios.innerHTML = '<option>Seleccione</option>';

                    if (dataModelos.length > 0) {
                        // Map para evitar modelos repetidos (por si vienen con distintos años) - Agregar cualquir tipo de dato {c:v}
                        const modelosUnicos = new Map();

                        dataModelos.forEach(element => {
                            if (!modelosUnicos.has(element.modelo)) { //Pregunto si ya fue agregado
                                modelosUnicos.set(element.modelo, element.idmodelo);
                            }
                        });

                        modelosUnicos.forEach((idmodelo, modelo) => {
                            modelos.innerHTML += `<option value="${idmodelo}">${modelo}</option>`;
                        });
                    } else {
                        modelos.innerHTML = '<option>No hay datos</option>';
                    }
                });


                modelos.addEventListener('change', async (event) => {
                    const modeloSeleccionado = event.target.value;
                    // Filtrar todos los objetos con ese modelo
                    const modelosFiltrados = dataModelos.filter(item => item.idmodelo == modeloSeleccionado);

                    // Extraer años únicos
                    const aniosUnicos = [...new Set(modelosFiltrados.map(item => item.anio))];

                    // Limpiar el select de años
                    anios.innerHTML = '<option>Seleccione</option>';

                    if (aniosUnicos.length > 0) {
                        aniosUnicos.forEach(anio => {
                            anios.innerHTML += `<option value="${anio}">${anio}</option>`;
                        });
                    } else {
                        anios.innerHTML += `<option>No hay datos</option>`;
                    }
                });

                function asignarFechaActual() {
                    const hoy = new Date()
                    const fechaFormat = hoy.toISOString().split('T')[0]
                    fechaEmision.value = fechaFormat
                    fechaEmision.setAttribute("disabled", true)
                }


                asignarFechaActual();
                await obtenerConcesionarios();
                await obtenerMarcas();
                
                // Inicializar estado de botones
                verificarEstadoBotones();

            });
        </script>


        <?php include __DIR__ . '/../layout/footer.php'; ?>