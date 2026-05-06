<?php

final class Persona
{
    public static function findByEmail(SupabaseClient $supabase, string $email): ?array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        $response = $supabase->request(
            'GET',
            '/rest/v1/persona',
            [
                'select' => 'id_persona,nombre,email',
                'email' => 'eq.' . $email,
                'limit' => 1,
            ],
            [
                'apikey' => $apiKey,
                'Authorization' => 'Bearer ' . $authBearer,
            ]
        );

        if ($response['status'] >= 400) {
            return null;
        }

        if (!is_array($response['data']) || empty($response['data'])) {
            return null;
        }

        $row = $response['data'][0] ?? null;
        if (!is_array($row)) {
            return null;
        }

        return [
            'id_persona' => $row['id_persona'] ?? null,
            'nombre' => $row['nombre'] ?? null,
            'email' => $row['email'] ?? null,
        ];
    }

    public static function create(SupabaseClient $supabase, string $nombre, string $email): array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        $response = $supabase->request(
            'POST',
            '/rest/v1/persona',
            [],
            [
                'apikey' => $apiKey,
                'Authorization' => 'Bearer ' . $authBearer,
                'Prefer' => 'return=representation',
            ],
            [
                'nombre' => $nombre,
                'email' => $email,
            ]
        );

        if ($response['status'] >= 400) {
            return [
                'ok' => false,
                'error' => $response['data']['message'] ?? $response['data']['msg'] ?? 'No se pudo guardar la persona.',
                'persona' => null,
            ];
        }

        $row = (is_array($response['data']) && isset($response['data'][0]) && is_array($response['data'][0]))
            ? $response['data'][0]
            : null;

        if ($row === null) {
            return [
                'ok' => true,
                'error' => null,
                'persona' => null,
            ];
        }

        return [
            'ok' => true,
            'error' => null,
            'persona' => [
                'id_persona' => $row['id_persona'] ?? null,
                'nombre' => $row['nombre'] ?? null,
                'email' => $row['email'] ?? null,
            ],
        ];
    }

    public static function updateNombreById(SupabaseClient $supabase, int $idPersona, string $nombre): array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        $response = $supabase->request(
            'PATCH',
            '/rest/v1/persona',
            [
                'id_persona' => 'eq.' . $idPersona,
            ],
            [
                'apikey' => $apiKey,
                'Authorization' => 'Bearer ' . $authBearer,
                'Prefer' => 'return=representation',
            ],
            [
                'nombre' => $nombre,
            ]
        );

        if ($response['status'] >= 400) {
            return [
                'ok' => false,
                'error' => $response['data']['message'] ?? $response['data']['msg'] ?? 'No se pudo actualizar el nombre.',
            ];
        }

        return [
            'ok' => true,
            'error' => null,
        ];
    }
}
