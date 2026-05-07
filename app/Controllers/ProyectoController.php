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
            $proyectos = Proyecto::listByCreador((int) $authUser['id_persona']);
        } catch (Throwable $e) {
            $proyectos = [];
            $error = $error ?: 'No se pudo cargar la lista de proyectos. Revisa DATABASE_URL.';
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
            $idProyecto = Proyecto::create((int) $authUser['id_persona'], $nombre);
        } catch (Throwable $e) {
            Session::flash('error', 'No se pudo crear el proyecto. Revisa DATABASE_URL y la estructura de la base de datos.');
            $this->redirect('/nuevo-proyecto.php');
        }

        Session::flash('success', 'Proyecto creado correctamente.');
        $this->redirect('/detalle-proyecto.php?id=' . $idProyecto);
    }

    public function show(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $idProyecto = (int) ($_GET['id'] ?? 0);
        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        try {
            // Se valida propiedad del proyecto (creador_id) para evitar acceso a datos de terceros.
            $proyecto = Proyecto::findOwnedById($idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                Session::flash('error', 'No tienes acceso a este proyecto.');
                $this->redirect('/proyectos.php');
            }

            $mision = Mision::findByProyecto($idProyecto);
            $vision = Vision::findByProyecto($idProyecto);
            $valores = Valor::listByProyecto($idProyecto);
        } catch (Throwable $e) {
            Session::flash('error', 'No se pudo cargar el proyecto. Revisa DATABASE_URL.');
            $this->redirect('/proyectos.php');
        }

        $edit = (string) ($_GET['edit'] ?? '');
        $editValorId = (int) ($_GET['valor'] ?? 0);
        $valorToEdit = null;
        if ($edit === 'valor' && $editValorId > 0) {
            $valorToEdit = Valor::findById($editValorId, $idProyecto);
            if ($valorToEdit === null) {
                $edit = '';
            }
        }

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

        $idProyecto = (int) ($_POST['id_proyecto'] ?? 0);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?id=' . $idProyecto . '&edit=valores');
        }

        $proyecto = Proyecto::findOwnedById($idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        Valor::create($idProyecto, $descripcion);
        Session::flash('success', 'Valor agregado correctamente.');
        $this->redirect('/detalle-proyecto.php?id=' . $idProyecto);
    }

    public function updateValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $idProyecto = (int) ($_POST['id_proyecto'] ?? 0);
        $idValor = (int) ($_POST['id_valor'] ?? 0);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0 || $idValor <= 0) {
            Session::flash('error', 'Valor inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?id=' . $idProyecto . '&edit=valor&valor=' . $idValor);
        }

        $proyecto = Proyecto::findOwnedById($idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        $ok = Valor::update($idValor, $idProyecto, $descripcion);
        if (!$ok) {
            Session::flash('error', 'No se pudo actualizar el valor.');
            $this->redirect('/detalle-proyecto.php?id=' . $idProyecto);
        }

        Session::flash('success', 'Valor actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?id=' . $idProyecto);
    }

    private function saveSingleTextBlock(string $block, string $editQuery, string $emptyMessage): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $idProyecto = (int) ($_POST['id_proyecto'] ?? 0);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '') {
            Session::flash('error', $emptyMessage);
            $this->redirect('/detalle-proyecto.php?id=' . $idProyecto . '&edit=' . $editQuery);
        }

        $proyecto = Proyecto::findOwnedById($idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if ($block === 'mision') {
            Mision::save($idProyecto, $descripcion);
        } elseif ($block === 'vision') {
            Vision::save($idProyecto, $descripcion);
        }

        Session::flash('success', 'Cambios guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?id=' . $idProyecto);
    }

    private function redirect(string $path): void
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '\\/');
        $location = $basePath === '' ? $path : ($basePath . $path);
        header('Location: ' . $location);
        exit;
    }
}
