<?php

require __DIR__ . '/../app/Core/Session.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Services/SupabaseClient.php';
require __DIR__ . '/../app/Models/Persona.php';
require __DIR__ . '/../app/Models/Proyecto.php';
require __DIR__ . '/../app/Models/Mision.php';
require __DIR__ . '/../app/Models/Vision.php';
require __DIR__ . '/../app/Models/Valor.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/ProyectoController.php';

$controller = new ProyectoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save_mision') {
        $controller->saveMision();
    }
    if ($action === 'save_vision') {
        $controller->saveVision();
    }
    if ($action === 'add_valor') {
        $controller->addValor();
    }
    if ($action === 'update_valor') {
        $controller->updateValor();
    }

    header('Location: proyectos.php');
    exit;
}

$controller->show();

