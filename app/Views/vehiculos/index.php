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
						<a href="/vehiculos?estado=libre"
							class="btn btn-sm <?= $estadoActual === 'libre' ? 'btn-primary' : 'btn-outline-primary' ?>">
							Libre
						</a>
						<a href="/vehiculos?estado=proceso"
							class="btn btn-sm <?= $estadoActual === 'proceso' ? 'btn-warning text-white' : 'btn-outline-warning' ?>">
							Proceso
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
										<a href="/vehiculos/edit/<?= $v['idvehiculo'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
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
		document.querySelectorAll('.btn-borrar').forEach(button => {
			button.addEventListener('click', () => {
				const id = button.getAttribute('data-id');
				if (!confirm(`¿Seguro que deseas eliminar el vehículo #${id}?`)) {
					return;
				}
				fetch('/vehiculos/delete', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: `idvehiculo=${encodeURIComponent(id)}`
				})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							alert(data.success);
							// para mantener el filtro actual en la recarga
							const params = new URLSearchParams(window.location.search);
							window.location.href = '/vehiculos?' + params.toString();
						} else {
							alert(data.error || 'Ocurrió un error al eliminar');
						}
					})
					.catch(error => {
						alert('Error en la solicitud: ' + error);
					});
			});
		});
	});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>