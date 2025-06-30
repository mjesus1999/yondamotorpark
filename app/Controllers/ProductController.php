<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
  private Product $productModel;

  public function __construct()
  {
    $this->productModel = new Product();
  }

  public function index(): void
  {
    $products = $this->productModel->getAll();
    $this->view('products.index', ['products' => $products]);
  }

  public function search(): void
  {
    $this->view('products.search');
  }

  public function create(): void
  {
    //$marcaModel = new Marca();
    //$marcas = $marcaModel->getAll();

    $this->view('products.create');
  }

  public function store(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = trim($_POST['name'] ?? '');
      $category = trim($_POST['category'] ?? '');
      $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

      if ($name && $category && $price !== false) {
        if ($this->productModel->create($name, $category, $price)) {
          $this->redirect('/products');
        } else {
          // Manejar error de inserción
          $this->view('products.create', ['error' => 'Error al crear el producto.']);
        }
      } else {
        $this->view('products.create', ['error' => 'Todos los campos son obligatorios y el precio debe ser un número válido.']);
      }
    }
  }

  public function edit(int $id): void
  {
    $product = $this->productModel->getById($id);
    if ($product) {
      $this->view('products.edit', ['product' => $product]);
    } else {
      http_response_code(404);
      $this->view('errors.404');
    }
  }

  public function update(int $id): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = trim($_POST['name'] ?? '');
      $category = trim($_POST['category'] ?? '');
      $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

      if ($name && $category && $price !== false) {
        if ($this->productModel->update($id, $name, $category, $price)) {
          $this->redirect('/products');
        } else {
          $product = $this->productModel->getById($id); // Recargar para la vista de error
          $this->view('products.edit', ['product' => $product, 'error' => 'Error al actualizar el producto.']);
        }
      } else {
        $product = $this->productModel->getById($id); // Recargar para la vista de error
        $this->view('products.edit', ['product' => $product, 'error' => 'Todos los campos son obligatorios y el precio debe ser un número válido.']);
      }
    }
  }

  public function delete(int $id): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Usamos POST para la eliminación por seguridad
      if ($this->productModel->delete($id)) {
        $this->redirect('/products');
      } else {
        // Puedes redirigir con un mensaje de error o mostrar una vista de error
        $this->redirect('/products?error=delete_failed');
      }
    } else {
      // Si se intenta acceder directamente por GET, redirigir o mostrar 405
      http_response_code(405);
      $this->view('errors.405'); // Podrías crear una vista para el error 405
    }
  }

  public function searchById(int $id): void{
    header('Content-Type: application/json');
    $product = $this->productModel->getById($id);

    if ($product){
      echo json_encode(['success' => true, 'product' => $product]);
    }else{
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    }
    exit();
  }
}