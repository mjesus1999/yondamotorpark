<!-- app/views/pdf/cotizacion/cotizacion-html2pdf.php (con padding determinista para aumentar tamaño ~230KB) -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotización <?= $id ?> Dependiente - YONDA PERÚ</title>
  <link rel="stylesheet" href="/assets/css/cotizacion-reports.css" />

  <style>
    .content {
      width: 100%;
      box-sizing: border-box;
    }

    .small-text,
    .closing {
      text-align: justify;
      display: block;
      margin-right: 0;
    }

    .detalle {
      font-size: 10pt;
      line-height: 1.1;
      /* Reducido de 1.3 a 1.1 */
      margin: 8px 0;
      /* Reducido el margen */
    }

    .detalle td {
      padding: 2px 0;
      /* Reducido el padding vertical */
    }

    .detalle .label {
      width: 140px;
      /* Mantener ancho fijo para alineación */
      vertical-align: top;
    }

    .detalle .value {
      vertical-align: top;
    }

    .financiamiento-table {
      font-size: 10pt;
      margin: 12px 0;
      line-height: 1.2;
    }

    .financiamiento-table th {
      background-color: #e9ecef;
      font-weight: bold;
      text-align: center;
      padding: 6px 4px;
      border: 0.5px solid black;
      /* Cambiado de 1px solid #000000 */
      line-height: 1.1;
    }

    .financiamiento-table td {
      text-align: center;
      padding: 4px;
      border: 0.5px solid black;
      /* Cambiado de 1px solid #000000 */
      line-height: 1.1;
    }

    .financiamiento-table tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }

    .financiamiento-table tbody tr:hover {
      background-color: #e9ecef;
    }

    /* Asegurar que los montos estén alineados a la derecha */
    .financiamiento-table td:last-child {
      text-align: right;
      font-weight: 500;
    }

    #cot-id-suffix {
      font-weight: normal;
      font-size: 0.85em;
      margin-left: 8px;
      vertical-align: middle;
    }

    .title {
      font-size: 15pt;
    }

    @media print {

      .small-text,
      .closing {
        text-align: justify !important;
      }

      .detalle {
        font-size: 10pt !important;
        line-height: 1.1 !important;
        /* Asegurar interlineado reducido en impresión */
      }
    }
  </style>
</head>

<body>
  <!-- Botón oculto para generar PDF manualmente -->
  <button class="btn-generate-pdf" onclick="generatePDF()" style="display: none;">
    Generar PDF
  </button>

  <!-- Indicador de carga -->
  <div id="loading-indicator" style="position: fixed; top:50%; left:50%; transform:translate(-50%,-50%);
          background:white; padding:20px; border:2px solid #d32f2f;
          border-radius:10px; z-index:2000; text-align:center;">
    <div style="color:#d32f2f; font-weight:bold; margin-bottom:10px;">
      Generando PDF...
    </div>
    <div style="font-size:12px;">Por favor espere...</div>
  </div>

  <div class="container">
    <div class="header">
      <img src="/assets/images/logos/cabecera-yondaa.png" alt="Cabecera Yonda">
    </div>

    <div class="row">
      <div class="left"></div>
      <div class="right fecha" id="fecha"></div>
    </div>

    <!-- TÍTULO: se agrega sufijo con el ID formateado -->
    <h2 class="title"><strong id="main-title">COTIZACIÓN VEHICULAR</strong> <span id="cot-id-suffix"></span></h2>

    <div class="content">
      <p class="tight small-text">Presente, atte. Yonda & Grupo Huaraca E.I.R.L.</p>
      <p class="tight small-text">RUC: 20609396866</p>
      <p class="small-text justified-text">
        De nuestra consideración, nos es grato dirigirnos a usted para brindarle una
        cotización vehicular de acuerdo al siguiente detalle:
      </p>

      <!-- DETALLE CLIENTE -->
      <table class="detalle">
        <tr>
          <td class="label">Nombre del cliente</td>
          <td class="value">: <span id="cliente-nombre"></span></td>
        </tr>
        <tr>
          <td class="label">Dni</td>
          <td class="value">: <span id="cliente-dni"></span></td>
        </tr>
        <tr>
          <td class="label">Celular</td>
          <td class="value">: <span id="cliente-celular"></span></td>
        </tr>
        <tr>
          <td class="label">Marca del vehículo</td>
          <td class="value">: <span id="vehiculo-marca"></span></td>
        </tr>
        <tr>
          <td class="label">Modelo</td>
          <td class="value">: <span id="vehiculo-modelo"></span></td>
        </tr>
        <tr>
          <td class="label">Año</td>
          <td class="value">: <span id="vehiculo-anio"></span></td>
        </tr>
        <tr>
          <td class="label">Color</td>
          <td class="value">: <span id="vehiculo-color"></span></td>
        </tr>
        <tr>
          <td class="label">Precio</td>
          <td class="value">: <span id="precio-vehiculo"></span></td>
        </tr>
      </table>

      <!-- CUADRO DE PRECIOS -->
      <table class="financiamiento-table" border="0.5"
        style="width: 100%; margin: 12px 0; border-collapse: collapse; font-size: 10pt; border: 0.5px solid black;">
        <thead>
          <tr>
            <th style="border: 0.5px solid black; padding: 6px 4px; background-color: #e9ecef;">#</th>
            <th style="border: 0.5px solid black; padding: 6px 4px; background-color: #e9ecef;">Meses</th>
            <th style="border: 0.5px solid black; padding: 6px 4px; background-color: #e9ecef;">Inicial</th>
            <th style="border: 0.5px solid black; padding: 6px 4px; background-color: #e9ecef;">Moneda</th>
            <th style="border: 0.5px solid black; padding: 6px 4px; background-color: #e9ecef;">Monto</th>
          </tr>
        </thead>
        <tbody id="financiamiento-tbody">
        </tbody>
      </table>

      <!-- <table class="pricing">
        <tr>
          <td>PRECIO</td>
          <td><span id="precio-usd"></span></td>
          <td>INICIAL</td>
          <td><span id="inicial-soles"></span></td>
        </tr>
        <tr>
          <td>24 MESES</td>
          <td><span id="cuota-24"></span></td>
          <td>36 MESES</td>
          <td><span id="cuota-36"></span></td>
        </tr>
        <tr>
          <td>48 MESES</td>
          <td><span id="cuota-48"></span></td>
          <td>60 MESES</td>
          <td><span id="cuota-60"></span></td>
        </tr>
      </table> -->

      <!-- INSTRUCCIONES -->
      <div class="instructions">
        <p>
          Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro
          ejecutivo de ventas la siguiente documentación:
        </p>
        <!-- <p><strong>Modalidad: </strong><span id="format-name"></span></p> -->
        <ol id="instructions-list">
        </ol>
        <p><span class="selected">ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES</span></p>
        <p class="closing">
          Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar
          cumplimiento a todos los procesos señalados.
        </p>
        <p class="closing"><strong>Atte.</strong></p>
      </div>
    </div>
    <div class="footer">
      <div class="footer-text" id="asesor-info">
        <span id="asesor-nombre"><!-- CHARLY YACTAYO ORTIZ --></span><br>
        <span id="asesor-cargo"><!-- Ejecutivo de Ventas --></span><br>
        TELÉFONO: <span id="asesor-telefono"><!-- (056) 934 008 037 --></span>
      </div>
      <img src="/assets/images/logos/footer-yondaa.png" alt="Piecera Yonda">
    </div>
  </div>

  <!-- html2pdf.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    const cotId = <?= json_encode($id, JSON_NUMERIC_CHECK) ?>;

    function getIdFromPath() {
      const parts = window.location.pathname.split('/');
      return parts[parts.length - 1];
    }

    function formatDateSpanish(dateInput, ciudad = 'Chincha') {
      const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
      let dt;
      if (!dateInput) dt = new Date();
      else if (dateInput instanceof Date) dt = dateInput;
      else {
        const normalized = String(dateInput).replace(' ', 'T');
        dt = new Date(normalized);
        if (isNaN(dt.getTime())) {
          const parts = String(dateInput).split(/[-T ]/);
          if (parts.length >= 3) {
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            dt = new Date(y, m, d);
          } else dt = new Date();
        }
      }
      const day = dt.getDate();
      const month = months[dt.getMonth()] || '';
      const year = dt.getFullYear();
      return `${ciudad}, ${day} de ${month.toLowerCase()} de ${year}`;
    }

    function populateFinanciamientoTable(opciones, moneda) {
      const tbody = document.getElementById('financiamiento-tbody');
      if (!tbody) return;

      tbody.innerHTML = '';

      if (!opciones || opciones.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
              <td colspan="5" style="text-align: center; color: #666; font-style: italic; padding: 20px;">
                  Sin opciones de financiamiento disponibles
              </td>
          `;
        tbody.appendChild(tr);
        return;
      }

      // Ordenar opciones por número de cuotas
      const opcionesOrdenadas = [...opciones].sort((a, b) => {
        return (parseInt(a.numcuotas) || 0) - (parseInt(b.numcuotas) || 0);
      });

      // Crear filas para cada opción
      opcionesOrdenadas.forEach((opcion, index) => {
        const meses = parseInt(opcion.numcuotas) || 0;
        const inicial = parseFloat(opcion.inicial) || 0;
        const cuota = parseFloat(opcion.valorcuota) || 0;

        const monedaTexto = (moneda === 'USD') ? 'DÓLARES' : 'SOLES';
        const simboloMoneda = (moneda === 'USD') ? '$' : 'S/';

        const tr = document.createElement('tr');
        tr.innerHTML = `
              <td style="text-align: center; padding: 8px; border: 0.5px solid black;">${index + 1}</td>
              <td style="text-align: center; padding: 8px; border: 0.5px solid black;">${meses}</td>
              <td style="text-align: center; padding: 8px; border: 0.5px solid black;">${simboloMoneda} ${inicial.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
              <td style="text-align: center; padding: 8px; border: 0.5px solid black;">${monedaTexto}</td>
              <td style="text-align: right; padding: 8px; border: 0.5px solid black; font-weight: 500;">${simboloMoneda} ${cuota.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
          `;
        tbody.appendChild(tr);
      });
    }

    async function fetchAndFill() {
      try {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';

        const container = document.querySelector('.container');
        if (container) container.style.visibility = 'hidden';

        const res = await fetch(`/api/cotizacion/${cotId}`);
        if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`);

        const payload = await res.json();
        const cotizacion = payload.cotizacion ?? payload;

        // Llenar fecha
        const fechaStr = formatDateSpanish(cotizacion.fecha, 'Chincha');
        document.getElementById('fecha').textContent = fechaStr.replace(/ /g, '\u00A0');

        try {
          const idNum = cotizacion.id || cotId || getIdFromPath() || '';
          const padded = String(idNum).padStart(7, '0');
          const suffixEl = document.getElementById('cot-id-suffix');
          if (suffixEl) suffixEl.textContent = `- ${padded}`;
        } catch (e) {
          console.warn('No se pudo ajustar sufijo del título:', e);
        }

        // Llenar datos del cliente
        document.getElementById('cliente-nombre').textContent = (cotizacion.cliente && cotizacion.cliente.nombre) ? cotizacion.cliente.nombre : '';
        document.getElementById('cliente-dni').textContent = (cotizacion.cliente && cotizacion.cliente.dni) ? cotizacion.cliente.dni : '';
        document.getElementById('cliente-celular').textContent = (cotizacion.cliente && cotizacion.cliente.celular) ? cotizacion.cliente.celular : '';

        // Llenar datos del vehículo
        document.getElementById('vehiculo-marca').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.marca) ? cotizacion.vehiculo.marca : '';
        document.getElementById('vehiculo-modelo').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.modelo) ? cotizacion.vehiculo.modelo : '';
        document.getElementById('vehiculo-anio').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.anio) ? cotizacion.vehiculo.anio : '';
        document.getElementById('vehiculo-color').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.color) ? cotizacion.vehiculo.color : '';

        // Llenar el precio del vehículo
        /* const precioVehiculo = cotizacion.precios ? cotizacion.precios.precio_usd : '0.00'; */
        /* const monedaCotizacion = cotizacion.moneda || 'PEN'; */
        /* const simboloPrecio = (monedaCotizacion === 'USD') ? '$' : 'S/'; */
        /* document.getElementById('precio-vehiculo').textContent = `${simboloPrecio} ${parseFloat(precioVehiculo).toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`; */

        const monedaCotizacion = cotizacion.moneda || 'PEN';
        let precioVehiculo = 0;
        let simboloPrecio = '';

        // Obtener el precio base
        const precioBase = cotizacion.precios ? parseFloat(cotizacion.precios.precio_original || cotizacion.precios.precio_usd || 0) : 0;

        // Determinar símbolo según moneda
        if (monedaCotizacion.toUpperCase() === 'USD') {
          simboloPrecio = '$';
          precioVehiculo = precioBase;
        } else {
          simboloPrecio = 'S/';
          precioVehiculo = precioBase;
        }

        // Formatear y mostrar precio
        const precioFormateado = precioVehiculo.toLocaleString('es-PE', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        document.getElementById('precio-vehiculo').textContent = `${simboloPrecio} ${precioFormateado}`;

        // NUEVO: Llenar la tabla de financiamiento
        const opcionesFinanciamiento = cotizacion.opciones_financiamiento || [];
        populateFinanciamientoTable(opcionesFinanciamiento, monedaCotizacion);

        //Llenar datos del asesor
        if (cotizacion.asesor) {
          const asesorNombre = document.getElementById('asesor-nombre');
          const asesorCargo = document.getElementById('asesor-cargo');
          const asesorTelefono = document.getElementById('asesor-telefono');

          if (asesorNombre) {
            asesorNombre.textContent = cotizacion.asesor.nombre_completo || cotizacion.asesor.nombre || 'CHARLY YACTAYO ORTIZ';
          }

          if (asesorCargo) {
            // Mapear cargos específicos a "Ejecutivo de Ventas"
            let cargoMostrar = cotizacion.asesor.cargo || 'Ejecutivo de Ventas';

            // Si el cargo contiene "vent" o "asesor", mostrar "Ejecutivo de Ventas"
            if (cargoMostrar.toLowerCase().includes('vent') || cargoMostrar.toLowerCase().includes('asesor')) {
              cargoMostrar = 'Ejecutivo de Ventas';
            }

            asesorCargo.textContent = cargoMostrar;
          }

          if (asesorTelefono) {
            let telefono = cotizacion.asesor.telefono || '';

            if (telefono && !telefono.includes('056') && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            } else if (telefono && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            }

            asesorTelefono.textContent = telefono;
          }
        }

        const instructionsOl = document.getElementById('instructions-list');
        if (instructionsOl) instructionsOl.innerHTML = '';
        const requisitos = cotizacion.requisitos ?? [];

        if (requisitos.length > 0) {
          requisitos.forEach(r => {
            const li = document.createElement('li');
            li.textContent = (r.requisito || r.texto || r);
            instructionsOl.appendChild(li);
          });
        }

        if (loadingIndicator) loadingIndicator.style.display = 'none';
        if (container) container.style.visibility = 'visible';

        // Generar PDF después de llenar datos
        setTimeout(() => generarPDFCotizacion(), 10);

      } catch (error) {
        console.error('Error al cargar datos:', error);
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        alert('Error al cargar los datos de la cotización. La ventana se cerrará.');
        setTimeout(() => window.close(), 50);
      }
    }

    function imageToDataURL(imgEl, alpha = 1) {
      return new Promise((resolve, reject) => {
        if (!imgEl) return resolve(null);
        function convert() {
          try {
            const canvas = document.createElement('canvas');
            const w = imgEl.naturalWidth || imgEl.width || 100;
            const h = imgEl.naturalHeight || imgEl.height || 40;
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            if (alpha < 1) ctx.globalAlpha = alpha;
            ctx.drawImage(imgEl, 0, 0, w, h);
            resolve(canvas.toDataURL('image/jpeg', 0.85));
          } catch (err) {
            reject(err);
          }
        }
        if (imgEl.complete && imgEl.naturalWidth) convert();
        else { imgEl.onload = convert; imgEl.onerror = (e) => reject(e); }
      });
    }

    async function generarPDFCotizacion() {
      const element = document.querySelector('.container');
      const loadingIndicator = document.getElementById('loading-indicator');
      if (loadingIndicator) loadingIndicator.style.display = 'none';

      const filename = `cotizacion-${getIdFromPath() || 'yonda'}.pdf`;

      // Medidas del elemento para evitar desplazamientos
      const rect = element.getBoundingClientRect();
      const elWidth = Math.ceil(rect.width);
      const elHeight = Math.ceil(rect.height);

      // Escala razonable (usar devicePixelRatio pero limitarlo)
      const scale = Math.min(2.0, Math.max(1.25, (window.devicePixelRatio || 1) * 1.25));

      // Opciones para html2pdf / html2canvas
      const opt = {
        margin: [10, 10, 10, 10],
        filename,
        image: { type: 'jpeg', quality: 0.95 },
        html2canvas: {
          scale: scale,
          useCORS: true,
          allowTaint: false,
          logging: false,
          imageTimeout: 30000,
          windowWidth: Math.max(document.documentElement.clientWidth, elWidth),
          windowHeight: Math.max(document.documentElement.clientHeight, elHeight),
          scrollX: 0,
          scrollY: 0,
          backgroundColor: '#ffffff'
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      // Status visual
      let status = document.getElementById('pdf-status');
      if (!status) {
        status = document.createElement('div');
        status.id = 'pdf-status';
        status.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.8);color:#fff;padding:14px;border-radius:8px;z-index:9999;font-family:Arial,sans-serif;';
        status.innerHTML = '📄 Generando PDF...';
        document.body.appendChild(status);
      } else {
        status.style.display = 'block';
      }

      // Preparar imágenes de cabecera/footer
      const headerEl = document.querySelector('.header');
      const footerEl = document.querySelector('.footer');
      const imgTopEl = document.querySelector('.pdf-watermark.top') || (headerEl ? headerEl.querySelector('img') : null);
      const imgBottomEl = document.querySelector('.pdf-watermark.bottom') || (footerEl ? footerEl.querySelector('img') : null);

      let topData = null;
      let bottomData = null;
      try { if (imgTopEl) topData = await imageToDataURL(imgTopEl, 1); } catch (e) { console.warn('cabecera->dataURL failed', e); topData = null; }
      try { if (imgBottomEl) bottomData = await imageToDataURL(imgBottomEl, 1); } catch (e) { console.warn('footer->dataURL failed', e); bottomData = null; }

      // Guardar display previos para restaurar después
      const prevHeaderDisplay = headerEl ? headerEl.style.display : null;
      const prevFooterDisplay = footerEl ? footerEl.style.display : null;

      try {
        // Ocultar header/footer
        if (headerEl) headerEl.style.display = 'none';
        if (footerEl) footerEl.style.display = 'none';

        // Generar PDF (html2pdf)
        const worker = html2pdf().set(opt).from(element).toPdf();

        worker.get('pdf').then((pdf) => {
          try {
            const totalPages = pdf.internal.getNumberOfPages();
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const margin = Array.isArray(opt.margin) ? opt.margin[0] : 10;

            // Generador de padding (para forzar tamaño del PDF si es necesario)
            function generatePaddingDataUrl(sizeKB = 200) {
              const factor = Math.max(600, Math.round(sizeKB * 6));
              const W = Math.min(3000, factor);
              const H = Math.min(2000, Math.round(factor * 0.6));
              const canvas = document.createElement('canvas');
              canvas.width = W;
              canvas.height = H;
              const ctx = canvas.getContext('2d');

              const dots = Math.min(8000, Math.round(W * H / 500));
              for (let i = 0; i < dots; i++) {
                ctx.fillStyle = `rgba(${Math.floor(Math.random() * 255)},${Math.floor(Math.random() * 255)},${Math.floor(Math.random() * 255)},0.01)`;
                ctx.fillRect(Math.random() * W, Math.random() * H, 1, 1);
              }
              return canvas.toDataURL('image/png');
            }

            const paddingDataUrl = generatePaddingDataUrl(700);

            // Si tenemos imágenes de header/footer o padding, las añadimos por página
            if (topData || bottomData || paddingDataUrl) {
              const targetWidth = pageWidth - margin * 2;

              for (let p = 1; p <= totalPages; p++) {
                pdf.setPage(p);

                // CABECERA: sólo en PRIMERA página
                if (topData && p === 1) {
                  const iw = (imgTopEl && imgTopEl.naturalWidth) ? imgTopEl.naturalWidth : 100;
                  const ih = (imgTopEl && imgTopEl.naturalHeight) ? imgTopEl.naturalHeight : 30;
                  const h = (ih / iw) * targetWidth;
                  const x = margin;
                  const y = 2;
                  try { pdf.addImage(topData, 'JPEG', x, y, targetWidth, h, undefined, 'FAST'); } catch (e) { console.warn('addImage top failed', e); }
                }

                // FOOTER: sólo en ÚLTIMA página
                let y2;
                if (bottomData && p === totalPages) {
                  const iw2 = (imgBottomEl && imgBottomEl.naturalWidth) ? imgBottomEl.naturalWidth : 100;
                  const ih2 = (imgBottomEl && imgBottomEl.naturalHeight) ? imgBottomEl.naturalHeight : 20;
                  let h2 = (ih2 / iw2) * targetWidth;
                  const maxFooterHeight = 10;
                  if (h2 > maxFooterHeight) h2 = maxFooterHeight;
                  const x2 = margin;
                  y2 = pageHeight - margin - h2 - 1;
                  try { pdf.addImage(bottomData, 'JPEG', x2, y2, targetWidth, h2, undefined, 'FAST'); } catch (e) { console.warn('addImage bottom failed', e); }
                } else {
                  y2 = pageHeight - margin - 6;
                }

                // Añadir padding EN LA PRIMERA PÁGINA para asegurar tamaño
                if (p === 1 && paddingDataUrl) {
                  try {
                    pdf.addImage(paddingDataUrl, 'PNG', pageWidth - 18, pageHeight - 18, 10, 10, undefined, 'FAST');
                  } catch (e) {
                    try { pdf.addImage(paddingDataUrl, 'PNG', 2, pageHeight - 20, 10, 10, undefined, 'FAST'); } catch (e2) { console.warn('padding add failed', e2); }
                  }
                }

                // Texto del footer - dibujado como texto y no como imagen
                const footerText = footerEl && footerEl.querySelector('.footer-text') ? footerEl.querySelector('.footer-text').innerText.trim() : '';
                if (footerText && p === totalPages) {
                  const lines = footerText.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
                  if (lines.length) {
                    pdf.setFont('helvetica');
                    pdf.setFontSize(10);
                    pdf.setTextColor(0, 0, 0);
                    const textX = pageWidth - margin;
                    const lineHeight = 4;
                    const firstLineY = y2 - 3 - ((lines.length - 1) * lineHeight);
                    try { pdf.text(lines, textX, firstLineY, { align: 'right' }); } catch (e) { console.warn('pdf.text failed', e); }
                  }
                }
              }
            }

            // Ocultar status
            if (status) status.style.display = 'none';
            // Mostrar modal de descarga
            showDownloadModal(pdf, filename);

          } catch (err) {
            console.error('Error postprocesando PDF:', err);
            if (status) status.style.display = 'none';
            showDownloadModal(pdf, filename);
          } finally {
            // Restaurar display de header/footer
            if (headerEl) headerEl.style.display = prevHeaderDisplay;
            if (footerEl) footerEl.style.display = prevFooterDisplay;
          }
        }).catch((err) => {
          console.error('No se obtuvo objeto jsPDF:', err);
          if (status) status.style.display = 'none';
          html2pdf().set(opt).from(element).toPdf().get('pdf').then((pdf) => {
            showDownloadModal(pdf, filename);
          });
        });

      } catch (err) {
        console.error('Error generando PDF:', err);
        if (headerEl) headerEl.style.display = prevHeaderDisplay;
        if (footerEl) footerEl.style.display = prevFooterDisplay;
        if (status) status.style.display = 'none';
        alert('Error generando PDF. Intente nuevamente.');
      }
    }


    function showDownloadModal(pdf, filename) {
      // Crear modal
      const modalOverlay = document.createElement('div');
      modalOverlay.id = 'download-modal-overlay';
      modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: Arial, sans-serif;
      `;

      const modal = document.createElement('div');
      modal.style.cssText = `
        background: white;
        padding: 20px;
        border: 1px solid #ccc;
        text-align: center;
        max-width: 300px;
        width: 90%;
      `;

      modal.innerHTML = `
        <p style="margin: 0 0 20px 0; font-size: 14px;">
          PDF generado. ¿Desea descargarlo?
        </p>
        <div>
          <button id="btn-download-pdf" style="
            background: white;
            color: black;
            border: 1px solid #ccc;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
          ">
            Descargar
          </button>
          <button id="btn-cancel-download" style="
            background: white;
            color: black;
            border: 1px solid #ccc;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
          ">
            Cancelar
          </button>
        </div>
      `;
      modalOverlay.appendChild(modal);
      document.body.appendChild(modalOverlay);

      const btnDownload = modal.querySelector('#btn-download-pdf');
      const btnCancel = modal.querySelector('#btn-cancel-download');

      // EVENTOS DE LOS BOTONES
      btnDownload.addEventListener('click', () => {
        try {
          console.log('Iniciando descarga...');

          // Metodo 1: Descarga directa con save()
          try {
            pdf.save(filename);
            console.log('Descarga iniciada con save()');
          } catch (saveError) {
            console.log('save() falló, usando método alternativo...');

            // Método 2: Crear enlace de descarga
            const blob = pdf.output('blob');
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = filename;
            link.style.position = 'fixed';
            link.style.left = '-9999px';

            document.body.appendChild(link);
            link.click();

            setTimeout(() => {
              document.body.removeChild(link);
              URL.revokeObjectURL(url);
            }, 100);

            console.log('Descarga iniciada con enlace');
          }

          closeModal();
          // Cerrar ventana después de un delay
          setTimeout(() => {
            try {
              window.close();
            } catch (e) {
              console.log('No se pudo cerrar la ventana automáticamente');
            }
          }, 1000);
        } catch (error) {
          console.error('Error en descarga:', error);
          alert('Error al descargar el PDF. Intente nuevamente.');
        }
      });

      btnCancel.addEventListener('click', () => {
        closeModal();
        // Cerrar ventana al cancelar
        setTimeout(() => {
          try {
            window.close();
          } catch (e) {
            console.log('No se pudo cerrar la ventana automáticamente');
          }
        }, 100);
      });

      // Cerrar modal al hacer clic fuera
      modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
          closeModal();
          setTimeout(() => {
            try { window.close(); } catch (e) { }
          }, 100);
        }
      });

      // Función para cerrar modal
      function closeModal() {
        if (modalOverlay && modalOverlay.parentNode) {
          modalOverlay.parentNode.removeChild(modalOverlay);
        }
      }

      // Cerrar con ESC
      document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
          closeModal();
          document.removeEventListener('keydown', escHandler);
          setTimeout(() => {
            try { window.close(); } catch (e) { }
          }, 100);
        }
      });
      setTimeout(() => {
        btnDownload.focus();
      }, 100);
    }
    function generatePDF() {
      generarPDFCotizacion();
    }
    window.addEventListener('DOMContentLoaded', async () => {
      await fetchAndFill();
    });
  </script>

</body>

</html>