<?php

namespace App\Controllers\Checkout;

use App\Controllers\PrivateController;
use App\Dao\Cart\CartDao;
use App\Dao\Orders\OrdersDao;
use App\Dao\Products\ProductsDao;
use App\Utilities\Security;

class Checkout extends PrivateController
{
    protected function execute(): void
    {
        $usercod = Security::getUserId();
        $cart = CartDao::getActiveCart($usercod);
        $mode = $_REQUEST["mode"] ?? "LIST";

        switch ($mode) {
            case "ADD":
                $this->handleAdd($cart);
                return;
            case "UPDATE":
                $this->handleUpdate($cart);
                return;
            case "REMOVE":
                $this->handleRemove($cart);
                return;
            case "PAYFORM":
                $this->showPaymentForm($cart);
                return;
            case "PAY":
                $this->handlePay($cart, $usercod);
                return;
            default:
                $this->showCart($cart);
        }
    }

    private function handleAdd(array $cart): void
    {
        $prodcod = intval($_POST["prodcod"] ?? 0);
        $cantidad = intval($_POST["cantidad"] ?? 0);

        $product = ProductsDao::getProductById($prodcod);
        if (!$product || $product["prodest"] !== "ACT") {
            $this->flashError("El producto ya no esta disponible.");
            $this->redirect("Checkout_Checkout");
        }

        if ($cantidad < 1) {
            $this->flashError("La cantidad debe ser mayor a cero.");
            $this->redirect("Checkout_Checkout");
        }

        $carritocod = (int) $cart["carritocod"];
        $reservadoOtros = CartDao::getReservedByOthers($prodcod, $carritocod);
        $disponible = (int) $product["prodstock"] - $reservadoOtros;

        $cantidadActual = CartDao::getQuantityInCart($carritocod, $prodcod);
        $nuevaCantidad = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $disponible) {
            $this->flashError("Solo hay {$disponible} unidades disponibles de {$product['proddsc']}.");
            $this->redirect("Checkout_Checkout");
        }

        CartDao::setItemQuantity($carritocod, $prodcod, $nuevaCantidad, (float) $product["prodprecio"]);
        $this->flashSuccess("Producto agregado al carrito.");
        $this->redirect("Checkout_Checkout");
    }

    private function handleUpdate(array $cart): void
    {
        $prodcod = intval($_POST["prodcod"] ?? 0);
        $cantidad = intval($_POST["cantidad"] ?? 0);
        $carritocod = (int) $cart["carritocod"];

        if ($cantidad < 1) {
            CartDao::removeItem($carritocod, $prodcod);
            $this->redirect("Checkout_Checkout");
        }

        $product = ProductsDao::getProductById($prodcod);
        $reservadoOtros = CartDao::getReservedByOthers($prodcod, $carritocod);
        $disponible = (int) $product["prodstock"] - $reservadoOtros;

        if ($cantidad > $disponible) {
            $this->flashError("Solo hay {$disponible} unidades disponibles de {$product['proddsc']}.");
            $this->redirect("Checkout_Checkout");
        }

        CartDao::setItemQuantity($carritocod, $prodcod, $cantidad, (float) $product["prodprecio"]);
        $this->redirect("Checkout_Checkout");
    }

    private function handleRemove(array $cart): void
    {
        $prodcod = intval($_POST["prodcod"] ?? $_GET["prodcod"] ?? 0);
        CartDao::removeItem((int) $cart["carritocod"], $prodcod);
        $this->redirect("Checkout_Checkout");
    }

    private function showCart(array $cart): void
    {
        $items = CartDao::getItems((int) $cart["carritocod"]);
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item["subtotal"];
        }

        $this->pullFlash();
        $this->viewData["items"] = $items;
        $this->viewData["total"] = number_format($total, 2);
        $this->viewData["hasItems"] = count($items) > 0;
        $this->viewData["pageTitle"] = "Carretilla de Compra";
        $this->renderView("checkout/checkout");
    }

    private function showPaymentForm(array $cart): void
    {
        $items = CartDao::getItems((int) $cart["carritocod"]);
        if (count($items) === 0) {
            $this->flashError("Su carrito esta vacio.");
            $this->redirect("Checkout_Checkout");
        }

        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item["subtotal"];
        }

        $this->pullFlash();
        $this->viewData["items"] = $items;
        $this->viewData["total"] = number_format($total, 2);
        $this->viewData["pageTitle"] = "Pago";
        $this->renderView("checkout/pago");
    }

    private function handlePay(array $cart, int $usercod): void
    {
        $carritocod = (int) $cart["carritocod"];
        $items = CartDao::getItems($carritocod);

        if (count($items) === 0) {
            $this->flashError("Su carrito esta vacio.");
            $this->redirect("Checkout_Checkout");
        }

        foreach ($items as $item) {
            $product = ProductsDao::getProductById((int) $item["prodcod"]);
            $reservadoOtros = CartDao::getReservedByOthers((int) $item["prodcod"], $carritocod);
            $disponible = (int) $product["prodstock"] - $reservadoOtros;
            if ((int) $item["cantidad"] > $disponible) {
                $this->flashError("El stock de {$item['proddsc']} cambio. Ajuste la cantidad.");
                $this->redirect("Checkout_Checkout");
            }
        }

        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item["subtotal"];
        }

        $numeroTarjeta = preg_replace('/\s+/', '', $_POST["numerotarjeta"] ?? "");
        $nombreTarjeta = trim($_POST["nombretarjeta"] ?? "");
        $vencimiento = trim($_POST["vencimiento"] ?? "");
        $cvv = trim($_POST["cvv"] ?? "");

        $datosValidos = $nombreTarjeta !== ""
            && preg_match('/^\d{13,19}$/', $numeroTarjeta)
            && preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $vencimiento)
            && preg_match('/^\d{3,4}$/', $cvv);

        $ultimoDigito = $datosValidos ? intval(substr($numeroTarjeta, -1)) : -1;
        $aprobado = $datosValidos && ($ultimoDigito % 2 === 0);

        $ordencod = OrdersDao::createOrder($usercod, $total, $aprobado ? "PAGADA" : "RECHAZADA");
        foreach ($items as $item) {
            OrdersDao::addOrderItem(
                $ordencod,
                (int) $item["prodcod"],
                $item["proddsc"],
                (int) $item["cantidad"],
                (float) $item["precio_unitario"],
                (float) $item["subtotal"]
            );
        }

        $ref = strtoupper(bin2hex(random_bytes(5)));
        OrdersDao::addTransaction($ordencod, $total, "TARJETA", $aprobado ? "APROBADA" : "RECHAZADA", $ref);

        if ($aprobado) {
            foreach ($items as $item) {
                ProductsDao::decreaseStock((int) $item["prodcod"], (int) $item["cantidad"]);
            }
            CartDao::markConverted($carritocod);
            $this->flashSuccess("Pago aprobado. Referencia: {$ref}");
        } else {
            $this->flashError(!$datosValidos
                ? "Los datos de la tarjeta no son validos."
                : "El pago fue rechazado por la pasarela. Referencia: {$ref}");
        }

        $this->redirect("Orders_Orders");
    }

    private function flashError(string $message): void
    {
        $_SESSION["flash_error"] = $message;
    }

    private function flashSuccess(string $message): void
    {
        $_SESSION["flash_success"] = $message;
    }

    private function pullFlash(): void
    {
        $this->viewData["errorMessage"] = $_SESSION["flash_error"] ?? "";
        $this->viewData["successMessage"] = $_SESSION["flash_success"] ?? "";
        unset($_SESSION["flash_error"], $_SESSION["flash_success"]);
    }
}
