<?php
/**
 * Small shared helpers used by every page. No side effects, no output.
 * Safe to include more than once.
 */

if (!function_exists('h')) {
    /** Escape a string for safe output inside HTML. */
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('nav_items')) {
    /**
     * Main navigation, in display order.
     * Key = page id used in $page, value = [href, label].
     */
    function nav_items(): array
    {
        return [
            'index'      => ['index.php',      'Home'],
            'about'      => ['about.php',      'About us'],
            'journey'    => ['journey.php',    'Our Journey'],
            'donors'     => ['donors.php',     'Donors'],
            'apply'      => ['apply.php',      'Apply'],
            'financials' => ['financials.php', 'Financials'],
            'contact'    => ['contact.php',    'Contact'],
        ];
    }
}

if (!function_exists('page_href')) {
    /**
     * Link target for a site page, e.g. page_href('donate.php').
     * TRANSITIONAL: while the static→PHP conversion is in progress, pages that have
     * not been converted yet still exist only as .html, so fall back to that twin.
     * Once every page is .php this always returns $php unchanged and can be removed.
     */
    function page_href(string $php): string
    {
        $root = dirname(__DIR__) . '/';
        if (is_file($root . $php)) {
            return $php;
        }
        $html = preg_replace('/\.php$/', '.html', $php);
        return is_file($root . $html) ? $html : $php;
    }
}
