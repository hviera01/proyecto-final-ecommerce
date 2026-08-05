<?php

namespace App\Controllers\Orders;

use App\Controllers\PrivateController;
use App\Dao\Orders\OrdersDao;
use App\Utilities\Security;

class Order extends PrivateController
{
    protected function execute(): void
    {
        $ordencod = intval($_GET["ordencod"] ?? 0);
        $usercod = Security::getUserId();

        $order = OrdersDao::getOrderById($ordencod, $usercod);
        if (!$order) {
            throw new \Exception("No se encontro la compra solicitada.");
        }

        $this->viewData["ordencod"] = $order["ordencod"];
        $this->viewData["ordenfecha"] = $order["ordenfecha"];
        $this->viewData["ordentotal"] = $order["ordentotal"];
        $this->viewData["ordenest"] = $order["ordenest"];
        $this->viewData["ordenestBadge"] = $order["ordenest"] === "PAGADA" ? "badge-ok" : "badge-err";
        $this->viewData["items"] = OrdersDao::getOrderItems($ordencod);
        $this->viewData["pageTitle"] = "Detalle de Compra";
        $this->renderView("orders/order");
    }
}
