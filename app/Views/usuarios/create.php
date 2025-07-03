<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

  <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex">
        <nav
          style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
          aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Usuario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <a href="/usuarios" class="">[ Mostrar lista ]</a>
      </div>
    </div>
  </div>

  <!-- Campos -->
  <div class="mb-2">
    <form action="/usuarios/store" id="formRegisterFull" method="POST" autocomplete="off">
      <input type="hidden" name="idpersona" id="idpersona" value="">
      <!-- PASO 1 - DATOS DE LA EMPRESA -->
      <div class="card mb-4">
        <div class="card-header bg-info">
          <strong>Paso 1:</strong> <span class="fst-italic">
            Datos de la persona
          </span>
        </div>
        <div class="card-body">

          <div class="row g-2">
            <!-- CAMPO DE DNI -->
            <div class="col-md-2 mb-2">
              <div class="input-group">
                <!-- form-floating con flex-grow para que ocupe todo el espacio -->
                <div class="form-floating flex-grow-1">
                  <input type="text" id="dni" name="nrodoc" class="form-control text-center" autofocus>
                  <label for="dni">DNI</label>
                </div>
                <button type="button" class="btn btn-primary" title="Administrar" data-bs-toggle="modal"
                  data-bs-target="#modalRegistrarPersona">
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
              </div>
            </div>

            <!-- CAMPO DE APELLIDOS Y NOMBRES -->
            <div class="col-md-5">
              <div class="form-floating">
                <input type="text" id="apellidos" name="apellidos" class="form-control">
                <label for="form-label">Apellidos</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-floating">
                <input type="text" id="nombres" name="nombres" class="form-control">
                <label for="form-label">Nombres</label>
              </div>
            </div>
          </div>

        </div> <!-- ./card-body -->
      </div><!-- ./card -->

      <!-- PASO 2 - AREA Y CARGOS -->
      <div class="card mb-4">
        <div class="card-header bg-info">
          <strong>Paso 2:</strong> <span class="fst-italic">
            Datos del contrato
          </span>
        </div>
        <div class="card-body">
          <div class="row g-2">
            <!-- Campo de área -->
            <div class="col-md-3 mb-2">
              <div class="form-floating">
                <select name="area" id="area" class="form-select" required>
                  <option value="">Seleccione</option>
                  <?php foreach ($areas as $a): ?>
                    <option value="<?= $a['idarea'] ?>"><?= htmlspecialchars($a['area']) ?></option>
                  <?php endforeach; ?>
                </select>
                <label for="area">Áreas</label>
              </div>
            </div>
            <!-- Campo de cargos -->
            <div class="col-md-4 mb-2">
              <div class="form-floating">
                <select name="idcargo" id="cargo" class="form-select" required disabled>
                  <option value="">Seleccione un área primero</option>
                </select>
                <label for="cargo">Cargo</label>
              </div>
            </div>
            <!-- Fecha inicio -->
            <div class="col-md-2 mb-2">
              <div class="form-floating">
                <input type="date" class="form-control" id="fecha-inicio" name="fecha_inicio">
                <label for="fecha-inicio">Fecha Inicio</label>
              </div>
            </div>
            <!-- Fecha fin -->
            <div class="col-md-2 mb-2">
              <div class="form-floating">
                <input type="date" class="form-control" id="fecha-fin" name="fecha_fin">
                <label for="fecha-fin">Fecha Fin</label>
              </div>
            </div>
            <!-- Checkbox al final -->
            <div class="col-md-1 mb-2 d-flex align-items-center">
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="sin-fecha-fin" name="sin_fecha_fin">
                <label class="form-check-label" for="sin-fecha-fin">
                  Indeterminado
                </label>
              </div>
            </div>

          </div>
        </div> <!-- ./card-body -->
      </div><!-- ./card -->

      <!-- PASO 3 - PARA CUENTA -->
      <div class="card mb-2">
        <div class="card-header bg-info">
          <strong>Paso 3:</strong> <span class="fst-italic">
            Crear cuenta
          </span>
        </div>
        <div class="card-body">
          <div class="row g-2">

            <!-- CAMPO DE USER AND PASSWORD -->
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" id="usernick" name="usuario" required>
                <label for="form-label">Nombre de usuario</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="password" class="form-control" id="password1" name="password1"
                  pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$"
                  title="Mínimo 8 caracteres: al menos letra, número y símbolo" required>
                <label for="form-label">Contraseña 1</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="password" class="form-control" id="password2" name="password2" required>
                <label for="form-label">Contraseña 2</label>
              </div>
            </div>
          </div>


        </div> <!-- ./card-body -->
      </div><!-- ./card -->

      <div class="card">
        <div class="card-footer text-end">
          <button type="reset" id="btn-cancelar-registro" class="btn btn-sm btn-outline-secondary">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm btnGuardarUsuario">Registrar</button>
        </div>
      </div>
    </form>
  </div>

</div>

<!-- 2) Estructura del modal -->
<div class="modal fade" id="modalRegistrarPersona" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
  aria-labelledby="modalRegistrarPersonaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-yonda">
        <h5 class="modal-title" id="modalRegistrarPersonaLabel">Datos de la Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form action="/usuarios/storePersona" id="formRegistrarPersona" method="POST" autocomplete="off">
          <div class="row g-1">
            <!-- Tipo y número de doc -->
            <div class="col-md-2 form-floating">
              <select class="form-select" id="modal-tipodoc" name="tipodoc">
                <option value="DNI">DNI</option>
                <option value="CEX">CEX</option>
                <option value="PAS">PAS</option>
              </select>
              <label for="modal-tipodoc">Tipo Doc</label>
            </div>
            <div class="col-md-2 form-floating">
              <input type="text" class="form-control" id="modal-nrodoc" name="nrodoc" required>
              <label for="modal-nrodoc">N° Documento</label>
            </div>

            <!-- Apellidos / Nombres -->
            <div class="col-md-4 form-floating">
              <input type="text" class="form-control" id="modal-apellidos" name="apellidos" required>
              <label for="modal-apellidos">Apellidos</label>
            </div>
            <div class="col-md-4 form-floating">
              <input type="text" class="form-control" id="modal-nombres" name="nombres" required>
              <label for="modal-nombres">Nombres</label>
            </div>

            <!-- Género / Fecha Nac. / Estado Civil -->
            <div class="col-md-2 form-floating">
              <select class="form-select" id="modal-genero" name="genero">
                <option value="M">M</option>
                <option value="F">F</option>
              </select>
              <label for="modal-genero">Género</label>
            </div>
            <div class="col-md-4 form-floating">
              <input type="date" class="form-control" id="modal-fechanac" name="fechanac">
              <label for="modal-fechanac">F. Nacimiento</label>
            </div>
            <div class="col-md-6 form-floating">
              <select class="form-select" id="modal-estadocivil" name="estadocivil">
                <option value="">--</option>
                <option value="SOL">Soltero</option>
                <option value="CAS">Casado</option>
                <option value="VDO">Viudo</option>
                <option value="DVC">Divorciado</option>
                <option value="CNV">Conviviente</option>
              </select>
              <label for="modal-estadocivil">Estado Civil</label>
            </div>

            <!-- Email / Distrito -->
            <div class="col-md-6 form-floating">
              <input type="email" class="form-control" id="modal-email" name="email">
              <label for="modal-email">Email</label>
            </div>
            <div class="col-md-6 form-floating">
              <select class="form-select" id="modal-iddistrito" name="iddistrito" disabled>
                <option value="">Cargando distritos…</option>
              </select>
              <label for="modal-iddistrito">Distrito</label>
            </div>

            <!-- Dirección / Referencia -->
            <div class="col-md-8 form-floating">
              <input type="text" class="form-control" id="modal-direccion" name="direccion">
              <label for="modal-direccion">Dirección</label>
            </div>
            <div class="col-md-4 form-floating">
              <input type="text" class="form-control" id="modal-referencia" name="referencia">
              <label for="modal-referencia">Referencia</label>
            </div>

            <!-- Teléfonos -->
            <div class="col-md-6 form-floating">
              <input type="text" class="form-control" id="modal-telprimario" name="telprimario" maxlength="9" required>
              <label for="modal-telprimario">Teléfono Primario</label>
            </div>
            <div class="col-md-6 form-floating">
              <input type="text" class="form-control" id="modal-telalternativo" name="telalternativo" maxlength="9">
              <label for="modal-telalternativo">Teléfono Alternativo</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="button" class="btn btn-sm btn-primary" id="btnGuardarPersona">
          Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const selArea = document.getElementById('area');
    const selCargo = document.getElementById('cargo');

    selArea.addEventListener('change', () => {
      const idarea = selArea.value;
      if (!idarea) {
        selCargo.innerHTML = '<option value="">Seleccione un área primero</option>';
        selCargo.disabled = true;
        return;
      }
      fetch(`/usuarios/cargos?idarea=${idarea}`)
        .then(res => res.json())
        .then(data => {
          selCargo.innerHTML = '<option value="">Seleccione cargo</option>';
          data.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.idcargo;
            opt.text = c.cargo;
            selCargo.appendChild(opt);
          });
          selCargo.disabled = false;
        })
        .catch(err => {
          console.error(err);
          selCargo.innerHTML = '<option value="">Error cargando cargos</option>';
          selCargo.disabled = true;
        });
    });

    const selDist = document.getElementById('modal-iddistrito');
    const modal = document.getElementById('modalRegistrarPersona');

    modal.addEventListener('show.bs.modal', () => {
      selDist.innerHTML = '<option value="">Cargando distritos…</option>';
      selDist.disabled = true;

      fetch('/ubigeo/distritos/all')
        .then(res => res.json())
        .then(data => {
          selDist.innerHTML = '<option value="">Seleccione Distrito</option>';
          data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.iddistrito;   // clave que devuelve tu JSON
            opt.text = d.distrito;     // nombre del distrito
            selDist.appendChild(opt);
          });
          selDist.disabled = false;
        })
        .catch(err => {
          console.error(err);
          selDist.innerHTML = '<option value="">Error al cargar distritos</option>';
        });
    });

    //registrar persona en el modal
    const btnGuardar = document.getElementById('btnGuardarPersona');
    const formModal = document.getElementById('formRegistrarPersona');

    btnGuardar.addEventListener('click', () => {
      // Serializar formData
      const formData = new FormData(formModal);
      for (const [key, val] of formData.entries()) {
        console.log(`formData: ${key} = ${val}`);
      }
      // Enviar por fetch
      fetch(formModal.action, {
        method: 'POST',
        body: formData,
        headers: {
          // Opcional: para que el controller reconozca AJAX
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            // Cerrar modal
            const modalEl = document.getElementById('modalRegistrarPersona');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            //rellenar los campos principales con los datos creados
            document.getElementById('dni').value = json.data.nrodoc;
            document.getElementById('apellidos').value = json.data.apellidos;
            document.getElementById('nombres').value = json.data.nombres;
            document.getElementById('idpersona').value = json.data.idpersona;

          } else {
            // Mostrar error dentro del modal
            alert('Error: ' + json.error);
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error al conectar con el servidor');
        });
    });
    const formRegister = document.getElementById('formRegisterFull');

    formRegister.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(formRegister);
      fetch('/usuarios/store', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            // ahora sí usamos la URL que vino del controlador
            window.location.href = json.redirect;
          } else {
            alert('Error: ' + json.error);
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error de conexión con el servidor');
        });
    });

    const inputDNI = document.getElementById('dni');
    const modalEl = document.getElementById('modalRegistrarPersona');
    const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

    inputDNI.addEventListener('blur', () => {
      const dni = inputDNI.value.trim();
      if (!dni) return;

      fetch(`/usuarios/searchByDNI?nrodoc=${encodeURIComponent(dni)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            // Persona existe: rellenamos y guardamos idpersona
            document.getElementById('apellidos').value = json.data.apellidos;
            document.getElementById('nombres').value = json.data.nombres;
            document.getElementById('idpersona').value = json.data.idpersona;
          } else {
            // NO existe: abrimos modal para crear persona
            // Limpiamos campos principales
            document.getElementById('apellidos').value = '';
            document.getElementById('nombres').value = '';
            document.getElementById('idpersona').value = '';

            // Prefill del modal: pongo el DNI y tipo doc
            document.getElementById('modal-nrodoc').value = dni;
            // Opcional: si quieres preseleccionar tipo doc a DNI
            document.getElementById('modal-tipodoc').value = 'DNI';

            // Abrir modal
            bsModal.show();
          }
        })
        .catch(err => console.error('Error buscando persona:', err));
    });
    const chkIndeterminado = document.getElementById('sin-fecha-fin');
    const inputFechaFin = document.getElementById('fecha-fin');

    if (chkIndeterminado.checked) {
      inputFechaFin.readOnly = true;
      inputFechaFin.value = '';
      inputFechaFin.placeholder = 'Indeterminado';
    }

    chkIndeterminado.addEventListener('change', () => {
      if (chkIndeterminado.checked) {
        // Marqué “Indeterminado”: lo hago solo lectura, limpio valor
        inputFechaFin.readOnly = true;
        inputFechaFin.value = '';
        inputFechaFin.placeholder = 'Indeterminado';
      } else {
        // Desmarqué: quito solo lectura para poder elegir fecha
        inputFechaFin.readOnly = false;
        inputFechaFin.placeholder = '';
      }
    });
  });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>