<?php

namespace App\Utilities;

class TemplateEngine
{
    public static function render(string $templateContent, array $data): string
    {
        $php = self::compile($templateContent);
        $cacheFile = tempnam(sys_get_temp_dir(), "tpl_");
        file_put_contents($cacheFile, $php);

        $__ctx = $data;
        $__stack = [];
        ob_start();
        include $cacheFile;
        $html = ob_get_clean();
        unlink($cacheFile);

        return $html;
    }

    public static function val(array $ctx, array $stack, string $name, bool $outer): mixed
    {
        if (!$outer) {
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if (is_array($stack[$i]) && array_key_exists($name, $stack[$i])) {
                    return $stack[$i][$name];
                }
            }
        }
        return $ctx[$name] ?? null;
    }

    private static function compile(string $tpl): string
    {
        $tokens = preg_split('/(\{\{.*?\}\})/s', $tpl, -1, PREG_SPLIT_DELIM_CAPTURE);

        $out = "<?php \$__val = ['" . self::class . "', 'val']; ?>\n";

        foreach ($tokens as $token) {
            if ($token === "") {
                continue;
            }

            if (preg_match('/^\{\{\s*if\s+(~?)([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= self::phpTag("if (!empty(call_user_func(\$__val, \$__ctx, \$__stack, '{$m[2]}', " . ($m[1] === "~" ? "true" : "false") . "))):");
                continue;
            }
            if (preg_match('/^\{\{\s*endif(?:\s+~?[A-Za-z0-9_]+)?\s*\}\}$/', $token)) {
                $out .= self::phpTag("endif;");
                continue;
            }
            if (preg_match('/^\{\{\s*ifnot\s+(~?)([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= self::phpTag("if (empty(call_user_func(\$__val, \$__ctx, \$__stack, '{$m[2]}', " . ($m[1] === "~" ? "true" : "false") . "))):");
                continue;
            }
            if (preg_match('/^\{\{\s*endifnot(?:\s+~?[A-Za-z0-9_]+)?\s*\}\}$/', $token)) {
                $out .= self::phpTag("endif;");
                continue;
            }
            if (preg_match('/^\{\{\s*foreach\s+([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= self::phpTag("foreach ((array)(\$__ctx['{$m[1]}'] ?? []) as \$__cur): \$__stack[] = \$__cur;");
                continue;
            }
            if (preg_match('/^\{\{\s*endfor(?:\s+[A-Za-z0-9_]+)?\s*\}\}$/', $token)) {
                $out .= self::phpTag("array_pop(\$__stack); endforeach;");
                continue;
            }
            if (preg_match('/^\{\{\s*~\s*([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= "<?= htmlspecialchars((string)(call_user_func(\$__val, \$__ctx, \$__stack, '{$m[1]}', true) ?? ''), ENT_QUOTES, 'UTF-8') ?>";
                continue;
            }
            if (preg_match('/^\{\{\s*!\s*([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= "<?= (string)(call_user_func(\$__val, \$__ctx, \$__stack, '{$m[1]}', true) ?? '') ?>";
                continue;
            }
            if (preg_match('/^\{\{\s*([A-Za-z0-9_]+)\s*\}\}$/', $token, $m)) {
                $out .= "<?= htmlspecialchars((string)(call_user_func(\$__val, \$__ctx, \$__stack, '{$m[1]}', false) ?? ''), ENT_QUOTES, 'UTF-8') ?>";
                continue;
            }

            $out .= $token;
        }

        return $out;
    }

    private static function phpTag(string $code): string
    {
        return "<?php {$code} ?>";
    }
}
