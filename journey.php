<?php
// Our Journey page. Shared layout lives in inc/ (head → page CSS → header → content → footer).
// The photo gallery is read from assets/gallery/<year>/ (see inc/gallery.php and assets/gallery/README.txt).
$page       = 'journey';
$page_title = 'Our Journey';
require __DIR__ . '/inc/head.php';
require_once __DIR__ . '/inc/gallery.php';
$gallery = gallery_build(__DIR__ . '/assets/gallery');
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
.card{background:#fff;border:1px solid var(--line);border-radius:24px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.g4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px}
.img{display:block;width:100%;object-fit:cover}
.num{font-family:var(--display);font-weight:800;letter-spacing:-0.04em;line-height:1}
.yr{display:grid;grid-template-columns:minmax(0,1fr) 120px minmax(0,1fr);gap:32px;align-items:center;padding:36px 0}
.dot{width:120px;display:flex;flex-direction:column;align-items:center;gap:10px}
.dot span.y{font-family:var(--display);font-weight:800;font-size:22px;color:#fff;background:var(--indigo);border-radius:999px;padding:10px 18px}
.filter{display:inline-flex;gap:6px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:6px}
.filter a{padding:10px 18px;border-radius:999px;font-weight:700;font-size:14px;color:var(--ink)}
.filter a.on{background:var(--ink);color:#fff}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.g3,.g4{grid-template-columns:repeat(2,minmax(0,1fr))}.nav{display:none}.yr{grid-template-columns:1fr}.dot{width:auto;align-items:flex-start}}
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
.yr{grid-template-columns:1fr !important;gap:14px !important;padding:28px 0 !important}.yr>div{text-align:left !important;align-items:flex-start !important}.yr .dot{order:0;width:auto !important;flex-direction:row !important;align-items:center !important;gap:14px !important}.yr>div[style*="flex-direction: column"]{order:1}.yr>div.mphoto{order:2;grid-template-columns:1fr 1fr !important;gap:8px !important}.yr>div.mphoto>img{grid-column:1 / -1;aspect-ratio:16 / 10 !important;border-radius:16px !important}.yr>div.mphoto>div{grid-column:1 / -1;display:grid;grid-template-columns:1fr 1fr;gap:8px}.yr>div.mphoto>div>img{aspect-ratio:4 / 3 !important;height:auto !important;border-radius:12px !important}.yr .dot span.y{font-size:18px !important;padding:8px 14px !important}.tl-line{display:none}[style*="grid-auto-rows: 220px"]{grid-auto-rows:150px !important}.filter{flex-wrap:wrap}.filter a{padding:8px 12px;font-size:13px}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<style>/* Gallery: tiles come from assets/gallery/<year>/; the filter below toggles them. */.gal{grid-auto-flow:dense}.gal img{height:100%;border-radius:20px}.gal img.big{grid-column:span 2;grid-row:span 2}.gal img.wide{grid-column:span 2}.gal img[hidden],.gal[hidden]{display:none}.gal-empty{margin:0;text-align:center;color:var(--muted);font-size:15px;padding:40px 24px;border:2px dashed var(--lilac2);border-radius:20px;background:var(--lilac)}</style>
<?php require __DIR__ . '/inc/header.php'; ?>

  <!-- Opening -->
  <section id="top" class="hero-over" style="position: relative; overflow: hidden; min-height: 560px; display: flex; align-items: center; margin-top: -112px; padding-top: 112px;">
    <img src="assets/g1.jpg" alt="Students singing at the sponsorship function" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 50% 30%;">
    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(18,26,82,0.5) 0%, rgba(18,26,82,0.9) 100%);"></div>
    <div class="wrap" style="position: relative; width: 100%; padding-top: 56px; padding-bottom: 80px; display: flex; flex-direction: column; gap: 22px; color: #fff; max-width: 1000px;">
      <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Our Journey</span>
      <h1 style="font-size: 64px; color: #fff;">Five years of lighting young minds.</h1>
      <p style="margin: 0; font-size: 19px; line-height: 1.65; color: #fff; max-width: 860px;">What began on 11 June 2022 as a tribute to Uma Lakshmi has become an annual promise kept to a growing family of students. From thirty children in one Chennai school to seventy-five across ten districts, every year has brought more students, more schools, and more families who no longer have to choose between fees and food.</p>
      <p style="margin: 0; font-size: 16px; line-height: 1.7; color: var(--on-dark-muted); max-width: 860px;">265+ students sponsored · 21+ schools and 9+ colleges · 10+ districts of Tamil Nadu.</p>
    </div>
  </section>

  <!-- Timeline -->
  <section style="padding: 96px 0 72px;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 24px;">
      <div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px; padding-bottom: 24px;"><span class="eyebrow">Year by year</span><h2 style="font-size: 52px;">The milestones so far.</h2></div>
      <div style="position: relative;">
        <div class="tl-line" style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: var(--lilac2); margin-left: -1px;"></div>

        <div class="yr" style="position: relative;">
          <div style="display: flex; flex-direction: column; gap: 14px; text-align: right; align-items: flex-end;">
            <span class="tag" style="background: var(--cream); color: var(--marigold-ink);">Founded 11 June 2022</span>
            <h3 style="font-size: 30px;">The first thirty students</h3>
            <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">Within a month of founding, notebooks and stationery reached 80 children at CSI School, Mandaveli. On 10 September the first sponsorship function at Adyar KB Nagar RA Club awarded cheques to 30 students from government-aided schools, in the presence of headmistress Mrs. Amutha, Dr. V. Bhaskar and Dr. N. Veerappan.</p>
          </div>
          <div class="dot"><span class="y">2022</span><span class="num" style="font-size: 40px; color: var(--indigo);">30</span><span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">students</span></div>
          <div class="mc2 mphoto" style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;"><img src="assets/y2022.jpg" alt="First sponsorship function, 2022" class="img" style="aspect-ratio: 4 / 3; border-radius: 20px;"><div style="display: grid; gap: 10px;"><img src="assets/t2022a.jpg" alt="Stationery distribution" class="img" style="height: 100%; border-radius: 16px;"><img src="assets/t_speech.jpg" alt="Founder's address" class="img" style="height: 100%; border-radius: 16px;"></div></div>
        </div>

        <div class="yr" style="position: relative;">
          <div class="mc2 mphoto" style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;"><div style="display: grid; gap: 10px;"><img src="assets/t_cheque2.jpg" alt="Cheque presentation, 2023" class="img" style="height: 100%; border-radius: 16px;"><img src="assets/t_audience.jpg" alt="Audience at the function" class="img" style="height: 100%; border-radius: 16px;"></div><img src="assets/y2023.jpg" alt="Second sponsorship function, 2023" class="img" style="aspect-ratio: 4 / 3; border-radius: 20px;"></div>
          <div class="dot"><span class="y">2023</span><span class="num" style="font-size: 40px; color: var(--indigo);">46</span><span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">students</span></div>
          <div style="display: flex; flex-direction: column; gap: 14px;">
            <span class="tag" style="background: var(--lilac); color: var(--indigo);">30 April 2023 · RA Puram</span>
            <h3 style="font-size: 30px;">Reaching beyond Chennai</h3>
            <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">The second function at Rani Meyyammai Girls Higher Secondary School welcomed Dr. Anantha Valli of CSIR as chief guest. Forty-six students were sponsored, and for the first time cheques were posted to outstation students in other districts.</p>
          </div>
        </div>

        <div class="yr" style="position: relative;">
          <div style="display: flex; flex-direction: column; gap: 14px; text-align: right; align-items: flex-end;">
            <span class="tag" style="background: var(--lilac); color: var(--indigo);">22 June 2024 · RA Puram</span>
            <h3 style="font-size: 30px;">Growing for the third year</h3>
            <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">Fifty-one students received sponsorship at the third annual function. The founder spoke of the steady growth in student strength, and this was also the year the trust began extending financial support to girl childrens who have lost their parents.</p>
          </div>
          <div class="dot"><span class="y">2024</span><span class="num" style="font-size: 40px; color: var(--indigo);">51</span><span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">students</span></div>
          <div class="mc2 mphoto" style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;"><img src="assets/t_group.jpg" alt="Trustees and guests, 2024" class="img" style="aspect-ratio: 4 / 3; border-radius: 20px;"><div style="display: grid; gap: 10px;"><img src="assets/t_cheque3.jpg" alt="A guest is honoured" class="img" style="height: 100%; border-radius: 16px;"><img src="assets/y2024.jpg" alt="2024 function poster" class="img" style="height: 100%; object-position: 50% 0; border-radius: 16px;"></div></div>
        </div>

        <div class="yr" style="position: relative;">
          <div class="mc2 mphoto" style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;"><div style="display: grid; gap: 10px;"><img src="assets/g4.jpg" alt="A student receives her cheque" class="img" style="height: 100%; border-radius: 16px;"><img src="assets/t_stage.jpg" alt="The stage" class="img" style="height: 100%; border-radius: 16px;"></div><img src="assets/y2025.jpg" alt="Fourth sponsorship function, 2025" class="img" style="aspect-ratio: 4 / 3; border-radius: 20px;"></div>
          <div class="dot"><span class="y">2025</span><span class="num" style="font-size: 40px; color: var(--indigo);">63</span><span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">students</span></div>
          <div style="display: flex; flex-direction: column; gap: 14px;">
            <span class="tag" style="background: var(--cream); color: var(--marigold-ink);">June 2025 · 12A &amp; 80G received</span>
            <h3 style="font-size: 30px;">Recognised and registered</h3>
            <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">Sixty-three students from schools and colleges in ten districts were sponsored at the fourth function. The same year the trust received its permanent registration under Section 12A and approval under Section 80G, so that every donor's gift is now tax-deductible.</p>
          </div>
        </div>

        <div class="yr" style="position: relative;">
          <div style="display: flex; flex-direction: column; gap: 14px; text-align: right; align-items: flex-end;">
            <span class="tag" style="background: var(--marigold); color: var(--marigold-ink);">2026 · Fifth anniversary</span>
            <h3 style="font-size: 30px;">Seventy-five students, and counting</h3>
            <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">The fifth annual sponsorship programme marks five years of the trust and its largest cohort yet — 75 students — bringing the total to 265 students supported since 2022. [Date, venue and highlights to be added after the function.]</p>
          </div>
          <div class="dot"><span class="y" style="background: var(--marigold); color: var(--marigold-ink);">2026</span><span class="num" style="font-size: 40px; color: var(--indigo);">75</span><span style="font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">students</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Activities -->
  <section style="background: var(--indigo); color: #fff; padding: 104px 0; position: relative; overflow: hidden;">
    <div style="position: absolute; right: -120px; top: -120px; width: 420px; height: 420px; border-radius: 999px; border: 48px solid rgba(255,255,255,0.06);"></div>
    <div class="wrap" style="display: flex; flex-direction: column; gap: 44px; position: relative;">
      <div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px;"><span class="eyebrow" style="color: var(--marigold);">Our activities</span><h2 style="font-size: 52px; color: #fff;">More than fees.</h2><p style="margin: 0; font-size: 17px; line-height: 1.65; color: var(--on-dark-muted);">Fee sponsorship is the heart of the trust's work, but it is not all of it. Alongside the annual programme, the trust responds to needs as they arise.</p></div>
      <div class="g4">
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
          <span class="tag" style="background: var(--marigold); color: var(--marigold-ink); align-self: flex-start;">01</span>
          <h3 style="font-size: 22px; color: #fff;">Fee sponsorship</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--on-dark-muted);">Annual school and college fees for 265+ students over five years, paid directly to 30+ institutions.</p>
        </div>
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
          <span class="tag" style="background: var(--coral); color: #fff; align-self: flex-start;">02</span>
          <h3 style="font-size: 22px; color: #fff;">Study materials</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--on-dark-muted);">Notebooks and stationery for around 100 children at CSI Elementary School, Mylapore, beginning in the trust's first month.</p>
        </div>
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
          <span class="tag" style="background: #fff; color: var(--indigo); align-self: flex-start;">03</span>
          <h3 style="font-size: 22px; color: #fff;">Children's home support</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--on-dark-muted);">Living and study materials supplied to a school for orphaned and abandoned children.</p>
        </div>
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
          <span class="tag" style="background: var(--lilac2); color: var(--indigo); align-self: flex-start;">04</span>
          <h3 style="font-size: 22px; color: #fff;">Full support for girls</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--on-dark-muted);">Three girl children fully sponsored for food, clothing and education together.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Gallery (photos read from assets/gallery/<year>/) -->
  <section id="gallery" style="padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 36px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap;">
        <div style="display: flex; flex-direction: column; gap: 12px;"><span class="eyebrow">Gallery</span><h2 style="font-size: 52px;">Moments from our functions.</h2></div>
        <nav class="filter" aria-label="Filter photos by year"><a href="#gallery" class="on" data-year="">All</a><?php foreach ($gallery['years'] as $y): ?><a href="#gallery" data-year="<?= h($y) ?>"><?= h($y) ?></a><?php endforeach; ?></nav>
      </div>
      <div id="gal" class="mc2 gal" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: 220px; gap: 14px;">
<?php foreach ($gallery['tiles'] as $t): ?>
        <img src="<?= h($t['src']) ?>" alt="<?= h($t['alt']) ?>" class="img" data-year="<?= h($t['year']) ?>" loading="lazy">
<?php endforeach; ?>
      </div>
      <p id="gal-empty" class="gal-empty" hidden>Photographs from the <span></span> function will be added soon.</p>
    </div>
  </section>
<script>
(function(){
  var gal=document.getElementById('gal'), empty=document.getElementById('gal-empty'); if(!gal) return;
  var tiles=[].slice.call(gal.querySelectorAll('img')), links=[].slice.call(document.querySelectorAll('.filter a')), ALL_MAX=12;
  function apply(year){
    var shown=0;
    tiles.forEach(function(t){ var ok = year ? t.getAttribute('data-year')===year : shown<ALL_MAX; if(ok) shown++; t.hidden=!ok; t.classList.remove('big','wide'); });
    var vis=tiles.filter(function(t){ return !t.hidden; });
    if(vis.length>=8){ vis[0].classList.add('big'); vis[7].classList.add('wide'); }
    gal.hidden=!vis.length; empty.hidden=!!vis.length; if(!vis.length) empty.querySelector('span').textContent=year||'next';
  }
  links.forEach(function(a){ a.addEventListener('click',function(e){ e.preventDefault(); links.forEach(function(b){ b.classList.remove('on'); }); a.classList.add('on'); apply(a.getAttribute('data-year')||''); }); });
  apply('');
})();
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>
