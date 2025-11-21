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

// Función helper para verificar si tiene permiso
function tienePermiso($modulo, $modulosPermitidos)
{
  return in_array($modulo, $modulosPermitidos, true);
}

// Función para verificar si tiene permiso a algún submódulo de un grupo
function tienePermisoGrupo($modulos, $modulosPermitidos)
{
  foreach ($modulos as $modulo) {
    if (in_array($modulo, $modulosPermitidos, true)) {
      return true;
    }
  }
  return false;
}
?>

<style>
  .sidebar-link.active {
    background-color: #007bff;
    color: white;
    font-weight: bold;
  }

  .sidebar-item .collapse.show {
    display: block;
  }
</style>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Motorpark Yonda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="/assets/css/style-dashboard.css">
  <link rel="stylesheet" href="/assets/css/motorpark-style.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
</head>

<body>

  <div class="wrapper">

    <aside id="sidebar" class="js-sidebar">
      <div class="h-100 sticky-top">
        <div class="sidebar-logo">
          <a href="/">
            <img src="/assets/images/motoropark-logo-blanco.png" class="img-fluid" alt="">
          </a>
        </div>
        <ul class="sidebar-nav">
          <li class="sidebar-header">Módulos</li>

          <!-- Módulo de Clientes y Concesionarios -->
          <?php if (tienePermisoGrupo(['concesionarios', 'locales', 'clientes'], $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                data-bs-target="#gestionClientesConcesionarios" aria-expanded="false">
                <i class="fa-solid fa-users pe-2"></i> Clientes y Concesionarios
              </a>
              <ul id="gestionClientesConcesionarios" class="sidebar-dropdown list-unstyled collapse ms-4"
                data-bs-parent="#sidebar">
                <?php if (tienePermiso('concesionarios', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/concesionarios" class="sidebar-link">
                      <i class="fa-solid fa-store pe-2"></i> Concesionarios
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('locales', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/locales" class="sidebar-link">
                      <i class="fa-solid fa-location-pin pe-2"></i> Locales
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('clientes', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/clientes" class="sidebar-link">
                      <i class="fa-solid fa-user pe-2"></i> Clientes
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Módulo de Vehículos -->
          <?php if (tienePermisoGrupo(['marcas', 'vehiculos', 'recepcionVehiculos'], $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#gestionVehiculos"
                aria-expanded="false">
                <i class="fa-solid fa-car pe-2"></i> Gestión de Vehículos
              </a>
              <ul id="gestionVehiculos" class="sidebar-dropdown list-unstyled collapse ms-3" data-bs-parent="#sidebar">
                <?php if (tienePermiso('marcas', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/marcas" class="sidebar-link">
                      <i class="fa-solid fa-tag pe-2"></i> Marcas
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('vehiculos', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/vehiculos" class="sidebar-link">
                      <i class="fa-solid fa-car-side pe-2"></i> Vehículos
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('recepcionVehiculos', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/recepcionVehiculos" class="sidebar-link">
                      <i class="bi bi-check-all fs-5"></i> <i class="bi bi-car-front-fill pe-2"></i>Recepción Vehículos
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Módulo de Compras -->
          <?php if (tienePermisoGrupo(['oc', 'compras'], $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#gestionCompras"
                aria-expanded="false">
                <i class="fa-solid fa-shopping-cart pe-2"></i> Gestión de Compras
              </a>
              <ul id="gestionCompras" class="sidebar-dropdown list-unstyled collapse ms-4" data-bs-parent="#sidebar">
                <?php if (tienePermiso('oc', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/oc" class="sidebar-link">
                      <i class="fa-solid fa-file pe-2"></i> Orden de compra
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('compras', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/compras" class="sidebar-link">
                      <i class="fa-solid fa-box pe-2"></i> Compras
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Módulo de Cotización -->
          <?php if (tienePermisoGrupo(['formatoCotizacion', 'cotizacion'], $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#cotizacionRequisitos"
                aria-expanded="false">
                <i class="fa-solid fa-clipboard-list pe-2"></i> Cotización y Requisitos
              </a>
              <ul id="cotizacionRequisitos" class="sidebar-dropdown list-unstyled collapse ms-4"
                data-bs-parent="#sidebar">
                <?php if (tienePermiso('formatoCotizacion', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/formatoCotizacion" class="sidebar-link">
                      <i class="fa-solid fa-file-alt pe-2"></i> Requisitos
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('cotizacion', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/cotizacion" class="sidebar-link">
                      <i class="fa-solid fa-calculator pe-2"></i> Cotización
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Contratos -->
          <?php if (tienePermiso('contratos', $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="/contratos" class="sidebar-link">
                <i class="bi bi-journal-text pe-2"></i> Contratos
              </a>
            </li>
          <?php endif; ?>

          <!-- Vehículos al Contado -->
          <?php if (tienePermiso('vehiculosAlContado', $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="/vehiculosAlContado" class="sidebar-link">
                <i class="fa-solid fa-car-side pe-2"></i> Vehículos al contado
              </a>
            </li>
          <?php endif; ?>

          <!-- Usuarios -->
          <?php if (tienePermiso('usuarios', $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="/usuarios" class="sidebar-link">
                <i class="fa-solid fa-users-cog pe-2"></i> Gestión de Usuarios
              </a>
            </li>
          <?php endif; ?>

          <!-- Módulo de Crédito y Caja -->
          <?php if (tienePermisoGrupo(['caja', 'creditos', 'egreso', 'arqueoCaja'], $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#gestionCreditoCaja"
                aria-expanded="false">
                <i class="fa-solid fa-money-bill pe-2"></i> Gestión de Crédito y Caja
              </a>
              <ul id="gestionCreditoCaja" class="sidebar-dropdown list-unstyled collapse ms-4" data-bs-parent="#sidebar">
                <?php if (tienePermiso('caja', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/caja" class="sidebar-link">
                      <i class="fa-solid fa-cash-register pe-2"></i> Caja
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('creditos', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/creditos" class="sidebar-link">
                      <i class="fa-solid fa-credit-card pe-2"></i> Crédito
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('egreso', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/egreso" class="sidebar-link">
                      <i class="bi bi-door-open pe-2"></i> Egresos
                    </a>
                  </li>
                <?php endif; ?>
                <?php if (tienePermiso('arqueoCaja', $modulosPermitidos)): ?>
                  <li class="sidebar-item">
                    <a href="/arqueoCaja" class="sidebar-link">
                      <i class="bi bi-piggy-bank-fill pe-2"></i> Arqueo
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Módulo de Cobranza -->
          <?php if (tienePermiso('cobranza', $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#gestionCobranza"
                aria-expanded="false">
                <i class="fa-solid fa-money-check-alt pe-2"></i> Gestión de Cobranza
              </a>
              <ul id="gestionCobranza" class="sidebar-dropdown list-unstyled collapse ms-4" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                  <a href="/Cobranza" class="sidebar-link">
                    <i class="fa-solid fa-money-bill-wave pe-2"></i> Cobranza
                  </a>
                </li>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Módulo de Autenticación (solo para usuarios con permiso) -->
          <?php if (tienePermiso('auth', $modulosPermitidos)): ?>
            <li class="sidebar-item">
              <a href="#" class="sidebar-link collapsed" data-bs-target="#auth" data-bs-toggle="collapse"
                aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i> Auth
              </a>
              <ul id="auth" class="sidebar-dropdown list-unstyled collapse ms-4" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                  <a href="/createAccount" class="sidebar-link">
                    <i class="fa-solid fa-user-plus pe-2"></i>Registrar cuenta
                  </a>
                </li>
              </ul>
            </li>
          <?php endif; ?>
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

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const currentPath = window.location.pathname;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const collapseElements = document.querySelectorAll('.collapse');

            const rutasCobranza = ['/Cobranza', '/Recordatorios', '/Vencidos'];
            const esRutaCobranza = rutasCobranza.some(ruta => currentPath.startsWith(ruta));

            sidebarLinks.forEach(link => {
              const href = link.getAttribute('href');

              if (href && href !== '#' && currentPath.startsWith(href)) {
                link.classList.add('active');
                const parentCollapse = link.closest('.sidebar-item').querySelector('.collapse');
                if (parentCollapse) {
                  parentCollapse.classList.add('show');
                }
              }

              if (esRutaCobranza && href === '/Cobranza') {
                link.classList.add('active');
                const cobranzaMenu = document.getElementById('gestionCobranza');
                if (cobranzaMenu) {
                  cobranzaMenu.classList.add('show');
                }
              }
            });

            document.body.addEventListener('shown.bs.collapse', function (event) {
              const openedCollapseId = event.target.id;
              localStorage.setItem(openedCollapseId, 'open');
            });

            document.body.addEventListener('hidden.bs.collapse', function (event) {
              const hiddenCollapseId = event.target.id;
              localStorage.setItem(hiddenCollapseId, 'closed');
            });

            collapseElements.forEach(collapse => {
              const collapseId = collapse.id;
              if (localStorage.getItem(collapseId) === 'open') {
                const bsCollapse = new bootstrap.Collapse(collapse, {
                  toggle: false
                });
                bsCollapse.show();
              }
            });
          });
        </script>