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
            'about'      => ['about.php',      'About'],
            'journey'    => ['journey.php',    'Our Journey'],
            'donors'     => ['donors.php',     'Donors'],
            'apply'      => ['apply.php',      'Apply'],
            'financials' => ['financials.php', 'Financials'],
            'contact'    => ['contact.php',    'Contact'],
        ];
    }
}
