<?php

final class Vision
{
    public static function findByProyecto(int $idProyecto): ?array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('SELECT id_vision, id_proyecto, descripcion FROM vision WHERE id_proyecto = :id_proyecto LIMIT 1');
        $stmt->execute([':id_proyecto' => $idProyecto]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public static function save(int $idProyecto, string $descripcion): void
    {
        $pdo = Database::pdo();

        $existing = self::findByProyecto($idProyecto);
        if ($existing) {
            $stmt = $pdo->prepare('UPDATE vision SET descripcion = :descripcion WHERE id_proyecto = :id_proyecto');
            $stmt->execute([
                ':descripcion' => $descripcion,
                ':id_proyecto' => $idProyecto,
            ]);
            return;
        }

        $stmt = $pdo->prepare('INSERT INTO vision (id_proyecto, descripcion) VALUES (:id_proyecto, :descripcion)');
        $stmt->execute([
            ':id_proyecto' => $idProyecto,
            ':descripcion' => $descripcion,
        ]);
    }
}

