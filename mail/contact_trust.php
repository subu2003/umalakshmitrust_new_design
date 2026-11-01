<?php
/** Contact-form message forwarded to the trust. Variables: $name, $email, $phone, $subject, $message, $received, $id */
ob_start(); ?>
<h1 style="margin:0 0 6px;font-size:22px;line-height:1.2;color:#141633;">New message from the website</h1>
<p style="margin:0 0 18px;color:#5B5E7A;">Received <?= htmlspecialchars($received) ?> · Contact form · #<?= (int) $id ?></p>
<table role="presentation" cellspacing="0" cellpadding="0" style="font-size:15px;border-collapse:collapse;margin-bottom:18px;">
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">From</td><td style="padding:6px 0;"><strong><?= htmlspecialchars($name) ?></strong></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Email</td><td style="padding:6px 0;"><a href="mailto:<?= htmlspecialchars($email) ?>" style="color:#1E2A78;"><?= htmlspecialchars($email) ?></a></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Phone</td><td style="padding:6px 0;"><?= $phone !== '' ? htmlspecialchars($phone) : '-' ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">About</td><td style="padding:6px 0;"><?= $subject !== '' ? htmlspecialchars($subject) : 'General enquiry' ?></td></tr>
</table>
<div style="background:#EEF0FF;border-radius:14px;padding:18px 20px;font-size:15px;line-height:1.7;color:#141633;white-space:pre-wrap;"><?= htmlspecialchars($message) ?></div>
<p style="margin:18px 0 0;font-size:13px;color:#5B5E7A;">Reply to this email to answer <?= htmlspecialchars($name) ?> directly.</p>
<?php $body = ob_get_clean(); $title = 'Website message from ' . $name; $preheader = mb_substr($message, 0, 90);
include __DIR__ . '/_layout.php';
