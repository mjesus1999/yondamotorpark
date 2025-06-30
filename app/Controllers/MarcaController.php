<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
//use App\Models\Product;

class MarcaController extends Controller
{
  //private Product $productModel;

  public function __construct()
  {
    //$this->productModel = new Product();
  }

  public function index(): void
  {
    //$products = $this->productModel->getAll();
    //$this->view('products.index', ['products' => $products]);
    $this->view('marcas.index');
  }


}