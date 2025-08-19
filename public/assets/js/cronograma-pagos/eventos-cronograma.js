import {
    validarNumeroTransaccion,
    marcarInput,
    validarComprobante,
    fechaVacia,
    fechaEsFutura
} from './helpers-cronograma.js';

export function configurarValidacionesFormulario(elements) {

    document.querySelectorAll('.numeros-transacciones').forEach((input) => {
        input.addEventListener('input', () => {
            if (input.value.trim().length > 0) {
                const valido = validarNumeroTransaccion(input.value.trim());
                marcarInput(input, valido);
            }
        });
    });


    // Validar fecha
    elements.fechaPagoInput.addEventListener('change', () => {
        const input = elements.fechaPagoInput;
        input.classList.remove('is-valid', 'is-invalid');

        if (fechaVacia(input.value) || fechaEsFutura(input.value)) {
            marcarInput(input, false);
        } else {
            marcarInput(input, true);
        }
    });

    // Validar comprobantes
    elements.comprobanteCuotaInput.addEventListener('change', () => {
        validarComprobante(elements.comprobanteCuotaInput);
    });

    elements.comprobantePenalidadInput.addEventListener('change', () => {
        validarComprobante(elements.comprobantePenalidadInput);
    });

    // Validar selección de cuenta
    elements.numeroCuentaSelect.addEventListener('change', () => {
        if (elements.numeroCuentaSelect.value === '') {
            marcarInput(elements.numeroCuentaSelect, false);
            showToast('Debe seleccionar un número de cuenta', 'INFO', 1200);
        }
    });

    
}
