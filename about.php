<?php
// About us page. Shared layout lives in inc/ (head → page CSS → header → content → footer).
$page       = 'about';
$page_title = 'About us';
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
.card{background:#fff;border:1px solid var(--line);border-radius:24px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.g4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px}
.g5{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:20px}
.img{display:block;width:100%;object-fit:cover}
.num{font-family:var(--display);font-weight:800;letter-spacing:-0.04em;line-height:1}
.msg p{margin:0;font-size:17px;line-height:1.75;color:var(--ink)}
.pol{display:flex;gap:20px;align-items:flex-start;padding:24px 0;border-top:1px solid var(--line)}
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
.pname{font-size:17px !important;line-height:1.2}
.mspan{grid-column:1 / -1 !important}
.pol{flex-direction:column;gap:8px}.msg p{font-size:16px}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<?php require __DIR__ . '/inc/header.php'; ?>

  <!-- Opening: mission over the stage photo -->
  <section id="top" class="hero-over" style="position: relative; overflow: hidden; min-height: 640px; display: flex; align-items: center; margin-top: -112px; padding-top: 112px;">
    <img src="assets/stage.jpg" alt="Trustees and guests on stage at the first sponsorship function" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 50% 30%;">
    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(18,26,82,0.55) 0%, rgba(18,26,82,0.88) 100%);"></div>
    <div class="wrap" style="position: relative; width: 100%; padding-top: 64px; padding-bottom: 88px; display: flex; flex-direction: column; gap: 22px; color: #fff; max-width: 1000px;">
      <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">About Us</span>
      <h1 style="font-size: 64px; color: #fff;">Our mission</h1>
      <p style="margin: 0; font-size: 21px; line-height: 1.6; color: #fff; max-width: 860px;">Uma Lakshmi Educational Trust exists so that no deserving child in Tamil Nadu has to give up education for want of money. We identify meritorious students from economically disadvantaged families, pay their school and college fees directly to their institutions, and stand beside them until they reach their goals.</p>
      <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--on-dark-muted); max-width: 860px;">We believe that giving basic education to many children matters more than giving extra education to a few; that educating a girl child educates a whole family; that no child who loses a parent should have to stop studying; and that donors deserve clear, accurate and honest information about where every rupee goes.</p>
    </div>
  </section>

  <!-- Bharathiyar -->
  <section style="background: var(--cream); padding: 96px 0;">
    <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 56px; align-items: center;">
      <div style="flex: 0 1 520px; min-width: 0;">
        <img src="assets/bharathi.jpg" alt="Portrait of Bharathiyar beside his verse on the merit of teaching one poor child to read" class="img" style="aspect-ratio: 13 / 10; border-radius: 28px; border: 8px solid #fff; box-shadow: 0 24px 60px rgba(61,43,0,0.12);">
      </div>
      <div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 18px;">
        <span class="eyebrow">The verse that inspired the trust</span>
        <h2 style="font-size: 44px;">Mahakavi Bharathiyar</h2>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--muted);">Bharathiyar wrote that one may build a thousand feeding houses and establish ten thousand temples, and that all such acts of charity bring merit — yet greater than all of them put together is the merit of teaching a single poor child to read.</p>
        <p style="margin: 0; font-size: 17px; line-height: 1.7; color: var(--muted);">It was this verse that moved our founder to establish an educational trust in his wife's name. It remains the measure of everything we do: not how much we give, but how many children we help to learn.</p>
      </div>
    </div>
  </section>

  <!-- Values -->
  <section style="padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 40px;">
      <div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px;">
        <span class="eyebrow">Our values</span>
        <h2 style="font-size: 52px;">What guides every decision we make.</h2>
      </div>
      <div class="g5">
        <div class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 12px; background: var(--lilac); border: 0;">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>
          <h3 style="font-size: 22px;">Compassion</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">Every decision begins with the child in front of us, and the love in whose memory the trust was founded.</p>
        </div>
        <div class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 12px; background: var(--cream); border: 0;">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--marigold-ink)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4L12 14.3 7.2 16.9l.9-5.4L4.2 7.7l5.4-.8z"></path></svg>
          <h3 style="font-size: 22px;">Merit with need</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">We support students who have both the ability to learn and a real financial need — without discrimination of any kind.</p>
        </div>
        <div class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 12px; background: var(--lilac); border: 0;">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
          <h3 style="font-size: 22px;">Transparency</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">Fees go directly to institutions, accounts are audited every year, and donors can see exactly what their donation achieved.</p>
        </div>
        <div class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 12px; background: var(--coral-tint); border: 0;">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--coral)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"></path></svg>
          <h3 style="font-size: 22px;">Dignity</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">Students are partners, not beneficiaries. Their information is kept private and their achievements are celebrated.</p>
        </div>
        <div class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 12px; background: var(--cream); border: 0;">
          <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--marigold-ink)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
          <h3 style="font-size: 22px;">Continuity</h3>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">The trust is built around a purpose, not a person, so that the chain of kindness continues beyond generations.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Policies: how we work -->
  <section id="policies" style="padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 64px; align-items: flex-start;">
      <div style="flex: 0 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 14px;">
        <span class="eyebrow">Our policies</span>
        <h2 style="font-size: 48px;">How we work.</h2>
        <p style="margin: 0; font-size: 16px; line-height: 1.65; color: var(--muted);">Clear rules, applied the same way to every applicant, every year. These are the policies the trustees follow when selecting and supporting students.</p>
      </div>
      <div style="flex: 1 1 600px; min-width: 0; display: flex; flex-direction: column;">
        <div class="pol"><span class="num" style="font-size: 28px; color: var(--marigold); flex: 0 0 52px;">01</span><span><strong style="display: block; font-size: 19px; margin-bottom: 6px;">Who can apply</strong><span style="font-size: 15px; line-height: 1.65; color: var(--muted);">Students of government and government-aided schools and colleges in Tamil Nadu whose family income is limited, with a satisfactory academic record. Preference is given to girl children, to students who have lost a parent, and to first-generation learners.</span></span></div>
        <div class="pol"><span class="num" style="font-size: 28px; color: var(--marigold); flex: 0 0 52px;">02</span><span><strong style="display: block; font-size: 19px; margin-bottom: 6px;">How students are selected</strong><span style="font-size: 15px; line-height: 1.65; color: var(--muted);">Applications are reviewed by the trustees on both need and merit, using the application form, mark sheets, income details and the headmaster's or principal's attestation. Selection is made without regard to religion, caste, community or gender.</span></span></div>
        <div class="pol"><span class="num" style="font-size: 28px; color: var(--marigold); flex: 0 0 52px;">03</span><span><strong style="display: block; font-size: 19px; margin-bottom: 6px;">How fees are paid</strong><span style="font-size: 15px; line-height: 1.65; color: var(--muted);">Sponsorship is paid by cheque or transfer in favour of the institution, never in cash to individuals. Cheques are presented at the annual function or posted to outstation students for handing over to their school or college.</span></span></div>
        <div class="pol"><span class="num" style="font-size: 28px; color: var(--marigold); flex: 0 0 52px;">04</span><span><strong style="display: block; font-size: 19px; margin-bottom: 6px;">Continuing support</strong><span style="font-size: 15px; line-height: 1.65; color: var(--muted);">Students may apply again each year. Continued support depends on regular attendance, passing all subjects and the institution's confirmation that the student is enrolled.</span></span></div>
        <div class="pol" style="border-bottom: 1px solid var(--line);"><span class="num" style="font-size: 28px; color: var(--marigold); flex: 0 0 52px;">05</span><span><strong style="display: block; font-size: 19px; margin-bottom: 6px;">Privacy and accountability</strong><span style="font-size: 15px; line-height: 1.65; color: var(--muted);">Applicant and family information is used only for selection and is never published or shared. The trust's accounts are audited annually and its registrations under Sections 12A and 80G are maintained in good standing.</span></span></div>
      </div>
    </div>
  </section>

  <!-- Trustees -->
  <section style="background: var(--cream); padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 40px;">
      <div style="display: flex; flex-direction: column; gap: 12px;"><span class="eyebrow">The people behind the trust</span><h2 style="font-size: 48px;">Our trustees.</h2></div>
      <div class="mc2" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; max-width: 1152px;">
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/founder2.jpg" alt="SP. Nachiappan" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 15%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><span><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">SP. Nachiappan</strong><span style="font-size: 15px; color: var(--muted);">Founder &amp; Managing Trustee</span></span></div>
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/subramanian2.jpg" alt="N. Subramanian" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 20%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><span><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">N. Subramanian</strong><span style="font-size: 15px; color: var(--muted);">Financial Trustee</span></span></div>
      </div>
    </div>
  </section>

  <!-- Volunteers -->
  <section style="background: #fff; padding: 104px 0;">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 40px;">
      <div style="display: flex; flex-direction: column; gap: 12px;"><span class="eyebrow">The hands that help</span><h2 style="font-size: 48px;">Our volunteers.</h2></div>
      <div class="mc2" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; max-width: 1152px;">
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/vol2.jpg" alt="K. Murugappan" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 20%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">K. Murugappan</strong></div>
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/vol4.jpg" alt="M. Parvathi" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 20%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">M. Parvathi</strong></div>
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/vol3.jpg" alt="SP. Palaniappan" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 20%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">SP. Palaniappan</strong></div>
        <div style="display: flex; flex-direction: column; gap: 16px;"><img src="assets/vol1.jpg" alt="P. Vishalakshi" class="img" style="aspect-ratio: 4 / 5; object-position: 50% 15%; border-radius: 22px; box-shadow: 0 20px 50px rgba(61,43,0,0.12);"><strong class="pname" style="display: block; font-family: var(--display); font-size: 20px;">P. Vishalakshi</strong></div>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/inc/footer.php'; ?>
