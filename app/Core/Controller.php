<?php
// app/Core/Controller.php

namespace App\Core;

class Controller
{
  protected function view(string $path, array $data = []): void
  {
    extract($data); // Extrae los datos para que estén disponibles como variables en la vista
    require __DIR__ . '/../Views/' . str_replace('.', '/', $path) . '.php';
  }

  protected function redirect(string $path): void
  {
    header("Location: " . $path);
    exit();
  }
}