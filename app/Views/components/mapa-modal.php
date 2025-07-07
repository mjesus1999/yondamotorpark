<!-- Modal del Mapa -->
<div class="modal fade" id="modalMapa" tabindex="-1" aria-labelledby="modalMapaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalMapaLabel">Seleccionar Ubicación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Mapa grande a la izquierda -->
                    <div class="col-md-8">
                        <div id="mapa" class="border rounded" style="height: 400px; width: 100%;"></div>
                    </div>

                    <!-- Controles a la derecha -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="buscarDireccion" class="form-label fw-semibold">Buscar dirección:</label>
                                    <input type="text" class="form-control" id="buscarDireccion" placeholder="Ingrese una dirección...">
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
                                <div class="d-grid">
                                    <button type="button" class="btn btn-success btn-sm" id="btnConfirmarUbicacion">
                                        <i class="bi bi-check-circle-fill"></i> Confirmar Ubicación
                                    </button>
                                </div>
                            </div>
                        </div> <!-- card -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
