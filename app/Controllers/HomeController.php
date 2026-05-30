<?php

/**
 * Controlador de inicio
 * app/Controllers/HomeController.php
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cobranza;
use App\Models\Cotizacion;
use App\Models\Credito;

class HomeController extends Controller
{
  public function index(): void
  {
    $this->authRequired();

    $user = $_SESSION['user'] ?? [];
    $stats = [
      'cotizaciones_pendientes' => 0,
      'contratos_vencidos' => 0,
      'por_vencer_3dias' => 0,
      'total_por_cobrar' => 0.0,
      'total_morosos' => 0,
      'db_disponible' => true,
    ];

    try {
      $cotizacion = new Cotizacion();
      $pendientes = $cotizacion->getAll('P');
      $stats['cotizaciones_pendientes'] = is_array($pendientes) ? count($pendientes) : 0;
    } catch (\Throwable $e) {
      $stats['db_disponible'] = false;
      error_log('Home KPI cotizaciones: ' . $e->getMessage());
    }

    try {
      $cobranza = new Cobranza();
      $cob = $cobranza->getEstadisticasContratos();
      $stats['contratos_vencidos'] = (int) ($cob['contratos_con_vencidos'] ?? 0);
      $stats['por_vencer_3dias'] = (int) ($cob['por_vencer_3dias'] ?? 0);
      $stats['total_por_cobrar'] = (float) ($cob['total_por_cobrar'] ?? 0);
    } catch (\Throwable $e) {
      $stats['db_disponible'] = false;
      error_log('Home KPI cobranza: ' . $e->getMessage());
    }

    try {
      $credito = new Credito();
      $morosos = $credito->getEstadisticasMorosos();
      $stats['total_morosos'] = (int) ($morosos['total_morosos'] ?? 0);
    } catch (\Throwable $e) {
      $stats['db_disponible'] = false;
      error_log('Home KPI morosos: ' . $e->getMessage());
    }

    $this->view('home.index', [
      'user' => $user,
      'stats' => $stats,
    ]);
  }
}
