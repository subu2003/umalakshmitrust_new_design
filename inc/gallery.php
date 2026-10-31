<?php
/**
 * Photo gallery for Our Journey, driven by folders under assets/gallery/.
 *
 *   assets/gallery/2022/*.jpg   → shown under "2022" and "All"
 *   assets/gallery/undated/*.jpg → shown under "All" only
 *
 * Nothing is stored in the database: trustees upload photos into the year folder
 * with cPanel File Manager and they appear on the next page load. Each visit shows
 * a fresh random selection (up to $perYear per year).
 */

/**
 * Years always offered in the filter. Any other 4-digit folder under assets/gallery/
 * (e.g. 2026) is added automatically once it contains at least one photo.
 */
function gallery_base_years(): array
{
    return ['2022', '2023', '2024', '2025'];
}

/** Image files directly inside $dir (no recursion), natural-sorted. */
function gallery_files(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $out = [];
    foreach (scandir($dir) ?: [] as $f) {
        if ($f === '' || $f[0] === '.') {
            continue;
        }
        if (preg_match('/\.(jpe?g|png|webp)$/i', $f) && is_file($dir . '/' . $f)) {
            $out[] = $f;
        }
    }
    natcasesort($out);
    return array_values($out);
}

/**
 * Build the gallery.
 *
 * @param string $root     absolute path of assets/gallery
 * @param string $webRoot  URL prefix for that folder, relative to the page
 * @return array{years: string[], counts: array<string,int>, tiles: array<int, array{src:string, year:string, alt:string}>}
 */
function gallery_build(string $root, string $webRoot = 'assets/gallery', int $perYear = 8, int $undated = 4): array
{
    $years = gallery_base_years();
    if (is_dir($root)) {
        foreach (scandir($root) ?: [] as $d) {
            if (preg_match('/^\d{4}$/', $d) && is_dir($root . '/' . $d) && gallery_files($root . '/' . $d)) {
                $years[] = $d;
            }
        }
    }
    $years = array_values(array_unique($years));
    sort($years, SORT_STRING);

    $tiles  = [];
    $counts = [];
    foreach ($years as $y) {
        $files      = gallery_files($root . '/' . $y);
        $counts[$y] = count($files);
        shuffle($files);
        foreach (array_slice($files, 0, $perYear) as $f) {
            $tiles[] = [
                'src'  => $webRoot . '/' . $y . '/' . rawurlencode($f),
                'year' => $y,
                'alt'  => 'Photograph from the ' . $y . ' sponsorship function',
            ];
        }
    }
    $files = gallery_files($root . '/undated');
    shuffle($files);
    foreach (array_slice($files, 0, $undated) as $f) {
        $tiles[] = [
            'src'  => $webRoot . '/undated/' . rawurlencode($f),
            'year' => '',
            'alt'  => 'Photograph from a sponsorship function',
        ];
    }
    shuffle($tiles);

    return ['years' => $years, 'counts' => $counts, 'tiles' => $tiles];
}
