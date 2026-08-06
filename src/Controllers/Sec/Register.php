<?php

namespace App\Controllers\Sec;

use App\Controllers\PublicController;
use App\Dao\Security\Security as SecurityDao;

class Register extends PublicController
{
    protected function execute(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $email = trim($_POST["useremail"] ?? "");
            $password = $_POST["userpswd"] ?? "";
            $password2 = $_POST["userpswd2"] ?? "";

            if ($username === "" || $email === "" || $password === "") {
                $this->viewData["errorMessage"] = "Todos los campos son obligatorios.";
            } elseif (strlen($password) < 8) {
                $this->viewData["errorMessage"] = "La contrasena debe tener al menos 8 caracteres.";
            } elseif ($password !== $password2) {
                $this->viewData["errorMessage"] = "Las contrasenas no coinciden.";
            } elseif (SecurityDao::findUserByEmail($email)) {
                $this->viewData["errorMessage"] = "Ya existe una cuenta con ese correo.";
            } else {
                SecurityDao::registerUser($username, $email, $password);
                $this->viewData["successMessage"] = "Cuenta creada correctamente. Ya puede iniciar sesion.";
            }
        }

        $this->viewData["pageTitle"] = "Crear Cuenta";
        $this->renderView("security/signin");
    }
}
