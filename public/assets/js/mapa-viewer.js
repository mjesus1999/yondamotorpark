/**
 * public/assets/js/mapa-viewer.js
 * Sistema de visualización de mapas para ubicaciones de clientes
 */

class MapaViewer {
    constructor() {
        this.mapa = null;
        this.marcador = null;
        this.infoWindow = null;
        this.geocoder = null;
        this.coordenadas = null;
        this.datosCliente = {
            nombre: '',
            direccion: '',
            lat: null,
            lng: null
        };
        this.isInitialized = false;
    }

    async inicializarLibrerias() {
        if (this.isInitialized) return;

        try {
            const { Map } = await google.maps.importLibrary("maps");
            const { Geocoder } = await google.maps.importLibrary("geocoding");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

            this.MapClass = Map;
            this.GeocoderClass = Geocoder;
            this.AdvancedMarkerElementClass = AdvancedMarkerElement;
            this.geocoder = new this.GeocoderClass();
            this.infoWindow = new google.maps.InfoWindow({ disableAutoPan: false });

            this.isInitialized = true;
            console.log('MapaViewer: Librerías inicializadas correctamente');
        } catch (error) {
            console.error("MapaViewer: Error al importar librerías:", error);
            throw error;
        }
    }

    async mostrarUbicacion(datos) {
        // Validar datos
        if (!datos || (!datos.lat && !datos.direccion)) {
            this.mostrarError('No hay información de ubicación disponible');
            return;
        }

        this.datosCliente = {
            nombre: datos.nombre || 'Cliente',
            direccion: datos.direccion || 'Sin dirección específica',
            lat: parseFloat(datos.lat) || null,
            lng: parseFloat(datos.lng) || null
        };

        // Abrir modal
        const modalEl = document.getElementById('modalMapaViewer');
        if (!modalEl) {
            console.error('Modal no encontrado');
            return;
        }

        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Esperar a que el modal esté completamente visible
        modalEl.addEventListener('shown.bs.modal', async () => {
            await this.cargarMapa();
        }, { once: true });
    }

    async cargarMapa() {
        try {
            // Inicializar librerías si no están listas
            if (!this.isInitialized) {
                await this.inicializarLibrerias();
            }

            // Actualizar información del cliente en el panel
            this.actualizarPanelInfo();

            let ubicacion;

            // Si hay coordenadas, usarlas directamente
            if (this.datosCliente.lat && this.datosCliente.lng) {
                ubicacion = {
                    lat: this.datosCliente.lat,
                    lng: this.datosCliente.lng
                };
                await this.crearMapa(ubicacion);
            } 
            // Si solo hay dirección, geocodificar
            else if (this.datosCliente.direccion && this.datosCliente.direccion !== 'Sin dirección específica') {
                this.mostrarCargando(true);
                ubicacion = await this.geocodificarDireccion(this.datosCliente.direccion);
                this.mostrarCargando(false);
                
                if (ubicacion) {
                    await this.crearMapa(ubicacion);
                } else {
                    this.mostrarError('No se pudo encontrar la ubicación en el mapa');
                }
            } else {
                this.mostrarError('No hay coordenadas ni dirección válida para mostrar');
            }

        } catch (error) {
            console.error('Error al cargar mapa:', error);
            this.mostrarError('Error al cargar el mapa. Por favor, intente nuevamente.');
        }
    }

    async crearMapa(ubicacion) {
        this.coordenadas = ubicacion;

        // Crear o actualizar mapa
        if (!this.mapa) {
            const mapOptions = {
                center: ubicacion,
                zoom: 16,
                mapId: 'VIEWER_MAP_ID',
                streetViewControl: true,
                mapTypeControl: true,
                fullscreenControl: true
            };
            this.mapa = new this.MapClass(document.getElementById('mapaViewer'), mapOptions);
        } else {
            this.mapa.setCenter(ubicacion);
            this.mapa.setZoom(16);
        }

        // Crear o actualizar marcador
        if (this.marcador) {
            this.marcador.position = ubicacion;
        } else {
            this.marcador = new this.AdvancedMarkerElementClass({
                position: ubicacion,
                map: this.mapa,
                title: this.datosCliente.nombre
            });

            // Agregar click listener para mostrar info
            this.marcador.addListener('click', () => {
                this.mostrarInfoWindow();
            });
        }

        // Actualizar displays de coordenadas
        document.getElementById('viewer-latitud-display').value = ubicacion.lat.toFixed(6);
        document.getElementById('viewer-longitud-display').value = ubicacion.lng.toFixed(6);

        // Actualizar botones
        this.actualizarBotones();

        // Mostrar InfoWindow automáticamente
        setTimeout(() => this.mostrarInfoWindow(), 500);
    }

    async geocodificarDireccion(direccion) {
        try {
            const { results } = await this.geocoder.geocode({
                address: direccion,
                componentRestrictions: { country: 'PE' }
            });

            if (results && results.length > 0) {
                const location = results[0].geometry.location;
                // Actualizar dirección con la versión formateada
                this.datosCliente.direccion = results[0].formatted_address;
                document.getElementById('viewer-direccion').textContent = this.datosCliente.direccion;
                
                return {
                    lat: location.lat(),
                    lng: location.lng()
                };
            }
            return null;
        } catch (error) {
            console.error('Error en geocodificación:', error);
            return null;
        }
    }

    mostrarInfoWindow() {
        if (!this.infoWindow || !this.mapa || !this.marcador) return;

        const contenido = `
            <div style="padding: 10px; max-width: 250px;">
                <h6 style="margin: 0 0 8px 0; color: #0d6efd; font-weight: 600;">
                    <i class="bi bi-person-circle"></i> ${this.datosCliente.nombre}
                </h6>
                <p style="margin: 0; font-size: 13px; color: #666;">
                    <i class="bi bi-geo-alt"></i> ${this.datosCliente.direccion}
                </p>
            </div>
        `;

        this.infoWindow.setContent(contenido);
        this.infoWindow.open({
            anchor: this.marcador,
            map: this.mapa
        });
    }

    actualizarPanelInfo() {
        document.getElementById('viewer-cliente-nombre').textContent = this.datosCliente.nombre;
        document.getElementById('viewer-direccion').textContent = this.datosCliente.direccion;
    }

    actualizarBotones() {
        if (!this.coordenadas) return;

        // Botón Google Maps
        const btnGoogleMaps = document.getElementById('btnAbrirGoogleMaps');
        if (btnGoogleMaps) {
            btnGoogleMaps.href = `https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`;
        }

        // Botón Compartir
        const btnCompartir = document.getElementById('btnCompartirUbicacion');
        if (btnCompartir) {
            btnCompartir.onclick = () => this.compartirUbicacion();
        }
    }

    compartirUbicacion() {
        if (!this.coordenadas) {
            this.mostrarMensaje('No hay ubicación para compartir', 'warning');
            return;
        }

        const textoCompartir = `📍 Ubicación de ${this.datosCliente.nombre}\n${this.datosCliente.direccion}\n\nVer en mapa: https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`;

        // Intentar usar Web Share API
        if (navigator.share) {
            navigator.share({
                title: `Ubicación: ${this.datosCliente.nombre}`,
                text: textoCompartir,
                url: `https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`
            })
            .then(() => this.mostrarMensaje('Ubicación compartida exitosamente', 'success'))
            .catch(err => {
                if (err.name !== 'AbortError') {
                    this.copiarAlPortapapeles(textoCompartir);
                }
            });
        } else {
            // Fallback: copiar al portapapeles
            this.copiarAlPortapapeles(textoCompartir);
        }
    }

    copiarAlPortapapeles(texto) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(texto)
                .then(() => this.mostrarMensaje('Ubicación copiada al portapapeles', 'success'))
                .catch(() => this.mostrarMensaje('No se pudo copiar', 'danger'));
        } else {
            // Fallback antiguo
            const textarea = document.createElement('textarea');
            textarea.value = texto;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                this.mostrarMensaje('Ubicación copiada al portapapeles', 'success');
            } catch (err) {
                this.mostrarMensaje('No se pudo copiar', 'danger');
            }
            document.body.removeChild(textarea);
        }
    }

    mostrarCargando(mostrar) {
        const mapaEl = document.getElementById('mapaViewer');
        if (!mapaEl) return;

        if (mostrar) {
            mapaEl.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted">Cargando ubicación...</p>
                    </div>
                </div>
            `;
        } else {
            mapaEl.innerHTML = '';
        }
    }

    mostrarError(mensaje) {
        const mapaEl = document.getElementById('mapaViewer');
        if (!mapaEl) return;

        mapaEl.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                <div class="text-center p-4">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">${mensaje}</p>
                </div>
            </div>
        `;
    }

    mostrarMensaje(mensaje, tipo = 'info') {
        if (typeof showToast === "function") {
            showToast(mensaje, tipo.toUpperCase(), 2000);
        } else {
            const toast = document.createElement('div');
            toast.className = `alert alert-${tipo} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
            toast.textContent = mensaje;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    }

    limpiar() {
        if (this.infoWindow) {
            this.infoWindow.close();
        }
        this.coordenadas = null;
        this.datosCliente = {
            nombre: '',
            direccion: '',
            lat: null,
            lng: null
        };
    }
}

// Instancia global del visor
let mapaViewerInstance = null;

// Función de inicialización (se llama desde el include)
async function initMapaViewer() {
    console.log('Inicializando MapaViewer...');
    if (!mapaViewerInstance) {
        mapaViewerInstance = new MapaViewer();
        // No inicializamos las librerías aquí, se hará cuando se abra el modal
        console.log('MapaViewer instanciado');
    }
}

// Función helper para abrir el mapa desde cualquier parte
window.abrirMapaViewer = function(datos) {
    if (!mapaViewerInstance) {
        console.error('MapaViewer no está inicializado');
        return;
    }
    mapaViewerInstance.mostrarUbicacion(datos);
};

// Limpiar al cerrar el modal
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalMapaViewer');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', () => {
            if (mapaViewerInstance) {
                mapaViewerInstance.limpiar();
            }
        });
    }
});