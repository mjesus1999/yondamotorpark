<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotizacion - Yonda Perú</title>
  <link rel="stylesheet" href="/assets/css/cot-reportes.css" />

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/vfs_fonts.js"></script>

</body>

</html>