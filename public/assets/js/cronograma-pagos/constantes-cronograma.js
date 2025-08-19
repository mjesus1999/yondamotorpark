export const TIPOS_PAGO = {
    soloCuota: 'soloCuota',
    soloPenalidad: 'soloPenalidad',
    ambas: 'ambas'
};

export const MEDIOS_PAGO = {
    efectivo: 'Efectivo',
    yape: 'Yape',
    transferenciaBancaria: 'Transferencia Bancaria',
    plin: 'Plin'
};

// CONSTANTES PARA EL MAXIMO DE TAMAÑO DEL COMPROBANTE
export const MAX_FILE_SIZE_MB = 4; 
export const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;