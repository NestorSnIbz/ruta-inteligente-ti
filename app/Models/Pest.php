<?php

final class Pest
{
    public static function ensureSeeded(SupabaseClient $supabase): void
    {
        $check = $supabase->request(
            'GET',
            '/rest/v1/pest_pregunta',
            [
                'select' => 'id_pregunta',
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
        foreach (self::defaultQuestions() as $n => $row) {
            $rows[] = [
                'numero' => (int) $n,
                'categoria' => (string) ($row['categoria'] ?? ''),
                'texto' => (string) ($row['texto'] ?? ''),
            ];
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'return=representation';

        $supabase->request(
            'POST',
            '/rest/v1/pest_pregunta',
            [],
            $headers,
            $rows
        );
    }

    public static function listPreguntas(SupabaseClient $supabase): array
    {
        $response = $supabase->request(
            'GET',
            '/rest/v1/pest_pregunta',
            [
                'select' => 'id_pregunta,numero,categoria,texto',
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
            '/rest/v1/pest_respuesta',
            [
                'select' => 'id_pregunta,valor',
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
            $qid = (int) ($row['id_pregunta'] ?? 0);
            $value = (int) ($row['valor'] ?? -1);
            if ($qid <= 0 || $value < 0 || $value > 4) {
                continue;
            }
            $map[$qid] = $value;
        }

        return $map;
    }

    public static function upsertRespuestasBatch(SupabaseClient $supabase, int $idProyecto, array $answers): bool
    {
        if ($idProyecto <= 0 || empty($answers)) {
            return false;
        }

        $rows = [];
        foreach ($answers as $idPregunta => $valor) {
            $idPregunta = (int) $idPregunta;
            $valor = (int) $valor;
            if ($idPregunta <= 0 || $valor < 0 || $valor > 4) {
                return false;
            }
            $rows[] = [
                'id_proyecto' => $idProyecto,
                'id_pregunta' => $idPregunta,
                'valor' => $valor,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ];
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/pest_respuesta',
            [
                'on_conflict' => 'id_proyecto,id_pregunta',
            ],
            $headers,
            $rows
        );

        if ($response['status'] >= 400 && self::isDebug()) {
            $payload = json_encode($response['data'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new RuntimeException('Supabase batch insert/upsert error (pest_respuesta). Status=' . (int) $response['status'] . ' Body=' . ($payload ?: 'null'));
        }

        return $response['status'] < 400;
    }

    public static function upsertResultado(SupabaseClient $supabase, int $idProyecto, array $pct): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/pest_resultado',
            [],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'sociales_pct' => (int) ($pct['SOCIALES'] ?? 0),
                'politicos_pct' => (int) ($pct['POLITICOS'] ?? 0),
                'economicos_pct' => (int) ($pct['ECONOMICOS'] ?? 0),
                'tecnologicos_pct' => (int) ($pct['TECNOLOGICOS'] ?? 0),
                'medioambientales_pct' => (int) ($pct['MEDIOAMBIENTALES'] ?? 0),
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        if ($response['status'] >= 400 && self::isDebug()) {
            $payload = json_encode($response['data'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new RuntimeException('Supabase upsert error (pest_resultado). Status=' . (int) $response['status'] . ' Body=' . ($payload ?: 'null'));
        }

        return $response['status'] < 400;
    }

    public static function compute(array $preguntas, array $respuestas): array
    {
        $categories = self::categories();
        $score = [];
        $max = [];
        foreach ($categories as $c) {
            $score[$c] = 0;
            $max[$c] = 0;
        }

        $count = 0;
        $valid = 0;
        foreach ($preguntas as $p) {
            if (!is_array($p)) {
                continue;
            }
            $qid = (int) ($p['id_pregunta'] ?? 0);
            $cat = strtoupper(trim((string) ($p['categoria'] ?? '')));
            if ($qid <= 0 || !in_array($cat, $categories, true)) {
                continue;
            }
            $count += 1;
            $max[$cat] += 4;
            if (!array_key_exists($qid, $respuestas)) {
                continue;
            }
            $v = (int) $respuestas[$qid];
            if ($v < 0 || $v > 4) {
                continue;
            }
            $valid += 1;
            $score[$cat] += $v;
        }

        $missing = max(0, $count - $valid);

        $pct = [];
        $conclusions = [];
        foreach ($categories as $cat) {
            $m = (int) ($max[$cat] ?? 0);
            $s = (int) ($score[$cat] ?? 0);
            $p = $m > 0 ? (int) floor(($s / $m) * 100) : 0;
            $p = max(0, min(100, $p));
            $pct[$cat] = $p;
            $conclusions[$cat] = self::conclusionForPct($p);
        }

        return [
            'valid' => (int) $valid,
            'count' => (int) $count,
            'missing' => (int) $missing,
            'score' => $score,
            'pct' => $pct,
            'conclusions' => $conclusions,
        ];
    }

    public static function conclusionForPct(int $pct): array
    {
        if ($pct >= 70) {
            return [
                'positive' => true,
                'text' => 'La influencia de este factor en el entorno de la empresa es alta y debe considerarse una variable estratégica para la toma de decisiones.',
            ];
        }

        return [
            'positive' => false,
            'text' => 'La influencia actual de este factor es moderada o reducida respecto al resto de variables del entorno.',
        ];
    }

    public static function categories(): array
    {
        return ['SOCIALES', 'MEDIOAMBIENTALES', 'POLITICOS', 'ECONOMICOS', 'TECNOLOGICOS'];
    }

    public static function defaultQuestions(): array
    {
        return [
            1 => ['categoria' => 'SOCIALES', 'texto' => 'Los cambios en la composición étnica de los consumidores de nuestro mercado está teniendo un notable impacto.'],
            2 => ['categoria' => 'SOCIALES', 'texto' => 'El envejecimiento de la población tiene un importante impacto en la demanda.'],
            3 => ['categoria' => 'SOCIALES', 'texto' => 'Los nuevos estilos de vida y tendencias originan cambios en la oferta de nuestro sector.'],
            4 => ['categoria' => 'SOCIALES', 'texto' => 'El envejecimiento de la población tiene un importante impacto en la oferta del sector donde operamos.'],
            5 => ['categoria' => 'SOCIALES', 'texto' => 'Las variaciones en el nivel de riqueza de la población impactan considerablemente en la demanda de los productos o servicios del sector donde operamos.'],
            6 => ['categoria' => 'POLITICOS', 'texto' => 'La legislación fiscal afecta muy considerablemente a la economía de las empresas del sector donde operamos.'],
            7 => ['categoria' => 'POLITICOS', 'texto' => 'La legislación laboral afecta muy considerablemente a la operativa del sector donde actuamos.'],
            8 => ['categoria' => 'POLITICOS', 'texto' => 'Las subvenciones otorgadas por las Administraciones Públicas son claves en el desarrollo competitivo del mercado donde operamos.'],
            9 => ['categoria' => 'POLITICOS', 'texto' => 'El impacto que tiene la legislación de protección al consumidor en la manera de producir bienes y servicios es muy importante.'],
            10 => ['categoria' => 'POLITICOS', 'texto' => 'La normativa autonómica tiene un impacto considerable en el funcionamiento del sector donde actuamos.'],
            11 => ['categoria' => 'ECONOMICOS', 'texto' => 'Las expectativas de crecimiento económico generales afectan crucialmente al mercado donde operamos.'],
            12 => ['categoria' => 'ECONOMICOS', 'texto' => 'La política de tipos de interés es fundamental en el desarrollo financiero del sector donde trabaja nuestra empresa.'],
            13 => ['categoria' => 'ECONOMICOS', 'texto' => 'La globalización permite a nuestra industria gozar de importantes oportunidades en nuevos mercados.'],
            14 => ['categoria' => 'ECONOMICOS', 'texto' => 'La situación del empleo es fundamental para el desarrollo económico de nuestra empresa y nuestro sector.'],
            15 => ['categoria' => 'ECONOMICOS', 'texto' => 'Las expectativas del ciclo económico de nuestro sector impactan en la situación económica de sus empresas.'],
            16 => ['categoria' => 'TECNOLOGICOS', 'texto' => 'Las Administraciones Públicas están incentivando el esfuerzo tecnológico de las empresas de nuestro sector.'],
            17 => ['categoria' => 'TECNOLOGICOS', 'texto' => 'Internet, el comercio electrónico, el wireless y otras NTIC están impactando en la demanda de nuestros productos o servicios y en los de la competencia.'],
            18 => ['categoria' => 'TECNOLOGICOS', 'texto' => 'El empleo de NTICs es generalizado en el sector donde trabajamos.'],
            19 => ['categoria' => 'TECNOLOGICOS', 'texto' => 'En nuestro sector es de gran importancia ser pionero o referente en el empleo de aplicaciones tecnológicas.'],
            20 => ['categoria' => 'TECNOLOGICOS', 'texto' => 'En el sector donde operamos, para ser competitivos, es condición indispensable innovar constantemente.'],
            21 => ['categoria' => 'MEDIOAMBIENTALES', 'texto' => 'La legislación medioambiental afecta al desarrollo de nuestro sector.'],
            22 => ['categoria' => 'MEDIOAMBIENTALES', 'texto' => 'Los clientes de nuestro mercado exigen que seamos socialmente responsables en el plano medioambiental.'],
            23 => ['categoria' => 'MEDIOAMBIENTALES', 'texto' => 'En nuestro sector, las políticas medioambientales son una fuente de ventajas competitivas.'],
            24 => ['categoria' => 'MEDIOAMBIENTALES', 'texto' => 'La creciente preocupación social por el medio ambiente impacta notablemente en la demanda de productos y servicios ofertados en nuestro mercado.'],
            25 => ['categoria' => 'MEDIOAMBIENTALES', 'texto' => 'El factor ecológico es una fuente de diferenciación clara en el sector donde opera nuestra empresa.'],
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

