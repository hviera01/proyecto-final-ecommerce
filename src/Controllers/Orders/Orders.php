<?php

namespace App\Controllers\Orders;

use App\Controllers\PrivateController;
use App\Dao\Orders\OrdersDao;
use App\Utilities\Security;

class Orders extends PrivateController
{
    protected function execute(): void
    {
        $usercod = Security::getUserId();
        $orders = OrdersDao::getOrdersByUser($usercod);

        foreach ($orders as &$order) {
            $order["ordenestBadge"] = $order["ordenest"] === "PAGADA" ? "badge-ok" : "badge-err";
            $order["transestBadge"] = $order["transest"] === "APROBADA" ? "badge-ok" : "badge-err";
        }
        unset($order);

        $this->viewData["orders"] = $orders;
        $this->viewData["hasOrders"] = count($orders) > 0;
        $this->viewData["pageTitle"] = "Mis Compras";
        $this->renderView("orders/orders");
    }
}
