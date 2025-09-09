import { validarAmortizacionCuota, validarNumeroTransaccion, fechaEsFutura, fechaVacia, marcarInput } from './helpers-cronograma.js';
import { TIPOS_PAGO, MEDIOS_PAGO } from "./constantes-cronograma.js";
import { configurarValidacionesFormulario } from './eventos-cronograma.js';

document.addEventListener('DOMContentLoaded', async () => {

    const totalItems = document.getElementById('tabla-body').dataset.totalItems;

    const config = {
        itemsPerPage: 10,
        totalItems: parseInt(totalItems),
    };
    config.totalPages = Math.ceil(config.totalItems / config.itemsPerPage);
    let currentPage = parseInt(localStorage.getItem('pageCronograma') || '1');
    let idCronogramaSeleccionado = null;
    let valorCuotaDeuda = 0;
    let valorPenalidadDeuda = 0;


    const elements = {

        // Nuevos elementos para diferente tipo pago de penalidad:
        diferenteMetodoPagoPenalidadCheck: document.getElementById('diferente-metodo-penalidad'), // CHECKBOX DE esOtroPagoPenalidad ?
        groupMedioPagoPenalidad: document.getElementById('group-medioPagoPenalidad'), // CONTENEDOR DE MEDIO PAGO DE PENALIDAD
        idCuentaPagoPenalidadSelect: document.getElementById('idcuentapagopenalidad'), // SELECT DE CUENTA DE PAGO PENALIDAD
        selectMedioPagoPenalidad: document.getElementById('mediopagopenalidad'), //  SELECT MEDIO DE PAGO DE PENALIDAD
        groupNumCuentaPenalidad: document.getElementById('group-select-numero-cuenta'), // CONTENEDOR DE NUMERO DE CUENTAS PENALIDAD. 
        groupMedioPagoCuota: document.getElementById('group-medioPagoCuota'), // CONTENEDOR DEL SELECT DE MEDIO DE PAGO DE cuota


        //-------------------------------------------------------------------------------
        tablaBody: document.getElementById('tabla-body'),
        infoPaginacion: document.getElementById('info-paginacion'),
        paginacion: document.getElementById('paginacion'),
        inputBuscar: document.getElementById('inputBuscar'),
        btnPdf: document.getElementById('btn-pdf'),
        btnConfirmarPago: document.getElementById('btn-confirmar-pago'),
        modalPago: new bootstrap.Modal(document.getElementById('modalPago')),
        formPago: document.getElementById('formPago'),
        numeroCuotaDisplay: document.getElementById('numero-cuota'),
        detalleCuotaInput: document.getElementById('detalle-cuota'),
        detallePenalidadInput: document.getElementById('detalle-penalidad'),
        detalleTotalDeudaInput: document.getElementById('detalle-total-deuda'),
        tipoPagoSelect: document.getElementById('tipoPago'),
        contenedorTipoPago: document.getElementById('contenedor-tipo-pago'),
        pagoCuotaGroup: document.getElementById('pagoCuota-group'),
        pagoPenalidadGroup: document.getElementById('pagoPenalidad-group'),
        numeroTransaccionPenalidadGroup: document.getElementById('group-numTransaccionPenalidad'),
        numeroTransaccionCuotaGroup: document.getElementById('group-numTransaccionCuota'),
        amortizacionCuotaInput: document.getElementById('amortizacionCuota'),
        amortizacionPenalidadInput: document.getElementById('amortizacionPenalidad'),
        comprobanteCuotaInput: document.getElementById('comprobanteCuota'),
        comprobantePenalidadInput: document.getElementById('comprobantePenalidad'),
        medioPagoSelect: document.getElementById('mediopago'),// cuota
        selectCuentas: document.querySelector('.select-cuentas'),
        numeroCuentaSelect: document.getElementById('idcuentapago'),
        numeroTransaccionInput: document.getElementById('numerotransaccion'),
        numeroTransaccionPenalidadInput: document.getElementById('numerotransaccion-penalidad'),
        fechaPagoInput: document.getElementById('fechapago'),
        observacionInput: document.getElementById('observacion'),
        contenedorInputMontoCuota: document.getElementById('contenedor-monto-cuota'),

    };

    /**
     * Gestiona la paginación de la tabla.
     * @param {number} page La página a mostrar.
     */
    function showPage(page) {
        currentPage = page;
        localStorage.setItem('pageCronograma', currentPage);

        const rows = Array.from(elements.tablaBody.querySelectorAll('tr'));
        rows.forEach(row => row.style.display = 'none');

        const startIndex = (currentPage - 1) * config.itemsPerPage;
        const endIndex = Math.min(startIndex + config.itemsPerPage, config.totalItems);

        for (let i = startIndex; i < endIndex; i++) {
            if (rows[i]) rows[i].style.display = '';
        }

        updatePagina(startIndex, endIndex);
    }

    /**
     * Actualiza los elementos de la interfaz de usuario de paginación.
     * @param {number} startItem Índice inicial del elemento.
     * @param {number} endItem Índice final del elemento.
     */
    function updatePagina(startItem, endItem) {
        elements.infoPaginacion.innerHTML = `Mostrando <span class="fw-bold">${startItem + 1}-${endItem}</span> de <span class="fw-bold">${config.totalItems}</span> cuotas`;

        const pageItems = elements.paginacion.querySelectorAll('.page-item');
        pageItems.forEach(item => item.classList.remove('active'));

        const activeLink = elements.paginacion.querySelector(`.page-link[data-page="${currentPage}"]`);
        if (activeLink) activeLink.parentElement.classList.add('active');

        document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
        document.getElementById('next-page').classList.toggle('disabled', currentPage === config.totalPages);

        if (currentPage < config.totalPages) {
            document.getElementById('next-page').querySelector('.page-link').dataset.page = currentPage + 1;
        }
        if (currentPage > 1) {
            document.getElementById('prev-page').querySelector('.page-link').dataset.page = currentPage - 1;
        }
    }

    /**
     * Muestra u oculta los campos de pago en el modal según el tipo de pago seleccionado.
     */
    function updateSelectTipoPago() {
        elements.numeroTransaccionInput.value = '';
        elements.numeroTransaccionPenalidadInput.value = '';
        elements.selectMedioPagoPenalidad.value = '';
        elements.medioPagoSelect.value = '';
        const tipoPago = elements.tipoPagoSelect.value;
        const isCuotaVisible = tipoPago === TIPOS_PAGO.soloCuota || tipoPago === TIPOS_PAGO.ambas;
        const isPenalidadVisible = tipoPago === TIPOS_PAGO.soloPenalidad || tipoPago === TIPOS_PAGO.ambas;
        const isPenalidadFieldsVisible = tipoPago === TIPOS_PAGO.ambas || tipoPago === TIPOS_PAGO.soloPenalidad;

        // Grupos principales
        elements.pagoCuotaGroup.classList.toggle('hidden', !isCuotaVisible);
        elements.pagoPenalidadGroup.classList.toggle('hidden', !isPenalidadVisible);

        // Deshabilitar inputs
        elements.amortizacionCuotaInput.disabled = !isCuotaVisible;
        elements.amortizacionPenalidadInput.disabled = !isPenalidadVisible;
        elements.numeroTransaccionCuotaGroup.classList.toggle('hidden', !isCuotaVisible);

        elements.groupMedioPagoCuota.classList.toggle('hidden', !isCuotaVisible);


        elements.groupMedioPagoPenalidad.classList.toggle('hidden', !isPenalidadFieldsVisible);
        elements.numeroTransaccionPenalidadGroup.classList.toggle('hidden', !isPenalidadFieldsVisible);


        if (!isPenalidadFieldsVisible || elements.selectMedioPagoPenalidad.value !== MEDIOS_PAGO.transferenciaBancaria) {
            elements.groupNumCuentaPenalidad.classList.add('hidden');
        }


        if (isPenalidadVisible) {
            elements.amortizacionPenalidadInput.setAttribute('readonly', true);
            elements.amortizacionPenalidadInput.style.backgroundColor = '#BDB7B7';
        } else {
            elements.amortizacionPenalidadInput.removeAttribute('readonly');
            elements.amortizacionPenalidadInput.style.backgroundColor = '';
        }
    }



    /**
     * Carga las cuentas bancarias desde la API y las llena en el select.
     */
    async function cargarCuentasBancarias(selectElement) {
        try {
            const res = await fetch('/api/numcuentaspagos');
            const data = await res.json();
            selectElement.innerHTML = '<option value="">Selecciona una cuenta</option>';
            data.forEach(cuenta => {
                selectElement.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
            });
        } catch (error) {
            showToast('Error al cargar cuentas bancarias.', 'ERROR', 2000);
        }
    }

    /**
     * Valida los datos del formulario antes del envío.
     * @returns {boolean} True si los datos son válidos, de lo contrario, false.
     */
    function validarForm() {
        if (!idCronogramaSeleccionado) {
            showToast('No se seleccionó ninguna cuota.', 'INFO', 1200);
            return false;
        }

        const tipoPago = elements.tipoPagoSelect.value;
        const amortizacionCuota = parseFloat(elements.amortizacionCuotaInput.value) || 0;
        const amortizacionPenalidad = parseFloat(elements.amortizacionPenalidadInput.value) || 0;

        const isCuota = tipoPago === TIPOS_PAGO.soloCuota || (tipoPago === TIPOS_PAGO.ambas && amortizacionCuota > 0);
        const isPenalidad = tipoPago === TIPOS_PAGO.soloPenalidad || (tipoPago === TIPOS_PAGO.ambas && amortizacionPenalidad > 0);

        if (isCuota) {
            const monto = parseFloat(elements.amortizacionCuotaInput.value);
            if (monto <= 0) {
                showToast('Monto de cuota inválido.', 'WARNING', 1200);
                marcarInput(elements.amortizacionCuotaInput, false);
                return false;
            }
            if (!validarAmortizacionCuota(monto, valorCuotaDeuda)) {
                marcarInput(elements.amortizacionCuotaInput, false);
                showToast(`La amortización no puede ser mayor a S/ ${valorCuotaDeuda.toFixed(2)}.`, 'WARNING', 1200);
                return false;
            }
        }

        if (isPenalidad && (amortizacionPenalidad <= 0 || amortizacionPenalidad !== parseFloat(valorPenalidadDeuda))) {
            showToast(amortizacionPenalidad <= 0 ? 'Monto de penalidad inválido.' : `La penalidad debe pagarse completa: S/ ${valorPenalidadDeuda.toFixed(2)}.`, 'INFO', 1200);
            return false;
        }

        // Validación de comprobante de CUOTA solo si el medio de pago NO es efectivo
        if (isCuota && elements.medioPagoSelect.value !== MEDIOS_PAGO.efectivo && elements.comprobanteCuotaInput.files.length === 0) {
            marcarInput(elements.comprobanteCuotaInput, false);
            showToast('Debe adjuntar el comprobante de la cuota.', 'WARNING', 1200);
            return false;
        }

        // Validación de comprobante de PENALIDAD solo si el medio de pago NO es efectivo
        if (isPenalidad && elements.selectMedioPagoPenalidad.value !== MEDIOS_PAGO.efectivo && elements.comprobantePenalidadInput.files.length === 0) {
            marcarInput(elements.comprobantePenalidadInput, false);
            showToast('Debe adjuntar el comprobante de la penalidad.', 'WARNING', 1200);
            return false;
        }

        if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
            if (elements.numeroCuentaSelect.value === '') {
                marcarInput(elements.numeroCuentaSelect, false);
                showToast('Debe seleccionar un número de cuenta', 'WARNING', 1200);
                return false;
            }
        }

        if (isCuota) {
            const numeroTransaccionCuota = elements.numeroTransaccionInput.value.trim();
            if (elements.medioPagoSelect.value !== MEDIOS_PAGO.efectivo && numeroTransaccionCuota === '') {
                marcarInput(elements.numeroTransaccionInput, false);
                showToast('El número de operación de la cuota es obligatorio.', 'INFO', 1500);
                return false;
            }
            if (numeroTransaccionCuota !== '' && !validarNumeroTransaccion(numeroTransaccionCuota)) {
                marcarInput(elements.numeroTransaccionInput, false);
                showToast('El número de operación de la cuota es inválido.', 'WARNING', 2000);
                return false;
            }
        }

        if (isPenalidad) {
            const numeroTransaccionPenalidad = elements.numeroTransaccionPenalidadInput.value.trim();
            if (elements.selectMedioPagoPenalidad.value !== MEDIOS_PAGO.efectivo && numeroTransaccionPenalidad === '') {
                marcarInput(elements.numeroTransaccionPenalidadInput, false);
                showToast('El número de operación de la penalidad es obligatorio.', 'INFO', 1500);
                return false;
            }
            if (numeroTransaccionPenalidad !== '' && !validarNumeroTransaccion(numeroTransaccionPenalidad)) {
                marcarInput(elements.numeroTransaccionPenalidadInput, false);
                showToast('El número de operación de la penalidad es inválido.', 'WARNING', 2000);
                return false;
            }
        }

        if (fechaVacia(elements.fechaPagoInput.value)) {
            marcarInput(elements.fechaPagoInput, false);
            showToast('La fecha de pago es obligatoria.', 'INFO', 1200);
            return false;
        }

        if (fechaEsFutura(elements.fechaPagoInput.value)) {
            marcarInput(elements.fechaPagoInput, false);
            showToast('La fecha de pago no puede ser mayor a la actual.', 'WARNING', 1200);
            return false;
        }

        return true;
    }


    configurarValidacionesFormulario(elements);

    elements.amortizacionCuotaInput.addEventListener('input', () => {

        elements.amortizacionCuotaInput.classList.remove('is-valid', 'is-invalid');
        const monto = parseFloat(elements.amortizacionCuotaInput.value);
        const containerInavlidText = document.getElementById('invalid-amortizacionCuota');
        containerInavlidText.textContent = '';
        if (!isNaN(monto) && monto > 0) {
            if (monto <= valorCuotaDeuda) {
                marcarInput(elements.amortizacionCuotaInput, true);
                //   elements.amortizacionCuotaInput.classList.add('is-valid');
            } else {
                marcarInput(elements.amortizacionCuotaInput, false);
                //   elements.amortizacionCuotaInput.classList.add('is-invalid');
                containerInavlidText.textContent = `Debe ser menor o igual al saldo pendiente de la cuota: S/${valorCuotaDeuda.toFixed(2)}`;
            }
        } else if (elements.amortizacionCuotaInput.value.trim().length > 0) {
            marcarInput(elements.amortizacionCuotaInput, false);
            //   elements.amortizacionCuotaInput.classList.add('is-invalid');
            containerInavlidText.textContent = 'Debe ser un monto válido y mayor a 0.';
        }
    });


    /**
     * Envía los datos del formulario
     */

    async function submitForm() {
        // Confirmación al usuario antes de proceder con el envío
        if (!await ask('¿Estás seguro de registrar este pago?', 'Confirmar')) {
            return;
        }

        // Deshabilitar el botón de confirmación y mostrar un indicador de carga
        elements.btnConfirmarPago.disabled = true;
        elements.btnConfirmarPago.classList.add('disabled', 'opacity-75');
        elements.btnConfirmarPago.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';

        // Crear un objeto FormData con los datos del formulario
        const formData = new FormData(elements.formPago);
        formData.append('idcronograma', idCronogramaSeleccionado);

        // Lógica para el pago de la CUOTA
        if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
            formData.append('idcuentapago', elements.numeroCuentaSelect.value);
        }

        // Lógica para el pago de la PENALIDAD 
        // Solo se procesan los campos de penalidad si el monto es mayor que 0
        if (elements.amortizacionPenalidadInput.value > 0) {
            // Se añade explícitamente el medio de pago de la penalidad al FormData
            formData.append('mediopagopenalidad', elements.selectMedioPagoPenalidad.value);

            // Si el pago de penalidad es una transferencia, se añade la cuenta
            if (elements.selectMedioPagoPenalidad.value === MEDIOS_PAGO.transferenciaBancaria) {
                formData.append('idcuentapagopenalidad', elements.idCuentaPagoPenalidadSelect.value);
            }

            // Se añade el número de transacción de la penalidad
            formData.append('numeroTransaccionPenalidad', elements.numeroTransaccionPenalidadInput.value);
        }

        let delay = 1000;
        const delayPromise = new Promise(resolve => setTimeout(resolve, delay));


        try {

            const [res] = await Promise.all([

                fetch('/pago/cronograma', {
                    method: 'POST',
                    body: formData
                }),
                delayPromise
            ]);


            const data = await res.json();

            if (data.debug_info) {
                console.error("Mensaje de Depuración:", data.debug_info);
            }


            if (data.success) {
                showToast(data.message, 'SUCCESS', 1200);
                setTimeout(() => {
                    elements.modalPago.hide();
                    setTimeout(() => location.reload(), 500);
                }, 500);
            } else {

                showToast(data.message || 'Error al registrar el pago.', 'WARNING', 2000);
                elements.btnConfirmarPago.disabled = false;
                elements.btnConfirmarPago.classList.remove('disabled', 'opacity-75');
                elements.btnConfirmarPago.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmar Pago';
            }
        } catch (err) {

            console.error('Error de red o del servidor:', err);
            showToast('Error de red o del servidor. Intenta de nuevo.', 'ERROR', 2000);
        }

    }


    /**
     * Filtra las filas de la tabla por el término de búsqueda.
     * @param {string} searchTerm El término de búsqueda.
     */
    function filterTable(searchTerm) {
        const rows = elements.tablaBody.querySelectorAll('tr');
        let found = false;
        rows.forEach(row => {
            const cuotaNum = row.cells[0].textContent.trim().toLowerCase();
            const showRow = cuotaNum.includes(searchTerm);
            row.style.display = showRow ? '' : 'none';
            if (showRow) found = true;
        });

        if (searchTerm === '') {
            showPage(currentPage);
        } else {
            elements.infoPaginacion.innerHTML = found ?
                `Mostrando resultados para: <span class="fw-bold">${searchTerm}</span>` :
                `No se encontraron resultados para: <span class="fw-bold">${searchTerm}</span>`;
        }
    }


    // Inicialización de la tabla
    showPage(currentPage);

    // Click en botones de pagar
    elements.tablaBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-pagar');
        if (!btn) return;

        const saldoCuota = parseFloat(btn.dataset.saldoCuota) || 0;
        const saldoPenalidad = parseFloat(btn.dataset.saldoPenalidad) || 0;

        idCronogramaSeleccionado = btn.dataset.idcronograma;
        valorCuotaDeuda = saldoCuota;
        valorPenalidadDeuda = saldoPenalidad;

        elements.amortizacionCuotaInput.value = saldoCuota.toFixed(2);
        elements.amortizacionPenalidadInput.value = saldoPenalidad.toFixed(2);
        elements.detalleCuotaInput.value = saldoCuota.toFixed(2);
        elements.detallePenalidadInput.value = saldoPenalidad.toFixed(2);
        elements.detalleTotalDeudaInput.value = (saldoCuota + saldoPenalidad).toFixed(2);
        elements.numeroCuotaDisplay.textContent = `Está a punto de registrar el pago de la cuota N° ${btn.dataset.cuota}`;

        const mostrarSoloPenalidad = saldoCuota === 0 && saldoPenalidad > 0;
        const mostrarSoloCuota = saldoCuota > 0 && saldoPenalidad === 0;
        const mostrarSelectCompleto = saldoCuota > 0 && saldoPenalidad > 0;

        elements.contenedorTipoPago.classList.toggle('hidden', !mostrarSelectCompleto);
        configurarValidacionesFormulario(elements, valorCuotaDeuda);

        if (mostrarSelectCompleto) {
            elements.tipoPagoSelect.innerHTML = `
                <option value='${TIPOS_PAGO.ambas}'>Cuota y Penalidad</option>
                <option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>
                <option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>
            `;
            elements.tipoPagoSelect.value = TIPOS_PAGO.ambas;
        } else if (mostrarSoloPenalidad) {
            elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>`;
            elements.tipoPagoSelect.value = TIPOS_PAGO.soloPenalidad;
        } else if (mostrarSoloCuota) {
            elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>`;
            elements.tipoPagoSelect.value = TIPOS_PAGO.soloCuota;
        }

        updateSelectTipoPago();
    });


    // Eventos del modal de pago
    elements.tipoPagoSelect.addEventListener('change', updateSelectTipoPago);


    // CUOTA
    elements.medioPagoSelect.addEventListener('change', (e) => {
        elements.numeroTransaccionInput.value = '';
        console.info('MEDIO DE PAGO CUOTA: ', e.target.value);
        const isTransferencia = e.target.value === MEDIOS_PAGO.transferenciaBancaria;
        elements.selectCuentas.classList.toggle('hidden', !isTransferencia);
        if (isTransferencia) {
            cargarCuentasBancarias(elements.numeroCuentaSelect);
        } else {
            elements.numeroCuentaSelect.innerHTML = '';
        }
    });

    // PENALIDAD.
    elements.selectMedioPagoPenalidad.addEventListener('change', (e) => {
        elements.numeroTransaccionPenalidadInput.value = '';
        console.error('MEDIO DE PAGO PENALIDAD: ', e.target.value);
        const isTransferencia = e.target.value === MEDIOS_PAGO.transferenciaBancaria;
        elements.groupNumCuentaPenalidad.classList.toggle('hidden', !isTransferencia);
        if (isTransferencia) {
            cargarCuentasBancarias(elements.idCuentaPagoPenalidadSelect);

        } else {
            elements.idCuentaPagoPenalidadSelect.innerHTML = '';
        }
    });




    elements.formPago.addEventListener('submit', (e) => {
        e.preventDefault();
        if (validarForm()) {
            submitForm();
        }
    });

    
    // Eventos de paginación y búsqueda
    elements.paginacion.addEventListener('click', function (e) {
        const target = e.target.closest('.page-link');
        if (!target || target.parentElement.classList.contains('disabled')) return;
        e.preventDefault();
        const newPage = parseInt(target.dataset.page);
        if (newPage && newPage !== currentPage) {
            showPage(newPage);
        }
    });

    elements.inputBuscar.addEventListener('input', (e) => {
        filterTable(e.target.value.trim().toLowerCase());
    });

    // Evento para limpiar la paginación al cambiar de vista
    document.querySelector('a[href="/caja/"]').addEventListener('click', function () {
        localStorage.removeItem('pageCronograma');
        elements.inputBuscar.value = '';
        showPage(1);
    });

    



    // Generar PDF
    elements.btnPdf.addEventListener('click', () => {
        setTimeout(() => {
            
            const logo = "data:image/png;base64,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";

            const fechaHora = new Date().toLocaleDateString();
            const head = [
                ["#", "Fecha Vencimiento", "Interés", "Abono Capital", "Valor Cuota", "Amortización", "Restante", "Saldo Capital", "Estado"]
            ];
            const rows = Array.from(elements.tablaBody.querySelectorAll('tr')).map(tr => {
                const tds = tr.querySelectorAll('td');
                return [
                    tds[0].innerText.trim(),
                    tds[1].querySelector('span.fw-bold')?.innerText.trim() || '',
                    tds[2].innerText.trim(),
                    tds[3].innerText.trim(),
                    tds[4].innerText.trim(),
                    tds[5].innerText.trim(),
                    tds[6].innerText.trim(),
                    tds[7].innerText.trim(),
                    tds[8].innerText.trim().split('-')[0]
                ];
            }).filter(row => row.length === 9);

            // Construir body para pdfMake
            const body = [
                head[0].map(h => ({ text: h, style: 'tableHeader', alignment: 'center' }))
            ];
            rows.forEach(row => {
                body.push(row.map((cell, idx) => {
                    // Alinear numéricos a la derecha, el resto al centro
                    if (idx > 1 && idx < 8) {
                        return { text: cell, alignment: 'right' };
                    }
                    return { text: cell, alignment: 'center' };
                }));
            });

            const documento = {
                pageSize: 'A4',
                pageOrientation: 'landscape',
                pageMargins: [40, 25, 25, 25],
                defaultStyle: {
                    fontSize: 9,
                },
                content: [
                    {
                        columns: [
                            {
                                image: logo,
                                width: 80,
                                alignment: 'left'
                            },
                            {
                                stack: [
                                    { text: 'YONDA & GRUPO HUARACA E.I.R.L', fontSize: 12, bold: true, color: '#2c3e50'},
                                    { text: 'RUC: 20609396866', fontSize: 10, margin: [0, 2, 0, 0],bold:true },
                                ],
                                alignment: 'right',
                                margin: [10, 0, 0, 0]
                            }
                        ],
                        margin: [0, 0, 0, 10]
                    },
                    {
                        text: 'CRONOGRAMA DE PAGOS',
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10],
                        decoration: 'underline',
                        fontSize: 14,
                        bold: true
                    },
                    {
                        text: `FECHA: ${fechaHora}`,
                        alignment: 'right',
                        fontSize: 10,
                        margin: [0, 0, 0, 10]
                    },
                    {
                        style: 'tableCronograma',
                        table: {
                            headerRows: 1,
                            widths: [30, '*', '*', '*', '*', '*', '*', '*', '*'],
                            body: body
                        },
                        layout: {
                            fillColor: function (rowIndex) {
                                return rowIndex === 0 ? '#e0e0e0' : null;
                            }
                        }
                    }
                ],
                styles: {
                    subheader: { bold: true, fontSize: 10 },
                    tableCronograma: { margin: [0, 5, 0, 0] },
                    tableHeader: { bold: true, fontSize: 10, fillColor: '#e0e0e0' }
                }
            };

            pdfMake.createPdf(documento).open();
            showToast('PDF GENERADO', 'SUCCESS', 1200);
        }, 1500);
    });

});