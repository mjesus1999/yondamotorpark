<?php include __DIR__ . '/../layout/header.php'; ?>
<script>
	const rucInicial = "<?= isset($ruc) ? $ruc : '' ?>";
</script>


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
									<input type="text" id="ruc" class="form-control text-center" pattern="[0-9]+" title="Solo se permiten números" maxlength="11" minlength="11" placeholder="RUC" autofocus required>
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
					</div>
					<!-- ./row -->
				</div>
				<!-- ./card-body -->
				<div class="card-footer text-end">
					<button type="reset" id="btn-cancelar-registro" class="btn btn-sm btn-outline-secondary">Cancelar</button>
					<button type="submit" id="btn-registrar-concesionario" class="btn btn-sm btn-primary">Guardar</button>
				</div>
				<!-- ./card-footer -->
			</div>
			<!-- ./card -->
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
							<textarea name="direccion" id="direccion" rows="3" class="form-control" placeholder="Dirección" required></textarea>
						</div>

						<div class="row g-2">
							<div class="col-md-4">
								<div class="form-floating mb-2">
									<input type="text" id="telefono" name="telefono" maxlength="12" minlength="9" pattern="[0-9]+" title="Solo se permiten números" class="form-control" placeholder="Teléfono" required>
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
	// === Variables UI ===
	const ruc = document.querySelector('#ruc');
	const razonSocial = document.querySelector('#razonsocial');
	const nombreComercial = document.querySelector('#nombrecomercial');
	const telefono = document.querySelector('#telefono');
	const direccion = document.querySelector('#direccion');
	const email = document.querySelector('#email');
	const contacto = document.querySelector('#contacto');
	const distrito = document.querySelector('#distrito');
	const tablaTiendas = document.querySelector('#tabla-tiendas tbody');

	const btnBuscar = document.querySelector('#btn-buscar-concesionario');
	const btnRegistrar = document.querySelector('#btn-registrar-concesionario');
	const btnCancelar = document.querySelector('#btn-cancelar-registro');
	const btnModalTiendas = document.querySelector('#btn-modal-tiendas');

	const formConcesionario = document.querySelector('#form-registro-concesionario');
	const formTienda = document.querySelector('#form-registro-tienda');

	const modalTienda = new bootstrap.Modal(document.getElementById("modal-tiendas"));

	let idconcesionario = null;

	const rucInicial = "<?= isset($ruc) ? $ruc : '' ?>";
	if (rucInicial !== '') {
		ruc.value = rucInicial;
		await obtenerConcesionarioByDB();
	}

	// === Eventos ===
	btnBuscar.addEventListener('click', obtenerConcesionarioByDB);
	btnModalTiendas.addEventListener('click', () => {
		if (!idconcesionario) {
			showToast('Primero registrar el concesionario', 'WARNING', 1200);
			ruc.focus();
			return;
		}
		modalTienda.show();
	});

	btnCancelar.addEventListener('click', () => {
		formConcesionario.reset();
		razonSocial.removeAttribute("disabled");
		nombreComercial.removeAttribute("disabled");
		ruc.removeAttribute("disabled");
	});

	formConcesionario.addEventListener('submit', registrarConcesionario);
	formTienda.addEventListener('submit', guardarTienda);

	tablaTiendas.addEventListener('click', handleAccionesTabla);

	// === Funciones ===
		// Buscar los concesionarios en la DB
	async function obtenerConcesionarioByDB() {
		if (ruc.value.length !== 11) return showToast("Se requiere 11 dígitos", "INFO", 1200);
		razonSocial.value = 'Buscando...';

		try {
			const res = await fetch(`/api/concesionarioDB/${ruc.value}`);
			const data = await res.json();

			if (data.length === 0) {
				btnRegistrar.removeAttribute("disabled");
				btnCancelar.removeAttribute("disabled");
				razonSocial.setAttribute("disabled", true);
				ruc.setAttribute("disabled", true);
				await obtenerConcesionarioBySunat();
			} else {
				const concesionario = data[0];
				idconcesionario = concesionario.idconcesionario;

				razonSocial.value = concesionario.razonsocial;
				nombreComercial.value = concesionario.nombrecomercial;

				[btnCancelar, btnRegistrar].forEach(btn => btn.setAttribute("disabled", true));
				[ruc, razonSocial, nombreComercial].forEach(el => el.setAttribute("disabled", true));

				await obtenerTiendasByConcesionario();
				showToast('Empresa encontrada', 'SUCCESS', 1200);
			}
		} catch (error) {
			console.error(error);
			showToast('Error al buscar concesionario', 'ERROR', 1200);
		}
	}
	// Buscar los Concesionarios en SUNAT
	async function obtenerConcesionarioBySunat() {
		try {
			const res = await fetch(`/api/concesionarioSunat/${ruc.value}`);
			const data = await res.json();

			if (data.razonSocial) {
				razonSocial.value = data.razonSocial;
				nombreComercial.value = '';
				nombreComercial.focus();
				showToast('Empresa encontrada', 'SUCCESS', 1200);
			} else {
				showToast('No existe la empresa', 'WARNING', 1200);
				ruc.value = '';
				razonSocial.value = '';
				[ruc,razonSocial].forEach(el => el.removeAttribute('disabled'));
				ruc.focus();
			}
		} catch (error) {
			console.error(error);
		}
	}
	// Registrar el concesionario
	async function registrarConcesionario(event) {
		event.preventDefault();
		if (!confirm('¿Desea registrar el concesionario?')) return;

		const form = new FormData();
		form.append('ruc', ruc.value);
		form.append('nombrecomercial', nombreComercial.value);
		form.append('razonsocial', razonSocial.value);

		try {
			const res = await fetch(`/concesionarios/store`, { method: 'POST', body: form });
			const data = await res.json();

			if (data.success) {
				idconcesionario = data.id;
				showToast(data.message, 'SUCCESS', 1200);
				btnRegistrar.setAttribute("disabled", true);
				btnCancelar.setAttribute("disabled", true);
			} else {
				showToast(data.message, 'ERROR', 1200);
			}
		} catch (error) {
			console.error(error);
		}
	}

	async function obtenerTiendasByConcesionario() {
		try {
			const res = await fetch(`/api/tiendasConcesionario/${idconcesionario}`);
			const tiendas = await res.json();

			if (!tiendas.length) {
				tablaTiendas.innerHTML = `<tr><td colspan="7" class="text-center">No hay tiendas registradas</td></tr>`;
				return;
			}

			tablaTiendas.innerHTML = '';
			tiendas.forEach((tienda, i) => {
				tablaTiendas.innerHTML += `
					<tr>
						<td>${i + 1}</td>
						<td>${tienda.ubigeo}</td>
						<td>${tienda.direccion}</td>
						<td>${tienda.telefono}</td>
						<td>${tienda.email}</td>
						<td>${tienda.contacto}</td>
						<td>
							<a href="#" class="btn btn-sm btn-outline-primary edit" data-id="${tienda.idtienda}"><i class="fa fa-pen"></i></a>
							<a href="#" class="btn btn-sm btn-outline-danger delete" data-id="${tienda.idtienda}"><i class="fa fa-trash"></i></a>
						</td>
					</tr>`;
			});
		} catch (error) {
			console.error(error);
		}
	}

	async function mostrarDatosTiendaEnModal(tienda) {
		await getAllDepartamentos();
		document.querySelector("#departamento").value = tienda.iddepartamento;
		await getProvinciasByDepartamento(tienda.iddepartamento);
		document.querySelector("#provincia").value = tienda.idprovincia;
		await getDistritosByProvincia(tienda.idprovincia);
		document.querySelector("#distrito").value = tienda.iddistrito;

		direccion.value = tienda.direccion;
		telefono.value = tienda.telefono;
		email.value = tienda.email;
		contacto.value = tienda.contacto;

		formTienda.dataset.idtienda = tienda.idtienda;
		document.querySelector("#btn-agregar-tienda").textContent = "Actualizar";
		modalTienda.show();
	}

	async function guardarTienda(event) {
		event.preventDefault();

		const idtienda = formTienda.dataset.idtienda;
		const esEdicion = !!idtienda; // Verificar si existe una tienda y convertirlo a un valor booleano.

		if (!confirm(esEdicion ? "¿Desea actualizar la tienda?" : "¿Desea registrar la tienda?")) return;

		const form = new FormData(formTienda);
		form.append('iddistrito', distrito.value);
		form.append('direccion', direccion.value);
		form.append('telefono', telefono.value);
		form.append('email', email.value);
		form.append('contacto', contacto.value);

		if (!esEdicion) form.append('idconcesionario', idconcesionario);

		const url = esEdicion ? `/tiendas/update/${idtienda}` : '/tiendas/store';

		try {
			const res = await fetch(url, { method: 'POST', body: form });
			const data = await res.json();

			if (data.success) {
				showToast(data.message, 'SUCCESS', 1200);
				modalTienda.hide();
				await obtenerTiendasByConcesionario();
				formTienda.reset();
				delete formTienda.dataset.idtienda;
				document.querySelector("#btn-agregar-tienda").textContent = "Guardar";
			} else {
				showToast(data.message, 'WARNING', 1200);
			}
		} catch (error) {
			console.error("Error al guardar tienda:", error);
		}
	}
	// Identificar que acciones se hacen en la tabla, si es edit o delete
	async function handleAccionesTabla(event) {
		const editBtn = event.target.closest('.edit');
		const deleteBtn = event.target.closest('.delete');

		if (editBtn) {
			event.preventDefault();
			try {
				const res = await fetch(`/tiendas/edit/${editBtn.dataset.id}`);
				const tienda = await res.json();
				if (tienda) await mostrarDatosTiendaEnModal(tienda);
			} catch (error) {
				console.error("Error al editar:", error);
			}
		}

		if (deleteBtn) {
			event.preventDefault();
			if (!confirm("¿Desea eliminar esta tienda?")) return;

			try {
				const res = await fetch(`/tiendas/delete/${deleteBtn.dataset.id}`, { method: 'POST' });
				const data = await res.json();
				if (data.success) {
					showToast(data.message, 'SUCCESS', 1200);
					await obtenerTiendasByConcesionario();
				} else {
					showToast(data.message || 'Error al eliminar', 'WARNING', 1200);
				}
			} catch (error) {
				// console.error("Error al eliminar tienda:", error);
				showToast("Error al eliminar tienda", "ERROR", 1200);
			}
		}
	}
});
</script>

	<?php include __DIR__ . '/../layout/footer.php'; ?>