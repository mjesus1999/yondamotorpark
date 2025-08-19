import { MAX_FILE_SIZE_BYTES, MAX_FILE_SIZE_MB } from './constantes-cronograma.js';
/**
 * Valida la longitud del número de transacción.
 * @param {string} numeroTransaccion
 * @returns {boolean}
 */
export function validarNumeroTransaccion(numeroTransaccion) {
    const regex = /^\d{6,30}$/;
    return regex.test(numeroTransaccion);
}

/**
 * Valida si el monto de amortización de la cuota no excede la deuda.
 * @param {number} monto
 * @param {number} valorDeuda
 * @returns {boolean}
 */
export function validarAmortizacionCuota(monto, valorDeuda) {
    return !isNaN(monto) && monto > 0 && monto <= valorDeuda;
}

/**
 * Verifica si la fecha de pago está vacía.
 * @param {string} fecha
 * @returns {boolean}
 */
export function fechaVacia(fecha) {
    return !fecha;
}

/**
 * Verifica si la fecha de pago es futura.
 * @param {string} fecha
 * @returns {boolean}
 */
export function fechaEsFutura(fecha) {
    const fechaPagoDate = new Date(fecha);
    const hoy = new Date();
    const fechaPagoFormato = fechaPagoDate.toISOString().slice(0, 10);
    const hoyFormato = hoy.toISOString().slice(0, 10);
    return fechaPagoFormato > hoyFormato;
}


/***
 * Marca un input si es valido o no es valido
 * @param {HtmlElement} input del campo de entrada
 * @returns {boolean} retorna si es true o false
 */
export function marcarInput(input, valido) {
    input.classList.remove('is-valid', 'is-invalid');
    input.classList.add(valido ? 'is-valid' : 'is-invalid');
}

export function validarComprobante(input) {
    const feedbackElement = input.nextElementSibling;
    const file = input.files[0];

    if (!file) {
        marcarInput(input, false);
        feedbackElement.textContent = 'Debe adjuntar un archivo';
        showToast('Debe adjuntar un archivo', 'ERROR', 1200);
        return;
    }

    const fileType = file.type;
    const fileSize = file.size;

    const isImage = fileType.startsWith('image/');
    const isPdf = fileType === 'application/pdf';

    if (!isImage && !isPdf) {
        feedbackElement.textContent = 'Formato de archivo inválido. Solo se permiten imágenes o PDF';
        marcarInput(input, false);
        showToast('Formato de archivo inválido.', 'ERROR', 1500);
        return;
    }

    if (fileSize > MAX_FILE_SIZE_BYTES) {
        feedbackElement.textContent = `El tamaño del archivo no puede exceder los ${MAX_FILE_SIZE_MB}MB`;
        marcarInput(input, false);
        showToast(`El archivo es demasiado grande (máx. ${MAX_FILE_SIZE_MB}MB).`, 'ERROR', 2000);
        return;
    }

    feedbackElement.textContent = '';
    marcarInput(input, true);

}