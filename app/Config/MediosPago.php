<?php

namespace App\Config;

/**
 * Catálogo de medios de pago usados en caja, cronograma y cobros.
 */
class MediosPago
{
    public const EFECTIVO = 'Efectivo';
    public const YAPE = 'Yape';
    public const TRANSFERENCIA = 'Transferencia Bancaria';
    public const INTERBANCARIO = 'Interbancario';
    public const PLIN = 'Plin';

    /** @return array<string, string> valor => etiqueta visible */
    public static function opciones(): array
    {
        return [
            self::EFECTIVO => self::EFECTIVO,
            self::YAPE => self::YAPE,
            self::TRANSFERENCIA => self::TRANSFERENCIA,
            self::INTERBANCARIO => self::INTERBANCARIO,
            self::PLIN => self::PLIN,
        ];
    }

    public static function requiereTransaccion(string $medio): bool
    {
        return $medio !== '' && $medio !== self::EFECTIVO;
    }

    public static function requiereCuenta(string $medio): bool
    {
        return $medio === self::TRANSFERENCIA || $medio === self::INTERBANCARIO;
    }

    /** @return 'Cuenta'|'CCI'|null */
    public static function tipoCuentaParaMedio(string $medio): ?string
    {
        if ($medio === self::TRANSFERENCIA) {
            return 'Cuenta';
        }
        if ($medio === self::INTERBANCARIO) {
            return 'CCI';
        }
        return null;
    }
}
