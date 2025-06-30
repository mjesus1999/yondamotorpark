<?php

//Para un proyecto real, considera usar Composer para el autocargado PSR-4.

// app/Core/Autoloader.php

class Autoloader
{
  public static function register()
  {
    spl_autoload_register(function ($class) {
      $prefix = 'App\\'; // Tu namespace base
      $base_dir = __DIR__ . '/../'; // Directorio base de 'app/'

      // ¿La clase usa el prefijo del namespace?
      $len = strlen($prefix);
      if (strncmp($prefix, $class, $len) !== 0) {
        // No, muévete al siguiente autoloader registrado
        return;
      }

      // Obtén el nombre de clase relativo
      $relative_class = substr($class, $len);

      // Reemplaza los separadores de namespace por separadores de directorio,
      // añade .php
      $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

      // Si el archivo existe, requerirlo
      if (file_exists($file)) {
        require $file;
      }
    });
  }
}