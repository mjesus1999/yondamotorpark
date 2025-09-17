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
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/vehiculos" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Lista
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
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/vehiculos">[ Volver ]</a>
            </div>
        </div>
    </div> -->

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

        let modelosCache = []; // [{ idmodelo, modelo, anio, ... }, ...]

        const setOptions = (sel, text = "Seleccione") => sel.innerHTML = `<option value="">${text}</option>`;
        const addOption = (sel, value, label, selectIt = false) => {
            const opt = document.createElement('option');
            opt.value = value ?? '';
            opt.textContent = label ?? value;
            sel.appendChild(opt);
            if (selectIt) sel.value = value;
        };

        function escapeHtml(s) {
            return String(s ?? '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
        }

        //VERSION
        const versionLs = document.getElementById('version-ls');
        const versionIn = document.getElementById('version-in');
        const bloqueVersionLista = document.getElementById('bloque-version-lista');
        const bloqueVersionInput = document.getElementById('bloque-version-input');
        const mostrarVersionLsBtn = document.getElementById('mostrar-version-ls');

        function mostrarInputVersion(valor = '') {
            bloqueVersionLista.classList.add('d-none');
            bloqueVersionInput.classList.remove('d-none');
            versionIn.value = valor;
            versionIn.setAttribute('name', 'version');
            versionIn.required = true;
            versionLs.removeAttribute('name');
            versionLs.required = false;
            versionLs.disabled = true;
        }

        function mostrarListaVersion() {
            bloqueVersionInput.classList.add('d-none');
            bloqueVersionLista.classList.remove('d-none');
            versionIn.removeAttribute('name');
            versionIn.required = false;
            versionLs.setAttribute('name', 'version');
            versionLs.required = true;
            versionLs.disabled = false;
        }

        function preseleccionarVersion(valorVersion) {
            if (!valorVersion) return;
            let encontrado = false;
            if (versionLs) {
                for (let i = 0; i < versionLs.options.length; i++) {
                    const opt = versionLs.options[i];
                    if (opt.value === valorVersion || opt.text === valorVersion) { versionLs.value = opt.value; encontrado = true; break; }
                }
            }
            if (!encontrado) mostrarInputVersion(valorVersion);
            else mostrarListaVersion();
        }

        if (versionLs) {
            versionLs.addEventListener('change', () => {
                if (versionLs.value === 'ESP') { mostrarInputVersion(''); versionIn.focus(); }
                else { if (versionIn) versionIn.value = versionLs.value; mostrarListaVersion(); }
            });
        }
        if (mostrarVersionLsBtn) {
            mostrarVersionLsBtn.addEventListener('click', () => { mostrarListaVersion(); versionLs.value = ''; versionLs.focus(); });
        }

        //CARGAS (MARCAS -> TIPO MODELO -> MODELO -> AÑO)
        async function cargarMarcas() {
            setOptions(marcasSel, "Cargando marcas...");
            try {
                const res = await fetch("/api/marcas", { headers: { Accept: "application/json" }, credentials: "same-origin" });
                if (!res.ok) throw new Error("HTTP " + res.status);
                const data = await res.json();
                if (!Array.isArray(data) || data.length === 0) { setOptions(marcasSel, "No hay marcas"); return []; }
                setOptions(marcasSel, "Seleccione Marca");
                data.forEach(m => addOption(marcasSel, m.idmarca, m.marca));
                if (modeloDetalle?.idmarca) marcasSel.value = modeloDetalle.idmarca;
                return data;
            } catch (err) {
                console.error("Error cargarMarcas:", err);
                setOptions(marcasSel, "Error cargando marcas");
                return [];
            }
        }

        async function cargarTiposPorMarca(idmarca) {
            setOptions(tiposSel, "Cargando tipos...");
            if (!idmarca) { setOptions(tiposSel, "Seleccione marca primero"); return []; }
            try {
                const url = `/api/getTipoVehiculoByMarca/${encodeURIComponent(idmarca)}`;
                const res = await fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin" });

                if (res.status === 404) {
                    // no hay tipos en backend: si modeloDetalle trae un tipo, añadirlo
                    setOptions(tiposSel, "No hay tipos");
                    if (modeloDetalle?.idtipovehiculo) {
                        addOption(tiposSel, modeloDetalle.idtipovehiculo, modeloDetalle.tipovehiculo ?? `Tipo #${modeloDetalle.idtipovehiculo}`, true);
                    }
                    return [];
                }
                if (!res.ok) throw new Error("HTTP " + res.status);

                const data = await res.json();
                const tipos = Array.isArray(data) ? data : (Array.isArray(data.tipos) ? data.tipos : (Array.isArray(data.data) ? data.data : []));

                if (!tipos || tipos.length === 0) {
                    setOptions(tiposSel, "No hay tipos");
                    if (modeloDetalle?.idtipovehiculo) {
                        addOption(tiposSel, modeloDetalle.idtipovehiculo, modeloDetalle.tipovehiculo ?? `Tipo #${modeloDetalle.idtipovehiculo}`, true);
                    }
                    return [];
                }

                setOptions(tiposSel, "Seleccione Tipo");
                tipos.forEach(t => addOption(tiposSel, t.idtipovehiculo ?? t.id ?? '', t.tipovehiculo ?? t.nombre ?? t.name ?? t.id));
                // preseleccionar si coincide con modeloDetalle
                if (modeloDetalle?.idtipovehiculo && String(modeloDetalle.idmarca) === String(idmarca)) {
                    tiposSel.value = modeloDetalle.idtipovehiculo;
                }
                return tipos;
            } catch (err) {
                console.error("Error cargarTiposPorMarca:", err);
                setOptions(tiposSel, "Error cargando tipos");
                // si tenemos dato en detalle, insertarlo para evitar vacío
                if (modeloDetalle?.idtipovehiculo) addOption(tiposSel, modeloDetalle.idtipovehiculo, modeloDetalle.tipovehiculo ?? `Tipo #${modeloDetalle.idtipovehiculo}`, true);
                return [];
            }
        }

        async function cargarModelosPorMarcaTipo(idmarca, idtipo) {
            setOptions(modelosSel, "Cargando modelos...");
            setOptions(aniosSel, "Seleccione Año");
            modelosCache = [];
            if (!idmarca || !idtipo) { setOptions(modelosSel, "Seleccione marca y tipo"); return []; }
            try {
                const url = `/api/getModeloByTipoMarca/${encodeURIComponent(idmarca)}/${encodeURIComponent(idtipo)}`;
                const res = await fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin" });
                if (res.status === 404) {
                    setOptions(modelosSel, "No hay modelos");
                    // si detalle tiene modelo, lo añadimos para que no quede vacío
                    if (modeloDetalle?.modelo) {
                        addOption(modelosSel, modeloDetalle.modelo, modeloDetalle.modelo, true);
                        modelosCache = [{ idmodelo: modeloDetalle.idmodelo ?? null, modelo: modeloDetalle.modelo, anio: modeloDetalle.anio ?? null }];
                    }
                    return [];
                }
                if (!res.ok) throw new Error("HTTP " + res.status);

                const data = await res.json();
                const modelos = Array.isArray(data) ? data : (Array.isArray(data.modelos) ? data.modelos : (Array.isArray(data.data) ? data.data : []));

                if (!modelos || modelos.length === 0) {
                    setOptions(modelosSel, "No hay modelos");
                    if (modeloDetalle?.modelo) {
                        addOption(modelosSel, modeloDetalle.modelo, modeloDetalle.modelo, true);
                        modelosCache = [{ idmodelo: modeloDetalle.idmodelo ?? null, modelo: modeloDetalle.modelo, anio: modeloDetalle.anio ?? null }];
                    }
                    return [];
                }

                modelosCache = modelos;
                // extraer nombres únicos
                const nombres = [...new Set(modelos.map(m => m.modelo))].sort();
                modelosSel.innerHTML = `<option value="">Seleccione Modelo</option>`;
                nombres.forEach(n => addOption(modelosSel, escapeHtml(n), escapeHtml(n)));

                // preselección por nombre si viene en detalle
                if (modeloDetalle?.modelo && String(modeloDetalle.idmarca) === String(idmarca) && String(modeloDetalle.idtipovehiculo) === String(idtipo)) {
                    modelosSel.value = modeloDetalle.modelo;
                    modelosSel.dispatchEvent(new Event('change'));
                }

                return modelos;
            } catch (err) {
                console.error("Error cargarModelosPorMarcaTipo:", err);
                setOptions(modelosSel, "Error cargando modelos");
                // fallback: insertar detalle si existe
                if (modeloDetalle?.modelo) {
                    addOption(modelosSel, modeloDetalle.modelo, modeloDetalle.modelo, true);
                    modelosCache = [{ idmodelo: modeloDetalle.idmodelo ?? null, modelo: modeloDetalle.modelo, anio: modeloDetalle.anio ?? null }];
                }
                return [];
            }
        }

        //EVENTOS (CHANGE)
        marcasSel.addEventListener("change", async () => {
            const idm = String(marcasSel.value || '').trim();
            tiposSel.innerHTML = '<option value="">Cargando tipos...</option>';
            modelosSel.innerHTML = '<option value="">Seleccione Modelo</option>';
            aniosSel.innerHTML = '<option value="">Seleccione Año</option>';
            modelosCache = [];
            hiddenModeloId.value = '';

            if (!idm) { tiposSel.innerHTML = '<option value="">Seleccione marca primero</option>'; return; }

            await cargarTiposPorMarca(idm);
            // si se cargaron tipos y ya existe modeloDetalle correspondiente, forzar carga de modelos
            if (tiposSel.value) tiposSel.dispatchEvent(new Event('change'));
        });

        tiposSel.addEventListener("change", async () => {
            const idm = marcasSel.value;
            const idt = tiposSel.value;
            setOptions(modelosSel, "Cargando modelos...");
            setOptions(aniosSel, "Seleccione Año");
            modelosCache = [];
            hiddenModeloId.value = '';

            if (!idm || !idt) { setOptions(modelosSel, "Seleccione marca y tipo"); return; }
            await cargarModelosPorMarcaTipo(idm, idt);
        });

        modelosSel.addEventListener("change", () => {
            const selModelo = modelosSel.value;
            setOptions(aniosSel, "Seleccione Año");
            hiddenModeloId.value = '';

            if (!selModelo || modelosCache.length === 0) return;

            // obtener años (filtrando por nombre de modelo)
            const años = modelosCache.filter(m => String(m.modelo) === String(selModelo)).map(m => m.anio);
            const añosUnicos = [...new Set(años)].filter(Boolean).sort((a, b) => Number(a) - Number(b));

            if (añosUnicos.length === 0) {
                if (modeloDetalle?.anio) { aniosSel.innerHTML = `<option value="${modeloDetalle.anio}">${modeloDetalle.anio}</option>`; aniosSel.value = modeloDetalle.anio; aniosSel.dispatchEvent(new Event('change')); }
                else { setOptions(aniosSel, "No hay años"); }
                return;
            }

            aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
            añosUnicos.forEach(y => aniosSel.insertAdjacentHTML("beforeend", `<option value="${y}">${y}</option>`));

            if (modeloDetalle?.anio && String(modeloDetalle.modelo) === String(selModelo)) {
                aniosSel.value = modeloDetalle.anio;
                aniosSel.dispatchEvent(new Event('change'));
            }
        });

        aniosSel.addEventListener("change", () => {
            const selModelo = modelosSel.value;
            const selAnio = aniosSel.value;
            if (!selModelo || !selAnio) { hiddenModeloId.value = ''; return; }
            const encontrado = modelosCache.find(m => String(m.modelo) === String(selModelo) && String(m.anio) === String(selAnio));
            // si no hay en cache, intentar usar modeloDetalle.idmodelo
            hiddenModeloId.value = encontrado ? encontrado.idmodelo : (modeloDetalle?.idmodelo ?? '');
        });

        //INICIALIZACIÓN ENCADENADA
        (async function init() {
            await cargarMarcas();

            // si no hay detalle, preseleccionar versión desde vehiculo (si existe)
            if (!modeloDetalle) {
                preseleccionarVersion(vehiculo?.version ?? '');
                return;
            }

            // 1) cargar tipos para la marca del detalle
            if (modeloDetalle.idmarca) {
                await cargarTiposPorMarca(modeloDetalle.idmarca);
            }

            // 2) cargar modelos para la marca+tipo del detalle
            if (modeloDetalle.idmarca && modeloDetalle.idtipovehiculo) {
                await cargarModelosPorMarcaTipo(modeloDetalle.idmarca, modeloDetalle.idtipovehiculo);
            }

            // 3) forzar selección de modelo + año si faltó por alguna razón
            if (modeloDetalle.modelo) {
                modelosSel.value = modeloDetalle.modelo;
                modelosSel.dispatchEvent(new Event('change'));
            }
            if (modeloDetalle.anio) {
                aniosSel.value = modeloDetalle.anio;
                aniosSel.dispatchEvent(new Event('change'));
            }

            // 4) asegurar hidden idmodelo si lo trae detalle
            if (modeloDetalle.idmodelo) hiddenModeloId.value = modeloDetalle.idmodelo;

            // 5) preseleccionar versión (preferir vehiculo.version)
            const valorVersion = (vehiculo && vehiculo.version) ? vehiculo.version : (modeloDetalle.version ?? '');
            preseleccionarVersion(valorVersion);

        })();

    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>