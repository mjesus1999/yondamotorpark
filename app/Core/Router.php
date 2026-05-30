<?php
// app/Core/Router.php

namespace App\Core;

/**
 * Clase Router
 * 
 * Maneja el enrutamiento de la aplicación, permitiendo registrar rutas
 * y despachar las peticiones HTTP a los controladores correspondientes.
 * 
 */
class Router
{

  /**
   * Almacena todas las rutas registradas en la aplicación.
   * 
   * Cada ruta es un array asociativo con las claves: method, uri, controller y action.
   * @var array
   */
  protected array $routes = [];

  /**
   * Registra una nueva ruta en el sistema de enrutamiento.
   * 
   * Permite definir qué controlador y acción deben ejecutarse cuando
   * se reciba una petición HTTP con el método y URI especificados.
   * 
   * @param string $method El método HTTP (GET, POST, PUT, DELETE)
   * @param string $uri La URI de la ruta
   * @param string $controller El nombre del controlador
   * @param string $action El nombre del método del controlador que se ejecutará
   * @return void
   */
  public function add(string $method, string $uri, string $controller, string $action): void
  {
    $this->routes[] = [
      'method' => strtoupper($method),
      'uri' => $uri,
      'controller' => $controller,
      'action' => $action
    ];
  }

  /**
   * Despacha la petición HTTP actual a la ruta correspondiente.
   * 
   * Este método procesa la URI de la petición, limpia parámetros de consulta,
   * busca una ruta coincidente y ejecuta el controlador y acción asociados.
   * 
   * 1. Limpia y normaliza la URI de la petición
   * 2. Obtiene el método HTTP (GET, POST,.......)
   * 3. Busca una ruta que coincida con el método y URI
   * 4. Extrae los parámetros dinámicos de la URI
   * 5. Instancia el controlador y ejecuta la acción
   * 6. Si no encuentra una ruta, muestra error 404
   * 
   * @return void 
   */
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
      $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $route['uri']);
      $pattern = '#^' . $pattern . '$#'; // Asegura que la expresión coincida con la cadena completa

      // Intenta hacer coincidir la URI actual con el patrón de la ruta
      if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // Elimina el primer elemento, que es la coincidencia completa de la cadena.
        // $matches ahora contiene solo los valores capturados (ej. '1' del ID).

        $controllerName = 'App\\Controllers\\' . $route['controller'];
        $actionName = $route['action'];

        if (!class_exists($controllerName)) {
          continue;
        }

        $controller = new $controllerName();
        if (!method_exists($controller, $actionName)) {
          error_log("Router: método inexistente {$controllerName}::{$actionName} para {$method} {$uri}");
          continue;
        }

        call_user_func_array([$controller, $actionName], $matches);
        return;
      }
    }

    // Si ninguna ruta coincide
    http_response_code(404);
    require __DIR__ . '/../Views/errors/404.php';
  }
}