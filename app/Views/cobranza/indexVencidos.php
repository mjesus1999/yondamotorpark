<!-- app/Views/cobranza/indexVencidos.php -->

<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

<!-- Includes del mapa viewer -->
<?php include __DIR__ . '/../components/mapa-viewer-includes.php'; ?>
<?php include __DIR__ . '/../components/mapa-viewer-modal.php'; ?>

<style>
    .btn-mapa-mini {
        padding: 2px 6px;
        font-size: 0.75rem;
        line-height: 1;
        border-radius: 3px;
    }

    .direccion-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .direccion-text {
        flex: 1;
        min-width: 0;
        word-break: break-word;
    }
</style>

<div class="container-fluid">
    <!-- CABECERA -->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">Área de Cobranza</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Vencidos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <button id="btn-exportar-excel" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
                </button>
                <a href="#" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar</a>
                <a href="/Cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-none d-md-flex justify-content-end">
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="1">
                <i class="bi bi-funnel me-1"></i>1 vencida (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="2">
                <i class="bi bi-funnel me-1"></i>2 vencidas (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="3">
                <i class="bi bi-funnel me-1"></i>3 vencidas (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="4+">
                <i class="bi bi-funnel me-1"></i>4 a + vencidas (0)
            </button>
            <button class="btn btn-warning btn-sm btn-filtro-tramo active" data-tramo="todos">
                <i class="bi bi-list-ul me-1"></i>Todos
            </button>
        </div>

        <div class="card-body">
            <!-- BÚSQUEDA Y CONTROLES (escritorio) -->
            <div class="table-responsive d-none d-md-block">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="busqueda-global" class="form-control" placeholder="Buscar ....">
                    </div>
                </div>
            </div>

            <!-- TABULATOR (escritorio) -->
            <div id="tabla-vencidos-tabulator" class="table-responsive d-none d-md-block">
                <div class="text-center py-5" id="spinner-vencidos">
                    <div class="spinner-border text-primary" role="status"><span
                            class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2">Cargando contratos vencidos...</p>
                </div>
            </div>

            <!-- ACORDEÓN (móvil) -->
            <div id="acordeonVencidos" class="d-block d-md-none">
                <!-- Se llenará dinámicamente -->
            </div>

            <!-- Mensaje vacío -->
            <div id="mensaje-vacio" class="alert alert-warning d-none mt-3">No hay contratos vencidos.</div>

            <!-- Total -->
            <div class="mt-3 text-end fw-bold">
                <i class="fas fa-calculator me-2"></i>Total Saldos Vencidos: <span id="total-deuda" class="text-danger">S/.
                    0.00</span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<script src="/assets/js/mapa-viewer.js"></script>

<script>
    let tablaGlobal = null;
    let tramoActivo = 'todos';
    let datosClientesCompletos = [];

    //Carga de Datos con cuotas vencidas del JSON 
    function cargarVencidos() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getVencidos', { cache: 'no-store' })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        if (response.ok && data.success) {
                            const fechaDatos = data.fecha_datos || '';
                            if (fechaDatos) {
                                
                                mostrarFechaDatos(fechaDatos);
                            }

                            // Guardar datos completos globalmente para el mapa
                            datosClientesCompletos = data.data || [];
                            resolve(datosClientesCompletos);
                        } else {
                            reject(new Error(data.message || 'Error al cargar datos'));
                        }
                    } catch (err) {
                        console.error('Respuesta no JSON:', text);
                        reject(new Error('Respuesta inválida del servidor'));
                    }
                })
                .catch(error => reject(error));
        });
    }

    function obtenerDatosCliente(idcontrato) {
        return datosClientesCompletos.find(cliente =>
            parseInt(cliente.idcontrato) === parseInt(idcontrato)
        );
    }

    function abrirMapaCliente(idcontrato) {
        const cliente = obtenerDatosCliente(idcontrato);

        if (!cliente) {
            showToast('No se encontraron datos del cliente', 'WARNING');
            return;
        }

        // Preparar datos para el mapa con coordenadas si existen
        const datosUbicacion = {
            nombre: cliente.cliente || 'Cliente',
            direccion: cliente.ubicacion_cliente || 'Sin dirección',
            lat: cliente.latitud ? parseFloat(cliente.latitud) : null,
            lng: cliente.longitud ? parseFloat(cliente.longitud) : null
        };

        // Intentar abrir el mapa (se geocodificará si no hay coordenadas)
        if (typeof window.abrirMapaViewer === 'function') {
            window.abrirMapaViewer(datosUbicacion);
        } else {
            console.error('MapaViewer no está disponible');
            showToast('Error al abrir el mapa', 'ERROR');
        }
    }

    function actualizarDatos() {
        const btnActualizar = document.querySelector('.btn-outline-success');
        if (btnActualizar) {
            btnActualizar.disabled = true;
            btnActualizar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...';
        }

        fetch('/Cobranza/actualizarVencidos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Datos actualizados correctamente', 'SUCCESS');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Error al actualizar', 'ERROR');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Error al actualizar los datos', 'ERROR');
            })
            .finally(() => {
                if (btnActualizar) {
                    btnActualizar.disabled = false;
                    btnActualizar.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Actualizar';
                }
            });
    }

    function mostrarFechaDatos(fecha) {
        const alertInfo = document.querySelector('.alert-info .row');
        if (alertInfo) {
            const fechaUTC = new Date(fecha + ' UTC');
            const fechaPeru = new Date(fechaUTC.getTime() - (5 * 60 * 60 * 1000));

            const dia = String(fechaPeru.getUTCDate()).padStart(2, '0');
            const mes = String(fechaPeru.getUTCMonth() + 1).padStart(2, '0');
            const anio = fechaPeru.getUTCFullYear();
            const horas = String(fechaPeru.getUTCHours()).padStart(2, '0');
            const minutos = String(fechaPeru.getUTCMinutes()).padStart(2, '0');
            const segundos = String(fechaPeru.getUTCSeconds()).padStart(2, '0');

            const fechaFormateada = `${dia}/${mes}/${anio} ${horas}:${minutos}:${segundos}`;

            const badge = document.createElement('span');
            badge.className = 'badge bg-info ms-2';
            badge.textContent = `Datos de: ${fechaFormateada}`;
            alertInfo.querySelector('.col-md-6')?.appendChild(badge);
        }
    }

    function verificarActualizacionAutomatica() {
        fetch('/Cobranza/verificarActualizacion')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.necesita_actualizacion) {
                    console.log('Datos desactualizados, actualizando...');
                    actualizarDatos();
                }
            })
            .catch(err => console.error('Error verificando actualización:', err));
    }

    function filtrarPorTramo(tramo) {
        if (!tablaGlobal) return;

        tramoActivo = tramo;

        document.querySelectorAll('.btn-filtro-tramo').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-primary');
        });

        const btnActivo = document.querySelector(`[data-tramo="${tramo}"]`);
        if (btnActivo) {
            btnActivo.classList.add('active');
            btnActivo.classList.remove('btn-outline-primary');
            btnActivo.classList.add('btn-warning');
        }

        aplicarFiltrosCombinados();
        actualizarContadores();
    }

    function aplicarFiltrosCombinados() {
        if (!tablaGlobal) return;

        const searchInput = document.getElementById("busqueda-global");
        const searchValue = searchInput ? searchInput.value.trim() : '';

        let filtros = [];

        if (searchValue !== '') {
            filtros.push([
                { field: "cliente", type: "like", value: searchValue },
                { field: "telefono", type: "like", value: searchValue },
                { field: "vehiculo", type: "like", value: searchValue },
                { field: "ubicacion_cliente", type: "like", value: searchValue },
                { field: "provincia_cliente", type: "like", value: searchValue },
                { field: "distrito_cliente", type: "like", value: searchValue }
            ]);
        }

        if (tramoActivo !== 'todos') {
            if (tramoActivo === '1') {
                filtros.push({ field: "cuotas_vencidas", type: "=", value: 1 });
            } else if (tramoActivo === '2') {
                filtros.push({ field: "cuotas_vencidas", type: "=", value: 2 });
            } else if (tramoActivo === '3') {
                filtros.push({ field: "cuotas_vencidas", type: "=", value: 3 });
            } else if (tramoActivo === '4+') {
                filtros.push({ field: "cuotas_vencidas", type: ">=", value: 4 });
            }
        }

        if (filtros.length === 0) {
            tablaGlobal.clearFilter();
        } else {
            tablaGlobal.setFilter(filtros);
        }
    }

    function actualizarContadores() {
        if (!tablaGlobal) return;

        const datosCompletos = tablaGlobal.getData();

        const count1 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 1).length;
        const count2 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 2).length;
        const count3 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 3).length;
        const count4 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) >= 4).length;

        const btn1 = document.querySelector('[data-tramo="1"]');
        const btn2 = document.querySelector('[data-tramo="2"]');
        const btn3 = document.querySelector('[data-tramo="3"]');
        const btn4 = document.querySelector('[data-tramo="4+"]');

        if (btn1) btn1.innerHTML = `<i class="bi bi-funnel me-1"></i>1 vencida (${count1})`;
        if (btn2) btn2.innerHTML = `<i class="bi bi-funnel me-1"></i>2 vencidas (${count2})`;
        if (btn3) btn3.innerHTML = `<i class="bi bi-funnel me-1"></i>3 vencidas (${count3})`;
        if (btn4) btn4.innerHTML = `<i class="bi bi-funnel me-1"></i>4 a + vencidas (${count4})`;
    }

    function formatMoneda(valor) {
        const n = parseFloat(valor) || 0;
        return 'S/. ' + n.toFixed(2);
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function obtenerDetalleVencidas(idContrato) {
        return new Promise((resolve, reject) => {
            fetch(`/Cobranza/getDetalleVencidas/${idContrato}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        resolve(data.data);
                    } else {
                        reject(new Error('No se pudieron cargar los detalles'));
                    }
                })
                .catch(err => reject(err));
        });
    }

    function mostrarPopoverVencidas(element, detalles) {
        document.querySelectorAll('.popover-vencidas').forEach(p => p.remove());

        if (!detalles || detalles.length === 0) return;

        const popover = document.createElement('div');
        popover.className = 'popover-vencidas';
        popover.style.cssText = `
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            max-width: 280px;
            font-size: 13px;
        `;

        let html = '<div style="font-weight: 600; margin-bottom: 8px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 6px;">Cuotas Vencidas</div>';

        detalles.forEach((cuota, index) => {
            const badgeColor = index === 0 ? '#dc3545' : '#6c757d';
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f5f5f5;">
                    <span style="color: #666;">
                        <span style="background: ${badgeColor}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-right: 6px;">
                            Cuota ${cuota.numcuota}
                        </span>
                        ${cuota.fecha_formateada}
                    </span>
                </div>
            `;
        });

        popover.innerHTML = html;
        document.body.appendChild(popover);

        const rect = element.getBoundingClientRect();
        popover.style.top = `${rect.bottom + window.scrollY + 5}px`;
        popover.style.left = `${rect.left + window.scrollX}px`;

        setTimeout(() => {
            document.addEventListener('click', function closePopover(e) {
                if (!popover.contains(e.target) && e.target !== element) {
                    popover.remove();
                    document.removeEventListener('click', closePopover);
                }
            });
        }, 100);
    }

    async function obtenerFechasVencidas(idContrato) {
        try {
            const response = await fetch(`/Cobranza/getDetalleVencidas/${idContrato}`);
            const data = await response.json();
            if (data.success && data.data) {
                return data.data.map(cuota =>
                    `Cuota ${cuota.numcuota}: ${cuota.fecha_formateada}`
                ).join(' | ');
            }
            return '';
        } catch (error) {
            console.error('Error al obtener fechas:', error);
            return '';
        }
    }

    async function exportarAExcel() {
        if (!tablaGlobal) {
            showToast('No hay datos para exportar', 'WARNING');
            return;
        }

        const btnExportar = document.getElementById('btn-exportar-excel');
        if (btnExportar) {
            btnExportar.disabled = true;
            btnExportar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Exportando...';
        }

        try {
            const datos = tablaGlobal.getData("active");

            if (!datos || datos.length === 0) {
                showToast('No hay datos para exportar', 'WARNING');
                return;
            }

            const datosConFechas = await Promise.all(
                datos.map(async (row) => {
                    const fechasVencidas = await obtenerFechasVencidas(row.idcontrato);
                    return { ...row, fechas_vencidas: fechasVencidas };
                })
            );

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Vencidos');

            worksheet.columns = [
                { header: '#', key: 'numero', width: 6 },
                { header: 'Cliente', key: 'cliente', width: 60 },
                { header: 'Documento', key: 'documento', width: 12 },
                { header: 'Teléfono', key: 'telefono', width: 12 },
                { header: 'Ubicación', key: 'ubicacion_cliente', width: 60 },
                { header: 'Vehículo', key: 'vehiculo', width: 50 },
                { header: 'Tienda', key: 'tienda', width: 12 },
                { header: 'C. Totales', key: 'cuotas_totales', width: 10 },
                { header: 'Monto Cuota', key: 'monto_primera_vencida', width: 15 },
                { header: 'Deuda', key: 'deuda_vencida', width: 15 },
                { header: 'C. Vencidas', key: 'cuotas_vencidas', width: 10 },
                { header: 'C. Pagadas', key: 'cuotas_pagadas', width: 10 },
                { header: 'Días Atraso', key: 'dias_atraso', width: 10 },
                { header: 'Fechas de Cuotas Vencidas', key: 'fechas_vencidas', width: 80 }
            ];

            worksheet.getRow(1).font = { bold: true };
            worksheet.getRow(1).fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FFD9D9D9' }
            };
            worksheet.getRow(1).alignment = { vertical: 'middle', horizontal: 'center' };

            datosConFechas.forEach((row, index) => {
                worksheet.addRow({
                    numero: index + 1,
                    cliente: row.cliente || '',
                    documento: row.documento || '',
                    telefono: row.telefono || '',
                    ubicacion_cliente: row.ubicacion_cliente || '',
                    vehiculo: row.vehiculo || '',
                    tienda: row.tienda || '',
                    cuotas_totales: row.cuotas_totales || 0,
                    monto_primera_vencida: parseFloat(row.monto_primera_vencida) || 0,
                    deuda_vencida: parseFloat(row.deuda_vencida) || 0,
                    cuotas_vencidas: parseInt(row.cuotas_vencidas) || 0,
                    cuotas_pagadas: parseInt(row.cuotas_pagadas) || 0,
                    dias_atraso: parseInt(row.dias_atraso) || 0,
                    fechas_vencidas: row.fechas_vencidas || ''
                });
            });

            worksheet.getColumn('monto_primera_vencida').numFmt = '"S/. "#,##0.00';
            worksheet.getColumn('deuda_vencida').numFmt = '"S/. "#,##0.00';

            worksheet.getColumn('cuotas_totales').alignment = { horizontal: 'center' };
            worksheet.getColumn('cuotas_vencidas').alignment = { horizontal: 'center' };
            worksheet.getColumn('cuotas_pagadas').alignment = { horizontal: 'center' };
            worksheet.getColumn('dias_atraso').alignment = { horizontal: 'center' };

            worksheet.getColumn('fechas_vencidas').alignment = { wrapText: true, vertical: 'top' };

            const totalDeuda = datosConFechas.reduce((sum, row) => sum + (parseFloat(row.deuda_vencida) || 0), 0);
            const totalRow = worksheet.addRow({
                numero: '',
                cliente: '',
                documento: '',
                telefono: '',
                ubicacion_cliente: '',
                vehiculo: '',
                tienda: '',
                cuotas_totales: '',
                monto_primera_vencida: 'TOTAL',
                deuda_vencida: totalDeuda,
                cuotas_vencidas: '',
                cuotas_pagadas: '',
                dias_atraso: '',
                fechas_vencidas: ''
            });

            totalRow.font = { bold: true };
            totalRow.getCell('deuda_vencida').numFmt = '"S/. "#,##0.00';

            workbook.xlsx.writeBuffer().then((buffer) => {
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;

                const fecha = new Date().toISOString().split('T')[0];
                /* a.download = `Vencidos_${fecha}.xlsx`; */

                let NombreArchivoFiltrado = 'Vencidos';

                if (tramoActivo !== 'todos') {
                    if (tramoActivo === '1') {
                        NombreArchivoFiltrado += '_1Cuota';
                    } else if (tramoActivo === '2') {
                        NombreArchivoFiltrado += '_2Cuotas';
                    } else if (tramoActivo === '3') {
                        NombreArchivoFiltrado += '_3Cuotas';
                    } else if (tramoActivo === '4+') {
                        NombreArchivoFiltrado += '_4oMasCuotas';
                    }
                }

                const searchInput = document.getElementById("busqueda-global");
                const searchValue = searchInput ? searchInput.value.trim() : '';
                if (searchValue !== '') {
                    NombreArchivoFiltrado = '_Busqueda_Filtrado';
                }

                NombreArchivoFiltrado += `_${fecha}.xlsx`;
                a.download = NombreArchivoFiltrado;

                a.click();
                window.URL.revokeObjectURL(url);

                showToast(`Excel exportado: ${datosConFechas.length} registros`, 'SUCCESS');
            });

        } catch (error) {
            console.error('Error al exportar:', error);
            showToast('Error al generar el archivo Excel', 'ERROR');
        } finally {
            if (btnExportar) {
                btnExportar.disabled = false;
                btnExportar.innerHTML = '<i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel';
            }
        }
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', async () => {
        // Inicializar MapaViewer cuando se cargue la página
        if (typeof initMapaViewer === 'function') {
            await initMapaViewer();
        }

        verificarActualizacionAutomatica();

        const btnExportarExcel = document.getElementById('btn-exportar-excel');
        if (btnExportarExcel) {
            btnExportarExcel.addEventListener('click', (e) => {
                e.preventDefault();
                exportarAExcel();
            });
        }

        const btnActualizar = document.querySelector('.btn-outline-success');
        if (btnActualizar) {
            btnActualizar.addEventListener('click', (e) => {
                e.preventDefault();
                actualizarDatos();
            });
        }

        document.querySelectorAll('.btn-filtro-tramo').forEach(btn => {
            btn.addEventListener('click', function () {
                const tramo = this.dataset.tramo;
                filtrarPorTramo(tramo);
            });
        });

        const contTabla = document.getElementById('tabla-vencidos-tabulator');
        const spinner = document.getElementById('spinner-vencidos');
        const mensajeVacio = document.getElementById('mensaje-vacio');
        const totalDeudaEl = document.getElementById('total-deuda');
        const acordeon = document.getElementById('acordeonVencidos');

        try {
            const datos = await cargarVencidos();

            if (spinner) spinner.remove();

            if (!Array.isArray(datos) || datos.length === 0) {
                mensajeVacio.classList.remove('d-none');
                totalDeudaEl.textContent = formatMoneda(0);
                return;
            }

            const totalDeuda = datos.reduce((s, r) => s + (parseFloat(r.deuda_vencida) || 0), 0);
            totalDeudaEl.textContent = formatMoneda(totalDeuda);

            tablaGlobal = new Tabulator("#tabla-vencidos-tabulator", {
                data: datos,
                layout: "fitDataFill",
                pagination: "local",
                paginationSize: 15,
                paginationSizeSelector: [10, 15, 25, 50],
                movableRows: false,
                reactiveData: false,
                placeholder: "No se encontraron resultados",
                columns: [
                    { title: "#", formatter: "rownum", width: 45, hozAlign: "center" },
                    { title: "Cliente", field: "cliente", width: 280, tooltip: true },
                    {
                        title: "Dirección",
                        field: "ubicacion_cliente",
                        width: 400,
                        formatter: function (cell) {
                            const direccion = cell.getValue();
                            const rowData = cell.getRow().getData();
                            const idcontrato = rowData.idcontrato;

                            return `
                                <div class="direccion-cell">
                                    <span class="direccion-text">${direccion}</span>
                                    <button class="btn btn-info btn-mapa-mini" 
                                            data-contrato="${idcontrato}"
                                            title="Ver en mapa">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },
                    { title: "Teléfono", field: "telefono", width: 100, tooltip: true, hozAlign: "center" },
                    { title: "N° Doc", field: "documento", width: 90, tooltip: true, hozAlign: "center" },
                    { title: "Vehículo", field: "vehiculo", width: 300, tooltip: true },
                    { title: "Tienda", field: "tienda", width: 100, tooltip: true, hozAlign: "center" },
                    { title: "Cuotas T.", field: "cuotas_totales", with: 100, hozAlign: "center", tooltip: true },
                    { title: "Monto Cuota", field: "monto_primera_vencida", width: 140, hozAlign: "right", tooltip: true, formatter: function (cell) { return formatMoneda(cell.getValue()); } },
                    { title: "Deuda", field: "deuda_vencida", width: 120, hozAlign: "right", tooltip: true, formatter: function (cell) { const v = parseFloat(cell.getValue()) || 0; return `<span class="${v > 0 ? 'text-danger fw-bold' : ''}">${formatMoneda(v)}</span>`; } },
                    {
                        title: "C. Venc.", field: "cuotas_vencidas", width: 100, hozAlign: "center", tooltip: true,
                        formatter: function (cell) {
                            const val = cell.getValue();
                            let badge = 'badge bg-secondary';
                            if (val >= 4) badge = 'badge bg-danger';
                            else if (val === 3) badge = 'badge bg-warning';
                            else if (val === 2) badge = 'badge bg-info';
                            else if (val === 1) badge = 'badge bg-primary';
                            return `<span class="${badge}">${val}</span>`;
                        }
                    },
                    {
                        title: "Estado de pagos",
                        field: "estado_pagos",
                        width: 150,
                        hozAlign: "center",
                        formatter: function (cell) {
                            const value = cell.getValue();
                            const rowData = cell.getRow().getData();

                            return `<span class="estado-pagos-clickable" 
                                        data-contrato="${rowData.idcontrato}" 
                                        style="cursor: pointer; color: #0d6efd; text-decoration: underline; text-decoration-style: dotted;">
                                        ${value}
                                    </span>`;
                        }
                    },
                    { title: "Cuota P.", field: "cuotas_pagadas", width: 100, hozAlign: "center", tooltip: true },
                    {
                        title: "Reporte",
                        hozAlign: "center",
                        headerSort: false,
                        width: 150,
                        formatter: function (cell) {
                            const data = cell.getRow().getData();
                            return `
                                <button class="btn btn-sm btn-danger btn-pdf-atrasado" data-contrato="${data.idcontrato}" title="Notificar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-pdf-recojo ms-1" data-contrato="${data.idcontrato}" title="Recojo PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            `;
                        }
                    }
                ],

                locale: true,
                langs: {
                    "es-es": {
                        "pagination": {
                            "first": "<<",
                            "last": ">>",
                            "prev": "<",
                            "next": ">",
                        }
                    }
                },
            });

            setTimeout(() => {
                actualizarContadores();
            }, 100);

            const btnTodos = document.querySelector('[data-tramo="todos"]');
            if (btnTodos) {
                btnTodos.classList.add('active');
                btnTodos.classList.remove('btn-primary');
                btnTodos.classList.add('btn-warning');
            }

            const searchInput = document.getElementById("busqueda-global");
            if (searchInput) {
                searchInput.addEventListener("keyup", function (e) {
                    aplicarFiltrosCombinados();
                });
            }

            // Event delegation para la tabla
            contTabla.addEventListener('click', async function (e) {
                // 1. Botón de mapa
                const mapaBtn = e.target.closest('.btn-mapa-mini');
                if (mapaBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const contrato = mapaBtn.dataset.contrato;
                    abrirMapaCliente(contrato);
                    return;
                }

                // 2. Estado de pagos
                const estadoPagos = e.target.closest('.estado-pagos-clickable');
                if (estadoPagos) {
                    const contrato = estadoPagos.dataset.contrato;
                    estadoPagos.style.opacity = '0.5';

                    try {
                        const detalles = await obtenerDetalleVencidas(contrato);
                        mostrarPopoverVencidas(estadoPagos, detalles);
                    } catch (err) {
                        console.error('Error al cargar detalles:', err);
                        showToast('No se pudo cargar el detalle', 'WARNING');
                    } finally {
                        estadoPagos.style.opacity = '1';
                    }
                    return;
                }

                // 3. Botones PDF
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');

                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    window.open(`/reportesAtrasado/${contrato}`, '_blank');
                    return;
                }

                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    window.open(`/reportesRecojo/${contrato}`, '_blank');
                    return;
                }
            });

            // Construir acordeón para móvil
            const gruposHtml = [];
            let contador = 1;
            datos.forEach(row => {
                const id = `vencido-${row.idcontrato}`;

                let badgeColor = 'secondary';
                const cv = parseInt(row.cuotas_vencidas);
                if (cv >= 4) badgeColor = 'danger';
                else if (cv === 3) badgeColor = 'warning';
                else if (cv === 2) badgeColor = 'info';
                else if (cv === 1) badgeColor = 'primary';

                const item = `
                    <div class="accordion-item mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-${id}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${id}" aria-expanded="false" aria-controls="collapse-${id}">
                                <span><i class="bi bi-person-circle me-2 text-primary"></i> ${escapeHtml(row.cliente)} 
                                <span class="badge bg-${badgeColor} ms-2">${cv} venc.</span></span>
                            </button>
                        </h2>
                        <div id="collapse-${id}" class="accordion-collapse collapse" aria-labelledby="heading-${id}" data-bs-parent="#acordeonVencidos">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>#:</strong> ${contador++}</li>
                                    <li class="list-group-item">
                                        <strong>Ubicación:</strong> ${escapeHtml(row.ubicacion_cliente)}
                                        <button class="btn btn-info btn-sm float-end btn-mapa-mini-mobile" 
                                                data-contrato="${row.idcontrato}"
                                                title="Ver en mapa">
                                            <i class="bi bi-geo-alt-fill"></i> Ver Mapa
                                        </button>
                                    </li>
                                    <li class="list-group-item"><strong>Provincia:</strong> ${escapeHtml(row.provincia_cliente)}</li>
                                    <li class="list-group-item"><strong>Distrito:</strong> ${escapeHtml(row.distrito_cliente)}</li>
                                    <li class="list-group-item"><strong>Teléfono:</strong> ${escapeHtml(row.telefono)}</li>
                                    <li class="list-group-item"><strong>N° Documento:</strong> ${escapeHtml(row.documento)}</li>
                                    <li class="list-group-item"><strong>Vehículo:</strong> ${escapeHtml(row.vehiculo)}</li>
                                    <li class="list-group-item"><strong>Tienda:</strong> <span class="badge bg-primary">${escapeHtml(row.tienda)}</span></li>
                                    <li class="list-group-item"><strong>Cuotas Totales:</strong> ${escapeHtml(row.cuotas_totales)}</li>
                                    <li class="list-group-item"><strong>Cuotas Vencidas:</strong> <span class="badge bg-${badgeColor}">${cv}</span></li>
                                    <li class="list-group-item"><strong>Monto Cuota:</strong> ${formatMoneda(row.monto_primera_vencida)}</li>
                                    <li class="list-group-item"><strong>Deuda:</strong> <span class="${(parseFloat(row.deuda_vencida) || 0) > 0 ? 'text-danger fw-bold' : ''}">${formatMoneda(row.deuda_vencida)}</span></li>
                                    <li class="list-group-item">
                                        <strong>Estado pagos:</strong> 
                                        <span class="estado-pagos-clickable" 
                                            data-contrato="${row.idcontrato}" 
                                            style="cursor: pointer; color: #0d6efd; text-decoration: underline; text-decoration-style: dotted;">
                                            ${escapeHtml(row.estado_pagos)}
                                        </span>
                                    </li>
                                    <li class="list-group-item"><strong>Cuotas Pagadas:</strong> ${escapeHtml(row.cuotas_pagadas)}</li>
                                    <li class="list-group-item d-flex gap-2">
                                        <button class="btn btn-sm btn-danger w-100 btn-pdf-atrasado" data-contrato="${row.idcontrato}">Notificar PDF</button>
                                        <button class="btn btn-sm btn-danger w-100 btn-pdf-recojo" data-contrato="${row.idcontrato}">Recojo PDF</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                gruposHtml.push(item);
            });
            acordeon.innerHTML = `<div class="accordion" id="acordeonVencidos">${gruposHtml.join('')}</div>`;

            // Event delegation para acordeón móvil
            acordeon.addEventListener('click', async function (e) {
                // 1. Botón de mapa móvil
                const mapaBtnMobile = e.target.closest('.btn-mapa-mini-mobile');
                if (mapaBtnMobile) {
                    e.preventDefault();
                    e.stopPropagation();
                    const contrato = mapaBtnMobile.dataset.contrato;
                    abrirMapaCliente(contrato);
                    return;
                }

                // 2. Estado de pagos
                const estadoPagos = e.target.closest('.estado-pagos-clickable');
                if (estadoPagos) {
                    const contrato = estadoPagos.dataset.contrato;
                    estadoPagos.style.opacity = '0.5';

                    try {
                        const detalles = await obtenerDetalleVencidas(contrato);
                        mostrarPopoverVencidas(estadoPagos, detalles);
                    } catch (err) {
                        console.error('Error al cargar detalles:', err);
                        showToast('No se pudo cargar el detalle', 'WARNING');
                    } finally {
                        estadoPagos.style.opacity = '1';
                    }
                    return;
                }

                // 3. Botones PDF
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');

                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    window.open(`/reportesAtrasado/${contrato}`, '_blank');
                    return;
                }

                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    window.open(`/reportesRecojo/${contrato}`, '_blank');
                    return;
                }
            });

        } catch (err) {
            console.error('Error al cargar vencidos:', err);
            if (spinner) spinner.remove();
            mensajeVacio.classList.remove('d-none');
            showToast('Error al cargar los contratos vencidos', 'ERROR');
        }
    });
</script>