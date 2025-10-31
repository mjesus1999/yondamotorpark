<!-- app/views/components/mapa-includes.php -->
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
        b[c] || (b[c] = {});
        var d = b[c];
        
        d.maps || (d.maps = {});

        // Guarda la función load original si existe
        const originalLoad = d.maps.load;

        d.maps.load = function(e, f) {
            // Usa la función load original o una implementación simple
            const loader = originalLoad || function(callback) { callback(); };

            loader(() => {
                var i = m.createElement("script");
                i.id = p;
                
                // Uso de la API KEY
                i.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyA1ISJORQDn45fHrOP_q5t0iVHNIVL6JLo&v=beta&callback=__googleMapsCallback"; 
                
                i.async = true;
                i.nonce = m.querySelector("script[nonce]")?.nonce || "";
                
                // Define el callback global ANTES de añadir el script
                b["__googleMapsCallback"] = function() {
                    delete b["__googleMapsCallback"]; // Limpia el callback global
                    
                    // Guarda la función importLibrary si no existe
                    b[q] || (b[q] = d.maps.importLibrary);
                    
                    // Importa las librerías necesarias y luego llama a la función final (f)
                    (e ? Promise.all(e.map(libName => b[q](libName))) : Promise.resolve())
                        .then(libraries => f(libraries))
                        .catch(err => console.error("Error importing libraries:", err));
                };

                m.head.append(i);
            });
        };

        // Llama a d.maps.load AHORA, pasando initMapa como la función final a ejecutar
        d.maps.load(['maps', 'geocoding', 'places', 'marker'], initMapa);

    })(window);
</script>