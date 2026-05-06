<?php

require __DIR__ . '/../app/Core/Session.php';
require __DIR__ . '/../app/Controllers/AuthController.php';

$controller = new AuthController();
$authUser = $controller->requireAuth();

require __DIR__ . '/../app/Views/dashboard/index.php';

