<?php

final class PlanEstrategicoConclusion
{
    public static function findByProyecto(SupabaseClient $supabase, int $idProyecto): ?array
    {
        $response = $supabase->request(
            'GET',
            '/rest/v1/plan_estrategico_conclusion',
            [
                'select' => 'id_conclusion,id_proyecto,descripcion,updated_at',
                'id_proyecto' => 'eq.' . $idProyecto,
                'limit' => 1,
            ],
            self::restHeaders($supabase)
        );

        if (($response['status'] ?? 500) >= 400) {
            throw new RuntimeException((string) ($response['data']['message'] ?? $response['data']['msg'] ?? 'No se pudo cargar la conclusión del plan estratégico.'));
        }

        if (!is_array($response['data'] ?? null) || empty($response['data']) || !is_array($response['data'][0] ?? null)) {
            return null;
        }

        return $response['data'][0];
    }

    public static function save(SupabaseClient $supabase, int $idProyecto, string $descripcion): void
    {
        $existing = self::findByProyecto($supabase, $idProyecto);
        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'return=representation';

        if ($existing) {
            $response = $supabase->request(
                'PATCH',
                '/rest/v1/plan_estrategico_conclusion',
                ['id_proyecto' => 'eq.' . $idProyecto],
                $headers,
                [
                    'descripcion' => $descripcion,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ]
            );

            if (($response['status'] ?? 500) >= 400) {
                throw new RuntimeException((string) ($response['data']['message'] ?? $response['data']['msg'] ?? 'No se pudo actualizar la conclusión del plan estratégico.'));
            }
            return;
        }

        $response = $supabase->request(
            'POST',
            '/rest/v1/plan_estrategico_conclusion',
            [],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'descripcion' => $descripcion,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        if (($response['status'] ?? 500) >= 400) {
            throw new RuntimeException((string) ($response['data']['message'] ?? $response['data']['msg'] ?? 'No se pudo guardar la conclusión del plan estratégico.'));
        }
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
