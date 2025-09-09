<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cotización <?= $id ?> Dependiente - YONDA PERÚ</title>
  
  <style>
    @page {
      size: A4 portrait;
      margin: 10mm;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 11pt;
      line-height: 1.3;
      color: #333;
      background: white;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 7.5in;
      margin: 0 auto;
      padding: 15mm;
      background: white;
      position: relative;
    }

    /* Header simplificado - sin posición fija */
    .header {
      text-align: center;
      margin-bottom: 15px;
      opacity: 0.3;
    }

    .header img {
      width: 50%;
      max-height: 35px;
      object-fit: contain;
    }

    .fecha {
      text-align: right;
      font-size: 11pt;
      margin: 10px 0;
      white-space: nowrap;
    }

    .title {
      text-align: center;
      margin: 15px 0;
      font-size: 16pt;
      font-weight: bold;
    }

    #cot-id-suffix {
      font-weight: normal;
      font-size: 0.85em;
      margin-left: 8px;
    }

    .content {
      width: 100%;
      margin: 0;
      padding: 0;
    }

    .content p {
      margin: 6px 0;
      line-height: 1.3;
      font-size: 11pt;
    }

    .tight {
      margin: 2px 0;
    }

    .justified-text {
      text-align: justify;
    }

    /* Tabla de detalles optimizada */
    .detalle {
      width: 75%;
      border-collapse: collapse;
      font-weight: bold;
      font-size: 10pt;
      margin: 10px 0;
    }

    .detalle td.label {
      width: 180px;
      text-align: left;
      padding: 2px 0;
    }

    .detalle td.value {
      text-align: left;
      padding: 2px 0;
    }

    /* Tabla de precios optimizada */
    .pricing {
      width: 100%;
      border-collapse: collapse;
      font-weight: bold;
      font-size: 11pt;
      margin: 15px 0;
    }

    .pricing,
    .pricing td {
      border: 1px solid #000;
    }

    .pricing td {
      padding: 6px 8px;
      text-align: center;
    }

    /* Instrucciones */
    .instructions {
      font-size: 11pt;
      margin: 10px 0;
    }

    .instructions p {
      margin: 4px 0;
    }

    .instructions ol li {
      margin-bottom: 3px;
      font-weight: bold;
      line-height: 1.3;
    }

    .selected {
      background-color: #fff3cd;
      padding: 4px 8px;
      text-align: center;
      border-radius: 4px;
      font-weight: bold;
      margin: 10px 0;
      display: block;
    }

    .closing {
      text-align: justify;
      margin-top: 10px;
    }

    /* Footer simplificado - sin posición fija */
    .footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 30px;
      opacity: 0.3;
      page-break-inside: avoid;
    }

    .footer-text {
      font-size: 10pt;
      line-height: 1.2;
      text-align: left;
    }

    .footer img {
      width: auto;
      max-width: 200px;
      height: 20mm;
      object-fit: contain;
    }

    /* Indicador de carga */
    #loading-indicator {
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
    }

    @media print {
      #loading-indicator {
        display: none !important;
      }
      
      .container {
        max-width: none;
        padding: 10mm;
      }
    }
  </style>
</head>

<body>
  <button class="btn-generate-pdf" onclick="generatePDF()" style="display: none;">Generar PDF</button>

  <div id="loading-indicator">
    <div style="color:#d32f2f; font-weight:bold; margin-bottom:10px;">
      Generando PDF...
    </div>
    <div style="font-size:12px;">Por favor espere...</div>
  </div>

  <div class="container">
    <div class="header">
      <img src="/assets/images/logos/cabecera-yondaa.png" alt="Cabecera Yonda">
    </div>

    <div class="fecha" id="fecha"></div>

    <h2 class="title">
      <strong id="main-title">COTIZACIÓN VEHICULAR</strong> 
      <span id="cot-id-suffix"></span>
    </h2>

    <div class="content">
      <p class="tight">Presente, atte. Yonda & Grupo Huaraca E.I.R.L.</p>
      <p class="tight">RUC: 20609396866</p>
      <p class="justified-text">
        De nuestra consideración, nos es grato dirigirnos a usted para brindarle una
        cotización vehicular de acuerdo al siguiente detalle:
      </p>

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

      <div class="instructions">
        <p>
          Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro
          ejecutivo de ventas la siguiente documentación:
        </p>
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
        <span id="asesor-nombre"></span><br>
        <span id="asesor-cargo"></span><br>
        TELÉFONO: <span id="asesor-telefono"></span>
      </div>
      <img src="/assets/images/logos/footer-yondaa.png" alt="Piecera Yonda">
    </div>
  </div>

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

    function formatInstruction(text) {
      if (!text && text !== 0) return '';
      let s = String(text).trim().toLowerCase();

      const corrections = {
        'declaracion': 'declaración',
        'jurada': 'jurada',
        'ingresos': 'ingresos',
        'fotocopia': 'fotocopia',
        'dni': 'DNI',
        'titular': 'titular',
        'conyuge': 'cónyuge',
        'cónyuge': 'cónyuge',
        'copia': 'copia',
        'ultimo': 'último',
        'recibo': 'recibo',
        'pagado': 'pagado',
        'servicios': 'servicios',
        'luz': 'luz',
        'agua': 'agua',
        'simple': 'simple',
        'vivienda': 'vivienda',
        'titulo': 'título',
        'título': 'título',
        'propiedad': 'propiedad',
        'certificado': 'certificado',
        'posesion': 'posesión',
        'posesión': 'posesión',
        'literal': 'literal',
        'boletas': 'boletas',
        'aval': 'aval',
        'evaluacion': 'evaluación',
        'evaluación': 'evaluación',
        'gastos': 'gastos',
        'familiares': 'familiares',
        'inicial': 'inicial',
        'minimo': 'mínimo',
        'mínimo': 'mínimo',
        'verificacion': 'verificación',
        'verificación': 'verificación',
        'domiciliaria': 'domiciliaria',
        'laboral': 'laboral',
        'pago': 'pago',
        'unico': 'único',
        'único': 'único',
        'administrativos': 'administrativos',
        'seguro': 'seguro',
        'vehicular': 'vehicular',
        'gps': 'GPS',
        'satelital': 'satelital',
        's/': 'S/',
        's/1,500.00': 'S/ 1,500.00'
      };

      const keys = Object.keys(corrections).map(k => k.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&'));
      if (keys.length) {
        const pattern = new RegExp('\\b(' + keys.join('|') + ')\\b', 'g');
        s = s.replace(pattern, function (m) {
          return corrections[m] || m;
        });
      }

      const firstWordMatch = s.match(/^\s*([^\s]+)/);
      if (firstWordMatch) {
        const first = firstWordMatch[1];
        const rest = s.slice(firstWordMatch[0].length - first.length);
        if (first === first.toUpperCase() && first.length <= 4) {
          return first + rest;
        } else {
          const capitalizedFirst = first.charAt(0).toUpperCase() + first.slice(1);
          return capitalizedFirst + rest;
        }
      }

      return s.charAt(0).toUpperCase() + s.slice(1);
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

        // Ajustar TÍTULO para incluir id formateado
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

        // Llenar datos del asesor
        if (cotizacion.asesor) {
          const asesorNombre = document.getElementById('asesor-nombre');
          const asesorCargo = document.getElementById('asesor-cargo');
          const asesorTelefono = document.getElementById('asesor-telefono');

          if (asesorNombre) {
            asesorNombre.textContent = cotizacion.asesor.nombre_completo || cotizacion.asesor.nombre || 'CHARLY YACTAYO ORTIZ';
          }

          if (asesorCargo) {
            let cargoMostrar = cotizacion.asesor.cargo || 'Ejecutivo de Ventas';
            if (cargoMostrar.toLowerCase().includes('vent') || cargoMostrar.toLowerCase().includes('asesor')) {
              cargoMostrar = 'Ejecutivo de Ventas';
            }
            asesorCargo.textContent = cargoMostrar;
          }

          if (asesorTelefono) {
            let telefono = cotizacion.asesor.telefono || '934 008 037';
            if (telefono && !telefono.includes('056') && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            } else if (telefono && telefono.length === 9) {
              telefono = `(056) ${telefono}`;
            }
            asesorTelefono.textContent = telefono;
          }
        }

        // Llenar requisitos
        const instructionsOl = document.getElementById('instructions-list');
        if (instructionsOl) instructionsOl.innerHTML = '';
        const requisitos = cotizacion.requisitos ?? [];

        if (requisitos.length > 0) {
          requisitos.forEach(r => {
            const li = document.createElement('li');
            li.textContent = formatInstruction(r.requisito || r.texto || r);
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
            li.textContent = formatInstruction(text);
            instructionsOl.appendChild(li);
          });
        }

        // Llenar tabla de precios con opciones de financiamiento
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
        if (pricingTable) {
          pricingTable.querySelectorAll('.extra-opt').forEach(node => node.remove());
        }

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

        if (extras.length && pricingTable) {
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

        if (loadingIndicator) loadingIndicator.style.display = 'none';
        if (container) container.style.visibility = 'visible';

        // Generar PDF después de llenar datos
        setTimeout(() => generarPDFOptimizado(), 500);

      } catch (error) {
        console.error('Error al cargar datos:', error);
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        alert('Error al cargar los datos de la cotización. La ventana se cerrará.');
        setTimeout(() => window.close(), 50);
      }
    }

    // Función de generación de PDF optimizada (similar al de OC)
    async function generarPDFOptimizado() {
      const element = document.querySelector('.container');
      const loadingIndicator = document.getElementById('loading-indicator');
      
      if (loadingIndicator) loadingIndicator.style.display = 'none';

      const filename = `cotizacion-${getIdFromPath() || 'yonda'}.pdf`;

      // Configuración optimizada similar al de OC
      const opt = {
        margin: [0.1, 0.1, 0.1, 0.1],
        filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
          scale: 1.75, // Reducido desde 2.0 
          useCORS: true,
          allowTaint: false,
          logging: false,
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight
        },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      };

      try {
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
      } catch (error) {
        console.error('Error al generar PDF:', error);
        alert('Error al generar el PDF. Intente nuevamente.');
      }
    }

    function generatePDF() {
      generarPDFOptimizado();
    }

    window.addEventListener('DOMContentLoaded', async () => {
      await fetchAndFill();
    });
  </script>
</body>
</html>