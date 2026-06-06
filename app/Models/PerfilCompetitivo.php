<?php

final class PerfilCompetitivo
{
    public static function ensureSeeded(SupabaseClient $supabase): void
    {
        $check = $supabase->request(
            'GET',
            '/rest/v1/perfil_competitivo_factor',
            [
                'select' => 'id_factor',
                'limit' => 1,
            ],
            self::restHeaders($supabase)
        );

        if ($check['status'] >= 400) {
            return;
        }

        if (is_array($check['data']) && !empty($check['data'])) {
            return;
        }

        $rows = [];
        foreach (self::defaultFactors() as $row) {
            $rows[] = $row;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'return=representation';

        $supabase->request(
            'POST',
            '/rest/v1/perfil_competitivo_factor',
            [],
            $headers,
            $rows
        );
    }

    public static function listFactores(SupabaseClient $supabase): array
    {
        $response = $supabase->request(
            'GET',
            '/rest/v1/perfil_competitivo_factor',
            [
                'select' => 'id_factor,numero,categoria,nombre_factor,hostil_label,favorable_label',
                'order' => 'numero.asc',
            ],
            self::restHeaders($supabase)
        );

        if ($response['status'] >= 400) {
            return [];
        }

        return is_array($response['data']) ? $response['data'] : [];
    }

    public static function listRespuestasByProyecto(SupabaseClient $supabase, int $idProyecto): array
    {
        $response = $supabase->request(
            'GET',
            '/rest/v1/perfil_competitivo_respuesta',
            [
                'select' => 'id_factor,valor',
                'id_proyecto' => 'eq.' . $idProyecto,
            ],
            self::restHeaders($supabase)
        );

        if ($response['status'] >= 400 || !is_array($response['data'])) {
            return [];
        }

        $map = [];
        foreach ($response['data'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $fid = (int) ($row['id_factor'] ?? 0);
            $value = (int) ($row['valor'] ?? -1);
            if ($fid <= 0 || $value < 0 || $value > 4) {
                continue;
            }
            $map[$fid] = $value;
        }

        return $map;
    }

    public static function existsFactor(SupabaseClient $supabase, int $idFactor): bool
    {
        if ($idFactor <= 0) {
            return false;
        }

        $response = $supabase->request(
            'GET',
            '/rest/v1/perfil_competitivo_factor',
            [
                'select' => 'id_factor',
                'id_factor' => 'eq.' . $idFactor,
                'limit' => 1,
            ],
            self::restHeaders($supabase)
        );

        if ($response['status'] >= 400) {
            return false;
        }

        return is_array($response['data']) && !empty($response['data']);
    }

    public static function upsertRespuesta(SupabaseClient $supabase, int $idProyecto, int $idFactor, int $valor): bool
    {
        if ($idProyecto <= 0 || $idFactor <= 0 || $valor < 0 || $valor > 4) {
            return false;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/perfil_competitivo_respuesta',
            [
                'on_conflict' => 'id_proyecto,id_factor',
            ],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'id_factor' => $idFactor,
                'valor' => $valor,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        if ($response['status'] >= 400 && self::isDebug()) {
            $payload = json_encode($response['data'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new RuntimeException('Supabase insert/upsert error (perfil_competitivo_respuesta). Status=' . (int) $response['status'] . ' Body=' . ($payload ?: 'null'));
        }

        return $response['status'] < 400;
    }

    public static function upsertRespuestasBatch(SupabaseClient $supabase, int $idProyecto, array $answers): bool
    {
        if ($idProyecto <= 0 || empty($answers)) {
            return false;
        }

        $rows = [];
        foreach ($answers as $idFactor => $valor) {
            $idFactor = (int) $idFactor;
            $valor = (int) $valor;
            if ($idFactor <= 0 || $valor < 0 || $valor > 4) {
                return false;
            }
            $rows[] = [
                'id_proyecto' => $idProyecto,
                'id_factor' => $idFactor,
                'valor' => $valor,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ];
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/perfil_competitivo_respuesta',
            [
                'on_conflict' => 'id_proyecto,id_factor',
            ],
            $headers,
            $rows
        );

        if ($response['status'] >= 400 && self::isDebug()) {
            $payload = json_encode($response['data'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new RuntimeException('Supabase batch insert/upsert error (perfil_competitivo_respuesta). Status=' . (int) $response['status'] . ' Body=' . ($payload ?: 'null'));
        }

        return $response['status'] < 400;
    }

    public static function upsertResultado(SupabaseClient $supabase, int $idProyecto, int $total, int $conclusionCode, string $conclusionText): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        if ($total < 0) {
            return false;
        }

        if ($conclusionCode < 1 || $conclusionCode > 4) {
            return false;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/perfil_competitivo_resultado',
            [],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'total' => $total,
                'conclusion_code' => $conclusionCode,
                'conclusion_text' => $conclusionText,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        if ($response['status'] >= 400 && self::isDebug()) {
            $payload = json_encode($response['data'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new RuntimeException('Supabase insert/upsert error (perfil_competitivo_resultado). Status=' . (int) $response['status'] . ' Body=' . ($payload ?: 'null'));
        }

        return $response['status'] < 400;
    }

    public static function compute(array $factores, array $respuestas): array
    {
        $total = 0;
        $valid = 0;
        $count = 0;

        foreach ($factores as $f) {
            if (!is_array($f)) {
                continue;
            }
            $fid = (int) ($f['id_factor'] ?? 0);
            if ($fid <= 0) {
                continue;
            }
            $count += 1;
            if (!array_key_exists($fid, $respuestas)) {
                continue;
            }
            $v = (int) $respuestas[$fid];
            if ($v < 0 || $v > 4) {
                continue;
            }
            $valid += 1;
            $total += $v;
        }

        $missing = max(0, $count - $valid);
        $conclusion = $missing > 0 ? null : self::conclusionForTotal($total);

        return [
            'total' => (int) $total,
            'valid' => (int) $valid,
            'count' => (int) $count,
            'missing' => (int) $missing,
            'conclusion_code' => $conclusion === null ? null : (int) $conclusion['code'],
            'conclusion_text' => $conclusion === null ? null : (string) $conclusion['text'],
        ];
    }

    public static function conclusionForTotal(int $total): array
    {
        if ($total < 30) {
            return [
                'code' => 1,
                'text' => 'Entorno altamente hostil. La empresa enfrenta una fuerte presión competitiva y condiciones desfavorables en el sector.',
            ];
        }

        if ($total >= 30 && $total < 45) {
            return [
                'code' => 2,
                'text' => 'Entorno moderadamente hostil. Existen factores que limitan la competitividad y requieren estrategias de mejora.',
            ];
        }

        if ($total >= 45 && $total < 60) {
            return [
                'code' => 3,
                'text' => 'Entorno favorable. La empresa cuenta con condiciones competitivas aceptables para desarrollar sus actividades.',
            ];
        }

        return [
            'code' => 4,
            'text' => 'Entorno muy favorable. La posición competitiva de la empresa es sólida y las condiciones del sector son altamente positivas.',
        ];
    }

    public static function defaultFactors(): array
    {
        return [
            [
                'numero' => 1,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Crecimiento',
                'hostil_label' => 'Lento',
                'favorable_label' => 'Rápido',
            ],
            [
                'numero' => 2,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Naturaleza de los competidores',
                'hostil_label' => 'Muchos',
                'favorable_label' => 'Pocos',
            ],
            [
                'numero' => 3,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Exceso de capacidad productiva',
                'hostil_label' => 'Sí',
                'favorable_label' => 'No',
            ],
            [
                'numero' => 4,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Rentabilidad media del sector',
                'hostil_label' => 'Baja',
                'favorable_label' => 'Alta',
            ],
            [
                'numero' => 5,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Diferenciación del producto',
                'hostil_label' => 'Escasa',
                'favorable_label' => 'Elevada',
            ],
            [
                'numero' => 6,
                'categoria' => 'RIVALIDAD_EMPRESAS_DEL_SECTOR',
                'nombre_factor' => 'Barreras de salida',
                'hostil_label' => 'Bajas',
                'favorable_label' => 'Altas',
            ],
            [
                'numero' => 7,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Economías de escala',
                'hostil_label' => 'No',
                'favorable_label' => 'Sí',
            ],
            [
                'numero' => 8,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Necesidad de capital',
                'hostil_label' => 'Bajas',
                'favorable_label' => 'Altas',
            ],
            [
                'numero' => 9,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Acceso a la tecnología',
                'hostil_label' => 'Fácil',
                'favorable_label' => 'Difícil',
            ],
            [
                'numero' => 10,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Reglamentos o leyes limitativos',
                'hostil_label' => 'No',
                'favorable_label' => 'Sí',
            ],
            [
                'numero' => 11,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Trámites burocráticos',
                'hostil_label' => 'No',
                'favorable_label' => 'Sí',
            ],
            [
                'numero' => 12,
                'categoria' => 'BARRERAS_DE_ENTRADA',
                'nombre_factor' => 'Reacción esperada actuales competidores',
                'hostil_label' => 'Escasa',
                'favorable_label' => 'Enérgica',
            ],
            [
                'numero' => 13,
                'categoria' => 'PODER_DE_LOS_CLIENTES',
                'nombre_factor' => 'Número de clientes',
                'hostil_label' => 'Pocos',
                'favorable_label' => 'Muchos',
            ],
            [
                'numero' => 14,
                'categoria' => 'PODER_DE_LOS_CLIENTES',
                'nombre_factor' => 'Posibilidad de integración ascendente',
                'hostil_label' => 'Pequeña',
                'favorable_label' => 'Grande',
            ],
            [
                'numero' => 15,
                'categoria' => 'PODER_DE_LOS_CLIENTES',
                'nombre_factor' => 'Rentabilidad de los clientes',
                'hostil_label' => 'Baja',
                'favorable_label' => 'Alta',
            ],
            [
                'numero' => 16,
                'categoria' => 'PODER_DE_LOS_CLIENTES',
                'nombre_factor' => 'Coste de cambio de proveedor para cliente',
                'hostil_label' => 'Bajo',
                'favorable_label' => 'Alto',
            ],
            [
                'numero' => 17,
                'categoria' => 'PRODUCTOS_SUSTITUTIVOS',
                'nombre_factor' => 'Disponibilidad de Productos Sustitutivos',
                'hostil_label' => 'Grande',
                'favorable_label' => 'Pequeña',
            ],
        ];
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

    private static function isDebug(): bool
    {
        $value = getenv('APP_DEBUG');
        if ($value === false) {
            return false;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }
}

