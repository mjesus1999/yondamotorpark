export function crearBloqueFirma(titulo, linea1, linea2) {
    return {
        stack: [ 
            { text: '__________________________________________________________', style: 'lineaFirma' },
            { text: titulo.toUpperCase(), style: 'tituloFirma' },
            { text: (linea1 || '').toUpperCase(), style: 'textoFirma' },
            { text: (linea2 || ''), style: 'textoFirma' }
        ],
        alignment: 'center', 
        width: '50%' 
    };
}

export function formatNumber(numStr) {
    if (!numStr) return '0.00';
    const num = parseFloat(numStr);
    if (isNaN(num)) return '0.00';
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}

export function getMonedaSymbol(monedaCode) {
    return (monedaCode === 'PEN') ? 'S/ ' : '$ ';
}