<?php include __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Editar Producto</h1>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<?php if (isset($product)): ?>
  <form action="/products/update/<?= htmlspecialchars($product['id']) ?>" method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Nombre del Producto</label>
      <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>"
        required>
    </div>
    <div class="mb-3">
      <label for="category" class="form-label">Categoría</label>
      <input type="text" class="form-control" id="category" name="category"
        value="<?= htmlspecialchars($product['category']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="price" class="form-label">Precio</label>
      <input type="number" step="0.01" class="form-control" id="price" name="price"
        value="<?= htmlspecialchars($product['price']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
    <a href="/products" class="btn btn-secondary ms-2">Cancelar</a>
  </form>
<?php else: ?>
  <div class="alert alert-warning" role="alert">
    Producto no encontrado.
  </div>
  <a href="/products" class="btn btn-secondary">Volver a la lista</a>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>