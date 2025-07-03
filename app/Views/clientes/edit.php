<div class="container-fluid">

    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-yonda text-white">
                <h5 class="mb-0">Actualizar Cliente (Normales)</h5>
            </div>
            <div class="card-body">

                <form action="" autocomplete="off" id="formulario-cliente-personas">

                    <div class="modal-content">
                        <div class="modal-header bg-yonda">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar Cliente (Persona)</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="apellidos" placeholder="Apellidos"
                                            required>
                                        <label for="apellidos">Apellidos</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nombres" placeholder="Nombres" required>
                                        <label for="nombres">Nombres</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="estadocivil" id="estadocivil" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            <option value="SOL">Solter@</option>
                                            <option value="CAS">Casad@</option>
                                            <option value="VDO">Viud@</option>
                                            <option value="DVC">Divorciad@</option>
                                            <option value="CNV">Conviviente</option>
                                        </select>
                                        <label for="estadocivil">Estado civil</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" placeholder="Correo">
                                        <label for="email">Correo</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="departamento" id="departamento" class="form-select" required>
                                            <option value="">Seleccione</option>
                                        </select>
                                        <label for="departamento">Departamento</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="provincia" id="provincia" class="form-select" required>
                                            <option value="">Seleccione</option>
                                        </select>
                                        <label for="provincia">Provincia</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="distrito" id="distrito" class="form-select" required>
                                            <option value="">Seleccione</option>
                                        </select>
                                        <label for="distrito">Distrito</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="direccion" placeholder="Dirección">
                                        <label for="direccion">Dirección</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="telprimario" placeholder="Teléfono"
                                            maxlength="9" pattern="[0-9]+" required>
                                        <label for="telprimario">Teléfono</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="latitud" placeholder="Latitud">
                                        <label for="latitud">Latitud</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="longitud" placeholder="Longitud">
                                        <label for="longitud">Longitud</label>
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <button type="button" class="btn btn-sm btn-success" id="btn-mapa">Ver mapa</button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>

                    </div>
                </form>

            </div>

        </div>


    </div>

</div>