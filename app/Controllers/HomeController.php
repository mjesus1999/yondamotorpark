<?php

/**
 * Controlador de inicio
 * app/Controllers/HomeController.php
 * 
 * Gestiona la pagina principal y el dashboard.
 * Proporciona acceso a la vista de inicio para usuarios autenticados.
 */
namespace App\Controllers; // Declara el namespace correcto

use App\Core\Controller; // Importa la clase base Controller

/**
 * Clase HomeController
 * 
 * Controlador principal para la pagina de inicio del sistema.
 * Maneja el acceso al dashboard y a la pagina principal despues del login
 */
class HomeController extends Controller
{

  /**
   * Muestra la pagina de inicio
   * 
   * Renderiza la pagina principal del dashboard. Requiere que el usuario este autenticado para acceder a esta pagina
   * 
   * @return void
   */
  public function index(): void
  {
    $this->authRequired();
    $this->view('home.index');
  }
}