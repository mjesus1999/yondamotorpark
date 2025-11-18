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

        btnExcel: document.getElementById('btn-excel'),
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


    elements.medioPagoSelect.addEventListener('change', (e) => {

        if (e.target.value.toUpperCase() === MEDIOS_PAGO.efectivo.toUpperCase()) {
            elements.numeroTransaccionInput.disabled = true;
            elements.comprobanteCuotaInput.disabled = true;

        } else {
            elements.numeroTransaccionInput.disabled = false;
            elements.comprobanteCuotaInput.disabled = false;
        }
    });

    elements.selectMedioPagoPenalidad.addEventListener('change', (e) => {

        if (e.target.value.toUpperCase() === MEDIOS_PAGO.efectivo.toUpperCase()) {
            elements.numeroTransaccionPenalidadInput.disabled = true;
            elements.comprobantePenalidadInput.disabled = true;
        } else {
            elements.numeroTransaccionPenalidadInput.disabled = false;
            elements.comprobantePenalidadInput.disabled = false;
        }
    });



    /**
 * Calcula el prorrateo de capital e interés cuando hay un pago parcial
 * @param {number} montoPagado - Monto que el cliente está pagando
 * @param {number} valorCuotaTotal - Valor total de la cuota
 * @param {number} interesCuota - Monto del interés de la cuota
 * @param {number} capitalCuota - Monto del capital de la cuota
 * @returns {Object} - {capital: number, interes: number}
 */
    function calcularProrrateo(montoPagado, valorCuotaTotal, interesCuota, capitalCuota) {
        if (valorCuotaTotal <= 0 || montoPagado <= 0) {
            return { capital: 0, interes: 0 };
        }

        // Calcular proporción del pago
        const proporcion = montoPagado / valorCuotaTotal;

        // Prorratear interés y capital
        const interesPagado = interesCuota * proporcion;
        const capitalPagado = capitalCuota * proporcion;

        return {
            interes: parseFloat(interesPagado.toFixed(2)),
            capital: parseFloat(capitalPagado.toFixed(2))
        };
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
        const containerInvalidText = document.getElementById('invalid-amortizacionCuota');
        containerInvalidText.textContent = '';

        const modal = document.getElementById('modalPago');
        const interesCuota = parseFloat(modal.dataset.interesCuota) || 0;
        const capitalCuota = parseFloat(modal.dataset.capitalCuota) || 0;

        if (!isNaN(monto) && monto > 0) {
            if (monto <= valorCuotaDeuda) {
                marcarInput(elements.amortizacionCuotaInput, true);

                //  CALCULAR Y MOSTRAR PRORRATEO
                const prorrateo = calcularProrrateo(monto, valorCuotaDeuda, interesCuota, capitalCuota);

                console.log(` Pago ingresado: S/ ${monto.toFixed(2)}`);
                console.log(` Interés prorrateado: S/ ${prorrateo.interes.toFixed(2)}`);
                console.log(`Capital prorrateado: S/ ${prorrateo.capital.toFixed(2)}`);
                console.log(`Total: S/ ${(prorrateo.interes + prorrateo.capital).toFixed(2)}`);

            } else {
                marcarInput(elements.amortizacionCuotaInput, false);
                containerInvalidText.textContent = `Debe ser menor o igual al saldo pendiente de la cuota: S/${valorCuotaDeuda.toFixed(2)}`;
            }
        } else if (elements.amortizacionCuotaInput.value.trim().length > 0) {
            marcarInput(elements.amortizacionCuotaInput, false);
            containerInvalidText.textContent = 'Debe ser un monto válido y mayor a 0.';
        }
    });

    /**
     * Envía los datos del formulario
     */

    /**
      * Envía los datos del formulario con VALORES ESTÁTICOS DE PRUEBA.
      */
    async function submitForm() {
        if (!await ask('¿Estás seguro de registrar este pago?', 'Confirmar')) {
            return;
        }

        elements.btnConfirmarPago.disabled = true;
        elements.btnConfirmarPago.classList.add('disabled', 'opacity-75');
        elements.btnConfirmarPago.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';

        const formData = new FormData(elements.formPago);
        formData.append('idcronograma', idCronogramaSeleccionado);

        
        const modal = document.getElementById('modalPago');
        const interesCuota = parseFloat(modal.dataset.interesCuota) || 0;
        const capitalCuota = parseFloat(modal.dataset.capitalCuota) || 0;
        const montoPagado = parseFloat(elements.amortizacionCuotaInput.value) || 0;

        const prorrateo = calcularProrrateo(montoPagado, valorCuotaDeuda, interesCuota, capitalCuota);

        console.log("ENVIANDO AL BACKEND:");
        console.log(`   Monto pagado: S/ ${montoPagado.toFixed(2)}`);
        console.log(`   Capital prorrateado: S/ ${prorrateo.capital.toFixed(2)}`);
        console.log(`   Interés prorrateado: S/ ${prorrateo.interes.toFixed(2)}`);

        formData.append('total_capital_prorrateado', prorrateo.capital.toFixed(2));
        formData.append('total_interes_prorrateado', prorrateo.interes.toFixed(2));

        // Lógica para el pago de la CUOTA (Cuentas bancarias)
        if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
            formData.append('idcuentapago', elements.numeroCuentaSelect.value);
        }

        // Lógica para el pago de la PENALIDAD 
        if (elements.amortizacionPenalidadInput.value > 0) {
            formData.append('mediopagopenalidad', elements.selectMedioPagoPenalidad.value);

            if (elements.selectMedioPagoPenalidad.value === MEDIOS_PAGO.transferenciaBancaria) {
                formData.append('idcuentapagopenalidad', elements.idCuentaPagoPenalidadSelect.value);
            }

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

                if (data.enlace_pdf) {
                    console.log("Abriendo Boleta:", data.enlace_pdf);
                    window.open(data.enlace_pdf, '_blank');
                }

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

            elements.btnConfirmarPago.disabled = false;
            elements.btnConfirmarPago.classList.remove('disabled', 'opacity-75');
            elements.btnConfirmarPago.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmar Pago';
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
    // Click en botones de pagar
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-pagar');

        if (btn) {
            const saldoCuota = parseFloat(btn.dataset.saldoCuota) || 0;
            const saldoPenalidad = parseFloat(btn.dataset.saldoPenalidad) || 0;

            //  CAPTURAR INTERÉS Y CAPITAL 
            const interesCuota = parseFloat(btn.dataset.interes) || 0;
            const capitalCuota = parseFloat(btn.dataset.abonocapital) || 0;

            const modal = document.getElementById('modalPago');
            if (modal) {
                idCronogramaSeleccionado = btn.dataset.idcronograma;
                valorCuotaDeuda = saldoCuota;
                valorPenalidadDeuda = saldoPenalidad;

                // GUARDAR VALORES EN EL MODAL
                modal.dataset.interesCuota = interesCuota;
                modal.dataset.capitalCuota = capitalCuota;
                modal.dataset.valorCuotaTotal = saldoCuota;

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
            }
        }
    });

    // elements.tablaBody.addEventListener('click', function (e) {
    //     const btn = e.target.closest('.btn-pagar');
    //     if (!btn) return;

    //     const saldoCuota = parseFloat(btn.dataset.saldoCuota) || 0;
    //     const saldoPenalidad = parseFloat(btn.dataset.saldoPenalidad) || 0;

    //     idCronogramaSeleccionado = btn.dataset.idcronograma;
    //     valorCuotaDeuda = saldoCuota;
    //     valorPenalidadDeuda = saldoPenalidad;

    //     elements.amortizacionCuotaInput.value = saldoCuota.toFixed(2);
    //     elements.amortizacionPenalidadInput.value = saldoPenalidad.toFixed(2);
    //     elements.detalleCuotaInput.value = saldoCuota.toFixed(2);
    //     elements.detallePenalidadInput.value = saldoPenalidad.toFixed(2);
    //     elements.detalleTotalDeudaInput.value = (saldoCuota + saldoPenalidad).toFixed(2);
    //     elements.numeroCuotaDisplay.textContent = `Está a punto de registrar el pago de la cuota N° ${btn.dataset.cuota}`;

    //     const mostrarSoloPenalidad = saldoCuota === 0 && saldoPenalidad > 0;
    //     const mostrarSoloCuota = saldoCuota > 0 && saldoPenalidad === 0;
    //     const mostrarSelectCompleto = saldoCuota > 0 && saldoPenalidad > 0;

    //     elements.contenedorTipoPago.classList.toggle('hidden', !mostrarSelectCompleto);
    //     configurarValidacionesFormulario(elements, valorCuotaDeuda);

    //     if (mostrarSelectCompleto) {
    //         elements.tipoPagoSelect.innerHTML = `
    //             <option value='${TIPOS_PAGO.ambas}'>Cuota y Penalidad</option>
    //             <option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>
    //             <option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>
    //         `;
    //         elements.tipoPagoSelect.value = TIPOS_PAGO.ambas;
    //     } else if (mostrarSoloPenalidad) {
    //         elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>`;
    //         elements.tipoPagoSelect.value = TIPOS_PAGO.soloPenalidad;
    //     } else if (mostrarSoloCuota) {
    //         elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>`;
    //         elements.tipoPagoSelect.value = TIPOS_PAGO.soloCuota;
    //     }

    //     updateSelectTipoPago();
    // });


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

        const logo = window.logoBase64;
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
            pageOrientation: 'portrait',
            pageMargins: [40, 25, 25, 25],
            defaultStyle: {
                fontSize: 7,
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
                                { text: 'YONDA & GRUPO HUARACA E.I.R.L', fontSize: 12, bold: true, color: '#2c3e50' },
                                { text: 'RUC: 20609396866', fontSize: 10, margin: [0, 2, 0, 0], bold: true },
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
                    fontSize: 13,
                    bold: true
                },

                {
                    style: 'tableCronograma',
                    table: {
                        headerRows: 1,
                        widths: ['*', '*', 60, '*', '*', '*', '*', '*', 60],
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
                tableHeader: { bold: true, fillColor: '#e0e0e0' }
            }
        };

        pdfMake.createPdf(documento).open();
        // showToast('PDF GENERADO', 'SUCCESS', 1200);

    });



    elements.btnExcel.addEventListener('click', async () => {

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Reporte de Pagos');


        const headerStyle = {
            font: { bold: true, color: { argb: 'FF2C3E50' } },
            fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEAECEE' } },
            alignment: { horizontal: 'center' }
        };
        const titleStyle = {
            font: { bold: true, size: 13, color: { argb: 'FF2C3E50' } },
            alignment: { horizontal: 'center' }
        };
        const currencyStyle = {
            numFmt: 'S/ #,##0.00'
        };
        const dateStyle = {
            numFmt: 'dd/mm/yyyy'
        };


        const titleRow = worksheet.addRow(['CRONOGRAMA DE PAGOS']);
        titleRow.getCell(1).style = titleStyle;
        worksheet.mergeCells('A1:I1');
        titleRow.height = 20;


        const headers = ["#", "Fecha Vencimiento", "Interés", "Abono Capital", "Valor Cuota", "Amortización", "Restante", "Saldo Capital", "Estado"];
        worksheet.addRow(headers).eachCell(cell => {
            Object.assign(cell, { style: headerStyle });
        });

        const rows = Array.from(elements.tablaBody.querySelectorAll('tr')).map(tr => {
            const tds = tr.querySelectorAll('td');
            console.log('tds: ', tds[1].innerText.trim());
            const rowData = [
                parseInt(tds[0].innerText.trim()), // #
                tds[1].querySelector('span.fw-bold')?.innerText.trim() || '',
                parseFloat(tds[2].innerText.trim().replace('S/', '').replace(',', '')), // Interés
                parseFloat(tds[3].innerText.trim().replace('S/', '').replace(',', '')), // Abono Capital
                parseFloat(tds[4].innerText.trim().replace('S/', '').replace(',', '')), // Valor Cuota
                parseFloat(tds[5].innerText.trim().replace('S/', '').replace(',', '')), // Amortización
                parseFloat(tds[6].innerText.trim().replace('S/', '').replace(',', '')), // Restante
                parseFloat(tds[7].innerText.trim().replace('S/', '').replace(',', '')), // Saldo Capital
                tds[8].innerText.trim().split('-')[0] // Estado
            ];

            return rowData;
        });

        rows.forEach(row => {
            const newRow = worksheet.addRow(row);
            newRow.eachCell((cell, colNumber) => {

                if (colNumber >= 3 && colNumber <= 8) {
                    Object.assign(cell, { style: currencyStyle });
                }

                if (colNumber === 2) {
                    Object.assign(cell, { style: dateStyle });
                }
            });
        });


        worksheet.columns.forEach(column => {
            const lengths = column.values.map(v => v.toString().length);
            const maxLength = Math.max(...lengths.filter(v => typeof v === 'number'));
            column.width = maxLength < 10 ? 10 : maxLength + 2;
        });


        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'reporte-cronograma.xlsx';
        a.click();
        window.URL.revokeObjectURL(url);


    });


});