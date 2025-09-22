<?php if ($estadoActual == 'emitido'): ?>
    <!-- Emitido -->
    <!-- <a href="/oc/reporte/<?= $ordenCompra['idordencompra'] ?>" target="_blank" title="PDF OC">
        <i class="bi bi-filetype-pdf text-danger fs-5 me-2"></i>
    </a> -->
    <button type="button" title="PDF OC" data-idoc=<?= $ordenCompra['idordencompra'] ?> id="btn-pdf-oc" class="btn-pdf-oc-class" style="background: none; border: none; padding: 0;">
            <i class="bi bi-filetype-pdf text-danger fs-5 me-2"></i>
    </button>
    <a href="#" class="show-details" data-idoc="<?= $ordenCompra['idordencompra'] ?>" title="Ver detalle">
        <i class="bi bi-info-circle text-primary fs-5 me-2"></i>
    </a>
    <a href="#" class="btn-abrir-modal-estado" data-id="<?= $ordenCompra['idordencompra'] ?>" data-accion="proceso" data-ruta="/oc/updateEstado/proceso/<?= $ordenCompra['idordencompra'] ?>" title="OC en proceso">
        <i class="bi-hourglass-split text-warning fs-5 me-2"></i>
    </a>
    <a href="#" class="btn-abrir-modal-estado" data-id="<?= $ordenCompra['idordencompra'] ?>" data-accion="anulado" data-ruta="/oc/updateEstado/anulado/<?= $ordenCompra['idordencompra'] ?>" title="OC anulado">
        <i class="bi bi-folder-x text-danger fs-5"></i>
    </a>

<?php elseif ($estadoActual == 'proceso'): ?>
    <!-- Proceso -->
    <a href="/oc/pagos/<?= $ordenCompra['idordencompra'] ?>" title="Pagar">
        <i class="bi bi-currency-dollar text-success fs-5"></i>
    </a>

<?php elseif ($estadoActual == 'pagado'): ?>
    <!-- Pagado -->
    <a href="#" data-idocmodal="<?= $ordenCompra['idordencompra'] ?>" title="Verificar estado de autos">
        <i class="bi bi-bookmark-check text-success fs-5 me-2"></i>
    </a>
    <a href="#" class="show-details" data-idoc="<?= $ordenCompra['idordencompra'] ?>" title="Ver detalle">
        <i class="bi bi-info-circle text-primary fs-5 me-2"></i>
    </a>
    <a href="/oc/pagos/<?= $ordenCompra['idordencompra'] ?>" title="Pagos realizados">
        <i class="bi-receipt text-warning fs-5"></i>
    </a>
<?php endif; ?>