<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .monto-input {
        width: 120px;
        text-align: right;
        font-weight: bold;
    }

    .bg-custom-blue {
        background-color: #003d75;
        color: white;
    }
</style>

<div class="container-fluid p-4 bg-custom-blue">
    <h1 class="text-white">Generador de Boletas de Pago</h1>
    <p class="text-white-50">Selecciona o ingresa los conceptos de pago para la boleta.</p>
</div>

<div class="row mt-4">

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
                    <div class="col-md-8">
                        <label for="concepto-manual" class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="concepto-manual" placeholder="Ej: Multa de Tránsito">
                    </div>
                    <div class="col-md-4">
                        <label for="monto-manual" class="form-label">Monto (S/)</label>
                        <div class="input-group">
                            <input type="number" class="form-control text-end" id="monto-manual" min="0" value="0.00">
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
                    <select class="form-select mb-4" id="cuenta" name="cuenta">
                        <option value="" disabled selected>Seleccione una cuenta de pago</option>

                    </select>

                </div>

                <div class="col-md-12 mb-4" style="display: none;" id="contenedor-comprobante">
                    <label class="form-label small text-muted" id="comprobante">Comprobante de Pago</label>
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Cliente Seleccionado</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">ID Cliente:</span>
                    <span id="cliente-id" class="fw-bold text-danger"></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Nombre completo:</span>
                    <span id="nombrecompleto" class="fw-bold"></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2 ">
                    <span class="fw-bold">DNI:</span>
                    <span id="cliente-dni"></span>
                </div>
                <div class="d-flex justify-content-between mb-2 ">
                    <span class="fw-bold">Correo:</span>
                    <span id="cliente-correo"></span>
                </div>
                <div class="d-flex justify-content-between mb-2 ">
                    <span class="fw-bold">Dirección:</span>
                    <span id="cliente-direccion"></span>
                </div>

            </div>
        </div>
    </div>
</div>



<script>
    const clienteNombre = document.getElementById('nombrecompleto');
    const clienteIDSpan = document.getElementById('cliente-id');
    const clienteDNI = document.getElementById('cliente-dni');
    const clienteCorreo = document.getElementById('cliente-correo');
    const clienteDireccion = document.getElementById('cliente-direccion');
    const clienteCard = document.getElementById('card-cliente');

    const selectMedioPago = document.getElementById('medio-pago');

    const contenedorComprobante = document.getElementById('contenedor-comprobante'); // OCULTO POR DEFECTO
    const inputComprobante = document.getElementById('comprobante');
    const contenedorCuenta = document.getElementById('contenedor-cuenta'); // OCULTO POR DEFECTO
    const selectCuenta = document.getElementById('cuenta');

    const conceptosBody = document.getElementById('conceptos-body');
    const subtotalSpan = document.getElementById('subtotal');
    const totalPagarSpan = document.getElementById('total-pagar');
    const btnGenerar = document.getElementById('btn-generar-boleta');
    const conceptosDB = document.getElementById('conceptos-db');
    const btnAddDB = document.getElementById('btn-add-db');
    const conceptoManual = document.getElementById('concepto-manual');
    const montoManual = document.getElementById('monto-manual');
    const btnAddManual = document.getElementById('btn-add-manual');
    const emptyMessage = document.getElementById('empty-message');
    const btnDNI = document.getElementById('btn-dni');
    const inputDNI = document.getElementById('input-dni');


    let idCliente = null;






    selectMedioPago.addEventListener('change', (e) => {

        if (e.value.toLowerCase() == 'efectivo') {
            inputComprobante.style.display = 'flex';
        }


    });





    /**
     * Muestra los datos del cliente en la tarjeta lateral y asigna el ID global.
     * @param {Object} data - Objeto cliente devuelto por la API.
     */
    function displayClienteData(data) {
        idCliente = data.idcliente; // Asignación de valor crucial
        clienteCard.style.display = 'block';

        clienteIDSpan.textContent = idCliente;
        clienteNombre.textContent = data.cliente ?? 'N/A';
        clienteDNI.textContent = data.nrodoc ?? 'N/A';
        clienteCorreo.textContent = data.email ?? 'N/A';
        clienteDireccion.textContent = data.direccion ?? 'N/A';
    }

    async function searchClienteDB() {
        const dni = inputDNI.value.trim();

        if (dni.length !== 8) {
            showToast('El DNI debe tener 8 dígitos.', 'ERROR', 1800);
            clienteCard.style.display = 'none';
            idCliente = null;
            return;
        }

        try {
            const req = await fetch(`/api/clienteByDNI/${dni}`, {
                method: 'GET'
            });

            if (req.status === 404) {
                showToast('Cliente no encontrado.', 'WARNING', 1400);
                clienteCard.style.display = 'none';
                idCliente = null;
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
            } else {
                showToast('Cliente no encontrado', 'ERROR', 1400);
                clienteCard.style.display = 'none';
                idCliente = null;
            }

        } catch (error) {
            console.error("Error al buscar cliente:", error);
            showToast('Ocurrió un error de red o servidor.', 'ERROR', 2000);
        }
    }



    btnDNI.addEventListener('click', searchClienteDB);

    inputDNI.addEventListener('keydown', async e => {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();

            await searchClienteDB();
            console.log('ID DEL CLIENTE ASIGNADO: ', idCliente);
        }
    });


    async function getConceptosPagos() {
        try {
            const req = await fetch('/api/conceptoPagos', {
                method: 'GET'
            });
            if (!req.ok) {
                throw new Error('Error en la solicitud ' + req.status);
            }

            const res = await req.json();
            res.conceptos.forEach(concepto => {
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

    // Función para actualizar el total
    function updateTotals() {
        let total = 0;
        const montoInputs = conceptosBody.querySelectorAll('input[type="number"]');

        montoInputs.forEach(input => {
            const monto = parseFloat(input.value) || 0;
            total += monto;
        });

        subtotalSpan.textContent = `S/ ${total.toFixed(2)}`;
        totalPagarSpan.textContent = `S/ ${total.toFixed(2)}`;

        btnGenerar.disabled = total <= 0;

        emptyMessage.style.display = (montoInputs.length === 0) ? 'block' : 'none';
    }


    // Función para añadir una nueva fila a la tabla
    function addConceptoRow(concepto, monto) {
        const newRow = conceptosBody.insertRow();

        newRow.insertCell(0).textContent = concepto.toUpperCase();

        const montoCell = newRow.insertCell(1);
        montoCell.classList.add('text-end');
        const montoInput = document.createElement('input');
        montoInput.type = 'number';
        montoInput.className = 'form-control monto-input d-inline-block';
        montoInput.min = '0';
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


    // Añadir desde la lista de DB (al hacer click en el botón)
    btnAddDB.addEventListener('click', () => {
        const selectedOption = conceptosDB.options[conceptosDB.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const conceptoTexto = selectedOption.getAttribute('data-concepto');
            const monto = selectedOption.getAttribute('data-monto');

            addConceptoRow(conceptoTexto, monto);

            conceptosDB.selectedIndex = 0;
            btnAddDB.disabled = true;
        }
    });


    // Habilitar el botón de Añadir de DB al seleccionar una opción
    conceptosDB.addEventListener('change', () => {
        btnAddDB.disabled = conceptosDB.selectedIndex === 0;
    });

    // Añadir manualmente
    btnAddManual.addEventListener('click', () => {
        const concepto = conceptoManual.value.trim();
        const monto = parseFloat(montoManual.value);

        if (concepto && monto > 0) {
            addConceptoRow(concepto, monto);
            conceptoManual.value = '';
            montoManual.value = '0.00';
        } else {
            showToast('Por favor, ingrese un concepto válido y un monto mayor a cero.', 'WARNING', 2000);
        }
    });

    // Llamadas iniciales
    getConceptosPagos();
    window.onload = updateTotals;
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>