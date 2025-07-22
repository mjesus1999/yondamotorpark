<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

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
									<td><?= htmlspecialchars($v['modelo']) ?></td>
									<td><?= htmlspecialchars($v['version']) ?></td>
									<td><?= htmlspecialchars($v['condicion']) ?></td>
									<td><?= htmlspecialchars($v['color']) ?></td>
									<td><?= htmlspecialchars($v['disponibilidad']) ?></td>
									<td class="text-center">
										<!-- Editar -->
										<a href="#"
											class="btn btn-sm btn-outline-primary" title="Editar">
											<i class="fa-solid fa-pen"></i>
										</a>
										<!-- Eliminar -->
										<button type="button" class="btn btn-sm btn-outline-danger btn-borrar"
											title="Eliminar">
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

<?php include __DIR__ . '/../layout/footer.php'; ?>