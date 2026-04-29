<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .monto-input {
        width: 120px;
        text-align: right;
        font-weight: bold;
    }
</style>

<div class="container-fluid p-4 ">

    <div class="alert alert-info mt-2" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pagos por conceptos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/caja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>

            </div>
        </div>
    </div>

</div>

<div class="row mt-3">

    <div class="col-lg-8">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Selección y Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="conceptos-db" class="form-label">Añadir Concepto Sugerido</label>
                        <div class="input-group">
                            <input
                                id="conceptos-db"
                                class="form-control"
                                list="conceptos-db-list"
                                placeholder="Escribe para buscar (ej: GPS, carta, duplicado...)"
                                autocomplete="off"
                            >
                            <datalist id="conceptos-db-list"></datalist>
                            <button class="btn btn-primary" id="btn-add-db" disabled>Añadir</button>
                        </div>
                        <div class="form-text">
                            Puedes escribir para buscar en el catálogo. Si necesitas un concepto totalmente libre y con monto variable, usa el bloque “Ingreso de Concepto Personalizado”.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="input-dni" class="form-label">Buscar cliente (DNI o RUC)</label>
                        <div class="input-group">
                            <input type="text" id="input-dni" class="form-control" placeholder="8 dígitos (DNI) o 11 (RUC)" maxlength="11" inputmode="numeric" autocomplete="off">
                            <button class="btn btn-info" id="btn-dni" title="Buscar Cliente">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Ingreso de Concepto Personalizado</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="concepto-manual" class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="concepto-manual" placeholder="Ej: Multa de Tránsito">
                    </div>

                    <div class="col-md-2">
                        <label for="monto-manual" class="form-label">Monto (S/)</label>
                        <div class="input-group">
                            <input type="number" class="form-control text-end" id="monto-manual" min="0" value="0.00">

                        </div>
                    </div>

                    <div class="col-md-5">
                        <label for="descripcion-concepto" class="form-label">Descripción</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="descripcion-concepto" placeholder="Ingrese alguna descripción">
                            <button class="btn btn-success" id="btn-add-manual">Añadir</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Conceptos a Incluir en la Boleta</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-end">Monto (S/)</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody id="conceptos-body">
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info text-center m-3" id="empty-message" role="alert">
                    Aún no se han añadido conceptos a la boleta.
                </div>
            </div>
        </div>

        <div class="card border-primary shadow-lg mt-4" id="card-cronograma" style="display: none;">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-check me-2" style="font-size: 1.2rem;"></i> Cronograma de pagos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Cuota</th>
                                <th>Fecha</th>
                                <th class="text-end">Monto (Cuota + GPS)</th>
                                <th style="width: 28%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-cronograma"></tbody>
                    </table>
                </div>
                <div class="small text-muted p-3" id="cronograma-footnote">
                    Mostrando cronograma del contrato activo más reciente del cliente.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-primary shadow-lg sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">S/ Resumen Total</h5>
            </div>
            <div class="card-body">

                <div class="col-md-12">
                    <label for="mediopago" class="mb-1">Medio de pago</label>
                    <select class="form-select mb-2" id="mediopago" name="mediopago">
                        <option value="">Seleccione medio de pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Yape">Yape</option>
                        <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                        <option value="Plin">Plin</option>
                    </select>
                </div>

                <div class="col-md-12" id="contenedor-cuenta" style="display:none;">
                    <label for="cuenta" class="mb-1">Cuenta Bancaria</label>
                    <select class="form-select mb-2" id="cuenta" name="cuenta">
                        <option value="" disabled selected>Seleccione una cuenta de pago</option>

                    </select>

                </div>

                <div class="col-md-12 mb-3" style="display: none;" id="contenedor-comprobante">
                    <label class="form-label small text-muted">Comprobante de Pago</label>
                    <input id="comprobante" class="form-control" type="file" name="comprobante" accept="image/*,.pdf">
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal" class="fw-bold">S/ 0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3 fw-bold">
                    <h5 class="fw-bold">Total a Pagar:</h5>
                    <h5 id="total-pagar" class="fw-bold">S/ 0.00</h5>
                </div>



                <button class="btn btn-warning w-100 btn-lg" id="btn-generar-boleta" disabled>
                    Generar Boleta y Pagar
                </button>
            </div>
        </div>



        <div class="card border-primary shadow-lg mt-4" id="card-cliente" style="display: none;">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2" style="font-size: 1.2rem;"></i> Cliente Seleccionado</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-normal text-muted">ID Cliente:</span>
                    <span id="cliente-id" class="fw-bold text-danger fs-5"></span>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-normal text-muted">Nombre completo:</span>
                    <span id="nombrecompleto" class="fw-bold"></span>
                </div>

                <hr class="my-3">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold d-flex align-items-center">
                        <i class="bi bi-person-vcard-fill text-primary me-2" style="font-size: 1.2rem;"></i> DNI:
                    </span>
                    <span id="cliente-dni" class="text-muted fw-semibold"></span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold d-flex align-items-center">
                        <i class="bi bi-envelope-at-fill text-success me-2" style="font-size: 1.2rem;"></i> Correo:
                    </span>
                    <span id="cliente-correo" class="text-muted fw-semibold"></span>
                </div>

                <div class="d-flex align-items-start justify-content-between mb-2">
                    <span class="fw-bold d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill text-danger me-2" style="font-size: 1.2rem;"></i> Dirección:
                    </span>
                    <span id="cliente-direccion" class="text-muted text-end fw-semibold"></span>
                </div>

            </div>
        </div>

        <div class="card border-primary shadow-lg mt-4" id="card-registro-ventas" style="display: none;">
            <div class="card-header bg-secondary text-white d-flex align-items-center">
                <h5 class="mb-0"><i class="bi bi-truck-front-fill me-2" style="font-size: 1.2rem;"></i> Registro ventas vehiculares</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="tbody-registro-ventas"></tbody>
                    </table>
                </div>
                <div class="small text-muted p-3">
                    Si este DNI no existe como <strong>cliente</strong> en el sistema, igual podés ver aquí los datos del Excel.
                    Para <strong>cobrar</strong> (generar boleta), necesitás que el cliente exista y tenga <strong>ID cliente</strong>.
                </div>
            </div>
        </div>

    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080" aria-live="polite" aria-atomic="true">
    <div id="caja-toast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex w-100">
            <div class="toast-body flex-grow-1" id="caja-toast-body"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>
<?php
$idVariosCaja = isset($idConceptoVarios) ? (int) $idConceptoVarios : 0;
?>
<div id="caja-config" class="d-none" data-id-concepto-varios="<?= $idVariosCaja ?>"></div>

<script>
    function showToast(message, type = 'INFO', duration = 3600) {
        const el = document.getElementById('caja-toast');
        const body = document.getElementById('caja-toast-body');
        if (!el || !body) {
            console.log(`[${type}] ${message}`);
            return;
        }
        const classMap = {
            SUCCESS: 'text-white bg-success',
            ERROR: 'text-white bg-danger',
            WARNING: 'text-dark bg-warning',
            INFO: 'text-white bg-info'
        };
        el.className = 'toast align-items-center border-0 ' + (classMap[type] || classMap.INFO);
        body.textContent = message;
        const t = bootstrap.Toast.getOrCreateInstance(el, { autohide: true, delay: Math.max(2000, duration) });
        t.show();
    }

    /** Respaldo: enlace clicable (útil si el bloqueo de pop-ups impidió abrir about:blank). */
    function showToastWithPdfLink(message, url, type = 'INFO', duration = 10000) {
        const el = document.getElementById('caja-toast');
        const body = document.getElementById('caja-toast-body');
        if (!el || !body) return;
        const classMap = {
            SUCCESS: 'text-white bg-success',
            ERROR: 'text-white bg-danger',
            WARNING: 'text-dark bg-warning',
            INFO: 'text-white bg-info'
        };
        el.className = 'toast align-items-center border-0 ' + (classMap[type] || classMap.INFO);
        body.replaceChildren();
        body.appendChild(document.createTextNode(message + ' '));
        const a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        a.textContent = 'Abrir comprobante (PDF)';
        a.className =
            type === 'SUCCESS' ? 'text-white fw-bold text-decoration-underline' :
            (type === 'WARNING' ? 'text-dark fw-bold text-decoration-underline' : 'text-white fw-bold text-decoration-underline');
        body.appendChild(a);
        const t = bootstrap.Toast.getOrCreateInstance(el, { autohide: true, delay: Math.max(4000, duration) });
        t.show();
    }

    const ID_CONCEPTO_VARIOS = (function () {
        const n = parseInt(document.getElementById('caja-config')?.dataset.idConceptoVarios || '0', 10);
        return n > 0 ? n : 0;
    })();


    const clienteNombre = document.getElementById('nombrecompleto');
    const clienteIDSpan = document.getElementById('cliente-id');
    const clienteDNI = document.getElementById('cliente-dni');
    const clienteCorreo = document.getElementById('cliente-correo');
    const clienteDireccion = document.getElementById('cliente-direccion');
    const clienteCard = document.getElementById('card-cliente');
    const registroVentasCard = document.getElementById('card-registro-ventas');
    const registroVentasTbody = document.getElementById('tbody-registro-ventas');
    const cronogramaCard = document.getElementById('card-cronograma');
    const cronogramaTbody = document.getElementById('tbody-cronograma');
    const cronogramaFootnote = document.getElementById('cronograma-footnote');

    const selectMedioPago = document.getElementById('mediopago');
    const contenedorComprobante = document.getElementById('contenedor-comprobante');
    const inputComprobante = document.getElementById('comprobante'); // Input File
    const contenedorCuenta = document.getElementById('contenedor-cuenta');
    const selectCuenta = document.getElementById('cuenta'); // Select de cuenta bancaria

    const conceptosBody = document.getElementById('conceptos-body');
    const subtotalSpan = document.getElementById('subtotal');
    const totalPagarSpan = document.getElementById('total-pagar');
    const btnGenerar = document.getElementById('btn-generar-boleta');
    const conceptosDB = document.getElementById('conceptos-db'); // input (datalist)
    const conceptosDBList = document.getElementById('conceptos-db-list');
    const btnAddDB = document.getElementById('btn-add-db');
    const conceptoManual = document.getElementById('concepto-manual');
    const montoManual = document.getElementById('monto-manual');
    const descripcionManual = document.getElementById('descripcion-concepto');
    const btnAddManual = document.getElementById('btn-add-manual');
    const emptyMessage = document.getElementById('empty-message');
    const btnDNI = document.getElementById('btn-dni');
    const inputDNI = document.getElementById('input-dni');

    let idCliente = null;
    let conceptosCatalogo = [];
    let conceptosCatalogoByKey = new Map();

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = String(s ?? '');
        return d.innerHTML;
    }

    function fmt(v) {
        if (v === null || v === undefined || v === '') return '—';
        return String(v);
    }

    function parseIsoDate(d) {
        if (!d) return null;
        // d viene como YYYY-MM-DD desde MySQL
        const dt = new Date(String(d) + 'T00:00:00');
        return isNaN(dt.getTime()) ? null : dt;
    }

    function daysInMonth(year, monthIndex0) {
        return new Date(year, monthIndex0 + 1, 0).getDate();
    }

    /**
     * Calcula el monto de cuota a pagar HOY basado en:
     * - fecha_inicio_credito: define el día de pago del mes
     * - cuota_base: monto normal
     * - tolerancia: 3 días
     * - mora: % (usa tasa_interes si está, sino 10)
     */
    function calcularMontoCuotaHoy(r) {
        const start = parseIsoDate(r?.fecha_inicio_credito);
        const cuota = parseFloat(r?.cuota_base ?? r?.pago_de_cuota ?? '0') || 0;
        const moraPct = parseFloat(r?.tasa_interes ?? '10') || 10;
        const totalConMoraFromDb = parseFloat(r?.total_con_mora ?? r?.cuota_total_mensual ?? '0') || 0;

        if (!start || cuota <= 0) {
            return {
                ok: false,
                monto: cuota,
                aplicaMora: false,
                dueDate: null,
                daysLate: null,
                moraPct,
                montoConMora: totalConMoraFromDb > 0 ? totalConMoraFromDb : (cuota > 0 ? cuota * (1 + moraPct / 100) : 0),
            };
        }

        const today = new Date();
        const dueDay = start.getDate();
        const y = today.getFullYear();
        const m = today.getMonth();
        const dom = Math.min(dueDay, daysInMonth(y, m));
        const due = new Date(y, m, dom, 0, 0, 0, 0);

        const msPerDay = 24 * 60 * 60 * 1000;
        const diffDays = Math.floor((today.setHours(0, 0, 0, 0) - due.getTime()) / msPerDay);
        const daysLate = diffDays > 0 ? diffDays : 0;
        const aplicaMora = daysLate > 3;

        const montoConMora = totalConMoraFromDb > 0
            ? totalConMoraFromDb
            : Math.round((cuota * (1 + moraPct / 100)) * 100) / 100;

        return {
            ok: true,
            monto: aplicaMora ? montoConMora : cuota,
            aplicaMora,
            dueDate: due,
            daysLate,
            moraPct,
            montoConMora,
        };
    }

    function fmtDate(d) {
        if (!d) return '—';
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yy = d.getFullYear();
        return `${dd}/${mm}/${yy}`;
    }

    function hasCuotaRow() {
        return !!conceptosBody.querySelector('tr[data-caja-cuota="1"]');
    }

    function addCuotaRowNoSave(monto, texto) {
        // NO crea concepto en DB. Se registra como "Varios" al cobrar (ver collectConceptsData).
        const newRow = conceptosBody.insertRow();
        newRow.setAttribute('data-idconcepto', '0');
        newRow.setAttribute('data-nombre-concepto', texto);
        newRow.setAttribute('data-caja-cuota', '1');
        newRow.insertCell(0).textContent = texto.toUpperCase();

        const montoCell = newRow.insertCell(1);
        montoCell.classList.add('text-end');
        const montoInput = document.createElement('input');
        montoInput.type = 'number';
        montoInput.className = 'form-control monto-input d-inline-block';
        montoInput.min = '0';
        montoInput.step = '0.01';
        montoInput.value = (parseFloat(monto) || 0).toFixed(2);
        montoInput.addEventListener('input', updateTotals);
        montoCell.appendChild(montoInput);

        const actionCell = newRow.insertCell(2);
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-outline-danger btn-sm';
        deleteBtn.innerHTML = '<i class="bi bi-trash"></i>';
        deleteBtn.addEventListener('click', () => {
            newRow.remove();
            updateTotals();
        });
        actionCell.appendChild(deleteBtn);

        updateTotals();
    }

    function renderRegistroVentas(rows) {
        if (!registroVentasCard || !registroVentasTbody) return;
        registroVentasTbody.innerHTML = '';

        if (!rows || !rows.length) {
            registroVentasCard.style.display = 'none';
            return;
        }

        const r = rows[0]; // mostrar la venta más reciente
        const cuotaInfo = calcularMontoCuotaHoy(r);
        const items = [
            ['ID', r.id],
            ['DNI', r.dni_cliente],
            ['Nombre', r.nombre_cliente],
            ['Aval', r.aval],
            ['DNI Aval', r.dni_aval],
            ['Dirección', r.direccion],
            ['Modelo', r.modelo],
            ['Marca', r.marca],
            ['Chasis', r.chasis],
            ['Motor', r.motor],
            ['Color', r.color],
            ['Placa', r.placa],
            ['Estado', r.estado_tramite],
            ['Celular', r.telefono_1],
            ['Celular respaldo', r.telefono_2],
            ['Monto total', r.precio_total],
            ['Inicial', r.pago_inicial],
            ['Deudas pendientes (monto)', r.deudas_pendientes],
            ['Deudas pendientes (detalle)', r.deudas_pendientes_detalle],
            ['Inicio contrato', r.fecha_inicio_credito],
            ['Número de cuotas', r.plazo_meses],
            ['Término contrato', r.fecha_fin_credito],
            ['Pago de cuota', r.cuota_base],
            ['%', r.tasa_interes],
            ['Mora (3 días)', r.mora_3_dias],
            ['Total con mora', r.total_con_mora],
            ['Número de cuota pagada', r.numero_cuota_pagada],
        ];

        // Info de cuota sugerida (y auto-agregar si aplica)
        if (cuotaInfo.ok) {
            const trInfo = document.createElement('tr');
            const badge = cuotaInfo.aplicaMora
                ? `<span class="badge bg-danger">MORA</span>`
                : `<span class="badge bg-success">NORMAL</span>`;
            trInfo.innerHTML = `
                <td colspan="2" class="small">
                    ${badge}
                    Día de pago: <strong>${escapeHtml(fmtDate(cuotaInfo.dueDate))}</strong>
                    ${cuotaInfo.daysLate ? `| Atraso: <strong>${escapeHtml(cuotaInfo.daysLate)}</strong> días` : ''}
                    | Monto a pagar hoy: <strong>S/ ${escapeHtml((cuotaInfo.monto || 0).toFixed(2))}</strong>
                </td>`;
            registroVentasTbody.appendChild(trInfo);

            // Auto-agregar una fila de pago si ya hay cliente seleccionado y no hay filas aún.
            if (idCliente !== null && idCliente > 0 && !hasCuotaRow() && conceptosBody.querySelectorAll('tr').length === 0) {
                const pagada = parseInt(r.numero_cuota_pagada, 10);
                const siguiente = Number.isFinite(pagada) ? (pagada + 1) : null;
                const cuotaN = siguiente ? ` (cuota ${siguiente})` : '';
                const txt = `Cuota mensual${cuotaN} - DNI ${r.dni_cliente}`;
                addCuotaRowNoSave(cuotaInfo.monto, txt);
                showToast('Se añadió un concepto sugerido (cuota). Revisá la lista: podés editar, borrar filas o agregar otros. La boleta y el PDF usan esos textos exactamente.', 'INFO', 8000);
            }
        }

        if (rows.length > 1) {
            registroVentasTbody.insertAdjacentHTML(
                'beforeend',
                `<tr><td colspan="2" class="small text-warning">Este DNI tiene <strong>${rows.length}</strong> ventas. Mostrando la más reciente.</td></tr>`
            );
        }

        items.forEach(([k, v]) => {
            registroVentasTbody.insertAdjacentHTML(
                'beforeend',
                `<tr><th class="text-nowrap" style="width:40%;">${escapeHtml(k)}</th><td class="small">${escapeHtml(fmt(v))}</td></tr>`
            );
        });

        registroVentasCard.style.display = 'block';
    }



    selectMedioPago.addEventListener('change', (e) => {
        const medioSeleccionado = e.target.value;
        const requiereComprobante = (medioSeleccionado !== 'Efectivo' && medioSeleccionado !== '');
        const esTransferencia = (medioSeleccionado === 'Transferencia Bancaria');

        contenedorComprobante.style.display = requiereComprobante ? 'block' : 'none';
        contenedorCuenta.style.display = esTransferencia ? 'block' : 'none';

        if (esTransferencia) {
            cargarCuentasBancarias();
        }
    });

    /**
     * Muestra los datos del cliente en la tarjeta lateral y asigna el ID global.
     */
    function displayClienteData(data) {
        idCliente = data.idcliente;
        clienteCard.style.display = 'block';

        clienteIDSpan.textContent = idCliente;
        clienteNombre.textContent = data.cliente ?? 'N/A';
        clienteDNI.textContent = data.nrodoc ?? 'N/A';
        clienteCorreo.textContent = data.email ?? 'N/A';
        clienteDireccion.textContent = data.direccion ?? 'N/A';

        updateTotals(); // Actualizar el estado del botón
        loadCronogramaCliente(idCliente);
    }

    function renderCronograma(rows, meta = {}) {
        if (!cronogramaCard || !cronogramaTbody) return;
        cronogramaTbody.innerHTML = '';

        cronogramaCard.style.display = 'block';
        if (cronogramaFootnote) {
            const veh = meta?.vehiculo ? ` — ${meta.vehiculo}` : '';
            const gps = (meta?.gps ?? null) !== null ? ` (GPS: S/ ${Number(meta.gps || 0).toFixed(2)})` : '';
            const est = meta?.contrato_estado ? ` [Contrato: ${meta.contrato_estado}]` : '';
            const src = meta?.source ? ` [Fuente: ${meta.source}]` : '';
            const msg = meta?.message ? ` ${meta.message}` : '';
            cronogramaFootnote.textContent = `Mostrando cronograma del contrato más reciente del cliente${veh}${gps}.${est}${src}${msg}`;
        }

        if (!rows || !rows.length) {
            cronogramaTbody.innerHTML = `<tr><td colspan="4" class="small text-muted text-center p-3">Sin cronograma para mostrar.</td></tr>`;
            return;
        }

        rows.forEach(r => {
            const tr = document.createElement('tr');
            const estado = String(r.estado || '');
            const badge =
                estado === 'Pagado' ? 'bg-success' :
                    (estado === 'Por saldar' ? 'bg-warning text-dark' : 'bg-secondary');
            const fecha = r.fechapago ? fmtDate(parseIsoDate(r.fechapago)) : '—';
            tr.innerHTML = `
                <td class="fw-bold">#${escapeHtml(r.numcuota)}</td>
                <td class="small">${escapeHtml(fecha)}</td>
                <td class="text-end fw-semibold">S/ ${escapeHtml(Number(r.total || 0).toFixed(2))}</td>
                <td><span class="badge ${badge}">${escapeHtml(estado)}</span></td>
            `;
            cronogramaTbody.appendChild(tr);
        });
    }

    async function loadCronogramaCliente(idcliente) {
        if (!idcliente || idcliente <= 0) {
            if (cronogramaCard) cronogramaCard.style.display = 'none';
            if (cronogramaTbody) cronogramaTbody.innerHTML = '';
            return;
        }
        try {
            const req = await fetch(`/api/caja/cronograma-por-cliente/${encodeURIComponent(String(idcliente))}`);
            if (!req.ok) throw new Error('Fallo al cargar cronograma: ' + req.status);
            const res = await req.json().catch(() => null);
            if (res && res.success) {
                renderCronograma(res.data || [], { vehiculo: res.vehiculo, gps: res.gps, contrato_estado: res.contrato_estado, source: res.source, message: res.message });
            } else {
                renderCronograma([]);
            }
        } catch (e) {
            console.error(e);
            renderCronograma([]);
        }
    }

    /**
     * Actualiza los totales y el estado del botón de generación.
     */
    function updateTotals() {
        let total = 0;
        const montoInputs = conceptosBody.querySelectorAll('input[type="number"]');

        montoInputs.forEach(input => {
            const monto = parseFloat(input.value) || 0;
            total += monto;
        });

        subtotalSpan.textContent = `S/ ${total.toFixed(2)}`;
        totalPagarSpan.textContent = `S/ ${total.toFixed(2)}`;

        // Habilita el botón solo si hay un total > 0 Y un cliente seleccionado
        btnGenerar.disabled = (total <= 0 || idCliente === null);

        emptyMessage.style.display = (montoInputs.length === 0) ? 'block' : 'none';
    }


    async function addConceptoRow(concepto, monto, idConcepto = 0) {
        let idFinal = idConcepto;

        if (idConcepto === 0) {
            try {
                const formData = new FormData();
                formData.append('concepto', concepto);
                formData.append('descripcion', document.getElementById('descripcion-concepto').value || '');
                formData.append('montosugerido', monto);

                const req = await fetch('/store/conceptoPago', {
                    method: 'POST',
                    body: formData
                });

                if (!req.ok) throw new Error('Error en la solicitud: ' + req.status);

                const res = await req.json();

                if (res.success) {
                    showToast(res.message, 'SUCCESS', 1250);
                    idFinal = res.id;
                } else {
                    showToast(res.message, 'ERROR', 1350);
                    return;
                }

            } catch (error) {
                console.error(error);
                showToast('Error al guardar el concepto', 'ERROR');
                return;
            }
        }


        const newRow = conceptosBody.insertRow();


        newRow.setAttribute('data-idconcepto', idFinal);
        newRow.setAttribute('data-nombre-concepto', concepto);
        newRow.insertCell(0).textContent = concepto.toUpperCase();
        const montoCell = newRow.insertCell(1);
        montoCell.classList.add('text-end');

        const montoInput = document.createElement('input');
        montoInput.type = 'number';
        montoInput.className = 'form-control monto-input d-inline-block';
        montoInput.min = '0';
        montoInput.step = '0.01';
        montoInput.value = (parseFloat(monto) || 0).toFixed(2);
        montoInput.addEventListener('input', updateTotals);
        montoCell.appendChild(montoInput);
        const actionCell = newRow.insertCell(2);
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-outline-danger btn-sm';
        deleteBtn.innerHTML = '<i class="bi bi-trash"></i>';
        deleteBtn.addEventListener('click', () => {
            newRow.remove();
            updateTotals();
        });
        actionCell.appendChild(deleteBtn);

        updateTotals();
    }



    async function cargarCuentasBancarias() {
        try {
            const req = await fetch('/api/numcuentaspagos', {
                method: 'GET'
            });
            if (!req.ok) throw new Error('Fallo al cargar cuentas.');

            const res = await req.json();
            selectCuenta.innerHTML = '<option value="" disabled selected>Seleccione una cuenta de pago</option>';
            res.forEach(cuenta => {
                selectCuenta.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
            });
        } catch (error) {
            showToast('Error al cargar cuentas bancarias.', 'ERROR', 2000);
            console.error(error);
        }
    }

    async function searchClienteDB() {
        const documento = inputDNI.value.trim().replace(/\D/g, '');
        inputDNI.value = documento;

        if (!/^\d{8}$/.test(documento) && !/^\d{11}$/.test(documento)) {
            showToast('Ingrese DNI (8 dígitos) o RUC (11 dígitos).', 'ERROR', 2200);
            clienteCard.style.display = 'none';
            idCliente = null;
            updateTotals();
            return;
        }

        /**
         * A partir de aquí el documento es formalmente válido (8 o 11 dígitos).
         * Limpiamos SIEMPRE los conceptos y el registro de ventas antes de buscar,
         * para evitar que queden filas (y montos) de un cliente anterior.
         * Así garantizamos que:
         * - Cada boleta se genera sólo con conceptos añadidos después de elegir cliente.
         * - No se arrastran cuotas sugeridas de otro DNI.
         */
        conceptosBody.innerHTML = '';
        renderRegistroVentas([]);
        renderCronograma([]);
        idCliente = null;
        updateTotals();

        try {
            const req = await fetch(`/api/clienteByDNI/${encodeURIComponent(documento)}`, {
                method: 'GET'
            });

            if (req.status === 400) {
                const err = await req.json().catch(() => ({}));
                showToast(err.message || 'Documento no válido.', 'WARNING', 3000);
                clienteCard.style.display = 'none';
                idCliente = null;
                updateTotals();
                return;
            }
            if (req.status === 404) {
                const err = await req.json().catch(() => ({}));
                showToast(err.message || 'Cliente no encontrado.', 'WARNING', 3000);
                clienteCard.style.display = 'none';
                idCliente = null;
                updateTotals();

                // Fallback: buscar en registro ventas vehiculares (solo para DNI 8)
                if (/^\d{8}$/.test(documento)) {
                    try {
                        const rvReq = await fetch(`/api/caja/registro-ventas-vehiculares/${encodeURIComponent(documento)}`);
                        if (rvReq.ok) {
                            const rv = await rvReq.json().catch(() => null);
                            if (rv && rv.success && Array.isArray(rv.data) && rv.data.length) {
                                renderRegistroVentas(rv.data);
                                showToast('Encontrado en registro vehicular (Excel). Para cobrar, primero registra al cliente.', 'INFO', 5500);
                            } else {
                                renderRegistroVentas([]);
                            }
                        } else {
                            renderRegistroVentas([]);
                        }
                    } catch (e) {
                        console.error(e);
                        renderRegistroVentas([]);
                    }
                } else {
                    renderRegistroVentas([]);
                }
                return;
            }
            if (!req.ok) {
                showToast('Error en la solicitud al servidor', 'ERROR', 1800);
                throw new Error(`Error en la solicitud: ${req.status}`);
            }

            const res = await req.json();

            if (res.success && res.cliente) {
                showToast('Cliente encontrado', 'SUCCESS', 1400);
                displayClienteData(res.cliente);
                // Si existe como cliente, igual mostramos (si hay) el registro vehicular
                if (/^\d{8}$/.test(documento)) {
                    try {
                        const rvReq = await fetch(`/api/caja/registro-ventas-vehiculares/${encodeURIComponent(documento)}`);
                        if (rvReq.ok) {
                            const rv = await rvReq.json().catch(() => null);
                            if (rv && rv.success) renderRegistroVentas(rv.data || []);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            } else {
                showToast('Cliente no encontrado', 'ERROR', 1400);
                clienteCard.style.display = 'none';
                idCliente = null;
                updateTotals();
                renderRegistroVentas([]);
            }

        } catch (error) {
            console.error("Error al buscar cliente:", error);
            showToast('Ocurrió un error de red o servidor.', 'ERROR', 2000);
        }

    }

    async function getConceptosPagos() {
        try {
            const req = await fetch('/api/conceptoPagos', {
                method: 'GET'
            });
            if (!req.ok) throw new Error('Error en la solicitud ' + req.status);

            const res = await req.json();
            if (!res.success || !Array.isArray(res.conceptos)) {
                showToast(res.message || 'No se pudieron cargar los conceptos sugeridos.', 'WARNING', 5000);
                return;
            }
            if (res.conceptos.length === 0) {
                if (conceptosDBList) conceptosDBList.innerHTML = '';
                conceptosDB.value = '';
                btnAddDB.disabled = true;
                showToast('No hay conceptos sugeridos en la base de datos. Puede usar concepto personalizado o cargar conceptospago.', 'WARNING', 7000);
                return;
            }
            conceptosCatalogo = res.conceptos.map(c => ({
                idconcepto: parseInt(c.idconcepto, 10),
                concepto: String(c.concepto ?? '').trim(),
                montosugerido: parseFloat(c.montosugerido ?? 0) || 0,
            })).filter(c => c.concepto);

            // Index por "concepto" y por "concepto - S/x" para poder pegar cualquiera.
            conceptosCatalogoByKey = new Map();
            conceptosCatalogo.forEach(c => {
                const label = `${c.concepto} - S/${c.montosugerido.toFixed(2)}`;
                conceptosCatalogoByKey.set(c.concepto.toLowerCase(), c);
                conceptosCatalogoByKey.set(label.toLowerCase(), c);
            });

            if (conceptosDBList) {
                conceptosDBList.innerHTML = '';
                conceptosCatalogo.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = `${c.concepto} - S/${c.montosugerido.toFixed(2)}`;
                    conceptosDBList.appendChild(opt);
                });
            }
            conceptosDB.value = '';
            btnAddDB.disabled = true;

        } catch (error) {
            console.error("Error al cargar conceptos:", error);
            showToast('Error al cargar conceptos. Revise conexión o permisos.', 'ERROR', 5000);
        }
    }


    /**
     * Recolecta todos los conceptos de la tabla y los serializa a un array JSON.
     * Esto es lo que el SP 'sp_registrar_pago_compuesto' espera en p_detalles_json.
     */
    function collectConceptsData() {
        const data = [];
        const rows = conceptosBody.querySelectorAll('tr');

        rows.forEach(row => {
            const conceptoTexto = row.getAttribute('data-nombre-concepto');
            const montoInput = row.cells[1].querySelector('input[type="number"]');
            const monto = parseFloat(montoInput.value) || 0;
            const idConcepto = parseInt(row.getAttribute('data-idconcepto'));


            const idConceptoFinal = idConcepto > 0 ? idConcepto : (ID_CONCEPTO_VARIOS > 0 ? ID_CONCEPTO_VARIOS : 1);
            if (idConcepto <= 0 && ID_CONCEPTO_VARIOS <= 0) {
                console.warn('Caja: configure el concepto "Varios caja (manual)" en MySQL (ver datos-inserts/caja_concepto_varios.sql).');
            }

            data.push({
                idconcepto: idConceptoFinal,
                monto: monto.toFixed(2),
                nombre: conceptoTexto,
                obs: ''
            });
        });

        return data;
    }
    async function generarBoletaYRegistrarPago() {
        if (idCliente === null || idCliente <= 0) {
            showToast('Debe seleccionar un cliente antes de generar la boleta.', 'ERROR', 2500);
            return;
        }

        const conceptosData = collectConceptsData();
        const montoTotal = parseFloat(totalPagarSpan.textContent.replace('S/ ', '')) || 0;
        const medioPago = selectMedioPago.value;
        const idCuentaPago = (contenedorCuenta.style.display === 'block') ? selectCuenta.value : null;


        if (conceptosData.length === 0 || montoTotal <= 0) {
            showToast('No hay conceptos o el monto es cero.', 'WARNING', 2000);
            return;
        }
        if (!medioPago) {
            showToast('Seleccione un medio de pago.', 'WARNING', 2000);
            return;
        }
        if (medioPago === 'Transferencia Bancaria' && !idCuentaPago) {
            showToast('Seleccione una cuenta bancaria para la transferencia.', 'ERROR', 2000);
            return;
        }

        const formData = new FormData();
        formData.append('idcliente', idCliente);
        formData.append('mediopago', medioPago);

        formData.append('numerotransaccion', 'N/A');
        formData.append('idcuentapago', idCuentaPago);
        formData.append('monto_total', montoTotal.toFixed(2));
        formData.append('detalles_json', JSON.stringify(conceptosData));

        // console.log(formData);

        const requiereComprobante = medioPago !== 'Efectivo';

        if (requiereComprobante) {

            if (inputComprobante && inputComprobante.files && inputComprobante.files.length > 0) {
                formData.append('comprobante', inputComprobante.files[0]);
            } else {
                showToast(`El comprobante es obligatorio para pagos con ${medioPago}.`, 'ERROR', 3000);
                return;
            }
        }


        btnGenerar.disabled = true;
        btnGenerar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando Boleta...';

        // Misma acción de clic: abrir pestaña vacía ya (así el navegador no bloquea al llegar el PDF tras await fetch).
        const pdfWindow = window.open('about:blank', '_blank');

        try {
            const req = await fetch('/api/storePagoCompuesto', {
                method: 'POST',
                body: formData
            });

            const res = await req.json();

            if (res.success) {
                const okFact = Object.prototype.hasOwnProperty.call(res, 'facturado')
                    ? (res.facturado === true)
                    : !!res.enlace_pdf;
                if (!okFact) {
                    const extra = res.mensaje_facturacion ? ' ' + res.mensaje_facturacion : '';
                    showToast((res.message || 'Pago registrado.') + extra, 'WARNING', 9000);
                } else {
                    let msgOk = res.message || 'Pago y comprobante OK.';
                    if (res.comprobante_serie && res.comprobante_numero != null && res.comprobante_numero !== '') {
                        const tipo = res.comprobante_tipo === 'F' ? 'Factura' : 'Boleta';
                        const n = parseInt(res.comprobante_numero, 10);
                        const nStr = Number.isFinite(n) ? String(n).padStart(6, '0') : String(res.comprobante_numero);
                        msgOk += ' — ' + res.comprobante_serie + '-' + nStr + ' (' + tipo + '). El PDF debe mostrar el mismo número y descripciones de la grilla.';
                    }
                    showToast(msgOk, 'SUCCESS', 7000);
                }

                if (res.enlace_pdf) {
                    const url = String(res.enlace_pdf);
                    if (pdfWindow) {
                        try {
                            pdfWindow.opener = null;
                            pdfWindow.location.href = url;
                        } catch (e) {
                            const a = document.createElement('a');
                            a.href = url;
                            a.target = '_blank';
                            a.rel = 'noopener noreferrer';
                            a.click();
                            showToastWithPdfLink('Si no se abre el comprobante, usá el enlace:', url, 'INFO', 12000);
                        }
                    } else {
                        const a = document.createElement('a');
                        a.href = url;
                        a.target = '_blank';
                        a.rel = 'noopener noreferrer';
                        a.click();
                        showToastWithPdfLink('Permití ventanas emergentes o abrí el comprobante aquí:', url, 'INFO', 12000);
                    }
                } else {
                    if (pdfWindow) {
                        try { pdfWindow.close(); } catch (e) { /* no-op */ }
                    }
                }

                conceptosBody.innerHTML = '';
                inputDNI.value = '';
                selectMedioPago.selectedIndex = 0;
                selectCuenta.selectedIndex = 0;
                contenedorComprobante.style.display = 'none';
                contenedorCuenta.style.display = 'none';
                idCliente = null;
                clienteCard.style.display = 'none';
                inputComprobante.value = '';
                updateTotals();

            } else {
                if (pdfWindow) {
                    try { pdfWindow.close(); } catch (e) { /* no-op */ }
                }
                showToast(' Error: ' + res.message, 'ERROR', 6000);
            }

        } catch (error) {
            if (pdfWindow) {
                try { pdfWindow.close(); } catch (e) { /* no-op */ }
            }
            console.error("Error al registrar pago:", error);
            showToast('Error de conexión o JSON inválido en la respuesta del servidor.', 'ERROR', 4000);
        } finally {
            btnGenerar.disabled = false;
            btnGenerar.innerHTML = 'Generar Boleta y Pagar';
        }
    }

    btnDNI.addEventListener('click', searchClienteDB);

    inputDNI.addEventListener('keydown', async e => {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            await searchClienteDB();
        }
    });

    function parseManualConceptoFromInput(raw) {
        // Permite escribir manualmente: "CONCEPTO - S/14.00"
        // Se añade como línea manual (no se guarda en catálogo).
        const m = String(raw || '').match(/^\s*(.+?)\s*-\s*S\/\s*(\d+(?:[.,]\d+)?)\s*$/i);
        if (!m) return null;
        const concepto = String(m[1] || '').trim();
        const monto = parseFloat(String(m[2]).replace(',', '.'));
        if (!concepto || !isFinite(monto) || monto <= 0) return null;
        return { idconcepto: 0, concepto, montosugerido: monto, __manual: true };
    }

    function getSelectedConceptoFromInput() {
        const raw = (conceptosDB?.value || '').trim();
        if (!raw) return null;

        // 1) Match exacto a catálogo (por concepto o "concepto - S/x")
        const c = conceptosCatalogoByKey.get(raw.toLowerCase());
        if (c) return c;

        // 2) Si no existe en catálogo, permitir manual si viene con monto " - S/xx"
        return parseManualConceptoFromInput(raw);
    }

    function syncConceptoAddButton() {
        btnAddDB.disabled = !getSelectedConceptoFromInput();
    }

    btnAddDB.addEventListener('click', () => {
        const c = getSelectedConceptoFromInput();
        if (!c) {
            showToast('Escribe un concepto del catálogo o usa el formato "CONCEPTO - S/14.00" para añadirlo manualmente.', 'WARNING', 3500);
            return;
        }
        addConceptoRow(c.concepto, c.montosugerido, c.idconcepto);
        conceptosDB.value = '';
        btnAddDB.disabled = true;
    });

    conceptosDB.addEventListener('input', syncConceptoAddButton);
    conceptosDB.addEventListener('change', syncConceptoAddButton);
    conceptosDB.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (!btnAddDB.disabled) btnAddDB.click();
        }
    });


    btnAddManual.addEventListener('click', () => {
        const concepto = conceptoManual.value.trim();
        const monto = parseFloat(montoManual.value);

        if (concepto && monto > 0) {
            addConceptoRow(concepto, monto, 0);
            conceptoManual.value = '';
            montoManual.value = '0.00';
        } else {
            showToast('Por favor, ingrese un concepto válido y un monto mayor a cero.', 'WARNING', 2000);
        }
    });


    btnGenerar.addEventListener('click', generarBoletaYRegistrarPago);



    getConceptosPagos();
    window.onload = updateTotals;
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>