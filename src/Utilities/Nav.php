<?php

namespace App\Utilities;

class Nav
{
    public static function build(): array
    {
        $configPath = __DIR__ . "/../Config/nav.config.json";
        $config = json_decode(file_get_contents($configPath), true);

        if (!Security::isLogged()) {
            return $config["public"] ?? [];
        }

        $usercod = Security::getUserId();
        $menu = [];
        foreach ($config["private"] as $item) {
            if (Security::isAuthorized($usercod, $item["id"])) {
                $menu[] = $item;
            }
        }
        return $menu;
    }
}
