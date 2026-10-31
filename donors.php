<?php
// List of Donors page. Shared layout lives in inc/ (head → page CSS → header → content → footer).
// The header sits in an indigo band together with the page intro; inc/header.php opens the band,
// this page closes it with </div> after the intro section.
$page        = 'donors';
$page_title  = 'List of Donors';
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
<?php require __DIR__ . '/inc/header.php'; ?>
    <!-- Page intro -->
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 88px;">
      <div class="wrap" style="display: flex; flex-direction: column; gap: 18px; max-width: 980px;">
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">List of Donors</span>
        <h1 style="font-size: 64px; color: #fff;">The people who make it possible.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 800px;">Every student we sponsor is sponsored by someone. From Chennai to Jubail, Dubai, Michigan and Vancouver, these donors and well-wishers have stood with the trust year after year. We are grateful to each of them.</p>
      </div>
    </section>
  </div>

  <!-- Donor grid -->
  <section style="padding: 80px 0 104px; background: var(--lilac);">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 40px;">
      <div class="grid">
        <div class="donor"><img src="assets/donors/nachiappan.jpg" alt="SP. Nachiappan"><strong>SP. Nachiappan</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/subramanian.jpg" alt="N. Subramanian"><strong>N. Subramanian</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/yuvaraj.jpg" alt="P. Yuvaraj Perisi Ramalingam"><strong>P. Yuvaraj Perisi Ramalingam</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/sumathi.jpg" alt="T. Sumathi"><strong>T. Sumathi</strong><span>Karur</span></div>
        <div class="donor"><img src="assets/donors/senthil.jpg" alt="R. Senthil Kumar"><strong>R. Senthil Kumar</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/murugappan.jpg" alt="K. Murugappan"><strong>K. Murugappan</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/alamelu.jpg" alt="Alamelu Murugappan"><strong>Alamelu Murugappan</strong><span>Vancouver, Canada</span></div>
        <div class="donor"><img src="assets/donors/soundarrajan.jpg" alt="G. Soundarrajan"><strong>G. Soundarrajan</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/eshwar.jpg" alt="S. Eshwar"><strong>S. Eshwar</strong><span>Michigan, USA</span></div>
        <div class="donor"><img src="assets/donors/kavya.jpg" alt="P. Kavya"><strong>P. Kavya</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/visalakshi.jpg" alt="MR. Visalakshi"><strong>MR. Visalakshi</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/srimathi.jpg" alt="R. Srimathi"><strong>R. Srimathi</strong><span>Srirangam</span></div>
        <div class="donor"><img src="assets/donors/kowsika.jpg" alt="P. Kowsika"><strong>P. Kowsika</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/meyyappan.jpg" alt="S. Meyyappan"><strong>S. Meyyappan</strong><span>Dubai</span></div>
        <div class="donor"><img src="assets/donors/sivaraman.jpg" alt="S. Sivaraman"><strong>S. Sivaraman</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/bhaskar.jpg" alt="C. Bhaskar"><strong>C. Bhaskar</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/johnson.jpg" alt="S. Johnson David"><strong>S. Johnson David</strong><span>Lalgudi</span></div>
        <div class="donor"><img src="assets/donors/ponnuswamy.jpg" alt="D. Ponnuswamy"><strong>D. Ponnuswamy</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/thilak.jpg" alt="D. Thilak"><strong>D. Thilak</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/palaniappan.jpg" alt="SP. Palaniappan"><strong>SP. Palaniappan</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/paul.jpg" alt="Paul Palocaren"><strong>Paul Palocaren</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/rengarajan.jpg" alt="S. Rengarajan Sampath"><strong>S. Rengarajan Sampath</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/chockalingam.jpg" alt="SP. Chockalingam"><strong>SP. Chockalingam</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/hariprasad.jpg" alt="CV. Hariprasad"><strong>CV. Hariprasad</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/rekha.jpg" alt="Rekha Rajagopal"><strong>Rekha Rajagopal</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/venkat.jpg" alt="P. Venkata Sairam"><strong>P. Venkata Sairam</strong><span>Jubail, KSA</span></div>
        <div class="donor"><img src="assets/donors/parvathi.jpg" alt="Parvathi"><strong>Parvathi</strong><span>Chennai</span></div>
        <div class="donor"><img src="assets/donors/sudarsan.jpg" alt="Sudarsan"><strong>Sudarsan</strong><span>Chennai</span></div>
      </div>
      <p style="margin: 0; text-align: center; font-size: 14px; color: var(--muted);">Donors are listed with their consent. Many more well-wishers have chosen to give anonymously — we thank them equally.</p>
    </div>
  </section>

  <!-- Join band -->
  <section style="padding: 0 0 104px; background: var(--lilac);">
    <div class="wrap">
      <div class="join" style="background: var(--marigold); color: var(--marigold-ink); border-radius: 32px; padding: 48px 56px; display: flex; flex-wrap: wrap; gap: 32px; align-items: center; justify-content: space-between;">
        <div style="display: flex; flex-direction: column; gap: 8px; max-width: 620px;">
          <span class="eyebrow" style="color: var(--marigold-ink);">Join them</span>
          <h2 style="font-size: 40px; color: var(--marigold-ink);">Your name could be here next year.</h2>
          <p style="margin: 0; font-size: 16px; line-height: 1.6;">Every donation is eligible for deduction under Section 80G.</p>
        </div>
        <a href="<?= h(page_href('donate.php')) ?>" class="btn" style="background: var(--ink); color: #fff; border-color: var(--ink);"><svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px; flex: 0 0 18px;"><path d="M12 21s-7-4.6-9.3-9.1C1 8.3 3.2 4.5 7 4.5c2 0 3.4 1.1 4.2 2.3l.8 1.2.8-1.2C13.6 5.6 15 4.5 17 4.5c3.8 0 6 3.8 4.3 7.4C19 16.4 12 21 12 21z"/></svg>Donate Now</a>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
