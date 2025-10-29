class MapaSelector {
    constructor(options = {}) {
        
        this.options = { 
            modalId: "modalMapa",
            mapaId: "mapa",
            btnMapaId: "btn-mapa",
            btnConfirmarId: "btnConfirmarUbicacion",
            inputBuscarId: "buscarDireccion", 
            btnBuscarId: "btnBuscar",         
            inputLatitudId: "latitud",
            inputLongitudId: "longitud",
            inputDireccion: "direccion",
            inputLatitudSeleccionadaId: "latitudSeleccionada",
            inputLongitudSeleccionadaId: "longitudSeleccionada",
            latInicial: -12.0464, //LIMA
            lngInicial: -77.0428,
            zoomInicial: 13,
            ...options,
        };
        this.mapa = null;
        this.marcador = null;
        this.geocoder = null;
        this.infoWindow = null;
        this.coordenadasSeleccionadas = null;
        this.MapClass = null;
        this.GeocoderClass = null;
        this.AdvancedMarkerElementClass = null;
        this.streetViewPanorama = null; 
        this.isStreetViewListenerAttached = false; 
    }

    async initAsync() {
        try {
            const { Map } = await google.maps.importLibrary("maps");
            const { Geocoder } = await google.maps.importLibrary("geocoding");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            const { StreetViewPanorama } = await google.maps.importLibrary("streetView");

            this.MapClass = Map;
            this.GeocoderClass = Geocoder;
            this.AdvancedMarkerElementClass = AdvancedMarkerElement;
            this.geocoder = new this.GeocoderClass();
            this.infoWindow = new google.maps.InfoWindow({ disableAutoPan: true });

            this.bindEvents();
        } catch (error) { console.error("Error importando librerías:", error); }
    }

    bindEvents() { 
        const btnMapa = document.getElementById(this.options.btnMapaId);
        if (btnMapa) {
            btnMapa.removeEventListener("click", this.handleAbrirModal);
            this.handleAbrirModal = () => this.abrirModal();
            btnMapa.addEventListener("click", this.handleAbrirModal);
        }
        const btnConfirmar = document.getElementById(this.options.btnConfirmarId);
        if (btnConfirmar) {
            btnConfirmar.removeEventListener("click", this.handleConfirmar);
            this.handleConfirmar = () => this.confirmarUbicacion();
            btnConfirmar.addEventListener("click", this.handleConfirmar);
        }
        const modalEl = document.getElementById(this.options.modalId);
        if (modalEl) {
            modalEl.removeEventListener("shown.bs.modal", this.handleModalShown);
            this.handleModalShown = () => this.inicializarMapa();
            modalEl.addEventListener("shown.bs.modal", this.handleModalShown);
        }
        const btnBuscar = document.getElementById(this.options.btnBuscarId);
        const inputBuscar = document.getElementById(this.options.inputBuscarId);
        if (btnBuscar) { btnBuscar.addEventListener("click", () => this.buscarDireccionManual()); }
        if (inputBuscar) {
            inputBuscar.addEventListener("keypress", (e) => {
                if (e.key === "Enter") { e.preventDefault(); this.buscarDireccionManual(); }
            });
        }
    }
    abrirModal() { 
        const modalEl = document.getElementById(this.options.modalId);
        if (!modalEl) { console.error(`Modal ID "${this.options.modalId}" no encontrado.`); return; }
        let modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl, { focus: true }); 
        modalInstance.show();
    }

    inicializarMapa() {
       
        const inputLatitud = document.getElementById(this.options.inputLatitudId);
        const inputLongitud = document.getElementById(this.options.inputLongitudId);
        const latInicial = parseFloat(inputLatitud?.value) || this.options.latInicial;
        const lngInicial = parseFloat(inputLongitud?.value) || this.options.lngInicial;
        const posicionInicial = { lat: latInicial, lng: lngInicial };
        const inputBuscar = document.getElementById(this.options.inputBuscarId);
        if (inputBuscar) inputBuscar.value = '';


        if (!this.mapa) {
            // console.log("Creando mapa...");
            const mapOptions = {
                center: posicionInicial,
                zoom: this.options.zoomInicial,
                mapId: 'YONDAMOTORPARK_MAP_ID',
                streetViewControl: true, 
                mapTypeControl: true,
                clickableIcons: true, 
            };
            this.mapa = new this.MapClass(document.getElementById(this.options.mapaId), mapOptions);

            // Listener para clics normales
            this.mapa.addListener("click", (e) => {
                
                // this.infoWindow.close();
                if (e.latLng) this.actualizarMarcador(e.latLng.lat(), e.latLng.lng());
            });

            // Acceder y escuchar cambios en Street View 
            this.streetViewPanorama = this.mapa.getStreetView(); // Obtener el panorama
            if (this.streetViewPanorama && !this.isStreetViewListenerAttached) {
                // console.log("Añadiendo listener 'position_changed' a Street View");
                this.streetViewPanorama.addListener('position_changed', () => {
                    const svPosition = this.streetViewPanorama.getPosition();
                    if (svPosition) {
                        const svLat = svPosition.lat();
                        const svLng = svPosition.lng();
                        console.log(`Street View movido a: lat=${svLat}, lng=${svLng}`);
                       
                        this.actualizarMarcador(svLat, svLng, true, null);
                    }
                });
                
                this.streetViewPanorama.addListener('visible_changed', () => {
                    if (this.streetViewPanorama.getVisible()) {
                        // console.log("Street View activado");
                        
                        const markerPos = this.marcador ? this.marcador.position : null;
                        if (markerPos) { this.streetViewPanorama.setPosition(markerPos); }
                    } else {
                        // console.log("Street View desactivado");
                        
                        const markerPos = this.marcador ? this.marcador.position : null;
                        if (markerPos) { this.mapa.panTo(markerPos); }
                    }
                });

                this.isStreetViewListenerAttached = true;
            }

        } else {
            this.mapa.setCenter(posicionInicial);
            this.mapa.setZoom(this.options.zoomInicial);
        }

        // Crear o mover marcador inicial
        if (!this.marcador) { this.actualizarMarcador(latInicial, lngInicial, false); }
        else { 
            this.marcador.position = posicionInicial;
            if (latInicial !== this.options.latInicial || lngInicial !== this.options.lngInicial) {
                this.obtenerDireccionPorCoordenadas(latInicial, lngInicial);
            } else { this.infoWindow.close(); }
        }
        if (inputBuscar) inputBuscar.focus();
    }

    async buscarDireccionManual() { 
        const inputBuscar = document.getElementById(this.options.inputBuscarId);
        const direccion = inputBuscar ? inputBuscar.value.trim() : "";
        if (!direccion) { this.mostrarMensaje("Ingrese dirección", "WARNING"); return; }
        // console.log(`Buscando: ${direccion}`);
        this.infoWindow.close();
        try {
            const { results } = await this.geocoder.geocode({ address: direccion, componentRestrictions: { country: 'PE' } });
            if (results?.length > 0) {
                const place = results[0];
                const location = place.geometry.location;
                const lat = location.lat(), lng = location.lng();
                if (isNaN(lat) || isNaN(lng)) throw new Error("Coords inválidas");
                // console.log("Encontrado:", place.formatted_address, lat, lng);
                this.mapa.panTo({ lat, lng });
                this.mapa.setZoom(17);
                this.actualizarMarcador(lat, lng, true, place.formatted_address);
                this.mostrarMensaje("Ubicación encontrada", "SUCCESS");
            } else { this.mostrarMensaje(`No se encontró "${direccion}"`, "ERROR"); }
        } catch (error) {
            console.error("Error Geocoder:", error); 
            if (error.code === 'ZERO_RESULTS') { this.mostrarMensaje(`No se encontró "${direccion}"`, "ERROR"); }
            else { this.mostrarMensaje("Error al buscar", "ERROR"); }
        }
    }

    actualizarMarcador(lat, lng, buscarDireccion = true, direccionConocida = null) { 
        const posicion = { lat: parseFloat(lat), lng: parseFloat(lng) };
        if (isNaN(posicion.lat) || isNaN(posicion.lng) || !this.mapa) return;
        console.log(`Actualizando marcador a: lat=${posicion.lat}, lng=${posicion.lng}`);
        if (this.marcador) { this.marcador.position = posicion; }
        else {
            this.marcador = new this.AdvancedMarkerElementClass({ position: posicion, map: this.mapa, gmpDraggable: true, title: "Ubicación" });
            this.marcador.addListener("dragend", () => {
                const pos = this.marcador.position;
                if (pos?.lat && pos?.lng) this.actualizarMarcador(pos.lat, pos.lng);
            });
            this.marcador.addListener('click', () => this.abrirInfoWindowConDireccionActual());
        }
        this.coordenadasSeleccionadas = posicion;
        this.actualizarCoordenadasSeleccionadas(posicion.lat, posicion.lng); 
        if (direccionConocida) { this.actualizarDireccion(direccionConocida); }
        else if (buscarDireccion) { this.obtenerDireccionPorCoordenadas(posicion.lat, posicion.lng); }
        else { this.infoWindow.close(); }
    }
    abrirInfoWindowConDireccionActual() { 
        if (this.coordenadasSeleccionadas) this.obtenerDireccionPorCoordenadas(this.coordenadasSeleccionadas.lat, this.coordenadasSeleccionadas.lng);
    }
    actualizarCoordenadasSeleccionadas(lat, lng) { 
        const inputLat = document.getElementById(this.options.inputLatitudSeleccionadaId);
        const inputLng = document.getElementById(this.options.inputLongitudSeleccionadaId);
        if (inputLat) inputLat.value = lat.toFixed(6);
        if (inputLng) inputLng.value = lng.toFixed(6);
    }

    async obtenerDireccionPorCoordenadas(lat, lng) {  
        if (!this.geocoder) return;
        try {
            const { results } = await this.geocoder.geocode({ location: { lat, lng } });
            if (results?.[0]) this.actualizarDireccion(results[0].formatted_address);
            else this.actualizarDireccion("Ubicación sin dirección");
        } catch (error) { console.error("Error Geocoder:", error); this.actualizarDireccion("No se pudo obtener dirección"); }
    }


    actualizarDireccion(direccionTexto) {
        if (!this.infoWindow || !this.mapa || !this.marcador) return;

        const contenido = `
        <div class="custom-infowindow">
            <strong>Dirección seleccionada</strong><br>
            ${direccionTexto}
        </div>
    `;
        this.infoWindow.setContent(contenido);
        this.infoWindow.open({
            anchor: this.marcador,
            map: this.mapa,
        });

        const inputDir = document.getElementById(this.options.inputDireccion);
        if (inputDir) inputDir.value = direccionTexto;
    }

    confirmarUbicacion() {
        if (!this.coordenadasSeleccionadas) {
            this.mostrarMensaje("Seleccione ubicación", "WARNING");
            return;
        }

        const inputLat = document.getElementById(this.options.inputLatitudId);
        const inputLng = document.getElementById(this.options.inputLongitudId);
        
        if (inputLat) inputLat.value = this.coordenadasSeleccionadas.lat.toFixed(6);
        if (inputLng) inputLng.value = this.coordenadasSeleccionadas.lng.toFixed(6);

      

        const modalEl = document.getElementById(this.options.modalId);
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        this.mostrarMensaje("Ubicación guardada correctamente", "SUCCESS");
    }


    mostrarMensaje(mensaje, tipo = "INFO", duracion = 1000) { 
        if (typeof showToast === "function") showToast(mensaje, tipo, duracion);
        else { alert(mensaje); }
    }
} 


let mapaSelectorInstance = null;
async function initMapa() { 
    if (document.getElementById("btn-mapa")) {
        if (!mapaSelectorInstance) {
            mapaSelectorInstance = new MapaSelector();
            await mapaSelectorInstance.initAsync();
        } else { mapaSelectorInstance.bindEvents(); }
    }
}
window.googleMapsCallback = initMapa;