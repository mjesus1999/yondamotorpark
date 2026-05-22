<?php

namespace App\Config;

/**
 * Rutas públicas del módulo Caja (deben coincidir con menú y breadcrumbs).
 */
final class CajaRoutes
{
    /** Lista de contratos activos (ACT) — sp_getAll_contratos_caja */
    public const LISTA_CONTRATOS = '/caja';

    /** Buscar cliente por DNI o RUC y ver contratos / registro Excel */
    public const BUSCAR_DOCUMENTO = '/caja/buscar-documento';

    /** Boleta y cobro por conceptos (Excel, sin contrato ACT, etc.) */
    public const COBRO_CONCEPTOS = '/caja/cobro-conceptos';

    /** @deprecated Usar BUSCAR_DOCUMENTO — redirección 301 en router */
    public const LEGACY_BUSCAR_CLIENTE = '/caja/buscar-cliente';

    /** @deprecated Usar COBRO_CONCEPTOS — redirección 301 en router */
    public const LEGACY_PAGOS_DENOMINACION = '/caja/pagos/denominacion';

    public static function cobroConceptosConDni(?string $dni): string
    {
        $base = self::COBRO_CONCEPTOS;
        if ($dni === null || $dni === '') {
            return $base;
        }
        return $base . '?dni=' . rawurlencode($dni);
    }
}
