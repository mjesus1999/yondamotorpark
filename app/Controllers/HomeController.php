<?php
// app/Controllers/HomeController.php

namespace App\Controllers; // Declara el namespace correcto
use App\Core\Controller; // Importa la clase base Controller

class HomeController extends Controller
{
  public function index(): void
  {
      $this->authRequired(); // si no está logueado, redirige a /login
        // si está logueado, muestra home
        $this->view('home.index');
  }
}