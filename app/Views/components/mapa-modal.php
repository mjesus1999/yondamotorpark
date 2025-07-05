<!-- Modal del Mapa -->
<div class="modal fade" id="modalMapa" tabindex="-1" aria-labelledby="modalMapaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMapaLabel">Seleccionar Ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="mapa" style="height: 400px; width: 100%;"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="buscarDireccion" class="form-label">Buscar dirección:</label>
                            <input type="text" class="form-control" id="buscarDireccion" placeholder="Ingrese una dirección...">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="btnBuscar">Buscar</button>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Coordenadas seleccionadas:</label>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="latitudSeleccionada" readonly>
                                <label for="latitudSeleccionada">Latitud</label>
                            </div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="longitudSeleccionada" readonly>
                                <label for="longitudSeleccionada">Longitud</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btnConfirmarUbicacion">Confirmar Ubicación</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 