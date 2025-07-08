<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Colaborador
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(int $idContrato, string $usernick, string $passwordHash): int
    {
        $sql = "INSERT INTO colaboradores
                  (idcontratolaboral, usernick, userpassword)
                VALUES
                  (:idcontrato, :usernick, :userpassword)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idcontrato' => $idContrato,
                ':usernick' => $usernick,
                ':userpassword' => $passwordHash,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return -1;
        }
    }
}
