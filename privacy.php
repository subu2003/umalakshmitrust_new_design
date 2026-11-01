<?php
// Privacy Policy page. Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
$page        = 'privacy';
$page_title  = 'Privacy Policy';
$header_band = true;
require __DIR__ . '/inc/head.php';
?>
<style>
:root{
  --bg:#FFFFFF; --ink:#141633; --muted:#5B5E7A; --line:#E6E7F2;
  --indigo:#1E2A78; --indigo-deep:#121A52; --lilac:#EEF0FF; --lilac2:#DCDFFF;
  --marigold:#FFB300; --marigold-ink:#3D2B00; --cream:#FFF6DC;
  --coral:#FF5A5F; --coral-tint:#FFE9EA;
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
.btn{display:inline-flex;align-items:center;gap:10px;min-height:54px;padding:15px 28px;border-radius:999px;font-family:var(--body);font-weight:700;font-size:16px;cursor:pointer;border:2px solid transparent}
.btn-marigold{background:var(--marigold);color:var(--marigold-ink);border-color:var(--marigold)}
.btn-marigold:hover{background:#F0A500;border-color:#F0A500;color:var(--marigold-ink)}
.btn-ghost-light{background:transparent;color:#fff;border-color:rgba(255,255,255,.6)}
.btn-ghost-light:hover{background:#fff;color:var(--ink)}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:24px 20px}
.donor{display:flex;flex-direction:column;align-items:flex-start;text-align:left;gap:0;background:#fff;border-radius:22px;padding:12px 12px 16px;box-shadow:0 10px 30px rgba(20,22,51,0.08);position:relative}
.donor:after{content:"";position:absolute;left:12px;right:12px;top:12px;height:6px;border-radius:999px 999px 0 0;background:var(--marigold)}
.donor img{display:block;width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:16px 16px 4px 4px;border:4px solid var(--lilac);border-top-width:10px;border-top-color:var(--marigold);box-sizing:border-box;margin-bottom:12px}
.donor strong{font-family:var(--display);font-size:16px;font-weight:800;letter-spacing:-0.01em;line-height:1.2;color:var(--ink);padding:0 4px}
.donor span{font-size:13px;color:var(--muted);padding:0 4px;margin-top:2px}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.grid{grid-template-columns:repeat(3,minmax(0,1fr))}.g3{grid-template-columns:repeat(2,minmax(0,1fr))}.nav{display:none}}
@media (max-width:600px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.g3{grid-template-columns:repeat(1,minmax(0,1fr))}.wrap{padding:0 16px}h1{font-size:44px !important}}

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
.grid{grid-template-columns:repeat(2,minmax(0,1fr)) !important;gap:16px 12px !important}.donor strong{font-size:14px}.join{padding:28px 24px !important}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<style>/* Legal pages */.legal{display:flex;flex-direction:column;gap:28px}.legal .card{padding:36px;display:flex;flex-direction:column;gap:14px}.legal h2{font-size:26px}.legal ul,.legal ol{margin:0;padding-left:22px;display:flex;flex-direction:column;gap:10px;font-size:16px;line-height:1.7;color:var(--ink)}.legal li::marker{color:var(--indigo);font-weight:700}.legal p{margin:0;font-size:16px;line-height:1.7;color:var(--ink)}.legal .note{font-size:14px;color:var(--muted)}@media (max-width:860px){.legal .card{padding:24px 20px !important}.legal h2{font-size:22px !important}}</style>
<?php require __DIR__ . '/inc/header.php'; ?>
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 88px;">
      <div class="wrap" style="display: flex; flex-direction: column; gap: 18px; max-width: 980px;">
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Privacy Policy</span>
        <h1 style="font-size: 64px; color: #fff;">Your information, handled with care.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 800px;">How Umalakshmi Educational Trust collects, uses and protects the personal and financial information you share with us, and your rights as a donor.</p>
        <span style="font-size: 13px; color: var(--on-dark-muted);">Last updated: 1 November 2026</span>
      </div>
    </section>
  </div>

  <section style="padding: 72px 0 104px; background: var(--lilac);">
    <div class="wrap legal" style="max-width: 960px;">
      <div class="card">
        <h2>How we handle your information</h2>
        <ul>
          <li>Umalakshmi Educational Trust takes your privacy seriously and treats all financial information about any transaction you have with the Trust as highly confidential.</li>
          <li>The Trust does not share the e-mail addresses or phone numbers of any of our donors or constituents.</li>
          <li>The Trust deeply values all contributions to sustain our mission.</li>
          <li>Personal information stored in the Trust’s database is protected behind a secured login with authentication, and each person is identified uniquely by a transaction ID.</li>
          <li>We may request personal information online, such as name, address, phone number and e-mail address, for the purpose of accepting donations to the Trust.</li>
          <li>We will not release or use this information for any other purpose unless we have your consent.</li>
          <li>We do not trade or sell your personal information to other organisations.</li>
          <li>Donors may ask not to receive certain mailings, such as our newsletter.</li>
        </ul>
      </div>
      <div class="card">
        <h2>Your rights as a donor</h2>
        <ul>
          <li>To be informed of the Trust’s mission, of the way the Trust intends to use donated resources, and of its capacity to use donations effectively for their intended purposes.</li>
          <li>To have access to the Trust’s most recent financial statements.</li>
          <li>To receive appropriate acknowledgment.</li>
          <li>To be assured that information about your donations is handled with respect and with confidentiality to the extent provided by law.</li>
        </ul>
      </div>
      <div class="card">
        <h2>Trust refund policy</h2>
        <ol>
          <li>Umalakshmi Educational Trust does not encourage refund or cancellation of funds.</li>
          <li>For any queries, please feel free to contact the Trust at the address given in the footer, or send us a mail at <a href="mailto:umalakshmieducationaltrust@gmail.com">umalakshmieducationaltrust@gmail.com</a>.</li>
        </ol>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
