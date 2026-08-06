<?php

namespace App\Dao\Cart;

use App\Dao\Database;

class CartDao
{
    public static function getActiveCart(int $usercod): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM carrito WHERE usercod = :usercod AND carritoest = 'ACT' LIMIT 1");
        $stmt->execute(["usercod" => $usercod]);
        $cart = $stmt->fetch();
        if ($cart) {
            return $cart;
        }

        $ins = $pdo->prepare("INSERT INTO carrito (usercod, carritoest, fchcreacion) VALUES (:usercod, 'ACT', NOW())");
        $ins->execute(["usercod" => $usercod]);
        return [
            "carritocod" => (int) $pdo->lastInsertId(),
            "usercod" => $usercod,
            "carritoest" => "ACT",
        ];
    }

    public static function getItems(int $carritocod): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT ci.prodcod, ci.cantidad, ci.precio_unitario, p.proddsc, p.prodstock,
                    (ci.cantidad * ci.precio_unitario) AS subtotal
             FROM carrito_item ci
             INNER JOIN producto p ON p.prodcod = ci.prodcod
             WHERE ci.carritocod = :carritocod
             ORDER BY p.proddsc"
        );
        $stmt->execute(["carritocod" => $carritocod]);
        return $stmt->fetchAll();
    }

    public static function getQuantityInCart(int $carritocod, int $prodcod): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT cantidad FROM carrito_item WHERE carritocod = :c AND prodcod = :p");
        $stmt->execute(["c" => $carritocod, "p" => $prodcod]);
        $row = $stmt->fetch();
        return $row ? (int) $row["cantidad"] : 0;
    }

    public static function getReservedByOthers(int $prodcod, int $carritocod): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(ci.cantidad), 0) AS reservado
             FROM carrito_item ci
             INNER JOIN carrito c ON c.carritocod = ci.carritocod
             WHERE ci.prodcod = :prodcod AND c.carritoest = 'ACT' AND ci.carritocod <> :carritocod"
        );
        $stmt->execute(["prodcod" => $prodcod, "carritocod" => $carritocod]);
        return (int) $stmt->fetch()["reservado"];
    }

    public static function setItemQuantity(int $carritocod, int $prodcod, int $cantidad, float $precio): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT cantidad FROM carrito_item WHERE carritocod = :c AND prodcod = :p");
        $stmt->execute(["c" => $carritocod, "p" => $prodcod]);

        if ($stmt->fetch()) {
            $upd = $pdo->prepare("UPDATE carrito_item SET cantidad = :cantidad WHERE carritocod = :c AND prodcod = :p");
            $upd->execute(["cantidad" => $cantidad, "c" => $carritocod, "p" => $prodcod]);
        } else {
            $ins = $pdo->prepare(
                "INSERT INTO carrito_item (carritocod, prodcod, cantidad, precio_unitario)
                 VALUES (:c, :p, :cantidad, :precio)"
            );
            $ins->execute(["c" => $carritocod, "p" => $prodcod, "cantidad" => $cantidad, "precio" => $precio]);
        }
    }

    public static function removeItem(int $carritocod, int $prodcod): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM carrito_item WHERE carritocod = :c AND prodcod = :p");
        $stmt->execute(["c" => $carritocod, "p" => $prodcod]);
    }

    public static function markConverted(int $carritocod): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE carrito SET carritoest = 'CONV' WHERE carritocod = :c");
        $stmt->execute(["c" => $carritocod]);
    }
}
