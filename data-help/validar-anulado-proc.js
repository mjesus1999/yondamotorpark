 formProceso.addEventListener("submit", async (e) => {
                e.preventDefault();
                const ruta = inputRuta.value;
                const obs = inputObs.value.trim();

                if (!obs) {
                    alert("Por favor ingrese el motivo antes de continuar.");
                    return;
                }

                if (confirm('¿Está seguro de actualizar el estado de la OC?')) {
                    try {
                        const res = await fetch(ruta, {
                            method: "POST",
                            body: new URLSearchParams({
                                observaciones: obs
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            modalProceso.hide();
                            showToast(data.message, "SUCCESS", 1200);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            //  Muestra el mensaje que viene del backend (incluye la validación de -2)
                            showToast(data.message, "WARNING", 1200);
                        }
                    } catch (error) {
                        console.error(error);
                        alert("Hubo un error al actualizar.");
                    }
                }
            });