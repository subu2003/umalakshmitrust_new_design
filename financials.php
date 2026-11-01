<?php
// Financials page. Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
// Year-wise documents: only the audited financial statement is listed. All years point at one placeholder PDF
// (pdf/financials/audited-financial-statements.pdf) until the per-year statements arrive from the auditor.
$page        = 'financials';
$page_title  = 'Financials';
$header_band = true;
$audited_pdf = 'pdf/financials/audited-financial-statements.pdf';
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
.btn-indigo{background:var(--indigo);color:#fff;border-color:var(--indigo)}
.btn-indigo:hover{background:var(--indigo-deep);border-color:var(--indigo-deep);color:#fff}
.card{background:#fff;border:1px solid var(--line);border-radius:24px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.g4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px}
.num{font-family:var(--display);font-weight:800;letter-spacing:-0.04em;line-height:1}
table.fin{width:100%;border-collapse:collapse;font-size:15px}
table.fin th{text-align:left;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:12px 16px;border-bottom:2px solid var(--line)}
table.fin td{padding:16px;border-bottom:1px solid var(--line)}
table.fin td.r,table.fin th.r{text-align:right}
.fy{background:#fff;border:1px solid var(--line);border-radius:28px;padding:36px 40px;display:flex;flex-direction:column;gap:18px}
.fy .hd{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.fy .ic{width:56px;height:56px;border-radius:16px;background:var(--lilac);display:inline-flex;align-items:center;justify-content:center;flex:0 0 56px}
.doc{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:16px 0;border-top:1px solid var(--line)}
.doc a.pill{display:inline-flex;align-items:center;min-height:42px;padding:10px 20px;border-radius:999px;background:var(--indigo);color:#fff;font-weight:700;font-size:14px;white-space:nowrap;flex:0 0 auto}
.doc a.pill:hover{background:var(--indigo-deep);color:#fff}
.doc a.pill.off{background:var(--lilac);color:var(--muted);pointer-events:none}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.g3,.g4{grid-template-columns:repeat(2,minmax(0,1fr))}.nav{display:none}}
@media (max-width:600px){.g2,.g3,.g4{grid-template-columns:repeat(1,minmax(0,1fr))}.wrap{padding:0 16px}h1{font-size:44px !important}}

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
.fy{padding:24px 20px !important}.fy .hd strong{font-size:20px !important}.doc{gap:12px !important}.doc a.pill{padding:8px 14px !important;font-size:13px !important}table.fin td,table.fin th{padding:10px 8px !important;font-size:14px}.mc1.summary{padding:24px !important;gap:28px !important}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<?php require __DIR__ . '/inc/header.php'; ?>
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 160px;">
      <div class="wrap" style="display: flex; flex-direction: column; gap: 18px; max-width: 980px;">
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Financials</span>
        <h1 style="font-size: 64px; color: #fff;">Every rupee, accounted for.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 820px;">We believe donors deserve clear, accurate and honest information about where their money goes. Every year the trust's accounts are audited and the statements are published here, alongside the number of students sponsored and the amount disbursed. Fees are paid directly to institutions — never in cash, never through intermediaries.</p>
      </div>
    </section>
  </div>

  <!-- Summary card overlapping -->
  <section style="position: relative; margin-top: -88px; padding-bottom: 96px;">
    <div class="wrap">
      <div class="mc1 summary" style="background: #fff; border-radius: 28px; box-shadow: 0 24px 60px rgba(20,22,51,0.12); padding: 40px; display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: 48px; align-items: center;">
        <div style="display: flex; flex-direction: column; gap: 20px;">
          <div style="display: flex; flex-direction: column; gap: 6px;"><span class="eyebrow">Five-year summary · 2022 to 2026</span><h2 style="font-size: 34px;">Sponsorship disbursed by year</h2></div>
          <table class="fin" aria-label="Students sponsored and amount disbursed by year">
            <thead><tr><th>Year</th><th class="r">Students</th><th class="r">Amount disbursed</th></tr></thead>
            <tbody>
              <tr><td><strong>2022</strong></td><td class="r">30</td><td class="r">₹ 2,21,000</td></tr>
              <tr><td><strong>2023</strong></td><td class="r">46</td><td class="r">₹ 3,41,000</td></tr>
              <tr><td><strong>2024</strong></td><td class="r">51</td><td class="r">₹ 3,71,000</td></tr>
              <tr><td><strong>2025</strong></td><td class="r">63</td><td class="r">₹ 4,17,000</td></tr>
              <tr><td><strong>2026</strong></td><td class="r">75</td><td class="r">₹ 7,52,000</td></tr>
              <tr style="background: var(--cream);"><td style="border-radius: 12px 0 0 12px;"><strong>Total</strong></td><td class="r"><strong>265</strong></td><td class="r" style="border-radius: 0 12px 12px 0;"><strong>₹ 21,02,000</strong></td></tr>
            </tbody>
          </table>
        </div>
        <figure style="margin: 0; display: flex; flex-direction: column; gap: 14px;">
          <figcaption style="font-size: 13px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);">Amount disbursed, ₹ lakh</figcaption>
          <div role="img" aria-label="Bar chart: 2.21 lakh in 2022, 3.41 in 2023, 3.71 in 2024, 4.17 in 2025, 7.52 in 2026" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 18px; align-items: end; height: 260px; padding: 0 8px; border-bottom: 2px solid var(--line);">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; height: 100%;"><span class="num" style="font-size: 16px; color: var(--indigo);">2.21</span><div style="width: 100%; height: 29%; background: var(--indigo); border-radius: 10px 10px 0 0;"></div></div>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; height: 100%;"><span class="num" style="font-size: 16px; color: var(--indigo);">3.41</span><div style="width: 100%; height: 45%; background: var(--indigo); border-radius: 10px 10px 0 0;"></div></div>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; height: 100%;"><span class="num" style="font-size: 16px; color: var(--indigo);">3.71</span><div style="width: 100%; height: 49%; background: var(--indigo); border-radius: 10px 10px 0 0;"></div></div>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; height: 100%;"><span class="num" style="font-size: 16px; color: var(--indigo);">4.17</span><div style="width: 100%; height: 55%; background: var(--indigo); border-radius: 10px 10px 0 0;"></div></div>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; height: 100%;"><span class="num" style="font-size: 16px; color: var(--marigold-ink);">7.52</span><div style="width: 100%; height: 100%; max-height: 84%; background: var(--marigold); border-radius: 10px 10px 0 0;"></div></div>
          </div>
          <div class="mc1" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 18px; padding: 0 8px; text-align: center; font-size: 13px; font-weight: 700; color: var(--muted);"><span>2022</span><span>2023</span><span>2024</span><span>2025</span><span>2026</span></div>
        </figure>
      </div>
    </div>
  </section>

  <!-- Year-wise statements -->
  <section style="padding: 0 0 104px;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 36px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap;">
        <div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px;"><span class="eyebrow">Audited accounts</span><h2 style="font-size: 44px;">Financial statements by year</h2></div>
        <span style="font-size: 14px; color: var(--muted);">Financial year April to March · PDF</span>
      </div>
      <div style="display: flex; gap: 18px; align-items: flex-start; background: var(--cream); border: 2px solid var(--marigold); border-radius: 20px; padding: 22px 26px;">
        <span style="flex: 0 0 44px; width: 44px; height: 44px; border-radius: 12px; background: var(--marigold); display: inline-flex; align-items: center; justify-content: center;"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--marigold-ink)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><path d="M12 2l7 4v6c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z"></path></svg></span>
        <p style="margin: 0; font-size: 16px; line-height: 1.65; color: var(--ink);"><strong style="font-family: var(--display); font-size: 18px; display: block; margin-bottom: 4px;">Audited every year</strong>The accounts of Uma Lakshmi Educational Trust are verified and certified every year by an independent certified auditor, and the trust's income-tax returns are filed for every financial year.</p>
      </div>
      <div class="g2" style="gap: 32px;">
        <div class="fy">
          <div class="hd"><span style="display: flex; align-items: center; gap: 14px;"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><strong style="font-family: var(--display); font-size: 22px;">2025 – 2026</strong></span><span class="tag" style="background: var(--cream); color: var(--marigold-ink);">Latest</span></div>
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);">Trust accounts</span>
          <div class="doc"><span style="font-size: 15px;">Audited financial statement</span><a href="<?= h($audited_pdf) ?>" class="pill" target="_blank" rel="noopener">Open PDF</a></div>
        </div>
        <div class="fy">
          <div class="hd"><span style="display: flex; align-items: center; gap: 14px;"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><strong style="font-family: var(--display); font-size: 22px;">2024 – 2025</strong></span></div>
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);">Trust accounts</span>
          <div class="doc"><span style="font-size: 15px;">Audited financial statement</span><a href="<?= h($audited_pdf) ?>" class="pill" target="_blank" rel="noopener">Open PDF</a></div>
        </div>
        <div class="fy">
          <div class="hd"><span style="display: flex; align-items: center; gap: 14px;"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><strong style="font-family: var(--display); font-size: 22px;">2023 – 2024</strong></span></div>
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);">Trust accounts</span>
          <div class="doc"><span style="font-size: 15px;">Audited financial statement</span><a href="<?= h($audited_pdf) ?>" class="pill" target="_blank" rel="noopener">Open PDF</a></div>
        </div>
        <div class="fy">
          <div class="hd"><span style="display: flex; align-items: center; gap: 14px;"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><strong style="font-family: var(--display); font-size: 22px;">2022 – 2023</strong></span><span class="tag" style="background: var(--lilac); color: var(--indigo);">First year</span></div>
          <span style="font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);">Trust accounts</span>
          <div class="doc"><span style="font-size: 15px;">Audited financial statement</span><a href="<?= h($audited_pdf) ?>" class="pill" target="_blank" rel="noopener">Open PDF</a></div>
        </div>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
