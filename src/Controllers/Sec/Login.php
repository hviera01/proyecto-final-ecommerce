<?php

namespace App\Controllers\Sec;

use App\Controllers\PublicController;
use App\Dao\Security\Security as SecurityDao;
use App\Utilities\Security;

class Login extends PublicController
{
    protected function execute(): void
    {
        if (Security::isLogged()) {
            $this->redirect("Products_Products");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST["useremail"] ?? "");
            $password = $_POST["userpswd"] ?? "";

            $user = SecurityDao::validateCredentials($email, $password);
            if ($user) {
                Security::login($user);
                $this->redirect("Products_Products");
            }

            $this->viewData["errorMessage"] = "Correo o contrasena incorrectos, o la cuenta esta inactiva.";
        }

        $this->viewData["pageTitle"] = "Iniciar Sesion";
        $this->renderView("security/login");
    }
}
