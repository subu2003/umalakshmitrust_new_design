<?php
// Terms of Use page. Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
$page        = 'terms';
$page_title  = 'Terms of Use';
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
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Terms of Use</span>
        <h1 style="font-size: 64px; color: #fff;">Terms and conditions for using this site.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 800px;">Please read these terms before using www.umalakshmitrust.com. By using the Site you accept them.</p>
        <span style="font-size: 13px; color: var(--on-dark-muted);">Last updated: 1 November 2026</span>
      </div>
    </section>
  </div>

  <section style="padding: 72px 0 104px; background: var(--lilac);">
    <div class="wrap legal" style="max-width: 960px;">
      <div class="card">
        <h2>Use of this website</h2>
        <ul>
          <li>Downloading or printing information (“Information”) from this website is for your personal, non-commercial use only.</li>
          <li>You must retain and reproduce each and every copyright notice or other proprietary rights notice contained in any Information you download. You may not, however, distribute, modify, transmit, reuse, repost or use the content of the Site for public or commercial purposes, including the text, images, graphics, audio and video, without the written permission of Umalakshmi Educational Trust.</li>
          <li>You should assume that everything you see or read on this Site is proprietary unless otherwise noted, and may not be used except as provided in these Terms and Conditions or in the text on the Site, without the written permission of Umalakshmi Educational Trust.</li>
          <li>Framing of this Site by sites or site elements of parties not part of Umalakshmi Educational Trust is not permitted. In-line linking or any other manner of incorporating parts of this Site in sites of parties not part of the Trust is equally prohibited.</li>
        </ul>
      </div>
      <div class="card">
        <h2>Accuracy and liability</h2>
        <ul>
          <li>While Umalakshmi Educational Trust uses reasonable efforts to include accurate and up-to-date information in the Site about the resources involved with us, the Trust makes no warranties or representations with respect to the content of the Site, which is provided “as is”. The Trust accepts no responsibility or liability whatsoever arising from or in any way connected with the use of this Site or its content.</li>
          <li>The Trust also assumes no responsibility, and shall not be liable, for any damages to, or viruses that may infect, your computer equipment or other property on account of your access to, use of, or browsing in the Site, or your downloading of any materials, data, text, images, video or audio from the Site. The Trust reserves the right to interrupt or discontinue any or all of the functionality of its Site.</li>
          <li>The Information may contain technical inaccuracies or typographical errors. The Trust reserves the right to make changes, corrections and/or improvements to the Information, and to the products and programmes described in such Information, at any time without notice.</li>
          <li>The Trust has no control over and does not endorse any of the sites to which the Site is linked. Because the Trust has not reviewed any or all of the sites to which the Site is linked, you acknowledge that the Trust is not responsible for the content of any off-Site pages or any other sites linked to the Site. Your linking to the Site, off-Site pages or other sites is at your own risk and without the permission of the Trust.</li>
        </ul>
      </div>
      <div class="card">
        <h2>Communications and personal information</h2>
        <ul>
          <li>Any communication or material you transmit to the Site by electronic mail or otherwise, including any data, questions, feedback, ideas, queries, comments, suggestions or the like, is, and will be treated as, non-confidential and non-proprietary.</li>
          <li>Umalakshmi Educational Trust is committed to the ethical collection, retention and use of information that you provide to us about yourself (“Personal Information”) on this Site.</li>
          <li>Your Personal Information may comprise your name, e-mail ID, mobile number and any other data the Trust may require. See our <a href="privacy.php">Privacy Policy</a> for how it is handled.</li>
        </ul>
      </div>
      <div class="card">
        <h2>Changes to these terms</h2>
        <ul>
          <li>Umalakshmi Educational Trust may at any time revise these Terms and Conditions by updating this posting. You are bound by any such revisions and should therefore periodically visit this page to review the then-current Terms and Conditions to which you are bound.</li>
        </ul>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
