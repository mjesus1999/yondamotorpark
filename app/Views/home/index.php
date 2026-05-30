<?php
use App\Config\CajaRoutes;

include __DIR__ . '/../layout/header.php';

$primerNombre = '';
if (!empty($user['nombres'])) {
  $primerNombre = explode(' ', trim($user['nombres']))[0];
}
$stats = $stats ?? [];
$dbOk = !empty($stats['db_disponible']);
$mods = $modulosPermitidos ?? [];

$mostrarKpiCotizacion = tienePermiso('cotizacion', $mods);
$mostrarKpiCobranza = tienePermiso('cobranza', $mods) || tienePermiso('caja', $mods);
$mostrarKpis = $mostrarKpiCotizacion || $mostrarKpiCobranza;
?>

<div class="container-fluid home-dashboard py-2">
  <div class="welcome-card mb-4">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1 class="h3 mb-2 text-white">
          Hola<?= $primerNombre ? ', ' . htmlspecialchars($primerNombre) : '' ?> 👋
        </h1>
        <p class="mb-0 text-secondary">
          Panel de <strong>Yonda Motor Park</strong>. Usa el menú lateral o los accesos rápidos según tu área.
        </p>
        <?php if (!$dbOk): ?>
          <p class="small text-warning mt-2 mb-0">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Los indicadores se mostrarán en cero hasta que la base de datos esté configurada.
          </p>
        <?php endif; ?>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <span class="badge bg-primary">Sistema operativo</span>
      </div>
    </div>
  </div>

  <?php if ($mostrarKpis): ?>
  <div class="row g-3 mb-4">
    <?php if ($mostrarKpiCotizacion): ?>
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon kpi-icon--orange"><i class="bi bi-file-earmark-text"></i></div>
          <div>
            <div class="text-muted small">Cotizaciones pendientes</div>
            <div class="h4 mb-0 text-white"><?= (int) ($stats['cotizaciones_pendientes'] ?? 0) ?></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if ($mostrarKpiCobranza): ?>
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon kpi-icon--red"><i class="bi bi-exclamation-circle"></i></div>
          <div>
            <div class="text-muted small">Contratos con vencidos</div>
            <div class="h4 mb-0 text-white"><?= (int) ($stats['contratos_vencidos'] ?? 0) ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon kpi-icon--blue"><i class="bi bi-calendar-event"></i></div>
          <div>
            <div class="text-muted small">Por vencer (3 días)</div>
            <div class="h4 mb-0 text-white"><?= (int) ($stats['por_vencer_3dias'] ?? 0) ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon kpi-icon--green"><i class="bi bi-cash-stack"></i></div>
          <div>
            <div class="text-muted small">Total por cobrar</div>
            <div class="h5 mb-0 text-white">S/ <?= number_format((float) ($stats['total_por_cobrar'] ?? 0), 2) ?></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php
  $hayAccesos = tienePermisoGrupo([
    'cotizacion', 'formatoCotizacion', 'caja', 'cobranza', 'creditos', 'egreso', 'arqueoCaja',
    'clientes', 'vehiculos', 'recepcionVehiculos', 'oc', 'compras', 'contratos', 'marcas',
    'concesionarios', 'locales', 'usuarios',
  ], $mods);
  ?>
  <?php if ($hayAccesos): ?>
  <h2 class="h6 text-uppercase text-muted mb-3">Accesos rápidos</h2>
  <div class="row g-3">
    <?php if (tienePermiso('cotizacion', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/cotizacion/P" class="quick-link-card">
        <i class="bi bi-calculator d-block"></i>
        <strong>Cotización</strong>
        <div class="small text-muted">Nuevas y pendientes</div>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('caja', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="<?= CajaRoutes::LISTA_CONTRATOS ?>" class="quick-link-card">
        <i class="bi bi-cash-coin d-block"></i>
        <strong>Caja (lista ACT)</strong>
        <div class="small text-muted">Contratos activos</div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="<?= CajaRoutes::BUSCAR_DOCUMENTO ?>" class="quick-link-card">
        <i class="bi bi-person-vcard d-block"></i>
        <strong>Buscar DNI / RUC</strong>
        <div class="small text-muted">Clientes y Excel</div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="<?= CajaRoutes::COBRO_CONCEPTOS ?>" class="quick-link-card">
        <i class="bi bi-cash-stack d-block"></i>
        <strong>Cobro por conceptos</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('cobranza', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/Cobranza" class="quick-link-card">
        <i class="bi bi-wallet2 d-block"></i>
        <strong>Cobranza</strong>
        <div class="small text-muted">Deudas activas</div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/Vencidos" class="quick-link-card">
        <i class="bi bi-clock-history d-block"></i>
        <strong>Vencidos</strong>
        <div class="small text-muted"><?= (int) ($stats['total_morosos'] ?? 0) ?> morosos</div>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('creditos', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/creditos" class="quick-link-card">
        <i class="bi bi-credit-card d-block"></i>
        <strong>Crédito</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('contratos', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/contratos" class="quick-link-card">
        <i class="bi bi-file-earmark-text d-block"></i>
        <strong>Contratos</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('clientes', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/clientes" class="quick-link-card">
        <i class="bi bi-people d-block"></i>
        <strong>Clientes</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('vehiculos', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/vehiculos" class="quick-link-card">
        <i class="bi bi-car-front d-block"></i>
        <strong>Vehículos</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('recepcionVehiculos', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/recepcionVehiculos" class="quick-link-card">
        <i class="bi bi-truck d-block"></i>
        <strong>Recepción vehículos</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('oc', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/oc/listar/emitido" class="quick-link-card">
        <i class="bi bi-cart-check d-block"></i>
        <strong>Órdenes de compra</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('compras', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/compras" class="quick-link-card">
        <i class="bi bi-bag-check d-block"></i>
        <strong>Compras</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('egreso', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/egreso" class="quick-link-card">
        <i class="bi bi-receipt d-block"></i>
        <strong>Egresos</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('arqueoCaja', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/arqueoCaja" class="quick-link-card">
        <i class="bi bi-piggy-bank d-block"></i>
        <strong>Arqueo de caja</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('marcas', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/marcas" class="quick-link-card">
        <i class="bi bi-tags d-block"></i>
        <strong>Marcas</strong>
      </a>
    </div>
    <?php endif; ?>
    <?php if (tienePermiso('usuarios', $mods)): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="/usuarios" class="quick-link-card">
        <i class="bi bi-person-gear d-block"></i>
        <strong>Usuarios</strong>
      </a>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
