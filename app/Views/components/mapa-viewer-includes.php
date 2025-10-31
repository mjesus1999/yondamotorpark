<!-- app/views/components/mapa-viewer-includes.php -->
<script>
    (g => {
        var h, a, k, p = "The Google Maps Viewer API",
            c = "google",
            l = "importLibrary",
            q = "__ib_viewer__",
            m = document,
            b = window;

        // Solo inicializar si no existe ya el sistema de mapas principal
        if (b.mapaViewerInitialized) return;

        b[c] = b[c] || {};
        var d = b[c];
        d.maps = d.maps || {};

        // Guardar la función load original si existe
        const originalLoad = d.maps.load;

        // Crear función load para el viewer
        d.maps.loadViewer = function (e, f) {
            // Si ya existe el load principal, usarlo
            const loader = originalLoad || function (callback) { callback(); };

            loader(() => {
                // Si ya está cargado Google Maps, solo ejecutar el callback
                if (b.google && b.google.maps && b.google.maps.Map) {
                    console.log('Google Maps ya está cargado, usando instancia existente');
                    if (f) f();
                    return;
                }

                // Si no está cargado, cargar el script
                var i = m.createElement("script");
                i.id = p;

                // Tu API KEY
                i.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBtnx36dsihnq0sNBChV9KHH4QtGEmkEfQ&callback=__googleMapsViewerCallback";

                i.async = true;
                i.defer = true;
                i.nonce = m.querySelector("script[nonce]")?.nonce || "";

                // Define el callback global
                b["__googleMapsViewerCallback"] = function () {
                    delete b["__googleMapsViewerCallback"];

                    b[q] = b[q] || d.maps.importLibrary;

                    if (f) f();
                };

                m.head.append(i);
            });
        };

        b.mapaViewerInitialized = true;

    })(window);
</script>