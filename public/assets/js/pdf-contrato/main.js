import { getPagina1 } from './secciones/pagina1.js';
import { getPagina2 } from './secciones/pagina2.js';
import { getPagina3 } from './secciones/pagina3.js';
import { getPagina4 } from './secciones/pagina4.js';
import { getPagina5 } from './secciones/pagina5.js';
import { getPagina6 } from './secciones/pagina6.js';
import { getPagina7 } from './secciones/pagina7.js';
import { getPagina8 } from './secciones/pagina8.js';
import { getPagina9 } from './secciones/pagina9.js';

async function generarPDFContrato(data) {
    try {
        // console.log('Datos del contrato para PDF:', data);

        const docDefinition = {
            pageSize: 'A4',
            pageOrientation: 'portrait',
            header: {
                image: window.cabeceraYonda,
                width: 620,
                height:40,
                alignment: 'left',
                margin: [-23, 25, 0, 20]
            },
            content: [
                ...getPagina1(data),
                { text: '', pageBreak: 'before'},
                ...getPagina2(data),
                { text: '', pageBreak: 'before'},
                ...getPagina3(),
                {text:'',pageBreak:'before'},
                ...getPagina4(),
                {text:'', pageBreak:'before'},
                getPagina5(),
                {text:'',pageBreak:'before'},
                ...getPagina6(),
                {text:'', pageBreak:'before'},
                ...getPagina7(data),
                {text:'', pageBreak:'before'},
                getPagina8(data),
                {text:'',pageBreak:'before'},
                getPagina9(data),

               
            ],
             footer: function() {
                return {
                    columns: [{
                        image: window.footerYonda,
                        width: 600,
                        height: 25,
                        alignment: 'center',
                    }]
                };
            },
           styles: {
              
                titulo: { 
                    
                    bold: true, 
                    alignment: 'center' 
                },
                
            
                tituloContrato: {
                   
                    bold: true,
                    fontSize:10,
                    alignment: 'center',
                    margin: [0, 2, 0, 2]
                },
                subtituloContrato: {
                   
                    alignment: 'center',
                    margin: [0, 2, 0, 2],
                    bold: true
                },
                parrafo: {
                  
                    alignment: 'justify',
                    lineHeight: 1.3
                },
                cellText: {
                 
                    fontSize: 7.5,
                    margin: [2, 2, 2, 2] 
                },
                parrafoLegal: {
                   
                    alignment: 'justify',
                    lineHeight: 1.2
                },
                lineaFirma: {
                    margin: [0, 0, 0, 1], 
                },
                tituloFirma: {
                    bold: true,
                   
                    margin: [0, 0, 0, 2] 
                },
                textoFirma: {
                },
                resumenHeader: {
                    bold: true,
                   
                    fillColor: '#EEEEEE', 
                    margin: [4, 4, 4, 4]
                },
               
                tituloImportante: {
                    bold: true,
                    decoration:'underline',
                    fontSize:10,
                    alignment: 'center'
                },
                listaItem: {
                
                    alignment: 'justify',
                    lineHeight: 1.2,
                    margin: [0, 0, 0, 8] 
                }
            },

            defaultStyle: { fontSize: 8,    color: '#333333' },
            pageMargins: [60, 80, 40,30],
    
          
        };

        pdfMake.createPdf(docDefinition).open();
    } catch (error) {
        console.error('Error generando PDF:', error);
    }
}

// Exponer función global
window.generarPDFContrato = generarPDFContrato;
