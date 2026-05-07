<?php

final class Proyecto
{
    public static function create(int $creadorId, string $nombre): int
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('INSERT INTO proyecto (creador_id, nombre) VALUES (:creador_id, :nombre) RETURNING id_proyecto');
        $stmt->execute([
            ':creador_id' => $creadorId,
            ':nombre' => $nombre,
        ]);

        $id = $stmt->fetchColumn();
        return (int) $id;
    }

    public static function listByCreador(int $creadorId): array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('SELECT id_proyecto, nombre FROM proyecto WHERE creador_id = :creador_id ORDER BY id_proyecto DESC');
        $stmt->execute([':creador_id' => $creadorId]);
        return $stmt->fetchAll();
    }

    public static function findOwnedById(int $idProyecto, int $creadorId): ?array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('SELECT id_proyecto, nombre, creador_id FROM proyecto WHERE id_proyecto = :id AND creador_id = :creador_id LIMIT 1');
        $stmt->execute([
            ':id' => $idProyecto,
            ':creador_id' => $creadorId,
        ]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }
}

