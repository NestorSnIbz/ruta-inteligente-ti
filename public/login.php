<?php

require __DIR__ . '/../app/Core/Session.php';
require __DIR__ . '/../app/Services/SupabaseClient.php';
require __DIR__ . '/../app/Models/Persona.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/RegisterController.php';

$authController = new AuthController();
$registerController = new RegisterController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'login');
    if ($action === 'register') {
        $registerController->register();
    }
    $authController->login();
}

$authController->showLogin();
