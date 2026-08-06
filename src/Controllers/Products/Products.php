<?php

namespace App\Controllers\Products;

use App\Controllers\PrivateController;
use App\Dao\Products\ProductsDao;

class Products extends PrivateController
{
    private array $products = [];
    private bool $product_DSP = false;
    private bool $product_UPD = false;
    private bool $product_DEL = false;
    private bool $product_INS = false;

    protected function execute(): void
    {
        $this->getParamsFromContext();
        $this->setParamsToDataView();
        $this->viewData["pageTitle"] = "Catalogo";
        $this->renderView("products/products");
    }

    private function getParamsFromContext(): void
    {
        $this->product_DSP = $this->isFeatureAuthorized("product_DSP");
        $this->product_UPD = $this->isFeatureAuthorized("product_UPD");
        $this->product_DEL = $this->isFeatureAuthorized("product_DEL");
        $this->product_INS = $this->isFeatureAuthorized("product_INS");
        $this->products = ProductsDao::getAllProducts();

        foreach ($this->products as &$producto) {
            $producto["hasImage"] = !empty($producto["prodimg"]);
            $producto["placeholderLetter"] = strtoupper(substr((string) $producto["proddsc"], 0, 1));
            $producto["placeholderClass"] = "ph-" . ((int) $producto["catcod"] % 4);
            $producto["agotado"] = (int) $producto["prodstock"] <= 0;
        }
        unset($producto);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["products"] = $this->products;
        $this->viewData["product_DSP"] = $this->product_DSP;
        $this->viewData["product_UPD"] = $this->product_UPD;
        $this->viewData["product_DEL"] = $this->product_DEL;
        $this->viewData["product_INS"] = $this->product_INS;
    }
}
