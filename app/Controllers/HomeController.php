<?php
// app/Controllers/HomeController.php

namespace App\Controllers; // Declara el namespace correcto

use App\Core\Controller; // Importa la clase base Controller

class HomeController extends Controller
{
  public function index(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $this->authRequired();

    /* header("Cache-Control: no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");  // En caso de que el navegador quiera cachear algo.
 */

    // Llama al método 'view' de la clase base Controller
    // para cargar la vista 'home/index.php'
    $this->view('home.index');
  }
}