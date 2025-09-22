<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotización - Yonda Perú</title>
  <style>
    /* Reset y estilos base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #000;
      background: #f5f5f5;
      padding: 20px;
    }

    .container {
      max-width: 210mm;
      margin: 0 auto;
      background: white;
      padding: 20mm;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      min-height: 297mm;
      position: relative;
    }

    /* Header con logo */
    .header {
      text-align: left;
      margin-bottom: 20px;
    }

    .header .logo img {
      max-height: 60px;
      width: auto;
    }

    /* Fecha */
    .date {
      text-align: right;
      font-size: 11px;
      margin-bottom: 15px;
    }

    /* Título principal */
    .main-title {
      text-align: center;
      font-size: 16px;
      font-weight: bold;
      margin: 20px 0;
      text-decoration: underline;
    }

    /* Código de cotización */
    .quote-number {
      text-align: right;
      font-size: 11px;
      margin-top: -15px;
      margin-bottom: 15px;
    }

    /* Presentación */
    .presentation {
      margin-bottom: 20px;
      font-size: 11px;
      text-align: justify;
    }

    .presentation p {
      margin-bottom: 8px;
    }

    /* Detalles del cliente */
    .client-details {
      margin-bottom: 25px;
      font-size: 11px;
    }

    .detail-row {
      display: flex;
      margin-bottom: 5px;
      align-items: baseline;
    }

    .detail-label {
      font-weight: bold;
      width: 150px;
      flex-shrink: 0;
    }

    .detail-colon {
      width: 20px;
      flex-shrink: 0;
    }

    .detail-value {
      flex: 1;
    }

    /* Tabla de financiamiento */
    .financing-section {
      margin-bottom: 20px;
    }

    .financing-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin-bottom: 20px;
    }

    .financing-table th,
    .financing-table td {
      border: 1px solid #000;
      padding: 8px;
      text-align: center;
    }

    .financing-table th {
      background-color: #f0f0f0;
      font-weight: bold;
    }

    .financing-table td:first-child {
      width: 30px;
    }

    .financing-table td:nth-child(2) {
      width: 60px;
    }

    .financing-table td:nth-child(3) {
      width: 100px;
    }

    .financing-table td:nth-child(4) {
      width: 80px;
    }

    .financing-table td:last-child {
      width: 100px;
    }

    /* Sección de requisitos */
    .requirements-section {
      margin-bottom: 30px;
      font-size: 11px;
      text-align: justify;
    }

    .requirements-section p {
      margin-bottom: 10px;
    }

    .requirements-list {
      margin: 15px 0;
      padding-left: 20px;
    }

    .requirements-list li {
      margin-bottom: 5px;
      text-align: justify;
    }

    /* Aviso de entrega */
    .delivery-notice {
      text-align: center;
      font-weight: bold;
      font-size: 12px !important;
      margin: 20px 0;
      padding: 10px;
      border: 1px solid #000;
    }

    /* Cierre */
    .closing {
      margin-top: 20px;
    }

    .closing p {
      margin-bottom: 8px;
    }

    /* Footer */
    .footer {
      position: absolute;
      bottom: 20mm;
      left: 20mm;
      right: 20mm;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .executive-info {
      font-size: 11px;
    }

    .executive-info p {
      margin-bottom: 3px;
      font-weight: bold;
    }

    .footer img {
      max-height: 40px;
      width: auto;
    }

    /* Botones de acción */
    .btn-generate-pdf {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #d32f2f;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .btn-generate-pdf:hover {
      background-color: #b71c1c;
    }

    /* Estados de carga */
    .loading {
      text-align: center;
      padding: 50px;
      font-size: 16px;
      color: #666;
    }

    .error {
      text-align: center;
      padding: 50px;
      font-size: 16px;
      color: #d32f2f;
    }

    /* Modal */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
      width: 90%;
    }

    .modal-content h3 {
      margin-bottom: 15px;
      color: #d32f2f;
    }

    .modal-buttons {
      margin-top: 20px;
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .modal-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .modal-btn-cancel {
      background: #ccc;
      color: #333;
    }

    .modal-btn-download {
      background: #d32f2f;
      color: white;
    }

    /* Responsive */
    @media print {
      body {
        background: white;
        padding: 0;
      }
      
      .container {
        box-shadow: none;
        margin: 0;
        padding: 15mm;
      }

      .btn-generate-pdf {
        display: none !important;
      }

      .modal-overlay {
        display: none !important;
      }
    }

    @media screen and (max-width: 768px) {
      .container {
        margin: 10px;
        padding: 15px;
      }
      
      .detail-row {
        flex-direction: column;
        margin-bottom: 10px;
      }
      
      .detail-label {
        width: auto;
        margin-bottom: 3px;
      }
      
      .detail-colon {
        display: none;
      }
      
      .footer {
        position: static;
        margin-top: 30px;
        flex-direction: column;
        gap: 20px;
        align-items: center;
      }
    }
  </style>
</head>

<body>
  <button class="btn-generate-pdf" onclick="generatePDF()">
    📄 Descargar PDF
  </button>

  <div id="loading-indicator" style="
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border: 2px solid #d32f2f;
    border-radius: 10px;
    z-index: 2000;
    text-align: center;
    display: none;
  ">
    <div style="color: #d32f2f; font-weight: bold; margin-bottom: 10px">
      Generando PDF...
    </div>
    <div style="font-size: 12px">Por favor espere...</div>
  </div>

  <!-- MODAL DE CONFIRMACIÓN DE DESCARGA -->
  <div id="download-modal" class="modal-overlay">
    <div class="modal-content">
      <h3>📄 Descargar Cotización</h3>
      <p>El documento está listo. ¿Desea descargar el PDF de la cotización?</p>
      <div class="modal-buttons">
        <button class="modal-btn modal-btn-cancel" onclick="cancelDownload()">
          Cancelar
        </button>
        <button class="modal-btn modal-btn-download" onclick="confirmDownload()">
          Descargar PDF
        </button>
      </div>
    </div>
  </div>

  <div class="container">
    <div id="loading-content" class="loading">
      Cargando datos de la cotización...
    </div>

    <div id="error-content" class="error" style="display: none;">
      Error al cargar los datos de la cotización. Por favor, intente nuevamente.
    </div>

    <div id="main-content" style="display: none;">
      <!-- HEADER -->
      <div class="header">
        <div class="logo">
          <img src="/assets/images/logos/cabecera-yondaa-1.png" alt="Cabecera Yonda" />
        </div>
      </div>

      <!-- FECHA -->
      <div class="date" id="fecha-cotizacion"></div>

      <!-- TÍTULO PRINCIPAL -->
      <h1 class="main-title">COTIZACIÓN VEHICULAR</h1>
      
      <!-- NÚMERO DE COTIZACIÓN -->
      <div class="quote-number" id="numero-cotizacion"></div>

      <!-- Sección de presentación -->
      <div class="presentation">
        <p><strong>Presente, atte. Yonda & Grupo Huaraca E.I.R.L.</strong></p>
        <p><strong>RUC:</strong> 20609396866</p>
        <p>
          De nuestra consideración, nos es grato dirigirnos a usted para
          brindarle una cotización vehicular de acuerdo al siguiente detalle:
        </p>
      </div>

      <!-- DETALLE CLIENTE -->
      <div class="client-details">
        <div class="detail-row">
          <span class="detail-label">Nombre del cliente</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="nombre_cliente"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">DNI</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="dni_cliente"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Celular</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="celular_cliente"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Marca del vehículo</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="marca_vehiculo"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Modelo</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="modelo_vehiculo"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Año</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="anio_vehiculo"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Color</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="color_vehiculo"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Precio</span>
          <span class="detail-colon">:</span>
          <span class="detail-value" id="precio_vehiculo"></span>
        </div>
      </div>

      <!-- CUADRO DE FINANCIAMIENTO -->
      <div class="financing-section">
        <table class="financing-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Meses</th>
              <th>Inicial</th>
              <th>Moneda</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody id="financing-table-body">
            <!-- Se llenarán dinámicamente -->
          </tbody>
        </table>
      </div>

      <!-- REQUISITOS Y DOCUMENTACIÓN -->
      <div class="requirements-section">
        <p>Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro ejecutivo
          de ventas la siguiente documentación:</p>

        <ol class="requirements-list" id="requirements-list">
          <!-- Se llenarán dinámicamente -->
        </ol>

        <p class="delivery-notice">ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES</p>

        <div class="closing">
          <p>Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar cumplimiento a
            todos los procesos señalados.</p>
          <p><strong>Atte.</strong></p>
        </div>
      </div>

      <!-- FOOTER -->
      <div class="footer">
        <div class="executive-info" id="asesor-info">
          <p id="asesor-nombre"><!-- CHARLY YACTAYO ORTIZ --></p>
          <p id="asesor-cargo"><!-- Ejecutivo de Ventas --></p>
          <p id="asesor-telefono"><!-- (056) 934 008 037 --></p>
        </div>
        <img src="/assets/images/logos/footer-yondaa1.png" alt="Piecera Yonda">
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/vfs_fonts.js"></script>

  <script>
    // Variables globales
    let cotizacionData = null;
    let isGeneratingPDF = false;

    // Función para obtener el ID de la cotización desde la URL
    function getCotizacionId() {
      // Suponiendo que la URL es algo como: /cotizacion/html2pdfReport/123
      const path = window.location.pathname;
      const parts = path.split('/');
      return parts[parts.length - 1];
    }

    // Función para cargar los datos de la cotización
    async function loadCotizacionData() {
      try {
        const cotizacionId = getCotizacionId();
        
        if (!cotizacionId || cotizacionId === '' || isNaN(cotizacionId)) {
          throw new Error('ID de cotización inválido');
        }

        const response = await fetch(`/cotizacion/apiShow/${cotizacionId}`);
        
        if (!response.ok) {
          throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.error) {
          throw new Error(data.error);
        }

        cotizacionData = data.cotizacion;
        populateDocument();
        
        // Mostrar el contenido y ocultar el loading
        document.getElementById('loading-content').style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
        document.querySelector('.btn-generate-pdf').style.display = 'block';

      } catch (error) {
        console.error('Error cargando cotización:', error);
        document.getElementById('loading-content').style.display = 'none';
        document.getElementById('error-content').style.display = 'block';
        document.getElementById('error-content').textContent = 
          'Error al cargar los datos de la cotización: ' + error.message;
      }
    }

    // Función para poblar el documento con los datos
    function populateDocument() {
      if (!cotizacionData) return;

      // Fecha
      const fechaElement = document.getElementById('fecha-cotizacion');
      if (cotizacionData.fecha) {
        const fecha = new Date(cotizacionData.fecha);
        const opciones = { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric',
          timeZone: 'America/Lima'
        };
        fechaElement.textContent = `Chincha, ${fecha.toLocaleDateString('es-PE', opciones)}`;
      }

      // Número de cotización
      const numeroElement = document.getElementById('numero-cotizacion');
      if (cotizacionData.id) {
        numeroElement.textContent = `- ${String(cotizacionData.id).padStart(7, '0')}`;
      }

      // Datos del cliente
      document.getElementById('nombre_cliente').textContent = cotizacionData.cliente?.nombre || '';
      document.getElementById('dni_cliente').textContent = cotizacionData.cliente?.dni || '';
      document.getElementById('celular_cliente').textContent = cotizacionData.cliente?.celular || '';

      // Datos del vehículo
      document.getElementById('marca_vehiculo').textContent = cotizacionData.vehiculo?.marca || '';
      document.getElementById('modelo_vehiculo').textContent = cotizacionData.vehiculo?.modelo || '';
      document.getElementById('anio_vehiculo').textContent = cotizacionData.vehiculo?.anio || '';
      document.getElementById('color_vehiculo').textContent = cotizacionData.vehiculo?.color || '';

      // Precio del vehículo
      const precioElement = document.getElementById('precio_vehiculo');
      if (cotizacionData.precios?.precio_mostrar) {
        const monedaSymbol = cotizacionData.precios.moneda_precio === 'USD' ? 'US$ ' : 'S/ ';
        precioElement.textContent = monedaSymbol + parseFloat(cotizacionData.precios.precio_mostrar).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }

      // Tabla de financiamiento
      populateFinancingTable();

      // Requisitos
      populateRequirements();

      // Datos del asesor
      document.getElementById('asesor-nombre').textContent = cotizacionData.asesor?.nombre_completo || 'CHARLY YACTAYO ORTIZ';
      document.getElementById('asesor-cargo').textContent = cotizacionData.asesor?.cargo || 'Ejecutivo de Ventas';
      
      let telefonoText = '';
      if (cotizacionData.asesor?.telefono) {
        telefonoText = `(056) ${cotizacionData.asesor.telefono}`;
      } else {
        telefonoText = '(056) 934 008 037';
      }
      document.getElementById('asesor-telefono').textContent = telefonoText;
    }

    // Función para poblar la tabla de financiamiento
    function populateFinancingTable() {
      const tbody = document.getElementById('financing-table-body');
      tbody.innerHTML = '';

      if (!cotizacionData.opciones_financiamiento || cotizacionData.opciones_financiamiento.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="5">No hay opciones de financiamiento disponibles</td>';
        tbody.appendChild(row);
        return;
      }

      cotizacionData.opciones_financiamiento.forEach((opcion, index) => {
        const row = document.createElement('tr');
        
        const inicial = parseFloat(opcion.inicial || 0);
        const monto = parseFloat(opcion.valorcuota || 0);
        const moneda = cotizacionData.moneda || 'PEN';
        const monedaSymbol = moneda === 'USD' ? 'US$ ' : 'S/ ';

        row.innerHTML = `
          <td>${index + 1}</td>
          <td>${opcion.numcuotas || 0}</td>
          <td>${monedaSymbol}${inicial.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
          <td>${moneda === 'USD' ? 'DÓLARES' : 'SOLES'}</td>
          <td>${monedaSymbol}${monto.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        `;
        
        tbody.appendChild(row);
      });
    }

    // Función para poblar los requisitos
    function populateRequirements() {
      const requirementsList = document.getElementById('requirements-list');
      requirementsList.innerHTML = '';

      if (!cotizacionData.requisitos || cotizacionData.requisitos.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No hay requisitos especificados';
        requirementsList.appendChild(li);
        return;
      }

      cotizacionData.requisitos.forEach(requisito => {
        const li = document.createElement('li');
        li.textContent = requisito.requisito || '';
        requirementsList.appendChild(li);
      });
    }

    // Función para generar PDF
    async function generatePDF() {
      if (isGeneratingPDF || !cotizacionData) return;
      
      document.getElementById('download-modal').style.display = 'flex';
    }

    function cancelDownload() {
      document.getElementById('download-modal').style.display = 'none';
    }

    async function confirmDownload() {
      document.getElementById('download-modal').style.display = 'none';
      document.getElementById('loading-indicator').style.display = 'block';
      
      isGeneratingPDF = true;

      try {
        // Preparar estructura del documento PDF
        const docDefinition = {
          content: [
            // Título
            {
              text: 'COTIZACIÓN VEHICULAR',
              style: 'title',
              alignment: 'center'
            },
            
            // Número de cotización
            {
              text: `- ${String(cotizacionData.id || 0).padStart(7, '0')}`,
              style: 'quoteNumber',
              alignment: 'right',
              margin: [0, -15, 0, 15]
            },

            // Presentación
            {
              text: 'Presente, atte. Yonda & Grupo Huaraca E.I.R.L.',
              style: 'bold',
              margin: [0, 0, 0, 5]
            },
            {
              text: 'RUC: 20609396866',
              style: 'bold',
              margin: [0, 0, 0, 10]
            },
            {
              text: 'De nuestra consideración, nos es grato dirigirnos a usted para brindarle una cotización vehicular de acuerdo al siguiente detalle:',
              style: 'normal',
              alignment: 'justify',
              margin: [0, 0, 0, 20]
            },

            // Datos del cliente
            ...buildClientDetails(),

            // Tabla de financiamiento
            ...buildFinancingTable(),

            // Requisitos
            ...buildRequirements(),

            // Aviso de entrega
            {
              text: 'ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES',
              style: 'deliveryNotice',
              alignment: 'center',
              margin: [0, 20, 0, 20]
            },

            // Cierre
            {
              text: 'Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar cumplimiento a todos los procesos señalados.',
              style: 'normal',
              alignment: 'justify',
              margin: [0, 0, 0, 10]
            },
            {
              text: 'Atte.',
              style: 'bold',
              margin: [0, 0, 0, 30]
            }
          ],
          styles: {
            title: {
              fontSize: 16,
              bold: true,
              decoration: 'underline',
              margin: [0, 20, 0, 20]
            },
            quoteNumber: {
              fontSize: 11
            },
            bold: {
              fontSize: 11,
              bold: true
            },
            normal: {
              fontSize: 11
            },
            clientDetail: {
              fontSize: 11,
              margin: [0, 2, 0, 2]
            },
            deliveryNotice: {
              fontSize: 12,
              bold: true,
              border: [true, true, true, true]
            }
          },
          defaultStyle: {
            fontSize: 11
          },
          pageMargins: [50, 60, 50, 60]
        };

        // Generar y descargar PDF
        pdfMake.createPdf(docDefinition).download(`Cotizacion_${String(cotizacionData.id || 0).padStart(7, '0')}.pdf`);

      } catch (error) {
        console.error('Error generando PDF:', error);
        alert('Error al generar el PDF: ' + error.message);
      } finally {
        isGeneratingPDF = false;
        document.getElementById('loading-indicator').style.display = 'none';
      }
    }

    // Función auxiliar para construir los detalles del cliente
    function buildClientDetails() {
      const monedaSymbol = cotizacionData.precios?.moneda_precio === 'USD' ? 'US$ ' : 'S/ ';
      const precio = parseFloat(cotizacionData.precios?.precio_mostrar || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      return [
        {
          columns: [
            { text: 'Nombre del cliente', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.cliente?.nombre || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'DNI', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.cliente?.dni || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Celular', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.cliente?.celular || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Marca del vehículo', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.vehiculo?.marca || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Modelo', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.vehiculo?.modelo || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Año', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.vehiculo?.anio || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Color', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: cotizacionData.vehiculo?.color || '', style: 'normal' }
          ],
          margin: [0, 2, 0, 2]
        },
        {
          columns: [
            { text: 'Precio', style: 'bold', width: 120 },
            { text: ':', width: 15 },
            { text: `${monedaSymbol}${precio}`, style: 'normal' }
          ],
          margin: [0, 2, 0, 25]
        }
      ];
    }

    // Función auxiliar para construir la tabla de financiamiento
    function buildFinancingTable() {
      if (!cotizacionData.opciones_financiamiento || cotizacionData.opciones_financiamiento.length === 0) {
        return [
          {
            text: 'No hay opciones de financiamiento disponibles',
            style: 'normal',
            margin: [0, 10, 0, 20]
          }
        ];
      }

      const moneda = cotizacionData.moneda || 'PEN';
      const monedaSymbol = moneda === 'USD' ? 'US$ ' : 'S/ ';
      const monedaText = moneda === 'USD' ? 'DÓLARES' : 'SOLES';

      const tableBody = [
        [
          { text: '#', style: 'bold' },
          { text: 'Meses', style: 'bold' },
          { text: 'Inicial', style: 'bold' },
          { text: 'Moneda', style: 'bold' },
          { text: 'Monto', style: 'bold' }
        ]
      ];

      cotizacionData.opciones_financiamiento.forEach((opcion, index) => {
        const inicial = parseFloat(opcion.inicial || 0);
        const monto = parseFloat(opcion.valorcuota || 0);

        tableBody.push([
          (index + 1).toString(),
          (opcion.numcuotas || 0).toString(),
          `${monedaSymbol}${inicial.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
          monedaText,
          `${monedaSymbol}${monto.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
        ]);
      });

      return [
        {
          table: {
            headerRows: 1,
            widths: [30, 60, 100, 80, 100],
            body: tableBody
          },
          layout: {
            hLineWidth: function() { return 1; },
            vLineWidth: function() { return 1; },
            hLineColor: function() { return '#000000'; },
            vLineColor: function() { return '#000000'; },
            fillColor: function(rowIndex) {
              return rowIndex === 0 ? '#f0f0f0' : null;
            }
          },
          margin: [0, 0, 0, 20]
        }
      ];
    }

    // Función auxiliar para construir los requisitos
    function buildRequirements() {
      const content = [
        {
          text: 'Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro ejecutivo de ventas la siguiente documentación:',
          style: 'normal',
          alignment: 'justify',
          margin: [0, 0, 0, 15]
        }
      ];

      if (!cotizacionData.requisitos || cotizacionData.requisitos.length === 0) {
        content.push({
          text: '1. No hay requisitos especificados',
          style: 'normal',
          margin: [20, 0, 0, 0]
        });
      } else {
        const requisitosFormatted = cotizacionData.requisitos.map((req, index) => {
          return {
            text: `${index + 1}. ${req.requisito || ''}`,
            style: 'normal',
            margin: [20, 2, 0, 2],
            alignment: 'justify'
          };
        });
        content.push(...requisitosFormatted);
      }

      return content;
    }

    // Cargar datos al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
      loadCotizacionData();
    });

    // Función auxiliar para formatear fecha
    function formatDate(dateString) {
      if (!dateString) return '';
      
      try {
        const date = new Date(dateString);
        const meses = [
          'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
          'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        
        const dia = date.getDate();
        const mes = meses[date.getMonth()];
        const año = date.getFullYear();
        
        return `${dia} de ${mes} de ${año}`;
      } catch (error) {
        console.error('Error formateando fecha:', error);
        return '';
      }
    }

    // Función auxiliar para obtener fecha actual formateada
    function getCurrentDateFormatted() {
      const now = new Date();
      const meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
      ];
      
      const dia = now.getDate();
      const mes = meses[now.getMonth()];
      const año = now.getFullYear();
      
      return `${dia} de ${mes} de ${año}`;
    }

    // Función para manejar errores de carga de imágenes
    function handleImageError(img) {
      console.warn('Error cargando imagen:', img.src);
      img.style.display = 'none';
    }

    // Función para validar datos antes de generar PDF
    function validateDataForPDF() {
      if (!cotizacionData) {
        throw new Error('No hay datos de cotización cargados');
      }

      if (!cotizacionData.cliente?.nombre) {
        throw new Error('Falta el nombre del cliente');
      }

      if (!cotizacionData.vehiculo?.marca || !cotizacionData.vehiculo?.modelo) {
        throw new Error('Faltan datos del vehículo');
      }

      return true;
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'error' ? '#f44336' : '#4caf50'};
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        z-index: 3000;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      `;
      
      notification.textContent = message;
      document.body.appendChild(notification);
      
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 5000);
    }

    // Función mejorada para generar PDF con mejor manejo de errores
    async function generatePDFImproved() {
      try {
        validateDataForPDF();

        const fechaCotizacion = cotizacionData.fecha 
          ? formatDate(cotizacionData.fecha)
          : getCurrentDateFormatted();

        // Construir datos del asesor para el footer
        const asesorData = buildAsesorFooter();

        const docDefinition = {
          content: [
            // Fecha en la esquina superior derecha
            {
              text: `Chincha, ${fechaCotizacion}`,
              style: 'fecha',
              alignment: 'right',
              margin: [0, 0, 0, 10]
            },

            // Título
            {
              text: 'COTIZACIÓN VEHICULAR',
              style: 'title',
              alignment: 'center',
              margin: [0, 20, 0, 5]
            },
            
            // Número de cotización
            {
              text: `- ${String(cotizacionData.id || 0).padStart(7, '0')}`,
              style: 'quoteNumber',
              alignment: 'right',
              margin: [0, 0, 0, 20]
            },

            // Presentación
            {
              text: 'Presente, atte. Yonda & Grupo Huaraca E.I.R.L.',
              style: 'presentacion',
              margin: [0, 0, 0, 8]
            },
            {
              text: 'RUC: 20609396866',
              style: 'presentacion',
              margin: [0, 0, 0, 15]
            },
            {
              text: 'De nuestra consideración, nos es grato dirigirnos a usted para brindarle una cotización vehicular de acuerdo al siguiente detalle:',
              style: 'normal',
              alignment: 'justify',
              margin: [0, 0, 0, 25]
            },

            // Datos del cliente
            ...buildClientDetails(),

            // Tabla de financiamiento
            ...buildFinancingTable(),

            // Requisitos
            ...buildRequirements(),

            // Aviso de entrega
            {
              canvas: [
                {
                  type: 'rect',
                  x: 0,
                  y: 0,
                  w: 515,
                  h: 25,
                  lineWidth: 1
                }
              ],
              margin: [0, 20, 0, 0]
            },
            {
              text: 'ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES',
              style: 'deliveryNotice',
              alignment: 'center',
              margin: [0, -18, 0, 20]
            },

            // Cierre
            {
              text: 'Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar cumplimiento a todos los procesos señalados.',
              style: 'normal',
              alignment: 'justify',
              margin: [0, 0, 0, 15]
            },
            {
              text: 'Atte.',
              style: 'presentacion',
              margin: [0, 0, 0, 60]
            },

            // Footer con datos del asesor
            ...asesorData
          ],
          styles: {
            fecha: {
              fontSize: 11
            },
            title: {
              fontSize: 16,
              bold: true,
              decoration: 'underline'
            },
            quoteNumber: {
              fontSize: 11
            },
            presentacion: {
              fontSize: 11,
              bold: true
            },
            normal: {
              fontSize: 11
            },
            deliveryNotice: {
              fontSize: 12,
              bold: true
            },
            asesorFooter: {
              fontSize: 11,
              bold: true
            }
          },
          defaultStyle: {
            fontSize: 11,
            lineHeight: 1.3
          },
          pageMargins: [50, 60, 50, 60],
          info: {
            title: `Cotización ${String(cotizacionData.id || 0).padStart(7, '0')}`,
            author: 'Yonda & Grupo Huaraca E.I.R.L.',
            subject: 'Cotización Vehicular',
            creator: 'Sistema Yonda',
            producer: 'Yonda Perú'
          }
        };

        // Generar y descargar PDF
        const fileName = `Cotizacion_${String(cotizacionData.id || 0).padStart(7, '0')}.pdf`;
        pdfMake.createPdf(docDefinition).download(fileName);
        
        showNotification('PDF generado exitosamente', 'success');
        
      } catch (error) {
        console.error('Error generando PDF:', error);
        showNotification('Error al generar el PDF: ' + error.message, 'error');
        throw error;
      }
    }

    // Función para construir el footer con datos del asesor
    function buildAsesorFooter() {
      const asesorNombre = cotizacionData.asesor?.nombre_completo || 'LETICIA LLANA LOZANO';
      const asesorCargo = cotizacionData.asesor?.cargo || 'Jefe de Logística';
      const asesorTelefono = cotizacionData.asesor?.telefono 
        ? `(056) ${cotizacionData.asesor.telefono}`
        : '(056) 934 008 037';

      return [
        {
          columns: [
            {
              stack: [
                {
                  text: asesorNombre,
                  style: 'asesorFooter'
                },
                {
                  text: asesorCargo,
                  style: 'asesorFooter'
                },
                {
                  text: `TELÉFONO: ${asesorTelefono}`,
                  style: 'asesorFooter'
                }
              ],
              width: '50%'
            },
            {
              text: '', // Espacio para el logo (que no podemos incluir en el PDF)
              width: '50%'
            }
          ],
          margin: [0, 40, 0, 0]
        }
      ];
    }

    // Actualizar la función confirmDownload para usar la versión mejorada
    async function confirmDownload() {
      document.getElementById('download-modal').style.display = 'none';
      document.getElementById('loading-indicator').style.display = 'block';
      
      isGeneratingPDF = true;

      try {
        await generatePDFImproved();
      } catch (error) {
        console.error('Error en confirmDownload:', error);
      } finally {
        isGeneratingPDF = false;
        document.getElementById('loading-indicator').style.display = 'none';
      }
    }

    // Event listeners adicionales
    window.addEventListener('beforeunload', function(e) {
      if (isGeneratingPDF) {
        e.preventDefault();
        e.returnValue = 'Se está generando un PDF. ¿Estás seguro de que deseas salir?';
        return e.returnValue;
      }
    });

    // Función para debug (opcional - puedes eliminarla en producción)
    window.debugCotizacion = function() {
      console.log('Datos de cotización cargados:', cotizacionData);
      return cotizacionData;
    };
  </script>
</body>
</html>