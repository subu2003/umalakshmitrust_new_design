<?php
/**
 * Builds the PDF copy of a scholarship application.
 *
 * Pages 1-2: the trust's own blank application form (inc/templates/application-form.pdf)
 * is used as the page background and the submitted answers are typed into its boxes,
 * the photo placed in the photo box and the printed checkboxes ticked. The Principal's
 * attestation block is replaced by a bonafide-certificate note.
 * Pages 3+: every uploaded document — images placed one per page; PDFs merged with FPDI
 * when its free parser can read them, otherwise listed and returned in 'separate'.
 *
 *   $r = application_pdf_build($app, $files, $outPath);
 *   // ['path' => $outPath, 'separate' => [files that could not be merged]]
 *
 * $app is the array assembled by apply_process.php; $files is a list of
 * ['kind','label','path','mime','name','size'].
 * Values are written with the PDF core fonts (Latin only): Tamil characters typed into
 * the web form come out as '?'. All coordinates below are millimetres from the top-left
 * of an A4 page and were measured from the template.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once dirname(__DIR__) . '/lib/fpdf/fpdf.php';
require_once dirname(__DIR__) . '/lib/fpdi/src/autoload.php';

use setasign\Fpdi\Fpdi;

final class ApplicationPdf extends Fpdi
{
    public const TEMPLATE = __DIR__ . '/templates/application-form.pdf';

    /** UTF-8 → cp1252 for the core fonts. */
    public static function t(?string $s): string
    {
        $s = str_replace(["\u{20B9}", "\u{2013}", "\u{2014}", "\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{00A0}", "\u{2026}"], ['Rs. ', '-', '-', "'", "'", '"', '"', ' ', '...'], (string) $s);
        $o = @iconv('UTF-8', 'windows-1252//TRANSLIT', $s);
        if ($o === false) {
            $o = @iconv('UTF-8', 'windows-1252//IGNORE', $s);
        }
        return $o === false ? (preg_replace('/[^\x20-\x7E]/', '?', $s) ?? '') : $o;
    }

    /** One line inside a box (x,y top-left; w,h size), vertically centred; font shrinks to fit, never below 6 pt. */
    public function val(float $x, float $y, float $w, float $h, ?string $txt, float $size = 9.5, string $align = 'L', string $style = ''): void
    {
        $txt = self::t($txt);
        if ($txt === '') {
            return;
        }
        $this->SetFont('Helvetica', $style, $size);
        while ($size > 6 && $this->GetStringWidth($txt) > $w - 2) {
            $size -= 0.5;
            $this->SetFont('Helvetica', $style, $size);
        }
        if ($this->GetStringWidth($txt) > $w - 2) { // still too long at 6 pt: cut
            while ($txt !== '' && $this->GetStringWidth($txt . '...') > $w - 2) {
                $txt = substr($txt, 0, -1);
            }
            $txt .= '...';
        }
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($x + 1, $y);
        $this->Cell($w - 2, $h, $txt, 0, 0, $align);
    }

    /** Several lines inside a box, top aligned, clipped to the box height. */
    public function para(float $x, float $y, float $w, float $h, ?string $txt, float $size = 8.5, float $lh = 4.0): void
    {
        $txt = self::t($txt);
        if (trim($txt) === '') {
            return;
        }
        $this->SetFont('Helvetica', '', $size);
        $this->SetTextColor(0, 0, 0);
        $maxLines = max(1, (int) floor(($h - 1) / $lh));
        $lines = $this->wrapLines($txt, $w - 2);
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] = rtrim(substr($lines[$maxLines - 1], 0, -3)) . '...';
        }
        $this->SetXY($x + 1, $y + 0.8);
        foreach ($lines as $l) {
            $this->SetX($x + 1);
            $this->Cell($w - 2, $lh, $l, 0, 2, 'L');
        }
    }

    private function wrapLines(string $txt, float $w): array
    {
        $out = [];
        foreach (explode("\n", $txt) as $parag) {
            $line = '';
            foreach (preg_split('/\s+/', trim($parag)) ?: [] as $wd) {
                $try = $line === '' ? $wd : $line . ' ' . $wd;
                if ($this->GetStringWidth($try) <= $w) {
                    $line = $try;
                } else {
                    if ($line !== '') {
                        $out[] = $line;
                    }
                    $line = $wd;
                }
            }
            $out[] = $line;
        }
        return $out;
    }

    /** Tick a printed checkbox whose square's top-left corner is at (x,y). */
    public function tick(float $x, float $y): void
    {
        $this->SetFont('ZapfDingbats', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->Text($x - 0.1, $y + 2.7, '4');
    }

    public function white(float $x, float $y, float $w, float $h): void
    {
        $this->SetFillColor(255, 255, 255);
        $this->Rect($x, $y, $w, $h, 'F');
    }

    /** Place an image scaled to fit inside a box, centred. */
    public function imageFit(string $path, float $x, float $y, float $w, float $h, float $pad = 0.6): void
    {
        $info = @getimagesize($path);
        if (!$info) {
            return;
        }
        [$iw, $ih] = $info;
        $s  = min(($w - 2 * $pad) / $iw, ($h - 2 * $pad) / $ih);
        $pw = $iw * $s;
        $ph = $ih * $s;
        $this->Image($path, $x + ($w - $pw) / 2, $y + ($h - $ph) / 2, $pw, $ph);
    }
}

/** "2026-27" → ["2026 - 2027", "2026-2027"]. */
function application_pdf_year_labels(string $academicYear): array
{
    if (preg_match('/^(\d{4})\D+(\d{2,4})$/', $academicYear, $m)) {
        $a = $m[1];
        $b = strlen($m[2]) === 2 ? substr($a, 0, 2) . $m[2] : $m[2];
        return [$a . ' - ' . $b, $a . '-' . $b];
    }
    return [$academicYear, $academicYear];
}

function application_pdf_phone(string $digits): string
{
    return strlen($digits) === 10 ? substr($digits, 0, 5) . ' ' . substr($digits, 5) : $digits;
}

function application_pdf_build(array $app, array $files, string $outPath): array
{
    if (!is_file(ApplicationPdf::TEMPLATE)) {
        throw new RuntimeException('Application form template missing: ' . ApplicationPdf::TEMPLATE);
    }
    $a = $app['applicant'];
    $d = $app['details'];
    $e = $app['education'];
    $f = $app['funding'];
    $dec = $app['declaration'];
    [$yearEn, $yearTa] = application_pdf_year_labels((string) $app['academic_year']);

    $pdf = new ApplicationPdf('P', 'mm', 'A4');
    $pdf->SetAutoPageBreak(false);
    $pdf->SetTitle(ApplicationPdf::t('Application ' . $app['ref_no'] . ' - ' . $a['name']));
    $pdf->SetAuthor('Umalakshmi Educational Trust');
    $pdf->setSourceFile(ApplicationPdf::TEMPLATE);

    // ---------------------------------------------------------------- page 1
    $pdf->AddPage();
    $pdf->useTemplate($pdf->importPage(1), 0, 0, 210, 297, true);

    // Title: rewrite the English line with the current year (also fixes the printed 'FNANCIAL'); year in the Tamil line
    $pdf->white(53, 41.5, 103, 4.6);
    $pdf->SetFont('Times', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(53, 41.6);
    $pdf->Cell(103, 4.4, ApplicationPdf::t('EDUCATIONAL FINANCIAL AID APPLICATION FORM ' . $yearEn), 0, 0, 'C');
    $pdf->white(145.5, 45.6, 17, 5.2);
    $pdf->SetXY(145.5, 45.9);
    $pdf->Cell(17, 4.4, ApplicationPdf::t($yearTa), 0, 0, 'L');
    // reference + submission
    $pdf->SetFont('Helvetica', '', 8);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->SetXY(10.8, 51.3);
    $pdf->Cell(166, 4, ApplicationPdf::t('Reference No. ' . $app['ref_no'] . '    ·    Submitted online on ' . $app['submitted_at']), 0, 0, 'R');
    // photo in the "Affix photo here" box
    foreach ($files as $fl) {
        if ($fl['kind'] === 'photo' && in_array($fl['mime'], ['image/jpeg', 'image/png'], true)) {
            $pdf->white(179, 34.3, 19.7, 20.7);
            $pdf->imageFit($fl['path'], 178.5, 33.8, 20.7, 21.7);
            break;
        }
    }

    // 1. Applicant
    $pdf->val(58, 63.0, 46, 7.2, mb_strtoupper($a['name']), 9.5, 'L', 'B');
    if ($a['gender'] === 'male' || $a['gender'] === 'female') {
        $pdf->tick($a['gender'] === 'male' ? 110.1 : 133.5, 64.4);
    }
    $pdf->val(57, 70.2, 47, 5.0, $a['dob_display']);
    $pdf->val(141, 70.2, 57, 5.0, trim($a['standard'] . ($a['course'] !== '' ? ' (' . $a['course'] . ')' : '')));
    $pdf->val(84, 75.2, 43, 7.5, application_pdf_phone($a['phone']), 9.5);
    $pdf->val(178.5, 75.2, 20.5, 7.5, application_pdf_phone($a['whatsapp']), 8);
    $pdf->val(69, 82.3, 58, 4.5, 'Address: ' . $a['address'], 7);
    $pdf->val(130, 82.3, 68.5, 4.5, 'Email: ' . $a['email'], 7);

    // 2. Family — rows: father, mother, sibling 1-3, guardian
    $slots = ['father' => [103.1, 4.9], 'mother' => [108.0, 5.0], 'sibling1' => [113.0, 6.7], 'sibling2' => [119.7, 6.7], 'sibling3' => [126.4, 6.8], 'guardian' => [133.2, 5.0]];
    $sibN = 0;
    foreach ($app['family'] as $m) {
        $key = $m['relation'] === 'sibling' ? 'sibling' . (++$sibN) : $m['relation'];
        if (!isset($slots[$key]) || ($m['name'] === '' && $m['education'] === '' && $m['occupation'] === '' && $m['income_display'] === '')) {
            continue;
        }
        [$y, $h] = $slots[$key];
        $isSib = str_starts_with($key, 'sibling');
        $pdf->val($isSib ? 57.5 : 51.5, $y, $isSib ? 36.1 : 42.1, $h, $m['name'], 9);
        $pdf->val(93.6, $y, 35.9, $h, $m['education'], 9);
        $pdf->val(129.5, $y, 26.5, $h, $m['occupation'], 9);
        $pdf->val(156.0, $y, 43.2, $h, $m['income_display'], 9, 'R');
    }
    if ($d['other_income'] === 'yes' || $d['other_income'] === 'no') {
        $pdf->tick($d['other_income'] === 'yes' ? 126.5 : 148.9, 143.6);
    }
    $pdf->val(127, 146.3, 71, 3.8, $d['other_income_detail'], 7.5); // under the Yes/No boxes
    if ($d['house'] === 'own' || $d['house'] === 'rent') {
        $pdf->tick($d['house'] === 'own' ? 126.5 : 149.1, 151.2);
        $pdf->val(127, 155.5, 71, 5, $d['house'] === 'own' ? 'Own house' : 'Rental house', 9);
        if ($d['house'] === 'rent' && $d['rent_display'] !== '') {
            $pdf->val(127, 160.0, 71, 5, 'Rent per month: Rs. ' . $d['rent_display'], 9);
        }
    }

    // 3. Education — the first ruled line carries the printed label, so lines 2, 3 and the space below
    $pdf->val(12.2, 176.4, 76.7, 4.2, trim($e['institution'] . ($e['inst_type'] !== '' ? ' (' . $e['inst_type'] . ')' : '')), 8.5);
    $pdf->val(12.2, 181.7, 76.7, 4.2, $e['address'], 8.5);
    $pdf->val(12.2, 186.8, 76.7, 4.2, $e['hm_name'] !== '' ? 'HM / Principal: ' . $e['hm_name'] : '', 8.5);
    $pdf->val(94.5, 178.0, 82.5, 6.9, $e['phone'], 9.5);
    // marks: 8 subject rows, then the totals row
    $rowY = [211.5, 216.1, 220.8, 225.4, 230.0, 234.8, 239.2, 244.0, 248.6];
    $i = 0;
    $totals = null;
    foreach ($app['marks'] as $m) {
        if ($m['subject'] === 'Total') {
            $totals = $m;
            continue;
        }
        if ($i >= 8) {
            break;
        }
        $y = $rowY[$i];
        $h = $rowY[$i + 1] - $y;
        $pdf->val(10.8, $y, 62.5, $h, $m['subject'], 9);
        $pdf->val(73.3, $y, 42.2, $h, $m['quarterly'], 9, 'C');
        $pdf->val(115.5, $y, 39.0, $h, $m['half_yearly'], 9, 'C');
        $pdf->val(154.5, $y, 44.7, $h, $m['annual'], 9, 'C');
        $i++;
    }
    if ($totals) {
        foreach ([['quarterly', 73.3, 21.8, 95.1, 20.4], ['half_yearly', 115.5, 20.3, 135.8, 18.7], ['annual', 154.5, 20.3, 174.8, 24.4]] as [$k, $x1, $w1, $x2, $w2]) {
            if ($totals[$k] !== '') {
                $pdf->val($x1, 248.6, $w1, 9.4, $totals[$k], 9.5, 'C', 'B');
                $pdf->val($x2, 248.6, $w2, 9.4, '%', 9, 'C');
            }
        }
    }

    // ---------------------------------------------------------------- page 2
    $pdf->AddPage();
    $pdf->useTemplate($pdf->importPage(2), 0, 0, 210, 297, true);
    if ($e['passed_all'] === 'yes' || $e['passed_all'] === 'no') {
        foreach ([65.6, 112.9, 161.2] as $x) {
            $pdf->tick($x, $e['passed_all'] === 'yes' ? 44.8 : 49.0);
        }
    }
    foreach ([[57.9, 47.1], [105.0, 47.0], [152.0, 47.2]] as [$x, $w]) {
        $pdf->val($x, 66.4, $w, 9.6, $e['rank'], 9.5, 'C');
        $pdf->val($x, 76.0, $w, 9.5, $e['class_strength'], 9.5, 'C');
    }
    $pdf->para(57.9, 85.5, 141.3, 27.5, $e['awards'], 9, 4.5);
    // 4. Funding
    if ($f['applied_before'] === 'yes' || $f['applied_before'] === 'no') {
        $pdf->tick($f['applied_before'] === 'yes' ? 140.4 : 161.0, 123.8);
    }
    if ($f['received_before'] === 'yes' || $f['received_before'] === 'no') {
        $pdf->tick($f['received_before'] === 'yes' ? 140.1 : 160.9, 138.8);
    }
    $pdf->val(130.1, 152.8, 69.1, 6.4, $f['years'] !== '' ? $f['years'] : '-', 9.5);
    $lines = [];
    foreach ($app['fees'] as $fee) {
        $lines[] = $fee['label'] . ': Rs. ' . $fee['amount_display'];
    }
    $pdf->para(130.1, 159.2, 69.1, 22.4, implode("\n", $lines), 8.5, 4.2);
    $pdf->val(130.1, 181.6, 69.1, 10.4, $f['total_display'] !== '' ? 'Rs. ' . $f['total_display'] : '', 10.5, 'L', 'B');
    // Declaration / signature block
    $pdf->val(13, 221.0, 90, 4.5, $dec['agreed'] ? 'Submitted online. Declaration accepted by the parent / guardian.' : 'Submitted online.', 7.5);
    $pdf->val(33.5, 233.6, 58, 4.6, $dec['parent_name'], 9.5);
    $pdf->val(30.5, 241.7, 61, 4.6, $dec['date_display'], 9.5);
    // Replace the Principal's attestation block with the bonafide note + document list
    $pdf->white(105.3, 212.8, 93.6, 37.6);
    $pdf->SetFont('Times', 'B', 9.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(107.5, 213.8);
    $pdf->Cell(89, 4.2, 'Bonafide certificate', 0, 2, 'L');
    $pdf->SetFont('Times', '', 8.5);
    $pdf->SetX(107.5);
    $pdf->MultiCell(89, 3.7, ApplicationPdf::t('A bonafide certificate from the school / college, with the seal and signature of the Headmaster / Principal, is attached with this application (see Documents).'), 0, 'L');
    $docLabels = [];
    foreach ($files as $fl) {
        if ($fl['kind'] !== 'application_pdf') {
            $docLabels[] = $fl['label'];
        }
    }
    $pdf->SetFont('Times', 'B', 9);
    $pdf->SetXY(107.5, 231.0);
    $pdf->Cell(89, 4.0, 'Documents attached', 0, 2, 'L');
    $pdf->SetFont('Times', '', 8.5);
    $pdf->SetX(107.5);
    $pdf->MultiCell(89, 3.8, ApplicationPdf::t($docLabels ? implode('  ·  ', array_unique($docLabels)) : 'None'), 0, 'L');

    // ---------------------------------------------------------------- attachments
    $separate = [];
    foreach ($files as $fl) {
        if ($fl['kind'] === 'application_pdf') {
            continue;
        }
        if (in_array($fl['mime'], ['image/jpeg', 'image/png'], true)) {
            $pdf->AddPage('P', 'A4');
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->SetTextColor(30, 42, 120);
            $pdf->SetXY(12, 10);
            $pdf->Cell(186, 6, ApplicationPdf::t('Attachment: ' . $fl['label'] . '  -  ' . $fl['name']), 0, 0, 'L');
            $pdf->imageFit($fl['path'], 12, 18, 186, 267, 0);
            continue;
        }
        if ($fl['mime'] === 'application/pdf') {
            try {
                $pages = $pdf->setSourceFile($fl['path']);
                for ($p = 1; $p <= $pages; $p++) {
                    $tpl  = $pdf->importPage($p);
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height'], true);
                    if ($p === 1) {
                        $pdf->SetFont('Helvetica', 'B', 8);
                        $pdf->SetTextColor(30, 42, 120);
                        $pdf->SetXY(6, 4);
                        $pdf->Cell(0, 4, ApplicationPdf::t('Attachment: ' . $fl['label'] . '  -  ' . $fl['name']), 0, 0, 'L');
                    }
                }
                continue;
            } catch (Throwable $ex) {
                // the free FPDI parser cannot read this PDF (e.g. compressed cross-reference streams)
            }
        }
        $separate[] = $fl;
    }
    if ($separate) {
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(30, 42, 120);
        $pdf->SetXY(12, 12);
        $pdf->Cell(186, 6, 'Attachments sent as separate files with this email', 0, 2, 'L');
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        foreach ($separate as $s) {
            $pdf->SetX(12);
            $pdf->Cell(186, 5, ApplicationPdf::t('- ' . $s['label'] . ': ' . $s['name']), 0, 2, 'L');
        }
    }

    $dir = dirname($outPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    $pdf->Output('F', $outPath);
    return ['path' => $outPath, 'separate' => $separate];
}
