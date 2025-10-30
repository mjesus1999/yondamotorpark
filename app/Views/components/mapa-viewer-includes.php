<!-- app/views/components/mapa-viewer-includes.php -->
<script>
    (g => {
        var h, a, k, p = "The Google Maps Viewer API",
            c = "google",
            l = "importLibrary",
            q = "__ib_viewer__",
            m = document,
            b = window;
        
        // Solo inicializar si no existe ya el visor
        if (b.mapaViewerInitialized) return;
        
        b[c] = b[c] || {};
        var d = b[c];
        d.maps = d.maps || {};

        const originalLoad = d.maps.load;

        d.maps.loadViewer = function(e, f) {
            const loader = originalLoad || function(callback) { callback(); };

            loader(() => {
                var i = m.createElement("script");
                i.id = p;
                i.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDyJjCF6m_aVhS2jSF8g5VTW4YXNLdhqxU&v=beta&callback=__googleMapsViewerCallback&libraries=places,geocoding"; 
                i.async = true;
                i.nonce = m.querySelector("script[nonce]")?.nonce || "";
                
                b["__googleMapsViewerCallback"] = function() {
                    delete b["__googleMapsViewerCallback"];
                    b[q] = b[q] || d.maps.importLibrary;
                    
                    (e ? Promise.all(e.map(libName => b[q](libName))) : Promise.resolve())
                        .then(libraries => f(libraries))
                        .catch(err => console.error("Error importing viewer libraries:", err));
                };

                m.head.append(i);
            });
        };

        b.mapaViewerInitialized = true;

    })(window);
</script>