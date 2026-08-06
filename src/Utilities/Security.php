<?php

namespace App\Utilities;

use App\Dao\Security\Security as SecurityDao;

class Security
{
    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION["usercod"] = (int) $user["usercod"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["useremail"] = $user["useremail"];
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();
    }

    public static function isLogged(): bool
    {
        self::startSession();
        return isset($_SESSION["usercod"]);
    }

    public static function getUserId(): ?int
    {
        self::startSession();
        return $_SESSION["usercod"] ?? null;
    }

    public static function getUser(): array|false
    {
        if (!self::isLogged()) {
            return false;
        }
        return SecurityDao::findUserById(self::getUserId());
    }

    public static function isAuthorized(?int $usercod, string $functionCode): bool
    {
        if ($usercod === null) {
            return false;
        }
        return SecurityDao::isAuthorized($usercod, $functionCode);
    }
}
