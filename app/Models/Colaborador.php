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

    public function create(int $idContrato, string $usernick, string $passwordHash, string $restr = 'S', ?int $idlocal = null): int
    {
        //Forzar el valor de entrar en restriccion Horaria
        $restr = (strtoupper($restr) === 'N') ? 'N' : 'S';

        $sql = "INSERT INTO colaboradores
              (idcontratolaboral, idlocal, usernick, userpassword, restriccionhoraria, creado)
            VALUES
              (:idcontrato, :idlocal, :usernick, :userpassword, :restr, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idcontrato' => $idContrato,
                ':idlocal'      => $idlocal,
                ':usernick' => $usernick,
                ':userpassword' => $passwordHash,
                ':restr' => $restr,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Colaborador::create error - ' . $e->getMessage());
            return -1;
        }
    }


    public function create1(int $idContrato, string $usernick, string $passwordHash): int
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
