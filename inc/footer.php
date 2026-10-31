<?php
/**
 * Site footer, full-screen mobile menu, menu script, and the closing tags.
 * Include at the very end of every page.
 *
 * Uses $page (set before inc/head.php) to mark the current item in the mobile menu.
 * Optional:
 *   $page_scripts  string  extra <script> markup printed after the menu script (e.g. the donate QR code).
 */
require_once __DIR__ . '/functions.php';

$page         = isset($page) ? (string) $page : '';
$page_scripts = isset($page_scripts) ? (string) $page_scripts : '';
$mm_links = '';
foreach (nav_items() as $key => [$href, $label]) {
    $mm_links .= '<a href="' . h(page_href($href)) . '" data-page="' . h($key) . '"' . ($key === $page ? ' class="on"' : '') . '>' . h($label) . '<i></i></a>';
}
?>

  <!-- Footer (preserved from the current site) -->
  <footer id="contact" class="ftr" style="background: var(--indigo-deep); color: var(--on-dark-muted); padding: 72px 0 28px;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 44px;">
      <div class="g3" style="gap: 40px;">
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <span style="display: inline-flex; align-items: center; gap: 12px;"><img src="assets/uet-logo.png" alt="" style="width: 40px; height: 42px; object-fit: contain; background: #fff; border-radius: 10px; padding: 3px; box-sizing: border-box;"><span style="display: flex; flex-direction: column; line-height: 1.05;"><span style="font-family: var(--display); font-size: 20px; font-weight: 800; color: #fff;">Umalakshmi</span><span style="font-size: 11px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--marigold);">Educational Trust</span></span></span>
          <p style="margin: 0; font-size: 15px; line-height: 1.65;">Uma Lakshmi Educational Trust, founded on 11 June 2022. In the memory of my beloved wife Uma Lakshmi, to spread her compassion towards educating poor children and share her love with future generations.</p>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 15px;">
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #fff;">Contact</span>
          <span style="line-height: 1.65;">3A, Palazzo Apartment, Ram Kamal Building, Shasthiri Nagar First Street, Adyar, Chennai 600020</span>
          <a href="tel:+919176189469">+91 9176189469</a>
          <a href="mailto:umalakshmieducationaltrust@gmail.com">umalakshmieducationaltrust@gmail.com</a>
          <a href="https://www.umalakshmitrust.com">www.umalakshmitrust.com</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 15px;">
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #fff;">Bank account details</span>
          <span>Name : Umalakshmi Educational Trust</span>
          <span>AC : 50200070075707</span>
          <span>IFSC Code : HDFC0001866</span>
          <span>HDFC Bank, Kasturibhai Nagar branch, Adayar, Chennai</span>
          <span style="margin-top: 6px; font-size: 13px;">UPI ID : umalakshmitrust@hdfcbank</span>
        </div>
      </div>
      <div style="border-top: 1px solid rgba(255,255,255,0.14); padding-top: 22px; display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; font-size: 13px;">
        <span style="display: inline-flex; gap: 20px;"><a href="<?= h(page_href('privacy.php')) ?>">Privacy Policy</a><a href="<?= h(page_href('contact.php')) ?>">Contact Us</a><a href="<?= h(page_href('terms.php')) ?>">Terms of Use</a></span>
        <span>Copyright © Umalakshmi Trust. All Rights Reserved. · Developed by Subramanian</span>
      </div>
    </div>
  </footer>

</div>

<div id="mmenu" class="mmenu" aria-hidden="true">
  <div class="mm-top"><a href="index.php" class="mm-brand"><img src="assets/uet-logo.png" alt=""><span><b>Umalakshmi</b><small>Educational Trust</small></span></a><button type="button" class="burger mm-close" aria-label="Close menu"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button></div>
  <nav class="mm-nav" aria-label="Main">
    <?= $mm_links ?>

  </nav>
  <div class="mm-bottom"><a href="<?= h(page_href('donate.php')) ?>" class="mm-donate">Donate Now</a><div class="mm-ci"><a href="tel:+919176189469">+91 9176189469</a><a href="mailto:umalakshmieducationaltrust@gmail.com">umalakshmieducationaltrust@gmail.com</a></div></div>
</div>
<script>
(function(){var m=document.getElementById('mmenu');var here=location.pathname.split('/').pop().replace(/\.(html|php)$/,'')||'index';var cur=m.querySelector('[data-page="'+here+'"]');if(cur)cur.classList.add('on');
function open(){m.classList.add('open');m.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';}
function close(){m.classList.remove('open');m.setAttribute('aria-hidden','true');document.body.style.overflow='';}
document.querySelectorAll('.burger:not(.mm-close)').forEach(function(b){b.addEventListener('click',open);});
m.querySelector('.mm-close').addEventListener('click',close);document.addEventListener('keydown',function(e){if(e.key==='Escape')close();});})();
</script>
<?= $page_scripts ?>

</body>
</html>
