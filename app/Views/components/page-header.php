<?php
/**
 * Cabecera de página unificada.
 *
 * Variables esperadas:
 * - $pageHeaderBreadcrumbs: array de ['label' => string, 'url' => string|null]
 * - $pageHeaderActionsHtml: string HTML opcional (botones / dropdown)
 * - $pageHeaderClass: string clase extra opcional (ej. caja-ui)
 */
$pageHeaderBreadcrumbs = $pageHeaderBreadcrumbs ?? [['label' => 'Inicio', 'url' => '/']];
$pageHeaderActionsHtml = $pageHeaderActionsHtml ?? '';
$pageHeaderClass = $pageHeaderClass ?? '';
?>
<div class="mp-page-header <?= htmlspecialchars($pageHeaderClass) ?>">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <?php foreach ($pageHeaderBreadcrumbs as $i => $crumb): ?>
        <?php
        $isLast = $i === count($pageHeaderBreadcrumbs) - 1;
        $label = htmlspecialchars($crumb['label'] ?? '');
        $url = $crumb['url'] ?? null;
        ?>
        <?php if ($isLast): ?>
          <li class="breadcrumb-item active" aria-current="page"><?= $label ?></li>
        <?php elseif ($url): ?>
          <li class="breadcrumb-item"><a href="<?= htmlspecialchars($url) ?>"><?= $label ?></a></li>
        <?php else: ?>
          <li class="breadcrumb-item"><?= $label ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ol>
  </nav>
  <?php if ($pageHeaderActionsHtml !== ''): ?>
    <div class="mp-page-header-actions">
      <?= $pageHeaderActionsHtml ?>
    </div>
  <?php endif; ?>
</div>
