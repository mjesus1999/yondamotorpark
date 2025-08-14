<?php
// app/Core/Database.php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
  private static ?PDO $instance = null;
  private array $config;

  private function __construct()
  {
    //$this->config = require __DIR__ . '/../Config/database.php';

    // primero getenv(), luego $_ENV, luego valor por defecto.
    $this->config = [
      'host' => getenv('DB_HOST') !== false ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? 'localhost'),
      'dbname' => getenv('DB_NAME') !== false ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? ''),
      'user' => getenv('DB_USER') !== false ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? ''),
      'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? ''),
      'charset' => getenv('DB_CHARSET') !== false ? getenv('DB_CHARSET') : ($_ENV['DB_CHARSET'] ?? 'utf8mb4'),
    ];
    /*
    $this->config = [
      'host' => $_ENV['DB_HOST'] ?? 'localhost',
      'dbname' => $_ENV['DB_NAME'] ?? '',
      'user' =>  $_ENV['DB_USER'] ?? '',
      'password' => $_ENV['DB_PASS'] ?? '',
      'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
    ];*/

    $this->connect();
  }

  private function connect()
  {
    $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
      self::$instance = new PDO($dsn, $this->config['user'], $this->config['password'], $options);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int) $e->getCode());
    }
  }

  public static function getInstance(): PDO
  {
    if (self::$instance === null) {
      new self(); // Llama al constructor para establecer la conexión
    }
    return self::$instance;
  }
}