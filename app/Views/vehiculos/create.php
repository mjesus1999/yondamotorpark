<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizaciones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizaciones/listar" class="">[ Mostrar Lista ]</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form id="formCotizacion" action="/cotizaciones" method="POST">
            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 1:</strong> <span class="fst-italic">
                        Información del cliente (opcional)
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="idcliente" name="idcliente" class="form-select" required>
                                    <option value="">Seleccionar cliente</option>
                                    <?php foreach ($clientes as $cli): ?>
                                        <option value="<?= $cli['idcliente'] ?>"
                                            data-doc="<?= htmlspecialchars($cli['nrodoc']) ?>"
                                            data-tel="<?= htmlspecialchars($cli['telprimario']) ?>">
                                            <?= htmlspecialchars($cli['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idcliente">Cliente</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="documento" name="documento" readonly>
                                <label for="documento">Documento</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="telefono" name="telefono" readonly>
                                <label for="telefono">Teléfono</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Vehículo -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 2:</strong> <span class="fst-italic">
                        Selección de vehículo
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select id="idmarca" name="idmarca" class="form-select" required>
                                    <option value="">Seleccionar marca</option>
                                    <?php foreach ($marcas as $m): ?>
                                        <option value="<?= $m['idmarca'] ?>">
                                            <?= htmlspecialchars($m['marca']) ?> (<?= $m['modelos'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idmarca">Marca</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select id="idtipovehiculo" name="idtipovehiculo" class="form-select" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php foreach ($tipovehiculos as $tv): ?>
                                        <option value="<?= $tv['idtipovehiculo'] ?>">
                                            <?= htmlspecialchars($tv['tipovehiculo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idtipovehiculo">Tipo de Vehículo</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="idmodelo" name="idmodelo" required>
                                    <option value="">Seleccionar modelo</option>
                                    <!-- Se carga dinámicamente -->
                                </select>
                                <label for="idmodelo">Modelo</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="anio" name="anio" required>
                                    <option value="">Seleccionar año</option>
                                    <!-- Se carga dinámicamente -->
                                </select>
                                <label for="anio">Año</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select" id="idvehiculo" name="idvehiculo" required>
                                    <option value="">Seleccionar vehículo</option>
                                    <!-- Se carga dinámicamente basado en filtros anteriores -->
                                </select>
                                <label for="idvehiculo">Vehículo Disponible</label>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Vehículo Seleccionado -->
                    <div id="infoVehiculo" class="mt-3" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="version" name="version" readonly>
                                    <label for="version">Versión</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="condicion" name="condicion" readonly>
                                    <label for="condicion">Condición</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="combustible" name="combustible"
                                        readonly>
                                    <label for="combustible">Combustible</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="color" name="color" readonly>
                                    <label for="color">Color</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Condiciones de Cotización -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 3:</strong> <span class="fst-italic">
                        Realizar la cotizacion
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="moneda" name="moneda" required>
                                    <option value="">Seleccionar moneda</option>
                                    <option value="PEN">Soles (PEN)</option>
                                    <option value="USD">Dólares (USD)</option>
                                </select>
                                <label for="moneda">Moneda</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="precioventa" name="precioventa"
                                    step="0.01" required>
                                <label for="precioventa">Precio de Venta</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="vigenciadias" name="vigenciadias" value=""
                                    min="1" max="90" required>
                                <label for="vigenciadias">Vigencia (días)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="inicial" name="inicial" step="0.01"
                                    min="0" required>
                                <label for="inicial">Cuota Inicial</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="numcuotas" name="numcuotas" min="1"
                                    max="72" required>
                                <label for="numcuotas">Número de Cuotas</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="valorcuota" name="valorcuota" step="0.01"
                                    readonly>
                                <label for="valorcuota">Valor de Cuota</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formato de Cotización -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 4:</strong> <span class="fst-italic">
                        Formato de cotizacion
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="idformato" name="idformato" required>
                                    <option value="">Seleccionar formato</option>
                                    <?php foreach ($formatos as $f): ?>
                                        <option value="<?= $f['idformato'] ?>">
                                            <?= htmlspecialchars($f['cotización']) ?>
                                            (<?= $f['fechainicio'] ?> -
                                            <?= $f['fechafin'] ?? '∞' ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idformato">Formato de Cotización</label>
                            </div>
                        </div>

                        <!-- Colaborador de Venta -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="idcolventa" name="idcolventa" required>
                                    <option value="">Seleccionar colaborador</option>
                                    <?php foreach ($colaboradores as $c): ?>
                                        <option value="<?= $c['idcolaborador'] ?>">
                                            <?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="idcolventa">Colaborador de Venta</label>
                            </div>
                        </div>

                        <!-- Estado de la Cotización -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="estadocotizacion" name="estadocotizacion" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="observado">Observado</option>
                                </select>
                                <label for="estadocotizacion">Estado de Cotización</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-footer text-end">
                    <button type="reset" id="btn-cancelar-registro"
                        class="btn btn-sm btn-outline-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm btnGuardarCotizacion">Registrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const clienteSelect = document.getElementById('idcliente');
        const documentoInput = document.getElementById('documento');
        const telefonoInput = document.getElementById('telefono');

        clienteSelect.addEventListener('change', () => {
            const option = clienteSelect.selectedOptions[0];
            if (!option || !option.value) {
                documentoInput.value = '';
                telefonoInput.value = '';
                return;
            }
            //se llena los campos al ser seleccionado un cliente
            documentoInput.value = option.dataset.doc || '';
            telefonoInput.value = option.dataset.tel || '';
        });

        const marcaSel = document.getElementById('idmarca');
        const tipoSel = document.getElementById('idtipovehiculo');
        const modeloSel = document.getElementById('idmodelo');
        const anioSel = document.getElementById('anio');

        let modelosData = [];

        // Función para poblar modelo y limpiar año
        function actualizarModelos() {
            const idmarca = marcaSel.value;
            const idtipo = tipoSel.value;
            modeloSel.innerHTML = '<option value="">Seleccionar modelo</option>';
            anioSel.innerHTML = '<option value="">Seleccionar año</option>';

            if (!idmarca || !idtipo) return;

            fetch(`/modelos/lista?marca=${idmarca}&tipo=${idtipo}`)
                .then(res => res.ok ? res.json() : Promise.reject(res.status))
                .then(json => {
                    if (!json.success) return;
                    modelosData = json.modelos;
                    // Extraer nombres de modelos únicos
                    const nombres = Array.from(new Set(modelosData.map(m => m.modelo)));
                    nombres.forEach(nombre => {
                        const opt = document.createElement('option');
                        opt.value = nombre;
                        opt.textContent = nombre;
                        modeloSel.append(opt);
                    });
                })
                .catch(err => console.error('Error al cargar modelos:', err));
        }

        // Cuando cambian marca o tipo
        marcaSel.addEventListener('change', actualizarModelos);
        tipoSel.addEventListener('change', actualizarModelos);

        // Al seleccionar un modelo, poblar años
        modeloSel.addEventListener('change', () => {
            const nombreSel = modeloSel.value;
            anioSel.innerHTML = '<option value="">Seleccionar año</option>';
            if (!nombreSel) return;
            // Filtrar datos para ese modelo
            const años = modelosData
                .filter(m => m.modelo === nombreSel)
                .map(m => m.anio);
            // Únicos y ordenados
            Array.from(new Set(años)).sort().forEach(year => {
                const opt = document.createElement('option');
                opt.value = year;
                opt.textContent = year;
                anioSel.append(opt);
            });
        });
        const vehSel = document.getElementById('idvehiculo');

        // Función para poblar vehículos disponibles
        function cargarDisponibles() {
            const idmarca = marcaSel.value;
            const idtipo = tipoSel.value;
            const modelo = modeloSel.value;
            const anio = anioSel.value;

            vehSel.innerHTML = '<option value="">Seleccionar vehículo</option>';
            if (!idmarca || !idtipo || !modelo || !anio) return;

            fetch(`/vehiculos/disponibles?marca=${idmarca}&tipo=${idtipo}` +
                `&modelo=${encodeURIComponent(modelo)}&anio=${anio}`)
                .then(res => res.ok ? res.json() : Promise.reject(res.status))
                .then(json => {
                    if (!json.success) return;
                    json.vehiculos.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.idvehiculo;
                        opt.textContent = `${v.version} | ${v.color} | ${v.placa}`;
                        vehSel.append(opt);
                    });
                })
                .catch(err => console.error('Error al cargar vehículos:', err));
        }

        // Dispara carga cuando cambien también modelo o año
        modeloSel.addEventListener('change', cargarDisponibles);
        anioSel.addEventListener('change', cargarDisponibles);

    });
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>