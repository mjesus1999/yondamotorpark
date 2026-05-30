export const TIPOS_PAGO = {
    soloCuota: 'soloCuota',
    soloPenalidad: 'soloPenalidad',
    ambas: 'ambas'
};

export const MEDIOS_PAGO = {
    efectivo: 'Efectivo',
    yape: 'Yape',
    transferenciaBancaria: 'Transferencia Bancaria',
    interbancario: 'Interbancario',
    plin: 'Plin'
};

export function medioRequiereCuenta(medio) {
    return medio === MEDIOS_PAGO.transferenciaBancaria || medio === MEDIOS_PAGO.interbancario;
}

/** @returns {'Cuenta'|'CCI'|null} */
export function tipoCuentaParaMedio(medio) {
    if (medio === MEDIOS_PAGO.transferenciaBancaria) return 'Cuenta';
    if (medio === MEDIOS_PAGO.interbancario) return 'CCI';
    return null;
}

// CONSTANTES PARA EL MAXIMO DE TAMAÑO DEL COMPROBANTE
export const MAX_FILE_SIZE_MB = 4; 
export const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;