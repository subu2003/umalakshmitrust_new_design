<?php
/** Shared HTML email frame. Variables: $title, $body (HTML), $preheader (optional). */
$title = $title ?? 'Umalakshmi Educational Trust';
$preheader = $preheader ?? '';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title><?= htmlspecialchars($title) ?></title></head>
<body style="margin:0;padding:0;background:#EEF0FF;font-family:'DM Sans',Helvetica,Arial,sans-serif;color:#141633;">
<?php if ($preheader !== ''): ?><span style="display:none;max-height:0;overflow:hidden;color:#EEF0FF;"><?= htmlspecialchars($preheader) ?></span><?php endif; ?>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#EEF0FF;padding:24px 12px;"><tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;">
  <tr><td style="background:#1E2A78;padding:22px 28px;">
    <table role="presentation" cellspacing="0" cellpadding="0"><tr>
      <td style="vertical-align:middle;padding-right:12px;"><img src="cid:uetlogo" width="44" height="45" alt="" style="display:block;background:#fff;border-radius:10px;padding:3px;"></td>
      <td style="vertical-align:middle;"><div style="font-size:20px;font-weight:800;color:#ffffff;line-height:1.1;">Umalakshmi</div><div style="font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#FFB300;">Educational Trust</div></td>
    </tr></table>
  </td></tr>
  <tr><td style="padding:28px 28px 8px 28px;font-size:15px;line-height:1.65;"><?= $body ?></td></tr>
  <tr><td style="padding:16px 28px 26px 28px;font-size:12px;line-height:1.6;color:#5B5E7A;border-top:1px solid #E6E7F2;">
    Uma Lakshmi Educational Trust · 3A, Palazzo Apartment, Ram Kamal Building, Shasthiri Nagar First Street, Adyar, Chennai 600020<br>
    +91 9176189469 · umalakshmieducationaltrust@gmail.com · www.umalakshmitrust.com
  </td></tr>
</table>
</td></tr></table>
</body></html>
