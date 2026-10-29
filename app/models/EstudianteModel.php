<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
use RuntimeException;

class EstudianteModel
{
    private PDO $db;

    public const DEFAULT_INSTITUTION = 'No indicó institución';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @param array{
     *   cedula: string,
     *   nombre: string,
     *   apellido: string,
     *   fecha_nacimiento: string,
     *   edad: int,
     *   correo: string,
     *   telefono: string,
     *   institution_name: string,
     *   institution_type: string
     * } $data
     */
    public function crear(array $data): int
    {
        $institutionName = trim((string) ($data['institution_name'] ?? ''));
        if ($institutionName === '') {
            $institutionName = self::DEFAULT_INSTITUTION;
        }
        $institutionType = strtolower(trim((string) ($data['institution_type'] ?? 'no_indico')));
        if (!in_array($institutionType, ['universidad', 'empresa', 'otra', 'no_indico'], true)) {
            $institutionType = 'no_indico';
        }

        $sql = 'INSERT INTO estudiantes
                (cedula, nombre, apellido, fecha_nacimiento, edad, correo, telefono,
                 institution_name, institution_type)
                VALUES
                (:cedula, :nombre, :apellido, :fecha_nacimiento, :edad, :correo, :telefono,
                 :institution_name, :institution_type)
                RETURNING id';

        try {
            Database::beginTransaction();

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':cedula', $data['cedula'], PDO::PARAM_STR);
            $stmt->bindValue(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindValue(':apellido', $data['apellido'], PDO::PARAM_STR);
            $stmt->bindValue(':fecha_nacimiento', $data['fecha_nacimiento'], PDO::PARAM_STR);
            $stmt->bindValue(':edad', $data['edad'], PDO::PARAM_INT);
            $stmt->bindValue(':correo', $data['correo'], PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $data['telefono'], PDO::PARAM_STR);
            $stmt->bindValue(':institution_name', $institutionName, PDO::PARAM_STR);
            $stmt->bindValue(':institution_type', $institutionType, PDO::PARAM_STR);
            $stmt->execute();

            $id = (int) $stmt->fetchColumn();
            Database::commit();

            return $id;
        } catch (PDOException $e) {
            Database::rollBack();

            // Violación de unicidad (cedula)
            if ($e->getCode() === '23505') {
                throw new RuntimeException('Ya existe un registro con esa cédula.', 409, $e);
            }

            error_log('Error al guardar estudiante: ' . $e->getMessage());
            throw new RuntimeException('Error al guardar el registro.', 500, $e);
        }
    }

    public function existePorCedula(string $cedula): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM estudiantes WHERE cedula = :cedula LIMIT 1');
        $stmt->bindValue(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    public function listarTodos(): array
    {
        $stmt = $this->db->query(
            'SELECT id, cedula, nombre, apellido, fecha_nacimiento, edad, correo, telefono,
                    institution_name, institution_type, asistio, fecha_registro
             FROM estudiantes
             ORDER BY apellido ASC, nombre ASC'
        );

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, cedula, nombre, apellido, fecha_nacimiento, edad, correo, telefono,
                    institution_name, institution_type, asistio, fecha_registro
             FROM estudiantes
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function buscarPorCedula(string $cedula): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, cedula, nombre, apellido, fecha_nacimiento, edad, correo, telefono,
                    institution_name, institution_type, asistio, fecha_registro
             FROM estudiantes
             WHERE cedula = :cedula
             LIMIT 1'
        );
        $stmt->bindValue(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function eliminar(int $id): bool
    {
        try {
            Database::beginTransaction();

            $stmt = $this->db->prepare('DELETE FROM estudiantes WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $deleted = $stmt->rowCount() > 0;

            Database::commit();

            return $deleted;
        } catch (PDOException $e) {
            Database::rollBack();
            throw new RuntimeException('No se pudo eliminar el registro.', 500, $e);
        }
    }
}
