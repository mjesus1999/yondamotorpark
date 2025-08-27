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
									pattern="[0-9]+" title="Solo se permiten números" placeholder="Precio" required>
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

		//tipo -> cargar modelos
		// Adaptado: usar estructura de tiposSel con lógica de getModeloByTipoMarca
		tiposSel.addEventListener("change", async (event) => {
			const idmarca = parseInt(marcasSel.value);
			const idTipoVehiculo = parseInt(event.target.value);

			modelosSel.innerHTML = `<option value="">Cargando Modelos...</option>`;
			aniosSel.innerHTML = `<option value="">Seleccione</option>`;
			modelosCache = [];

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

				// Map para evitar modelos repetidos
				const modelosUnicos = new Map();

				dataModelos.forEach(element => {
					if (!modelosUnicos.has(element.modelo)) {
						modelosUnicos.set(element.modelo, element.idmodelo);
					}
				});

				modelosSel.innerHTML = `<option value="">Seleccione Modelo</option>`;
				modelosUnicos.forEach((idmodelo, modelo) => {
					modelosSel.insertAdjacentHTML("beforeend",
						`<option value="${idmodelo}">${modelo}</option>`);
				});

				// Guarda en cache para futuros usos
				modelosCache = dataModelos;

			} catch (error) {
				console.error('Error al cargar modelos:', error);
				modelosSel.innerHTML = `<option value="">Error al cargar modelos</option>`;
			}
		});





		//modelo -> cargar años
		modelosSel.addEventListener("change", () => {
			const idmodeloSeleccionado = modelosSel.value;
			aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
			hiddenModeloId.value = '';

			if (!idmodeloSeleccionado) return;

			// Filtrar modelos por idmodelo (no por nombre)
			const modelosFiltrados = modelosCache.filter(item => item.idmodelo == idmodeloSeleccionado);

			if (modelosFiltrados.length === 0) {
				aniosSel.innerHTML = `<option value="">No hay años disponibles</option>`;
				return;
			}

			// Extraer años únicos y ordenarlos
			const aniosUnicos = [...new Set(modelosFiltrados.map(item => item.anio))].sort((a, b) => a - b);

			aniosSel.innerHTML = `<option value="">Seleccione Año</option>`;
			aniosUnicos.forEach(anio => {
				aniosSel.insertAdjacentHTML("beforeend",
					`<option value="${anio}">${anio}</option>`);
			});

			// Puedes guardar el idmodelo seleccionado si lo necesitas más adelante
			hiddenModeloId.value = idmodeloSeleccionado;
		});



		// modelos.addEventListener('change', async (event) => {
		//     const modeloSeleccionado = event.target.value;
		//     // Filtrar todos los objetos con ese modelo
		//     const modelosFiltrados = dataModelos.filter(item => item.idmodelo == modeloSeleccionado);

		//     // Extraer años únicos
		//     const aniosUnicos = [...new Set(modelosFiltrados.map(item => item.anio))];

		//     // Limpiar el select de años
		//     anios.innerHTML = '<option>Seleccione</option>';

		//     if (aniosUnicos.length > 0) {
		//         aniosUnicos.forEach(anio => {
		//             anios.innerHTML += `<option value="${anio}">${anio}</option>`;
		//         });
		//     } else {
		//         anios.innerHTML += `<option>No hay datos</option>`;
		//     }
		// });


		//Al cambiar año, buscar el objeto completo y setear su ID
		aniosSel.addEventListener("change", () => {
			const selModelo = modelosSel.value;
			const selAnio = aniosSel.value;
			if (!selModelo || !selAnio) {
				hiddenModeloId.value = '';
				return;
			}
			const encontrado = modelosCache.find(m =>
				m.modelo === selModelo && String(m.anio) === selAnio
			);
			hiddenModeloId.value = encontrado ?
				encontrado.idmodelo :
				'';
		});


		aniosSel.addEventListener("change", () => {
			const modeloSeleccionado = modelosSel.value;
			const anioSeleccionado = aniosSel.value;

			if (!modeloSeleccionado || !anioSeleccionado) {
				hiddenModeloId.value = '';
				return;
			}

			const modeloEncontrado = modelosCache.find(m =>
				m.idmodelo == modeloSeleccionado && m.anio == anioSeleccionado
			);

			if (modeloEncontrado) {
				hiddenModeloId.value = modeloEncontrado.idmodelo;
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

		document.getElementById('btn-add-year').addEventListener('click', async () => {
			const idmodeloBase = document.getElementById('idmodelo').value;
			const anio = prompt('Nuevo año a agregar:');
			if (!anio) return;

			try {
				const res = await fetch('/vehiculos/agregarAnio', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ idmodelo_base: idmodeloBase, anio: parseInt(anio, 10) })
				});
				const json = await res.json();
				if (res.ok && json.success) {
					alert('Año agregado: ID ' + json.idmodelo + ' — ' + json.anio);

					// Añadir el nuevo año al select de años (sin recargar la página)
					const aniosSel = document.getElementById("anios");
					const option = document.createElement("option");
					option.value = json.anio;
					option.textContent = json.anio;
					aniosSel.appendChild(option);  // Añadir el nuevo año al final del select
					aniosSel.value = json.anio;   // Opcional: seleccionar el nuevo año

				} else {
					alert('Error: ' + (json.error || 'No se pudo agregar'));
				}
			} catch (err) {
				alert('Error en la petición: ' + err.message);
			}
		});

	});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>