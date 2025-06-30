<?php include __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Lista de Productos</h1>

<a href="/products/create" class="btn btn-success mb-3">Agregar</a>
<a href="/products/search" class="btn btn-success mb-3">Buscador</a>

<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr>
          <td colspan="6" class="text-center">No hay productos registrados.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?= htmlspecialchars($product['id']) ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['category']) ?></td>
            <td>S/ <?= number_format(htmlspecialchars($product['price']), 2) ?></td>
            <td><?= htmlspecialchars($product['date']) ?></td>
            <td>
              <a href="/products/edit/<?= htmlspecialchars($product['id']) ?>"
                class="btn btn-warning btn-sm me-2">Editar</a>
              <form action="/products/delete/<?= htmlspecialchars($product['id']) ?>" method="POST" class="d-inline"
                onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>