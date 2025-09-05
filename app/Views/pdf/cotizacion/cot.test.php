<!-- app/views/pdf/cotizacion/cotizacion-html2pdf.php -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotización <?= $id ?> Dependiente - YONDA PERÚ</title>
  <link rel="stylesheet" href="/assets/css/cot-reporte.css" />
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
      <img src="/assets/images/logos/cabecera-yonda.png" alt="Cabecera Yonda">
    </div>

    <div class="row">
      <div class="left"></div>
      <div class="right fecha" id="fecha"></div>
    </div>

    <h2 class="title"><strong>COTIZACIÓN VEHICULAR</strong></h2>

    <div class="content">
      <p class="tight small-text">Presente, atte. Yonda & Grupo Huaraca E.I.R.L.</p>
      <p class="tight small-text">RUC: 20609396866</p>
      <p class="small-text">
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
          <!-- <li>FOTOCOPIA DNI DEL TITULAR Y CÓNYUGE</li>
          <li>COPIA DEL ÚLTIMO RECIBO PAGADO DE SERVICIOS (LUZ O AGUA)</li>
          <li>COPIA SIMPLE DE VIVIENDA (TÍTULO DE PROPIEDAD / CERTIFICADO DE POSESIÓN, COPIA LITERAL)</li>
          <li>BOLETAS DE PAGO</li>
          <li>DNI AVAL (DNI CÓNYUGE DE SER NECESARIO)</li>
          <li>EVALUACIÓN DE GASTOS FAMILIARES</li>
          <li>30% DE INICIAL COMO MÍNIMO (aumenta según precio de la unidad)</li>
          <li>VERIFICACIÓN DOMICILIARIA Y LABORAL</li>
          <li>PAGO ÚNICO POR GASTOS ADMINISTRATIVOS S/ 1,500.00</li>
          <li>SEGURO VEHICULAR (bajo evaluación)</li>
          <li>GPS SATELITAL</li> -->
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
      <div class="footer-text">
        CHARLY YACTAYO ORTIZ<br>
        Ejecutivo de Ventas<br>
        TELÉFONO: (056) 934 008 037
      </div>
      <img src="/assets/images/logos/footer-yonda.png" alt="Piecera Yonda">
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

        const fechaStr = formatDateSpanish(cotizacion.fecha, 'Chincha');
        document.getElementById('fecha').textContent = fechaStr.replace(/ /g, '\u00A0');

        document.getElementById('cliente-nombre').textContent = (cotizacion.cliente && cotizacion.cliente.nombre) ? cotizacion.cliente.nombre : '';
        document.getElementById('cliente-dni').textContent = (cotizacion.cliente && cotizacion.cliente.dni) ? cotizacion.cliente.dni : '';
        document.getElementById('cliente-celular').textContent = (cotizacion.cliente && cotizacion.cliente.celular) ? cotizacion.cliente.celular : '';
        document.getElementById('vehiculo-marca').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.marca) ? cotizacion.vehiculo.marca : '';
        document.getElementById('vehiculo-modelo').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.modelo) ? cotizacion.vehiculo.modelo : '';
        document.getElementById('vehiculo-anio').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.anio) ? cotizacion.vehiculo.anio : '';
        document.getElementById('vehiculo-color').textContent = (cotizacion.vehiculo && cotizacion.vehiculo.color) ? cotizacion.vehiculo.color : '';

        document.getElementById('precio-usd').textContent = cotizacion.precios ? `$ ${cotizacion.precios.precio_usd}` : '';
        document.getElementById('inicial-soles').textContent = cotizacion.precios ? `S/ ${cotizacion.precios.inicial_soles}` : '';
        document.getElementById('cuota-24').textContent = (cotizacion.precios && cotizacion.precios.meses_24) ? cotizacion.precios.meses_24 : '-';
        document.getElementById('cuota-36').textContent = (cotizacion.precios && cotizacion.precios.meses_36) ? cotizacion.precios.meses_36 : '-';
        document.getElementById('cuota-48').textContent = (cotizacion.precios && cotizacion.precios.meses_48) ? cotizacion.precios.meses_48 : '-';
        document.getElementById('cuota-60').textContent = (cotizacion.precios && cotizacion.precios.meses_60) ? cotizacion.precios.meses_60 : '-';

        const instructionsOl = document.getElementById('instructions-list');
        if (instructionsOl) instructionsOl.innerHTML = '';
        const requisitos = cotizacion.requisitos ?? [];
        if (requisitos.length > 0) {
          requisitos.forEach(r => {
            const li = document.createElement('li');
            li.textContent = r.requisito;
            instructionsOl.appendChild(li);
          });
        } else {
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

        if (loadingIndicator) loadingIndicator.style.display = 'none';
        if (container) container.style.visibility = 'visible';

        // tiny delay to ensure DOM painted well
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

      let topData = null;
      let bottomData = null;
      try { if (imgTopEl) topData = await imageToDataURL(imgTopEl, 0.12); } catch (e) { console.warn('cabecera->dataURL failed', e); topData = null; }
      try { if (imgBottomEl) bottomData = await imageToDataURL(imgBottomEl, 0.9); } catch (e) { console.warn('footer->dataURL failed', e); bottomData = null; }

      const prevHeaderDisplay = headerEl ? headerEl.style.display : null;
      const prevFooterDisplay = footerEl ? footerEl.style.display : null;

      try {
        // hide header/footer in DOM so html2canvas does not capture them (we'll draw them into the pdf)
        if (headerEl) headerEl.style.display = 'none';
        if (footerEl) footerEl.style.display = 'none';

        const worker = html2pdf().set(opt).from(element).toPdf();

        worker.get('pdf').then((pdf) => {
          try {
            const totalPages = pdf.internal.getNumberOfPages();
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const margin = Array.isArray(opt.margin) ? opt.margin[0] : 10;
            const footerText = footerEl && footerEl.querySelector('.footer-text') ? footerEl.querySelector('.footer-text').innerText.trim() : '';
            const lineHeight = 4; // mm

            for (let p = 1; p <= totalPages; p++) {
              pdf.setPage(p);

              // header image (top)
              if (topData) {
                const targetWidth = pageWidth - margin * 2;
                const iw = (imgTopEl && imgTopEl.naturalWidth) ? imgTopEl.naturalWidth : 100;
                const ih = (imgTopEl && imgTopEl.naturalHeight) ? imgTopEl.naturalHeight : 30;
                const h = (ih / iw) * targetWidth;
                const x = margin;
                const y = 2; // mm from top
                pdf.addImage(topData, 'PNG', x, y, targetWidth, h, undefined, 'FAST');
              }

              // footer image and text
              let y2;
              const targetWidth = pageWidth - margin * 2;
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

              // draw footer text aligned right
              if (footerText) {
                const lines = footerText.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
                if (lines.length) {
                  pdf.setFont('helvetica');
                  pdf.setFontSize(10);
                  pdf.setTextColor(0, 0, 0);
                  const textX = pageWidth - margin;
                  const firstLineY = y2 - 3 - ((lines.length - 1) * lineHeight);
                  pdf.text(lines, textX, firstLineY, { align: 'right' });
                }
              }
            }

            // save
            pdf.save(filename);
          } catch (err) {
            console.error('Error postprocesando PDF:', err);
            // fallback: save what html2pdf produced (no postprocessing)
            html2pdf().set(opt).from(element).save();
          } finally {
            if (headerEl) headerEl.style.display = prevHeaderDisplay;
            if (footerEl) footerEl.style.display = prevFooterDisplay;
            if (status) status.style.display = 'none';
          }
        }).catch((err) => {
          console.error('Error obteniendo jsPDF:', err);
          // fallback direct
          html2pdf().set(opt).from(element).save().finally(() => { if (status) status.style.display = 'none'; });
        });

      } catch (err) {
        console.error('Error generando PDF:', err);
        if (headerEl) headerEl.style.display = prevHeaderDisplay;
        if (footerEl) footerEl.style.display = prevFooterDisplay;
        if (status) status.style.display = 'none';
        html2pdf().set(opt).from(element).save();
      }
    }

    function generatePDF() { generarPDFCotizacion(); }

    window.addEventListener('DOMContentLoaded', async () => { await fetchAndFill(); });
  </script>



</body>

</html>