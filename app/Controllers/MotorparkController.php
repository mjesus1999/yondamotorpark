<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\Motorpark;

class MotorparkController extends Controller
{
    private Motorpark $motorParkModel;

    public function __construct()
    {
        $this->motorParkModel = new Motorpark();
    }

    public function index(): void
    {
        // $locales = $this->motorParkModel->getMotorPark();
        // $this->view('locales.index', ['locales' => $locales]);
    }


    // SE USARA PARA API:

    public function getMotorPark(): void{
    header('Content-Type: application/json');
    $this->authRequired();
    $motorpark = $this->motorParkModel->getMotorPark();

    if ($motorpark){
      echo json_encode(['success' => true, 'motorpark' => $motorpark]);
    }else{
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'No se encontro motorpark']);
    }
    exit();
  }


}