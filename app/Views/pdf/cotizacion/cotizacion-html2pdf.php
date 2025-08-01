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
      <div class="left fecha" id="fecha">
        <!-- Fecha será cargada dinámicamente -->
      </div>
      <div class="right"></div>
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
        <ol>
          <li>FOTOCOPIA DNI DEL TITULAR Y CÓNYUGE</li>
          <li>COPIA DEL ÚLTIMO RECIBO PAGADO DE SERVICIOS (LUZ O AGUA)</li>
          <li>COPIA SIMPLE DE VIVIENDA (TÍTULO DE PROPIEDAD / CERTIFICADO DE POSESIÓN, COPIA LITERAL)</li>
          <li>BOLETAS DE PAGO</li>
          <li>DNI AVAL (DNI CÓNYUGE DE SER NECESARIO)</li>
          <li>EVALUACIÓN DE GASTOS FAMILIARES</li>
          <li>30% DE INICIAL COMO MÍNIMO (aumenta según precio de la unidad)</li>
          <li>VERIFICACIÓN DOMICILIARIA Y LABORAL</li>
          <li>PAGO ÚNICO POR GASTOS ADMINISTRATIVOS S/ 1,500.00</li>
          <li>SEGURO VEHICULAR (bajo evaluación)</li>
          <li>GPS SATELITAL</li>
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

    async function fetchAndFill() {
      try {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
          loadingIndicator.style.display = 'block';
        }

        // Ocultar el contenido mientras se carga
        const container = document.querySelector('.container');
        if (container) {
          container.style.visibility = 'hidden';
        }

        const res = await fetch(`/api/cotizacion/${cotId}`);

        if (!res.ok) {
          throw new Error(`Error ${res.status}: ${res.statusText}`);
        }

        const { cotizacion } = await res.json();

        // Rellena campos con los datos obtenidos
        document.getElementById('fecha').textContent = cotizacion.fecha || '';
        document.getElementById('cliente-nombre').textContent = cotizacion.cliente.nombre;
        document.getElementById('cliente-dni').textContent = cotizacion.cliente.dni;
        document.getElementById('cliente-celular').textContent = cotizacion.cliente.celular;
        document.getElementById('vehiculo-marca').textContent = cotizacion.vehiculo.marca;
        document.getElementById('vehiculo-modelo').textContent = cotizacion.vehiculo.modelo;
        document.getElementById('vehiculo-anio').textContent = cotizacion.vehiculo.anio;
        document.getElementById('vehiculo-color').textContent = cotizacion.vehiculo.color;
        document.getElementById('precio-usd').textContent = `$ ${cotizacion.precios.precio_usd}`;
        document.getElementById('inicial-soles').textContent = `S/ ${cotizacion.precios.inicial_soles}`;
        document.getElementById('cuota-24').textContent = cotizacion.precios.meses_24 || '-';
        document.getElementById('cuota-36').textContent = cotizacion.precios.meses_36 || '-';
        document.getElementById('cuota-48').textContent = cotizacion.precios.meses_48 || '-';
        document.getElementById('cuota-60').textContent = cotizacion.precios.meses_60 || '-';

        // Ocultar indicador de carga
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        if (container) {
          container.style.visibility = 'visible';
        }
        setTimeout(() => {
          generarPDFCotizacion();
        },
          10);

      } catch (error) {
        console.error('Error al cargar datos:', error);

        // Ocultar indicador de carga
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        // Mostrar error y cerrar
        alert('Error al cargar los datos de la cotización. La ventana se cerrará.');
        setTimeout(() => window.close(), 50);
      }
    }

    function generarPDFCotizacion() {
      const element = document.querySelector('.container');
      const loadingIndicator = document.getElementById('loading-indicator');

      // Asegurar que el indicador esté oculto
      if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
      }

      // Opciones optimizadas para PDF
      const opt = {
        margin: [0, 0, 0, 0],
        filename: `cotizacion-${getIdFromPath() || 'yonda'}.pdf`,
        image: {
          type: 'jpeg',
          quality: 0.98
        },
        html2canvas: {
          scale: 1.75,
          useCORS: true,
          allowTaint: true,
          logging: false, // Desactivar logs para mejor rendimiento
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight,
          scrollX: 0,
          scrollY: 0
        },
        jsPDF: {
          unit: 'in',
          format: 'letter',
          orientation: 'portrait'
        }
      };

      // Mostrar mensaje mientras se genera el PDF
      const statusMessage = document.createElement('div');
      statusMessage.id = 'pdf-status';
      statusMessage.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px;
        border-radius: 10px;
        z-index: 9999;
        text-align: center;
        font-family: Arial, sans-serif;
      `;
      statusMessage.innerHTML = `
        <div style="font-size: 16px; margin-bottom: 10px;">📄 Generando PDF...</div>
        <div style="font-size: 12px;">Por favor espere, se descargará automáticamente</div>
      `;
      document.body.appendChild(statusMessage);

      // Generar y descargar PDF
      html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
          console.log('PDF de cotización generado y descargado exitosamente.');

          // Remover mensaje de estado
          const status = document.getElementById('pdf-status');
          if (status) {
            status.remove();
          }

          // Cerrar ventana después de un breve delay
          setTimeout(() => {
            window.close();
          }, 1000);
        })
        .catch(error => {
          console.error('Error al generar PDF de cotización:', error);

          // Remover mensaje de estado
          const status = document.getElementById('pdf-status');
          if (status) {
            status.remove();
          }

          alert('Error al generar el PDF. La ventana se cerrará.');
          setTimeout(() => {
            window.close();
          }, 1000);
        });
    }

    // Función para generar PDF manualmente (botón oculto)
    function generatePDF() {
      generarPDFCotizacion();
    }

    // Inicializar cuando la página esté completamente cargada
    window.addEventListener('DOMContentLoaded', async () => {
      // Ejecutar la carga de datos y generación de PDF
      await fetchAndFill();
    });
  </script>

</body>

</html>