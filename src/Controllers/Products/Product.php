<?php

namespace App\Controllers\Products;

use App\Controllers\PrivateController;
use App\Dao\Products\ProductsDao;

class Product extends PrivateController
{
    private array $modeDescriptions = [
        "DSP" => "Detalle de Producto",
        "UPD" => "Editar Producto",
        "DEL" => "Eliminar Producto",
        "INS" => "Nuevo Producto",
    ];

    private string $mode = "";
    private string $readonly = "";
    private bool $showCommitBtn = false;
    private array $product = [];

    protected function execute(): void
    {
        $this->getData();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->commitData();
            return;
        }

        $this->setParamsToDataView();
        $this->viewData["pageTitle"] = $this->modeDescriptions[$this->mode];
        $this->renderView("products/product");
    }

    private function getData(): void
    {
        $this->mode = $_REQUEST["mode"] ?? "NOF";

        if (!isset($this->modeDescriptions[$this->mode])) {
            throw new \Exception("Formulario cargado en modalidad invalida.");
        }

        if (!$this->isFeatureAuthorized("product_" . $this->mode)) {
            throw new \Exception("No tiene permisos para realizar esta accion.");
        }

        $this->readonly = $this->mode === "DEL" ? "readonly" : "";
        $this->showCommitBtn = $this->mode !== "DSP";

        if ($this->mode !== "INS") {
            $prodcod = intval($_REQUEST["prodcod"] ?? 0);
            $this->product = ProductsDao::getProductById($prodcod);
            if (!$this->product) {
                throw new \Exception("No se encontro el producto.");
            }
        }
    }

    private function commitData(): void
    {
        if ($this->mode === "INS") {
            ProductsDao::insertProduct(
                trim($_POST["proddsc"] ?? ""),
                trim($_POST["proddet"] ?? ""),
                intval($_POST["catcod"] ?? 0),
                floatval($_POST["prodprecio"] ?? 0),
                intval($_POST["prodstock"] ?? 0),
                $this->uploadImage()
            );
        } elseif ($this->mode === "UPD") {
            ProductsDao::updateProduct(
                intval($this->product["prodcod"]),
                trim($_POST["proddsc"] ?? ""),
                trim($_POST["proddet"] ?? ""),
                intval($_POST["catcod"] ?? 0),
                floatval($_POST["prodprecio"] ?? 0),
                intval($_POST["prodstock"] ?? 0),
                $this->uploadImage()
            );
        } elseif ($this->mode === "DEL") {
            ProductsDao::deactivateProduct(intval($this->product["prodcod"]));
        }

        $this->redirect("Products_Products");
    }

    private function uploadImage(): ?string
    {
        if (!isset($_FILES["prodimg"]) || $_FILES["prodimg"]["error"] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp"];
        $ext = strtolower(pathinfo($_FILES["prodimg"]["name"], PATHINFO_EXTENSION));

        if (!isset($allowed[$ext]) || $_FILES["prodimg"]["size"] > 3 * 1024 * 1024) {
            return null;
        }

        $filename = uniqid("prod_", true) . "." . $ext;
        $destination = __DIR__ . "/../../../public/img/productos/" . $filename;
        move_uploaded_file($_FILES["prodimg"]["tmp_name"], $destination);

        return $filename;
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["modeTitle"] = $this->modeDescriptions[$this->mode];
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["isInsert"] = $this->mode === "INS";
        $this->viewData["isDelete"] = $this->mode === "DEL";

        $this->viewData["prodcod"] = $this->product["prodcod"] ?? "";
        $this->viewData["proddsc"] = $this->product["proddsc"] ?? "";
        $this->viewData["proddet"] = $this->product["proddet"] ?? "";
        $this->viewData["prodprecio"] = $this->product["prodprecio"] ?? "";
        $this->viewData["prodstock"] = $this->product["prodstock"] ?? "";
        $this->viewData["prodimg"] = $this->product["prodimg"] ?? "";
        $this->viewData["hasImage"] = !empty($this->product["prodimg"]);
        $this->viewData["placeholderLetter"] = strtoupper(substr((string) ($this->product["proddsc"] ?? "?"), 0, 1));
        $this->viewData["placeholderClass"] = "ph-" . ((int) ($this->product["catcod"] ?? 0) % 4);
        $this->viewData["canEditImage"] = in_array($this->mode, ["INS", "UPD"], true);

        $selectedCatcod = intval($this->product["catcod"] ?? 0);
        $categorias = ProductsDao::getCategories();
        foreach ($categorias as &$categoria) {
            $categoria["selected"] = ((int) $categoria["catcod"] === $selectedCatcod);
        }
        unset($categoria);
        $this->viewData["categorias"] = $categorias;

        $this->viewData["canAddToCart"] = $this->mode === "DSP"
            && ($this->product["prodest"] ?? "") === "ACT"
            && (int) ($this->product["prodstock"] ?? 0) > 0;
    }
}
