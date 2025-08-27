<?php
// app/Controllers/HomeController.php

namespace App\Controllers; // Declara el namespace correcto
use App\Core\Controller; // Importa la clase base Controller

class HomeController extends Controller
{
  public function index(): void
  {
    $this->authRequired();
        $this->view('home.index');
  }
}