<?php include __DIR__ . '/../layout/header.php'; ?>

<h1 class="mb-4">Buscador de Productos</h1>

<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<div class="container">
  <div class="row">
    <div class="col-md-4 mx-auto">
      <form action="" method="POST" autocomplete="off" id="formulario">
        <div class="mb-2">
          <input type="text" class="form-control" placeholder="ID" id="idbuscado" autofocus required>
        </div>
        <div>
          <input type="text" class="form-control" placeholder="Nombre" id="nombre">
        </div>
        <button class="btn btn-sm btn-primary" type="submit">Buscar</button>
      </form>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", () => {
    const idbuscado = document.querySelector("#idbuscado")
    const nombre = document.querySelector("#nombre")
    const formulario = document.querySelector("#formulario")

    formulario.addEventListener("submit", (event) => {
      event.preventDefault()

      const id = idbuscado.value

      fetch(`/api/products/${id}`, { method: 'GET'})
        .then(response => response.json())
        .then(data => {
          console.log(data)
          nombre.value = data['product']['name']
        })
        .catch(e => console.error(e))
    })
    
  })
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>