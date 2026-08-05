<?php

namespace App\Utilities;

class View
{
    private const TEMPLATES_DIR = __DIR__ . "/../Views/templates";

    public static function render(string $template, array $data = [], bool $withLayout = true): void
    {
        $contentHtml = self::renderTemplate($template, $data);

        if (!$withLayout) {
            echo $contentHtml;
            return;
        }

        $layoutData = [
            "content" => $contentHtml,
            "pageTitle" => $data["pageTitle"] ?? "Variedades Lopsi",
            "isLogged" => Security::isLogged(),
            "username" => Security::isLogged() ? ($_SESSION["username"] ?? "") : "",
            "nav" => Nav::build(),
        ];

        echo self::renderTemplate("layout/main", $layoutData, false);
    }

    private static function renderTemplate(string $template, array $data, bool $prependNothing = true): string
    {
        $file = self::TEMPLATES_DIR . "/" . $template . ".view.tpl";
        if (!file_exists($file)) {
            throw new \RuntimeException("No existe la plantilla: {$template}.view.tpl");
        }
        $content = file_get_contents($file);
        return TemplateEngine::render($content, $data);
    }
}
