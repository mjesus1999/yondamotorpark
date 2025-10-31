<?php

namespace App\Config;

/**
 * Clase ConceptosPago
 * 
 * Catálogo centralizado de identificadores de conceptos de pago del sistema.
 * Define constantes inmutables que representan los tipos de pago disponibles
 * en la aplicación, facilitando su referencia consistente
 */
class ConceptosPago
{
      /**
       * ID del concepto "Pago Inicial"
       * 
       * Representa el pago de cuota inicial o enganche en una compra a crédito.
       * Este concepto se utiliza cuando el cliente realiza el primer desembolso
       * al adquirir un vehículo mediante financiamiento, típicamente entre el
       * 20% y 40% del valor total.
       * 
       * @var int
       */
      const int INICIAL_ID = 2;

      /**
       * ID del concepto "Pago de Contado"
       * 
       * Representa el pago completo y único por la compra de un vehículo sin
       * financiamiento. Este concepto se aplica cuando el cliente realiza el
       * pago total del 100% del valor del bien en una sola transacción, sin
       * generar cuotas ni cronograma de pagos posteriores.
       * 
       * @var int
       */
      const int CONTADO_ID = 1;
}