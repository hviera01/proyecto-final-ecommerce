<?php

namespace App\Controllers;

use App\Utilities\View;

abstract class Controller
{
    protected array $viewData = [];

    public function __construct()
    {
        try {
            $this->execute();
        } catch (\Exception $e) {
            $this->viewData["errorMessage"] = $e->getMessage();
            View::render("errors/error", $this->viewData);
        }
    }

    abstract protected function execute(): void;

    protected function renderView(string $template): void
    {
        View::render($template, $this->viewData);
    }

    protected function redirect(string $page, array $params = []): void
    {
        $query = array_merge(["page" => $page], $params);
        header("Location: index.php?" . http_build_query($query));
        exit;
    }
}
