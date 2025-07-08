/**
 * Sistema de Mapa Reutilizable
 * Usa OpenStreetMap + Leaflet para selección de ubicaciones
 */

class MapaSelector {
    constructor(options = {}) {
        this.options = {
            modalId: 'modalMapa',
            mapaId: 'mapa',
            btnMapaId: 'btn-mapa',
            btnBuscarId: 'btnBuscar',
            btnConfirmarId: 'btnConfirmarUbicacion',
            inputBuscarId: 'buscarDireccion',
            inputLatitudId: 'latitud',
            inputLongitudId: 'longitud',
            inputLatitudSeleccionadaId: 'latitudSeleccionada',
            inputLongitudSeleccionadaId: 'longitudSeleccionada',
            latInicial: -12.0464, // Lima, Perú
            lngInicial: -77.0428,
            zoomInicial: 13,
            ...options
        };

        this.mapa = null;
        this.marcador = null;
        this.coordenadasSeleccionadas = null;
        this.init();
    }

    init() {
        // Verificar que Leaflet esté cargado
        if (typeof L === 'undefined') {
            console.error('Leaflet no está cargado. Asegúrate de incluir la librería.');
            return;
        }

        this.bindEvents();
    }

    bindEvents() {
        // Botón para abrir el mapa
        const btnMapa = document.getElementById(this.options.btnMapaId);
        if (btnMapa) {
            btnMapa.addEventListener('click', () => this.abrirModal());
        }

        // Botón de búsqueda
        const btnBuscar = document.getElementById(this.options.btnBuscarId);
        if (btnBuscar) {
            btnBuscar.addEventListener('click', () => this.buscarDireccion());
        }

        // Botón de confirmar ubicación
        const btnConfirmar = document.getElementById(this.options.btnConfirmarId);
        if (btnConfirmar) {
            btnConfirmar.addEventListener('click', () => this.confirmarUbicacion());
        }

        // Búsqueda con Enter
        const inputBuscar = document.getElementById(this.options.inputBuscarId);
        if (inputBuscar) {
            inputBuscar.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.buscarDireccion();
                }
            });
        }
    }

    abrirModal() {
        const modal = new bootstrap.Modal(document.getElementById(this.options.modalId));
        modal.show();
        
        // Inicializar mapa después de que el modal esté visible
        setTimeout(() => {
            this.inicializarMapa();
        }, 300);
    }

    inicializarMapa() {
        if (this.mapa) {
            this.mapa.remove();
        }

        // Obtener coordenadas iniciales
        const inputLatitud = document.getElementById(this.options.inputLatitudId);
        const inputLongitud = document.getElementById(this.options.inputLongitudId);
        
        const latInicial = inputLatitud ? parseFloat(inputLatitud.value) || this.options.latInicial : this.options.latInicial;
        const lngInicial = inputLongitud ? parseFloat(inputLongitud.value) || this.options.lngInicial : this.options.lngInicial;

        // Crear mapa
        this.mapa = L.map(this.options.mapaId).setView([latInicial, lngInicial], this.options.zoomInicial);

        // Agregar capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.mapa);

        // Agregar marcador inicial si hay coordenadas válidas
        if (latInicial !== this.options.latInicial || lngInicial !== this.options.lngInicial) {
            this.marcador = L.marker([latInicial, lngInicial]).addTo(this.mapa);
            this.coordenadasSeleccionadas = { lat: latInicial, lng: lngInicial };
            this.actualizarCoordenadasSeleccionadas(latInicial, lngInicial);
        }

        // Evento para agregar marcador al hacer click
        this.mapa.on('click', (e) => {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            this.actualizarMarcador(lat, lng);
        });
    }

    actualizarMarcador(lat, lng) {
        // Remover marcador anterior
        if (this.marcador) {
            this.mapa.removeLayer(this.marcador);
        }

        // Agregar nuevo marcador
        this.marcador = L.marker([lat, lng]).addTo(this.mapa);
        this.coordenadasSeleccionadas = { lat, lng };
        this.actualizarCoordenadasSeleccionadas(lat, lng);
    }

    actualizarCoordenadasSeleccionadas(lat, lng) {
        const inputLatSeleccionada = document.getElementById(this.options.inputLatitudSeleccionadaId);
        const inputLngSeleccionada = document.getElementById(this.options.inputLongitudSeleccionadaId);
        
        if (inputLatSeleccionada) inputLatSeleccionada.value = lat.toFixed(6);
        if (inputLngSeleccionada) inputLngSeleccionada.value = lng.toFixed(6);
    }

    async buscarDireccion() {
        const inputBuscar = document.getElementById(this.options.inputBuscarId);
        const direccion = inputBuscar ? inputBuscar.value.trim() : '';

        if (!direccion) {
            this.mostrarMensaje('Por favor ingrese una dirección', 'WARNING');
            return;
        }

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}&limit=1&addressdetails=1&countrycodes=pe`);
            const data = await response.json();

            if (data.length > 0) {
                const resultado = data[0];
                const lat = parseFloat(resultado.lat);
                const lng = parseFloat(resultado.lon);

                // Centrar mapa en la ubicación encontrada
                this.mapa.setView([lat, lng], 16);
                this.actualizarMarcador(lat, lng);

                this.mostrarMensaje('Ubicación encontrada', 'SUCCESS');
            } else {
                this.mostrarMensaje('No se encontró la dirección', 'ERROR');
            }
        } catch (error) {
            console.error('Error al buscar dirección:', error);
            this.mostrarMensaje('Error al buscar la dirección', 'ERROR');
        }
    }

    confirmarUbicacion() {
        if (this.coordenadasSeleccionadas) {
            const inputLatitud = document.getElementById(this.options.inputLatitudId);
            const inputLongitud = document.getElementById(this.options.inputLongitudId);
            
            if (inputLatitud) inputLatitud.value = this.coordenadasSeleccionadas.lat.toFixed(6);
            if (inputLongitud) inputLongitud.value = this.coordenadasSeleccionadas.lng.toFixed(6);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById(this.options.modalId));
            modal.hide();
            
            this.mostrarMensaje('Ubicación guardada correctamente', 'SUCCESS');
        } else {
            this.mostrarMensaje('Por favor seleccione una ubicación en el mapa', 'WARNING');
        }
    }

    mostrarMensaje(mensaje, tipo = 'INFO', duracion = 1000) {
        
        if (typeof showToast === 'function') {
            showToast(mensaje, tipo, duracion);
        } else {
            alert(mensaje);
        }
    }
}

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si existe el botón de mapa para inicializar
    if (document.getElementById('btn-mapa')) {
        new MapaSelector();
    }
}); 