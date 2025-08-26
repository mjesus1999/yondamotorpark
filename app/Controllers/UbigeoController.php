<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ubigeo;

class UbigeoController extends Controller
{
    private Ubigeo $ubigeoModel;

    public function __construct()
    {
        $this->ubigeoModel = new Ubigeo();
    }

    /**
     * Obtiene y retorna todos los departamentos como JSON.
     * @return void
     */
    public function departamentos(): void
    {
        $departamentos = $this->ubigeoModel->getAllDepartamentos();


        header('Content-Type: application/json');

        http_response_code(200);

        echo json_encode($departamentos);

        exit;
    }

    /**
     * Obtiene y retorna las provincias de un departamento específico como JSON.
     * @param int $iddepartamento El ID del departamento.
     * @return void
     */
    public function provincias(int $iddepartamento): void
    {
        // Validación básica del ID
        if ($iddepartamento <= 0) {
            header('Content-Type: application/json');
            http_response_code(400); // 400 Bad Request
            echo json_encode(['error' => 'ID de departamento inválido']);
            exit;
        }

        $provincias = $this->ubigeoModel->getAllProvincias($iddepartamento);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($provincias);
        exit;
    }

    /**
     * Obtiene y retorna los distritos de una provincia específica como JSON.
     * @param int $idprovincia El ID de la provincia.
     * @return void
     */
    public function distritos(int $idprovincia): void
    {
        // Validación básica del ID
        if ($idprovincia <= 0) {
            header('Content-Type: application/json');
            http_response_code(400); // 400 Bad Request
            echo json_encode(['error' => 'ID de provincia inválido']);
            exit;
        }

        $distritos = $this->ubigeoModel->getAllDistritos($idprovincia);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($distritos);
        exit;
    }

    public function getAllDistritosAll(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->ubigeoModel->getAllDistritosAll());
    }
}
