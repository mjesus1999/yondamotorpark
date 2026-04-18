<?php include __DIR__ . '/../layout/header.php'; ?>
<!-- yonda:cobros-denom build=2026-04-17-v4 resumen-credito + cliente arriba del resumen -->

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
                            <select id="conceptos-db" class="form-select">
                                <option value="" disabled selected>Seleccione un concepto de la lista...</option>
                            </select>
                            <button class="btn btn-primary" id="btn-add-db" disabled>Añadir</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="input-dni" class="form-label">Buscar Cliente por DNI</label>
                        <div class="input-group">
                            <input type="text" id="input-dni" class="form-control" placeholder="Ingrese el DNI del cliente" maxlength="8">
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
    </div>

    <div class="col-lg-4 mt-4 mt-lg-0">
        <!-- Cliente primero: así se ve el resumen de crédito sin quedar debajo del botón de pago -->
        <div class="card border-primary shadow-lg mb-4" id="card-cliente" style="display: none;">
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

                <hr class="my-3" id="credito-sep" style="display: none;">

                <div id="credito-resumen" class="small" style="display: none;">
                    <div class="fw-bold text-info mb-2"><i class="bi bi-calendar2-week me-1"></i> Crédito / cronograma (contrato ACT)</div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Contrato:</span><span id="cred-idcontrato" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Cuotas del plan:</span><span id="cred-numcuotas" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Valor cuota:</span><span id="cred-valorcuota" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Pagadas / pend. / venc.:</span><span id="cred-contadores" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Próxima cuota (nº):</span><span id="cred-sig-num" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Fecha próx. cuota:</span><span id="cred-sig-fecha" class="fw-semibold"></span></div>
                    <div class="d-flex justify-content-between mb-0"><span class="text-muted">Estado próx. cuota:</span><span id="cred-sig-estado" class="fw-semibold"></span></div>
                </div>

                <div id="credito-sin-datos" class="small text-warning mt-2" style="display: none;">
                    <i class="bi bi-info-circle me-1"></i> Sin contrato activo o sin cronograma cargado para este cliente.
                </div>

            </div>
        </div>

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

    </div>
</div>


<script>
    // Notificaciones: usa showToast de /assets/js/swalcustom.js (cargado en el footer).
    console.info('[Yonda] cobros-denominacion JS build 2026-04-17-v4-credito-ui');

    const clienteNombre = document.getElementById('nombrecompleto');
    const clienteIDSpan = document.getElementById('cliente-id');
    const clienteDNI = document.getElementById('cliente-dni');
    const clienteCorreo = document.getElementById('cliente-correo');
    const clienteDireccion = document.getElementById('cliente-direccion');
    const clienteCard = document.getElementById('card-cliente');
    const credSep = document.getElementById('credito-sep');
    const credBox = document.getElementById('credito-resumen');
    const credSin = document.getElementById('credito-sin-datos');

    const selectMedioPago = document.getElementById('mediopago');
    const contenedorComprobante = document.getElementById('contenedor-comprobante');
    const inputComprobante = document.getElementById('comprobante'); // Input File
    const contenedorCuenta = document.getElementById('contenedor-cuenta');
    const selectCuenta = document.getElementById('cuenta'); // Select de cuenta bancaria

    const conceptosBody = document.getElementById('conceptos-body');
    const subtotalSpan = document.getElementById('subtotal');
    const totalPagarSpan = document.getElementById('total-pagar');
    const btnGenerar = document.getElementById('btn-generar-boleta');
    const conceptosDB = document.getElementById('conceptos-db');
    const btnAddDB = document.getElementById('btn-add-db');
    const conceptoManual = document.getElementById('concepto-manual');
    const montoManual = document.getElementById('monto-manual');
    const descripcionManual = document.getElementById('descripcion-concepto');
    const btnAddManual = document.getElementById('btn-add-manual');
    const emptyMessage = document.getElementById('empty-message');
    const btnDNI = document.getElementById('btn-dni');
    const inputDNI = document.getElementById('input-dni');

    let idCliente = null;



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
    function limpiarBloqueCredito() {
        if (credSep) credSep.style.display = 'none';
        if (credBox) credBox.style.display = 'none';
        if (credSin) credSin.style.display = 'none';
    }

    function displayClienteData(data, credito) {
        idCliente = data.idcliente;
        clienteCard.style.display = 'block';

        clienteIDSpan.textContent = idCliente;
        clienteNombre.textContent = data.cliente ?? 'N/A';
        clienteDNI.textContent = data.nrodoc ?? 'N/A';
        clienteCorreo.textContent = data.email ?? 'N/A';
        clienteDireccion.textContent = data.direccion ?? 'N/A';

        limpiarBloqueCredito();
        const tieneCred = credito && (credito.idcontrato != null && String(credito.idcontrato) !== '');
        if (tieneCred && credSep && credBox) {
            credSep.style.display = '';
            credBox.style.display = 'block';
            const el = (id) => document.getElementById(id);
            if (el('cred-idcontrato')) el('cred-idcontrato').textContent = String(credito.idcontrato);
            if (el('cred-numcuotas')) el('cred-numcuotas').textContent = String(credito.numcuotas ?? '—');
            const vc = credito.valorcuota != null ? Number(credito.valorcuota) : null;
            if (el('cred-valorcuota')) {
                el('cred-valorcuota').textContent =
                    vc != null && !Number.isNaN(vc) ? `S/ ${vc.toFixed(2)}` : '—';
            }
            const cp = Number(credito.cuotas_pagadas ?? 0);
            const cpe = Number(credito.cuotas_pendientes ?? 0);
            const cv = Number(credito.cuotas_vencidas ?? 0);
            if (el('cred-contadores')) el('cred-contadores').textContent = `${cp} / ${cpe} / ${cv}`;
            const sn = credito.siguiente_cuota_num;
            if (el('cred-sig-num')) {
                el('cred-sig-num').textContent =
                    sn != null && sn !== '' ? String(sn) : '— (al día o sin filas en cronograma)';
            }
            if (el('cred-sig-fecha')) el('cred-sig-fecha').textContent =
                credito.siguiente_cuota_fecha ? String(credito.siguiente_cuota_fecha) : '—';
            if (el('cred-sig-estado')) el('cred-sig-estado').textContent =
                credito.siguiente_cuota_estado ? String(credito.siguiente_cuota_estado) : '—';
        } else if (credSep && credSin) {
            credSep.style.display = '';
            credSin.style.display = 'block';
        }

        updateTotals(); // Actualizar el estado del botón
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

    /**
     * Para conceptos escritos a mano no hace falta INSERT en conceptospago (fallaba en varios servidores).
     * El SP usa idconcepto solo como FK; el texto cobrado va en "nombre" del JSON (nombre_concepto_manual).
     */
    function primerIdConceptoDelCatalogo() {
        const sel = document.getElementById('conceptos-db');
        if (!sel) return 1;
        for (let i = 0; i < sel.options.length; i++) {
            const v = parseInt(sel.options[i].value, 10);
            if (!Number.isNaN(v) && v > 0) return v;
        }
        return 1;
    }

    async function addConceptoRow(concepto, monto, idConcepto = 0) {
        let idFinal = idConcepto;

        if (idConcepto === 0) {
            idFinal = primerIdConceptoDelCatalogo();
            showToast('Concepto agregado. El nombre que ingresaste es el que verá el cliente en el detalle del pago.', 'SUCCESS', 2200);
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
        const dni = inputDNI.value.trim();

        if (dni.length !== 8) {
            showToast('El DNI debe tener 8 dígitos.', 'ERROR', 1800);
            clienteCard.style.display = 'none';
            limpiarBloqueCredito();
            idCliente = null;
            updateTotals();
            return;
        }

        try {
            const req = await fetch(`/api/clienteByDNI/${dni}`, {
                method: 'GET'
            });

            if (req.status === 404) {
                showToast('Cliente no encontrado.', 'WARNING', 1400);
                clienteCard.style.display = 'none';
                limpiarBloqueCredito();
                idCliente = null;
                updateTotals();
                return;
            }
            if (!req.ok) {
                showToast('Error en la solicitud al servidor', 'ERROR', 1800);
                throw new Error(`Error en la solicitud: ${req.status}`);
            }

            const res = await req.json();

            if (res.success && res.cliente) {
                showToast('Cliente encontrado', 'SUCCESS', 1400);
                displayClienteData(res.cliente, res.credito ?? null);
            } else {
                showToast('Cliente no encontrado', 'ERROR', 1400);
                clienteCard.style.display = 'none';
                limpiarBloqueCredito();
                idCliente = null;
                updateTotals();
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
            const lista = res.conceptos || [];
            lista.forEach(concepto => {
                conceptosDB.innerHTML += `
                    <option 
                        value="${concepto.idconcepto}" 
                        data-concepto="${concepto.concepto}"  
                        data-monto="${concepto.montosugerido}">
                        ${concepto.concepto} - S/${concepto.montosugerido}
                    </option>
                `;
            });

        } catch (error) {
            console.error("Error al cargar conceptos:", error);
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


            const idConceptoFinal = idConcepto > 0 ? idConcepto : 1;

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



        try {
            const req = await fetch('/api/storePagoCompuesto', {
                method: 'POST',
                body: formData
            });

            const res = await req.json();

            if (res.success) {
                showToast(res.message, 'SUCCESS', 1250);


                if (res.enlace_pdf) {
                    setTimeout(() => {
                        window.open(res.enlace_pdf, '_blank');
                    }, 500);
                }

                conceptosBody.innerHTML = '';
                inputDNI.value = '';
                selectMedioPago.selectedIndex = 0;
                selectCuenta.selectedIndex = 0;
                contenedorComprobante.style.display = 'none';
                contenedorCuenta.style.display = 'none';
                idCliente = null;
                clienteCard.style.display = 'none';
                limpiarBloqueCredito();
                inputComprobante.value = '';
                updateTotals();

            } else {
                showToast(' Error: ' + res.message, 'ERROR', 6000);
            }

        } catch (error) {
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

    btnAddDB.addEventListener('click', () => {
        const selectedOption = conceptosDB.options[conceptosDB.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const idConcepto = parseInt(selectedOption.value);
            const conceptoTexto = selectedOption.getAttribute('data-concepto');
            const monto = selectedOption.getAttribute('data-monto');

            addConceptoRow(conceptoTexto, monto, idConcepto); // Pasa el ID

            conceptosDB.selectedIndex = 0;
            btnAddDB.disabled = true;
        }
    });

    conceptosDB.addEventListener('change', () => {
        btnAddDB.disabled = conceptosDB.selectedIndex === 0;
    });


    btnAddManual.addEventListener('click', () => {
        const tituloConcepto = conceptoManual.value.trim() || descripcionManual.value.trim();
        const monto = parseFloat(montoManual.value);

        if (!tituloConcepto) {
            showToast('Ingresá el nombre del concepto o una descripción.', 'WARNING', 2500);
            return;
        }
        if (!(monto > 0)) {
            showToast('El monto debe ser mayor a cero (S/).', 'WARNING', 2500);
            return;
        }

        addConceptoRow(tituloConcepto, monto, 0);
        conceptoManual.value = '';
        descripcionManual.value = '';
        montoManual.value = '0.00';
    });


    btnGenerar.addEventListener('click', generarBoletaYRegistrarPago);



    getConceptosPagos();
    window.onload = updateTotals;
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>