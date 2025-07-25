<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="#">Modulo de editar Vehiculo</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="" id="edit-registro-vehiculos">
            <div class="card mb-2">
                <div class="card-header bg-info">
                    <strong>Paso 1:</strong> <span class="fst-italic">Registra Vehiculos</span>
                </div>
                <div class="card-body">

                    <div class="row g-2">
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <select name="marcas" id="marcas" class="form-select" required>
                                    <option value="">Seleccione</option>
                                </select>
                                <label for="marcas" class="form-label">Marca <span class="text-danger">*</span></label>
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
                        <div class="col-md-2 mb-2">
                            <div class="form-floating">
                                <select name="moneda" id="moneda" class="form-select" required>
                                    <option value="USD" <?= $vehiculo['moneda'] === 'USD' ? 'selected' : '' ?>>Dólares
                                    </option>
                                    <option value="PEN" <?= $vehiculo['moneda'] === 'PEN' ? 'selected' : '' ?>>Soles
                                    </option>
                                </select>
                                <label for="moneda">Moneda <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div> <!-- ./row -->

                    <div class="row g-2">
                        <div class="col-md-2 mb-2">

                            <!-- lista de versiones -->
                            <div class="form-floating" id="bloque-version-lista">
                                <select id="version-ls" class="form-select" required name="version">
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
                                    <option value="nuevo" <?= $vehiculo['condicion'] === 'nuevo' ? 'selected' : '' ?>>Nuevo
                                    </option>
                                    <option value="seminuevo" <?= $vehiculo['condicion'] === 'seminuevo' ? 'selected' : '' ?>>Seminuevo</option>
                                </select>
                                <label for="condicion">Condición <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <select name="combustible" id="combustible" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="1" <?= $vehiculo['idcombustible'] == 1 ? 'selected' : '' ?>>Gasolina
                                    </option>
                                    <option value="2" <?= $vehiculo['idcombustible'] == 2 ? 'selected' : '' ?>>Diésel
                                    </option>
                                    <option value="3" <?= $vehiculo['idcombustible'] == 3 ? 'selected' : '' ?>>GLP</option>
                                    <option value="4" <?= $vehiculo['idcombustible'] == 4 ? 'selected' : '' ?>>GNV</option>
                                    <option value="5" <?= $vehiculo['idcombustible'] == 5 ? 'selected' : '' ?>>Dual:
                                        Gasolina, GLP</option>
                                </select>
                                <label for="combustible">Tipo de combustible <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="idmodelo" id="idmodelo">
                        <div class="col-md-2 mb-2">
                            <div class="form-floating">
                                <input type="text" id="color" name="color" class="form-control"
                                    value="<?= $vehiculo['color'] ?>" placeholder="Color">
                                <label for="color">Color</label>
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="form-floating">
                                <input type="text" id="precio" name="precio" class="form-control text-end"
                                    value="<?= $vehiculo['precioventa'] ?>" pattern="[0-9]+"
                                    title="Solo se permiten números" placeholder="Precio" required>
                                <label for="precio">Precio <span class="text-danger">*</span></label>
                            </div>
                        </div>

                    </div>
                    <div class="row g-2">
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="text" name="chasis" class="form-control text-center"
                                    value="<?= $vehiculo['chasis'] ?>" placeholder="Chasis">
                                <label for="chasis">Chasis</label>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="text" name="placa" class="form-control text-center"
                                    value="<?= $vehiculo['placa'] ?>" placeholder="Placa">
                                <label for="placa">Placa</label>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="text" name="placarotativa" class="form-control text-center"
                                    value="<?= $vehiculo['placarotativa'] ?>" placeholder="Placa Rotativa">
                                <label for="placarotativa">Placa Rotativa</label>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="text" name="seriemotor" class="form-control text-center"
                                    value="<?= $vehiculo['seriemotor'] ?>" placeholder="Serie Motor">
                                <label for="seriemotor">Serie Motor</label>
                            </div>
                        </div>
                    </div>
                </div> <!-- ./card-body -->

                <div class="card-footer text-end">
                    <button type="reset" class="btn btn-sm btn-outline-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Registrar</button>
                </div>
            </div> <!-- ./card -->
        </form>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const vehiculo = <?= json_encode($vehiculo, JSON_UNESCAPED_UNICODE) ?>;
        const modeloDetalle = <?= json_encode($modeloDetalle, JSON_UNESCAPED_UNICODE) ?>;
        const hiddenModeloId = document.getElementById('idmodelo');

        // Cargar las Marcas > Tipos > Modelos > Años
        const marcasSel = document.getElementById("marcas");
        const tiposSel = document.getElementById("tipos");
        const modelosSel = document.getElementById("modelos");
        const aniosSel = document.getElementById("anios");

        let modelosCache = [];

        //Carga de marcas
        async function cargarMarcas() {
            try {
                const res = await fetch("/marcas/lista", { headers: { "Accept": "application/json" } });
                if (!res.ok) throw new Error(res.status);
                const { success, marcas } = await res.json();
                if (!success) throw new Error("Error API marcas");
                marcasSel.innerHTML = `<option value="">Seleccione Marca</option>`;
                marcas.forEach(m => {
                    marcasSel.insertAdjacentHTML("beforeend",
                        `<option value="${m.idmarca}">${m.marca}</option>`);
                });
            } catch (err) {
                console.error(err);
                marcasSel.innerHTML = `<option value="">No se pudieron cargar marcas</option>`;
            }
        }

        //marca -> cargar tipos
        marcasSel.addEventListener("change", async () => {
            const idm = marcasSel.value;
            tiposSel.innerHTML = `<option value="">Cargando Tipo</option>`;
            modelosCache = [];

            if (!idm) {
                tiposSel.innerHTML = `<option value="">Seleccione Marca primero</option>`;
                return;
            }

            try {
                const res = await fetch(`/tipovehiculos/lista?idmarca=${idm}`, { headers: { "Accept": "application/json" } });
                if (!res.ok) throw new Error(res.status);
                const { success, tipos } = await res.json();
                if (!success) throw new Error("Error API tipos");
                tiposSel.innerHTML = `<option value="">Seleccione Tipo</option>`;
                tipos.forEach(t => {
                    tiposSel.insertAdjacentHTML("beforeend",
                        `<option value="${t.idtipovehiculo}">${t.tipovehiculo}</option>`);
                });
            } catch (err) {
                console.error(err);
                tiposSel.innerHTML = `<option value="">No se pudieron cargar tipos</option>`;
            }
        });

        //tipo -> cargar modelos
        tiposSel.addEventListener("change", async () => {
            const idm = marcasSel.value;
            const idt = tiposSel.value;
            modelosSel.innerHTML = `<option value="">Cargando Modelos</option>`;
            modelosCache = [];
            if (!idm || !idt) {
                modelosSel.innerHTML = `<option value="">Seleccione marca y tipo primero</option>`;
                return;
            }

            try {
                const res = await fetch(`/modelos/lista?marca=${idm}&tipo=${idt}`, { headers: { "Accept": "application/json" } });
                if (!res.ok) throw new Error(res.status);
                const { success, modelos } = await res.json();
                if (!success) throw new Error("Error API modelos");
                modelosCache = modelos;
                // extraer nombres únicos
                const nombres = [...new Set(modelos.map(m => m.modelo))].sort();
                modelosSel.innerHTML = `<option value="">Seleccione Modelo</option>`;
                nombres.forEach(n => {
                    modelosSel.insertAdjacentHTML("beforeend",
                        `<option value="${n}">${n}</option>`);
                });
            } catch (err) {
                console.error(err);
                modelosSel.innerHTML = `<option value="">No se pudieron cargar modelos</option>`;
            }
        });

        //modelo -> cargar años
        modelosSel.addEventListener("change", () => {
            const selModelo = modelosSel.value;
            aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;

            if (!selModelo) {
                hiddenModeloId.value = '';
                return;
            }

            // extraer años únicos para ese nombre
            const años = modelosCache
                .filter(m => m.modelo === selModelo)
                .map(m => m.anio)
                .sort((a, b) => a - b)
                .filter((v, i, a) => a.indexOf(v) === i);

            años.forEach(year => {
                aniosSel.insertAdjacentHTML("beforeend",
                    `<option value="${year}">${year}</option>`);
            });
            hiddenModeloId.value = '';
        });

        //Al cambiar año, buscar el objeto completo y setear su ID
        aniosSel.addEventListener("change", () => {
            const selModelo = modelosSel.value;
            const selAnio = aniosSel.value;
            if (!selModelo || !selAnio) {
                hiddenModeloId.value = '';
                return;
            }
            const encontrado = modelosCache.find(m =>
                m.modelo === selModelo && String(m.anio) === selAnio
            );
            hiddenModeloId.value = encontrado
                ? encontrado.idmodelo
                : '';
        });

        cargarMarcas();

    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>