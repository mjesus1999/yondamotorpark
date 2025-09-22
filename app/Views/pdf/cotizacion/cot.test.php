<!-- ESTE PDF ES SIN VISTA PREVIA DE (Cotizacion-html2pdf -->)
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cotización <?= $id ?> - YONDA PERÚ</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: white;
    }

    #loading-indicator {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border: 2px solid #d32f2f;
      border-radius: 15px;
      z-index: 2000;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    #loading-indicator::before {
      content: "";
      display: block;
      width: 40px;
      height: 40px;
      margin: 0 auto 15px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #d32f2f;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    #download-modal-overlay {
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
    }

    .modal {
      background: white;
      padding: 25px;
      border-radius: 8px;
      text-align: center;
      max-width: 350px;
      width: 90%;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .modal button {
      background: white;
      color: black;
      border: 1px solid #ccc;
      padding: 10px 20px;
      cursor: pointer;
      font-size: 14px;
      margin: 0 5px;
      border-radius: 4px;
    }

    .modal button:hover {
      background: #f5f5f5;
    }
  </style>
</head>

<body>
  <!-- Indicador de carga -->
  <div id="loading-indicator">
    <div style="color: #d32f2f; font-weight: bold; margin-bottom: 15px; font-size: 18px;">
      Generando PDF...
    </div>
    <div style="font-size: 14px; color: #666;">Por favor espere...</div>
  </div>

  <!-- PDFMake -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <script>
    const cotId = <?= json_encode($id, JSON_NUMERIC_CHECK) ?>;

    function formatDateSpanish(dateInput, ciudad = 'Chincha') {
      const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
      let dt;

      if (!dateInput) {
        dt = new Date();
      } else if (dateInput instanceof Date) {
        dt = dateInput;
      } else {
        const normalized = String(dateInput).replace(' ', 'T');
        dt = new Date(normalized);
        if (isNaN(dt.getTime())) {
          const parts = String(dateInput).split(/[-T ]/);
          if (parts.length >= 3) {
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            dt = new Date(y, m, d);
          } else {
            dt = new Date();
          }
        }
      }

      const day = dt.getDate();
      const month = months[dt.getMonth()] || '';
      const year = dt.getFullYear();
      return `${ciudad}, ${day} de ${month.toLowerCase()} de ${year}`;
    }

    function formatCurrency(amount, currency = 'PEN') {
      const symbol = (currency === 'USD') ? '$' : 'S/';
      const numAmount = parseFloat(amount) || 0;
      return `${symbol} ${numAmount.toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })}`;
    }

    async function fetchCotizacionData() {
      try {
        const response = await fetch(`/api/cotizacion/${cotId}`);
        if (!response.ok) {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        const payload = await response.json();
        return payload.cotizacion ?? payload;
      } catch (error) {
        console.error('Error al obtener datos:', error);
        throw error;
      }
    }

    function createPDFDefinition(cotizacion, headerImageBase64) {
      const cotizacionId = String(cotizacion.id || cotId || '').padStart(7, '0');
      const fecha = formatDateSpanish(cotizacion.fecha, 'Chincha');

      // Procesar opciones de financiamiento
      const opcionesFinanciamiento = cotizacion.opciones_financiamiento || [];
      const opcionesOrdenadas = [...opcionesFinanciamiento].sort((a, b) => {
        return (parseInt(a.numcuotas) || 0) - (parseInt(b.numcuotas) || 0);
      });

      // Procesar requisitos con gastos administrativos dinámicos
      const gastosAdmin = parseFloat(cotizacion.gastosadministrativos || 1500);
      const requisitos = (cotizacion.requisitos || []).map(req => {
        const textoRequisito = req.requisito || req.texto || req;
        const textoLower = String(textoRequisito).toLowerCase();

        if (textoLower.includes('gastos administrativos') ||
          textoLower.includes('pago único') ||
          textoLower.includes('pago unico')) {
          return `Pago único por gastos administrativos S/ ${gastosAdmin.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
        return textoRequisito;
      });

      // Construir tabla de financiamiento
      const tablaFinanciamiento = opcionesOrdenadas.length > 0 ?
        opcionesOrdenadas.map((opcion, index) => {
          const meses = parseInt(opcion.numcuotas) || 0;
          const inicial = parseFloat(opcion.inicial) || 0;
          const cuota = parseFloat(opcion.valorcuota) || 0;
          const monedaTexto = (cotizacion.moneda === 'USD') ? 'DÓLARES' : 'SOLES';

          return [
            { text: (index + 1).toString(), style: 'tableCell' },
            { text: meses.toString(), style: 'tableCell' },
            { text: formatCurrency(inicial, cotizacion.moneda), style: 'tableCell' },
            { text: monedaTexto, style: 'tableCell' },
            { text: formatCurrency(cuota, cotizacion.moneda), style: 'tableCellRight' }
          ];
        }) :
        [[{ text: 'Sin opciones de financiamiento disponibles', colSpan: 5, style: 'tableCellEmpty' }]];

      // Preparar asesor info
      const asesorNombre = cotizacion.asesor?.nombre_completo || cotizacion.asesor?.nombre || 'CHARLY YACTAYO ORTIZ';
      const asesorCargo = cotizacion.asesor?.cargo || 'Ejecutivo de Ventas';
      let asesorTelefono = cotizacion.asesor?.telefono || '934 008 037';

      // Formatear teléfono
      if (asesorTelefono && !asesorTelefono.includes('056') && asesorTelefono.length === 9) {
        asesorTelefono = `(056) ${asesorTelefono}`;
      }

      return {
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [70, 65, 40, 80], // Márgenes ajustados - reducido margen superior

        header: function (currentPage, pageCount, pageSize) {
          if (headerImageBase64 && currentPage === 1) {
            return {
              image: headerImageBase64,
              width: 520,
              alignment: 'center',
              margin: [0, 10, 0, 0]
            };
          }
          return null;
        },

        footer: function (currentPage, pageCount, pageSize) {
          // Footer con información del asesor y línea naranja
          return {
            stack: [
              // Información del asesor alineada a la derecha
              {
                text: `${asesorNombre}\n${asesorCargo}\nTELÉFONO: ${asesorTelefono}`,
                style: 'footerAsesor',
                alignment: 'right',
                margin: [0, 0, 0, 5]
              },
              // Línea naranja debajo
              {
                canvas: [
                  {
                    type: 'line',
                    x1: 0,
                    y1: 0,
                    x2: 525,
                    y2: 0,
                    lineWidth: 4,
                    lineColor: '#ff6600'
                  }
                ],
                margin: [0, 0, 0, 0]
              }
            ],
            margin: [40, 10, 40, 10]
          };
        },

        content: [
          // Fecha
          {
            text: fecha.replace(/ /g, '\u00A0'),
            style: 'fecha',
            alignment: 'right',
            margin: [0, 0, 0, 8]
          },

          // Título
          {
            text: `COTIZACIÓN VEHICULAR - ${cotizacionId}`,
            style: 'titulo',
            alignment: 'center',
            margin: [0, 0, 0, 12]
          },

          // Introducción
          {
            text: 'Presente, atte. Yonda & Grupo Huaraca E.I.R.L.',
            style: 'normal',
            margin: [0, 0, 0, 4]
          },
          {
            text: 'RUC: 20609396866',
            style: 'normal',
            margin: [0, 0, 0, 8]
          },
          {
            text: 'De nuestra consideración, nos es grato dirigirnos a usted para brindarle una cotización vehicular de acuerdo al siguiente detalle:',
            style: 'normal',
            alignment: 'justify',
            margin: [0, 0, 0, 12]
          },

          // DETALLE CLIENTE - Datos del cliente y vehículo con interlineado y espaciado reducido
          {
            table: {
              widths: [140, '*'],
              body: [
                [{ text: 'Nombre del cliente', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.nombre || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Dni', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.dni || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Celular', style: 'tableClienteLabel' }, { text: `: ${cotizacion.cliente?.celular || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Marca del vehículo', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.marca || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Modelo', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.modelo || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Año', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.anio || ''}`, style: 'tableClienteValue' }],
                [{ text: 'Color', style: 'tableClienteLabel' }, { text: `: ${cotizacion.vehiculo?.color || 'Por definir'}`, style: 'tableClienteValue' }],
                [{ text: 'Precio', style: 'tableClienteLabel' }, { text: `: ${formatCurrency(cotizacion.precios?.precio_original || 0, cotizacion.moneda)}`, style: 'tableClienteValue' }]
              ]
            },
            layout: {
              hLineWidth: function () { return 0; },
              vLineWidth: function () { return 0; },
              paddingLeft: function () { return 0; },
              paddingRight: function () { return 0; },
              paddingTop: function () { return 1; },
              paddingBottom: function () { return 1; }
            },
            margin: [0, 0, 0, 12]
          },

          // Tabla de financiamiento
          {
            table: {
              headerRows: 1,
              widths: [30, 50, 70, 80, '*'],
              body: [
                [
                  { text: '#', style: 'tableHeader' },
                  { text: 'Meses', style: 'tableHeader' },
                  { text: 'Inicial', style: 'tableHeader' },
                  { text: 'Moneda', style: 'tableHeader' },
                  { text: 'Monto', style: 'tableHeader' }
                ],
                ...tablaFinanciamiento
              ]
            },
            layout: {
              hLineWidth: function () { return 0.5; },
              vLineWidth: function () { return 0.5; },
              hLineColor: function () { return '#000000'; },
              vLineColor: function () { return '#000000'; }
            },
            margin: [0, 0, 0, 15]
          },

          // Instrucciones y requisitos
          {
            text: 'Con la finalidad de iniciar el proceso de desembolso de su crédito agradeceremos entregar a nuestro ejecutivo de ventas la siguiente documentación:',
            style: 'normal',
            margin: [0, 0, 0, 8]
          },

          // Lista de requisitos con más margen izquierdo
          ...(requisitos.length > 0 ? [{
            ol: requisitos,
            style: 'lista',
            margin: [30, 0, 0, 12] // Aumentado el margen izquierdo de 0 a 30
          }] : []),

          // Nota destacada con fondo amarillo suave
          {
            table: {
              widths: ['*'],
              body: [
                [{
                  text: 'ENTREGA DE LA UNIDAD EN UN MÁXIMO DE 25 DÍAS HÁBILES',
                  fontSize: 11,
                  bold: true,
                  alignment: 'center',
                  fillColor: '#fff9c4',
                  noWrap: true
                }]
              ]
            },
            layout: {
              hLineWidth: function () { return 0; },
              vLineWidth: function () { return 0; },
              paddingLeft: function () { return 8; },
              paddingRight: function () { return 8; },
              paddingTop: function () { return 4; },
              paddingBottom: function () { return 4; }
            },
            alignment: 'center',
            margin: [20, 8, 20, 12]
          },

          // Cierre
          {
            text: 'Finalmente, agradecemos su confianza en nosotros y reiteramos nuestro compromiso en dar cumplimiento a todos los procesos señalados.',
            style: 'normal',
            alignment: 'justify',
            margin: [0, 0, 0, 8]
          },
          {
            text: 'Atte.',
            style: 'normal',
            bold: true,
            margin: [0, 0, 0, 15]
          }
        ],

        styles: {
          fecha: {
            fontSize: 11
          },
          titulo: {
            fontSize: 15,
            bold: true
          },
          normal: {
            fontSize: 11,
            lineHeight: 1.15
          },
          // Estilos específicos para la tabla de cliente con interlineado reducido
          tableClienteLabel: {
            fontSize: 10,
            bold: true,
            lineHeight: 0.95,  // Interlineado reducido
            margin: [0, 1, 0, 1]  // Espaciado vertical reducido
          },
          tableClienteValue: {
            fontSize: 10,
            bold: true,
            lineHeight: 0.95,  // Interlineado reducido
            margin: [0, 1, 0, 1]  // Espaciado vertical reducido
          },
          tableLabel: {
            fontSize: 10,
            bold: true
          },
          tableValue: {
            fontSize: 10,
            bold: true
          },
          tableHeader: {
            fontSize: 10,
            bold: true,
            fillColor: '#e9ecef',
            alignment: 'center'
          },
          tableCell: {
            fontSize: 10,
            alignment: 'center'
          },
          tableCellRight: {
            fontSize: 10,
            alignment: 'right',
            bold: true
          },
          tableCellEmpty: {
            fontSize: 10,
            alignment: 'center',
            color: '#666',
            italics: true
          },
          lista: {
            fontSize: 11,
            lineHeight: 1.15,
            bold: true
          },
          destacado: {
            fontSize: 11,
            bold: true,
            margin: [8, 8, 8, 8] // Padding interno para el texto
          },
          footerAsesor: {
            fontSize: 11,
            color: '#333333',
            lineHeight: 1.2,
            bold: true
          },
          asesorInfo: {
            fontSize: 10,
            color: '#333333',
            lineHeight: 1.3,
            bold: true
          }
        }
      };
    }

    function showDownloadModal(pdfDocGenerator, filename) {
      const modalOverlay = document.createElement('div');
      modalOverlay.id = 'download-modal-overlay';
      modalOverlay.innerHTML = `
        <div class="modal">
            <p style="margin: 0 0 20px 0; font-size: 14px;">
                PDF generado correctamente. ¿Desea descargarlo?
            </p>
            <div>
                <button id="btn-download-pdf">Descargar</button>
                <button id="btn-cancel-download">Cancelar</button>
            </div>
        </div>
      `;

      document.body.appendChild(modalOverlay);

      const btnDownload = modalOverlay.querySelector('#btn-download-pdf');
      const btnCancel = modalOverlay.querySelector('#btn-cancel-download');

      function closeModal() {
        if (modalOverlay && modalOverlay.parentNode) {
          modalOverlay.parentNode.removeChild(modalOverlay);
        }
      }

      btnDownload.addEventListener('click', () => {
        try {
          pdfDocGenerator.download(filename);
          closeModal();
          setTimeout(() => {
            try { window.close(); } catch (e) { }
          }, 1000);
        } catch (error) {
          console.error('Error al descargar:', error);
          alert('Error al descargar el PDF');
        }
      });

      btnCancel.addEventListener('click', () => {
        closeModal();
        setTimeout(() => {
          try { window.close(); } catch (e) { }
        }, 100);
      });

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

      // Cerrar al hacer clic fuera
      modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
          closeModal();
          setTimeout(() => {
            try { window.close(); } catch (e) { }
          }, 100);
        }
      });

      setTimeout(() => btnDownload.focus(), 100);
    }

    async function convertImageToBase64(imagePath) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';

        img.onload = function () {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');

          canvas.width = img.naturalWidth;
          canvas.height = img.naturalHeight;

          ctx.drawImage(img, 0, 0);

          try {
            const dataURL = canvas.toDataURL('image/jpeg', 0.85);
            resolve(dataURL);
          } catch (error) {
            reject(error);
          }
        };

        img.onerror = function () {
          console.warn(`No se pudo cargar la imagen: ${imagePath}`);
          resolve(null);
        };

        img.src = imagePath;
      });
    }

    async function generatePDFCotizacion() {
      const loadingIndicator = document.getElementById('loading-indicator');

      try {
        // Obtener datos de la cotización
        const cotizacion = await fetchCotizacionData();

        // Convertir imagen de cabecera a base64
        const headerImageBase64 = await convertImageToBase64('/assets/images/logos/cabecera-yondaa.png');

        // Crear definición del PDF con imágenes
        const docDefinition = createPDFDefinition(cotizacion, headerImageBase64);

        // Crear PDF
        const pdfDocGenerator = pdfMake.createPdf(docDefinition);
        const filename = `cotizacion-${String(cotizacion.id || cotId || 'yonda').padStart(7, '0')}.pdf`;

        // Ocultar loading
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        // Mostrar modal de descarga
        showDownloadModal(pdfDocGenerator, filename);

      } catch (error) {
        console.error('Error al generar PDF:', error);
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }
        alert('Error al cargar los datos de la cotización. La ventana se cerrará.');
        setTimeout(() => window.close(), 50);
      }
    }

    // Iniciar proceso cuando se carga la página
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => generatePDFCotizacion(), 100);
    });
  </script>
</body>

</html>