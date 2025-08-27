<?php include __DIR__ . '/../layout/header.php'; ?>

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
			<div class="col-md-6 d-flex aling-items-center justify-content-start">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item"><a href="#">Vehiculos</a></li>
						<li class="breadcrumb-item active" aria-current="page">Listar</li>
					</ol>
				</nav>
			</div>
			<div class="col-md-6 text-end">
				<a href="/vehiculos/create" class="">[ Registrar ]</a>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-22">
			<div class="card">

				<div class="card-header">
					<?php $estadoActual = $estadoActual ?? ''; ?>

					<div class="btn-group m-1" id="botones-filtro">

						<a href="/vehiculos?estado=proceso"
							class="btn btn-sm <?= $estadoActual === 'proceso' ? 'btn-warning text-white' : 'btn-outline-warning' ?>">
							Proceso
						</a>
						<a href="/vehiculos?estado=libre"
							class="btn btn-sm <?= $estadoActual === 'libre' ? 'btn-primary' : 'btn-outline-primary' ?>">
							Libre
						</a>
						<a href="/vehiculos?estado=separado"
							class="btn btn-sm <?= $estadoActual === 'separado' ? 'btn-success' : 'btn-outline-success' ?>">
							Separado
						</a>
						<a href="/vehiculos?estado=vendido"
							class="btn btn-sm <?= $estadoActual === 'vendido' ? 'btn-danger' : 'btn-outline-danger' ?>">
							Pagado
						</a>
					</div>
				</div>

				<div class="card-body">
					<table class="table table-sm table-hover table-hover-yonda" id="tabla-vehiculos">
						<thead>
							<tr>
								<th>#</th>
								<th>Marca</th>
								<th>Tipo Vehiculo</th>
								<th>Modelo</th>
								<th>Version</th>
								<th>Condicion</th>
								<th>Color</th>
								<th>Disponibilidad</th>
								<th>Opciones</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($vehiculos as $v): ?>
								<tr>
									<td><?= htmlspecialchars($v['idvehiculo']) ?></td>
									<td><?= htmlspecialchars($v['marca']) ?></td>
									<td><?= htmlspecialchars($v['tipovehiculo']) ?></td>
									<td><?= htmlspecialchars($v['modelo']) ?></td>
									<td><?= htmlspecialchars($v['version']) ?></td>
									<td><?= htmlspecialchars($v['condicion']) ?></td>
									<td><?= htmlspecialchars($v['color'] ?? 'N/A') ?></td>
									<td><?= htmlspecialchars($v['disponibilidad']) ?></td>
									<td class="text-center">
										<!-- Editar -->
										<a href="/vehiculos/edit/<?= $v['idvehiculo'] ?>"
											class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="fa-solid fa-pen"></i>
										</a>
										<!-- Eliminar -->
										<button type="button" class="btn btn-sm btn-outline-danger btn-borrar"
											title="Eliminar" data-id="<?= htmlspecialchars($v['idvehiculo']) ?>">
											<i class="fa-solid fa-trash"></i>
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>

					</table>
				</div>
			</div>
		</div>
	</div>

</div>

<script>
	document.addEventListener("DOMContentLoaded", function () {
		// 1) Inicializar DataTable en español con paginación simbólica
		$('#tabla-vehiculos').DataTable({
			order: [[0, 'desc']],
			pagingType: 'full_numbers',       // « ‹ 1 2 3 … › »
			pageLength: 10,
			lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
			responsive: true,
			language: {
				url: "https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json",
				paginate: {
					first: '«',
					previous: '‹',
					next: '›',
					last: '»'
				}
			}
		});

		// 2) Asignar evento de borrado a cada botón
		document.querySelectorAll('.btn-borrar').forEach(btn => {
			btn.addEventListener('click', async () => {
				const id = btn.getAttribute('data-id');
				if (!id) return;

				Swal.fire({
					title: '¿Estás seguro?',
					text: "¡Eliminar!",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Sí',
					cancelButtonText: 'Cancelar',
					reverseButtons: false
				}).then(async (result) => {
					if (!result.isConfirmed) return;

					try {
						const res = await fetch('/vehiculos/delete', {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
							body: new URLSearchParams({ idvehiculo: id }).toString(),
							credentials: 'same-origin'
						});

						// intentar leer JSON (si no es JSON, data será {})
						const data = await res.json().catch(() => ({}));

						if (res.ok && (data.success || data.success === true)) {
							await Swal.fire('Eliminado', data.success || 'Vehículo eliminado correctamente', 'success');
							const params = new URLSearchParams(window.location.search);
							window.location.href = '/vehiculos?' + params.toString();
							return;
						}

						// ===== Aquí interceptamos errores técnicos y los mapeamos a un mensaje amigable =====
						// rawMsg: lo que vino del servidor (puede contener traza SQL)
						const rawMsg = data.error || (Array.isArray(data.errors) ? data.errors.join('\n') : '') || '';

						// normalizamos y buscamos pistas de FK / MySQL 1451
						const lower = String(rawMsg).toLowerCase();

						let userMsg = rawMsg || 'No se pudo eliminar el vehículo.';
						if (lower.includes('1451') || lower.includes('foreign key') || lower.includes('cannot delete or update a parent row')) {
							userMsg = 'No se puede eliminar este vehículo porque está referenciado en cotizaciones u otros registros';
						} else if (lower.includes('constraint') && lower.includes('foreign')) {
							userMsg = 'No se puede eliminar el registro debido a referencias en otras tablas (clave foránea).';
						} else if (lower.includes('permission') || lower.includes('denied') || res.status === 403) {
							userMsg = 'No tienes permisos para eliminar este vehículo.';
						} else if (data.message) {
							// algunos controladores devuelven 'message'
							userMsg = data.message;
						} else if (!rawMsg && !res.ok) {
							userMsg = `Error del servidor (${res.status}).`;
						}

						Swal.fire('Error', userMsg, 'error');

					} catch (err) {
						Swal.fire('Error', 'Error en la solicitud: ' + (err.message || err), 'error');
					}
				});
			});
		});
	});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>