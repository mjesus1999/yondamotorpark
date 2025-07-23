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
									<td><?= htmlspecialchars($v['color']) ?></td>
									<td><?= htmlspecialchars($v['disponibilidad']) ?></td>
									<td class="text-center">
										<!-- Editar -->
										<a href="#" class="btn btn-sm btn-outline-primary" title="Editar">
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
	document.querySelectorAll('.btn-borrar').forEach(button => {
		button.addEventListener('click', () => {
			const id = button.getAttribute('data-id');
			if (confirm(`¿Seguro que deseas eliminar el vehículo #${id}?`)) {
				fetch('/vehiculos/delete', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: `idvehiculo=${encodeURIComponent(id)}`
				})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							alert(data.success);
							window.location.reload(); // recarga la lista
						} else {
							alert(data.error || 'Ocurrió un error al eliminar');
						}
					})
					.catch(error => {
						alert('Error en la solicitud: ' + error);
					});
			}
		});
	});
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>