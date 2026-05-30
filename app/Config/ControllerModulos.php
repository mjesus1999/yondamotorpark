<?php

/**
 * Módulo RBAC por controlador (debe coincidir con tabla accesos.modulo).
 * null = solo requiere sesión activa, sin módulo específico.
 *
 * @return array<class-string, string|string[]|null>
 */
return [
    'App\\Controllers\\OrdenCompraController' => 'oc',
    'App\\Controllers\\CompraController' => 'compras',
    'App\\Controllers\\CajaController' => 'caja',
    'App\\Controllers\\ComprobantesController' => 'caja',
    'App\\Controllers\\ComprobanteNubefactController' => 'caja',
    'App\\Controllers\\CotizacionController' => 'cotizacion',
    'App\\Controllers\\NotaCreditoController' => 'caja',
    'App\\Controllers\\PagoCronogramaController' => 'caja',
    'App\\Controllers\\ConceptoPagoController' => 'caja',
    'App\\Controllers\\VehiculoController' => 'vehiculos',
    'App\\Controllers\\HomeController' => null,
    'App\\Controllers\\EgresoController' => 'egreso',
    'App\\Controllers\\CreditoController' => 'creditos',
    'App\\Controllers\\ContratoController' => 'contratos',
    'App\\Controllers\\EmpresaController' => 'clientes',
    'App\\Controllers\\ArqueoCajaController' => 'arqueoCaja',
    'App\\Controllers\\EntregaDineroController' => 'arqueoCaja',
    'App\\Controllers\\PersonaController' => 'clientes',
    'App\\Controllers\\ClienteController' => 'clientes',
    'App\\Controllers\\CobranzaController' => 'cobranza',
    'App\\Controllers\\ConcesionarioController' => 'concesionarios',
    'App\\Controllers\\DetalleOCController' => 'oc',
    'App\\Controllers\\PagosOCController' => 'oc',
    'App\\Controllers\\FichaSolicitudController' => 'cotizacion',
    'App\\Controllers\\ForCotController' => 'formatoCotizacion',
    'App\\Controllers\\LocalController' => 'locales',
    'App\\Controllers\\MarcaController' => 'marcas',
    'App\\Controllers\\ModeloController' => ['marcas', 'vehiculos', 'cotizacion'],
    'App\\Controllers\\TipoVehiculoController' => ['marcas', 'vehiculos', 'cotizacion'],
    'App\\Controllers\\MotorparkController' => 'vehiculos',
    'App\\Controllers\\TiendaController' => 'concesionarios',
    'App\\Controllers\\UbigeoController' => null,
    'App\\Controllers\\ProductController' => 'vehiculos',
];
