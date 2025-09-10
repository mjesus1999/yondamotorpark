<!-- app/views/pdf/cotizacion/cotizacion-html2pdf.php (corregido — sin formatInstruction) -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotización <?= $id ?> Dependiente - YONDA PERÚ</title>
  <link rel="stylesheet" href="/assets/css/cotizacion-reportP.css" />

  <!-- Overrides específicos solicitados -->
  <style>
    /* Asegurar que los textos small-text y closing estén justificados y ocupen ancho hasta los márgenes del footer */
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

    /* Reducir tamaño de la tabla DETALLE cliente (título/tamaño solicitado) */
    .detalle {
      font-size: 10pt;
      /* bajado desde 12pt */
    }

    /* Sufijo al título (estética) */
    #cot-id-suffix {
      font-weight: normal;
      font-size: 0.85em;
      margin-left: 8px;
      vertical-align: middle;
    }

    .title {
      font-size: 15pt;
    }

    /* Aseguramos que en impresión se mantengan los overrides */
    @media print {

      .small-text,
      .closing {
        text-align: justify !important;
      }

      .detalle {
        font-size: 10pt !important;
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
          <td class="label">NOMBRE DEL CLIENTE</td>
          <td class="value">: <span id="cliente-nombre"></span></td>
        </tr>
        <tr>
          <td class="label">DNI</td>
          <td class="value">: <span id="cliente-dni"></span></td>
        </tr>
        <tr>
          <td class="label">CELULAR</td>
          <td class="value">: <span id="cliente-celular"></span></td>
        </tr>
        <tr>
          <td class="label">MARCA DEL VEHÍCULO</td>
          <td class="value">: <span id="vehiculo-marca"></span></td>
        </tr>
        <tr>
          <td class="label">MODELO</td>
          <td class="value">: <span id="vehiculo-modelo"></span></td>
        </tr>
        <tr>
          <td class="label">AÑO</td>
          <td class="value">: <span id="vehiculo-anio"></span></td>
        </tr>
        <tr>
          <td class="label">COLOR</td>
          <td class="value">: <span id="vehiculo-color"></span></td>
        </tr>
      </table>

      <!-- CUADRO DE PRECIOS -->
      <table class="pricing">
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
      </table>

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
      return `${ciudad}, ${day} de ${month} de ${year}`;
    }

    // Modificación en la función fetchAndFill del JavaScript (sin formatInstruction)
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

        // Ajustar TÍTULO para incluir id formateado (ej: 000001)
        try {
          const idNum = cotizacion.id || cotId || getIdFromPath() || '';
          const padded = String(idNum).padStart(6, '0');
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

        // Llenar precios
        document.getElementById('precio-usd').textContent = cotizacion.precios ? `$ ${cotizacion.precios.precio_usd}` : '';
        document.getElementById('inicial-soles').textContent = cotizacion.precios ? `S/ ${cotizacion.precios.inicial_soles}` : '';
        document.getElementById('cuota-24').textContent = (cotizacion.precios && cotizacion.precios.meses_24) ? cotizacion.precios.meses_24 : '-';
        document.getElementById('cuota-36').textContent = (cotizacion.precios && cotizacion.precios.meses_36) ? cotizacion.precios.meses_36 : '-';
        document.getElementById('cuota-48').textContent = (cotizacion.precios && cotizacion.precios.meses_48) ? cotizacion.precios.meses_48 : '-';
        document.getElementById('cuota-60').textContent = (cotizacion.precios && cotizacion.precios.meses_60) ? cotizacion.precios.meses_60 : '-';

        // NUEVA SECCIÓN: Llenar datos del asesor
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
            // Formatear teléfono con código de área si es necesario
            let telefono = cotizacion.asesor.telefono || '934 008 037';

            // Si el teléfono no tiene el código de área, agregarlo
            if (telefono && !telefono.includes('056') && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            } else if (telefono && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            }

            asesorTelefono.textContent = telefono;
          }
        }

        // Llenar requisitos SIN formato adicional (se muestra tal cual vienen)
        const instructionsOl = document.getElementById('instructions-list');
        if (instructionsOl) instructionsOl.innerHTML = '';
        const requisitos = cotizacion.requisitos ?? [];

        if (requisitos.length > 0) {
          requisitos.forEach(r => {
            const li = document.createElement('li');
            // Mostrar el texto tal cual viene desde la API
            li.textContent = (r.requisito || r.texto || r);
            instructionsOl.appendChild(li);
          });
        } else {
          // Requisitos por defecto si no hay específicos (se mantienen tal cual)
          const defaults = [
            'FOTOCOPIA DNI DEL TITULAR Y CÓNYUGE',
            'COPIA DEL ÚLTIMO RECIBO PAGADO DE SERVICIOS (LUZ O AGUA)',
            'COPIA SIMPLE DE VIVIENDA (TÍTULO DE PROPIEDAD / CERTIFICADO DE POSESIÓN, COPIA LITERAL)',
            'BOLETAS DE PAGO',
            'DNI AVAL (DNI CÓNYUGE DE SER NECESARIO)',
            'EVALUACIÓN DE GASTOS FAMILIARES',
            '30% DE INICIAL COMO MÍNIMO (aumenta según precio de la unidad)',
            'VERIFICACIÓN DOMICILIARIA Y LABORAL',
            'PAGO ÚNICO POR GASTOS ADMINISTRATIVOS S/ 1,500.00',
            'SEGURO VEHICULAR (bajo evaluación)',
            'GPS SATELITAL'
          ];
          defaults.forEach(text => {
            const li = document.createElement('li');
            li.textContent = text;
            instructionsOl.appendChild(li);
          });
        }

        // Llenar tabla de precios con opciones de financiamiento (código existente)
        (function populatePricingTable() {
          const inicialCell = document.getElementById('inicial-soles');
          const precioCell = document.getElementById('precio-usd');
          const idMap = {
            24: 'cuota-24',
            36: 'cuota-36',
            48: 'cuota-48',
            60: 'cuota-60'
          };

          const precios = cotizacion.precios || {};
          const inicialSoles = precios.inicial_soles ? String(precios.inicial_soles) : '0.00';
          const precioUsd = precios.precio_usd ? String(precios.precio_usd) : '0.00';

          if (precioCell) precioCell.textContent = `$ ${parseFloat(precioUsd).toFixed(2)}`;
          if (inicialCell) inicialCell.textContent = `S/ ${parseFloat(inicialSoles).toFixed(2)}`;

          Object.values(idMap).forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '-';
          });

          const pricingTable = document.querySelector('.pricing');
          if (!pricingTable) return;
          pricingTable.querySelectorAll('.extra-opt').forEach(node => node.remove());

          const opciones = cotizacion.opciones_financiamiento || [];
          const extras = [];

          opciones.forEach(opt => {
            const meses = Number(opt.numcuotas || opt.numCuotas || 0);
            const cuota = (opt.valorcuota !== undefined && opt.valorcuota !== null)
              ? Number(opt.valorcuota)
              : null;

            if (idMap[meses]) {
              const target = document.getElementById(idMap[meses]);
              if (target) {
                target.textContent = cuota !== null ? `S/ ${cuota.toFixed(2)}` : '-';
              }
            } else {
              extras.push({
                meses,
                cuota: cuota !== null ? `S/ ${cuota.toFixed(2)}` : '-'
              });
            }
          });

          if (extras.length) {
            extras.forEach(ex => {
              const tr = document.createElement('tr');
              tr.classList.add('extra-opt');
              tr.innerHTML = `
            <td>${ex.meses} meses</td>
            <td>${ex.cuota}</td>
            <td>S/ ${parseFloat(inicialSoles).toFixed(2)}</td>
            <td>$ ${parseFloat(precioUsd).toFixed(2)}</td>
          `;
              pricingTable.appendChild(tr);
            });
          }
        })();

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

    // convert <img> element to dataURL (uses canvas; requires same-origin or CORS headers)
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
            resolve(canvas.toDataURL('image/png'));
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

      const opt = {
        margin: [10, 10, 10, 10],
        filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
          scale: 2.0,
          useCORS: true,
          allowTaint: false,
          logging: false,
          imageTimeout: 15000,
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight,
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

      const headerEl = document.querySelector('.header');
      const footerEl = document.querySelector('.footer');
      const imgTopEl = document.querySelector('.pdf-watermark.top') || (headerEl ? headerEl.querySelector('img') : null);
      const imgBottomEl = document.querySelector('.pdf-watermark.bottom') || (footerEl ? footerEl.querySelector('img') : null);

      // Convierte imágenes a dataURL
      let topData = null;
      let bottomData = null;
      try { if (imgTopEl) topData = await imageToDataURL(imgTopEl, 1); } catch (e) { console.warn('cabecera->dataURL failed', e); topData = null; }
      try { if (imgBottomEl) bottomData = await imageToDataURL(imgBottomEl, 1); } catch (e) { console.warn('footer->dataURL failed', e); bottomData = null; }

      const prevHeaderDisplay = headerEl ? headerEl.style.display : null;
      const prevFooterDisplay = footerEl ? footerEl.style.display : null;

      try {
        // Ocultar header/footer para html2canvas
        if (headerEl) headerEl.style.display = 'none';
        if (footerEl) footerEl.style.display = 'none';

        // Generar PDF
        const worker = html2pdf().set(opt).from(element).toPdf();

        worker.get('pdf').then((pdf) => {
          try {
            const totalPages = pdf.internal.getNumberOfPages();
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const margin = Array.isArray(opt.margin) ? opt.margin[0] : 10;

            if (topData || bottomData) {
              const targetWidth = pageWidth - margin * 2;

              for (let p = 1; p <= totalPages; p++) {
                pdf.setPage(p);

                // CABECERA
                if (topData) {
                  const iw = (imgTopEl && imgTopEl.naturalWidth) ? imgTopEl.naturalWidth : 100;
                  const ih = (imgTopEl && imgTopEl.naturalHeight) ? imgTopEl.naturalHeight : 30;
                  const h = (ih / iw) * targetWidth;
                  const x = margin;
                  const y = 2;
                  pdf.addImage(topData, 'PNG', x, y, targetWidth, h, undefined, 'FAST');
                }

                // FOOTER
                let y2;
                if (bottomData) {
                  const iw2 = (imgBottomEl && imgBottomEl.naturalWidth) ? imgBottomEl.naturalWidth : 100;
                  const ih2 = (imgBottomEl && imgBottomEl.naturalHeight) ? imgBottomEl.naturalHeight : 20;
                  let h2 = (ih2 / iw2) * targetWidth;
                  const maxFooterHeight = 10;
                  if (h2 > maxFooterHeight) h2 = maxFooterHeight;
                  const x2 = margin;
                  y2 = pageHeight - margin - h2 - 1;
                  pdf.addImage(bottomData, 'PNG', x2, y2, targetWidth, h2, undefined, 'FAST');
                } else {
                  y2 = pageHeight - margin - 6;
                }

                // Texto del footer
                const footerText = footerEl && footerEl.querySelector('.footer-text') ? footerEl.querySelector('.footer-text').innerText.trim() : '';
                if (footerText) {
                  const lines = footerText.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
                  if (lines.length) {
                    pdf.setFont('helvetica');
                    pdf.setFontSize(10);
                    pdf.setTextColor(0, 0, 0);
                    const textX = pageWidth - margin;
                    const lineHeight = 4;
                    const firstLineY = y2 - 3 - ((lines.length - 1) * lineHeight);
                    pdf.text(lines, textX, firstLineY, { align: 'right' });
                  }
                }
              }
            }

            // Ocultar status de generación
            if (status) status.style.display = 'none';

            // MOSTRAR MODAL DE DESCARGA
            showDownloadModal(pdf, filename);

          } catch (err) {
            console.error('Error postprocesando PDF:', err);
            if (status) status.style.display = 'none';
            // Fallback directo
            showDownloadModal(pdf, filename);
          } finally {
            // Restaurar elementos DOM
            if (headerEl) headerEl.style.display = prevHeaderDisplay;
            if (footerEl) footerEl.style.display = prevFooterDisplay;
          }
        }).catch((err) => {
          console.error('No se obtuvo objeto jsPDF:', err);
          if (status) status.style.display = 'none';
          // Fallback básico
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

          // Método 1: Descarga directa con save()
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

          // Cerrar modal
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

      // Auto-focus en el botón de descarga
      setTimeout(() => {
        btnDownload.focus();
      }, 100);
    }

    // REMOVER TODA LÓGICA DE PREVIEW
    function isPreviewMode() {
      return false; // Siempre devolver false para deshabilitar preview
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