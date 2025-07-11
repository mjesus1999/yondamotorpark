<?php include __DIR__ . '/../layout/header.php'; ?>



<div class="container-fluid">

  <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex align-items-center justify-content-start">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Concesionarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <a class="btn btn-sm btn-outline-primary" href="/concesionarios" class="">Mostrar lista</a>
      </div>
    </div>
  </div>

  <div class="mb-2">
    <form action="" id="form-registro-concesionario" autocomplete="off">
      <div class="card mb-0">
        <div class="card-header">Complete la información solicitada</div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-3">
              <div class="input-group">
                <div class="form-floating">
                  <input type="text" id="ruc" class="form-control text-center" pattern="[0-9]+"
                    title="Solo se permiten números" maxlength="11" minlength="11" placeholder="RUC"
                    autofocus required>
                  <label for="ruc" class="form-label">RUC</label>
                </div>
                <button type="button" id="btn-buscar-concesionario" class="btn btn-success"><i
                    class="fa-solid fa-magnifying-glass"></i></button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="nombrecomercial" placeholder="Nombre comercial" required>
                <label for="nombrecomercial" class="form-label">Nombre comercial</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="razonsocial" placeholder="Razon social" readonly required>
                <label for="razonsocial" class="form-label">Razon social</label>
              </div>
            </div>
          </div> <!-- ./row -->
        </div> <!-- ./card-body -->
        <div class="card-footer text-end">
          <button type="reset" id="btn-cancelar-registro" class="btn btn-sm btn-outline-secondary">Cancelar</button>
          <button type="submit" id="btn-registrar-concesionario" class="btn btn-sm btn-primary">Guardar</button>
        </div> <!-- ./card-footer -->
      </div> <!-- ./card -->
    </form>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6 d-flex align-items-center justify-content-start">
          <span>Lista de tiendas</span>
        </div>
        <div class="col-md-6 text-end">
          <button type="button" id="btn-modal-tiendas" class="btn btn-sm btn-outline-primary">Agregar</button>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row g-2">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table table-sm table-hover table-hover-yonda" id="tabla-tiendas">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Ubigeo</th>
                  <th>Dirección</th>
                  <th>Teléfono</th>
                  <th>Email</th>
                  <th>Contacto</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="7" class="text-center mt-2 mb-2">No hay tiendas registradas</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Zona de modales -->
   <div class="modal fade" id="modal-tiendas" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="Modal tiendas" aria-hidden="true">
    <div class="modal-dialog">
      <form action="" autocomplete="off" id="form-registro-tienda">
        <div class="modal-content">
          <div class="modal-header bg-yonda">
            <h5 class="modal-title">Agregar tienda</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-floating mb-2">
              <select name="departamentos" id="departamento" class="form-select" required>
                <option value="">Seleccione</option>
              </select>
              <label for="">Departamentos</label>
            </div>
            <div class="form-floating mb-2">
              <select name="provincias" id="provincia" class="form-select" required>
                <option value="">Seleccione</option>
              </select>
              <label for="">Provincias</label>
            </div>
            <div class="form-floating mb-2">
              <select name="distritos" id="distrito" class="form-select" required>
                <option value="">Seleccione</option>
              </select>
              <label for="">Distritos</label>
            </div>
            <div class="mb-2">
              <textarea name="direccion" id="direccion" rows="3" class="form-control" placeholder="Dirección"
                required></textarea>
            </div>

            <div class="row g-2">
              <div class="col-md-4">
                <div class="form-floating mb-2">
                  <input type="text" id="telefono" name="telefono" maxlength="12" minlength="9" pattern="[0-9]+"
                    title="Solo se permiten números" class="form-control" placeholder="Teléfono" required>
                  <label for="telefono">Teléfono</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-floating">
                  <input type="text" name="contacto" id="contacto" class="form-control" placeholder="Contacto" required>
                  <label for="contacto" class="form-label">Contacto</label>
                </div>
              </div>
            </div>
            <div class="form-floating mb-2">
              <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
              <label for="email" class="form-label">Email</label>
            </div>

          </div> 
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-sm btn-primary" id="btn-agregar-tienda">Guardar</button>
          </div>
        </div> 
      </form>
    </div> 
  </div> 


  <script src="/assets/js/ubigeo.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {


      let idconcesionario = null;




      const ruc = document.querySelector('#ruc');
      const razonSocial = document.querySelector('#razonsocial');
      const nombreComercial = document.querySelector('#nombrecomercial');
      const telefono = document.querySelector('#telefono');
      const direccion = document.querySelector('#direccion');
      const email = document.querySelector('#email');
      const contacto = document.querySelector('#contacto');
      const distrito = document.querySelector('#distrito');

    

      const btnBuscarConcesionario = document.querySelector('#btn-buscar-concesionario');
      const btnRegistrarConcesionario = document.querySelector('#btn-registrar-concesionario');
      const btnCancelarRegistro = document.querySelector('#btn-cancelar-registro');
      const btnModalTiendas = document.querySelector('#btn-modal-tiendas');

      const modalTienda = new bootstrap.Modal(document.getElementById("modal-tiendas"))

      const formConcesionarioRegistro = document.querySelector('#form-registro-concesionario');
      const formTiendaRegistro = document.querySelector('#form-registro-tienda');

      const tablaTiendas = document.querySelector('#tabla-tiendas tbody');



      // Obtiener los Concesionarios buscado por la api de SUNAT
      async function obtenerConcesionarioBySunat() {

        razonSocial.value = 'Buscando.....';
        try {

          if (ruc.value.length === 11) {

            const response = await fetch(`/api/concesionarioSunat/${ruc.value}`);
            const data = await response.json();
            // console.log(data);

            if (data.razonSocial !== undefined) {

              razonSocial.value = data.razonSocial;
              nombreComercial.value = '';
              nombreComercial.focus();
              showToast('Empresa encontrada', 'SUCCESS', 1500);
            } else {
              showToast('No existe la empresa', 'WARNING', 2000);
              razonSocial.value = '';
            }
          } else {
            showToast("Se requiere 11 dígitos", "WARNING", 1500);
          }

        } catch (error) {
          console.error(error);
        }
      }

      // Obtener los concesionarios

      async function obtenerConcesionarioByDB() {
        razonSocial.value = 'Buscando....';

        try {

          if (ruc.value.length === 11) {
            const response = await fetch(`/api/concesionarioDB/${ruc.value}`);
            const data = await response.json();

            // Si no existe en la DB - Buscar por la API DE SUNAT
            if (data.length === 0) {
              btnRegistrarConcesionario.removeAttribute("disabled");
              btnCancelarRegistro.removeAttribute("disabled");
              razonSocial.setAttribute("disabled", true);
              ruc.setAttribute("disabled", true);
              idconcesionario = null;
              obtenerConcesionarioBySunat();

            } else {
              // Si se encontro en la DB:
              // console.log(data)
              idconcesionario = data[0].idconcesionario;
              razonSocial.value = data[0].razonsocial;
              nombreComercial.value = data[0].nombrecomercial;
              btnCancelarRegistro.setAttribute("disabled", true);
              btnRegistrarConcesionario.setAttribute("disabled", true);
              nombreComercial.setAttribute("disabled", true);
              razonSocial.setAttribute("disabled", true);
              ruc.setAttribute("disabled", true);
              obtenerTiendasByConcesionario();

            }

          } else {
            razonSocial.value = '';
            showToast("Se requiere 11 dígitos", "WARNING", 1500);
          }


        } catch (error) {
          console.error(error);
        }
      }
      // Obtiene las tiendas del concesionario 

      async function obtenerTiendasByConcesionario() {
        try {

          const response = await fetch(`/api/tiendasConcesionario/${idconcesionario}`)
          const data = await response.json();

          if (data.length === 0) {

            tablaTiendas.innerHTML += `<tr>
                  <td colspan="7" class="text-center mt-2 mb-2">No hay tiendas registradas</td>
                </tr>
              `;
          } else {
            tablaTiendas.innerHTML = ``;
            let numFila = 1;
            data.forEach(tienda => {
              tablaTiendas.innerHTML += `
                  <tr>
                    <td class='align-middle'>${numFila}</td>
                    <td class='align-middle'>${tienda.ubigeo}</td>
                    <td class='align-middle'>${tienda.direccion}</td>
                    <td class='align-middle'>${tienda.telefono}</td>
                    <td class='align-middle'>${tienda.email}</td>
                    <td class='align-middle'>${tienda.contacto}</td>
                    <td>
                      <a href='#' title='Editar' data-idtienda='${tienda.idtienda}' class='btn btn-sm btn-outline-primary edit'><i class="fa-solid fa-pen"></i></a>
                      <a href='#' title='Eliminar' data-idtienda='${tienda.idtienda}' class='btn btn-sm btn-outline-danger delete'><i class="fa-solid fa-trash"></i></a>
                    </td>
                  </tr>
                `;
              numFila++;
            });
          }


        } catch (error) {
          console.error(error);
        }

      }

      // Registrar la Tienda
      formTiendaRegistro.addEventListener('submit', async (event) => {

        event.preventDefault();

        if (confirm('¿Desea registrar la tienda?')) {

            params = new FormData();
            params.append('iddistrito', distrito.value);
            params.append('idconcesionario', idconcesionario);
            params.append('direccion', direccion.value);
            params.append('email', email.value);
            params.append('telefono', telefono.value);
            params.append('contacto', contacto.value);

            try {
              const response = await fetch(`/tiendas/store`,{
                method:'POST',
                body:params
              });

              const data = await response.json();
              console.log(data);
              if (data.success) {
                showToast(data.message, 'SUCCESS', 1500);
                modalTienda.hide();
                obtenerTiendasByConcesionario();
              } else {
                showToast(data.message,'WARNING', 1500);
              }
              
            } catch(error) {
              console.error(error);
            }

        }

      });

      // Registrar el concesionario.
      formConcesionarioRegistro.addEventListener('submit', async (event) => {
        event.preventDefault()

        if (confirm('¿Desea registrar el concesionario?')) {
          params = new FormData();
          params.append('ruc', ruc.value);
          params.append('nombrecomercial', nombreComercial.value);
          params.append('razonsocial', razonSocial.value);


          try {

            const response = await fetch(`/concesionarios/store`, {
              method: 'POST',
              body: params
            });
            const data = await response.json();
            // console.log(data);
            if (data.success) {
              idconcesionario = data.id;
              console.log('IDCONCESIONARIO:', idconcesionario);
              showToast(data.message, 'SUCCESS', 1500);
              btnCancelarRegistro.setAttribute("disabled", true);
              btnRegistrarConcesionario.setAttribute("disabled", true);
            } else {
              showToast(data.message, 'ERROR', 2000);
            }


          } catch (error) {
            console.log(error);
          }


        }


      });


      btnModalTiendas.addEventListener('click',  async () => {
        console.log(idconcesionario);
        if (idconcesionario === null) {
          showToast('Pimero indicar la tienda', 'WARNING', 1500);
          ruc.focus();
        } else {
          modalTienda.show();
        }
      });






















      btnBuscarConcesionario.addEventListener('click', () => {
        obtenerConcesionarioByDB();
      });




    });
  </script>



  <?php include __DIR__ . '/../layout/footer.php'; ?>