<?php
/** "After you pay" details forwarded to the trust. */
ob_start(); ?>
<h1 style="margin:0 0 6px;font-size:22px;line-height:1.2;color:#141633;">Donation reported on the website</h1>
<p style="margin:0 0 18px;color:#5B5E7A;">Received <?= htmlspecialchars($received) ?> · Donation #<?= (int) $id ?> · <strong style="color:#3D2B00;">pending verification</strong></p>
<table role="presentation" cellspacing="0" cellpadding="0" style="font-size:15px;border-collapse:collapse;margin-bottom:16px;">
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Amount</td><td style="padding:6px 0;"><strong style="font-size:18px;">&#8377; <?= number_format($amount, 2) ?></strong></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Donor</td><td style="padding:6px 0;"><strong><?= htmlspecialchars($name) ?></strong></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Mobile</td><td style="padding:6px 0;"><?= htmlspecialchars($phone) ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Email</td><td style="padding:6px 0;"><a href="mailto:<?= htmlspecialchars($email) ?>" style="color:#1E2A78;"><?= htmlspecialchars($email) ?></a></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">PAN</td><td style="padding:6px 0;"><?= $pan !== '' ? htmlspecialchars($pan) : '<em style="color:#5B5E7A;">not given</em>' ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Address</td><td style="padding:6px 0;"><?= $address !== '' ? nl2br(htmlspecialchars($address)) : '-' ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">UPI / UTR ref</td><td style="padding:6px 0;"><?= $txnRef !== '' ? '<code>' . htmlspecialchars($txnRef) . '</code>' : '-' ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">Screenshot</td><td style="padding:6px 0;"><?= $hasShot ? 'attached' : 'none' ?></td></tr>
  <tr><td style="padding:6px 16px 6px 0;color:#5B5E7A;">List on Donors page</td><td style="padding:6px 0;"><?= $listOnSite ? 'Yes' : 'No' ?></td></tr>
</table>
<?php if ($intentState === 'valid'): ?>
<div style="background:#EEF0FF;border-radius:14px;padding:14px 18px;font-size:14px;line-height:1.6;">
  QR reference <strong><?= htmlspecialchars($intentRef) ?></strong> was generated on the page for <strong>&#8377; <?= number_format((float) $intentAmount, 2) ?></strong>.
  <?php if (abs((float) $intentAmount - (float) $amount) > 0.009): ?><br><strong style="color:#B3261E;">Note:</strong> the donor reports a different amount than the QR was made for — check the bank entry.<?php else: ?><br>Reported amount matches the QR amount. Look for the note "Donation <?= htmlspecialchars($intentRef) ?>" in the bank statement.<?php endif; ?>
</div>
<?php elseif ($intentState === 'invalid'): ?>
<div style="background:#FFE9EA;border-radius:14px;padding:14px 18px;font-size:14px;line-height:1.6;"><strong style="color:#B3261E;">Warning:</strong> the QR reference sent with this form did not pass the signature check (stale or edited page). Treat the amount as unverified and match it with the bank statement.</div>
<?php else: ?>
<div style="background:#FFF6DC;border-radius:14px;padding:14px 18px;font-size:14px;line-height:1.6;">No QR reference: the donor probably paid by bank transfer or from a saved UPI ID. Match the UTR / amount with the bank statement.</div>
<?php endif; ?>
<p style="margin:18px 0 0;font-size:13px;color:#5B5E7A;">Once the credit is confirmed, mark the donation verified and issue the 80G receipt to <?= htmlspecialchars($email) ?>. Reply to this email to contact the donor.</p>
<?php $body = ob_get_clean(); $title = 'Donation reported: Rs ' . number_format($amount) . ' by ' . $name; $preheader = $name . ' · Rs ' . number_format($amount) . ($intentRef ? ' · ' . $intentRef : '');
include __DIR__ . '/_layout.php';
