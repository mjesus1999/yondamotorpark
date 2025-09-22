<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotizacion - Yonda Perú</title>
  <link rel="stylesheet" href="/assets/css/cot-reportes.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      line-height: 1.15;
    }

    .main-title {
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      color: #000000;
      margin: 20px 0;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      margin-bottom: 20px;
      margin-top: -10px;
    }

    .date {
      text-align: right;
      font-weight: normal;
      font-size: 11px;
      margin-bottom: 20px;
    }

    .logo img {
      max-width: 100%;
      height: auto;
    }

    .section {
      margin: 20px 0;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
    }

    .presentation {
      background-color: transparent;
      line-height: 1.15;
      margin: 20px 0;
      padding: 0;
      font-size: 12px;
    }

    .presentation p {
      margin: 8px 0;
    }

    .client-details {
      background-color: transparent;
      line-height: 1.0;
      margin: 20px 0;
      padding: 0;
    }

    .client-details .detail-row {
      display: flex;
      margin: 5px 0;
    }

    .client-details .detail-label {
      width: 180px;
      font-weight: bold;
      flex-shrink: 0;
    }

    .client-details .detail-colon {
      margin: 0 10px;
      font-weight: bold;
    }

    .client-details .detail-value {
      flex: 1;
      font-weight: bold;
    }

    .financing-section {
      margin: 30px 0;
      padding-right: 15px;
    }

    .financing-table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
      border: 0.5px solid black;
    }

    .financing-table th,
    .financing-table td {
      border: 0.5px solid black;
      padding: 8px 12px;
      text-align: center;
      font-weight: bold;
    }

    .financing-table th {
      background-color: #f5f5f5;
    }

    .financing-table td {
      background-color: white;
    }

    .requirements-section {
      margin: 30px 0;
      font-size: 12px;
      line-height: 1.15;
    }

    .requirements-section p {
      margin: 10px 0;
    }

    .requirements-list {
      margin: 15px 0;
      padding-left: 20px;
    }

    .requirements-list li {
      margin: 5px 0;
      list-style-position: inside;
      font-weight: bold;
    }

    .delivery-notice {
      font-weight: bold;
      margin: 20px 0;
      text-align: center;
    }

    .closing {
      margin: 20px 0;
      font-size: 12px;
    }

    .footer {
      margin-top: 40px;
      text-align: right;
      border-top: 1px solid #ddd;
      /* padding-top: 20px; */
    }

    .footer .executive-info {
      margin: 15px 0;
      line-height: 1.15;
      padding-right: 15px;
    }

    .footer .executive-info p {
      margin: 5px 0;
    }

    .footer img {
      max-width: 100%;
      height: auto;
      margin-top: 20px;
    }

    .loading {
      text-align: center;
      padding: 20px;
      color: #666;
    }

    .error {
      color: #d32f2f;
      text-align: center;
      padding: 20px;
      background-color: #ffebee;
      border: 1px solid #ffcdd2;
      border-radius: 4px;
      margin: 20px 0;
    }

    /* MODAL STYLES */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 3000;
      display: none;
      justify-content: center;
      align-items: center;
    }

    .modal-overlay.show {
      display: flex;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      min-width: 350px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .modal-content h3 {
      color: #d32f2f;
      margin-bottom: 15px;
      font-size: 18px;
    }

    .modal-content p {
      margin-bottom: 25px;
      font-size: 14px;
      color: #666;
    }

    .modal-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
    }

    .modal-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .modal-btn-cancel {
      background-color: #666;
      color: white;
    }

    .modal-btn-cancel:hover {
      background-color: #555;
    }

    .modal-btn-download {
      background-color: #d32f2f;
      color: white;
    }

    .modal-btn-download:hover {
      background-color: #b71c1c;
    }

    /* Estilos específicos para PDF */
    @media print {
      .btn-generate-pdf {
        display: none !important;
      }

      #loading-indicator {
        display: none !important;
      }

      #download-modal {
        display: none !important;
      }

      body {
        background: white !important;
      }

      .container {
        max-width: none !important;
        padding: 0 !important;
      }
    }

    /* Optimizaciones para el PDF generado */
    .pdf-optimized {
      page-break-inside: avoid;
    }

    .financing-table {
      page-break-inside: avoid;
    }

    .requirements-section {
      page-break-inside: avoid;
    }
  </style>
</head>

<body>
  <button class="btn-generate-pdf" onclick="generatePDF()" style="
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
        display: none;
      " onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'">
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
          <p id="asesor-nombre">CHARLY YACTAYO ORTIZ</p>
          <p id="asesor-cargo">Ejecutivo de Ventas</p>
          <p id="asesor-telefono">(056) 934 008 037</p>
        </div>
        <img src="/assets/images/logos/footer-yondaa1.png" alt="Piecera Yonda">
      </div>
    </div>
  </div>

  <!-- html2pdf.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <!-- Variable global para el ID de cotización (debe ser definida por PHP) -->
  <script>
    // Esta variable debe ser definida por el controlador PHP
    window.cotizacionId = <?php echo json_encode($id ?? null); ?>;

    // Variable para controlar el proceso de descarga
    let isGeneratingPDF = false;
    let pdfBlob = null;
  </script>

  <script>
    /**
     * Función principal para cargar los datos de la cotización
     */
    async function cargarDatosCotizacion() {
      try {
        // Obtener el ID de cotización desde la URL o variable global
        const cotizacionId = obtenerIdCotizacion();

        console.log('ID de cotización detectado:', cotizacionId);
        console.log('URL actual:', window.location.pathname);

        if (!cotizacionId) {
          throw new Error('No se encontró el ID de la cotización en la URL');
        }

        // Realizar petición al API
        const apiUrl = `/api/cotizacion/${cotizacionId}`;
        console.log('Realizando petición a:', apiUrl);

        const response = await fetch(apiUrl);

        console.log('Respuesta del servidor:', response.status, response.statusText);

        if (!response.ok) {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        if (!data.cotizacion) {
          throw new Error('Datos de cotización no válidos en la respuesta');
        }

        // Llenar los datos en la plantilla
        llenarDatosCotizacion(data.cotizacion);

        // Ocultar loading y mostrar contenido
        document.getElementById('loading-content').style.display = 'none';
        document.getElementById('main-content').style.display = 'block';

        // Mostrar botón de descarga PDF después de cargar los datos
        setTimeout(() => {
          document.querySelector('.btn-generate-pdf').style.display = 'block';
        }, 500);

      } catch (error) {
        console.error('Error al cargar datos:', error);
        mostrarError(error.message);
      }
    }

    /**
     * Obtiene el ID de cotización desde diferentes fuentes posibles
     */
    function obtenerIdCotizacion() {
      // Método 1: Desde variable global (si se pasa desde PHP)
      if (typeof window.cotizacionId !== 'undefined') {
        return window.cotizacionId;
      }

      // Método 2: Desde la URL (para rutas como /cotizacion/reporte/123)
      const urlParts = window.location.pathname.split('/');

      // Buscar 'reporte' en la URL y tomar el siguiente elemento
      const reporteIndex = urlParts.indexOf('reporte');
      if (reporteIndex !== -1 && urlParts[reporteIndex + 1]) {
        return parseInt(urlParts[reporteIndex + 1]);
      }

      // Método 3: Buscar cualquier número al final de la URL
      const ultimoSegmento = urlParts[urlParts.length - 1];
      if (ultimoSegmento && /^\d+$/.test(ultimoSegmento)) {
        return parseInt(ultimoSegmento);
      }

      // Método 4: Desde parámetros GET
      const urlParams = new URLSearchParams(window.location.search);
      const id = urlParams.get('id');
      if (id) {
        return parseInt(id);
      }

      return null;
    }

    /**
     * Llena todos los datos de la cotización en la plantilla
     */
    function llenarDatosCotizacion(cotizacion) {
      // Datos del cliente
      actualizarElemento('nombre_cliente', cotizacion.cliente?.nombre);
      actualizarElemento('dni_cliente', cotizacion.cliente?.dni);
      actualizarElemento('celular_cliente', cotizacion.cliente?.celular);

      // Datos del vehículo
      actualizarElemento('marca_vehiculo', cotizacion.vehiculo?.marca);
      actualizarElemento('modelo_vehiculo', cotizacion.vehiculo?.modelo);
      actualizarElemento('anio_vehiculo', cotizacion.vehiculo?.anio);
      actualizarElemento('color_vehiculo', cotizacion.vehiculo?.color);

      // Precio del vehículo
      const precio = formatearPrecio(
        cotizacion.precios?.precio_mostrar,
        cotizacion.precios?.moneda_precio
      );
      actualizarElemento('precio_vehiculo', precio);

      // Datos del asesor
      actualizarElemento('asesor-nombre', cotizacion.asesor?.nombre);
      actualizarElemento('asesor-cargo', cotizacion.asesor?.cargo);

      const telefonoFormateado = formatearTelefono(cotizacion.asesor?.telefono);
      actualizarElemento('asesor-telefono', telefonoFormateado);

      // Fecha de la cotización
      if (cotizacion.fecha) {
        const fechaFormateada = formatearFecha(cotizacion.fecha);
        actualizarElemento('fecha-cotizacion', fechaFormateada);
      }

      // Llenar opciones de financiamiento
      llenarOpcionesFinanciamiento(cotizacion.opciones_financiamiento || []);

      // Llenar requisitos
      llenarRequisitos(cotizacion.requisitos || [], cotizacion.gastosadministrativos);
    }

    /**
     * Actualiza un elemento del DOM con validación
     */
    function actualizarElemento(id, valor) {
      const elemento = document.getElementById(id);
      if (elemento) {
        elemento.textContent = valor || 'No especificado';
      }
    }

    /**
     * Formatea el precio según la moneda
     */
    function formatearPrecio(precio, moneda) {
      if (!precio || precio === '0.00') {
        return 'Por consultar';
      }

      const simboloMoneda = moneda === 'USD' ? 'US$ ' : 'S/ ';
      const precioFormateado = parseFloat(precio).toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      return simboloMoneda + precioFormateado;
    }

    /**
     * Formatea el teléfono con código de área
     */
    function formatearTelefono(telefono) {
      if (!telefono) return '(056) 934 008 037'; // Valor por defecto

      // Si ya tiene paréntesis, devolverlo tal como está
      if (telefono.includes('(')) {
        return telefono;
      }

      // Si tiene 9 dígitos, agregar código de área
      if (telefono.length === 9) {
        return `(056) ${telefono}`;
      }

      return telefono;
    }

    /**
     * Formatea la fecha al formato requerido
     */
    function formatearFecha(fechaISO) {
      if (!fechaISO) {
        const hoy = new Date();
        return `Chincha, ${hoy.getDate()} de ${obtenerMes(hoy.getMonth())} de ${hoy.getFullYear()}`;
      }

      const fecha = new Date(fechaISO);
      const dia = fecha.getDate();
      const mes = obtenerMes(fecha.getMonth());
      const anio = fecha.getFullYear();

      return `Chincha, ${dia} de ${mes} de ${anio}`;
    }

    /**
     * Obtiene el nombre del mes en español
     */
    function obtenerMes(numeroMes) {
      const meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'
      ];
      return meses[numeroMes] || 'enero';
    }

    /**
     * Llena la tabla de opciones de financiamiento
     */
    function llenarOpcionesFinanciamiento(opciones) {
      const tbody = document.getElementById('financing-table-body');
      if (!tbody) return;

      tbody.innerHTML = '';

      if (!opciones || opciones.length === 0) {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td colspan="5" style="text-align: center; color: #666;">
              No hay opciones de financiamiento disponibles
            </td>
          `;
        tbody.appendChild(fila);
        return;
      }

      opciones.forEach((opcion, index) => {
        const fila = document.createElement('tr');

        const numero = index + 1;
        const meses = opcion.numcuotas || 0;
        const inicial = parseFloat(opcion.inicial || 0).toLocaleString('es-PE', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        });
        const moneda = opcion.moneda === 'USD' ? 'DÓLARES' : 'SOLES';
        const cuota = parseFloat(opcion.valorcuota || 0).toLocaleString('es-PE', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        fila.innerHTML = `
            <td>${numero}</td>
            <td>${meses}</td>
            <td>${inicial}</td>
            <td>${moneda}</td>
            <td>${cuota}</td>
          `;

        tbody.appendChild(fila);
      });
    }

    /**
     * Llena la lista de requisitos dinámicamente
     */
    function llenarRequisitos(requisitos, gastosAdmin) {
      const lista = document.getElementById('requirements-list');
      if (!lista) return;

      lista.innerHTML = '';

      if (!requisitos || requisitos.length === 0) {
        // Usar requisitos por defecto si no hay datos
        const requisitosDefecto = [
          'Fotocopia DNI del titular y cónyuge',
          'Copia del último recibo pagado de servicios (luz o agua)',
          'Copia simple de vivienda (título de propiedad / certificado de posesión, copia literal)',
          'Declaración jurada de ingresos',
          'Licencia de conducir',
          'Récord de papeletas',
          'DNI aval (DNI cónyuge de ser necesario)',
          'Evaluación de gastos familiares',
          '30% de inicial como mínimo (aumenta según precio de la unidad)',
          'Verificación domiciliaria y laboral',
          `Pago único por gastos administrativos S/ ${(gastosAdmin || 1500).toLocaleString('es-PE', { minimumFractionDigits: 2 })}`,
          'Seguro vehicular (bajo evaluación)',
          'GPS satelital',
          'Recibo de servicios',
          'Boletas de pago'
        ];

        requisitosDefecto.forEach(requisito => {
          const li = document.createElement('li');
          li.textContent = requisito;
          lista.appendChild(li);
        });

        return;
      }

      requisitos.forEach(req => {
        const li = document.createElement('li');
        li.textContent = req.requisito || req;
        lista.appendChild(li);
      });
    }

    /**
     * Muestra el mensaje de error
     */
    function mostrarError(mensaje = null) {
      document.getElementById('loading-content').style.display = 'none';
      const errorContent = document.getElementById('error-content');

      if (mensaje) {
        errorContent.innerHTML = `
            <strong>Error:</strong> ${mensaje}<br>
            <small>Verifique la consola del navegador para más detalles.</small>
          `;
      }

      errorContent.style.display = 'block';
    }

    /**
     * Función actualizada para generar PDF con modal de confirmación
     */
    async function generatePDF() {
      if (isGeneratingPDF) {
        console.log('Ya se está generando un PDF...');
        return;
      }

      isGeneratingPDF = true;

      // Mostrar indicador de carga
      const loadingIndicator = document.getElementById('loading-indicator');
      loadingIndicator.style.display = 'block';

      try {
        // Configurar opciones del PDF
        const opt = {
          margin: [10, 10, 10, 10],
          filename: `cotizacion_${obtenerIdCotizacion()}_${new Date().toISOString().slice(0, 10)}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: {
            scale: 2,
            useCORS: true,
            letterRendering: true,
            allowTaint: true
          },
          jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
          }
        };

        // Obtener elemento a convertir
        const element = document.getElementById('main-content');

        // Generar PDF y almacenarlo
        pdfBlob = await html2pdf().set(opt).from(element).toPdf().output('blob');

        console.log('PDF generado correctamente');

      } catch (error) {
        console.error('Error al generar PDF:', error);
        alert('Error al generar el PDF. Por favor, intente nuevamente.');
        isGeneratingPDF = false;
        loadingIndicator.style.display = 'none';
        return;
      }

      // Ocultar indicador de carga
      loadingIndicator.style.display = 'none';

      // Mostrar modal de confirmación
      const modal = document.getElementById('download-modal');
      modal.classList.add('show');

      isGeneratingPDF = false;
    }

    /**
     * Confirmar descarga del PDF
     */
    function confirmDownload() {
      if (!pdfBlob) {
        alert('Error: El PDF no está disponible');
        return;
      }

      // Crear enlace de descarga con configuración específica
      const link = document.createElement('a');
      const url = URL.createObjectURL(pdfBlob);
      link.href = url;
      link.download = `cotizacion_${obtenerIdCotizacion()}_${new Date().toISOString().slice(0, 10)}.pdf`;

      // Asegurar que la descarga no abra nueva pestaña
      link.target = '_self';
      link.style.display = 'none';

      // Descargar archivo
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      // Limpiar URL del objeto
      URL.revokeObjectURL(url);

      // Cerrar modal
      closeModal();

      console.log('PDF descargado correctamente');

      // Cerrar la ventana actual después de la descarga (como en el archivo viejo)
      setTimeout(() => {
        try {
          // Intentar cerrar la ventana actual
          window.close();
        } catch (e) {
          console.log('No se pudo cerrar la ventana automáticamente');
          // Si no se puede cerrar, al menos mostrar mensaje
          alert('Descarga completada. Puede cerrar esta pestaña manualmente.');
        }
      }, 1000);
    }

    /**
     * Mostrar mensaje temporal de éxito
     */
    function showSuccessMessage(message) {
      // Crear elemento de notificación
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4caf50;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 4000;
        font-weight: bold;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
      `;
      notification.textContent = message;

      // Agregar al documento
      document.body.appendChild(notification);

      // Mostrar con animación
      setTimeout(() => {
        notification.style.opacity = '1';
      }, 100);

      // Ocultar después de 3 segundos
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
          if (notification.parentNode) {
            document.body.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    /**
     * Cancelar descarga del PDF
     */
    function cancelDownload() {
      // Limpiar blob si existe
      if (pdfBlob) {
        pdfBlob = null;
      }

      // Cerrar modal
      closeModal();

      console.log('Descarga cancelada por el usuario');
    }

    /**
     * Cerrar modal
     */
    function closeModal() {
      const modal = document.getElementById('download-modal');
      modal.classList.remove('show');
    }

    // Cerrar modal al hacer clic fuera de él
    window.onclick = function (event) {
      const modal = document.getElementById('download-modal');
      if (event.target === modal) {
        cancelDownload();
      }
    }

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        const modal = document.getElementById('download-modal');
        if (modal.classList.contains('show')) {
          cancelDownload();
        }
      }
    });

    // Inicializar cuando se carga la página
    document.addEventListener('DOMContentLoaded', function () {
      cargarDatosCotizacion();
    });
  </script>

</body>

</html>