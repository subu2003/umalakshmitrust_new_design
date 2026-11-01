<?php
// Apply for Scholarship page (4-step aid application form).
// Shared layout lives in inc/ (head → page CSS → header → content → footer). Header sits in the indigo band;
// inc/header.php opens the band and this page closes it with </div> after the intro section.
// Form field names are the contract for apply_process.php (backend step 3 in BACKEND-SPEC.md).
$page        = 'apply';
$page_title  = 'Apply for Scholarship';
$page_fonts  = '&amp;family=Noto+Sans+Tamil:wght@400;600';
$header_band = true;
require_once __DIR__ . '/inc/csrf.php';
uet_session_start();
// One-shot message from apply_process.php (success popup or validation errors)
$flash = $_SESSION['apply_flash'] ?? null;
unset($_SESSION['apply_flash']);
$maxPhotoMb = (float) env('MAX_PHOTO_MB', '2');
$maxDocMb   = (float) env('MAX_DOC_MB', '5');
require __DIR__ . '/inc/head.php';
?>
<style>
:root{
  --bg:#FFFFFF; --ink:#141633; --muted:#5B5E7A; --line:#E6E7F2;
  --indigo:#1E2A78; --indigo-deep:#121A52; --lilac:#EEF0FF; --lilac2:#DCDFFF;
  --marigold:#FFB300; --marigold-ink:#3D2B00; --cream:#FFF6DC;
  --coral:#FF5A5F; --coral-tint:#FFE9EA;
  --on-dark:#FFFFFF; --on-dark-muted:#C9CDF0;
  --display:'Bricolage Grotesque', system-ui, sans-serif; --body:'DM Sans', system-ui, sans-serif; --tamil:'Noto Sans Tamil', 'DM Sans', sans-serif;
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
.wrap{max-width:1240px;margin:0 auto;padding:0 32px;box-sizing:border-box}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.step{background:#fff;border:1px solid var(--line);border-radius:28px;padding:40px;display:flex;flex-direction:column;gap:24px}
.sh{display:flex;align-items:center;gap:16px}
.sn{width:48px;height:48px;border-radius:999px;background:var(--marigold);color:var(--marigold-ink);font-family:var(--display);font-weight:800;font-size:20px;display:inline-flex;align-items:center;justify-content:center;flex:0 0 48px}
.ta{font-family:var(--tamil);font-size:13px;color:var(--muted);font-weight:400}
.row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.row3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.row4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px}
.f{display:flex;flex-direction:column;gap:8px}
.f label{font-size:14px;font-weight:700;color:var(--ink);display:flex;flex-wrap:wrap;gap:6px;align-items:baseline}
.in{width:100%;box-sizing:border-box;min-height:50px;padding:12px 16px;border:1.5px solid var(--line);border-radius:14px;font-family:var(--body);font-size:15px;color:var(--ink);background:#fff}
.in:focus{outline:3px solid var(--lilac2);border-color:var(--indigo)}
textarea.in{min-height:96px;resize:vertical}
.chk{display:inline-flex;align-items:center;gap:10px;font-size:15px;font-weight:500;min-height:44px;padding:0 16px;border:1.5px solid var(--line);border-radius:999px;cursor:pointer}
.chk input{width:18px;height:18px;accent-color:var(--indigo)}
.up{border:2px dashed var(--lilac2);border-radius:16px;padding:18px;display:flex;align-items:center;gap:14px;background:var(--lilac);font-size:14px;color:var(--muted)}
.up strong{color:var(--ink);display:block;font-size:14px}
table.marks{width:100%;border-collapse:separate;border-spacing:0;font-size:14px}
table.marks th{text-align:left;font-weight:700;padding:10px 12px;background:var(--lilac);color:var(--indigo);font-size:13px}
table.marks th:first-child{border-radius:12px 0 0 12px}table.marks th:last-child{border-radius:0 12px 12px 0}
table.marks td{padding:6px 6px}
table.marks td .in{min-height:44px;padding:8px 12px}
.req{color:var(--coral)}
.ftr a{color:#C9CDF0}.ftr a:hover{color:#fff}
@media (max-width:980px){.row3,.row4{grid-template-columns:repeat(2,minmax(0,1fr))}.g3{grid-template-columns:repeat(1,minmax(0,1fr))}.nav{display:none}}
@media (max-width:600px){.row,.row3,.row4{grid-template-columns:repeat(1,minmax(0,1fr))}.wrap{padding:0 16px}h1{font-size:44px !important}.step{padding:24px}}

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
.row,.row3,.row4{grid-template-columns:1fr !important}.step{padding:22px 18px !important}.f label{font-size:13px}table.marks{display:block;overflow-x:auto;white-space:nowrap}table.marks td .in{min-width:88px}.sh h2{font-size:24px !important}.chk{font-size:14px}}
</style>
<style>html{scroll-behavior:smooth} *,*:before,*:after{box-sizing:border-box} img{max-width:100%}</style>
<style>/* Upload tiles: chosen-file states */.up.has-file{border-style:solid;border-color:var(--indigo);background:#fff}.up.has-file strong:after{content:" \2713";color:var(--indigo)}.up .up-sub{display:block}
.up.up-photo.has-file{position:relative;padding:0 !important;overflow:hidden}.up.up-photo.has-file img.up-prev{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.up.up-photo.has-file svg,.up.up-photo.has-file strong,.up.up-photo.has-file .up-sub{display:none}.up.up-photo .up-ok{display:none}.up.up-photo.has-file .up-ok{display:block;position:absolute;left:0;right:0;bottom:0;background:rgba(30,42,120,.86);color:#fff;font-size:12px;font-weight:700;padding:7px 10px;text-align:center}
.upwrap{display:flex;flex-direction:column;gap:6px;min-width:0}.uplist{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px;font-size:13px;color:var(--ink)}.uplist li{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);border-radius:10px;padding:6px 10px}.uplist li span{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.uplist li small{color:var(--muted);white-space:nowrap}.uplist button{border:0;background:var(--coral-tint);color:var(--coral);border-radius:999px;width:22px;height:22px;cursor:pointer;font-weight:700;line-height:1;flex:0 0 22px}.uperr{font-size:13px;color:var(--coral);font-weight:600}
@media (max-width:860px){.up.up-photo{width:100%;max-width:220px;min-height:0}}</style>
<?php require __DIR__ . '/inc/header.php'; ?>
    <section id="top" style="position: relative; color: #fff; padding: 56px 0 160px;">
      <div class="wrap" style="display: flex; flex-wrap: wrap; gap: 40px; align-items: flex-end; justify-content: space-between;">
        <div style="display: flex; flex-direction: column; gap: 16px; max-width: 760px;">
          <span style="font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: var(--marigold);">Apply for Support</span>
          <h1 style="font-size: 60px; color: #fff;">Educational Financial Aid Application</h1>
          <p style="margin: 0; font-family: var(--tamil); font-size: 18px; color: var(--on-dark-muted);">கல்வி உதவித்தொகை விண்ணப்பப் படிவம் · கல்வி ஆண்டு 2026 – 2027</p>
          <p style="margin: 0; font-size: 17px; line-height: 1.65; color: var(--on-dark-muted);">Students of government and government-aided schools and colleges may apply. Please keep your mark sheets, fee demand letter, income certificate and a passport-size photo ready before you begin. The form takes about 15 minutes.</p>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px; color: var(--on-dark-muted); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); border-radius: 20px; padding: 20px 24px; min-width: 280px;">
          <strong style="color: #fff; font-size: 13px; letter-spacing: 0.12em; text-transform: uppercase;">Before you apply</strong>
          <span>· Fees are paid directly to the institution</span>
          <span>· Selection is on need and merit</span>
          <span>· Your information is kept private</span>
          <a href="about.php#policies" style="color: var(--marigold); font-weight: 700;">Read our policies →</a>
        </div>
      </div>
    </section>
  </div>

  <!-- Step indicator (overlapping) -->
  <section style="position: relative; margin-top: -88px; padding-bottom: 40px;">
    <div class="wrap">
      <div class="mc2" style="background: #fff; border-radius: 24px; box-shadow: 0 24px 60px rgba(20,22,51,0.12); padding: 24px 32px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;"><span class="sn">1</span><span style="display: flex; flex-direction: column;"><strong style="font-size: 15px;">Applicant</strong><span class="ta">விண்ணப்பதாரர் விவரம்</span></span></div>
        <div style="display: flex; align-items: center; gap: 12px;"><span class="sn" style="background: var(--lilac); color: var(--indigo);">2</span><span style="display: flex; flex-direction: column;"><strong style="font-size: 15px;">Family &amp; income</strong><span class="ta">குடும்பம் / வருமான விவரம்</span></span></div>
        <div style="display: flex; align-items: center; gap: 12px;"><span class="sn" style="background: var(--lilac); color: var(--indigo);">3</span><span style="display: flex; flex-direction: column;"><strong style="font-size: 15px;">Education</strong><span class="ta">கல்வி விவரம்</span></span></div>
        <div style="display: flex; align-items: center; gap: 12px;"><span class="sn" style="background: var(--lilac); color: var(--indigo);">4</span><span style="display: flex; flex-direction: column;"><strong style="font-size: 15px;">Funding &amp; declaration</strong><span class="ta">நிதி விவரம் &amp; உறுதிமொழி</span></span></div>
      </div>
    </div>
  </section>

  <!-- Form -->
  <section style="padding: 24px 0 104px; background: var(--lilac);">
    <form id="applyform" class="wrap" style="display: flex; flex-direction: column; gap: 28px; max-width: 1000px;" action="apply_process.php" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div style="position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true"><label>Leave this field empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
<?php if ($flash && ($flash['type'] ?? '') === 'error'): ?>
      <div id="form-errors" role="alert" style="background: var(--coral-tint); border: 1.5px solid var(--coral); border-radius: 20px; padding: 20px 24px; display: flex; flex-direction: column; gap: 8px;">
        <strong style="font-size: 16px; color: var(--ink);">Please correct the following and submit again <span class="ta">பின்வருவனவற்றைச் சரிசெய்து மீண்டும் சமர்ப்பிக்கவும்</span></strong>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.6; color: var(--ink);">
<?php foreach ((array) ($flash['errors'] ?? []) as $msg): ?>
          <li><?= h($msg) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endif; ?>

      <!-- Step 1 -->
      <div class="step" id="step1">
        <div class="sh"><span class="sn">1</span><div><h2 style="font-size: 28px;">Applicant information</h2><span class="ta">விண்ணப்பதாரர் விவரம்</span></div></div>
        <div class="mc1" style="display: grid; grid-template-columns: minmax(0, 1fr) 180px; gap: 24px; align-items: start;">
          <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="f"><label for="name">Full name (in capitals) <span class="ta">பெயர்</span> <span class="req">*</span></label><input class="in" id="name" name="name" type="text" placeholder="AS IN SCHOOL RECORDS" required></div>
            <div class="row">
              <div class="f"><label>Gender <span class="ta">பாலினம்</span> <span class="req">*</span></label><div style="display: flex; gap: 10px; flex-wrap: wrap;"><label class="chk"><input type="radio" name="gender" value="male" required>Male <span class="ta">ஆண்</span></label><label class="chk"><input type="radio" name="gender" value="female">Female <span class="ta">பெண்</span></label></div></div>
              <div class="f"><label for="dob">Date of birth <span class="ta">பிறந்த தேதி</span> <span class="req">*</span></label><input class="in" id="dob" name="dob" type="date" required></div>
            </div>
            <div class="row">
              <div class="f"><label for="std">Standard / Year of study <span class="ta">வகுப்பு</span> <span class="req">*</span></label><select class="in" id="std" name="standard" required><option value="">Select…</option><option>Std 6</option><option>Std 7</option><option>Std 8</option><option>Std 9</option><option>Std 10</option><option>Std 11</option><option>Std 12</option><option>College — Year 1</option><option>College — Year 2</option><option>College — Year 3</option><option>College — Year 4</option></select></div>
              <div class="f"><label for="course">Course / Group <span class="ta">பாடப்பிரிவு</span></label><input class="in" id="course" name="course" type="text" placeholder="e.g. Biology-Maths, B.Com, B.E. ECE"></div>
            </div>
            <div class="row">
              <div class="f"><label for="phone">Contact phone <span class="ta">தொலைபேசி எண்</span> <span class="req">*</span></label><input class="in" id="phone" name="phone" type="tel" placeholder="+91" required></div>
              <div class="f"><label for="wa">WhatsApp number <span class="ta">வாட்ஸ் அப் எண்</span></label><input class="in" id="wa" name="whatsapp" type="tel" placeholder="+91"></div>
            </div>
            <div class="f"><label for="email">Email <span class="ta">மின்னஞ்சல்</span> <span class="req">*</span></label><input class="in" id="email" name="email" type="email" placeholder="name@example.com" required></div>
          </div>
          <div class="f"><label for="photo">Photograph <span class="ta">புகைப்படம்</span> <span class="req">*</span></label><label for="photo" class="up up-photo" style="flex-direction: column; text-align: center; aspect-ratio: 3 / 4; justify-content: center; padding: 16px;"><svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg><strong>Affix photo</strong><span class="up-sub">Passport size · JPG or PNG · under <?= h((string) $maxPhotoMb) ?> MB</span></label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png" data-max-mb="<?= h((string) $maxPhotoMb) ?>" style="position: absolute; width: 1px; height: 1px; opacity: 0;" required></div>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="step" id="step2">
        <div class="sh"><span class="sn">2</span><div><h2 style="font-size: 28px;">Family and income information</h2><span class="ta">குடும்பம் / வருமான விவரம்</span></div></div>
        <table class="marks" aria-label="Family members">
          <thead><tr><th style="width: 22%;">Member <span class="ta">உறவு</span></th><th>Name <span class="ta">பெயர்</span></th><th>Education <span class="ta">கல்வி</span></th><th>Occupation <span class="ta">தொழில்</span></th><th>Yearly income (₹) <span class="ta">ஆண்டு வருமானம்</span></th></tr></thead>
          <tbody>
            <tr><td><strong>Father</strong> <span class="ta">தந்தை</span> <span class="req">*</span></td><td><input class="in" type="text" name="father_name" aria-label="Father's name" required></td><td><input class="in" type="text" name="father_edu" aria-label="Father's education" required></td><td><input class="in" type="text" name="father_occ" aria-label="Father's occupation" required></td><td><input class="in" type="number" name="father_inc" aria-label="Father's yearly income" required></td></tr>
            <tr><td><strong>Mother</strong> <span class="ta">தாய்</span> <span class="req">*</span></td><td><input class="in" type="text" name="mother_name" aria-label="Mother's name" required></td><td><input class="in" type="text" name="mother_edu" aria-label="Mother's education" required></td><td><input class="in" type="text" name="mother_occ" aria-label="Mother's occupation" required></td><td><input class="in" type="number" name="mother_inc" aria-label="Mother's yearly income" required></td></tr>
            <tr><td><strong>Sibling 1</strong> <span class="ta">உடன்பிறந்தவர்</span></td><td><input class="in" type="text" name="sib1_name" aria-label="Sibling 1 name"></td><td><input class="in" type="text" name="sib1_edu" aria-label="Sibling 1 education"></td><td><input class="in" type="text" name="sib1_occ" aria-label="Sibling 1 occupation"></td><td><input class="in" type="number" name="sib1_inc" aria-label="Sibling 1 yearly income"></td></tr>
            <tr><td><strong>Sibling 2</strong></td><td><input class="in" type="text" name="sib2_name" aria-label="Sibling 2 name"></td><td><input class="in" type="text" name="sib2_edu" aria-label="Sibling 2 education"></td><td><input class="in" type="text" name="sib2_occ" aria-label="Sibling 2 occupation"></td><td><input class="in" type="number" name="sib2_inc" aria-label="Sibling 2 yearly income"></td></tr>
            <tr><td><strong>Guardian</strong> <span class="ta">காப்பாளர்</span></td><td><input class="in" type="text" name="guard_name" aria-label="Guardian's name"></td><td><input class="in" type="text" name="guard_edu" aria-label="Guardian's education"></td><td><input class="in" type="text" name="guard_occ" aria-label="Guardian's occupation"></td><td><input class="in" type="number" name="guard_inc" aria-label="Guardian's yearly income"></td></tr>
          </tbody>
        </table>
        <a href="#step2" style="font-size: 14px; font-weight: 700; align-self: flex-start;">+ Add another sibling</a>
        <div class="row">
          <div class="f"><label>Any other income? <span class="ta">இதர வருமானம்</span> <span class="req">*</span></label><div style="display: flex; gap: 10px;"><label class="chk"><input type="radio" name="other_income" value="yes" required>Yes <span class="ta">ஆம்</span></label><label class="chk"><input type="radio" name="other_income" value="no" required>No <span class="ta">இல்லை</span></label></div></div>
          <div class="f"><label for="other_inc_detail">If yes, details <span class="ta">ஆம் எனில் விவரம்</span></label><input class="in" id="other_inc_detail" name="other_income_detail" type="text"></div>
        </div>
        <div class="row">
          <div class="f"><label>Own house or rented? <span class="ta">சொந்த வீடு அல்லது வாடகை வீடு</span> <span class="req">*</span></label><div style="display: flex; gap: 10px;"><label class="chk"><input type="radio" name="house" value="own" required>Own <span class="ta">சொந்தம்</span></label><label class="chk"><input type="radio" name="house" value="rent" required>Rented <span class="ta">வாடகை</span></label></div></div>
          <div class="f"><label for="rent">If rented, rent per month (₹) <span class="ta">மாத வாடகை</span></label><input class="in" id="rent" name="rent" type="number"></div>
        </div>
        <div class="f"><label for="address">Residential address <span class="ta">வீட்டு முகவரி</span> <span class="req">*</span></label><textarea class="in" id="address" name="address" placeholder="Door no., street, area, town, district, PIN" required></textarea></div>
      </div>

      <!-- Step 3 -->
      <div class="step" id="step3">
        <div class="sh"><span class="sn">3</span><div><h2 style="font-size: 28px;">Education details</h2><span class="ta">கல்வி விவரம்</span></div></div>
        <div class="row">
          <div class="f"><label for="school">School / College name <span class="ta">பள்ளி / கல்லூரி பெயர்</span> <span class="req">*</span></label><input class="in" id="school" name="school" type="text" required></div>
          <div class="f"><label for="school_phone">School / College phone <span class="ta">பள்ளி தொலைபேசி எண்</span> <span class="req">*</span></label><input class="in" id="school_phone" name="school_phone" type="tel" required></div>
        </div>
        <div class="f"><label for="school_addr">School / College address <span class="ta">பள்ளி முகவரி</span> <span class="req">*</span></label><textarea class="in" id="school_addr" name="school_address" required></textarea></div>
        <div class="row">
          <div class="f"><label for="hm">Headmaster / Principal name <span class="ta">தலைமை ஆசிரியர் பெயர்</span> <span class="req">*</span></label><input class="in" id="hm" name="hm_name" type="text" required></div>
          <div class="f"><label for="type">Type of institution <span class="ta">நிறுவன வகை</span> <span class="req">*</span></label><select class="in" id="type" name="inst_type" required><option value="">Select…</option><option>Government</option><option>Government-aided</option><option>Private / Matriculation</option><option>College / University</option><option>Polytechnic / ITI</option></select></div>
        </div>
        <div class="f">
          <label>Summary of marks <span class="ta">மதிப்பெண்கள் விவரம்</span></label>
          <table class="marks" aria-label="Marks summary">
            <thead><tr><th>Subject <span class="ta">பாடம்</span></th><th>Quarterly <span class="ta">காலிறுதி</span></th><th>Half-yearly <span class="ta">அரையிறுதி</span></th><th>Annual, last year % <span class="ta">முழு ஆண்டு</span></th></tr></thead>
            <tbody>
              <tr><td><input class="in" type="text" name="sub1" placeholder="Subject 1" aria-label="Subject 1" required></td><td><input class="in" type="number" name="q1" aria-label="Quarterly 1" min="0" max="100" required></td><td><input class="in" type="number" name="h1" aria-label="Half-yearly 1" min="0" max="100" required></td><td><input class="in" type="number" name="a1" aria-label="Annual 1" min="0" max="100" required></td></tr>
              <tr><td><input class="in" type="text" name="sub2" placeholder="Subject 2" aria-label="Subject 2" required></td><td><input class="in" type="number" name="q2" aria-label="Quarterly 2" min="0" max="100" required></td><td><input class="in" type="number" name="h2" aria-label="Half-yearly 2" min="0" max="100" required></td><td><input class="in" type="number" name="a2" aria-label="Annual 2" min="0" max="100" required></td></tr>
              <tr><td><input class="in" type="text" name="sub3" placeholder="Subject 3" aria-label="Subject 3" required></td><td><input class="in" type="number" name="q3" aria-label="Quarterly 3" min="0" max="100" required></td><td><input class="in" type="number" name="h3" aria-label="Half-yearly 3" min="0" max="100" required></td><td><input class="in" type="number" name="a3" aria-label="Annual 3" min="0" max="100" required></td></tr>
              <tr><td><input class="in" type="text" name="sub4" placeholder="Subject 4" aria-label="Subject 4"></td><td><input class="in" type="number" name="q4" aria-label="Quarterly 4"></td><td><input class="in" type="number" name="h4" aria-label="Half-yearly 4"></td><td><input class="in" type="number" name="a4" aria-label="Annual 4"></td></tr>
              <tr><td><input class="in" type="text" name="sub5" placeholder="Subject 5" aria-label="Subject 5"></td><td><input class="in" type="number" name="q5" aria-label="Quarterly 5"></td><td><input class="in" type="number" name="h5" aria-label="Half-yearly 5"></td><td><input class="in" type="number" name="a5" aria-label="Annual 5"></td></tr>
              <tr><td><strong style="padding: 0 12px;">Total % <span class="ta">மொத்தம்</span> <span class="req">*</span></strong></td><td><input class="in" type="number" name="qt" aria-label="Quarterly total" min="0" max="100" required style="background: var(--cream);"></td><td><input class="in" type="number" name="ht" aria-label="Half-yearly total" min="0" max="100" required style="background: var(--cream);"></td><td><input class="in" type="number" name="at" aria-label="Annual total" min="0" max="100" required style="background: var(--cream);"></td></tr>
            </tbody>
          </table>
          <a href="#step3" style="font-size: 14px; font-weight: 700; align-self: flex-start;">+ Add subject</a>
        </div>
        <div class="row3">
          <div class="f"><label>Passed all subjects? <span class="ta">அனைத்தும் தேர்ச்சி?</span> <span class="req">*</span></label><div style="display: flex; gap: 10px;"><label class="chk"><input type="radio" name="passed" value="yes" required>Yes <span class="ta">ஆம்</span></label><label class="chk"><input type="radio" name="passed" value="no" required>No <span class="ta">இல்லை</span></label></div></div>
          <div class="f"><label for="rank">Rank in class <span class="ta">தகுதிநிலை</span> <span class="req">*</span></label><input class="in" id="rank" name="rank" type="number" min="1" required></div>
          <div class="f"><label for="strength">Class strength <span class="ta">மாணவர் எண்ணிக்கை</span> <span class="req">*</span></label><input class="in" id="strength" name="strength" type="number" min="1" required></div>
        </div>
        <div class="f"><label for="awards">Awards / achievements <span class="ta">விருதுகள் · சாதனைகள்</span></label><textarea class="in" id="awards" name="awards" placeholder="Prizes, sports, competitions, NCC / NSS…"></textarea></div>
        <div class="row">
          <label for="marksheet" class="up"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path></svg><span><strong>Upload last year's mark sheet</strong>PDF or image · under <?= h((string) $maxDocMb) ?> MB</span><input id="marksheet" name="marksheet" type="file" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
          <label for="certs" class="up"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4L12 14.3 7.2 16.9l.9-5.4L4.2 7.7l5.4-.8z"></path></svg><span><strong>Upload award certificates (optional)</strong>Add one at a time or several together · up to 5 files · PDF or image · under <?= h((string) $maxDocMb) ?> MB each</span><input id="certs" name="certs[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" data-max-files="5" style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
        </div>
      </div>

      <!-- Step 4 -->
      <div class="step" id="step4">
        <div class="sh"><span class="sn">4</span><div><h2 style="font-size: 28px;">Funding details and declaration</h2><span class="ta">நிதி விவரம் &amp; உறுதிமொழி</span></div></div>
        <div class="row">
          <div class="f"><label>Applied to the trust before? <span class="ta">முன்பு விண்ணப்பித்துள்ளீர்களா?</span> <span class="req">*</span></label><div style="display: flex; gap: 10px;"><label class="chk"><input type="radio" name="applied" value="yes" required>Yes <span class="ta">ஆம்</span></label><label class="chk"><input type="radio" name="applied" value="no" required>No <span class="ta">இல்லை</span></label></div></div>
          <div class="f"><label>Received support before? <span class="ta">முன்பு உதவி பெற்றுள்ளீர்களா?</span> <span class="req">*</span></label><div style="display: flex; gap: 10px;"><label class="chk"><input type="radio" name="received" value="yes" required>Yes <span class="ta">ஆம்</span></label><label class="chk"><input type="radio" name="received" value="no" required>No <span class="ta">இல்லை</span></label></div></div>
        </div>
        <div class="f"><label for="years">If yes, which years? <span class="ta">ஆம் எனில் எந்த ஆண்டுகள்?</span></label><input class="in" id="years" name="years" type="text" placeholder="e.g. 2024, 2025"></div>
        <div class="f">
          <label>Amount required per year — breakdown <span class="ta">ஆண்டுக்குத் தேவையான தொகை · செலவினங்கள்</span> <span class="req">*</span></label>
          <table class="marks" aria-label="Fee breakdown">
            <thead><tr><th>Expense <span class="ta">செலவினம்</span></th><th style="width: 30%;">Amount (₹) <span class="ta">தொகை</span></th></tr></thead>
            <tbody>
              <tr><td><input class="in" type="text" value="School / College tuition fee" name="exp1" aria-label="Expense 1"></td><td><input class="in" type="number" name="amt1" aria-label="Amount 1"></td></tr>
              <tr><td><input class="in" type="text" value="Exam fee" name="exp2" aria-label="Expense 2"></td><td><input class="in" type="number" name="amt2" aria-label="Amount 2"></td></tr>
              <tr><td><input class="in" type="text" placeholder="Books, hostel, transport…" name="exp3" aria-label="Expense 3"></td><td><input class="in" type="number" name="amt3" aria-label="Amount 3"></td></tr>
              <tr><td><strong style="padding: 0 12px;">Total fees <span class="ta">மொத்த கட்டணம்</span></strong></td><td><input class="in" type="number" name="total" aria-label="Total fees" min="0" step="1" required style="background: var(--cream); font-weight: 700;"></td></tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <label for="feedemand" class="up"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path></svg><span><strong>Upload fee demand / receipt</strong>From the school or college · PDF or image · under <?= h((string) $maxDocMb) ?> MB</span><input id="feedemand" name="feedemand" type="file" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
          <label for="income" class="up"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path></svg><span><strong>Upload income certificate</strong>Issued by the Tahsildar / VAO · PDF or image · under <?= h((string) $maxDocMb) ?> MB</span><input id="income" name="income" type="file" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
        </div>

        <div style="background: var(--cream); border-radius: 20px; padding: 24px 28px; display: flex; flex-direction: column; gap: 16px;">
          <strong style="font-size: 16px;">Declaration <span class="ta">உறுதிமொழி</span></strong>
          <label style="display: flex; gap: 12px; align-items: flex-start; font-size: 15px; line-height: 1.6; cursor: pointer;"><input type="checkbox" name="declare" value="yes" required style="width: 20px; height: 20px; margin-top: 3px; accent-color: var(--indigo);"><span>I agree to provide the information requested in this form and certify that the information provided is true and accurate. <span class="req">*</span> <span class="ta" style="display: block; margin-top: 4px;">இந்தப் படிவத்தில் நான் அளித்துள்ள விவரங்கள் அனைத்தும் உண்மையானவை என்று உறுதியளிக்கிறேன்.</span></span></label>
          <div class="row">
            <div class="f"><label for="parent">Name of parent / guardian <span class="ta">பெற்றோர் / பாதுகாவலர் பெயர்</span> <span class="req">*</span></label><input class="in" id="parent" name="parent_name" type="text" required></div>
            <div class="f"><label for="pdate">Date <span class="ta">தேதி</span> <span class="req">*</span></label><input class="in" id="pdate" name="date" type="date" required></div>
          </div>
        </div>

        <div style="background: var(--lilac); border-radius: 20px; padding: 24px 28px; display: flex; flex-direction: column; gap: 14px;">
          <strong style="font-size: 16px;">Bonafide certificate from the school / college <span class="ta">பள்ளி / கல்லூரி படிப்புச் சான்றிதழ்</span> <span class="req">*</span></strong>
          <p style="margin: 0; font-size: 14px; line-height: 1.6; color: var(--muted);">Obtain a bonafide certificate from your school or college, with the seal <span class="ta">(முத்திரை)</span> and signature of the Headmaster / Principal, and upload a scan or clear photo of it here. Applications without a bonafide certificate cannot be considered.</p>
          <label for="bonafide" class="up" style="background: #fff;"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><path d="M12 2l7 4v6c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z"></path></svg><span><strong>Upload bonafide certificate <span class="req">*</span></strong>With seal and signature of the Headmaster / Principal · PDF or image · under <?= h((string) $maxDocMb) ?> MB</span><input id="bonafide" name="bonafide" type="file" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="<?= h((string) $maxDocMb) ?>" required style="position: absolute; width: 1px; height: 1px; opacity: 0;"></label>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; padding-top: 8px;">
          <span style="font-size: 13px; color: var(--muted);"><span class="req">*</span> Required fields. You will receive a reference number and a copy of your application by email or WhatsApp.</span>
          <button type="submit" class="btn btn-indigo" id="submitbtn">Submit application<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></button>
        </div>
      </div>

    </form>
  </section>

  <!-- Help: small, left-aligned, above the footer -->
  <section style="padding: 40px 0 56px;">
    <div class="wrap" style="display: flex; flex-direction: column; align-items: flex-start; gap: 10px;">
      <span class="btn btn-marigold" style="min-height: 44px; padding: 10px 18px; font-size: 14px; cursor: default;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M9.5 9.5a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5M12 17h.01"></path></svg>Need help?</span>
      <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px 20px; font-size: 14px; color: var(--muted);">
        <span>Facing an issue with the form?</span>
        <a href="tel:+919176189469" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--ink); min-height: 36px;"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6.2 6.2l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"></path></svg>Call +91 9176189469</a>
        <a href="https://wa.me/919176189469" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--ink); min-height: 36px;"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.5L3 21l2-5.5A8.5 8.5 0 1 1 21 11.5z"></path></svg>WhatsApp</a>
        <a href="mailto:umalakshmieducationaltrust@gmail.com" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--ink); min-height: 36px;"><svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>Mail us</a>
      </div>
    </div>
  </section>

<?php if ($flash && ($flash['type'] ?? '') === 'success'): ?>
<div id="okmodal" role="dialog" aria-modal="true" aria-labelledby="okttl" style="position: fixed; inset: 0; z-index: 200; background: rgba(18,26,82,0.72); display: flex; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; border-radius: 28px; padding: 40px 36px; max-width: 520px; width: 100%; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 14px; box-shadow: 0 30px 80px rgba(20,22,51,0.35);">
    <span style="width: 72px; height: 72px; border-radius: 999px; background: var(--marigold); display: inline-flex; align-items: center; justify-content: center;"><svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--marigold-ink)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg></span>
    <h2 id="okttl" style="font-size: 28px;">Application submitted successfully</h2>
    <p style="margin: 0; font-size: 16px; line-height: 1.65; color: var(--ink);">Your reference number is <strong style="font-family: var(--display); font-size: 20px; color: var(--indigo); letter-spacing: 0.04em;"><?= h($flash['ref'] ?? '') ?></strong>.</p>
    <p style="margin: 0; font-size: 15px; line-height: 1.65; color: var(--muted);">A copy of your application has been mailed to <strong style="color: var(--ink);"><?= h($flash['email'] ?? '') ?></strong>. Please keep the reference number for any correspondence with the trust.</p>
    <span class="ta" style="font-size: 14px;">உங்கள் விண்ணப்பம் வெற்றிகரமாகச் சமர்ப்பிக்கப்பட்டது. விண்ணப்பத்தின் நகல் உங்கள் மின்னஞ்சலுக்கு அனுப்பப்பட்டுள்ளது.</span>
    <a href="index.php" class="btn btn-indigo" style="margin-top: 8px;">Back to home</a>
  </div>
</div>
<?php endif; ?>
<script>
(function(){
  var f=document.getElementById('applyform'), b=document.getElementById('submitbtn');
  if(f&&b){ f.addEventListener('submit',function(){ if(!f.checkValidity||f.checkValidity()){ b.disabled=true; b.firstChild.textContent='Submitting… '; } }); }
  var e=document.getElementById('form-errors'); if(e){ e.scrollIntoView({block:'center'}); }
  var m=document.getElementById('okmodal'); if(m){ document.addEventListener('keydown',function(ev){ if(ev.key==='Escape') m.remove(); }); }
  // Upload tiles: validate size/count, show previews and file lists, allow removing a file
  var MB=1048576, TOTAL_MAX_MB=40;
  function fmt(b){ return b>=MB ? (b/MB).toFixed(1)+' MB' : Math.max(1,Math.round(b/1024))+' KB'; }
  [].forEach.call(document.querySelectorAll('input[type=file]'),function(inp){
    var tile=document.querySelector('label.up[for="'+inp.id+'"]'); if(!tile) return;
    var isPhoto=tile.classList.contains('up-photo');
    var maxMb=parseFloat(inp.getAttribute('data-max-mb'))||5, maxFiles=parseInt(inp.getAttribute('data-max-files'),10)||1;
    var wrap=null, list=null, err=document.createElement('div'); err.className='uperr';
    if(isPhoto){ var ok=document.createElement('span'); ok.className='up-ok'; ok.textContent='✓ Photo added · tap to change'; tile.appendChild(ok); tile.parentNode.appendChild(err); }
    else { wrap=document.createElement('div'); wrap.className='upwrap'; tile.parentNode.insertBefore(wrap,tile); wrap.appendChild(tile); list=document.createElement('ul'); list.className='uplist'; wrap.appendChild(list); wrap.appendChild(err); }
    function render(){
      var files=[].slice.call(inp.files||[]), has=files.length>0;
      tile.classList.toggle('has-file',has);
      if(isPhoto){
        var old=tile.querySelector('img.up-prev'); if(old){ URL.revokeObjectURL(old.src); old.remove(); }
        if(has && /^image\//.test(files[0].type)){ var img=document.createElement('img'); img.className='up-prev'; img.alt=''; img.src=URL.createObjectURL(files[0]); tile.insertBefore(img,tile.firstChild); }
      } else {
        list.innerHTML='';
        files.forEach(function(f,i){ var li=document.createElement('li'); var sp=document.createElement('span'); sp.textContent=f.name; var sm=document.createElement('small'); sm.textContent=fmt(f.size); var b=document.createElement('button'); b.type='button'; b.setAttribute('aria-label','Remove '+f.name); b.textContent='×'; b.setAttribute('data-i',i); li.appendChild(sp); li.appendChild(sm); li.appendChild(b); list.appendChild(li); });
      }
    }
    var kept=[]; // multi-file inputs: files chosen earlier, so a second pick adds to them
    function setFiles(files){ try{ var dt=new DataTransfer(); files.forEach(function(f){ dt.items.add(f); }); inp.files=dt.files; }catch(e){ if(!files.length) inp.value=''; } }
    inp.addEventListener('change',function(){
      err.textContent='';
      var picked=[].slice.call(inp.files||[]), files=picked, bad='';
      if(maxFiles>1){
        files=kept.slice();
        picked.forEach(function(f){ var dup=files.some(function(k){ return k.name===f.name&&k.size===f.size; }); if(!dup) files.push(f); });
      }
      if(files.length>maxFiles) bad='You can attach at most '+maxFiles+' files. Remove one before adding another.';
      files.forEach(function(f){ if(!bad && f.size>maxMb*MB) bad='"'+f.name+'" is '+fmt(f.size)+'. Each file must be under '+maxMb+' MB.'; });
      if(bad){ err.textContent=bad; files=(maxFiles>1)?kept.slice():[]; }
      if(maxFiles>1){ kept=files; setFiles(files); } else if(bad){ inp.value=''; }
      render();
    });
    if(list){ list.addEventListener('click',function(ev){ var b=ev.target.closest('button[data-i]'); if(!b) return; var i=parseInt(b.getAttribute('data-i'),10);
      var rest=[].filter.call(inp.files,function(f,j){ return j!==i; }); if(maxFiles>1) kept=rest; setFiles(rest); err.textContent=''; render(); }); }
  });
  if(f){ f.addEventListener('submit',function(ev){ var total=0; [].forEach.call(f.querySelectorAll('input[type=file]'),function(i){ [].forEach.call(i.files||[],function(x){ total+=x.size; }); });
    if(total>TOTAL_MAX_MB*MB){ ev.preventDefault(); alert('The attachments together are '+fmt(total)+'. Please keep the total under '+TOTAL_MAX_MB+' MB.'); if(b){ b.disabled=false; b.firstChild.textContent='Submit application'; } } }); }
})();
</script>
<?php require __DIR__ . '/inc/footer.php'; ?>
