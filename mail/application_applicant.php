<?php
/** Confirmation to the applicant. Variables: $ref, $name, $year, $submitted, $separateCount */
ob_start(); ?>
<h1 style="margin:0 0 12px;font-size:24px;line-height:1.2;color:#141633;">Your application has been received</h1>
<p style="margin:0 0 14px;">Dear <?= htmlspecialchars($name) ?>,</p>
<p style="margin:0 0 14px;">Thank you for applying to the Uma Lakshmi Educational Trust for the academic year <?= htmlspecialchars($year) ?>. Your application was received on <?= htmlspecialchars($submitted) ?>.</p>
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 18px;background:#FFF6DC;border-radius:14px;"><tr><td style="padding:14px 18px;">
  <div style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#3D2B00;">Reference number</div>
  <div style="font-size:24px;font-weight:800;color:#1E2A78;letter-spacing:.04em;"><?= htmlspecialchars($ref) ?></div>
</td></tr></table>
<p style="margin:0 0 14px;">A PDF copy of your completed application form is attached to this email<?= $separateCount > 0 ? ', together with ' . (int) $separateCount . ' of your uploaded document' . ($separateCount > 1 ? 's' : '') . ' that could not be merged into it' : ', including the documents you uploaded' ?>. Please keep it safe and quote the reference number in any correspondence.</p>
<p style="margin:0 0 14px;"><strong>What happens next</strong><br>The trustees review every application on need and merit. Selected students are informed by phone or email before the annual sponsorship function, and fees are paid directly to the school or college.</p>
<p style="margin:0 0 6px;">With best wishes for your studies,<br><strong>Umalakshmi Educational Trust</strong></p>
<p style="margin:0;font-size:13px;color:#5B5E7A;">Questions? Reply to this email or WhatsApp +91 9176189469.</p>
<?php $body = ob_get_clean(); $title = 'Application received - ' . $ref; $preheader = 'Reference ' . $ref . '. Your application form is attached.';
include __DIR__ . '/_layout.php';
