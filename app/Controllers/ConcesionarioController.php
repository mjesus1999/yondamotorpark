<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Concesionario;

class ConcesionarioController extends Controller {

    private  Concesionario $consecionarioModel;

    public function __construct()
    {
        $this->consecionarioModel = new Concesionario();
    }

    public function index():void {
        $consecionarios = $this->consecionarioModel->getAll();
        $this->view('concesionarios.index', ['concesionarios' => $consecionarios]);

    }
    public function create():void {
        $this->view('concesionarios.create');
    }






    // METODOS PARA LAS APIS:


    // Retorna los datos de un Concesioanrio buscado mediante la api de Sunat
   public function searchRucSunat(): void {
    if (!isset($_GET['ruc']) || strlen($_GET['ruc']) != 11) {
        http_response_code(400);
        echo json_encode(['error' => 'RUC inválido']);
        return;
    }

    $ruc = $_GET['ruc'];
    $token = 'apis-token-10575.ycXCIbBCEoM8ufZplATB5oIDwVTo7mzp';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc/full?numero=' . $ruc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Referer: http://apis.net.pe/api-ruc',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    header('Content-Type: application/json; charset=utf-8');
    echo $response;
}

 


}