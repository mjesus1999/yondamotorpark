<?php
// app/Controllers/ForCotController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FormatoCotizacion;

class ForCotController
{
    private FormatoCotizacion $formatoModel;

    public function __construct()
    {
        $this->formatoModel = new FormatoCotizacion();
    }

    public function getAllFormato()
    {
        $formatos = $this->formatoModel->getAll();
    }
}