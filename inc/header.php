<?php
/**
 * Closes <head>, opens <body> and prints the site header (logo, pill nav, Donate button, burger).
 * Include AFTER inc/head.php and the page's <style> block(s).
 *
 * Uses $page (set before inc/head.php) to mark the active nav item.
 *
 * Optional:
 *   $header_band  bool  true on donors/apply/financials/donate/contact, which show the header
 *                       inside an indigo band together with the page intro. When true this file
 *                       OPENS the band <div>; the page must CLOSE it with </div> right after its
 *                       intro <section>. Home/about/journey leave it unset: their header sits
 *                       over the hero image and the brand text carries a shadow.
 */
require_once __DIR__ . '/functions.php';

$page        = isset($page) ? (string) $page : '';
$header_band = !empty($header_band);
$brand_shadow = $header_band ? '' : ' text-shadow: 0 2px 12px rgba(0,0,0,0.35);';
$sub_shadow   = $header_band ? '' : ' text-shadow: 0 2px 10px rgba(0,0,0,0.35);';
?>
<style>/* Header row: the "About us" and "Scholarship" labels plus the heart make the row wider than the original, so to keep it on one line inside the 1240px container the row gap is 12px (was 24), nav pills use 10px side padding (was 14) and the Donate pill 20px (was 24). ~20px to spare at 1304px and wider. */.nav a{padding:10px 10px}
/* Header Donate button: heart icon + "Donate Now" on desktop (no flex gap between the words). Below 860px the row (logo, brand, Donate, burger) has no spare width, so the pill becomes a heart-only circle the same height as the burger; the aria-label keeps it readable. */.hdr-donate{gap:0}.hdr-ico{width:18px;height:18px;flex:0 0 18px;margin-right:9px}@media (max-width:860px){.hdr-donate{width:48px;height:48px;min-height:48px !important;padding:0 !important;justify-content:center;flex:0 0 48px}.hdr-donate .lbl-donate{display:none}.hdr-ico{width:22px;height:22px;flex-basis:22px;margin-right:0}}</style>
</head>
<body>
<div style="font-family: var(--body); color: var(--ink); background: var(--bg); width: 100%; box-sizing: border-box; line-height: 1.55;">

<?php if ($header_band): ?>
  <!-- Header on indigo -->
  <div style="background: var(--indigo); position: relative; overflow: hidden;">
    <div style="position: absolute; right: -160px; top: -200px; width: 560px; height: 560px; border-radius: 999px; border: 56px solid rgba(255,255,255,0.05);"></div>
<?php else: ?>
  <!-- Header -->
<?php endif; ?>
  <header style="position: relative; z-index: 2;">
    <div class="wrap hdr-row" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 112px; flex-wrap: wrap; padding-top: 16px; padding-bottom: 16px;">
      <a href="index.php" style="display: inline-flex; align-items: center; gap: 12px; color: #fff;">
        <img class="brand-logo" src="assets/uet-logo.png" alt="Umalakshmi Educational Trust logo" style="width: 80px; height: 82px; object-fit: contain; background: #fff; border-radius: 18px; padding: 6px; box-sizing: border-box; box-shadow: 0 8px 24px rgba(0,0,0,0.25);">
        <span style="display: flex; flex-direction: column; line-height: 1.02;">
          <span class="brand-name" style="font-family: var(--display); font-size: 40px; font-weight: 800; letter-spacing: -0.02em;<?= $brand_shadow ?>">Umalakshmi</span>
          <span class="brand-sub" style="font-size: 17px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; color: var(--marigold);<?= $sub_shadow ?>">Educational Trust</span>
        </span>
      </a>
      <nav class="nav" aria-label="Main" style="display: flex; align-items: center; gap: 2px; background: #fff; border: 1px solid var(--line); border-radius: 999px; padding: 6px;">
<?php foreach (nav_items() as $key => [$href, $label]): ?>
        <a href="<?= h(page_href($href)) ?>"<?= $key === $page ? ' class="active"' : '' ?>><?= h($label) ?></a>
<?php endforeach; ?>
      </nav>
      <div style="display: flex; align-items: center; gap: 10px;"><a href="<?= h(page_href('donate.php')) ?>" class="btn btn-marigold hdr-donate" aria-label="Donate Now" style="min-height: 50px; padding: 12px 20px; font-size: 15px;"><svg class="hdr-ico" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.3-9.1C1 8.3 3.2 4.5 7 4.5c2 0 3.4 1.1 4.2 2.3l.8 1.2.8-1.2C13.6 5.6 15 4.5 17 4.5c3.8 0 6 3.8 4.3 7.4C19 16.4 12 21 12 21z"/></svg><span class="lbl-donate">Donate</span><span class="lbl-full">&nbsp;Now</span></a><button type="button" class="burger" aria-label="Open menu"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button></div>
    </div>
  </header>
