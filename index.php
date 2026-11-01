<?php
// Home page. Shared layout lives in inc/ (head → page CSS → header → content → footer).
$page       = 'index';
$page_title = 'Home';
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
.btn-ghost{background:transparent;color:var(--ink);border-color:var(--ink)}
.btn-ghost:hover{background:var(--ink);color:#fff}
.btn-ghost-light{background:transparent;color:#fff;border-color:rgba(255,255,255,.6)}
.btn-ghost-light:hover{background:#fff;color:var(--ink)}
.card{background:#fff;border:1px solid var(--line);border-radius:24px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.g5{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:0}
.g4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0}
.img{display:block;width:100%;object-fit:cover}
.num{font-family:var(--display);font-weight:800;letter-spacing:-0.04em;line-height:1}
.dist{font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--indigo);margin:18px 0 8px}
.school{font-size:15px;color:var(--ink);padding:9px 0;border-bottom:1px solid var(--line);display:flex;gap:10px;align-items:flex-start}
.school:before{content:"";width:6px;height:6px;border-radius:999px;background:var(--marigold);margin-top:8px;flex:0 0 6px}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.g3,.g4,.g5{grid-template-columns:repeat(2,minmax(0,1fr))}.nav{display:none}}
@media (max-width:600px){.g2,.g3,.g4,.g5{grid-template-columns:repeat(1,minmax(0,1fr))}.wrap{padding:0 16px}h1{font-size:44px !important}}

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
.stats{grid-template-columns:repeat(2,minmax(0,1fr)) !important;padding:20px 8px !important;gap:8px 0 !important}.stats>div{padding:12px 16px !important;border-right:0 !important}.stats>div:nth-child(odd){border-right:1px solid var(--line) !important}.stats>div:nth-child(-n+2){border-bottom:1px solid var(--line)}.stats .num{font-size:40px !important}.hero-btns{flex-direction:row;gap:10px !important}.hero-btns .btn{flex:1 1 0;min-height:48px;padding:10px 12px;font-size:14px;white-space:nowrap;justify-content:center}.hero-eyebrow{font-size:16px !important;letter-spacing:.18em !important}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<?php require __DIR__ . '/inc/header.php'; ?>

  <!-- Hero: seated students photo -->
  <section id="top" class="hero-over" style="position: relative; overflow: hidden; min-height: 720px; display: flex; align-items: center; margin-top: -112px; padding-top: 112px;">
    <img src="assets/hero.jpg" alt="Students seated at the annual sponsorship function" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 50% 40%;">
    <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(18,26,82,0.88) 0%, rgba(18,26,82,0.72) 45%, rgba(18,26,82,0.35) 100%);"></div>
    <div class="wrap" style="position: relative; width: 100%; padding-top: 72px; padding-bottom: 96px; display: flex; flex-direction: column; gap: 26px; color: #fff;">
      <span class="hero-eyebrow" style="font-family: var(--display); font-size: 24px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Educate, Empower, Elevate</span>
      <h1 style="font-size: 80px; color: #fff; max-width: 900px;">Lighting Young Minds, Transforming Generations.</h1>
      <p style="margin: 0; font-size: 20px; line-height: 1.55; color: var(--on-dark-muted); max-width: 600px;">Uma Lakshmi Educational Trust sponsors the education of deserving students from low-income families across Tamil Nadu — so that no child's future is decided by poverty.</p>
      <div class="hero-btns" style="display: flex; gap: 14px; flex-wrap: wrap;">
        <a href="apply.php" class="btn btn-ghost-light">Apply for Support</a>
      </div>
    </div>
  </section>

  <!-- Numbers -->
  <section style="position: relative; margin-top: -64px; padding-bottom: 104px;">
    <div class="wrap">
      <div class="g4 stats" style="background: #fff; border-radius: 28px; padding: 36px 16px; box-shadow: 0 24px 60px rgba(20,22,51,0.12);">
        <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px 28px; border-right: 1px solid var(--line);"><span class="num" style="font-size: 56px; color: var(--indigo);">265<span style="color: var(--marigold);">+</span></span><span style="font-size: 15px; color: var(--muted); font-weight: 500;">Students sponsored</span></div>
        <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px 28px; border-right: 1px solid var(--line);"><span class="num" style="font-size: 56px; color: var(--indigo);">21<span style="color: var(--marigold);">+</span></span><span style="font-size: 15px; color: var(--muted); font-weight: 500;">Schools</span></div>
        <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px 28px; border-right: 1px solid var(--line);"><span class="num" style="font-size: 56px; color: var(--indigo);">9<span style="color: var(--marigold);">+</span></span><span style="font-size: 15px; color: var(--muted); font-weight: 500;">Colleges</span></div>
        <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px 28px;"><span class="num" style="font-size: 56px; color: var(--indigo);">5<span style="color: var(--marigold);">+</span></span><span style="font-size: 15px; color: var(--muted); font-weight: 500;">Years · 2022 to 2026</span></div>
      </div>
    </div>
  </section>

  <!-- In memory of Uma Lakshmi -->
  <section id="memory" style="padding: 0 0 104px;">
    <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 64px; align-items: center;">
      <div style="flex: 0 1 400px; min-width: 0; position: relative; padding: 24px 0 0 24px;">
        <div style="position: absolute; left: 0; top: 0; width: 60%; height: 60%; background: var(--marigold); border-radius: 160px 0 0 0;"></div>
        <img src="assets/portrait.jpg" alt="Portrait of Uma Lakshmi" class="img" style="position: relative; aspect-ratio: 4 / 5; object-position: 50% 20%; border-radius: 0 0 160px 0;">
      </div>
      <div style="flex: 1 1 480px; min-width: 0; display: flex; flex-direction: column; gap: 22px;">
        <span class="eyebrow">In loving memory</span>
        <h2 style="font-size: 52px;">Uma Lakshmi</h2>
        <p style="margin: 0; font-family: var(--display); font-weight: 600; font-size: 24px; line-height: 1.35; color: var(--indigo); letter-spacing: -0.01em;">“To spread her compassion towards educating poor children, and share her love with future generations.”</p>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--muted);">Uma Lakshmi Educational Trust was founded in Chennai on 11 June 2022 by SP. Nachiappan in memory of his beloved wife. What began as a tribute has grown into a five-year commitment: every year the trust selects deserving students from government and government-aided schools and colleges, and pays their fees directly to the institution.</p>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--muted);">A kindness done in one name now lives on in the lives of 265+ students.</p>
      </div>
    </div>
  </section>

  <!-- Founder's message -->
  <section id="founder" style="background: var(--lilac); padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 64px; align-items: center; flex-direction: row-reverse;">
      <div style="flex: 1 1 440px; min-width: 0; display: flex; flex-direction: column; gap: 18px;">
        <img src="assets/founder2.jpg" alt="SP. Nachiappan, Founder and Managing Trustee" class="img" style="width: 100%; aspect-ratio: 4 / 5; object-position: 50% 15%; border-radius: 32px; box-shadow: 0 24px 60px rgba(20,22,51,0.16);">
        <span style="display: flex; flex-direction: column; gap: 2px;"><strong style="font-family: var(--display); font-size: 26px;">SP. Nachiappan</strong><span style="font-size: 15px; color: var(--muted);">Founder &amp; Managing Trustee</span></span>
      </div>
      <div style="flex: 1 1 520px; min-width: 0; display: flex; flex-direction: column; gap: 20px;">
        <span class="eyebrow">Founder's message</span>
        <h2 style="font-size: 44px;">A commitment beyond generations.</h2>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--ink);">Education is not merely the acquisition of knowledge; it is the foundation upon which individuals, families and society build a better future. Uma Lakshmi Educational Trust was established to ensure that no deserving child is deprived of education because of financial difficulties.</p>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--ink);">We firmly believe that every child deserves an opportunity, every talent deserves encouragement, and every dream deserves a chance to become reality. This trust is not built around an individual; it is built around a purpose.</p>
        <div style="display: flex; flex-direction: column; gap: 10px; padding: 20px 24px; background: #fff; border-radius: 20px;">
          <span style="font-size: 13px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--indigo);">Our solemn commitment</span>
          <span style="font-size: 15px; line-height: 1.6; color: var(--muted);">To serve with sincerity, transparency and accountability · to support deserving students without discrimination · to recognise genuine achievement · to stand beside students as they progress towards their ambitions · to reach more children every year.</span>
        </div>
        <p style="margin: 0; font-family: var(--display); font-weight: 700; font-size: 20px; line-height: 1.4; color: var(--indigo);">Our commitment is to education. Our purpose is to empower. Our legacy is to transform lives.</p>
      </div>
    </div>
  </section>

  <!-- 12A & 80G -->
  <section id="certified" style="background: var(--cream); padding: 104px 0; position: relative; overflow: hidden;">
    <div style="position: absolute; left: 50%; top: -420px; width: 1200px; height: 1200px; margin-left: -600px; border-radius: 999px; border: 1px solid rgba(255,179,0,0.45);"></div>
    <div style="position: absolute; left: 50%; top: -300px; width: 920px; height: 920px; margin-left: -460px; border-radius: 999px; border: 1px solid rgba(255,179,0,0.3);"></div>
    <div class="wrap" style="position: relative; display: flex; flex-direction: column; align-items: center; gap: 24px; text-align: center;">
      <svg role="img" aria-label="Seal of certification" width="132" height="132" viewBox="0 0 140 140" fill="none">
        <circle cx="70" cy="70" r="66" stroke="#FFB300" stroke-width="2"></circle>
        <circle cx="70" cy="70" r="57" stroke="#1E2A78" stroke-width="1" stroke-dasharray="2 5"></circle>
        <circle cx="70" cy="70" r="46" fill="#FFB300"></circle>
        <path d="M48 71l14 14 30-32" stroke="#1E2A78" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M30 104c-9-11-9-33 4-48M110 104c9-11 9-33-4-48" stroke="#1E2A78" stroke-width="2" stroke-linecap="round"></path>
      </svg>
      <span class="eyebrow">Recognised by the Income Tax Department</span>
      <h2 style="font-size: 52px; max-width: 900px;">Permanently registered under <span style="color: var(--indigo);">Section 12A</span> and approved under <span style="color: var(--indigo);">Section 80G</span>.</h2>
      <p style="margin: 0; font-size: 17px; line-height: 1.65; color: var(--muted); max-width: 660px;">Donations to Uma Lakshmi Educational Trust are eligible for income-tax deduction under Section 80G of the Income Tax Act, 1961. Every donor receives a receipt bearing the trust's registration details.</p>
      <div class="g2" style="width: 100%; max-width: 860px; margin-top: 16px; text-align: left;">
        <div class="card pad32" style="padding: 32px; display: flex; flex-direction: column; gap: 10px; border: 2px solid var(--marigold); box-shadow: 0 20px 50px rgba(61,43,0,0.08);">
          <span class="tag" style="background: var(--marigold); color: var(--marigold-ink); align-self: flex-start;">Section 12A</span>
          <h3 style="font-size: 24px;">Registration of the trust</h3>
          <span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">Unique registration number</span>
          <span class="num" style="font-size: 24px; color: var(--indigo); letter-spacing: 0.04em;">AAATU1234KE20251</span>
        </div>
        <div class="card pad32" style="padding: 32px; display: flex; flex-direction: column; gap: 10px; border: 2px solid var(--marigold); box-shadow: 0 20px 50px rgba(61,43,0,0.08);">
          <span class="tag" style="background: var(--marigold); color: var(--marigold-ink); align-self: flex-start;">Section 80G</span>
          <h3 style="font-size: 24px;">Approval for donor tax benefit</h3>
          <span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">Unique registration number</span>
          <span class="num" style="font-size: 24px; color: var(--indigo); letter-spacing: 0.04em;">AAATU1234KF20251</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Schools & colleges -->
  <section id="institutions" style="padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 40px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap;">
        <div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px;">
          <span class="eyebrow">Where our students study</span>
          <h2 style="font-size: 52px;">21 schools and 9 colleges across 10 districts of Tamil Nadu.</h2>
        </div>
        <div style="display: flex; gap: 8px;"><span class="tag" style="background: var(--ink); color: #fff;">Schools · 21</span><span class="tag" style="background: var(--lilac); color: var(--indigo);">Colleges · 9</span></div>
      </div>
      <div class="g3" style="gap: 40px; align-items: start;">
        <div>
          <div class="dist">Chennai</div>
          <div class="school">Rani Meyyammai Girls Hr. Sec. School, RA Puram</div>
          <div class="school">Rani Lady Meyyammai Hr. Sec. School, Adyar</div>
          <div class="school">Kumararaja Muthiah Hr. Sec. School, Adyar</div>
          <div class="school">Venkateswara Hr. Sec. School, Madipakkam</div>
          <div class="school">Thanthai Periyar Govt Hr. Sec. School, Madipakkam</div>
          <div class="school">Oxward Matriculation School, Kodungaiyur</div>
          <div class="school">Velammal Vidyalaya, Avadi</div>
          <div class="dist">Chengalpattu</div>
          <div class="school">St Mary's Girls Hr. Sec. School, Chengalpattu</div>
        </div>
        <div>
          <div class="dist">Sivaganga</div>
          <div class="school">Govt Hr. Sec. School, Ariyakudi</div>
          <div class="school">Govt Hr. Sec. School, Karaikudi</div>
          <div class="school">Pethal Achi Girls Hr. Sec. School, Devakottai</div>
          <div class="dist">Tiruchirappalli</div>
          <div class="school">St. Joseph Girls Hr. Sec. School, Trichy</div>
          <div class="school">Nest Matriculation School, Lalgudi</div>
          <div class="school">St Antony Hr. Sec. School, Lalgudi</div>
          <div class="school">The Kalaivani Matriculation School, Srirangam</div>
          <div class="dist">Pudukkottai</div>
          <div class="school">Shri Saraswathy Hr. Sec. School, Konapet</div>
        </div>
        <div>
          <div class="dist">Thiruvarur</div>
          <div class="school">Manba Ul Ula Hr. Sec. School, Koothanallur</div>
          <div class="school">Sri Mahadeva Guruji Vidyalaya Matriculation School, Kudavasal</div>
          <div class="dist">Madurai · Virudhunagar · Tirunelveli</div>
          <div class="school">Govt Hr. Sec. School, Madurai</div>
          <div class="school">Govt Hr. Sec. School, Virudhunagar</div>
          <div class="school">Fathima Girls Hr. Sec. School, Tirunelveli</div>
        </div>
      </div>
      <div style="background: var(--lilac); border-radius: 28px; padding: 36px 40px; display: flex; flex-direction: column; gap: 8px;">
        <div style="display: flex; align-items: baseline; gap: 14px; flex-wrap: wrap;"><h3 style="font-size: 26px;">Colleges and institutes</h3><span style="font-size: 14px; color: var(--muted);">9 institutions</span></div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
          <span class="tag" style="background: #fff; color: var(--ink);">Chellammal College, Guindy</span>
          <span class="tag" style="background: #fff; color: var(--ink);">CA Foundation, Mylapore</span>
          <span class="tag" style="background: #fff; color: var(--ink);">Guru Nanak College, Velachery</span>
          <span class="tag" style="background: #fff; color: var(--ink);">Anand Institute of Engineering, Padur</span>
          <span class="tag" style="background: #fff; color: var(--ink);">VELS University, Tambaram</span>
          <span class="tag" style="background: #fff; color: var(--ink);">University of Madras, Chennai</span>
          <span class="tag" style="background: #fff; color: var(--ink);">Jamal Mohamed College, Trichy</span>
          <span class="tag" style="background: #fff; color: var(--ink);">Rajarajan College, Amaravathi</span>
          <span class="tag" style="background: #fff; color: var(--ink);">St. Joseph Polytechnic College, Thirumayam</span>
        </div>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
