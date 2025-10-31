/**
 * public/assets/js/mapa-viewer.js
 * visualización de mapas para ubicaciones de clientes
 */

class MapaViewer {
  constructor() {
    this.mapa = null;
    this.marcador = null;
    this.infoWindow = null;
    this.geocoder = null;
    this.coordenadas = null;
    this.datosCliente = {
      nombre: "",
      direccion: "",
      lat: null,
      lng: null,
    };
    this.isInitialized = false;
    this.apiLoaded = false;
    this.pendingShowModal = false;
  }

  async cargarScriptGoogleMaps() {
    // Si ya está cargado, retornar inmediatamente

    if (window.google && window.google.maps && window.google.maps.Map) {
      this.apiLoaded = true;
      console.log("-> Google Maps ya está disponible");
      return Promise.resolve();
    }

    if (window.googleMapsLoading) {
      console.log("-> Esperando a que termine de cargar Google Maps...");
      return window.googleMapsLoading;
    }

    console.log("-> Cargando Google Maps API por primera vez...");

    // Crear promesa de carga

    window.googleMapsLoading = new Promise((resolve, reject) => {
      // Verificar si existe el sistema de carga del proyecto
      if (
        window.google &&
        window.google.maps &&
        window.google.maps.loadViewer
      ) {
        console.log("-> Usando sistema de carga existente");
        window.google.maps.loadViewer(null, () => {
          this.apiLoaded = true;
          console.log("-> Google Maps cargado vía loadViewer");
          resolve();
        });
        return;
      }

      // Fallback: cargar directamente
      console.log("-> Usando carga directa (fallback)");
      const script = document.createElement("script");
      script.src =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyBtnx36dsihnq0sNBChV9KHH4QtGEmkEfQ&callback=__googleMapsViewerDirectCallback";
      script.async = true;
      script.defer = true;

      window.__googleMapsViewerDirectCallback = () => {
        delete window.__googleMapsViewerDirectCallback;
        this.apiLoaded = true;
        console.log("-> Google Maps API cargada correctamente (direct)");
        resolve();
      };

      script.onerror = (error) => {
        console.error("-> Error al cargar Google Maps API:", error);
        reject(error);
      };

      document.head.appendChild(script);
    });

    return window.googleMapsLoading;
  }

  async inicializarLibrerias() {
    if (this.isInitialized) {
      console.log("-> Librerías ya inicializadas, reutilizando...");
      return;
    }

    try {
      console.log("-> Inicializando servicios de Google Maps...");

      // Asegurar que la API está cargada
      await this.cargarScriptGoogleMaps();

      // Esperar hasta que google.maps esté completamente disponible
      let intentos = 0;
      while ((!window.google || !window.google.maps) && intentos < 20) {
        await new Promise((resolve) => setTimeout(resolve, 250));
        intentos++;
      }

      // Verificar que google.maps existe
      if (!window.google || !window.google.maps) {
        throw new Error(
          "-> Google Maps no está disponible después de esperar 5 segundos"
        );
      }

      // Inicializar los servicios que necesitamos
      this.geocoder = new window.google.maps.Geocoder();
      this.infoWindow = new window.google.maps.InfoWindow({
        disableAutoPan: false,
      });

      this.isInitialized = true;
      console.log("-> MapaViewer: Servicios inicializados correctamente");
    } catch (error) {
      console.error("-> MapaViewer: Error al inicializar servicios:", error);
      throw error;
    }
  }

  async mostrarUbicacion(datos) {
    // Validar datos
    if (!datos || (!datos.lat && !datos.direccion)) {
      this.mostrarError("No hay información de ubicación disponible");
      return;
    }

    // Si ya hay un modal pendiente, ignorar
    if (this.pendingShowModal) {
      console.log("-> Ya hay un modal en proceso de apertura");
      return;
    }

    this.pendingShowModal = true;

    this.datosCliente = {
      nombre: datos.nombre || "Cliente",
      direccion: datos.direccion || "Sin dirección específica",
      lat: parseFloat(datos.lat) || null,
      lng: parseFloat(datos.lng) || null,
    };

    console.log("-> Preparando ubicación:", this.datosCliente);

    // Abrir modal
    const modalEl = document.getElementById("modalMapaViewer");
    if (!modalEl) {
      console.error("Modal no encontrado");
      this.pendingShowModal = false;
      return;
    }

    //Registrar el listener ANTES de mostrar el modal
    const handleModalShown = async () => {
      console.log("-> Modal mostrado completamente, cargando mapa...");

      try {
        await this.cargarMapa();
      } catch (error) {
        console.error("-> Error al cargar mapa:", error);
        this.mostrarError(
          "-> Error al cargar el mapa. Por favor, intente nuevamente."
        );
      } finally {
        this.pendingShowModal = false;
      }
    };

    // Registrar listener (con once: true para que se ejecute solo una vez)
    modalEl.addEventListener("shown.bs.modal", handleModalShown, {
      once: true,
    });

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  async cargarMapa() {
    try {
      //Se carga el google.maps una vez (solo cuando se abre el modal)
      if (!this.isInitialized) {
        this.mostrarCargando(true);
        //console.log("-> Primera vez abriendo el mapa, inicializando servicios...");
        await this.inicializarLibrerias();
      }

      // Actualizar información del cliente en el panel
      this.actualizarPanelInfo();

      let ubicacion;

      // Si hay coordenadas, usarlas directamente
      if (this.datosCliente.lat && this.datosCliente.lng) {
        console.log(
          "-> Usando coordenadas directas:",
          this.datosCliente.lat,
          this.datosCliente.lng
        );
        ubicacion = {
          lat: this.datosCliente.lat,
          lng: this.datosCliente.lng,
        };
        this.mostrarCargando(false);
        await this.crearMapa(ubicacion);
      }

      // Si solo hay dirección, geocodificar
      else if (
        this.datosCliente.direccion &&
        this.datosCliente.direccion !== "Sin dirección específica"
      ) {
        console.log(
          "-> Geocodificando dirección:",
          this.datosCliente.direccion
        );
        this.mostrarCargando(true);
        ubicacion = await this.geocodificarDireccion(
          this.datosCliente.direccion
        );
        this.mostrarCargando(false);

        if (ubicacion) {
          await this.crearMapa(ubicacion);
        } else {
          this.mostrarError("-> No se pudo encontrar la ubicación en el mapa");
        }
      } else {
        this.mostrarError(
          "-> No hay coordenadas ni dirección válida para mostrar"
        );
      }
    } catch (error) {
      console.error("-> Error al cargar mapa:", error);
      this.mostrarError(
        "-> Error al cargar el mapa. Por favor, intente nuevamente."
      );
      this.mostrarCargando(false);
    }
  }

  async crearMapa(ubicacion) {
    this.coordenadas = ubicacion;

    // Verificar que el contenedor del mapa existe
    const mapaContainer = document.getElementById("mapaViewer");
    if (!mapaContainer) {
      console.error("-> Contenedor del mapa no encontrado");
      return;
    }

    // Limpiar contenido previo
    mapaContainer.innerHTML = "";

    // Crear o actualizar mapa
    if (!this.mapa) {
      console.log("-> Creando nueva instancia del mapa...");
      const mapOptions = {
        center: ubicacion,
        zoom: 16,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
      };

      this.mapa = new window.google.maps.Map(mapaContainer, mapOptions);
      console.log("-> Mapa creado exitosamente");
    } else {
      console.log("-> Actualizando mapa existente...");
      this.mapa.setCenter(ubicacion);
      this.mapa.setZoom(16);
    }

    // Crear o actualizar marcador
    if (this.marcador) {
      this.marcador.setMap(null);
    }

    this.marcador = new window.google.maps.Marker({
      position: ubicacion,
      map: this.mapa,
      title: this.datosCliente.nombre,
      animation: window.google.maps.Animation.DROP,
    });

    // Agregar click listener para mostrar info
    this.marcador.addListener("click", () => {
      this.mostrarInfoWindow();
    });

    // Actualizar displays de coordenadas
    document.getElementById("viewer-latitud-display").value =
      ubicacion.lat.toFixed(6);
    document.getElementById("viewer-longitud-display").value =
      ubicacion.lng.toFixed(6);

    // Actualizar botones
    this.actualizarBotones();

    // Mostrar InfoWindow automáticamente
    setTimeout(() => this.mostrarInfoWindow(), 500);

    console.log("-> Mapa completamente configurado");
  }

  async geocodificarDireccion(direccion) {
    try {
      console.log("Geocodificando:", direccion);

      const response = await this.geocoder.geocode({
        address: direccion,
        componentRestrictions: { country: "PE" },
      });

      if (response.results && response.results.length > 0) {
        const location = response.results[0].geometry.location;

        // Actualizar dirección con la versión formateada
        this.datosCliente.direccion = response.results[0].formatted_address;
        document.getElementById("viewer-direccion").textContent =
          this.datosCliente.direccion;

        console.log(
          "-> Geocodificación exitosa:",
          location.lat(),
          location.lng()
        );

        return {
          lat: location.lat(),
          lng: location.lng(),
        };
      }

      console.log("-> No se encontraron resultados de geocodificación");
      return null;
    } catch (error) {
      console.error("-> Error en geocodificación:", error);
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
    this.infoWindow.open(this.mapa, this.marcador);
  }

  actualizarPanelInfo() {
    document.getElementById("viewer-cliente-nombre").textContent =
      this.datosCliente.nombre;
    document.getElementById("viewer-direccion").textContent =
      this.datosCliente.direccion;
  }

  actualizarBotones() {
    if (!this.coordenadas) return;

    // Botón Google Maps
    const btnGoogleMaps = document.getElementById("btnAbrirGoogleMaps");
    if (btnGoogleMaps) {
      btnGoogleMaps.href = `https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`;
    }

    // Botón Compartir
    const btnCompartir = document.getElementById("btnCompartirUbicacion");
    if (btnCompartir) {
      btnCompartir.onclick = () => this.compartirUbicacion();
    }
  }

  compartirUbicacion() {
    if (!this.coordenadas) {
      this.mostrarMensaje("-> No hay ubicación para compartir", "warning");
      return;
    }

    const textoCompartir = `Ubicación de ${this.datosCliente.nombre}\n${this.datosCliente.direccion}\n\nVer en mapa: https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`;

    // Intentar usar Web Share API
    if (navigator.share) {
      navigator
        .share({
          title: `Ubicación: ${this.datosCliente.nombre}`,
          text: textoCompartir,
          url: `https://www.google.com/maps?q=${this.coordenadas.lat},${this.coordenadas.lng}`,
        })
        .then(() =>
          this.mostrarMensaje("Ubicación compartida exitosamente", "success")
        )
        .catch((err) => {
          if (err.name !== "AbortError") {
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
      navigator.clipboard
        .writeText(texto)
        .then(() =>
          this.mostrarMensaje("Ubicación copiada al portapapeles", "success")
        )
        .catch(() => this.mostrarMensaje("No se pudo copiar", "danger"));
    } else {
      // Fallback antiguo
      const textarea = document.createElement("textarea");
      textarea.value = texto;
      textarea.style.position = "fixed";
      textarea.style.opacity = "0";
      document.body.appendChild(textarea);
      textarea.select();
      try {
        document.execCommand("copy");
        this.mostrarMensaje("Ubicación copiada al portapapeles", "success");
      } catch (err) {
        this.mostrarMensaje("No se pudo copiar", "danger");
      }
      document.body.removeChild(textarea);
    }
  }

  mostrarCargando(mostrar) {
    const mapaEl = document.getElementById("mapaViewer");
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
    }
  }

  mostrarError(mensaje) {
    const mapaEl = document.getElementById("mapaViewer");
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

  mostrarMensaje(mensaje, tipo = "info") {
    const toast = document.createElement("div");
    toast.className = `alert alert-${tipo} position-fixed`;
    toast.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; min-width: 250px;";
    toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${
                  tipo === "success" ? "check-circle" : "info-circle"
                } me-2"></i>
                <div>${mensaje}</div>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  limpiar() {
    if (this.infoWindow) {
      this.infoWindow.close();
    }
    this.coordenadas = null;
    this.datosCliente = {
      nombre: "",
      direccion: "",
      lat: null,
      lng: null,
    };
    // NO destruir el mapa ni los servicios para reutilizarlos
    console.log("Datos del cliente limpiados");
  }
}

// ============================================
// INICIALIZACIÓN GLOBAL
// ============================================

// Instancia global del visor
let mapaViewerInstance = null;

//Función de inicialización (NO carga Google Maps, solo crea la instancia vacía)
async function initMapaViewer() {
  console.log("🚀 Inicializando MapaViewer (instancia vacía)...");
  if (!mapaViewerInstance) {
    mapaViewerInstance = new MapaViewer();
    console.log(
      "✓ MapaViewer instanciado correctamente (sin cargar Google Maps aún)"
    );
  }
}

// Función helper para abrir el mapa desde cualquier parte
window.abrirMapaViewer = function (datos) {
  if (!mapaViewerInstance) {
    console.error(
      "MapaViewer no está inicializado. Llama a initMapaViewer() primero."
    );
    return;
  }
  mapaViewerInstance.mostrarUbicacion(datos);
};

//Limpiar al cerrar el modal
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modalMapaViewer");
  if (modal) {
    modal.addEventListener("hidden.bs.modal", () => {
      if (mapaViewerInstance) {
        mapaViewerInstance.limpiar();
      }
    });
  }
});
