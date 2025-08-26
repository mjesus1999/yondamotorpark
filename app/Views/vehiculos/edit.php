<?php include __DIR__ . '/../layout/header.php'; ?>

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
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/vehiculos">[ Volver ]</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="/vehiculos/update" method="POST" id="edit-registro-vehiculos">
            <input type="hidden" name="idvehiculo" value="<?= htmlspecialchars($vehiculo['idvehiculo']) ?>">
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
                                <input type="number" id="precio" step="0.01" min="0" inputmode="decimal" name="precio"
                                    class="form-control text-end"
                                    value="<?= number_format((float) ($vehiculo['precioventa'] ?? 0), 2, '.', '') ?>"
                                    pattern="[0-9]+" title="Solo se permiten números" placeholder="Precio" required>
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
                    <button type="submit" class="btn btn-sm btn-primary">Guardar cambios</button>
                </div>
            </div> <!-- ./card -->
        </form>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const vehiculo = <?= json_encode($vehiculo, JSON_UNESCAPED_UNICODE) ?>;
        const modeloDetalle = <?= json_encode($modeloDetalle ?? null, JSON_UNESCAPED_UNICODE) ?>;
        const hiddenModeloId = document.getElementById('idmodelo');

        const marcasSel = document.getElementById("marcas");
        const tiposSel = document.getElementById("tipos");
        const modelosSel = document.getElementById("modelos");
        const aniosSel = document.getElementById("anios");

        let modelosCache = [];

        // cargar marcas (devuelve array de marcas)
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
                return marcas;
            } catch (err) {
                console.error(err);
                marcasSel.innerHTML = `<option value="">No se pudieron cargar marcas</option>`;
                return [];
            }
        }

        // cargar tipos por marca (devuelve array de tipos)
        async function cargarTiposPorMarca(idmarca) {
            try {
                const res = await fetch(`/tipovehiculos/lista?idmarca=${idmarca}`, { headers: { "Accept": "application/json" } });
                if (!res.ok) throw new Error(res.status);
                const { success, tipos } = await res.json();
                if (!success) throw new Error("Error API tipos");
                tiposSel.innerHTML = `<option value="">Seleccione Tipo</option>`;
                tipos.forEach(t => {
                    tiposSel.insertAdjacentHTML("beforeend",
                        `<option value="${t.idtipovehiculo}">${t.tipovehiculo}</option>`);
                });
                return tipos;
            } catch (err) {
                console.error(err);
                tiposSel.innerHTML = `<option value="">No se pudieron cargar tipos</option>`;
                return [];
            }
        }

        // cargar modelos por marca+tipo (devuelve modelos)
        async function cargarModelosPorMarcaTipo(idmarca, idtipo) {
            try {
                const res = await fetch(`/modelos/lista?marca=${idmarca}&tipo=${idtipo}`, { headers: { "Accept": "application/json" } });
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
                return modelos;
            } catch (err) {
                console.error(err);
                modelosSel.innerHTML = `<option value="">No se pudieron cargar modelos</option>`;
                modelosCache = [];
                return [];
            }
        }

        // evento: cuando cambia modelo, rellenar años
        modelosSel.addEventListener("change", () => {
            const selModelo = modelosSel.value;
            aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
            if (!selModelo) {
                hiddenModeloId.value = '';
                return;
            }
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

        // al cambiar año, asignar idmodelo correcto
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
            hiddenModeloId.value = encontrado ? encontrado.idmodelo : '';
        });

        // Manejo de cambio de marca -> cargar tipos
        marcasSel.addEventListener("change", async () => {
            const idm = marcasSel.value;
            tiposSel.innerHTML = `<option value="">Cargando Tipo</option>`;
            modelosSel.innerHTML = `<option value="">Seleccione Modelo</option>`;
            aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
            modelosCache = [];
            if (!idm) {
                tiposSel.innerHTML = `<option value="">Seleccione Marca primero</option>`;
                return;
            }
            await cargarTiposPorMarca(idm);
        });

        // Manejo de cambio de tipo -> cargar modelos
        tiposSel.addEventListener("change", async () => {
            const idm = marcasSel.value;
            const idt = tiposSel.value;
            modelosSel.innerHTML = `<option value="">Cargando Modelos</option>`;
            aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
            modelosCache = [];
            if (!idm || !idt) {
                modelosSel.innerHTML = `<option value="">Seleccione marca y tipo primero</option>`;
                return;
            }
            await cargarModelosPorMarcaTipo(idm, idt);
        });

        // Inicialización encadenada y pre-selección cuando hay modeloDetalle
        (async function init() {
            await cargarMarcas();

            if (modeloDetalle && modeloDetalle.idmodelo) {
                // 1) seleccionar marca
                if (modeloDetalle.idmarca) {
                    marcasSel.value = modeloDetalle.idmarca;
                    // cargar tipos para esa marca
                    await cargarTiposPorMarca(modeloDetalle.idmarca);
                }

                // 2) seleccionar tipo
                if (modeloDetalle.idtipovehiculo) {
                    tiposSel.value = modeloDetalle.idtipovehiculo;
                    // cargar modelos para marca+tipo
                    await cargarModelosPorMarcaTipo(modeloDetalle.idmarca, modeloDetalle.idtipovehiculo);
                }

                // 3) seleccionar modelo (por nombre)
                if (modeloDetalle.modelo) {
                    modelosSel.value = modeloDetalle.modelo;
                    // poblar años correspondientes al modelo y seleccionar el año del detalle
                    aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
                    const años = modelosCache
                        .filter(m => m.modelo === modeloDetalle.modelo)
                        .map(m => m.anio)
                        .sort((a, b) => a - b)
                        .filter((v, i, a) => a.indexOf(v) === i);

                    años.forEach(year => {
                        aniosSel.insertAdjacentHTML("beforeend",
                            `<option value="${year}">${year}</option>`);
                    });

                    if (modeloDetalle.anio) {
                        aniosSel.value = modeloDetalle.anio;
                    }
                }

                // 4) setear idmodelo oculto (ya lo tenemos)
                hiddenModeloId.value = modeloDetalle.idmodelo;
            }

            // --- Manejo de la versión (preselección y toggle lista/input)
            const versionLs = document.getElementById('version-ls');
            const versionInput = document.getElementById('version-in');
            const bloqueVersionLista = document.getElementById('bloque-version-lista');
            const bloqueVersionInput = document.getElementById('bloque-version-input');
            const mostrarVersionLsBtn = document.getElementById('mostrar-version-ls');
            
            // Funciones para alternar correctamente (evitan required/duplicados)
            function mostrarInputVersion(valor = '') {
                bloqueVersionLista.classList.add('d-none');
                bloqueVersionInput.classList.remove('d-none');

                // habilitar input y deshabilitar select para evitar validación doble
                versionInput.value = valor;
                versionInput.setAttribute('name', 'version');
                versionInput.required = true;

                versionLs.disabled = true;
                versionLs.required = false;
            }

            function mostrarListaVersion() {
                bloqueVersionInput.classList.add('d-none');
                bloqueVersionLista.classList.remove('d-none');

                // deshabilitar el name/required del input y reactivar el select
                versionInput.removeAttribute('name');
                versionInput.required = false;

                versionLs.disabled = false;
                versionLs.required = true;
            }

            // Cuando el select selecciona "Especificar..." mostrar input
            if (versionLs) {
                versionLs.addEventListener('change', () => {
                    if (versionLs.value === 'ESP') {
                        mostrarInputVersion('');
                        versionInput.focus();
                    } else {
                        mostrarListaVersion();
                    }
                });
            }

            // Botón para volver a la lista desde el input libre
            if (mostrarVersionLsBtn) {
                mostrarVersionLsBtn.addEventListener('click', () => {
                    mostrarListaVersion();
                    versionLs.value = '';
                    versionLs.focus();
                });
            }

            // Función para inicializar y preseleccionar la versión del vehículo
            function preseleccionarVersion(valorVersion) {
                if (!valorVersion) return;
                let encontrado = false;
                for (let i = 0; i < versionLs.options.length; i++) {
                    const opt = versionLs.options[i];
                    if (opt.value === valorVersion || opt.text === valorVersion) {
                        versionLs.value = opt.value;
                        encontrado = true;
                        break;
                    }
                }
                if (!encontrado) {
                    // si no está en la lista, mostrar input libre con el valor
                    mostrarInputVersion(valorVersion);
                } else {
                    // asegurar que la lista esté visible y el input inactivo
                    mostrarListaVersion();
                }
            }

            // Llama a preseleccionarVersion desde tu init() (después de asignar vehiculo/modeloDetalle)
            // agregar dentro de init(), al final despues de hiddenModeloId.value = modeloDetalle.idmodelo;
            preseleccionarVersion(vehiculo.version ?? modeloDetalle?.version ?? '');

        })();

    });
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>