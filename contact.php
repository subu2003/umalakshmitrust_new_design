<?php
// Contact page. Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
// The form posts to form_process.php, which stores the message and mails the trust inbox.
$page        = 'contact';
$page_title  = 'Contact';
$header_band = true;
require_once __DIR__ . '/inc/csrf.php';
uet_session_start();
$flash = $_SESSION['contact_flash'] ?? null;
unset($_SESSION['contact_flash']);
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
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:54px;padding:15px 28px;border-radius:999px;font-family:var(--body);font-weight:700;font-size:16px;cursor:pointer;border:2px solid transparent}
.btn-marigold{background:var(--marigold);color:var(--marigold-ink);border-color:var(--marigold)}
.btn-marigold:hover{background:#F0A500;border-color:#F0A500;color:var(--marigold-ink)}
.btn-indigo{background:var(--indigo);color:#fff;border-color:var(--indigo)}
.btn-indigo:hover{background:var(--indigo-deep);border-color:var(--indigo-deep);color:#fff}
.card{background:#fff;border:1px solid var(--line);border-radius:24px}
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.ci{display:flex;gap:18px;align-items:flex-start;padding:22px 0;border-bottom:1px solid var(--line)}
.ci .ic{width:52px;height:52px;border-radius:16px;background:var(--lilac);display:inline-flex;align-items:center;justify-content:center;flex:0 0 52px}
.ci strong{display:block;font-family:var(--display);font-size:18px;font-weight:800;margin-bottom:4px}
.ci span,.ci a{font-size:15px;line-height:1.6;color:var(--muted)}
.ci a{color:var(--indigo);font-weight:600}
.f{display:flex;flex-direction:column;gap:6px;font-size:14px;font-weight:700}
.in{width:100%;box-sizing:border-box;min-height:52px;padding:12px 16px;border:1.5px solid var(--line);border-radius:14px;font-family:var(--body);font-size:15px;color:var(--ink);background:#fff;font-weight:400}
.in:focus{outline:3px solid var(--lilac2);border-color:var(--indigo)}
textarea.in{min-height:140px;resize:vertical}
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
.card.pad36{padding:24px 20px !important}.ci{gap:14px}.ci .ic{width:44px;height:44px;flex-basis:44px}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<?php require __DIR__ . '/inc/header.php'; ?>
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 160px;">
      <div class="wrap" style="display: flex; flex-direction: column; gap: 18px; max-width: 980px;">
        <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Contact</span>
        <h1 style="font-size: 64px; color: #fff;">We'd love to hear from you.</h1>
        <p style="margin: 0; font-size: 19px; line-height: 1.65; color: var(--on-dark-muted); max-width: 820px;">Whether you want to sponsor a student, refer a child who needs support, volunteer at our annual function, or simply know more about the trust — write to us, call, or visit.</p>
      </div>
    </section>
  </div>

  <!-- Contact info + form -->
  <section style="position: relative; margin-top: -96px; padding-bottom: 104px;">
    <div class="wrap mc1" style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.3fr); gap: 24px; align-items: start;">

      <div class="card pad36" style="padding: 36px; box-shadow: 0 24px 60px rgba(20,22,51,0.10); display: flex; flex-direction: column;">
        <span class="eyebrow" style="margin-bottom: 6px;">Contact info</span>
        <h2 style="font-size: 28px; margin-bottom: 8px;">Umalakshmi Educational Trust</h2>
        <div class="ci"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></span><div><strong>Our address</strong><span>3A, Palazzo Apartment, Ram Kamal Building,<br>Shasthiri Nagar First Street, Adyar,<br>Chennai 600020</span><br><a href="https://maps.google.com/?q=Shastri+Nagar+First+Street+Adyar+Chennai+600020" style="display: inline-block; margin-top: 6px;">Open in Google Maps →</a></div></div>
        <div class="ci"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6.2 6.2l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"></path></svg></span><div><strong>Phone &amp; WhatsApp</strong><a href="tel:+919176189469">+91 9176189469</a><br><a href="https://wa.me/919176189469" style="display: inline-block; margin-top: 4px;">Message on WhatsApp →</a></div></div>
        <div class="ci" style="border-bottom: 0;"><span class="ic"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg></span><div><strong>Email</strong><a href="mailto:umalakshmieducationaltrust@gmail.com">umalakshmieducationaltrust@gmail.com</a></div></div>
      </div>

      <form id="msgform" class="card mc1" style="padding: 36px; box-shadow: 0 24px 60px rgba(20,22,51,0.10); display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; align-content: start;" action="form_process.php" method="post">
        <div class="mspan mspan" style="grid-column: span 2; display: flex; flex-direction: column; gap: 6px;"><span class="eyebrow">Get in touch</span><h2 style="font-size: 28px;">Send us a message</h2></div>
        <?= csrf_field() ?>
        <div style="position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true"><label>Leave this field empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
<?php if ($flash && ($flash['type'] ?? '') === 'error'): ?>
        <div id="form-errors" role="alert" class="mspan" style="grid-column: span 2; background: var(--coral-tint); border: 1.5px solid var(--coral); border-radius: 16px; padding: 14px 18px; font-size: 14px; line-height: 1.6; color: var(--ink);">
          <strong>Please correct the following:</strong>
          <ul style="margin: 4px 0 0; padding-left: 20px;"><?php foreach ((array) ($flash['errors'] ?? []) as $msg): ?><li><?= h($msg) ?></li><?php endforeach; ?></ul>
        </div>
<?php endif; ?>
        <label class="f">Name <span style="color: var(--coral);">*</span><input class="in" type="text" name="firstname" required=""></label>
        <label class="f">Email <span style="color: var(--coral);">*</span><input class="in" type="email" name="email" required=""></label>
        <label class="f">Phone / WhatsApp<input class="in" type="tel" name="phone" placeholder="+91"></label>
        <label class="f">I'm writing about<select class="in" name="subject"><option value="">Select…</option><option>Making a donation</option><option>Applying for support</option><option>Referring a student</option><option>Volunteering</option><option>Donation receipt / 80G</option><option>Something else</option></select></label>
        <label class="f mspan" style="grid-column: span 2;">Message <span style="color: var(--coral);">*</span><textarea class="in" name="comment" required="" placeholder="How can we help?"></textarea></label>
        <div class="mspan mspan" style="grid-column: span 2; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
          <span style="font-size: 13px; color: var(--muted);">We use your details only to reply to you.</span>
          <button type="submit" class="btn btn-indigo" id="sendbtn">Send message<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></button>
        </div>
      </form>
    </div>
  </section>

  <!-- Quick links -->
  <section style="background: var(--lilac); padding: 72px 0;">
    <div class="wrap">
      <div class="mc1" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px;">
        <a href="<?= h(page_href('donate.php')) ?>" class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 8px; color: var(--ink); border: 0;"><span class="tag" style="background: var(--marigold); color: var(--marigold-ink); align-self: flex-start;">Donate</span><strong style="font-family: var(--display); font-size: 22px;">Sponsor a student</strong><span style="font-size: 14px; color: var(--muted);">Bank transfer or UPI, with an 80G receipt.</span></a>
        <a href="<?= h(page_href('apply.php')) ?>" class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 8px; color: var(--ink); border: 0;"><span class="tag" style="background: var(--lilac); color: var(--indigo); align-self: flex-start;">Apply</span><strong style="font-family: var(--display); font-size: 22px;">Apply for support</strong><span style="font-size: 14px; color: var(--muted);">The online educational financial-aid form.</span></a>
      </div>
    </div>
  </section>

<?php if ($flash && ($flash['type'] ?? '') === 'success'): ?>
<div id="okmodal" role="dialog" aria-modal="true" aria-labelledby="okttl" style="position: fixed; inset: 0; z-index: 200; background: rgba(18,26,82,0.72); display: flex; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; border-radius: 28px; padding: 40px 36px; max-width: 480px; width: 100%; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 14px; box-shadow: 0 30px 80px rgba(20,22,51,0.35);">
    <span style="width: 72px; height: 72px; border-radius: 999px; background: var(--marigold); display: inline-flex; align-items: center; justify-content: center;"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--marigold-ink)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg></span>
    <h2 id="okttl" style="font-size: 28px;">Message sent</h2>
    <p style="margin: 0; font-size: 16px; line-height: 1.65; color: var(--ink);">Thank you, <strong><?= h($flash['name'] ?? '') ?></strong>. Your message has reached the trust and we will reply to <strong><?= h($flash['email'] ?? '') ?></strong> as soon as we can.</p>
    <button type="button" class="btn btn-indigo" style="margin-top: 8px;" onclick="document.getElementById('okmodal').remove()">Close</button>
  </div>
</div>
<?php endif; ?>
<script>
(function(){
  var f=document.getElementById('msgform'), b=document.getElementById('sendbtn');
  if(f&&b){ f.addEventListener('submit',function(){ if(!f.checkValidity||f.checkValidity()){ b.disabled=true; b.firstChild.textContent='Sending… '; } }); }
  var e=document.getElementById('form-errors'); if(e){ e.scrollIntoView({block:'center'}); }
  var m=document.getElementById('okmodal'); if(m){ document.addEventListener('keydown',function(ev){ if(ev.key==='Escape') m.remove(); }); }
})();
</script>
<?php require __DIR__ . '/inc/footer.php'; ?>
