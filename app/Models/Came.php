<?php

final class Came
{
    public static function listAccionesByProyecto(SupabaseClient $supabase, int $idProyecto): array
    {
        if ($idProyecto <= 0) {
            return self::emptyGroups();
        }

        $response = $supabase->request(
            'GET',
            '/rest/v1/came_accion',
            [
                'select' => 'categoria,posicion,descripcion',
                'id_proyecto' => 'eq.' . $idProyecto,
                'order' => 'categoria.asc,posicion.asc',
                'limit' => 500,
            ],
            self::restHeaders($supabase)
        );

        if (($response['status'] ?? 500) >= 400 || !is_array($response['data'] ?? null)) {
            return self::emptyGroups();
        }

        $out = self::emptyGroups();
        foreach ($response['data'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $cat = strtoupper(trim((string) ($row['categoria'] ?? '')));
            $pos = (int) ($row['posicion'] ?? 0);
            $desc = trim((string) ($row['descripcion'] ?? ''));
            if (!in_array($cat, self::categoryOrder(), true) || $pos < 1) {
                continue;
            }
            $out[$cat][] = [
                'position' => $pos,
                'description' => $desc,
            ];
        }

        return $out;
    }

    public static function replaceAcciones(SupabaseClient $supabase, int $idProyecto, array $rows): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $delete = $supabase->request(
            'DELETE',
            '/rest/v1/came_accion',
            [
                'id_proyecto' => 'eq.' . $idProyecto,
            ],
            self::restHeaders($supabase)
        );

        if (($delete['status'] ?? 500) >= 400) {
            return false;
        }

        if (empty($rows)) {
            return true;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'return=minimal';

        $insert = $supabase->request(
            'POST',
            '/rest/v1/came_accion',
            [],
            $headers,
            $rows
        );

        return ($insert['status'] ?? 500) < 400;
    }

    public static function upsertResultado(SupabaseClient $supabase, int $idProyecto, array $calc): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $totalActions = (int) ($calc['total_actions'] ?? 0);
        $categoriesUsed = (int) ($calc['categories_used'] ?? 0);

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/came_resultado',
            [
                'on_conflict' => 'id_proyecto',
            ],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'acciones_registradas' => max(0, $totalActions),
                'categorias_utilizadas' => max(0, min(4, $categoriesUsed)),
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        return ($response['status'] ?? 500) < 400;
    }

    public static function compute(array $accionesByPos): array
    {
        $accionesByPos = self::normalizeGroups($accionesByPos);
        $totalActions = 0;
        $categoriesUsed = 0;
        $counts = [];

        foreach (self::categoryOrder() as $cat) {
            $rows = $accionesByPos[$cat] ?? [];
            $count = 0;
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $text = trim((string) ($row['description'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $count++;
            }
            $counts[$cat] = $count;
            $totalActions += $count;
            if ($count > 0) {
                $categoriesUsed++;
            }
        }

        return [
            'counts' => $counts,
            'total_actions' => $totalActions,
            'categories_used' => $categoriesUsed,
            'empty' => $totalActions === 0,
        ];
    }

    public static function categoryOrder(): array
    {
        return ['C', 'A', 'M', 'E'];
    }

    public static function tituloPorCategoria(string $cat): string
    {
        $cat = strtoupper(trim($cat));
        return match ($cat) {
            'C' => 'Corregir las debilidades',
            'A' => 'Afrontar las amenazas',
            'M' => 'Mantener las fortalezas',
            'E' => 'Explotar las oportunidades',
            default => '',
        };
    }

    private static function emptyGroups(): array
    {
        return [
            'C' => [],
            'A' => [],
            'M' => [],
            'E' => [],
        ];
    }

    private static function normalizeGroups(array $groups): array
    {
        $out = self::emptyGroups();
        foreach (self::categoryOrder() as $cat) {
            $rows = is_array($groups[$cat] ?? null) ? (array) $groups[$cat] : [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $out[$cat][] = [
                    'position' => max(1, (int) ($row['position'] ?? (count($out[$cat]) + 1))),
                    'description' => trim((string) ($row['description'] ?? '')),
                ];
            }
        }
        return $out;
    }

    private static function restHeaders(SupabaseClient $supabase): array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        return [
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $authBearer,
        ];
    }
}
