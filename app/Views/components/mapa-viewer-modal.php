<!-- app/Views/components/mapa-viewer-modal.php -->

<div class="modal fade" id="modalMapaViewer" tabindex="-1" aria-labelledby="modalMapaViewerLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-yonda">
                <h5 class="modal-title" id="modalMapaViewerLabel">
                    <i class="bi bi-geo-alt-fill me-2"></i>Ubicación del Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Mapa -->
                    <div class="col-md-8">
                        <div id="mapaViewer" style="height: 500px; width: 100%;"></div>
                    </div>

                    <!-- Panel de información -->
                    <div class="col-md-4 p-3 border-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person-circle me-1"></i>Cliente:
                            </label>
                            <p class="mb-0" id="viewer-cliente-nombre">-</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-house-door me-1"></i>Dirección:
                            </label>
                            <p class="mb-0 small" id="viewer-direccion">-</p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-geo me-1"></i>Coordenadas:
                            </label>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text">Lat:</span>
                                <input type="text" class="form-control" id="viewer-latitud-display" readonly>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Lng:</span>
                                <input type="text" class="form-control" id="viewer-longitud-display" readonly>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <!-- <button type="button" class="btn btn-success btn-sm" id="btnCompartirUbicacion">
                                <i class="bi bi-share-fill me-1"></i>Compartir Ubicación
                            </button> -->
                            <!-- <a href="#" id="btnAbrirGoogleMaps" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-globe me-1"></i>Abrir en Google Maps
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>