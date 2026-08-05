<?php

namespace App\Dao\Products;

use App\Dao\Database;

class ProductsDao
{
    public static function getAllProducts(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            "SELECT p.prodcod, p.proddsc, p.proddet, p.prodprecio, p.prodstock, p.prodest, c.catdsc
             FROM producto p
             INNER JOIN categoria c ON c.catcod = p.catcod
             ORDER BY p.proddsc"
        );
        return $stmt->fetchAll();
    }

    public static function getProductById(int $prodcod): array|false
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM producto WHERE prodcod = :prodcod");
        $stmt->execute(["prodcod" => $prodcod]);
        $row = $stmt->fetch();
        return $row ?: false;
    }

    public static function getCategories(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT * FROM categoria WHERE catest = 'ACT' ORDER BY catdsc")->fetchAll();
    }

    public static function insertProduct(string $proddsc, string $proddet, int $catcod, float $prodprecio, int $prodstock): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO producto (proddsc, proddet, catcod, prodprecio, prodstock, prodest, prodfching)
             VALUES (:proddsc, :proddet, :catcod, :prodprecio, :prodstock, 'ACT', NOW())"
        );
        $stmt->execute([
            "proddsc" => $proddsc,
            "proddet" => $proddet,
            "catcod" => $catcod,
            "prodprecio" => $prodprecio,
            "prodstock" => $prodstock,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function updateProduct(int $prodcod, string $proddsc, string $proddet, int $catcod, float $prodprecio, int $prodstock): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "UPDATE producto
             SET proddsc = :proddsc, proddet = :proddet, catcod = :catcod, prodprecio = :prodprecio, prodstock = :prodstock
             WHERE prodcod = :prodcod"
        );
        $stmt->execute([
            "proddsc" => $proddsc,
            "proddet" => $proddet,
            "catcod" => $catcod,
            "prodprecio" => $prodprecio,
            "prodstock" => $prodstock,
            "prodcod" => $prodcod,
        ]);
    }

    public static function deactivateProduct(int $prodcod): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE producto SET prodest = 'INA' WHERE prodcod = :prodcod");
        $stmt->execute(["prodcod" => $prodcod]);
    }

    public static function decreaseStock(int $prodcod, int $cantidad): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE producto SET prodstock = prodstock - :cantidad WHERE prodcod = :prodcod");
        $stmt->execute(["cantidad" => $cantidad, "prodcod" => $prodcod]);
    }
}
