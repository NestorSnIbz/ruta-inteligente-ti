<?php

final class Valor
{
    public static function listByProyecto(int $idProyecto): array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('SELECT id_valor, id_proyecto, descripcion FROM valor WHERE id_proyecto = :id_proyecto ORDER BY id_valor ASC');
        $stmt->execute([':id_proyecto' => $idProyecto]);
        return $stmt->fetchAll();
    }

    public static function findById(int $idValor, int $idProyecto): ?array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('SELECT id_valor, id_proyecto, descripcion FROM valor WHERE id_valor = :id_valor AND id_proyecto = :id_proyecto LIMIT 1');
        $stmt->execute([
            ':id_valor' => $idValor,
            ':id_proyecto' => $idProyecto,
        ]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public static function create(int $idProyecto, string $descripcion): void
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('INSERT INTO valor (id_proyecto, descripcion) VALUES (:id_proyecto, :descripcion)');
        $stmt->execute([
            ':id_proyecto' => $idProyecto,
            ':descripcion' => $descripcion,
        ]);
    }

    public static function update(int $idValor, int $idProyecto, string $descripcion): bool
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('UPDATE valor SET descripcion = :descripcion WHERE id_valor = :id_valor AND id_proyecto = :id_proyecto');
        $stmt->execute([
            ':descripcion' => $descripcion,
            ':id_valor' => $idValor,
            ':id_proyecto' => $idProyecto,
        ]);

        return $stmt->rowCount() > 0;
    }
}

