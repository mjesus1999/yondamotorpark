<?php
use App\Models\Permisos;

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$permisosModel = new Permisos();

if (!empty($_SESSION['user']['idcargo'])) {
  $modulosPermitidos = $permisosModel->getPermisosByCargo((int) $_SESSION['user']['idcargo']);
} else {
  $modulosPermitidos = [];
}

//Definimos los modulos en un array
$allModules = [
  'oc' => ['url' => '/oc', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Orden de compra'],
  'compras' => ['url' => '/compras', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Compras'],
  'Concesionarios' => ['url' => '/concesionarios', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Concesionarios'],
  'marcas' => ['url' => '/marcas', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Marcas'],
  'vehiculos' => ['url' => '/vehiculos', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Vehículos'],
  'usuarios' => ['url' => '/usuarios', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Usuarios'],
  'formatoCotizacion' => ['url' => '/formatoCotizacion', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Requisitos'],
  'cotizacion' => ['url' => '/cotizacion', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Cotización']
  /* 'auth' => ['url' => '/auth', 'icon' => 'fa-solid fa-list pe-2', 'label' => 'Auth'] */
];
?>


<!DOCTYPE html>

<html lang="es" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Motorpark Yonda</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

  <!-- <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css"> -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/select/3.0.0/css/select.bootstrap5.css">

  <link rel="stylesheet" href="/assets/css/style-dashboard.css">
  <link rel="stylesheet" href="/assets/css/motorpark-style.css">

</head>

<body>

  <div class="wrapper">

    <aside id="sidebar" class="js-sidebar">
      <!-- Content For Sidebar -->
      <div class="h-100 sticky-top">
        <div class="sidebar-logo">
          <a href="/">
            <img src="/assets/images/motoropark-logo-blanco.png" class="img-fluid" alt="">
          </a>
        </div>
        <ul class="sidebar-nav">
          <li class="sidebar-header">
            Módulos
          </li>

          <?php foreach ($allModules as $codigo => $m): ?>
            <?php if (in_array($codigo, $modulosPermitidos, true)): ?>
              <li class="sidebar-item">
                <a href="<?= htmlspecialchars($m['url']) ?>" class="sidebar-link">
                  <i class="fa-solid <?= htmlspecialchars($m['icon']) ?> pe-2"></i>
                  <?= htmlspecialchars($m['label']) ?>
                </a>
              </li>
            <?php endif ?>
          <?php endforeach ?>


          <?php

          $showAuth = empty($_SESSION['user']) || in_array('auth', $modulosPermitidos, true);
          if ($showAuth):
            ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-target="#auth" data-bs-toggle="collapse"
                aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>
                Auth
              </a>

              <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <?php if (empty($_SESSION['user'])): // visitante -> Login + Recuperar ?>
                  <li class="sidebar-item">
                    <a href="/login" class="sidebar-link">Login</a>
                  </li>
                  <li class="sidebar-item">
                    <a href="/recoverAccount" class="sidebar-link">Recuperar contraseña</a>
                  </li>
                <?php endif; ?>

                <?php if (in_array('auth', $modulosPermitidos, true)): // usuario con permiso 'auth' -> Registrar ?>
                  <li class="sidebar-item">
                    <a href="/createAccount" class="sidebar-link">Registrar cuenta</a>
                  </li>
                <?php endif; ?>

                <!-- mostrar "Forgot Password" para todos -->
                <!--
                <li class="sidebar-item">
                  <a href="/recoverAccount" class="sidebar-link">Forgot Password</a>
                </li>
                -->
              </ul>
            </li>
          <?php endif; ?>


          


          <li class="sidebar-item">
            <a href="/oc" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Orden de compra
            </a>
          </li>
          <li class="sidebar-item">
            <a href="/compras" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Compras
            </a>
          </li>
          <li class="sidebar-item">
            <a href="/concesionarios" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Concesionarios
            </a>
          </li>
          <!-- <li class="sidebar-item">
            <a href="/marcas" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Marcas
            </a>
          </li> -->
          <!-- <li class="sidebar-item">
            <a href="/vehiculos" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Vehículos
            </a>
          </li> -->
          <li class="sidebar-item">
            <a href="/locales" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Locales
            </a>
          </li>

          <li class="sidebar-item">
            <a href="/clientes" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Clientes
            </a>
          </li>

          <li class="sidebar-item">
            <a href="/caja" class="sidebar-link">
              <i class="fa-solid fa-list pe-2"></i>
              Caja
            </a>
          </li>
          <!-- <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#pages" data-bs-toggle="collapse"
              aria-expanded="false"><i class="fa-solid fa-file-lines pe-2"></i>
              Pages
            </a>
            <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Page 1</a>
              </li>
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Page 2</a>
              </li>
            </ul>
          </li> -->
          <!-- <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#posts" data-bs-toggle="collapse"
              aria-expanded="false"><i class="fa-solid fa-sliders pe-2"></i>
              Posts
            </a>
            <ul id="posts" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Post 1</a>
              </li>
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Post 2</a>
              </li>
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Post 3</a>
              </li>
            </ul>
          </li> -->
          <!-- <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#auth" data-bs-toggle="collapse"
              aria-expanded="false"><i class="fa-regular fa-user pe-2"></i>
              Auth
            </a>
            <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Login</a>
              </li>
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Register</a>
              </li>
              <li class="sidebar-item">
                <a href="#" class="sidebar-link">Forgot Password</a>
              </li>
            </ul>
          </li> -->
          <!-- <li class="sidebar-header">
            Multi Level Menu
          </li>
          <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#multi" data-bs-toggle="collapse"
              aria-expanded="false"><i class="fa-solid fa-share-nodes pe-2"></i>
              Multi Dropdown
            </a>
            <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed" data-bs-target="#level-1" data-bs-toggle="collapse"
                  aria-expanded="false">Level 1</a>
                <ul id="level-1" class="sidebar-dropdown list-unstyled collapse">
                  <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Level 1.1</a>
                  </li>
                  <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Level 1.2</a>
                  </li>
                </ul>
              </li>
            </ul>
          </li> -->
        </ul>
      </div>
    </aside>

    <div class="main">

      <nav class="navbar navbar-expand px-3 border-bottom">

        <button class="btn" id="sidebar-toggle" type="button">

          <span class="navbar-toggler-icon"></span>

        </button>

        <div class="navbar-collapse navbar">

          <ul class="navbar-nav">

            <li class="nav-item dropdown">

              <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">

                <img src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? '/assets/images/profile.jpg') ?>"

                  class="avatar img-fluid rounded" alt="Avatar" />

              </a>

              <div class="dropdown-menu dropdown-menu-end">



                <!-- MOSTRAR POR NOMBRE Y APELLIDOS -->

                <?php if (!empty($_SESSION['user'])): ?>

                  <a href="/usuarios/profile/<?= $_SESSION['user']['id'] ?>" class="dropdown-item">

                    <?php

                    $primerNombre = isset($_SESSION['user']['nombres']) ? explode(' ', trim($_SESSION['user']['nombres']))[0] : '';

                    $primerApellido = isset($_SESSION['user']['apellidos']) ? explode(' ', trim($_SESSION['user']['apellidos']))[0] : '';

                    echo htmlspecialchars($primerNombre . ' ' . $primerApellido);

                    ?>

                  </a>

                <?php endif; ?>



                <a href="#" class="dropdown-item">Configuración</a>

                <a href="#" class="dropdown-item">Cambiar contraseña</a>

                <a href="/logout" class="dropdown-item">Cerrar sesión</a>

              </div>

            </li>

          </ul>

        </div>

      </nav>
      <main class="content px-3 py-2">