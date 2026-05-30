<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>405 - Método no permitido | Motorpark Yonda</title>
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
    <div class="error-code">405</div>
    <h1 class="h3 mb-3">Método no permitido</h1>
    <p class="text-secondary mb-4">La acción solicitada no está permitida para esta URL.</p>
    <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">Volver</a>
    <a href="/login" class="btn btn-primary">Iniciar sesión</a>
  </div>
</body>
</html>
