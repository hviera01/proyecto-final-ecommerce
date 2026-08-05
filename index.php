<?php

require_once __DIR__ . "/vendor/autoload.php";

use App\Utilities\Security;

Security::startSession();

$page = $_GET["page"] ?? "Sec_Login";

$parts = explode("_", $page, 2);
if (count($parts) !== 2) {
    $parts = ["Sec", "Login"];
}
[$namespace, $class] = $parts;

$controllerClass = "App\\Controllers\\{$namespace}\\{$class}";

if (!class_exists($controllerClass)) {
    http_response_code(404);
    echo "Pagina no encontrada.";
    exit;
}

new $controllerClass();
