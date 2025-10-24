<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
  img:hover {
    scale: 1.03;
    transition-duration: 0.5s;
  }
</style>
<div class="container-fluid">

  <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex align-items-center justify-content-start">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Marcas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Listar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <span>Desde este módulo podrá gestionar marcas, tipos y modelos</span>
      </div>
    </div>
  </div>

  <div class="row">

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col">Lista de marcas</div>
            <div class="col text-end"><a href="#" id="lnk-agregar-marca">[ Agregar ]</a></div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm table-hover table-hover-yonda" id="tabla-marcas">
            <colgroup>
              <col style="width: 65%;">
              <col style="width: 10%;">
              <col style="width: 25%;">
            </colgroup>
            <thead>
              <tr>
                <th>Nombre marca</th>
                <th>#</th>
                <th class="text-center">Op</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($marcas)) : ?>
                <?php foreach ($marcas as $marca) : ?>
                  <tr class="marca-fila" data-idmarca="<?= htmlspecialchars($marca['idmarca']) ?>" data-nombre="<?= htmlspecialchars($marca['marca']) ?>">
                    <td><?= htmlspecialchars($marca['marca']) ?></td>
                    <td class="text-center" data-conteo="modelos"><?= htmlspecialchars($marca['modelos']) ?></td>
                    <td class="text-center">
                      <a href="#" class="btn btn-sm btn-outline-primary btn-editar-marca" data-idmarca="<?= htmlspecialchars($marca['idmarca']) ?>" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-marca" title="Eliminar" data-idmarca="<?= htmlspecialchars($marca['idmarca']) ?>">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr id="fila-no-marcas">
                  <td colspan="3" class="text-center">No hay marcas disponibles</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="text-end">
            <span style="font-style: italic;">Seleccione un elemento</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
              <strong id="marca-activa">SIN ESPECIFICAR</strong>
            </div>
            <div class="col-md-6 text-end">
              <a href="#" id="lnk-agregar-modelo" class="d-none">[ Agregar Modelo ]</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm table-hover table-hover-yonda" id="tabla-modelos">
            <colgroup>
              <col style="width: 10%;">
              <col style="width: 30%;">
              <col style="width: 30%;">
              <col style="width: 10%;">
              <col style="width: 20%;">
            </colgroup>
            <thead>
              <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Operaciones</th>
              </tr>
            </thead>
            <tbody>
              <tr id="fila-no-modelos">
                <td colspan="5" class="text-center">Seleccione una marca para continuar</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
<div class="modal fade" id="modal-marcas" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalMarcas" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form autocomplete="off" id="formulario-marcas">
        <div class="modal-header bg-yonda">
          <h1 class="modal-title fs-5" id="modal-marcas-titulo">Marcas</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idmarca" name="idmarca" value="0">
          <div class="form-floating">
            <input type="text" class="form-control" id="marca" name="marca" maxlength="30" placeholder="Nueva marca" required>
            <label for="marca" class="form-label">Nombre de la marca</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-modelos" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalModelos" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form autocomplete="off" id="formulario-modelos" enctype="multipart/form-data">
        <div class="modal-header bg-yonda">
          <h1 class="modal-title fs-5" id="modal-modelos-titulo">MODELO</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idmodelo" name="idmodelo" value="0">
          <input type="hidden" id="idmarca_modelo" name="idmarca" value="0">

          <div class="row g-2">
            <div class="col-md-5">
              <div class="form-floating mb-2">
                <select name="idtipovehiculo" id="tipo-vehiculo" class="form-select" required>
                  <option value="">Seleccione</option>
                </select>
                <label for="tipo-vehiculo">Tipo vehículo</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-floating mb-2">
                <input type="text" class="form-control" id="modelo" name="modelo" required>
                <label for="modelo">Modelo</label>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-floating mb-2">
                <input type="text" class="form-control" maxlength="4" pattern="[0-9]+" title="Solo se permiten números" id="anio" name="anio" required>
                <label for="anio">Año</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mb-2">
              <label for="imagen" class="form-label">Imagen Referencial (Opcional)</label>
              <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg">
              <small id="imagen-actual-texto" class="form-text text-primary fw-bold"></small>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-12 text-center">
              <img src="/assets/images/vehiculos/model-cars.jpg"
                id="imagen-preview"
                style="max-width: 100%; max-height: 550px; height: auto; object-fit: cover; border-radius: 8px;">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", () => {


    const tablaMarcasBody = document.querySelector("#tabla-marcas tbody");
    const tablaModelosBody = document.querySelector("#tabla-modelos tbody");
    const tipoVehiculoSelect = document.querySelector("#tipo-vehiculo");

    const modalMarca = new bootstrap.Modal(document.getElementById("modal-marcas"));
    const modalModelo = new bootstrap.Modal(document.getElementById("modal-modelos"));

    const formularioMarcas = document.querySelector("#formulario-marcas");
    const formularioModelos = document.querySelector("#formulario-modelos");

    const lnkAgregarMarca = document.querySelector("#lnk-agregar-marca");
    const lnkAgregarModelo = document.querySelector("#lnk-agregar-modelo");
    const marcaActivaTitulo = document.querySelector("#marca-activa");
    const modalModelosTitulo = document.querySelector("#modal-modelos-titulo");


    const inputIdMarca = document.querySelector("#idmarca");
    const inputMarca = document.querySelector("#marca");
    const inputIdModelo = document.querySelector("#idmodelo");
    const inputIdMarcaModelo = document.querySelector("#idmarca_modelo");
    const inputModelo = document.querySelector("#modelo");
    const inputAnio = document.querySelector("#anio");
    const inputImagen = document.querySelector("#imagen");
    const imagenPreview = document.querySelector("#imagen-preview");
    const imagenActualTexto = document.querySelector("#imagen-actual-texto");

    let idmarcaSeleccionada = 0;
    const defaultImage = "/assets/images/vehiculos/model-cars.jpg";


    const RUTAS = {
      marcas: {
        store: '/marcas/store',
        show: '/marcas/show',
        update: '/marcas/update',
        destroy: '/marcas/destroy'
      },
      modelos: {
        getAll: '/modelos/getall',
        store: '/modelos/store',
        show: '/modelos/show',
        update: '/modelos/update',
        destroy: '/modelos/destroy'
      },
      tipos: {
        getAll: '/tipos/getall'
      }
    };


    cargarTipoVehiculos();
    iniciarEventListeners();




    async function cargarTipoVehiculos() {
      try {
        const req = await fetch(RUTAS.tipos.getAll);
        const res = await req.json();
        if (res.success && res.data.length > 0) {
          tipoVehiculoSelect.innerHTML = '<option value="">Seleccione</option>';
          res.data.forEach(tipo => {
            tipoVehiculoSelect.innerHTML += `<option value="${tipo.idtipovehiculo}">${tipo.tipovehiculo}</option>`;
          });
        } else {
          tipoVehiculoSelect.innerHTML = '<option value="">No se encontraron tipos</option>';
        }
      } catch (error) {
        console.error("Error cargando tipos de vehículo:", error);
        tipoVehiculoSelect.innerHTML = '<option value="">Error al cargar</option>';
      }
    }





    function iniciarEventListeners() {

      // Botón "Agregar Marca"
      lnkAgregarMarca.addEventListener("click", (e) => {
        e.preventDefault();
        abrirModalMarca('create');
      });

      // Botón "Agregar Modelo"
      lnkAgregarModelo.addEventListener("click", (e) => {
        e.preventDefault();
        if (idmarcaSeleccionada > 0) {
          abrirModalModelo('create');
        } else {
          showToast("Por favor, seleccione una marca primero.", "WARNING");
        }
      });

      // Submit del formulario de Marcas (Crear/Editar)
      formularioMarcas.addEventListener('submit', guardarMarca);

      // Submit del formulario de Modelos (Crear/Editar)
      formularioModelos.addEventListener('submit', guardarModelo);

      // Eventos de Modales (Resetear al cerrar)
      document.getElementById("modal-marcas").addEventListener('hidden.bs.modal', () => {
        formularioMarcas.reset();
        inputIdMarca.value = "0";
      });

      document.getElementById("modal-modelos").addEventListener('hidden.bs.modal', () => {
        formularioModelos.reset();
        inputIdModelo.value = "0";
        inputIdMarcaModelo.value = "0";
        imagenPreview.src = defaultImage;
        imagenActualTexto.textContent = "";
      });

      // Preview de imagen al seleccionarla
      inputImagen.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            imagenPreview.src = e.target.result;
          }
          reader.readAsDataURL(file);
        } else {
          imagenPreview.src = defaultImage;
        }
      });



      // Clicks en la tabla de MARCAS (Seleccionar, Editar, Eliminar)
      tablaMarcasBody.addEventListener("click", async (e) => {
        const fila = e.target.closest('tr.marca-fila');
        const btnEditar = e.target.closest('.btn-editar-marca');
        const btnEliminar = e.target.closest('.btn-eliminar-marca');

        if (btnEditar) {
          e.stopPropagation();
          const id = btnEditar.dataset.idmarca;
          abrirModalMarca('edit', id);
          return;
        }

        if (btnEliminar) {
          e.stopPropagation();
          const id = btnEliminar.dataset.idmarca;
          eliminarMarca(id);
          return;
        }

        if (fila) {
          // Resaltar fila seleccionada
          document.querySelectorAll('tr.marca-fila.table-active').forEach(row => row.classList.remove('table-active'));
          fila.classList.add('table-active');

          // Cargar modelos
          idmarcaSeleccionada = parseInt(fila.dataset.idmarca);
          const nombreMarca = fila.dataset.nombre;
          marcaActivaTitulo.textContent = nombreMarca.toUpperCase();
          modalModelosTitulo.textContent = `MODELO PARA ${nombreMarca.toUpperCase()}`;
          lnkAgregarModelo.classList.remove('d-none'); // Mostrar botón "Agregar Modelo"
          await cargarModelos(idmarcaSeleccionada);
        }
      });

      // Clicks en la tabla de MODELOS (Editar, Eliminar)
      tablaModelosBody.addEventListener("click", async (e) => {
        const btnEditar = e.target.closest('.btn-editar-modelo');
        const btnEliminar = e.target.closest('.btn-eliminar-modelo');

        if (btnEditar) {
          e.stopPropagation();
          const id = btnEditar.dataset.idmodelo;
          abrirModalModelo('edit', id);
          return;
        }

        if (btnEliminar) {
          e.stopPropagation();
          const id = btnEliminar.dataset.idmodelo;
          eliminarModelo(id);
          return;
        }
      });
    }



    /**
     * Abre el modal de marcas para crear o editar.
     */
    async function abrirModalMarca(modo, id = null) {
      formularioMarcas.reset();
      inputIdMarca.value = "0";

      if (modo === 'create') {
        document.getElementById("modal-marcas-titulo").textContent = "Nueva Marca";
        modalMarca.show();
      } else if (modo === 'edit' && id) {
        document.getElementById("modal-marcas-titulo").textContent = "Editar Marca";
        try {

          const req = await fetch(`${RUTAS.marcas.show}?id=${id}`);
          const res = await req.json();
          if (res.success && res.data) {
            inputIdMarca.value = res.data.idmarca;
            inputMarca.value = res.data.marca;
            modalMarca.show();
          } else {
            showToast(res.message || "No se pudo cargar la marca.", "ERROR");
          }
        } catch (error) {
          console.error(error);
          showToast("Error al cargar datos de la marca.", "ERROR");
        }
      }
    }

    /**
     * Envía el formulario de marcas (Crear o Actualizar).
     */
    async function guardarMarca(e) {
      e.preventDefault();
      const id = inputIdMarca.value;
      const esEdicion = id > 0;
      const url = esEdicion ? RUTAS.marcas.update : RUTAS.marcas.store;

      const formData = new FormData(formularioMarcas);

      if (!await ask(esEdicion ? '¿Actualizar marca?' : '¿Registrar marca?')) return;

      try {
        const req = await fetch(url, {
          method: 'POST',
          body: formData
        });
        const res = await req.json();

        if (res.success) {
          showToast(res.message, 'SUCCESS');
          modalMarca.hide();

          if (esEdicion) {
            // Actualizar la fila existente
            const fila = tablaMarcasBody.querySelector(`tr[data-idmarca="${id}"]`);
            if (fila) {
              fila.dataset.nombre = res.data.marca; // Actualizar data-attribute
              fila.children[0].textContent = res.data.marca; // Actualizar celda nombre

            }
          } else {
            // Agregar nueva fila
            agregarFilaMarca(res.data);
          }
        } else {
          showToast(res.message, res.type || 'ERROR');
        }
      } catch (error) {
        console.error(error);
        showToast('Error al procesar la solicitud.', 'ERROR');
      }
    }

    async function eliminarMarca(id) {
      if (!await ask('¿Eliminar esta marca? Se eliminarán todos sus modelos.', 'Eliminar Marca', 'WARNING')) return;

      const formData = new FormData();
      formData.append('idmarca', id);

      try {
        const req = await fetch(RUTAS.marcas.destroy, {
          method: 'POST',
          body: formData
        });
        const res = await req.json();

        if (res.success) {
          showToast(res.message, 'SUCCESS');
          const fila = tablaMarcasBody.querySelector(`tr[data-idmarca="${id}"]`);
          if (fila) {
            fila.remove();
          }
          // Si la marca eliminada era la seleccionada, limpiar la tabla de modelos
          if (idmarcaSeleccionada === parseInt(id)) {
            idmarcaSeleccionada = 0;
            marcaActivaTitulo.textContent = "SIN ESPECIFICAR";
            lnkAgregarModelo.classList.add('d-none');
            tablaModelosBody.innerHTML = `<tr id="fila-no-modelos"><td colspan="5" class="text-center">Seleccione una marca para continuar</td></tr>`;
          }
          // Verificar si la tabla de marcas quedó vacía
          if (tablaMarcasBody.children.length === 0) {
            tablaMarcasBody.innerHTML = `<tr id="fila-no-marcas"><td colspan="3" class="text-center">No hay marcas disponibles</td></tr>`;
          }
        } else {
          showToast(res.message, res.type || 'ERROR');
        }
      } catch (error) {
        console.error(error);
        showToast('Error al procesar la solicitud.', 'ERROR');
      }
    }


    function agregarFilaMarca(marca) {
      // Si la tabla estaba vacía, quita la fila de "No hay marcas"
      const filaVacia = document.getElementById('fila-no-marcas');
      if (filaVacia) {
        filaVacia.remove();
      }

      const newRow = document.createElement('tr');
      newRow.classList.add('marca-fila');
      newRow.dataset.idmarca = marca.idmarca;
      newRow.dataset.nombre = marca.marca;
      newRow.innerHTML = `
                <td>${marca.marca}</td>
                <td class="text-center" data-conteo="modelos">${marca.modelos}</td>
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-outline-primary btn-editar-marca" data-idmarca="${marca.idmarca}" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-marca" title="Eliminar" data-idmarca="${marca.idmarca}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
      tablaMarcasBody.appendChild(newRow);
    }



    /**
     * Carga los modelos de la marca seleccionada en la tabla de modelos.
     */
    async function cargarModelos(idmarca) {
      tablaModelosBody.innerHTML = `<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
      try {
        const req = await fetch(`${RUTAS.modelos.getAll}?idmarca=${idmarca}`);
        const res = await req.json();

        tablaModelosBody.innerHTML = '';
        if (res.success && res.data.length > 0) {
          let i = 1;
          res.data.forEach(modelo => {
            agregarFilaModelo(modelo, i);
            i++;
          });
        } else {
          tablaModelosBody.innerHTML = `<tr id="fila-no-modelos"><td colspan="5" class="text-center">No hay modelos registrados para esta marca</td></tr>`;
        }
      } catch (error) {
        console.error("Error cargando modelos:", error);
        tablaModelosBody.innerHTML = `<tr><td colspan="5" class="text-center">Error al cargar modelos</td></tr>`;
      }
    }

    /**
     * Abre el modal de modelos para crear o editar.
     */
    async function abrirModalModelo(modo, id = null) {
      formularioModelos.reset();
      inputIdModelo.value = "0";
      inputIdMarcaModelo.value = idmarcaSeleccionada; // Asignar la marca activa
      imagenPreview.src = defaultImage;
      imagenActualTexto.textContent = "";

      if (modo === 'create') {
        modalModelo.show();
      } else if (modo === 'edit' && id) {
        try {

          const req = await fetch(`${RUTAS.modelos.show}?id=${id}`);
          const res = await req.json();
          if (res.success && res.data) {
            inputIdModelo.value = res.data.idmodelo;
            inputIdMarcaModelo.value = res.data.idmarca;
            tipoVehiculoSelect.value = res.data.idtipovehiculo;
            inputModelo.value = res.data.modelo;
            inputAnio.value = res.data.anio;

            // Manejo de la imagen
            if (res.data.imagenreferencial) {
              imagenPreview.src = `/assets/images/vehiculos/${res.data.imagenreferencial}`;
              imagenActualTexto.textContent = `Imagen actual: ${res.data.imagenreferencial}. Seleccione una nueva para reemplazarla.`;
            } else {
              imagenPreview.src = defaultImage;
              imagenActualTexto.textContent = "No hay imagen referencial.";
            }

            modalModelo.show();
          } else {
            showToast(res.message || "No se pudo cargar el modelo.", "ERROR");
          }
        } catch (error) {
          console.error(error);
          showToast("Error al cargar datos del modelo.", "ERROR");
        }
      }
    }



    /**
     * Envía el formulario de modelos (Crear o Actualizar).
     */
    async function guardarModelo(e) {
      e.preventDefault();
      const id = inputIdModelo.value;
      const esEdicion = id > 0;
      const url = esEdicion ? RUTAS.modelos.update : RUTAS.modelos.store;

      const formData = new FormData(formularioModelos);

      if (!await ask(esEdicion ? '¿Actualizar modelo?' : '¿Registrar modelo?')) return;

      try {
        const req = await fetch(url, {
          method: 'POST',
          body: formData
        });
        const res = await req.json();

        if (res.success) {
          showToast(res.message, 'SUCCESS');
          modalModelo.hide();
          await cargarModelos(idmarcaSeleccionada);

          // Actualizar el contador de modelos en la tabla de marcas
          actualizarConteoMarca(idmarcaSeleccionada, res.nuevoConteo);

        } else {
          showToast(res.message, res.type || 'ERROR');
        }
      } catch (error) {
        console.error(error);
        showToast('Error al procesar la solicitud.', 'ERROR');
      }
    }


    async function eliminarModelo(id) {
      if (!await ask('¿Eliminar este modelo?', 'Eliminar Modelo', 'warning')) return;

      const formData = new FormData();
      formData.append('idmodelo', id);
      formData.append('idmarca', idmarcaSeleccionada); // Enviar para saber qué conteo devolver

      try {
        const req = await fetch(RUTAS.modelos.destroy, {
          method: 'POST',
          body: formData
        });
        const res = await req.json();

        if (res.success) {
          showToast(res.message, 'SUCCESS');
          const fila = tablaModelosBody.querySelector(`tr[data-idmodelo="${id}"]`);
          if (fila) {
            fila.remove();
          }
          // Verificar si la tabla de modelos quedó vacía
          if (tablaModelosBody.children.length === 0) {
            tablaModelosBody.innerHTML = `<tr id="fila-no-modelos"><td colspan="5" class="text-center">No hay modelos registrados para esta marca</td></tr>`;
          }

          // Actualizar el contador de modelos en la tabla de marcas
          actualizarConteoMarca(idmarcaSeleccionada, res.nuevoConteo);

        } else {
          showToast(res.message, res.type || 'ERROR');
        }
      } catch (error) {
        console.error(error);
        showToast('Error al procesar la solicitud.', 'ERROR');
      }
    }

    /**
     * Añade una fila de modelo a la tabla de modelos.
     */
    function agregarFilaModelo(modelo, indice) {
      // Si la tabla estaba vacía, quita la fila de "No hay modelos"
      const filaVacia = document.getElementById('fila-no-modelos');
      if (filaVacia) {
        filaVacia.remove();
      }

      const newRow = document.createElement('tr');
      newRow.dataset.idmodelo = modelo.idmodelo;
      newRow.innerHTML = `
                <td>${indice}</td>
                <td>${modelo.tipovehiculo}</td>
                <td>${modelo.modelo}</td>
                <td>${modelo.anio}</td>
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-outline-primary btn-editar-modelo" data-idmodelo="${modelo.idmodelo}" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-modelo" title="Eliminar" data-idmodelo="${modelo.idmodelo}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
      tablaModelosBody.appendChild(newRow);
    }



    /**
     * Actualiza el contador de modelos en la fila de la marca correspondiente.
     * @param {number} idmarca El ID de la marca a actualizar.
     * @param {number} nuevoConteo El nuevo número total de modelos.
     */
    function actualizarConteoMarca(idmarca, nuevoConteo) {
      const filaMarca = tablaMarcasBody.querySelector(`tr[data-idmarca="${idmarca}"]`);
      if (filaMarca) {
        const celdaConteo = filaMarca.querySelector('td[data-conteo="modelos"]');
        if (celdaConteo) {
          celdaConteo.textContent = nuevoConteo;
        }
      }
    }

  });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>