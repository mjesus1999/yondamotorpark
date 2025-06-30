<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
  protected array $routes = [];

  public function add(string $method, string $uri, string $controller, string $action): void
  {
    $this->routes[] = [
      'method' => strtoupper($method),
      'uri' => $uri,
      'controller' => $controller,
      'action' => $action
    ];
  }

  public function dispatch(): void
  {
    // === Lógica para limpiar la URI, como ya habíamos hecho para Virtual Hosts ===
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // Si usas un Virtual Host, $basePath debería ser '/', de lo contrario, será el subdirectorio.
    // str_replace(basename($scriptName), '', $scriptName) se vuelve "" o "/" si es un VirtualHost
    // Esto maneja ambos escenarios: VirtualHost (basePath = /) o Subdirectorio (basePath = /mi_proyecto/public/)
    $basePath = str_replace(basename($scriptName), '', $scriptName);

    // Elimina la URI base del requestUri
    // Ejemplo: /mi_proyecto/public/products/edit/1 -> /products/edit/1
    $uri = substr($requestUri, strlen($basePath));

    // Elimina cualquier parámetro de consulta
    $uri = strtok($uri, '?');

    // Asegura que la URI siempre empiece con '/' y no termine con '/' (excepto la raíz)
    if (empty($uri)) {
      $uri = '/';
    } elseif ($uri[0] !== '/') {
      $uri = '/' . $uri;
    }
    $uri = rtrim($uri, '/');
    if (empty($uri)) {
      $uri = '/';
    }
    // ===================================================================

    $method = $_SERVER['REQUEST_METHOD'];

    foreach ($this->routes as $route) {
      // Convierte la URI de la ruta en una expresión regular
      // '{id}' se convierte en '([0-9]+)' para capturar uno o más dígitos
      // Puedes ajustar '([0-9]+)' a '([^/]+)' si esperas cualquier carácter excepto '/'
      $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([0-9]+)', $route['uri']);
      $pattern = '#^' . $pattern . '$#'; // Asegura que la expresión coincida con la cadena completa

      // Intenta hacer coincidir la URI actual con el patrón de la ruta
      if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // Elimina el primer elemento, que es la coincidencia completa de la cadena.
        // $matches ahora contiene solo los valores capturados (ej. '1' del ID).

        $controllerName = 'App\\Controllers\\' . $route['controller'];
        $actionName = $route['action'];

        if (class_exists($controllerName)) {
          $controller = new $controllerName();
          if (method_exists($controller, $actionName)) {
            // Llama al método del controlador, pasando los parámetros capturados
            call_user_func_array([$controller, $actionName], $matches);
            return; // Ruta encontrada y procesada, salir
          }
        }
      }
    }

    // Si ninguna ruta coincide
    http_response_code(404);
    require __DIR__ . '/../Views/errors/404.php';
  }
}