<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

<div class="container-fluid">

  <?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <?= htmlspecialchars($_SESSION['success_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
      <?= htmlspecialchars($_SESSION['error_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <div class="alert alert-info mt-2" role="alert">
    <div class="row">
      <div class="col-md-6 d-flex align-items-center justify-content-start">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Listar</li>
          </ol>
        </nav>
      </div>
      <div class="col-md-6 text-end">
        <a href="/usuarios/create" class="btn btn-outline-primary btn-sm">Registrar</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <!-- BUSCADOR -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="input-group w-100">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="busqueda-global" class="form-control" placeholder="Buscar...">
        </div>
      </div>

      <!-- TABULATOR -->
      <div id="tabla-usuarios-tabulator" class="table-responsive">
        <div class="text-center py-5" id="spinner-usuarios">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="mt-2">Cargando usuarios...</p>
        </div>
      </div>

      <!-- MENSAJE VACÍO -->
      <div id="mensaje-vacio" class="alert alert-warning d-none mt-1">No hay usuarios para mostrar.</div>
    </div>
  </div>

</div>

<!-- MODAL CAMBIO DE PASSWORD -->
<div class="modal fade" id="modalCambiarClave" tabindex="-1" aria-labelledby="modalCambiarClaveLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-yonda">
        <h5 class="modal-title" id="modalCambiarClaveLabel">Cambiar contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formCambiarClave" action="/usuarios/changePassword" method="POST" autocomplete="off">
          <input type="hidden" id="cc-idcolaborador" name="idcolaborador">

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="cc-usuario" name="usuario" readonly>
            <label for="cc-usuario">Usuario</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="cc-password1" name="password1"
              pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$"
              title="Mínimo 8 caracteres: incluye letra, número y símbolo" required>
            <label for="cc-password1">Nueva contraseña</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="cc-password2" name="password2" required>
            <label for="cc-password2">Repetir contraseña</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm btn-primary" id="btnAceptarCambiarClave">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
  let modalCambiarClave;

  // Funciones auxiliares
  function escapeHtml(t) {
    return t ? t.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])) : '';
  }

  function mostrarToast(mensaje, tipo = 'info') {
    const nt = document.createElement('div');
    nt.className = `alert alert-${tipo === 'success' ? 'success' : tipo === 'warning' ? 'warning' : 'danger'} position-fixed`;
    nt.style.cssText = `top:20px; right:20px; z-index:9999; min-width:260px;`;
    nt.innerHTML = `<div class="d-flex align-items-center"><i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i><div>${mensaje}</div><button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button></div>`;
    document.body.appendChild(nt);
    setTimeout(() => nt.remove(), 5000);
  }

  // Preparar datos para Tabulator
  const usuariosData = <?= json_encode($Usuarios) ?>;

  // Inicializar vista
  document.addEventListener('DOMContentLoaded', () => {
    const spinner = document.getElementById('spinner-usuarios');
    const mensajeVacio = document.getElementById('mensaje-vacio');

    modalCambiarClave = new bootstrap.Modal(document.getElementById('modalCambiarClave'));

    try {
      if (spinner) spinner.remove();

      if (!Array.isArray(usuariosData) || usuariosData.length === 0) {
        mensajeVacio.classList.remove('d-none');
        return;
      }

      // TABULATOR
      const tabla = new Tabulator("#tabla-usuarios-tabulator", {
        data: usuariosData,
        layout: "fitDataStretch",
        pagination: "local",
        paginationSize: 15,
        paginationSizeSelector: [10, 15, 25, 50],
        columns: [
          { title: "#", field: "idcolaborador", width: 70, hozAlign: "center" },
          { title: "Apellidos", field: "apellidos", width: 220, widthGrow: 3 },
          { title: "Nombres", field: "nombres", width: 220, widthGrow: 3 },
          { title: "Área", field: "area", width: 130, widthGrow: 2 },
          { title: "Cargo", field: "cargo", width: 200, widthGrow: 2 },
          { title: "Fecha Inicio", field: "fecha_inicio", width: 150, widthGrow: 2 },
          { title: "Fecha Fin", field: "fecha_fin", width: 150, widthGrow: 2 },
          {
            title: "Usuario",
            field: "usuario",
            width: 160,
            widthGrow: 2,
            formatter: (cell) => {
              const data = cell.getRow().getData();
              const usuario = escapeHtml(cell.getValue());
              const restriccion = data.restriccionhoraria === 'S'
                ? '<span class="ms-1" title="Restricción horaria">🕜</span>'
                : '';
              return usuario + restriccion;
            }
          },
          {
            title: "Acciones",
            headerSort: false,
            hozAlign: "center",
            widthGrow: 2,
            formatter: (cell) => {
              const data = cell.getRow().getData();
              const isRestr = data.restriccionhoraria === 'S';
              return `
                <div class="acciones-btns gap-2 mt-1">
                  <a href="/usuarios/edit/${data.idcolaborador}" class="btn btn-sm btn-outline-primary" title="Editar">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                  <button class="btn btn-sm btn-outline-warning btn-cambiar-clave" title="Cambiar contraseña"
                    data-idcolab="${data.idcolaborador}" data-usuario="${escapeHtml(data.usuario)}">
                    <i class="fa-solid fa-key"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger btn-borrar" title="Eliminar"
                    data-idcolab="${data.idcolaborador}">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  <button class="btn btn-sm ${isRestr ? 'btn-outline-secondary' : 'btn-outline-info'} btn-restriccion"
                    title="${isRestr ? 'Quitar restricción horaria' : 'Poner restricción horaria'}"
                    data-idcolab="${data.idcolaborador}" data-restriccion="${isRestr ? 'S' : 'N'}">
                    <i class="fa-${isRestr ? 'solid' : 'regular'} fa-clock"></i>
                  </button>
                </div>
              `;
            }
          }
        ]
      });

      // Filtro de búsqueda
      const searchInput = document.getElementById("busqueda-global");
      if (searchInput) {
        searchInput.addEventListener("keyup", function (e) {
          const val = e.target.value.trim();
          if (!val) {
            tabla.clearFilter();
          } else {
            tabla.setFilter([
              [
                { field: "apellidos", type: "like", value: val },
                { field: "nombres", type: "like", value: val },
                { field: "area", type: "like", value: val },
                { field: "cargo", type: "like", value: val },
                { field: "usuario", type: "like", value: val }
              ]
            ]);
          }
        });
      }

      // Eventos dentro de la tabla
      document.getElementById('tabla-usuarios-tabulator').addEventListener('click', async (e) => {
        const btnClave = e.target.closest('.btn-cambiar-clave');
        const btnBorrar = e.target.closest('.btn-borrar');
        const btnRestriccion = e.target.closest('.btn-restriccion');

        if (btnClave) {
          const idColab = btnClave.dataset.idcolab;
          const usuario = btnClave.dataset.usuario;
          document.getElementById('cc-idcolaborador').value = idColab;
          document.getElementById('cc-usuario').value = usuario;
          modalCambiarClave.show();
        }

        if (btnBorrar) {
          e.preventDefault();
          const id = btnBorrar.dataset.idcolab;

          let confirmado;
          if (typeof ask === 'function') {
            confirmado = await ask('¿Desea confirmar la eliminación de este usuario?', '¿Eliminar usuario?');
          } else {
            confirmado = confirm('¿Desea confirmar la eliminación de este usuario?');
          }

          if (!confirmado) return;

          btnBorrar.disabled = true;
          btnBorrar.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/usuarios/disabled/${id}`;
          document.body.appendChild(form);
          form.submit();
        }

        if (btnRestriccion) {
          e.preventDefault();
          const id = btnRestriccion.dataset.idcolab;
          const isActive = btnRestriccion.dataset.restriccion === 'S';

          const mensaje = isActive
            ? '¿Desea confirmar quitar la restricción horaria de este usuario?'
            : '¿Desea confirmar poner restricción horaria a este usuario?';
          const titulo = isActive ? '¿Quitar restricción?' : '¿Poner restricción?';

          let confirmado;
          if (typeof ask === 'function') {
            confirmado = await ask(mensaje, titulo);
          } else {
            confirmado = confirm(mensaje);
          }

          if (!confirmado) return;

          btnRestriccion.disabled = true;
          btnRestriccion.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '/usuarios/toggleRestriccion';
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'idcolaborador';
          input.value = id;
          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
      });

      // Cambiar contraseña
      document.getElementById('btnAceptarCambiarClave').addEventListener('click', async (e) => {
        e.preventDefault();

        let confirmado;
        if (typeof ask === 'function') {
          confirmado = await ask('¿Desea confirmar el cambio de contraseña?', '¿Cambiar contraseña?');
        } else {
          confirmado = confirm('¿Desea confirmar el cambio de contraseña?');
        }

        if (!confirmado) return;

        const form = document.getElementById('formCambiarClave');
        const formData = new FormData(form);

        const btnAceptar = e.target;
        btnAceptar.disabled = true;
        btnAceptar.innerHTML = 'Cambiando...';

        try {
          const res = await fetch('/api/usuarios/changePassword', {
            method: 'POST',
            body: formData
          });

          const json = await res.json();
          modalCambiarClave.hide();
          form.reset();
          window.location.reload();
        } catch (err) {
          console.error(err);
          alert('Error de conexión.');
          btnAceptar.disabled = false;
          btnAceptar.innerHTML = 'Guardar';
        }
      });

    } catch (err) {
      console.error(err);
      if (spinner) spinner.remove();
      mensajeVacio.classList.remove('d-none');
      mostrarToast('Error al cargar usuarios', 'danger');
    }
  });
</script>