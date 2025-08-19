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
        medioPagoSelect: document.getElementById('mediopago'),
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
        const tipoPago = elements.tipoPagoSelect.value;
        const isCuotaVisible = tipoPago === TIPOS_PAGO.soloCuota || tipoPago === TIPOS_PAGO.ambas;
        const isPenalidadVisible = tipoPago === TIPOS_PAGO.soloPenalidad || tipoPago === TIPOS_PAGO.ambas;

        elements.pagoCuotaGroup.classList.toggle('hidden', !isCuotaVisible);
        elements.pagoPenalidadGroup.classList.toggle('hidden', !isPenalidadVisible);

        elements.amortizacionCuotaInput.disabled = !isCuotaVisible;
        elements.amortizacionPenalidadInput.disabled = !isPenalidadVisible;


        elements.numeroTransaccionCuotaGroup.classList.toggle('hidden', !isCuotaVisible);

        if (isPenalidadVisible) {
            elements.amortizacionPenalidadInput.setAttribute('readonly', true);
            elements.amortizacionPenalidadInput.style.backgroundColor = '#BDB7B7';
            elements.numeroTransaccionPenalidadGroup.classList.remove('hidden');
        } else {
            elements.amortizacionPenalidadInput.removeAttribute('readonly');
            elements.amortizacionPenalidadInput.style.backgroundColor = '';
            elements.numeroTransaccionPenalidadGroup.classList.add('hidden');
        }
    }

    /**
     * Carga las cuentas bancarias desde la API y las llena en el select.
     */
    async function cargarCuentasBancarias() {
        try {
            const res = await fetch('/api/numcuentaspagos');
            const data = await res.json();
            elements.numeroCuentaSelect.innerHTML = '<option value="">Selecciona una cuenta</option>';
            data.forEach(cuenta => {
                elements.numeroCuentaSelect.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
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
                elements.amortizacionCuotaInput.classList.add('is-invalid');
                return false;
            }
            if (!validarAmortizacionCuota(monto, valorCuotaDeuda)) {
                marcarInput(elements.amortizacionCuotaInput, false);
                showToast(`La amortización no puede ser mayor a S/ ${valorCuotaDeuda.toFixed(2)}.`, 'WARNING', 1200);
                // elements.amortizacionCuotaInput.classList.add('is-invalid');
                return false;
            }
        }
        if (isPenalidad && (amortizacionPenalidad <= 0 || amortizacionPenalidad !== parseFloat(valorPenalidadDeuda))) {
            showToast(amortizacionPenalidad <= 0 ? 'Monto de penalidad inválido.' : `La penalidad debe pagarse completa: S/ ${valorPenalidadDeuda.toFixed(2)}.`, 'INFO', 1200);
            return false;
        }
        if (isCuota && elements.comprobanteCuotaInput.files.length === 0) {
            marcarInput(elements.comprobanteCuotaInput, false);
            showToast('Debe adjuntar el comprobante de la cuota.', 'WARNING', 1200);
            return false;
        }
        if (isPenalidad && elements.comprobantePenalidadInput.files.length === 0) {
            marcarInput(elements.comprobantePenalidadInput, false);
            showToast('Debe adjuntar el comprobante de la penalidad.', 'WARNING', 1200);
            return false;
        }

        if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
            if (elements.numeroCuentaSelect.value === '') {
                marcarInput(elements.numeroCuentaSelect, false);
                showToast('Debe seleccionar un núnmero de cuenta', 'WARNING', 1200);
                return false;
            }
        }

        if (isCuota) {
            const numeroTransaccionCuota = elements.numeroTransaccionInput.value.trim();
            if (numeroTransaccionCuota === '') {
                marcarInput(elements.numeroTransaccionInput, false);
                showToast('El número de operación de la cuota es obligatorio.', 'INFO', 1500);
                // elements.numeroTransaccionInput.classList.add('is-invalid');
            }
            if (!validarNumeroTransaccion(numeroTransaccionCuota)) {
                marcarInput(elements.numeroTransaccionInput, false);
                showToast('El número de operación de la cuota es inválido.', 'WARNING', 2000);
                // elements.numeroTransaccionInput.classList.add('is-invalid');
                return false;
            }
        }

        if (isPenalidad) {
            const numeroTransaccionPenalidad = elements.numeroTransaccionPenalidadInput.value.trim();
            if (numeroTransaccionPenalidad === '') {
                marcarInput(elements.numeroTransaccionPenalidadInput, false);
                showToast('El número de operación de la penalidad es obligatorio.', 'INFO', 1500);
                // elements.numeroTransaccionPenalidadInput.classList.add('is-invalid');
                return false;
            }
            if (!validarNumeroTransaccion(numeroTransaccionPenalidad)) {
                marcarInput(elements.numeroTransaccionPenalidadInput, false);
                showToast('El número de operación de la penalidad es inválido.', 'WARNING', 2000);
                // elements.numeroTransaccionPenalidadInput.classList.add('is-invalid');
                return false;
            }
        }

        if (fechaVacia(elements.fechaPagoInput.value)) {
            marcarInput(elements.fechaPagoInput, false);
            showToast('La fecha de pago es obligatoria.', 'INFO', 1200);
            // elements.fechaPagoInput.classList.add('is-invalid');
            return false;
        }
        if (fechaEsFutura(elements.fechaPagoInput.value)) {
            marcarInput(elements.fechaPagoInput, false);
            showToast('La fecha de pago no puede ser mayor a la actual.', 'WARNING', 1200);
            // elements.fechaPagoInput.classList.add('is-invalid');
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
        if (!await ask('¿Resgitrar el pago?','Caja')) return;
        // if (!confirm('¿Seguro de registrar el pago?')) return;
        elements.btnConfirmarPago.disabled = true;
        elements.btnConfirmarPago.classList.add('disabled', 'opacity-75');
        elements.btnConfirmarPago.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';

        const formData = new FormData(elements.formPago);
        formData.append('idcronograma', idCronogramaSeleccionado);
        if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
            formData.append('idcuentapago', elements.numeroCuentaSelect.value);
        }

        // Agregar el número de transacción de la penalidad al formData si existe un valor
        if (elements.amortizacionPenalidadInput.value > 0) {
            formData.append('numeroTransaccionPenalidad', elements.numeroTransaccionPenalidadInput.value);
        }

        setTimeout(async () => {
            try {
                const res = await fetch('/pago/cronograma', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message, 'SUCCESS', 1200);
                    elements.modalPago.hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message || 'Error al registrar el pago.', 'WARNING', 2000);
                }
            } catch (err) {
                console.error('Error de red o del servidor:', err);
                showToast('Error de red o del servidor. Intente nuevamente.', 'ERROR', 2000);
            } finally {
                elements.btnConfirmarPago.disabled = false;
                elements.btnConfirmarPago.classList.remove('disabled', 'opacity-75');
                elements.btnConfirmarPago.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmar Pago';
            }

        }, 2000);
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

    elements.medioPagoSelect.addEventListener('change', (e) => {
        const isTransferencia = e.target.value === MEDIOS_PAGO.transferenciaBancaria;
        elements.selectCuentas.classList.toggle('hidden', !isTransferencia);
        if (isTransferencia) {
            cargarCuentasBancarias();
        } else {
            elements.numeroCuentaSelect.innerHTML = '';
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
        showToast('GENERANDO EL PDF.....', 'INFO', 1500);
        setTimeout(() => {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape', 'mm', 'a4');

            doc.setFontSize(18);
            doc.text("Cronograma de Pagos", doc.internal.pageSize.getWidth() / 2, 15, {
                align: 'center'
            });

            const fechaHora = new Date().toLocaleDateString();
            doc.setFontSize(11);
            doc.text(`FECHA: ${fechaHora}`, doc.internal.pageSize.getWidth() - 10, 22, {
                align: 'right'
            });

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

            doc.autoTable({
                head,
                body: rows,
                startY: 25,
                styles: {
                    fontSize: 10,
                    halign: 'center'
                },
                headStyles: {
                    fillColor: [200, 200, 200],
                    textColor: 20,
                    fontStyle: 'bold'
                },
                margin: {
                    left: 10,
                    right: 10
                }
            });

            doc.save('cronograma_pagos.pdf');
            showToast('PDF GENERADO', 'SUCCESS', 1500);
        }, 1500);
    });
});