  document.addEventListener("DOMContentLoaded", () => {

            const botonesFiltro = document.querySelectorAll("#botones-filtro .btn")
            const enlacesDetalle = document.querySelectorAll(".show-details")
            const botonVolver = document.querySelector("#btn-volver")
            const speedAnimation = 750;

            // Referencias a los contenedores principales
            const listaOc = document.getElementById('lista-oc');
            const ocDetailView = document.getElementById('detalle-oc');

            // Referencias a elementos dentro de la vista de detalle
            const detailConcesionarioRazonSocial = document.getElementById('detail-concesionario-razon-social');
            const detailOcSummary = document.getElementById('detail-oc-summary');
            const tablaDetallesBody = document.getElementById('tabla-detalles').querySelector('tbody');
            const btnVolver = document.getElementById('btn-volver');

            // Selecciona todos los enlaces con la clase 'show-details'
            const detailLinks = document.querySelectorAll('.show-details');

            // Variables para el modal de verificar si los autos llegaron bien
            const tablaAutosModalBody = document.querySelector("#tabla-autos-modal tbody");
            const modalOc = new bootstrap.Modal(document.getElementById('modal-oc'));
            const formularioOc = document.getElementById("formulario-oc");

            // Campos para abrir el modal de proceso o anulado:
            const modalProceso = new bootstrap.Modal(document.getElementById("modal-proceso"));
            const formProceso = document.getElementById("form-proceso");
            const inputIdOc = document.getElementById("id-oc-proceso");
            const inputRuta = document.getElementById("modal-proceso-ruta");
            const inputObs = document.getElementById("observaciones");
            const modalTitulo = document.getElementById("modal-proceso-titulo");


            let idOC = null; // Para identifcar el idoc a actualizar desde el modal para verificar si los autos llegarón de acuerdo a la OC

            formularioOc.addEventListener("submit", async (e) => {
                e.preventDefault();

                const valorSeleccionado = document.querySelector('input[name="escorrecto"]:checked').value;

                if (confirm('¿Actualizar el detalle?')) {
                    formData = new FormData();
                    formData.append('escorrecto', valorSeleccionado);

                    try {
                        const res = await fetch(`/oc/update/${idOC}`, {
                            method: 'POST',
                            body: formData
                        })

                        const data = await res.json();

                        if (data.success) {
                            modalOc.hide();
                            showToast(data.message, 'SUCCESS', 1000);
                            // setTimeout(() => location.reload(), 1000);

                        } else {
                            modalOc.hide();
                            showToast(data.message, 'WARNING', 1000);
                        }

                    } catch (error) {
                        console.error(error);
                    }

                }

            });


            document.querySelectorAll(".btn-abrir-modal-estado").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const idOC = btn.dataset.id;
                    const accion = btn.dataset.accion;
                    const ruta = btn.dataset.ruta;

                    inputIdOc.value = idOC;
                    inputRuta.value = ruta;
                    inputObs.value = "";

                    // Cambiar el título dinámicamente
                    modalTitulo.textContent = (accion === "proceso") ?
                        "Observaciones - Cambio a Proceso" :
                        "Observaciones - Cambio a Anulado";

                    modalProceso.show();
                });
            });

            // Enviar el formulario
            formProceso.addEventListener("submit", async (e) => {
                e.preventDefault();
                const ruta = inputRuta.value;
                const obs = inputObs.value.trim();

                if (!obs) {
                    alert("Por favor ingrese el motivo antes de continuar.");
                    return;
                }

                if (confirm('¿Esta seguro de acttulizar el estado de la OC?'))

                {
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
                            showToast(data.message, "WARNING", 1200);
                        }
                    } catch (error) {
                        console.error(error);
                        alert("Hubo un error al actualizar.");
                    }

                }

            });


            // Para el modal de check

            document.querySelectorAll("a[data-idocmodal]").forEach(icono => {
                icono.addEventListener("click", async (e) => {
                    e.preventDefault();
                    idOC = e.currentTarget.dataset.idocmodal;
                    if (!idOC) return;

                    try {
                        const response = await fetch(`/api/oc/infoAutos/${idOC}`);
                        if (!response.ok) throw new Error("Error al obtener los autos");

                        const autos = await response.json();
                        if (!autos || autos.length === 0) {
                            tablaAutosModalBody.innerHTML = `<tr><td colspan="4" class="text-center">No hay autos para esta orden</td></tr>`;
                        } else {
                            tablaAutosModalBody.innerHTML = "";
                            autos.sort((a, b) => a.auto.localeCompare(b.auto));

                            autos.forEach((item, index) => {
                                const row = document.createElement("tr");
                                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.auto}</td>
                            <td>${item.cantidad}</td>
                        `;
                                tablaAutosModalBody.appendChild(row);
                            });
                        }

                        modalOc.show();

                    } catch (error) {
                        console.error(error);
                        alert("No se pudieron cargar los datos.");
                    }
                });
            });


            function limpiarVistaDetalle() {
                // Limpiar el nombre del concesionario
                if (detailConcesionarioRazonSocial) {
                    detailConcesionarioRazonSocial.textContent = '';
                }

                // Limpiar el resumen de la OC (número, fecha, moneda, total)
                if (detailOcSummary) {
                    detailOcSummary.textContent = '';
                }

                // Limpiar la tabla de detalles (vehículos)
                if (tablaDetallesBody) {
                    tablaDetallesBody.innerHTML = '';
                }
            }

            // Evento para eventos clikc de detalles
            detailLinks.forEach(link => {
                link.addEventListener('click', async (event) => {
                    event.preventDefault();

                    const enlace = event.currentTarget || event.target.closest('a');
                    const ocId = enlace.dataset.idoc;

                    if (!ocId) {
                        console.warn('ID de Orden de Compra no encontrado en el enlace de detalle.');
                        return;
                    }
                    // Limpiar vista antes de cargar nuevos datos
                    limpiarVistaDetalle();

                    const apiUrl = `/api/oc/${ocId}`;

                    try {
                        const response = await fetch(apiUrl);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const ocDetails = await response.json();

                        // Validar si hay datos válidos
                        if (!ocDetails || !Array.isArray(ocDetails) || ocDetails.length === 0) {
                            showToast('No hay datos para la OC', 'WARNING', 1200);
                            return; // No mostrar la vista de detalles.
                        }

                        // Mostrar la vista de detalle con animación
                        $("#lista-oc").slideUp(speedAnimation);
                        $("#detalle-oc").slideDown(speedAnimation);

                        // Llenar información del encabezado
                        const firstDetail = ocDetails[0];

                        if (detailConcesionarioRazonSocial && detailOcSummary) {
                            detailConcesionarioRazonSocial.textContent = firstDetail.concesionario_razon_social || 'N/A';

                            const numeroOc = firstDetail.numero_oc_formateado || 'N/A';
                            const fechaEmision = firstDetail.fecha_emision_oc || 'N/A';
                            const moneda = firstDetail.moneda_oc || 'N/A';
                            const total = firstDetail.total_general_orden ? parseFloat(firstDetail.total_general_orden).toFixed(2) : '0.00';

                            detailOcSummary.textContent = `${numeroOc} | ${fechaEmision} | ${moneda} ${total}`;
                        }

                        // Llenar tabla de detalles
                        if (tablaDetallesBody) {
                            tablaDetallesBody.innerHTML = '';

                            ocDetails.forEach((detail, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${detail.vehiculo_marca || 'N/A'}</td>
                            <td>${detail.vehiculo_modelo || 'N/A'}</td>
                            <td>${detail.vehiculo_version || 'N/A'}</td>
                            <td>${detail.vehiculo_combustible || 'N/A'}</td>
                            <td>${detail.vehiculo_anio_modelo || 'N/A'}</td>
                            <td>${detail.vehiculo_chasis || 'N/A'}</td>
                            <td>${detail.vehiculo_serie_motor || 'N/A'}</td>
                            <td>${detail.vehiculo_placa || 'N/A'}</td>
                            <td>${detail.vehiculo_placa_rotativa || 'N/A'}</td>
                            <td>${detail.vehiculo_color || 'N/A'}</td>
                            <td>${detail.moneda_oc || 'N/A'}</td>
                            <td>${detail.vehiculo_precio_unitario ? parseFloat(detail.vehiculo_precio_unitario).toFixed(2) : '0.00'}</td>
                        `;
                                tablaDetallesBody.appendChild(row);
                            });
                        }

                    } catch (error) {
                        showToast('No se ha podido cargar los datos', 'WARNING', 1200);
                    }
                });
            });


            if (botonVolver) {
                botonVolver.addEventListener('click', () => {

                    limpiarVistaDetalle();


                    $("#detalle-oc").slideUp(speedAnimation);
                    $("#lista-oc").slideDown(speedAnimation);
                });
            }




        });



        
    <script src="/assets/index-oc.js"></script>
