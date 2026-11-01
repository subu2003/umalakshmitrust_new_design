<?php
// Donate page. Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
// The UPI QR code is generated in the browser (script at the bottom, passed to the footer as $page_scripts).
$page        = 'donate';
$page_title  = 'Donate';
$header_band = true;
require_once __DIR__ . '/inc/csrf.php';
uet_session_start();
$flash = $_SESSION['donate_flash'] ?? null;
unset($_SESSION['donate_flash']);
$old = $_SESSION['donate_old'] ?? null;
unset($_SESSION['donate_old']);
$upi_vpa   = (string) env('UPI_VPA', '');
$upi_payee = (string) env('UPI_PAYEE', 'Umalakshmi Educational Trust');
$don_min   = (int) env('DONATION_MIN', '10');
$don_max   = (int) env('DONATION_MAX', '100000');
$qr_valid  = (int) env('QR_VALID_SECONDS', '180');
$maxDocMb  = (float) env('MAX_DOC_MB', '5');
require __DIR__ . '/inc/head.php';
?>
<style>
:root{
  --bg:#FFFFFF; --ink:#141633; --muted:#5B5E7A; --line:#E6E7F2;
  --indigo:#1E2A78; --indigo-deep:#121A52; --lilac:#EEF0FF; --lilac2:#DCDFFF;
  --marigold:#FFB300; --marigold-ink:#3D2B00; --cream:#FFF6DC;
  --coral:#FF5A5F; --coral-tint:#FFE9EA; --green:#1B8A5A; --green-tint:#E3F5EC;
  --on-dark:#FFFFFF; --on-dark-muted:#C9CDF0;
  --display:'Bricolage Grotesque', system-ui, sans-serif; --body:'DM Sans', system-ui, sans-serif;
}
body{margin:0;background:var(--bg)}
a{color:var(--indigo);text-decoration:none}a:hover{color:var(--indigo-deep)}
h1,h2,h3{font-family:var(--display);font-weight:800;letter-spacing:-0.03em;margin:0;color:var(--ink);line-height:1.05}
.nav a{color:var(--ink);font-weight:600;font-size:15px;padding:10px 14px;border-radius:999px}
.nav a:hover{background:var(--lilac)}
.nav a.active{background:var(--ink);color:#fff}
.tag{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;font-size:13px;font-weight:700;letter-spacing:.02em}
.eyebrow{font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--indigo)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:54px;padding:15px 28px;border-radius:999px;font-family:var(--body);font-weight:700;font-size:16px;cursor:pointer;border:2px solid transparent}
.btn-marigold{background:var(--marigold);color:var(--marigold-ink);border-color:var(--marigold)}
.btn-marigold:hover{background:#F0A500;border-color:#F0A500;color:var(--marigold-ink)}
.btn-indigo{background:var(--indigo);color:#fff;border-color:var(--indigo)}
.btn-indigo:hover{background:var(--indigo-deep);border-color:var(--indigo-deep);color:#fff}
.btn-ghost{background:#fff;color:var(--ink);border-color:var(--line)}
.btn-ghost:hover{border-color:var(--ink)}
.card{background:#fff;border:1px solid var(--line);border-radius:28px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--line)}
.row .k{font-size:13px;color:var(--muted)}
.row .v{font-weight:700;font-size:16px;color:var(--ink)}
.copy{display:inline-flex;align-items:center;gap:6px;min-height:36px;padding:6px 12px;border-radius:999px;border:1.5px solid var(--line);background:#fff;font-family:var(--body);font-size:12px;font-weight:700;color:var(--indigo);cursor:pointer;white-space:nowrap}
.copy:hover{border-color:var(--indigo)}
.in{width:100%;box-sizing:border-box;min-height:64px;padding:12px 18px 12px 48px;border:2px solid var(--line);border-radius:18px;font-family:var(--display);font-weight:800;font-size:30px;letter-spacing:-0.02em;color:var(--ink);background:#fff}
.in:focus{outline:3px solid var(--lilac2);border-color:var(--indigo)}
.preset{min-height:44px;padding:8px 16px;border-radius:999px;border:1.5px solid var(--line);background:#fff;font-family:var(--body);font-weight:700;font-size:14px;color:var(--ink);cursor:pointer}
.preset:hover{border-color:var(--indigo);color:var(--indigo)}
.preset.on{background:var(--ink);border-color:var(--ink);color:#fff}
.app{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 8px;border-radius:16px;border:1.5px solid var(--line);background:#fff;font-size:12px;font-weight:700;color:var(--ink);min-height:44px}
.app:hover{border-color:var(--indigo);color:var(--indigo)}
.app .dot{width:36px;height:36px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-family:var(--display);font-weight:800;font-size:15px}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.g3{grid-template-columns:repeat(1,minmax(0,1fr))}.nav{display:none}}
@media (max-width:600px){.wrap{padding:0 16px}h1{font-size:44px !important}}

.burger{display:none;align-items:center;justify-content:center;width:48px;height:48px;border-radius:14px;background:#fff;color:var(--ink);border:0;cursor:pointer;flex:0 0 48px}
.mmenu{position:fixed;inset:0;z-index:100;background:var(--indigo);color:#fff;font-family:var(--body);display:flex;flex-direction:column;transform:translateX(100%);transition:transform .28s ease;overflow:auto}
.mmenu.open{transform:none}
.mm-top{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;min-height:84px;box-sizing:border-box}
.mm-brand{display:inline-flex;align-items:center;gap:10px;color:#fff}.mm-brand img{width:50px;height:52px;object-fit:contain;background:#fff;border-radius:12px;padding:4px;box-sizing:border-box}
.mm-brand span{display:flex;flex-direction:column;line-height:1.02}.mm-brand b{font-family:var(--display);font-size:24px;font-weight:800;letter-spacing:-0.02em}.mm-brand small{font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--marigold)}
.mm-nav{display:flex;flex-direction:column;gap:4px;padding:12px 16px 0;flex-grow:1}
.mm-nav a{display:flex;align-items:center;justify-content:space-between;min-height:60px;padding:0 18px;border-radius:18px;font-family:var(--display);font-weight:800;font-size:22px;letter-spacing:-0.02em;color:#fff}
.mm-nav a.on{background:#fff;color:var(--indigo)}
.mm-nav a i{width:20px;height:20px;opacity:.55;background:currentColor;-webkit-mask:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'><path d='M5 12h14M13 6l6 6-6 6'/></svg>") center/contain no-repeat;mask:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'><path d='M5 12h14M13 6l6 6-6 6'/></svg>") center/contain no-repeat}
.mm-bottom{padding:16px 20px 28px;display:flex;flex-direction:column;gap:18px}
.mm-donate{display:flex;align-items:center;justify-content:center;min-height:56px;border-radius:999px;background:var(--marigold);color:var(--marigold-ink);font-weight:800;font-size:17px}
.mm-ci{display:flex;flex-direction:column;gap:6px;border-top:1px solid rgba(255,255,255,.14);padding-top:14px}.mm-ci a{color:#C9CDF0;font-size:14px;font-weight:600;word-break:break-all}
@media (max-width:860px){ .burger{display:inline-flex} .hdr-row{min-height:84px !important;flex-wrap:nowrap !important;padding-top:12px !important;padding-bottom:12px !important} .hero-over{margin-top:-84px !important;padding-top:84px !important} .hdr-donate{min-height:44px !important;padding:10px 16px !important;font-size:14px !important;white-space:nowrap} .hdr-row>a:first-child{flex:1 1 auto;min-width:0} .hdr-row>a:first-child>span{min-width:0} .brand-name,.brand-sub{white-space:nowrap} .hdr-donate .lbl-full{display:none} }
@media (max-width:860px){
/* ---- Mobile overrides (390px artboard) ---- */
.wrap{padding:0 20px}
h1{font-size:40px !important;line-height:1.05 !important}
h2{font-size:32px !important}
h3{font-size:22px !important}
.nav{display:none !important}
.g2,.g3,.g4,.g5{grid-template-columns:repeat(1,minmax(0,1fr)) !important}
.brand-name{font-size:24px !important}
.brand-sub{font-size:11px !important;letter-spacing:.16em !important}
.brand-logo{width:50px !important;height:52px !important;border-radius:12px !important;padding:4px !important}

section{padding-top:64px !important;padding-bottom:64px !important}
*{min-width:0}
a,span,strong,p{overflow-wrap:anywhere}
.ftr a,.ci a{word-break:break-all}
.f{flex-direction:row !important;flex-wrap:wrap !important;align-items:center !important;gap:4px 6px !important}
.f .in{flex:1 1 100% !important}
.f .ta{flex-basis:100%}
.mc1{grid-template-columns:1fr !important}
.mc2{grid-template-columns:repeat(2,minmax(0,1fr)) !important}
.mspan{grid-column:1 / -1 !important}
form.mc1{padding:24px !important}.card.pad32{padding:24px !important}.in{font-size:26px}.row .v{font-size:15px}.copy{padding:6px 10px}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<style>/* Donate: QR / app-button behaviour */#apps-note-desktop{display:block}#apps-note-mobile{display:none}@media (max-width:860px){#apps{order:-1}#apps-note-desktop{display:none}#apps-note-mobile{display:block}}@media (min-width:861px){#apps{display:none !important}}.upi-check{font-size:12px;line-height:1.6;color:var(--muted);text-align:center;background:var(--lilac);border-radius:12px;padding:8px 12px}.upi-check strong{color:var(--ink)}.ref-line{font-size:12px;color:var(--muted);letter-spacing:.04em}.qr-err{font-size:13px;color:var(--coral);font-weight:600;text-align:center}.app .dot{position:relative;overflow:hidden;width:64px;height:38px;border-radius:12px}.app .dot img{width:56px;height:28px;object-fit:contain;display:block}.app .dot img:not([hidden])+i{display:none}.app .dot i{font-style:normal}.payform .uplist{list-style:none;margin:6px 0 0;padding:0;font-size:13px}.payform .uplist li{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);border-radius:10px;padding:6px 10px}.payform .uplist li span{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.payform .uplist button{border:0;background:var(--coral-tint);color:var(--coral);border-radius:999px;width:22px;height:22px;cursor:pointer;font-weight:700;line-height:1}.payform .uperr{font-size:13px;color:var(--coral);font-weight:600}</style>
<script src="assets/forms.js"></script>
<?php require __DIR__ . '/inc/header.php'; ?>
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 160px;">
      <div class="wrap" style="display: flex; flex-direction: column; gap: 18px; max-width: 980px;">
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Donate Now</span>
        <h1 style="font-size: 64px; color: #fff;">Sponsor a child's year in school.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 820px;">Give by UPI in seconds, or transfer to the trust's bank account. Every donation is eligible for deduction under Section 80G, and every rupee goes directly to a student's institution.</p>
      </div>
    </section>
  </div>

  <!-- Three ways to give -->
  <section style="position: relative; margin-top: -96px; padding-bottom: 96px;">
    <div class="wrap">
      <div class="mc1" style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.25fr); gap: 24px; align-items: start;">

        <!-- Bank details -->
        <div class="card pad32" style="padding: 32px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 24px 60px rgba(20,22,51,0.10);">
          <span class="tag" style="background: var(--lilac); color: var(--indigo); align-self: flex-start;">Bank transfer · NEFT / IMPS</span>
          <h2 style="font-size: 26px; margin-top: 6px;">Trust account details</h2>
          <div class="row"><span><span class="k">Account name</span><br><span class="v">Umalakshmi Educational Trust</span></span><button type="button" class="copy" data-copy="Umalakshmi Educational Trust">Copy</button></div>
          <div class="row"><span><span class="k">Account number</span><br><span class="v">50200070075707</span></span><button type="button" class="copy" data-copy="50200070075707">Copy</button></div>
          <div class="row"><span><span class="k">IFSC code</span><br><span class="v">HDFC0001866</span></span><button type="button" class="copy" data-copy="HDFC0001866">Copy</button></div>
          <div class="row" style="border-bottom: 0;"><span><span class="k">Bank &amp; branch</span><br><span class="v" style="font-size: 15px;">HDFC Bank, Kasturibai Nagar, Adyar, Chennai</span></span></div>
        </div>

        <!-- UPI with dynamic QR -->
        <div class="card pad32" style="padding: 32px; display: flex; flex-direction: column; gap: 18px; border: 2px solid var(--marigold); box-shadow: 0 24px 60px rgba(61,43,0,0.12);">
          <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;"><span class="tag" style="background: var(--marigold); color: var(--marigold-ink);">UPI · Instant</span><span style="font-size: 13px; color: var(--muted);">UPI ID: <strong style="color: var(--ink);"><?= h($upi_vpa) ?></strong></span></div>
          <h2 style="font-size: 26px;">Enter an amount to generate your QR</h2>
          <div style="position: relative;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-family: var(--display); font-weight: 800; font-size: 28px; color: var(--muted);">₹</span>
            <input class="in" type="number" min="<?= $don_min ?>" max="<?= $don_max ?>" step="1" inputmode="numeric" aria-label="Donation amount in rupees" placeholder="0" id="amt">
          <div id="qrerr" class="qr-err" hidden></div>
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" class="preset" data-amt="500">₹ 500</button><button type="button" class="preset" data-amt="1000">₹ 1,000</button><button type="button" class="preset" data-amt="5000">₹ 5,000</button><button type="button" class="preset" data-amt="10000">₹ 10,000</button>
          </div>

          <div id="idle">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 8px 0;">
              <div style="width: 232px; height: 232px; border-radius: 20px; border: 2px dashed var(--lilac2); background: var(--lilac); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: var(--muted); font-size: 13px; text-align: center; padding: 16px; box-sizing: border-box;"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect><path d="M14 14h3v3M21 14v3M14 21h3M21 21h-1"></path></svg>Your QR code will appear here</div>
              <button type="button" class="btn btn-indigo" style="width: 100%;" data-act="generate">Generate QR code</button>
            </div>
          </div>

          <div id="qrbox" hidden>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
              <div style="padding: 12px; background: #fff; border: 2px solid var(--line); border-radius: 20px;">
                <svg role="img" id="qrsvg" aria-label="UPI QR code" width="208" height="208" viewBox="0 0 21 21" shape-rendering="crispEdges"><rect x="0" y="0" id="qrbg" width="21" height="21" fill="#ffffff"></rect><path id="qrpath" d="" fill="#141633"></path></svg>
              </div>
              <span style="font-family: var(--display); font-weight: 800; font-size: 24px; color: var(--ink);">Pay ₹<span id="amtPretty"></span></span>
              <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--marigold-ink); background: var(--cream); padding: 6px 14px; border-radius: 999px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>QR valid for <span id="remain">03:00</span></span>
              <span class="ref-line">Reference <strong id="refShow"></strong></span>
              <span id="apps-note-desktop" style="font-size: 13px; color: var(--muted); text-align: center;">Scan with any UPI app on your phone</span>
              <span id="apps-note-mobile" style="font-size: 13px; color: var(--muted); text-align: center;">Tap your app to pay, or scan the QR from another phone</span>
              <span class="upi-check">Before you confirm the payment, check that your app shows the UPI ID <strong><?= h($upi_vpa) ?></strong> and the name registered to it.</span>
              <div id="apps" class="mc2" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; width: 100%;">
                <a href="#" id="lnk-gpay" class="app"><span class="dot" style="background: #fff; color: var(--indigo); border: 1px solid var(--line);"><img src="assets/upi/gpay.png" alt="" width="56" height="28" loading="lazy" onerror="this.hidden=true"><i>G</i></span>Google Pay</a>
                <a href="#" id="lnk-phonepe" class="app"><span class="dot" style="background: #fff; color: var(--indigo); border: 1px solid var(--line);"><img src="assets/upi/phonepe.png" alt="" width="56" height="28" loading="lazy" onerror="this.hidden=true"><i>Pe</i></span>PhonePe</a>
                <a href="#" id="lnk-paytm" class="app"><span class="dot" style="background: #fff; color: var(--indigo); border: 1px solid var(--line);"><img src="assets/upi/paytm.svg" alt="" width="56" height="28" loading="lazy" onerror="this.hidden=true"><i>Pt</i></span>Paytm</a>
                <a href="#" id="lnk-upi" class="app"><span class="dot" style="background: #fff; color: var(--indigo); border: 1px solid var(--line);"><img src="assets/upi/upi.svg" alt="" width="56" height="28" loading="lazy" onerror="this.hidden=true"><i>B</i></span>BHIM / other</a>
              </div>
              <button type="button" class="copy" data-act="reset" style="margin-top: 4px;">Change amount</button>
            </div>
          </div>

          <div id="expired" hidden>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 8px 0;">
              <div style="width: 232px; height: 232px; border-radius: 20px; background: var(--coral-tint); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: var(--coral); font-size: 14px; font-weight: 700; text-align: center; padding: 16px; box-sizing: border-box;"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>This QR code has expired</div>
              <button type="button" class="btn btn-indigo" style="width: 100%;" data-act="generate">Generate a new QR code</button>
            </div>
          </div>
          <p style="margin: 0; font-size: 12px; line-height: 1.6; color: var(--muted); text-align: center;">Each QR code is valid for <?= (int) round($qr_valid / 60) ?> minutes. Payments go straight to the trust's account.</p>
          <div style="border-top: 1px solid var(--line); padding-top: 14px; display: flex; gap: 12px; align-items: flex-start;"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex: 0 0 24px;"><path d="M9 12l2 2 4-4"></path><path d="M12 2l7 4v6c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z"></path></svg><span style="font-size: 13px; line-height: 1.6; color: var(--muted);"><strong style="color: var(--ink);">80G tax benefit.</strong> Permanently approved under Section 80G (AAATU1234KF20251).</span></div>
        </div>

      </div>
    </div>
  </section>

  <!-- After you pay -->
  <section style="background: var(--lilac); padding: 96px 0;">
    <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 56px; align-items: flex-start;">
      <div style="flex: 1 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 14px;">
        <span class="eyebrow">After you pay</span>
        <h2 style="font-size: 44px;">Tell us who to thank.</h2>
        <p style="margin: 0; font-size: 16px; line-height: 1.7; color: var(--muted);">Share your details so we can send your 80G receipt and, if you wish, list you among our donors. We never publish amounts.</p>
        <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 14px; color: var(--muted);"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>Receipts are issued within 7 working days.</div>
      </div>
      <form id="payform" class="mc1 payform" style="flex: 1 1 560px; min-width: 0; background: #fff; border-radius: 28px; padding: 36px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px;" action="donation_receipt.php" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="intent_ref" id="intent_ref" value=""><input type="hidden" name="intent_amount" id="intent_amount" value=""><input type="hidden" name="intent_sig" id="intent_sig" value="">
        <div style="position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true"><label>Leave this field empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
<?php if ($flash && ($flash['type'] ?? '') === 'error'): ?>
        <div id="form-errors" role="alert" style="grid-column: span 2; background: var(--coral-tint); border: 1.5px solid var(--coral); border-radius: 16px; padding: 14px 18px; font-size: 14px; line-height: 1.6; color: var(--ink);">
          <strong>Please correct the following:</strong>
          <ul style="margin: 4px 0 0; padding-left: 20px;"><?php foreach ((array) ($flash['errors'] ?? []) as $msg): ?><li><?= h($msg) ?></li><?php endforeach; ?></ul>
        </div>
<?php endif; ?>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">Full name <span style="color: var(--coral);">*</span><input type="text" name="name" required style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">Mobile / WhatsApp <span style="color: var(--coral);">*</span><input type="tel" name="phone" required style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">Email <span style="color: var(--coral);">*</span><input type="email" name="email" required style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">PAN (for 80G receipt)<input type="text" name="pan" placeholder="ABCDE1234F" pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]" title="PAN looks like ABCDE1234F" style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px; text-transform: uppercase;"></label>
        <label class="mspan mspan" style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700; grid-column: span 2;">Address<input type="text" name="address" style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">Amount donated (₹) <span style="color: var(--coral);">*</span><input type="number" name="amount" id="payamount" min="1" step="1" required style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 700;">UPI / transaction reference (UTR)<input type="text" name="ref" id="payref" style="min-height: 50px; padding: 12px 16px; border: 1.5px solid var(--line); border-radius: 14px; font-family: var(--body); font-size: 15px;"></label>
        <label for="shot" style="grid-column: span 2; display: flex; align-items: center; gap: 14px; padding: 16px; border: 2px dashed var(--lilac2); border-radius: 16px; background: var(--lilac); font-size: 14px; color: var(--muted); cursor: pointer;"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"></rect><path d="M8 13l3 3 5-6"></path></svg><span><strong style="color: var(--ink); display: block;">Upload payment screenshot (optional)</strong>Helps us match your payment faster</span><input id="shot" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" name="screenshot" type="file" style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
        <label class="mspan mspan" style="grid-column: span 2; display: flex; align-items: center; gap: 10px; font-size: 14px; cursor: pointer;"><input type="checkbox" name="list" style="width: 18px; height: 18px; accent-color: var(--indigo);">List my name and city on the Donors page</label>
        <p style="grid-column: span 2; margin: 0; font-size: 13px; color: var(--muted);">Please give the UPI / UTR reference or attach the payment screenshot, so that we can match your payment with our bank statement.</p>
        <button type="submit" class="btn btn-indigo" id="paybtn" style="grid-column: span 2; justify-self: start;">Send details</button>
      </form>
    </div>
  </section>

<?php if ($flash && ($flash['type'] ?? '') === 'success'): ?>
<div id="okmodal" role="dialog" aria-modal="true" aria-labelledby="okttl" style="position: fixed; inset: 0; z-index: 200; background: rgba(18,26,82,0.72); display: flex; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; border-radius: 28px; padding: 40px 36px; max-width: 500px; width: 100%; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 14px; box-shadow: 0 30px 80px rgba(20,22,51,0.35);">
    <span style="width: 72px; height: 72px; border-radius: 999px; background: var(--marigold); display: inline-flex; align-items: center; justify-content: center;"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="currentColor" style="color: var(--marigold-ink);"><path d="M12 21s-7-4.6-9.3-9.1C1 8.3 3.2 4.5 7 4.5c2 0 3.4 1.1 4.2 2.3l.8 1.2.8-1.2C13.6 5.6 15 4.5 17 4.5c3.8 0 6 3.8 4.3 7.4C19 16.4 12 21 12 21z"/></svg></span>
    <h2 id="okttl" style="font-size: 28px;">Thank you for your donation</h2>
    <p style="margin: 0; font-size: 16px; line-height: 1.65; color: var(--ink);">Thank you, <strong><?= h($flash['name'] ?? '') ?></strong>, for your donation of <strong>&#8377;<?= h($flash['amount'] ?? '') ?></strong>. We have received your details.</p>
    <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">We will verify the payment with the trust's bank statement and send your payment receipt shortly.</p>
    <button type="button" class="btn btn-indigo" style="margin-top: 8px;" onclick="document.getElementById('okmodal').remove()">Close</button>
  </div>
</div>
<?php endif; ?>
<?php ob_start(); ?>
<script>
// ---- Minimal QR encoder (byte mode, error-correction level L, versions 1-9, mask 0) ----
var GF_EXP = [], GF_LOG = [];
(function () {
  for (var i = 0; i < 8; i++) GF_EXP[i] = 1 << i;
  for (var j = 8; j < 256; j++) GF_EXP[j] = GF_EXP[j - 4] ^ GF_EXP[j - 5] ^ GF_EXP[j - 6] ^ GF_EXP[j - 8];
  for (var k = 0; k < 255; k++) GF_LOG[GF_EXP[k]] = k;
})();
function gexp(n) { while (n < 0) n += 255; while (n >= 256) n -= 255; return GF_EXP[n]; }
function glog(n) { return GF_LOG[n]; }
function Poly(num, shift) {
  var off = 0; while (off < num.length && num[off] === 0) off++;
  this.num = new Array(num.length - off + shift);
  for (var i = 0; i < num.length - off; i++) this.num[i] = num[i + off];
  for (var j = num.length - off; j < this.num.length; j++) this.num[j] = 0;
}
Poly.prototype.get = function (i) { return this.num[i]; };
Poly.prototype.len = function () { return this.num.length; };
Poly.prototype.multiply = function (e) {
  var num = new Array(this.len() + e.len() - 1); for (var z = 0; z < num.length; z++) num[z] = 0;
  for (var i = 0; i < this.len(); i++) for (var j = 0; j < e.len(); j++) {
    if (this.get(i) === 0 || e.get(j) === 0) continue;
    num[i + j] ^= gexp(glog(this.get(i)) + glog(e.get(j)));
  }
  return new Poly(num, 0);
};
Poly.prototype.mod = function (e) {
  if (this.len() - e.len() < 0) return this;
  var ratio = glog(this.get(0)) - glog(e.get(0));
  var num = this.num.slice();
  for (var i = 0; i < e.len(); i++) num[i] ^= gexp(glog(e.get(i)) + ratio);
  return new Poly(num, 0).mod(e);
};
// [blocks, totalCodewords, dataCodewords] for level L, versions 1..9
var RS_L = [[1,26,19],[1,44,34],[1,70,55],[1,100,80],[1,134,108],[2,86,68],[2,98,78],[2,121,97],[2,146,116]];
var ALIGN = [[],[6,18],[6,22],[6,26],[6,30],[6,34],[6,22,38],[6,24,42],[6,26,46]];
function bch(data, g, shift) {
  function digit(x) { var d = 0; while (x !== 0) { d++; x >>>= 1; } return d; }
  var d = data << shift;
  while (digit(d) - digit(g) >= 0) d ^= (g << (digit(d) - digit(g)));
  return (data << shift) | d;
}
function utf8(str) { var out = [], s = unescape(encodeURIComponent(str)); for (var i = 0; i < s.length; i++) out.push(s.charCodeAt(i)); return out; }
function makeQR(text) {
  var bytes = utf8(text), version = -1;
  for (var v = 0; v < RS_L.length; v++) { if (RS_L[v][0] * RS_L[v][2] >= bytes.length + 2) { version = v + 1; break; } }
  if (version < 0) return null;
  var rs = RS_L[version - 1], blocks = rs[0], dcLen = rs[2], ecLen = rs[1] - rs[2], totalData = blocks * dcLen;
  // bit buffer
  var bits = [];
  function put(num, len) { for (var i = len - 1; i >= 0; i--) bits.push((num >>> i) & 1); }
  put(4, 4); put(bytes.length, 8); for (var b = 0; b < bytes.length; b++) put(bytes[b], 8);
  var cap = totalData * 8; if (bits.length + 4 <= cap) put(0, 4); while (bits.length % 8 !== 0) bits.push(0);
  var padToggle = true; while (bits.length < cap) { put(padToggle ? 0xEC : 0x11, 8); padToggle = !padToggle; }
  var data = []; for (var i = 0; i < bits.length; i += 8) { var val = 0; for (var j = 0; j < 8; j++) val = (val << 1) | bits[i + j]; data.push(val); }
  // RS per block
  var gen = new Poly([1], 0); for (var g = 0; g < ecLen; g++) gen = gen.multiply(new Poly([1, gexp(g)], 0));
  var dcs = [], ecs = [], off = 0;
  for (var bl = 0; bl < blocks; bl++) {
    var dc = data.slice(off, off + dcLen); off += dcLen;
    var mod = new Poly(dc, ecLen).mod(gen), ec = new Array(ecLen);
    for (var e = 0; e < ecLen; e++) { var idx = e + mod.len() - ecLen; ec[e] = idx >= 0 ? mod.get(idx) : 0; }
    dcs.push(dc); ecs.push(ec);
  }
  var all = [];
  for (var di = 0; di < dcLen; di++) for (var bb = 0; bb < blocks; bb++) all.push(dcs[bb][di]);
  for (var ei = 0; ei < ecLen; ei++) for (var bc = 0; bc < blocks; bc++) all.push(ecs[bc][ei]);
  // matrix
  var n = 17 + 4 * version, m = [];
  for (var r = 0; r < n; r++) { m[r] = []; for (var c = 0; c < n; c++) m[r][c] = null; }
  function probe(row, col) {
    for (var r2 = -1; r2 <= 7; r2++) { if (row + r2 < 0 || row + r2 >= n) continue;
      for (var c2 = -1; c2 <= 7; c2++) { if (col + c2 < 0 || col + c2 >= n) continue;
        m[row + r2][col + c2] = (r2 >= 0 && r2 <= 6 && (c2 === 0 || c2 === 6)) || (c2 >= 0 && c2 <= 6 && (r2 === 0 || r2 === 6)) || (r2 >= 2 && r2 <= 4 && c2 >= 2 && c2 <= 4);
      } }
  }
  probe(0, 0); probe(n - 7, 0); probe(0, n - 7);
  var pos = ALIGN[version - 1];
  for (var pi = 0; pi < pos.length; pi++) for (var pj = 0; pj < pos.length; pj++) {
    var pr = pos[pi], pc = pos[pj]; if (m[pr][pc] !== null) continue;
    for (var ar = -2; ar <= 2; ar++) for (var ac = -2; ac <= 2; ac++) m[pr + ar][pc + ac] = (ar === -2 || ar === 2 || ac === -2 || ac === 2 || (ar === 0 && ac === 0));
  }
  for (var t = 8; t < n - 8; t++) { if (m[t][6] === null) m[t][6] = (t % 2 === 0); if (m[6][t] === null) m[6][t] = (t % 2 === 0); }
  // format info (level L = 1, mask 0)
  var fmt = bch((1 << 3) | 0, 0x537, 10) ^ 0x5412;
  for (var fi = 0; fi < 15; fi++) {
    var fb = ((fmt >> fi) & 1) === 1;
    if (fi < 6) m[fi][8] = fb; else if (fi < 8) m[fi + 1][8] = fb; else m[n - 15 + fi][8] = fb;
    if (fi < 8) m[8][n - fi - 1] = fb; else if (fi < 9) m[8][15 - fi] = fb; else m[8][15 - fi - 1] = fb;
  }
  m[n - 8][8] = true;
  if (version >= 7) {
    var vb = bch(version, 0x1F25, 12);
    for (var vi = 0; vi < 18; vi++) { var vm = ((vb >> vi) & 1) === 1; m[Math.floor(vi / 3)][vi % 3 + n - 11] = vm; m[vi % 3 + n - 11][Math.floor(vi / 3)] = vm; }
  }
  // data placement, mask 0
  var inc = -1, row = n - 1, bitIndex = 7, byteIndex = 0;
  for (var col = n - 1; col > 0; col -= 2) {
    if (col === 6) col--;
    while (true) {
      for (var cc = 0; cc < 2; cc++) {
        if (m[row][col - cc] === null) {
          var dark = false;
          if (byteIndex < all.length) dark = (((all[byteIndex] >>> bitIndex) & 1) === 1);
          if ((row + (col - cc)) % 2 === 0) dark = !dark;
          m[row][col - cc] = dark;
          bitIndex--; if (bitIndex === -1) { byteIndex++; bitIndex = 7; }
        }
      }
      row += inc;
      if (row < 0 || row >= n) { row -= inc; inc = -inc; break; }
    }
  }
  var path = '';
  for (var y = 0; y < n; y++) for (var x = 0; x < n; x++) if (m[y][x]) path += 'M' + x + ' ' + y + 'h1v1h-1z';
  return { size: n, path: path };
}


(function(){
  var amt=document.getElementById('amt'), idle=document.getElementById('idle'), box=document.getElementById('qrbox'), exp=document.getElementById('expired'), err=document.getElementById('qrerr');
  var VALID=180, startedAt=0, timer=null, cur=null;
  var MIN=parseFloat(amt.min)||10, MAX=parseFloat(amt.max)||100000;
  function amount(){ return parseFloat(String(amt.value).replace(/[^0-9.]/g,''))||0; }
  function csrf(){ var i=document.querySelector('input[name=csrf_token]'); return i?i.value:''; }
  function fmt(n){ return Number(n).toLocaleString('en-IN'); }
  function params(d){ return 'pa='+encodeURIComponent(d.pa)+'&pn='+encodeURIComponent(d.pn)+'&am='+encodeURIComponent(d.amount)+'&cu=INR&tn='+encodeURIComponent(d.tn); }
  function link(scheme,d){ var path = scheme==='tez' ? 'upi/pay' : 'pay'; return scheme+'://'+path+'?'+params(d); }
  function show(which){ idle.hidden = which!=='idle'; box.hidden = which!=='qr'; exp.hidden = which!=='expired'; }
  function fail(msg){ err.textContent=msg; err.hidden=false; }
  function tick(){ var left=Math.max(0, VALID-Math.floor((Date.now()-startedAt)/1000)); var m=Math.floor(left/60), s=left%60; document.getElementById('remain').textContent=(m<10?'0'+m:m)+':'+(s<10?'0'+s:s); if(left<=0){ clearInterval(timer); show('expired'); } }
  function setIntent(d){ document.getElementById('intent_ref').value=d?d.ref:''; document.getElementById('intent_amount').value=d?d.amount:''; document.getElementById('intent_sig').value=d?d.sig:''; var pa=document.getElementById('payamount'); if(d&&pa&&!pa.value){ pa.value=Math.round(parseFloat(d.amount)); } }
  function render(d){
    var q=makeQR(link('upi',d)); if(!q){ fail('Amount too large for a QR code.'); return; }
    document.getElementById('qrpath').setAttribute('d',q.path); document.getElementById('qrsvg').setAttribute('viewBox','0 0 '+q.size+' '+q.size); var bg=document.getElementById('qrbg'); bg.setAttribute('width',q.size); bg.setAttribute('height',q.size);
    document.getElementById('qrsvg').setAttribute('aria-label','UPI QR code to pay '+fmt(d.amount)+' rupees to '+d.pn);
    document.getElementById('amtPretty').textContent=fmt(parseFloat(d.amount));
    document.getElementById('refShow').textContent=d.ref;
    document.getElementById('lnk-gpay').href=link('tez',d); document.getElementById('lnk-phonepe').href=link('phonepe',d); document.getElementById('lnk-paytm').href=link('paytmmp',d); document.getElementById('lnk-upi').href=link('upi',d);
    VALID=parseInt(d.valid,10)||180; startedAt=Date.now(); clearInterval(timer); timer=setInterval(tick,1000); tick(); show('qr'); cur=d; setIntent(d);
  }
  function generate(){
    err.hidden=true; var a=amount();
    if(a<MIN||a>MAX){ fail('Please enter an amount between ₹'+fmt(MIN)+' and ₹'+fmt(MAX)+'.'); amt.focus(); return; }
    var btns=document.querySelectorAll('[data-act="generate"]'); btns.forEach(function(b){ b.disabled=true; });
    var fd=new FormData(); fd.append('amount', String(a)); fd.append('csrf_token', csrf());
    fetch('donation_qr.php',{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){ return r.json().catch(function(){ return {ok:false,error:'Unexpected reply from the server.'}; }); })
      .then(function(d){ if(!d.ok){ fail(d.error||'The QR code could not be prepared. Please try again.'); return; } render(d); })
      .catch(function(){ fail('Could not reach the server. Please check your connection and try again.'); })
      .then(function(){ btns.forEach(function(b){ b.disabled=false; }); });
  }
  function presets(){ var a=amount(); document.querySelectorAll('.preset').forEach(function(b){ b.classList.toggle('on', parseFloat(b.dataset.amt)===a); }); }
  function reset(){ clearInterval(timer); show('idle'); cur=null; setIntent(null); }
  document.querySelectorAll('.preset').forEach(function(b){ b.addEventListener('click',function(){ amt.value=b.dataset.amt; presets(); err.hidden=true; reset(); }); });
  amt.addEventListener('input',function(){ presets(); err.hidden=true; reset(); });
  document.querySelectorAll('[data-act="generate"]').forEach(function(b){ b.addEventListener('click',generate); });
  document.querySelectorAll('[data-act="reset"]').forEach(function(b){ b.addEventListener('click',reset); });
  document.querySelectorAll('[data-copy]').forEach(function(b){ b.addEventListener('click',function(){ try{ navigator.clipboard.writeText(b.dataset.copy); }catch(e){} var t=b.textContent; b.textContent='Copied'; setTimeout(function(){ b.textContent=t; },1600); }); });
  // "After you pay" form: file preview + size check, reference-or-screenshot rule, double-submit guard
  var f=document.getElementById('payform'), pb=document.getElementById('paybtn'), shot=document.getElementById('shot');
  if(shot){ var tile=document.querySelector('label[for="shot"]'); var wrap=document.createElement('div'); wrap.className='upwrap'; wrap.style.gridColumn='span 2'; tile.parentNode.insertBefore(wrap,tile); wrap.appendChild(tile); var list=document.createElement('ul'); list.className='uplist'; wrap.appendChild(list); var ue=document.createElement('div'); ue.className='uperr'; wrap.appendChild(ue);
    var maxMb=parseFloat(shot.getAttribute('data-max-mb'))||5;
    function renderShot(){ list.innerHTML=''; var fl=shot.files&&shot.files[0]; if(fl){ var li=document.createElement('li'); var sp=document.createElement('span'); sp.textContent=fl.name; var sm=document.createElement('small'); sm.textContent=(fl.size>=1048576?(fl.size/1048576).toFixed(1)+' MB':Math.max(1,Math.round(fl.size/1024))+' KB'); var b=document.createElement('button'); b.type='button'; b.textContent='×'; b.setAttribute('aria-label','Remove'); b.addEventListener('click',function(){ shot.value=''; renderShot(); }); li.appendChild(sp); li.appendChild(sm); li.appendChild(b); list.appendChild(li); } }
    shot.addEventListener('change',function(){ ue.textContent=''; var fl=shot.files&&shot.files[0]; if(!fl){ renderShot(); return; }
      ue.textContent='Preparing file\u2026';
      UET.compressImage(fl, 1600, 0.8).then(function(out){ ue.textContent='';
        if(out.size>maxMb*1048576){ ue.textContent='"'+out.name+'" is too large. The screenshot must be under '+maxMb+' MB.'; shot.value=''; }
        else if(out!==fl){ try{ var dt=new DataTransfer(); dt.items.add(out); shot.files=dt.files; }catch(e){} }
        renderShot(); }); }); }
  if(f){ f.addEventListener('submit',function(ev){ var hasRef=document.getElementById('payref').value.trim()!==''; var hasShot=shot&&shot.files&&shot.files.length>0; if(!hasRef&&!hasShot){ ev.preventDefault(); alert('Please give the UPI / UTR reference or attach the payment screenshot.'); document.getElementById('payref').focus(); return; } if(pb){ pb.disabled=true; pb.textContent='Sending…'; } }); }
  var fe=document.getElementById('form-errors'); if(fe){ fe.scrollIntoView({block:'center'}); }
<?php if ($old): ?>  UET.refill(document.getElementById('payform'), <?= json_encode($old, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>);
<?php endif; ?>
  var mo=document.getElementById('okmodal'); if(mo){ document.addEventListener('keydown',function(ev){ if(ev.key==='Escape') mo.remove(); }); }
})();
</script>
<?php $page_scripts = ob_get_clean(); require __DIR__ . '/inc/footer.php'; ?>
