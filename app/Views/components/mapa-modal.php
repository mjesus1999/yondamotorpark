<div class="modal fade" id="modalMapa" aria-labelledby="modalMapaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow d-flex flex-column h-100">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalMapaLabel">Seleccionar Ubicación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="row g-4 h-100">
                    <div class="col-md-9 d-flex flex-column">
                        <div id="mapa" class="border rounded flex-grow-1" style="min-height: 400px; width: 100%;"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 h-100"> <div class="card-body d-flex flex-column"> <div class="mb-3">
                                    <label for="buscarDireccion" class="form-label fw-semibold">Buscar dirección:</label>
                                    <input type="text" class="form-control" id="buscarDireccion" placeholder="Ingrese dirección y presione Buscar">
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnBuscar">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                </div>
                                <hr>
                                <label class="form-label fw-semibold">Coordenadas seleccionadas:</label>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="latitudSeleccionada" readonly>
                                    <label for="latitudSeleccionada">Latitud</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="longitudSeleccionada" readonly>
                                    <label for="longitudSeleccionada">Longitud</label>
                                </div>
                                <div class="d-grid mt-auto">
                                     <button type="button" class="btn btn-success btn-sm" id="btnConfirmarUbicacion">
                                         <i class="bi bi-check-circle-fill"></i> Confirmar Ubicación
                                     </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>