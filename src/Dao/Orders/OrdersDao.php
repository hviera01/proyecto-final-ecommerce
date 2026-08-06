<?php

namespace App\Dao\Orders;

use App\Dao\Database;

class OrdersDao
{
    public static function createOrder(int $usercod, float $total, string $estado): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO orden (usercod, ordenfecha, ordentotal, ordenest) VALUES (:usercod, NOW(), :total, :estado)"
        );
        $stmt->execute(["usercod" => $usercod, "total" => $total, "estado" => $estado]);
        return (int) $pdo->lastInsertId();
    }

    public static function addOrderItem(int $ordencod, int $prodcod, string $proddsc, int $cantidad, float $precio, float $subtotal): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO orden_item (ordencod, prodcod, proddsc, cantidad, precio_unitario, subtotal)
             VALUES (:ordencod, :prodcod, :proddsc, :cantidad, :precio, :subtotal)"
        );
        $stmt->execute([
            "ordencod" => $ordencod,
            "prodcod" => $prodcod,
            "proddsc" => $proddsc,
            "cantidad" => $cantidad,
            "precio" => $precio,
            "subtotal" => $subtotal,
        ]);
    }

    public static function addTransaction(int $ordencod, float $monto, string $metodo, string $estado, string $ref): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO transaccion (ordencod, transfecha, transmonto, transmetodo, transest, transref)
             VALUES (:ordencod, NOW(), :monto, :metodo, :estado, :ref)"
        );
        $stmt->execute([
            "ordencod" => $ordencod,
            "monto" => $monto,
            "metodo" => $metodo,
            "estado" => $estado,
            "ref" => $ref,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function getOrdersByUser(int $usercod): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT o.ordencod, o.ordenfecha, o.ordentotal, o.ordenest,
                    t.transest, t.transref, t.transmetodo
             FROM orden o
             LEFT JOIN transaccion t ON t.ordencod = o.ordencod
             WHERE o.usercod = :usercod
             ORDER BY o.ordenfecha DESC"
        );
        $stmt->execute(["usercod" => $usercod]);
        return $stmt->fetchAll();
    }

    public static function getOrderItems(int $ordencod): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orden_item WHERE ordencod = :ordencod");
        $stmt->execute(["ordencod" => $ordencod]);
        return $stmt->fetchAll();
    }

    public static function getOrderById(int $ordencod, int $usercod): array|false
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orden WHERE ordencod = :ordencod AND usercod = :usercod");
        $stmt->execute(["ordencod" => $ordencod, "usercod" => $usercod]);
        $row = $stmt->fetch();
        return $row ?: false;
    }
}
