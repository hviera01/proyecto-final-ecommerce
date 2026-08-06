<?php

namespace App\Controllers;

use App\Utilities\Security;
use App\Utilities\View;

abstract class PrivateController extends Controller
{
    public function __construct()
    {
        Security::startSession();

        if (!Security::isLogged()) {
            header("Location: index.php?page=Sec_Login");
            exit;
        }

        $controllerFunction = ltrim(str_replace("App\\", "", static::class), "\\");
        if (!Security::isAuthorized(Security::getUserId(), $controllerFunction)) {
            $this->viewData["errorMessage"] = "No tiene permisos para acceder a este recurso.";
            View::render("errors/forbidden", $this->viewData);
            return;
        }

        parent::__construct();
    }

    protected function isFeatureAuthorized(string $functionCode): bool
    {
        return Security::isAuthorized(Security::getUserId(), $functionCode);
    }
}
