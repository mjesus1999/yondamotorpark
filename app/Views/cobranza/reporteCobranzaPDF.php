<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de Cobranza Atrasado - Vista Previa PDF</title>
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
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
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
    // Detectar si estamos en modo preview
    const urlParams = new URLSearchParams(window.location.search);
    const isPreview = urlParams.get('preview') === '1';

    function formatDateSpanish() {
      const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
      const dt = new Date();
      const day = dt.getDate();
      const month = months[dt.getMonth()];
      const year = dt.getFullYear();
      return `Chincha Alta ${day} de ${month} del ${year}`;
    }

    function showDownloadModal(pdfDocGenerator, filename) {
      const modalOverlay = document.createElement('div');
      modalOverlay.id = 'download-modal-overlay';
      modalOverlay.innerHTML = `
        <div class="modal">
          <p style="margin: 0 0 20px 0; font-size: 14px;">PDF generado correctamente. ¿Desea descargarlo?</p>
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
            try { window.close(); } catch (e) {}
          }, 1000);
        } catch (error) {
          console.error('Error al descargar:', error);
          alert('Error al descargar el PDF');
        }
      });

      btnCancel.addEventListener('click', () => {
        closeModal();
        setTimeout(() => {
          try { window.close(); } catch (e) {}
        }, 100);
      });

      // Cerrar con ESC
      document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
          closeModal();
          document.removeEventListener('keydown', escHandler);
          setTimeout(() => {
            try { window.close(); } catch (e) {}
          }, 100);
        }
      });

      // Cerrar al hacer clic fuera
      modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
          closeModal();
          setTimeout(() => {
            try { window.close(); } catch (e) {}
          }, 100);
        }
      });

      setTimeout(() => btnDownload.focus(), 100);
    }

    function showPDFPreview(pdfDocGenerator, filename) {
      const previewHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #f5f5f5; z-index: 9999;">
          <div style="height: 60px; background: white; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0; color: #333; font-size: 18px;">Vista Previa - ${filename}</h3>
            <div>
              <button id="btn-download-preview" style="background: #d32f2f; color: white; border: none; padding: 10px 20px; margin-right: 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                Descargar PDF
              </button>
              <button id="btn-close-preview" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                Cerrar
              </button>
            </div>
          </div>
          <div id="pdf-preview-container" style="height: calc(100% - 60px); overflow: auto; padding: 20px;">
            <div id="pdf-loading" style="text-align: center; padding: 50px; color: #666;">
              <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #d32f2f; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
              <div>Generando vista previa...</div>
            </div>
            <iframe id="pdf-frame" style="width: 100%; height: 100%; border: none; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: none;"></iframe>
          </div>
        </div>
      `;

      document.body.insertAdjacentHTML('beforeend', previewHTML);

      const btnDownload = document.getElementById('btn-download-preview');
      const btnClose = document.getElementById('btn-close-preview');
      const previewContainer = document.querySelector('[style*="position: fixed"]');
      const pdfFrame = document.getElementById('pdf-frame');
      const pdfLoading = document.getElementById('pdf-loading');

      // Generar el PDF como blob
      pdfDocGenerator.getBlob((blob) => {
        const url = URL.createObjectURL(blob);
        pdfFrame.src = url;
        pdfFrame.style.display = 'block';
        pdfLoading.style.display = 'none';

        function cleanup() {
          URL.revokeObjectURL(url);
        }

        btnClose.addEventListener('click', cleanup);
        window.addEventListener('beforeunload', cleanup);
      });

      btnDownload.addEventListener('click', () => {
        try {
          pdfDocGenerator.download(filename);
        } catch (error) {
          console.error('Error al descargar:', error);
          alert('Error al descargar el PDF');
        }
      });

      btnClose.addEventListener('click', () => {
        previewContainer.remove();
        setTimeout(() => {
          try { window.close(); } catch (e) {}
        }, 100);
      });

      document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
          previewContainer.remove();
          document.removeEventListener('keydown', escHandler);
          setTimeout(() => {
            try { window.close(); } catch (e) {}
          }, 100);
        }
      });
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

    function createNotificacionPDF(headerImageBase64, footerImageBase64) {
      return {
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [60, 80, 60, 80],

        header: function (currentPage, pageCount, pageSize) {
          if (headerImageBase64) {
            return {
              image: headerImageBase64,
              width: 500,
              alignment: 'center',
              margin: [0, 20, 0, 0]
            };
          }
          return null;
        },

        footer: function (currentPage, pageCount, pageSize) {
          if (footerImageBase64) {
            return {
              image: footerImageBase64,
              width: 500,
              alignment: 'center',
              margin: [0, 0, 0, 20]
            };
          }
          return null;
        },

        content: [
          // Fecha
          {
            text: formatDateSpanish(),
            style: 'fecha',
            alignment: 'right',
            margin: [0, 0, 0, 20]
          },

          // Título principal
          {
            text: 'NOTIFICACION DE COBRANZA',
            style: 'titulo',
            alignment: 'center',
            margin: [0, 0, 0, 25]
          },

          // Presentación del cliente
          {
            text: [
              { text: 'SR. ', style: 'normal', bold: true },
              { text: 'JUAN CARLOS PÉREZ', style: 'normal', bold: true }
            ],
            margin: [0, 0, 0, 8]
          },
          {
            text: [
              { text: 'DNI: ', style: 'normal', bold: true },
              { text: '78954623', style: 'normal', bold: true }
            ],
            margin: [0, 0, 0, 8]
          },
          {
            text: [
              { text: 'Direccion: ', style: 'normal', bold: true },
              { text: 'Chincha Alta', style: 'normal', bold: true }
            ],
            margin: [0, 0, 0, 15]
          },

          // Párrafo explicativo
          {
            text: 'Mediante la presente: YHON KENNIDEY MENDOZA HUARACA. Representante General de YONDA & GRUPO HUARACA E.I.R.L hace de su conocimiento TIENE DEUDA PENDIENTE CON NUESTRA EMPRESA DEL VEHICULO CON LAS SIGUIENTES CARACTERISTICAS:',
            style: 'normal',
            alignment: 'justify',
            margin: [0, 0, 0, 20]
          },

          // Detalles del vehículo en tabla
          {
            table: {
              widths: [80, 15, '*'],
              body: [
                [
                  { text: 'Modelo', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: 'SCH6430', style: 'detalleValue' }
                ],
                [
                  { text: 'Marca', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: 'Suzuki Swift', style: 'detalleValue' }
                ],
                [
                  { text: 'Chasis', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: 'L3HMCKBE3PA000773', style: 'detalleValue' }
                ],
                [
                  { text: 'Motor', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: '209000375', style: 'detalleValue' }
                ],
                [
                  { text: 'Color', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: 'Gris', style: 'detalleValue' }
                ],
                [
                  { text: 'Placa', style: 'detalleLabel' },
                  { text: ':', style: 'detalleSeparador' },
                  { text: 'Z7L-589', style: 'detalleValue' }
                ]
              ]
            },
            layout: {
              hLineWidth: function () { return 0; },
              vLineWidth: function () { return 0; },
              paddingLeft: function () { return 0; },
              paddingRight: function () { return 0; },
              paddingTop: function () { return 2; },
              paddingBottom: function () { return 2; }
            },
            margin: [0, 0, 0, 25]
          },

          // Párrafo de incumplimiento
          {
            text: [
              'Por Incumplimiento de pago ya que según registros de cobranza de nuestra empresa Usted adeuda, POR LA MORA DE FEBRERO DE S/ 389.00 SOLES, DE LA CUOTA DE MARZO CON MORA DE S/ 1,556.50, ',
              { text: 'CUYO MONTO TOTAL A PAGAR ES DE S/ 1,945.50 SOLES', bold: true },
              ' Y HABIENDO USTED COMPROMETIDO SEGÚN EL CONTRATO NOTARIAL FIRMADO EL 05 DE OCTUBRE DEL 2023. (CADA NOTIFICACIÓN LLEGADA AL DOMICILIO SE HARA EL COBRO ADICIONAL DE S/50 SOLES).'
            ],
            style: 'normal',
            alignment: 'justify',
            margin: [0, 0, 0, 15]
          },

          // Párrafo de denuncia
          {
            text: 'A su vez se procederá a realizar la denuncia correspondiente mediante instancias legales y judiciales que amerita el caso.',
            style: 'normal',
            alignment: 'justify',
            margin: [0, 0, 0, 15]
          },

          // Atentamente
          {
            text: 'ATTE: GERENCIA',
            style: 'normal',
            alignment: 'left',
            margin: [0, 0, 0, 50]
          },

          // Sección de firma
          {
            table: {
              widths: ['*'],
              body: [
                [{ 
                  canvas: [
                    {
                      type: 'line',
                      x1: 0,
                      y1: 0,
                      x2: 200,
                      y2: 0,
                      lineWidth: 1,
                      lineColor: 'black'
                    }
                  ],
                  alignment: 'center',
                  border: [false, false, false, false],
                  margin: [0, 0, 0, 5]
                }],
                [{ 
                  text: 'YHON MENDOZA HUARACA', 
                  style: 'firma', 
                  alignment: 'center', 
                  border: [false, false, false, false],
                  margin: [0, 0, 0, 2]
                }],
                [{ 
                  text: 'GERENTE GENERAL', 
                  style: 'cargo', 
                  alignment: 'center', 
                  border: [false, false, false, false]
                }]
              ]
            },
            layout: 'noBorders',
            margin: [0, 0, 0, 0]
          }
        ],

        styles: {
          fecha: {
            fontSize: 11,
            color: '#333'
          },
          titulo: {
            fontSize: 16,
            bold: true,
            color: '#000'
          },
          normal: {
            fontSize: 11,
            lineHeight: 1.3,
            color: '#000'
          },
          detalleLabel: {
            fontSize: 11,
            bold: true,
            color: '#000'
          },
          detalleSeparador: {
            fontSize: 11,
            bold: true,
            color: '#000',
            alignment: 'center'
          },
          detalleValue: {
            fontSize: 11,
            bold: true,
            color: '#000'
          },
          firma: {
            fontSize: 12,
            bold: true,
            color: '#000'
          },
          cargo: {
            fontSize: 11,
            bold: true,
            color: '#000'
          }
        }
      };
    }

    async function generatePDFNotificacion() {
      const loadingIndicator = document.getElementById('loading-indicator');

      try {
        // Convertir imágenes a base64
        const headerImageBase64 = await convertImageToBase64('/assets/images/logos/cabecera-yondaa.png');
        const footerImageBase64 = await convertImageToBase64('/assets/images/logos/footer-yonda1.png');

        // Crear definición del PDF con datos estáticos
        const docDefinition = createNotificacionPDF(headerImageBase64, footerImageBase64);

        // Crear PDF
        const pdfDocGenerator = pdfMake.createPdf(docDefinition);
        const filename = 'notificacion-cobranza-juan-perez.pdf';

        // Ocultar loading
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        // Mostrar vista previa por defecto, o modal si no se especifica preview
        if (isPreview || !urlParams.has('preview')) {
          showPDFPreview(pdfDocGenerator, filename);
        } else {
          showDownloadModal(pdfDocGenerator, filename);
        }

      } catch (error) {
        console.error('Error al generar PDF:', error);
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }
        alert('Error al generar el PDF. La ventana se cerrará.');
        setTimeout(() => window.close(), 50);
      }
    }

    // Iniciar proceso cuando se carga la página
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => generatePDFNotificacion(), 100);
    });
  </script>
</body>

</html>