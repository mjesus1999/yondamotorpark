<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra - YONDA PERÚ</title>
    <link rel="stylesheet" href="/assets/css/OC.css">

</head>
<body>
    <!-- Botón para generar PDF -->
    <button class="btn-generate-pdf" onclick="generatePDF()" style="display: none;">Generar PDF</button>

    <!-- Indicador de carga -->
    <div id="loading-indicator" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 2px solid #d32f2f; border-radius: 10px; z-index: 2000; text-align: center;">
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
                
                if (data && Array.isArray(data) && data.length > 0) {
                    ocDetalles = data;
                    cargarDatos();
                    // Generar PDF automáticamente después de cargar los datos
                    setTimeout(() => {
                        generarPDFAutomatico();
                    }, 1000); // Esperar 1 segundo para que se renderice todo
                } else {
                    console.error('No se encontraron datos para esta orden de compra');
                    // Cargar datos de ejemplo si no hay datos
                    cargarDatosEjemplo();
                    setTimeout(() => {
                        generarPDFAutomatico();
                    }, 1000);
                }

            } catch (error) {
                console.error('Error al cargar los datos:', error);
                // Si hay error, generar PDF con datos vacíos
                setTimeout(() => {
                    generarPDFAutomatico();
                }, 1000);
            }
        }

        // Función para agrupar vehículos similares
        function agruparVehiculos(vehiculos) {
            const grupos = {};
            
            vehiculos.forEach(vehiculo => {
                const clave = `${vehiculo.vehiculo_marca}-${vehiculo.vehiculo_modelo}-${vehiculo.vehiculo_version}-${vehiculo.vehiculo_combustible}-${vehiculo.vehiculo_anio_modelo}-${vehiculo.vehiculo_color}`;
                
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
        function cargarDatos() {
            if (ocDetalles.length > 0) {
                const primerDetalle = ocDetalles[0];
                
                // Cargar datos generales
                document.getElementById('numero-oc').textContent = primerDetalle.numero_oc_formateado || '2025-00007';
                document.getElementById('punto-venta').textContent = primerDetalle.concesionario_ubigeo_completo || 'Chacoche / Abancay / Apurimac';
                document.getElementById('razon-social').textContent = primerDetalle.concesionario_razon_social || 'HYUNDAI ENGINEERING & CONSTRUCTION CO., LTD-SUCURSAL DEL PERU';
                document.getElementById('ruc').textContent = primerDetalle.concesionario_ruc || '20605661522';
                document.getElementById('fecha').textContent = primerDetalle.fecha_emision_oc || '18/07/2025';
                document.getElementById('direccion').textContent = primerDetalle.concesionario_direccion || 'Jiron San Martin 123';
                document.getElementById('vendedor').textContent = primerDetalle.concesionario_vendedor_contacto || 'Arturo Magallanes Castro';
                document.getElementById('telefono').textContent = primerDetalle.concesionario_telefono || '956888999';
                document.getElementById('observaciones').textContent = primerDetalle.observaciones_oc || 'Pago al contado.';
                
                // Cargar resumen financiero
                document.getElementById('valor-venta').textContent = `$ ${parseFloat(primerDetalle.total_valor_venta_orden || 42000).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                document.getElementById('igv').textContent = `$ ${parseFloat(primerDetalle.total_igv_orden || 7560).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                document.getElementById('total').textContent = `$ ${parseFloat(primerDetalle.total_general_orden || 49560).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                
                // Cargar vehículos agrupados
                cargarVehiculosAgrupados();
            } else {
                // Si no hay datos, mostrar datos de ejemplo
                cargarDatosEjemplo();
            }
        }

        // Función para cargar vehículos agrupados
        function cargarVehiculosAgrupados() {
            const tbody = document.getElementById('vehiculos-tbody');
            tbody.innerHTML = '';
            
            const vehiculosAgrupados = agruparVehiculos(ocDetalles);
            
            if (vehiculosAgrupados.length === 0) {
                return;
            }
            
            // Si hay muchos vehículos, usar un enfoque diferente
            if (vehiculosAgrupados.length >= 5) {
                cargarVehiculosConPrecioIndividual(vehiculosAgrupados);
            } else {
                cargarVehiculosConRowSpan(vehiculosAgrupados);
            }
        }
        
        // Función para cargar vehículos con rowSpan (para pocos vehículos)
        function cargarVehiculosConRowSpan(vehiculosAgrupados) {
            const tbody = document.getElementById('vehiculos-tbody');
            
            vehiculosAgrupados.forEach((grupo, index) => {
                const row = document.createElement('tr');
                
                // Agregar todas las celdas de datos
                row.appendChild(createCell(grupo.vehiculo.vehiculo_marca));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_modelo));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_version));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_combustible));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_anio_modelo));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_color));
                row.appendChild(createCell(grupo.cantidad));
                
                // Solo agregar la celda de precio en la primera fila
                if (index === 0) {
                    const precioCell = document.createElement('td');
                    precioCell.style.textAlign = 'center';
                    precioCell.style.fontWeight = 'bold';
                    precioCell.style.fontSize = '11px';
                    precioCell.style.verticalAlign = 'middle';
                    precioCell.rowSpan = vehiculosAgrupados.length;
                    precioCell.textContent = `$ ${parseFloat(ocDetalles[0].total_general_orden).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                    row.appendChild(precioCell);
                }
                
                tbody.appendChild(row);
            });
        }
        
        // Función para cargar vehículos con precio individual (para muchos vehículos)
        function cargarVehiculosConPrecioIndividual(vehiculosAgrupados) {
            const tbody = document.getElementById('vehiculos-tbody');
            const precioPorVehiculo = parseFloat(ocDetalles[0].total_general_orden) / ocDetalles.length;
            
            vehiculosAgrupados.forEach((grupo) => {
                const row = document.createElement('tr');
                
                // Agregar todas las celdas de datos
                row.appendChild(createCell(grupo.vehiculo.vehiculo_marca));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_modelo));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_version));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_combustible));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_anio_modelo));
                row.appendChild(createCell(grupo.vehiculo.vehiculo_color));
                row.appendChild(createCell(grupo.cantidad));
                
                // Agregar precio individual
                const precioCell = document.createElement('td');
                precioCell.style.textAlign = 'center';
                precioCell.style.fontWeight = 'bold';
                precioCell.style.fontSize = '11px';
                precioCell.style.verticalAlign = 'middle';
                precioCell.textContent = `$ ${precioPorVehiculo.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                row.appendChild(precioCell);
                
                tbody.appendChild(row);
            });
        }
        
        // Función auxiliar para crear celdas
        function createCell(text) {
            const cell = document.createElement('td');
            cell.textContent = text;
            return cell;
        }

        // Función para generar PDF
        function generatePDF() {
            const btn = document.querySelector('.btn-generate-pdf');
            btn.style.display = 'none';
            
            const element = document.querySelector('.container');
            const opt = {
                margin: [0.1, 0.3, 0.1, 0.3], // [top, right, bottom, left] - reducido el margen superior
                filename: 'orden-compra-yonda.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                btn.style.display = 'block';
            });
        }

        // Función para generar PDF automáticamente
        function generarPDFAutomatico() {
            const element = document.querySelector('.container');
            const loadingIndicator = document.getElementById('loading-indicator');
            
            const opt = {
                margin: [0.1, 0.3, 0.1, 0.3], // [top, right, bottom, left] - reducido el margen superior
                filename: `orden-compra-${getIdFromPath() || 'yonda'}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                // Ocultar indicador de carga
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
                // Cerrar la ventana después de generar el PDF
                setTimeout(() => {
                    window.close();
                }, 500);
            }).catch(error => {
                console.error('Error al generar PDF:', error);
                // Ocultar indicador de carga
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
                // Si hay error, mostrar mensaje y cerrar
                alert('Error al generar el PDF. La ventana se cerrará.');
                setTimeout(() => {
                    window.close();
                }, 1000);
            });
        }

        // Cargar datos cuando se carga la página
        document.addEventListener('DOMContentLoaded', cargarDatosDesdeAPI);
    </script>
</body>
</html> 