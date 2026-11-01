<?php
/** Notification to the trust inbox. Variables: $ref, $app (array), $files (list), $separateCount */
$a = $app['applicant']; $e = $app['education']; $f = $app['funding'];
ob_start(); ?>
<h1 style="margin:0 0 12px;font-size:22px;line-height:1.2;color:#141633;">New scholarship application <?= htmlspecialchars($ref) ?></h1>
<p style="margin:0 0 14px;color:#5B5E7A;">Submitted <?= htmlspecialchars($app['submitted_at']) ?> · Academic year <?= htmlspecialchars($app['academic_year']) ?> · IP <?= htmlspecialchars($app['ip']) ?></p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;border-collapse:collapse;">
<?php
$rows = [
  'Applicant'       => mb_strtoupper($a['name']) . ' (' . ucfirst($a['gender']) . ', born ' . $a['dob_display'] . ')',
  'Standard'        => trim($a['standard'] . ' ' . $a['course']),
  'Phone / WhatsApp'=> $a['phone'] . ($a['whatsapp'] !== '' ? ' / ' . $a['whatsapp'] : ''),
  'Email'           => $a['email'],
  'Address'         => $a['address'],
  'Institution'     => trim($e['institution'] . ' ' . ($e['inst_type'] !== '' ? '(' . $e['inst_type'] . ')' : '')),
  'Headmaster'      => $e['hm_name'] . ($e['phone'] !== '' ? ' · ' . $e['phone'] : ''),
  'Total fees'      => $f['total_display'] !== '' ? 'Rs. ' . $f['total_display'] : '-',
  'Applied before'  => ucfirst($f['applied_before'] ?: '-') . ' · Received before: ' . ucfirst($f['received_before'] ?: '-') . ($f['years'] !== '' ? ' (' . $f['years'] . ')' : ''),
  'Parent / guardian' => $app['declaration']['parent_name'],
];
foreach ($rows as $k => $v): ?>
  <tr><td style="padding:7px 10px 7px 0;color:#5B5E7A;white-space:nowrap;vertical-align:top;border-top:1px solid #E6E7F2;"><?= htmlspecialchars($k) ?></td><td style="padding:7px 0;vertical-align:top;border-top:1px solid #E6E7F2;"><?= nl2br(htmlspecialchars($v === '' ? '-' : $v)) ?></td></tr>
<?php endforeach; ?>
</table>
<p style="margin:18px 0 6px;"><strong>Documents</strong></p>
<ul style="margin:0 0 14px;padding-left:18px;font-size:14px;">
<?php foreach ($files as $fl): if ($fl['kind'] === 'application_pdf') continue; ?>
  <li><?= htmlspecialchars($fl['label']) ?> — <?= htmlspecialchars($fl['name']) ?> (<?= number_format($fl['size'] / 1024) ?> KB)</li>
<?php endforeach; ?>
</ul>
<p style="margin:0 0 14px;">The complete application PDF (form + documents) is attached<?= $separateCount > 0 ? '; ' . (int) $separateCount . ' uploaded PDF' . ($separateCount > 1 ? 's' : '') . ' could not be merged and ' . ($separateCount > 1 ? 'are' : 'is') . ' attached separately' : '' ?>. The originals are stored on the server under <code><?= htmlspecialchars($ref) ?></code>.</p>
<?php $body = ob_get_clean(); $title = 'New application ' . $ref; $preheader = mb_strtoupper($a['name']) . ' · ' . $a['standard'] . ' · ' . $e['institution'];
include __DIR__ . '/_layout.php';
