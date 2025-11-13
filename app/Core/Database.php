<?php
// app/Core/Database.php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database
 */
class Database
{

  /**
   * Instancia única de PDO
   * 
   * Almacena la conexión activa a la base de datos. Es estática para
   * mantener una única instancia compartida por toda la aplicación.
   * Se inicializa como null y se crea solo cuando se solicita por
   * primera vez.
   * 
   * @var 
   */
  private static ?PDO $instance = null;

  /**
   * Configuración de la base de datos
   * 
   * Array asociativo que almacena los parámetros de conexión extraídos
   * de las variables de entorno. Incluye host, nombre de base de datos,
   * credenciales de usuario y conjunto de caracteres.
   * 
   * @var array <string, string> Parámetros de configuración
   */
  private array $config;

  /**
   * Constructor privado (patrón Singleton)
   * 
   * Inicializa la configuración de la base de datos leyendo variables de
   * entorno. Utiliza una estrategia de fallback en dos niveles:
   * 
   * 1. Intenta leer con getenv() (compatible con Apache/Nginx)
   * 2. Si falla, lee desde $_ENV (compatible con CLI/Docker)
   * 3. Si ambos fallan, usa valores por defecto
   * 
   * Esta estrategia dual garantiza compatibilidad en diferentes entornos
   * de ejecución 
   * 
   * Variables de entorno esperadas:
   * - DB_HOST: Servidor de base de datos (default: localhost)
   * - DB_NAME: Nombre de la base de datos (default: '')
   * - DB_USER: Usuario de MySQL (default: '')
   * - DB_PASS: Contraseña (default: '')
   * - DB_CHARSET: Codificación (default: utf8mb4)
   * 
   */
  private function __construct()
  {
    //$this->config = require __DIR__ . '/../Config/database.php';

    // $this->config = [
    //   'host' => $_ENV['DB_HOST'] ?? 'localhost',
    //   'dbname' => $_ENV['DB_NAME'] ?? '',
    //   'user' =>  $_ENV['DB_USER'] ?? '',
    //   'password' => $_ENV['DB_PASS'] ?? '',
    //   'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
    // ];
    $this->config = [
      'host' => getenv('DB_HOST') !== false ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? 'localhost'),
      'dbname' => getenv('DB_NAME') !== false ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? ''),
      'user' => getenv('DB_USER') !== false ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? ''),
      'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? ''),
      'charset' => getenv('DB_CHARSET') !== false ? getenv('DB_CHARSET') : ($_ENV['DB_CHARSET'] ?? 'utf8mb4'),
    ];
    $this->connect();
  }

  /**
   * Establece la conexión PDO con la base de datos
   * 
   * Crea una nueva instancia de PDO utilizando los parámetros de configuración
   * cargados desde las variables de entorno. Configura opciones de seguridad
   * y rendimiento optimizadas para producción:
   * 
   * - ERRMODE_EXCEPTION: Lanza excepciones en lugar de warnings/errors
   * - FETCH_ASSOC: Retorna arrays asociativos por defecto
   * - EMULATE_PREPARES: false - Usa prepared statements reales del servidor
   * - STRINGIFY_FETCHES: false - Mantiene tipos de datos nativos (int, float)
   * 
   * @throws \PDOException Si la conexión falla
   * @return void
   */
  private function connect(): void
  {
    $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    try {
      self::$instance = new PDO($dsn, $this->config['user'], $this->config['password'], $options);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int) $e->getCode());
    }
  }

  /**
   * Obtiene la instancia única de conexión PDO (patrón Singleton)
   * 
   * Implementa lazy initialization: la conexión se crea solo cuando
   * se solicita por primera vez. Las peticiones subsecuentes reutilizan
   * la misma instancia, optimizando recursos y evitando múltiples
   * conexiones innecesarias.
   * 
   * Este método es el único punto de acceso público a la conexión PDO
   * 
   * @return PDO|null Instancia única de conexión a la base de datos
   */
  public static function getInstance(): PDO
  {
    if (self::$instance === null) {
      new self(); // Llama al constructor para establecer la conexión
    }
    return self::$instance;
  }
}
