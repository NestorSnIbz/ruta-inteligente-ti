<?php

final class ProyectoController
{
    public function index(): void
    {
        // Protege el módulo: solo usuarios autenticados pueden gestionar proyectos.
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        try {
            $supabase = new SupabaseClient();
            $proyectos = Proyecto::listByCreador($supabase, (int) $authUser['id_persona']);
            $proyectos = $this->attachProjectTokens($proyectos);
        } catch (Throwable $e) {
            $proyectos = [];
            $error = $error ?: $this->friendlySupabaseError($e, 'No se pudo cargar la lista de proyectos.');
        }

        require dirname(__DIR__) . '/Views/proyectos/index.php';
    }

    public function createForm(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        require dirname(__DIR__) . '/Views/proyectos/nuevo-proyecto.php';
    }

    public function store(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        // Validación mínima antes de insertar en la tabla proyecto.
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') < 3) {
            Session::flash('error', 'El nombre del proyecto es obligatorio (mínimo 3 caracteres).');
            $this->redirect('/nuevo-proyecto.php');
        }

        try {
            $supabase = new SupabaseClient();
            $idProyecto = Proyecto::create($supabase, (int) $authUser['id_persona'], $nombre);
        } catch (Throwable $e) {
            Session::flash('error', $this->friendlySupabaseError($e, 'No se pudo crear el proyecto.'));
            $this->redirect('/nuevo-proyecto.php');
        }

        Session::flash('success', 'Proyecto creado correctamente.');
        $token = $this->issueProjectToken($idProyecto);
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token));
    }

    public function show(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $token = trim((string) ($_GET['t'] ?? ''));
        $idProyecto = 0;

        if ($token !== '') {
            $idProyecto = $this->projectIdFromToken($token);
            if ($idProyecto <= 0) {
                Session::flash('error', 'Enlace inválido o expirado. Regresa a la lista de proyectos.');
                $this->redirect('/proyectos.php');
            }
        } else {
            $idProyecto = (int) ($_GET['id'] ?? 0);
            if ($idProyecto <= 0) {
                Session::flash('error', 'Proyecto inválido.');
                $this->redirect('/proyectos.php');
            }
        }

        try {
            $supabase = new SupabaseClient();
            // Se valida propiedad del proyecto (creador_id) para evitar acceso a datos de terceros.
            $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                Session::flash('error', 'No tienes acceso a este proyecto.');
                $this->redirect('/proyectos.php');
            }

            $mision = Mision::findByProyecto($supabase, $idProyecto);
            $vision = Vision::findByProyecto($supabase, $idProyecto);
            $valores = Valor::listByProyecto($supabase, $idProyecto);
        } catch (Throwable $e) {
            Session::flash('error', $this->friendlySupabaseError($e, 'No se pudo cargar el proyecto.'));
            $this->redirect('/proyectos.php');
        }

        $objetivosEstrategicos = [];
        $objetivosEspecificosByEstrategico = [];
        $objetivosError = '';

        try {
            $objetivosEstrategicos = ObjetivoEstrategico::listByProyecto($supabase, $idProyecto);
            $objetivosEstrategicos = $this->attachObjetivoEstrategicoTokens($objetivosEstrategicos);

            $objetivosEspecificos = ObjetivoEspecifico::listByProyecto($supabase, $idProyecto);
            $objetivosEspecificosByEstrategico = $this->groupObjetivosEspecificosByEstrategicoWithTokens($objetivosEspecificos);

            $objetivosEstrategicos = $this->attachEspecificosCountToObjetivosEstrategicos($objetivosEstrategicos, $objetivosEspecificosByEstrategico);
        } catch (Throwable $e) {
            $objetivosError = $this->isDebug() ? ('No se pudieron cargar los objetivos. Detalle: ' . $e->getMessage()) : 'No se pudieron cargar los objetivos.';
        }

        if ($token === '') {
            $token = $this->issueProjectToken($idProyecto);
            $query = [];
            if (isset($_GET['edit'])) {
                $query['edit'] = (string) $_GET['edit'];
            }
            if (isset($_GET['section'])) {
                $query['section'] = (string) $_GET['section'];
            }
            if (isset($_GET['oe_edit'])) {
                $query['oe_edit'] = (string) $_GET['oe_edit'];
            }
            if (isset($_GET['oesp_edit'])) {
                $query['oesp_edit'] = (string) $_GET['oesp_edit'];
            }
            $qs = http_build_query(array_filter($query, fn ($v) => $v !== ''), '', '&', PHP_QUERY_RFC3986);
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . ($qs ? ('&' . $qs) : ''));
        }

        $projectToken = $token;
        $edit = (string) ($_GET['edit'] ?? '');
        $oeEditToken = trim((string) ($_GET['oe_edit'] ?? ''));
        $oespEditToken = trim((string) ($_GET['oesp_edit'] ?? ''));

        require dirname(__DIR__) . '/Views/proyectos/detalle-proyecto.php';
    }

    public function saveMision(): void
    {
        $this->saveSingleTextBlock('mision', 'mision', 'La misión es obligatoria.');
    }

    public function saveVision(): void
    {
        $this->saveSingleTextBlock('vision', 'vision', 'La visión es obligatoria.');
    }

    public function addValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        Valor::create($supabase, $idProyecto, $descripcion);
        Session::flash('success', 'Valor agregado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function saveValores(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $valores = $_POST['valores'] ?? [];

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if (!is_array($valores)) {
            Session::flash('error', 'Valores inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $clean = [];
        foreach ($valores as $v) {
            $v = trim((string) $v);
            if ($v === '') {
                continue;
            }
            if (mb_strlen($v, 'UTF-8') < 2) {
                Session::flash('error', 'Cada valor debe tener al menos 2 caracteres.');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
            }
            $clean[] = $v;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                Session::flash('error', 'No tienes acceso a este proyecto.');
                $this->redirect('/proyectos.php');
            }

            Valor::replaceAll($supabase, $idProyecto, $clean);
        } catch (Throwable $e) {
            Session::flash('error', $this->friendlySupabaseError($e, 'No se pudieron guardar los valores.'));
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores&edit=valores');
        }

        Session::flash('success', 'Valores guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function updateValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $idValor = (int) ($_POST['id_valor'] ?? 0);
        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0 || $idValor <= 0) {
            Session::flash('error', 'Valor inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        $ok = Valor::update($supabase, $idValor, $idProyecto, $descripcion);
        if (!$ok) {
            Session::flash('error', 'No se pudo actualizar el valor.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
        }

        Session::flash('success', 'Valor actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function createObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            Session::flash('error', 'La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        ObjetivoEstrategico::create($supabase, $idProyecto, $descripcion);
        Session::flash('success', 'Objetivo estratégico registrado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function updateObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            Session::flash('error', 'La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oe_edit=' . urlencode($oeToken));
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEstrategico::update($supabase, $idObjetivoEst, $idProyecto, $descripcion);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            Session::flash('error', 'No se pudo actualizar el objetivo estratégico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oe_edit=' . urlencode($oeToken));
        }

        Session::flash('success', 'Objetivo estratégico actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function deleteObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEstrategico::delete($supabase, $idObjetivoEst, $idProyecto);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            Session::flash('error', 'No se pudo eliminar el objetivo estratégico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        Session::flash('success', 'Objetivo estratégico eliminado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function createObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            Session::flash('error', 'La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        ObjetivoEspecifico::create($supabase, $idObjetivoEst, $descripcion);
        Session::flash('success', 'Objetivo específico registrado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function updateObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $oespToken = trim((string) ($_POST['oesp'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);
        $idObjetivoEsp = $this->objetivoEspecificoIdFromToken($oespToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0 || $idObjetivoEsp <= 0) {
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            Session::flash('error', 'La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oesp_edit=' . urlencode($oespToken));
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if (!ObjetivoEspecifico::existsInObjetivoEstrategico($supabase, $idObjetivoEsp, $idObjetivoEst)) {
            Session::flash('error', 'No tienes acceso a este objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEspecifico::update($supabase, $idObjetivoEsp, $idObjetivoEst, $descripcion);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            Session::flash('error', 'No se pudo actualizar el objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oesp_edit=' . urlencode($oespToken));
        }

        Session::flash('success', 'Objetivo específico actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function deleteObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $oespToken = trim((string) ($_POST['oesp'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);
        $idObjetivoEsp = $this->objetivoEspecificoIdFromToken($oespToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0 || $idObjetivoEsp <= 0) {
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if (!ObjetivoEspecifico::existsInObjetivoEstrategico($supabase, $idObjetivoEsp, $idObjetivoEst)) {
            Session::flash('error', 'No tienes acceso a este objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEspecifico::delete($supabase, $idObjetivoEsp, $idObjetivoEst);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            Session::flash('error', 'No se pudo eliminar el objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        Session::flash('success', 'Objetivo específico eliminado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    private function saveSingleTextBlock(string $block, string $editQuery, string $emptyMessage): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '') {
            Session::flash('error', $emptyMessage);
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=' . urlencode($editQuery) . '&edit=' . $editQuery);
        }

        $supabase = new SupabaseClient();
        $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if ($block === 'mision') {
            Mision::save($supabase, $idProyecto, $descripcion);
        } elseif ($block === 'vision') {
            Vision::save($supabase, $idProyecto, $descripcion);
        }

        Session::flash('success', 'Cambios guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=' . urlencode($editQuery));
    }

    private function redirect(string $path): void
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '\\/');
        $location = $basePath === '' ? $path : ($basePath . $path);
        header('Location: ' . $location);
        exit;
    }

    private function friendlySupabaseError(Throwable $e, string $prefix): string
    {
        $message = $e->getMessage();
        $lower = strtolower($message);

        $hint = ' Revisa SUPABASE_URL, SUPABASE_ANON_KEY y que existan las tablas (proyecto, mision, vision, valor) en Supabase.';

        if (str_contains($lower, 'permission denied') || str_contains($lower, 'row level security') || str_contains($lower, 'rls')) {
            $hint = ' Falta permiso (RLS). Usa SUPABASE_SERVICE_ROLE_KEY en el .env o configura policies.';
        } elseif (str_contains($lower, 'does not exist') && str_contains($lower, 'relation')) {
            $hint = ' No existen las tablas en la base (ejecuta base-datos.sql en el SQL Editor de Supabase).';
        }

        if ($this->isDebug()) {
            return $prefix . $hint . ' Detalle: ' . $message;
        }

        return $prefix . $hint;
    }

    private function isDebug(): bool
    {
        $value = getenv('APP_DEBUG');
        if ($value === false) {
            return false;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function issueProjectToken(int $idProyecto): string
    {
        Session::start();
        $tokens = Session::get('project_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idProyecto && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idProyecto;
        Session::set('project_tokens', $tokens);
        return $token;
    }

    private function projectIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('project_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        $id = $tokens[$token] ?? 0;
        return (int) $id;
    }

    private function attachProjectTokens(array $proyectos): array
    {
        $out = [];
        foreach ($proyectos as $p) {
            if (!is_array($p)) {
                continue;
            }

            $id = (int) ($p['id_proyecto'] ?? 0);
            if ($id > 0) {
                $p['token'] = $this->issueProjectToken($id);
            }

            $out[] = $p;
        }

        return $out;
    }

    private function issueObjetivoEstrategicoToken(int $idObjetivoEst): string
    {
        Session::start();
        $tokens = Session::get('obj_est_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idObjetivoEst && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idObjetivoEst;
        Session::set('obj_est_tokens', $tokens);
        return $token;
    }

    private function objetivoEstrategicoIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('obj_est_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        return (int) ($tokens[$token] ?? 0);
    }

    private function issueObjetivoEspecificoToken(int $idObjetivoEsp): string
    {
        Session::start();
        $tokens = Session::get('obj_esp_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idObjetivoEsp && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idObjetivoEsp;
        Session::set('obj_esp_tokens', $tokens);
        return $token;
    }

    private function objetivoEspecificoIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('obj_esp_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        return (int) ($tokens[$token] ?? 0);
    }

    private function attachObjetivoEstrategicoTokens(array $objetivos): array
    {
        $out = [];
        foreach ($objetivos as $o) {
            if (!is_array($o)) {
                continue;
            }

            $id = (int) ($o['id_objetivo_est'] ?? 0);
            if ($id > 0) {
                $o['token'] = $this->issueObjetivoEstrategicoToken($id);
            }

            $out[] = $o;
        }

        return $out;
    }

    private function groupObjetivosEspecificosByEstrategicoWithTokens(array $especificos): array
    {
        $out = [];
        foreach ($especificos as $e) {
            if (!is_array($e)) {
                continue;
            }

            $idEsp = (int) ($e['id_objetivo_esp'] ?? 0);
            $idEst = (int) ($e['id_objetivo_est'] ?? 0);
            if ($idEsp > 0) {
                $e['token'] = $this->issueObjetivoEspecificoToken($idEsp);
            }

            if (!isset($out[$idEst])) {
                $out[$idEst] = [];
            }

            $out[$idEst][] = $e;
        }

        return $out;
    }

    private function attachEspecificosCountToObjetivosEstrategicos(array $estrategicos, array $especificosByEstrategico): array
    {
        $out = [];
        foreach ($estrategicos as $o) {
            if (!is_array($o)) {
                continue;
            }

            $id = (int) ($o['id_objetivo_est'] ?? 0);
            $o['especificos_count'] = isset($especificosByEstrategico[$id]) ? count($especificosByEstrategico[$id]) : 0;
            $out[] = $o;
        }

        return $out;
    }
}
