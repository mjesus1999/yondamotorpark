<!-- <?php
    // var_dump($ocDetalles);
?> -->


<style>
    body {
        font-family: Calibri, sans-serif; /* Usar sans-serif como fallback */
        font-size: 8px;
        text-align: justify;
        margin: 2cm;
    }

    .container {
        width: 800px;
        margin: 0 auto; /* Centrar el contenedor en la página */
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%; /* Asegura que el header ocupe todo el ancho disponible */
        padding-top: 5px;
        padding-bottom: 5px;
        box-sizing: border-box;
    }

    .header img {
        margin-left: 20px;
        height: 50px;
        width: auto; /* Mantener la proporción */
        max-width: 100px;
        flex-shrink: 0;
        margin-right: 10px;
        z-index: 2;
    }

    .orden-compra {
        border: 1px solid black;
        margin-right: 20px;
        padding: 5px 10px;
        flex-grow: 1; /* Permite que el div ocupe el espacio restante */
        text-align: center; /* Centra el contenido del div */
    }

    .orden-compra p {
        font-weight: bold;
        text-align: center; /* Asegura que el texto dentro del párrafo se centre */
        margin: 0; /* Elimina el margen por defecto del párrafo */
        font-size: 12px; /* Ajusta el tamaño de la fuente para el título */
    }

    /* Estilos para los bloques de información (tablas) */
    .section-block {
          margin-top: 15px; /* Espacio entre el header y la primera tabla, y entre tablas */
        border: 1px solid #ccc;
        padding: 5px; /* Reducir padding para más compactación si es necesario */
        background-color: #f9f9f9;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* Sombra más sutil */
    }

    .section-block table {
         max-width:100px;
        border-collapse: collapse;
        font-size: 8px; /* Tamaño de fuente para el contenido de la tabla */
    }

    .section-block table td {
        /* border: 1px solid #ddd; /* Quitar bordes internos si quieres un look más limpio */
        padding: 3px 5px; /* Padding interno para las celdas */
        text-align: left;
        vertical-align: top;
    }

    /* Estilo para las etiquetas (primera celda de cada par) */
    .section-block table td.label {
        font-weight: bold;
        width: 15%; /* Ajusta el ancho si es necesario para las etiquetas */
        white-space: nowrap; /* Evita que las etiquetas se rompan en varias líneas */
    }

    /* Estilo para los valores (segunda celda de cada par) */
    .section-block table td.value {
        width: 35%; /* Ajusta el ancho si es necesario para los valores */
    }

    /* Estilo para el separador visual */
    .section-separator {
        border-top: 1px solid #aaa; /* Línea horizontal */
        margin: 20px 0; /* Espacio antes y después del separador */
    }
</style>


<div class="container">

    <div class="header" style="margin-top: 25px;">
        <div>
           
        </div>
        <div class="orden-compra">
            <p class="bold text-center">ORDEN DE COMPRA</p>
        </div>
    </div>

    <!-- Sección de Datos de la Orden y Concesionario -->
    <div class="section-block">
        <table>
            <tbody>
                <tr>
                    <td class="label">Punto de venta:</td>
                    <td class="value">Lima</td>
                    <td class="label">N° Oper:</td>
                    <td class="value">43434</td>
                </tr>
                <tr>
                    <td class="label">Razón social:</td>
                    <td class="value">MAQUINARIA NACIONAL S.A.C</td>
                    <td class="label">Fecha:</td>
                    <td class="value">17/07/25</td>
                </tr>
                <tr>
                    <td class="label">Ruc:</td>
                    <td class="value">20292929222</td>
                    <td class="label">Teléfono:</td>
                    <td class="value">995858555</td>
                </tr>
                <tr>
                    <td class="label">Dirección:</td>
                    <td class="value" colspan="3">CRISTOBAL #44</td> <!-- Esta celda abarca el resto de la fila -->
                </tr>
                <tr>
                    <td class="label">Vendedor:</td>
                    <td class="value" colspan="3">Juan Perez</td> <!-- Esta celda abarca el resto de la fila -->
                </tr>
            </tbody>
        </table>
    </div>



    <!-- Separador visual entre secciones -->
    <div class="section-separator"></div>

    <!-- Sección de Datos del Titular -->
    <div class="section-block">
        <table>
            <tbody>
                <tr>
                    <td class="label">Titular:</td>
                    <td class="value">YONDA Y GRUPO HUARACA E.I.R.L</td>
                    <td class="label">Teléfono:</td>
                    <td class="value">926743607</td>
                </tr>
                <tr>
                    <td class="label">DNI o RUC:</td>
                    <td class="value">206093968966</td>
                    <td class="label">Correo:</td>
                    <td class="value">asistentecontable@yondaperu.com</td>
                </tr>
                <tr>
                    <td class="label">Dirección:</td>
                    <td class="value" colspan="3">PANAMERICA SUR KM PUERTA 201</td> <!-- Esta celda abarca el resto de la fila -->
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section-block">
        <table>
            <tr>
                <th colspan="4" class="label">DESCRIPCIÓN</th>
                <th colspan="4" class="label">IMPORTE</th>
            </tr>
            <tbody>
                <?php  foreach($ocDetalles as $c => $v): ?>
                        <?php  var_dump($v) ?>

                  <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>











