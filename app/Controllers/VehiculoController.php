<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
//use App\Models\Product;

class VehiculoController extends Controller
{
  //private Product $productModel;

  public function __construct()
  {
    //$this->productModel = new Product();
  }

  public function index(): void
  {
    $this->authRequired();
    //$products = $this->productModel->getAll();
    //$this->view('products.index', ['products' => $products]);
    $this->view('vehiculos.index');
  }
  
  public function create(): void
  {
    $this->authRequired();
    $this->view('vehiculos.create');
  }

}