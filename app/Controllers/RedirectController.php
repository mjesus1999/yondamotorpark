<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Redirecciones 301 desde URLs antiguas o mal escritas hacia rutas canónicas.
 */
class RedirectController extends Controller
{
    public function ordenCompraEmitido(): void
    {
        $this->redirectPermanente('/oc/listar/emitido');
    }

    public function ordenCompraEstado(string $estado): void
    {
        $this->redirectPermanente('/oc/listar/' . rawurlencode($estado));
    }

    public function egresosEstado(string $estado): void
    {
        $this->redirectPermanente('/egreso/listar/' . rawurlencode($estado));
    }

    private function redirectPermanente(string $ruta): void
    {
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        $destino = $ruta . ($qs !== '' ? '?' . $qs : '');
        header('Location: ' . $destino, true, 301);
        exit;
    }
}
