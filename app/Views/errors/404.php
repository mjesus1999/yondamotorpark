<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - No encontrado | Motorpark Yonda</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/tokens.css">
  <style>
    body {
      font-family: Inter, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.15), transparent 45%),
        linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
      color: #e5e7eb;
    }
    .error-code { font-size: 6rem; font-weight: 700; color: #f97316; line-height: 1; }
  </style>
</head>
<body>
  <div class="container text-center py-5">
    <div class="error-code">404</div>
    <h1 class="h3 mb-3">Página no encontrada</h1>
    <p class="text-secondary mb-4">
      La ruta que buscas no existe, fue movida o no tienes permiso para ese módulo.
    </p>
    <div class="d-flex flex-wrap gap-2 justify-content-center">
      <a href="/" class="btn btn-primary">Ir al inicio</a>
      <a href="/caja" class="btn btn-outline-primary">Caja (lista)</a>
      <a href="/caja/buscar-documento" class="btn btn-outline-primary">Buscar DNI / RUC</a>
      <a href="javascript:history.back()" class="btn btn-outline-secondary">Volver</a>
      <a href="/login" class="btn btn-outline-secondary">Iniciar sesión</a>
    </div>
  </div>
</body>
</html>
