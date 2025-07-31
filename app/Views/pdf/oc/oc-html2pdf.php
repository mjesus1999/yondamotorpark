<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra - YONDA PERÚ</title>
    <link rel="stylesheet" href="/assets/css/OC-reporte.css">

</head>

<body>
    <!-- Botón para generar PDF -->
    <button class="btn-generate-pdf" onclick="generatePDF()" style="display: none;">Generar PDF</button>

    <!-- Indicador de carga -->
    <div id="loading-indicator"
        style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 2px solid #d32f2f; border-radius: 10px; z-index: 2000; text-align: center;">
        <div style="color: #d32f2f; font-weight: bold; margin-bottom: 10px;">Generando PDF...</div>
        <div style="font-size: 12px;">Por favor espere...</div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="/assets/images/logo.png" alt="YONDA PERÚ">
            </div>
            <div class="orden-box">
                <div class="orden-title">ORDEN DE COMPRA</div>
                <div class="orden-number" id="numero-oc"></div>
            </div>
        </div>

        <!-- Sección de Concesionario -->
        <div class="section">
            <div class="section-title">CONCESIONARIO</div>
            <div class="section-content">
                <table class="info-table">
                    <tr>
                        <td class="label">Punto de venta:</td>
                        <td class="value" id="punto-venta"></td>
                        <td class="label">Banco:</td>
                        <td class="value"></td>
                    </tr>
                    <tr>
                        <td class="label">Razón Social:</td>
                        <td class="value" id="razon-social"></td>
                        <td class="label">N° Oper:</td>
                        <td class="value">-</td>
                    </tr>
                    <tr>
                        <td class="label">RUC:</td>
                        <td class="value" id="ruc"></td>
                        <td class="label">Fecha:</td>
                        <td class="value" id="fecha"></td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td class="value" colspan="3" id="direccion"></td>
                    </tr>
                    <tr>
                        <td class="label">Vendedor:</td>
                        <td class="value" id="vendedor"></td>
                        <td class="label">Teléfono:</td>
                        <td class="value" id="telefono"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección de Asociado -->
        <div class="section">
            <div class="section-title">ASOCIADO</div>
            <div class="section-content">
                <table class="info-table">
                    <tr>
                        <td class="label">Titular:</td>
                        <td class="value">YONDA & GRUPO HUARACA EIRL</td>
                        <td class="label">Teléfono:</td>
                        <td class="value">926743607</td>
                    </tr>
                    <tr>
                        <td class="label">DNI o RUC:</td>
                        <td class="value">20609396866</td>
                        <td class="label">Correo:</td>
                        <td class="value">asistentecontable@yondaperu.com</td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td class="value" colspan="3">PANAMERICANA SUR KM PUERTA 201</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección de Vehículos -->
        <div class="section">
            <div class="section-title">DESCRIPCIÓN DE VEHÍCULOS</div>
            <div class="section-content">
                <table class="vehicles-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">MARCA</th>
                            <th style="width: 15%;">MODELO</th>
                            <th style="width: 15%;">VERSIÓN</th>
                            <th style="width: 12%;">COMBUSTIBLE</th>
                            <th style="width: 8%;">AÑO</th>
                            <th style="width: 10%;">COLOR</th>
                            <th style="width: 10%;">CANTIDAD</th>
                            <th style="width: 15%;">IMPORTE</th>

                        </tr>
                    </thead>
                    <tbody id="vehiculos-tbody">
                        <!-- Los vehículos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-title">RESUMEN FINANCIERO</div>
                <div class="summary-row">
                    <span class="summary-label">Valor Venta:</span>
                    <span class="summary-value" id="valor-venta"></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">IGV:</span>
                    <span class="summary-value" id="igv"></span>
                </div>
                <div class="summary-row summary-total">
                    <span class="summary-label">TOTAL:</span>
                    <span class="summary-value" id="total"></span>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="observations-section">
            <div class="observations-title">Observaciones:</div>
            <div class="observations-content" id="observaciones"></div>
        </div>

        <!-- Sección de Aprobaciones -->
        <div class="section approval-section">
            <div class="section-title">APROBACIONES</div>
            <div class="section-content">
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>PREPARADO</th>
                            <th>APROBADO</th>
                            <th>AUTORIZADO</th>
                            <th>PROCESADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- HTML2PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


    <script>

        // Función para obtener parámetros de la URL
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            let results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Función para obtener el ID de la ruta
        function getIdFromPath() {
            const pathSegments = window.location.pathname.split('/');
            return pathSegments[pathSegments.length - 1];
        }

        // Variable global para los datos
        let ocOrden = null;
        let ocDetalles = [];

        // Función para cargar datos desde la API
        async function cargarDatosDesdeAPI() {
            try {
                const ocId = getIdFromPath() || getUrlParameter('id');

                if (!ocId) {
                    console.error('No se encontró ID de orden de compra en la URL');
                    return;
                }

                const response = await fetch(`/api/oc/${ocId}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data && data.orden && Array.isArray(data.vehiculos)) {
                    ocDetalles = data.vehiculos; // solo vehículos para agrupar
                    cargarDatos(data.orden, data.vehiculos);
                    setTimeout(() => {
                        generarPDFAutomatico();
                    }, 1000);
                } else {
                    console.error('No se encontraron datos válidos en la respuesta');
                    cargarDatosEjemplo();
                    setTimeout(() => {
                        generarPDFAutomatico();
                    }, 1000);
                }

            } catch (error) {
                console.error('Error al cargar los datos:', error);
                setTimeout(() => {
                    generarPDFAutomatico();
                }, 1000);
            }
        }

        // Función para agrupar vehículos similares
        function agruparVehiculos(vehiculos) {
            const grupos = {};

            vehiculos.forEach(vehiculo => {
                const clave = `${vehiculo.marca}-${vehiculo.modelo}-${vehiculo.version}-${vehiculo.combustible}-${vehiculo.anio_modelo}-${vehiculo.color}`;

                if (!grupos[clave]) {
                    grupos[clave] = {
                        vehiculo: vehiculo,
                        cantidad: 0
                    };
                }
                grupos[clave].cantidad++;
            });

            return Object.values(grupos);
        }


        // Función para cargar los datos
        function cargarDatos(orden, vehiculos) {
            // Datos del encabezado
            document.getElementById('numero-oc').textContent = orden.numero_oc_formateado || '-';
            document.getElementById('punto-venta').textContent = orden.concesionario.ubigeo || '-';
            document.getElementById('razon-social').textContent = orden.concesionario.razon_social || '-';
            document.getElementById('ruc').textContent = orden.concesionario.ruc || '-';
            document.getElementById('fecha').textContent = orden.fecha_emision_oc || '-';
            document.getElementById('direccion').textContent = orden.concesionario.direccion || '-';
            document.getElementById('vendedor').textContent = orden.concesionario.vendedor_contacto || '-';
            document.getElementById('telefono').textContent = orden.concesionario.telefono || '-';
            document.getElementById('observaciones').textContent = orden.observaciones_oc || 'Sin observaciones';

            // Resumen financiero
            document.getElementById('valor-venta').textContent = `$ ${parseFloat(orden.totales.valor_venta || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            document.getElementById('igv').textContent = `$ ${parseFloat(orden.totales.igv || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            document.getElementById('total').textContent = `$ ${parseFloat(orden.totales.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;

            // Guardamos en variable global para poder agrupar y usar en otras funciones
            ocDetalles = vehiculos;
            ocOrden = orden;

            // Cargar vehículos agrupados
            cargarVehiculosAgrupados();
        }

        // Función para cargar vehículos agrupados
        function cargarVehiculosAgrupados() {
            const tbody = document.getElementById('vehiculos-tbody');
            tbody.innerHTML = '';

            const vehiculosAgrupados = agruparVehiculos(ocDetalles);

            if (vehiculosAgrupados.length === 0) {
                return;
            }

            // Si hay muchos vehículos, usar un enfoque diferente(Se podría poner precio unitario)
            if (vehiculosAgrupados.length >= 5) {
                cargarVehiculosConRowSpan(vehiculosAgrupados);
            } else {
                cargarVehiculosConRowSpan(vehiculosAgrupados);
            }
        }

        // Función para cargar vehículos con rowSpan
        function cargarVehiculosConRowSpan(vehiculosAgrupados) {
            const tbody = document.getElementById('vehiculos-tbody');
            tbody.innerHTML = '';

            vehiculosAgrupados.forEach((grupo, index) => {
                const row = document.createElement('tr');

                // Datos base
                row.appendChild(createCell(grupo.vehiculo.marca));
                row.appendChild(createCell(grupo.vehiculo.modelo));
                row.appendChild(createCell(grupo.vehiculo.version));
                row.appendChild(createCell(grupo.vehiculo.combustible));
                row.appendChild(createCell(grupo.vehiculo.anio_modelo));
                row.appendChild(createCell(grupo.vehiculo.color));
                row.appendChild(createCell(grupo.cantidad));

                // Importe total 
                if (index === 0) {
                    const totalCell = document.createElement('td');
                    totalCell.style.textAlign = 'center';
                    totalCell.style.fontWeight = 'bold';
                    totalCell.style.fontSize = '11px';
                    totalCell.style.verticalAlign = 'middle';
                    totalCell.rowSpan = vehiculosAgrupados.length;
                    totalCell.textContent = `$ ${parseFloat(ocOrden.totales.total).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                    row.appendChild(totalCell);
                }
                tbody.appendChild(row);
            });
        }

        function createCell(text) {
            const cell = document.createElement('td');
            cell.textContent = text;
            return cell;
        }
        function generarPDFAutomatico() {
            const element = document.querySelector('.container');
            const loadingIndicator = document.getElementById('loading-indicator');

            // Ocultar el indicador de carga inmediatamente antes de generar el PDF
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }

            const opt = {
                margin: [0.4, 0.1, 0.1, 0.1],
                filename: `orden-compra-${getIdFromPath() || 'yonda'}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    
                    scale: 1.75,
                    useCORS: true,
                    // windowWidth y windowHeight para asegurar que html2canvas capture todo el contenido
                    windowWidth: document.documentElement.offsetWidth,
                    windowHeight: document.documentElement.offsetHeight
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter', 
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                console.log('PDF generado y descargado.');
                setTimeout(() => {
                    window.close(); 
                }, 500);
            }).catch(error => {
                console.error('Error al generar PDF:', error);
                alert('Error al generar el PDF. La ventana se cerrará.');
                setTimeout(() => {
                    window.close();
                }, 1000);
            });
        }


        document.addEventListener('DOMContentLoaded', cargarDatosDesdeAPI);
    </script>
</body>

</html>