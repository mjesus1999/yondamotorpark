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

  protected function authRequired(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (empty($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }

    //evita cache del navegador
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }

}