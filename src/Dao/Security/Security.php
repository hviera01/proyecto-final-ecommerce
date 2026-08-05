<?php

namespace App\Dao\Security;

use App\Dao\Database;
use PDO;

class Security
{
    public static function findUserByEmail(string $email): array|false
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE useremail = :email LIMIT 1");
        $stmt->execute(["email" => $email]);
        $row = $stmt->fetch();
        return $row ?: false;
    }

    public static function findUserById(int $usercod): array|false
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE usercod = :usercod LIMIT 1");
        $stmt->execute(["usercod" => $usercod]);
        $row = $stmt->fetch();
        return $row ?: false;
    }

    public static function registerUser(string $username, string $email, string $password): int
    {
        $pdo = Database::getConnection();
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            "INSERT INTO usuario
                (useremail, username, userpswd, userfching, userpswdest, userpswdexp, userest, useractcod, userpswdchg, usertipo)
             VALUES
                (:useremail, :username, :userpswd, NOW(), 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR), 'ACT', :useractcod, 0, 'CLI')"
        );
        $stmt->execute([
            "useremail" => $email,
            "username" => $username,
            "userpswd" => $hash,
            "useractcod" => bin2hex(random_bytes(8)),
        ]);
        $usercod = (int) $pdo->lastInsertId();

        self::assignRole($usercod, "CLIENTE");

        return $usercod;
    }

    public static function assignRole(int $usercod, string $rolescod): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO roles_usuario (usercod, rolescod, roleuserest, roleuserfch, roleuserexp)
             VALUES (:usercod, :rolescod, 'ACT', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))"
        );
        $stmt->execute(["usercod" => $usercod, "rolescod" => $rolescod]);
    }

    public static function validateCredentials(string $email, string $password): array|false
    {
        $user = self::findUserByEmail($email);
        if (!$user) {
            return false;
        }
        if ($user["userest"] !== "ACT") {
            return false;
        }
        if (!password_verify($password, $user["userpswd"])) {
            return false;
        }
        return $user;
    }

    public static function isAuthorized(int $usercod, string $functionCode): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS total
             FROM roles_usuario ru
             INNER JOIN funciones_roles fr ON fr.rolescod = ru.rolescod
             INNER JOIN funciones fn ON fn.fncod = fr.fncod
             WHERE ru.usercod = :usercod
               AND fr.fncod = :fncod
               AND ru.roleuserest = 'ACT'
               AND fr.fnrolest = 'ACT'
               AND fn.fnest = 'ACT'
               AND ru.roleuserexp >= NOW()
               AND fr.fnexp >= NOW()"
        );
        $stmt->execute(["usercod" => $usercod, "fncod" => $functionCode]);
        $row = $stmt->fetch();
        return $row && (int) $row["total"] > 0;
    }
}
