<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

	<div class="alert alert-info mt-2" role="alert">
		<div class="row">
			<div class="col-md-6 d-flex aling-items-center justify-content-start">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
						<li class="breadcrumb-item active" aria-current="page">Registrar</li>
					</ol>
				</nav>
			</div>
			<div class="col-md-6 text-end">
				<a href="/vehiculos" class="">[ Listar ]</a>
			</div>
		</div>
	</div>

	<div class="mb-2">
		<form action="/vehiculos/store" method="POST" id="registrar-vehiculos" autocomplete="off">
			<div class="card mb-2">
				<div class="card-header bg-info">
					<strong>Paso 1:</strong> <span class="fst-italic">Registra Vehiculos</span>
				</div>
				<div class="card-body">

					<div class="row g-2">
						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<select name="marcas" id="marcas" class="form-select" required>
									<option value="">Seleccione</option>
								</select>
								<label for="marcas" class="form-label">Marca <span class="text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-2 mb-2">
							<div class="form-floating">
								<select name="tipos" id="tipos" class="form-select" required>
									<option value="">Seleccione</option>
								</select>
								<label for="tipos">Tipo de vehículo <span class="text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<select name="modelos" id="modelos" class="form-select" required>
									<option value="">Seleccione</option>
								</select>
								<label for="modelos">Modelos disponibles <span class="text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-2 mb-2">
							<div class="input-group">
								<div class="form-floating">
									<select name="anios" id="anios" class="form-select" required>
										<option value="">Seleccione</option>
									</select>
									<label for="anios">Año <span class="text-danger">*</span></label>
								</div>
								<button type="button" class="btn btn-outline-success" id="btn-add-year"
									title="Incrementa el año del modelo y lo guarda en la base de datos">+</button>
							</div>
						</div>
						<div class="col-md-2 mb-2">
							<div class="form-floating">
								<select name="moneda" id="moneda" class="form-select" required>
									<option value="USD" selected>Dolares (USD)</option>
									<option value="PEN">Soles (PEN)</option>
								</select>
								<label for="moneda">Moneda <span class="text-danger">*</span></label>
							</div>
						</div>
					</div> <!-- ./row -->
					<div class="row g-2">
						<div class="col-md-2 mb-2">

							<!-- lista de versiones -->
							<div class="form-floating" id="bloque-version-lista">
								<select id="version-ls" class="form-select" required name="version">
									<option value="">Seleccione</option>
									<optgroup label="Prestaciones">
										<option value="Básico">Básico</option>
										<option value="Semi Full">Semi Full</option>
										<option value="Full">Full</option>
										<option value="Tope de gama">Tope de gama</option>
									</optgroup>
									<optgroup label="Otro">
										<option value="ESP">Especificar...</option>
									</optgroup>
								</select>
								<label for="version-ls">Versión <span class="text-danger">*</span></label>
							</div>

							<!-- input de versión (especificada por el usuario) -->
							<div class="input-group d-none" id="bloque-version-input">
								<div class="form-floating">
									<input type="text" class="form-control" id="version-in">
									<label for="version-in">Describa la versión</label>
								</div>
								<button type="button" id="mostrar-version-ls" class="btn btn-outline-secondary"
									title="Mostrar lista"><i class="fa-solid fa-bars-staggered"></i></button>
							</div>

						</div>
						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<select name="condicion" id="condicion" class="form-select" required>
									<option value="nuevo" selected>Nuevo</option>
									<option value="seminuevo">Seminuevo</option>
								</select>
								<label for="condicion">Condición <span class="text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<select name="combustible" id="combustible" class="form-select" required>
									<option value="">Seleccione</option>
									<option value="1">Gasolina</option>
									<option value="2">Diésel</option>
									<option value="3">GLP</option>
									<option value="4">GNV</option>
									<option value="5">Dual: Gasolina, GLP</option>
								</select>
								<label for="combustible">Tipo de combustible <span class="text-danger">*</span></label>
							</div>
						</div>
						<input type="hidden" name="idmodelo" id="idmodelo">
						<div class="col-md-2 mb-2">
							<div class="form-floating">
								<input type="text" id="color" name="color" class="form-control" placeholder="Color">
								<label for="color">Color</label>
							</div>
						</div>
						<div class="col-md-2 mb-2">
							<div class="form-floating">
								<input type="text" id="precio" name="precio" class="form-control text-end"
									 title="Solo se permiten números" placeholder="Precio" required>
								<label for="precio">Precio <span class="text-danger">*</span></label>
							</div>
						</div>

					</div>
					<div class="row g-2">
						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<input type="text" name="chasis" class="form-control text-center" placeholder="Chasis">
								<label for="chasis">Chasis</label>
							</div>
						</div>

						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<input type="text" name="placa" class="form-control text-center" placeholder="Placa"
									maxlength="10">
								<label for="placa">Placa</label>
							</div>
						</div>

						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<input type="text" name="placarotativa" class="form-control text-center"
									placeholder="Placa Rotativa" maxlength="10">
								<label for="placarotativa">Placa Rotativa</label>
							</div>
						</div>

						<div class="col-md-3 mb-2">
							<div class="form-floating">
								<input type="text" name="seriemotor" class="form-control text-center"
									placeholder="Serie Motor">
								<label for="seriemotor">Serie Motor</label>
							</div>
						</div>
					</div>
				</div> <!-- ./card-body -->

				<div class="card-footer text-end">
					<button type="reset" class="btn btn-sm btn-outline-secondary">Cancelar</button>
					<button type="submit" class="btn btn-sm btn-primary">Registrar</button>
				</div>
			</div><!-- ./card -->
		</form>

	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", () => {

		const hiddenModeloId = document.getElementById('idmodelo');

		// Cargar las Marcas > Tipos > Modelos > Años
		const marcasSel = document.getElementById("marcas");
		const tiposSel = document.getElementById("tipos");
		const modelosSel = document.getElementById("modelos");
		const aniosSel = document.getElementById("anios");

		let modelosCache = [];

		//Carga de marcas
		async function cargarMarcas() {
			try {
				const res = await fetch("/api/marcas");
				const data = await res.json();
				console.log('DTA;::', data);
				if (data.length > 0) {
					marcasSel.innerHTML = `<option value="">Seleccione Marca</option>`;
					data.forEach(m => {
						marcasSel.insertAdjacentHTML("beforeend",
							`<option value="${m.idmarca}">${m.marca}</option>`);
					});

				} else {
					marcasSel.innerHTML = `No se han obtenido los datos`;
				}
			} catch (error) {
				console.error(error);
			}
		}

		//marca -> cargar tipos
		marcasSel.addEventListener("change", async (event) => {
			const idm = event.target.value;
			const res = await fetch(`/api/getTipoVehiculoByMarca/${idm}`);
			const data = await res.json();

			if (data.length > 0) {
				tiposSel.innerHTML = '<option>Seleccione</option>';
				data.forEach(element => {
					tipos.innerHTML += `<option value="${element.idtipovehiculo}">${element.tipovehiculo}</option>`;
				});
			} else {
				tiposSel.innerHTML = '<option> No hay datos</option>';
			}

		});

		//tipo -> cargar modelos (VERSIÓN CORREGIDA)
		tiposSel.addEventListener("change", async (event) => {
			const idmarca = parseInt(marcasSel.value);
			const idTipoVehiculo = parseInt(event.target.value);

			modelosSel.innerHTML = `<option value="">Cargando Modelos...</option>`;
			aniosSel.innerHTML = `<option value="">Seleccione</option>`;
			modelosCache = [];
			hiddenModeloId.value = ''; // Limpiar también el hidden

			if (!idmarca || !idTipoVehiculo) {
				modelosSel.innerHTML = `<option value="">Seleccione marca y tipo primero</option>`;
				return;
			}

			try {
				const res = await fetch(`/api/getModeloByTipoMarca/${idmarca}/${idTipoVehiculo}`);
				const dataModelos = await res.json();

				if (!Array.isArray(dataModelos) || dataModelos.length === 0) {
					modelosSel.innerHTML = `<option value="">No hay modelos disponibles</option>`;
					return;
				}

				//console.log('Modelos recibidos:', dataModelos); // Debug

				// Guarda TODOS los modelos en cache (incluye todas las combinaciones modelo+año)
				modelosCache = dataModelos;

				// Extraer solo nombres únicos para mostrar en el select
				const nombresUnicos = [...new Set(dataModelos.map(element => element.modelo))].sort();

				modelosSel.innerHTML = `<option value="">Seleccione Modelo</option>`;

				nombresUnicos.forEach(nombreModelo => {
					modelosSel.insertAdjacentHTML("beforeend",
						`<option value="${nombreModelo}">${nombreModelo}</option>`);
				});

				console.log('Cache de modelos:', modelosCache); // Debug
				console.log('Nombres únicos:', nombresUnicos); // Debug

			} catch (error) {
				console.error('Error al cargar modelos:', error);
				modelosSel.innerHTML = `<option value="">Error al cargar modelos</option>`;
			}
		});

		// EVENTO DE MODELO SELECCIONADO (CORREGIDO)
		modelosSel.addEventListener("change", () => {
			const nombreModeloSeleccionado = modelosSel.value; // Ahora es el NOMBRE
			aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
			hiddenModeloId.value = '';

			if (!nombreModeloSeleccionado) return;

			console.log('Modelo seleccionado:', nombreModeloSeleccionado); // Debug

			// Filtrar por NOMBRE del modelo (no por idmodelo)
			const modelosFiltrados = modelosCache.filter(item => item.modelo === nombreModeloSeleccionado);

			console.log('Modelos filtrados:', modelosFiltrados); // Debug

			if (modelosFiltrados.length === 0) {
				aniosSel.innerHTML = `<option value="">No hay años disponibles</option>`;
				return;
			}

			// Extraer años únicos y ordenarlos
			const aniosUnicos = [...new Set(modelosFiltrados.map(item => parseInt(item.anio)))]
				.sort((a, b) => b - a); // Orden descendente (más nuevo primero)

			console.log('Años únicos:', aniosUnicos); // Debug

			// Recargar el select de años
			aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
			aniosUnicos.forEach(anio => {
				aniosSel.insertAdjacentHTML("beforeend", `<option value="${anio}">${anio}</option>`);
			});
		});

		// EVENTO DE AÑO SELECCIONADO (CORREGIDO)
		aniosSel.addEventListener("change", () => {
			const nombreModeloSeleccionado = modelosSel.value; // Es el NOMBRE
			const anioSeleccionado = parseInt(aniosSel.value);

			console.log('Buscando:', nombreModeloSeleccionado, anioSeleccionado); // Debug

			if (!nombreModeloSeleccionado || !anioSeleccionado) {
				hiddenModeloId.value = '';
				return;
			}

			// Buscar el registro exacto por nombre + año
			const modeloEncontrado = modelosCache.find(m =>
				m.modelo === nombreModeloSeleccionado && parseInt(m.anio) === anioSeleccionado
			);

			console.log('Modelo encontrado:', modeloEncontrado); // Debug

			if (modeloEncontrado) {
				hiddenModeloId.value = modeloEncontrado.idmodelo;
				console.log('idmodelo asignado:', modeloEncontrado.idmodelo); // Debug
			} else {
				hiddenModeloId.value = '';
				console.warn("No se encontró modelo con ese año");
			}
		});

		//boton de ESP (input de especificar)
		const versionLS = document.getElementById('version-ls');
		const versionIN = document.getElementById('version-in');
		const mostrarVersionLS = document.getElementById('mostrar-version-ls');
		const bloqueVersionLista = document.getElementById("bloque-version-lista");
		const bloqueVersionInput = document.getElementById("bloque-version-input");

		versionLS.addEventListener("change", (event) => {
			const opcion = event.target.value;
			if (opcion === "ESP") {
				// ocultar
				bloqueVersionLista.classList.add("d-none");
				bloqueVersionInput.classList.remove("d-none");
				//pasar el nombre
				versionLS.removeAttribute("name");
				versionIN.setAttribute("name", "version");
				versionIN.value = "";
				versionIN.focus();
			} else {
				versionIN.value = opcion;
			}
		});

		mostrarVersionLS.addEventListener("click", () => {
			// volver al select
			versionIN.removeAttribute("name");
			versionLS.setAttribute("name", "version");
			versionIN.value = "";
			bloqueVersionLista.classList.remove("d-none");
			bloqueVersionInput.classList.add("d-none");
			versionLS.value = "";
		});

		cargarMarcas();

		/* const form = document.getElementById('registrar-vehiculos');
		form.addEventListener('submit', e => {
		  // Antes de enviar, volcar todo el FormData
		  const data = new FormData(form);
		  console.group('FormData antes de submit');
		  for (let [key, val] of data.entries()) {
			console.log(key, val);
		  }
		  console.groupEnd();
		}); */

		const formVeh = document.getElementById('registrar-vehiculos');
		formVeh.addEventListener('submit', async (e) => {
			e.preventDefault();

			const {
				isConfirmed
			} = await Swal.fire({
				title: '¿Registrar nuevo vehículo?',
				text: 'Esta acción registrará el vehículo en el sistema.',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Sí',
				cancelButtonText: 'Cancelar',
				reverseButtons: false
			});

			if (isConfirmed) {
				formVeh.submit();
			}
		});

		// BOTÓN AGREGAR AÑO (CORREGIDO)
		document.getElementById('btn-add-year').addEventListener('click', async () => {
			const nombreModeloActual = modelosSel.value; // Nombre del modelo actual

			// Solo verificar que haya un modelo seleccionado, no un año específico
			if (!nombreModeloActual) {
				alert('Debe seleccionar un modelo primero');
				return;
			}

			const anio = prompt('Nuevo año a agregar:');
			if (!anio || isNaN(anio)) {
				alert('Debe ingresar un año válido');
				return;
			}

			// Verificar si el año ya existe para este modelo
			const modelosFiltrados = modelosCache.filter(item => item.modelo === nombreModeloActual);
			const anioYaExiste = modelosFiltrados.some(item => parseInt(item.anio) === parseInt(anio));

			if (anioYaExiste) {
				alert('Este año ya existe para el modelo seleccionado');
				return;
			}

			try {
				// Usar cualquier idmodelo del mismo modelo como base (tomar el primero disponible)
				const modeloBase = modelosFiltrados[0]; // Primer registro del modelo
				const idmodeloBase = modeloBase.idmodelo;

				const res = await fetch('/vehiculos/agregarAnio', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						idmodelo_base: parseInt(idmodeloBase),
						anio: parseInt(anio, 10)
					})
				});

				const json = await res.json();

				if (res.ok && json.success) {
					alert('Año agregado: ID ' + json.idmodelo + ' — ' + json.anio);

					// Agregar el nuevo registro al cache
					const nuevoRegistro = {
						idmodelo: json.idmodelo,
						modelo: modeloBase.modelo,
						anio: parseInt(json.anio),
						idmarca: modeloBase.idmarca,
						idtipovehiculo: modeloBase.idtipovehiculo
					};

					modelosCache.push(nuevoRegistro);
					console.log('Nuevo registro agregado al cache:', nuevoRegistro);

					// Recargar los años para el modelo actual
					const modelosFiltradosActualizados = modelosCache.filter(item =>
						item.modelo === nombreModeloActual
					);

					const aniosUnicos = [...new Set(modelosFiltradosActualizados.map(item => parseInt(item.anio)))]
						.sort((a, b) => b - a);

					// Recargar el select de años
					aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
					aniosUnicos.forEach(anioItem => {
						aniosSel.insertAdjacentHTML("beforeend",
							`<option value="${anioItem}">${anioItem}</option>`);
					});

					// Seleccionar automáticamente el año recién agregado
					aniosSel.value = json.anio;
					aniosSel.dispatchEvent(new Event('change'));

				} else {
					alert('Error: ' + (json.error || 'No se pudo agregar'));
				}
			} catch (err) {
				console.error('Error:', err);
				alert('Error en la petición: ' + err.message);
			}
		});

	});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>