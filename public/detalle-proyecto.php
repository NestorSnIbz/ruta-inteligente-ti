<?php

require __DIR__ . '/../app/Core/Session.php';
require __DIR__ . '/../app/Services/SupabaseClient.php';
require __DIR__ . '/../app/Models/Persona.php';
require __DIR__ . '/../app/Models/Proyecto.php';
require __DIR__ . '/../app/Models/Mision.php';
require __DIR__ . '/../app/Models/Vision.php';
require __DIR__ . '/../app/Models/Valor.php';
require __DIR__ . '/../app/Models/ObjetivoEstrategico.php';
require __DIR__ . '/../app/Models/ObjetivoEspecifico.php';
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
    if ($action === 'save_valores') {
        $controller->saveValores();
    }
    if ($action === 'update_valor') {
        $controller->updateValor();
    }
    if ($action === 'create_obj_est') {
        $controller->createObjetivoEstrategico();
    }
    if ($action === 'update_obj_est') {
        $controller->updateObjetivoEstrategico();
    }
    if ($action === 'delete_obj_est') {
        $controller->deleteObjetivoEstrategico();
    }
    if ($action === 'create_obj_esp') {
        $controller->createObjetivoEspecifico();
    }
    if ($action === 'update_obj_esp') {
        $controller->updateObjetivoEspecifico();
    }
    if ($action === 'delete_obj_esp') {
        $controller->deleteObjetivoEspecifico();
    }

    header('Location: proyectos.php');
    exit;
}

$controller->show();
