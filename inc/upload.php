<?php
/**
 * Upload handling for the public forms.
 *
 *   $f = upload_take('photo', ['required' => true, 'kinds' => ['image'], 'max_mb' => 2]);
 *   upload_store($f, upload_dir() . '/applications/UET-2026-0001');
 *
 * Files are checked by real content (finfo), not by the browser's claimed type,
 * stored under a random name and never executed or served directly.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

final class UploadError extends RuntimeException {}

/** Allowed types, by the family named in the rule → [mime => extension]. */
function upload_allowed(array $kinds): array
{
    $map = [
        'image' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/heic' => 'heic', 'image/heif' => 'heic'],
        'pdf'   => ['application/pdf' => 'pdf'],
    ];
    $out = [];
    foreach ($kinds as $k) {
        $out += $map[$k] ?? [];
    }
    return $out;
}

/**
 * Validate one uploaded file field.
 * Returns null when the field is optional and empty, otherwise
 * ['tmp' => path, 'name' => original, 'mime' => detected, 'size' => bytes, 'ext' => jpg|png|pdf|heic].
 * Throws UploadError with a user-facing message.
 */
function upload_take(string $field, array $rules, string $label = ''): ?array
{
    $label    = $label !== '' ? $label : $field;
    $required = !empty($rules['required']);
    $kinds    = $rules['kinds'] ?? ['image', 'pdf'];
    $maxMb    = (float) ($rules['max_mb'] ?? 5);

    $u = $_FILES[$field] ?? null;
    if ($u === null || !isset($u['error']) || is_array($u['error'])) {
        if ($required) {
            throw new UploadError("{$label}: please attach the file.");
        }
        return null;
    }
    if ($u['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            throw new UploadError("{$label}: please attach the file.");
        }
        return null;
    }
    if ($u['error'] === UPLOAD_ERR_INI_SIZE || $u['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new UploadError("{$label}: the file is too large (limit {$maxMb} MB).");
    }
    if ($u['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($u['tmp_name'])) {
        throw new UploadError("{$label}: the upload failed, please try again.");
    }
    if ($u['size'] <= 0) {
        throw new UploadError("{$label}: the file is empty.");
    }
    if ($u['size'] > $maxMb * 1024 * 1024) {
        throw new UploadError("{$label}: the file is too large (limit {$maxMb} MB).");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $finfo->file($u['tmp_name']);
    $allowed = upload_allowed($kinds);
    if (!isset($allowed[$mime])) {
        $want = in_array('pdf', $kinds, true) ? 'PDF, JPG or PNG' : 'JPG or PNG';
        throw new UploadError("{$label}: please upload a {$want} file.");
    }
    // Belt and braces for images: must decode as an image.
    if (str_starts_with($mime, 'image/') && $mime !== 'image/heic' && $mime !== 'image/heif' && @getimagesize($u['tmp_name']) === false) {
        throw new UploadError("{$label}: the image could not be read.");
    }

    $orig = (string) $u['name'];
    $orig = preg_replace('/[^\p{L}\p{N} ._()-]/u', '_', $orig) ?? 'file';
    $orig = mb_substr($orig, 0, 120);

    return ['tmp' => $u['tmp_name'], 'name' => $orig, 'mime' => $mime, 'size' => (int) $u['size'], 'ext' => $allowed[$mime]];
}

/** Move a validated upload into $dir under a random name; returns the stored file name. */
function upload_store(array $f, string $dir): string
{
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        throw new UploadError('The server could not create the upload folder.');
    }
    $stored = bin2hex(random_bytes(12)) . '.' . $f['ext'];
    if (!move_uploaded_file($f['tmp'], $dir . '/' . $stored)) {
        throw new UploadError('The server could not save the uploaded file.');
    }
    chmod($dir . '/' . $stored, 0640);
    return $stored;
}

/**
 * Validate a multiple-file field (<input name="certs[]" multiple>). Returns a list of the
 * same arrays upload_take() returns (possibly empty). Throws UploadError on any bad file
 * or when more than $maxFiles were sent.
 */
function upload_take_many(string $field, array $rules, string $label, int $maxFiles = 5): array
{
    $u = $_FILES[$field] ?? null;
    if ($u === null) {
        return [];
    }
    if (!is_array($u['name'])) { // a single file was sent without []
        $one = upload_take($field, ['required' => false] + $rules, $label);
        return $one ? [$one] : [];
    }
    $count = count($u['name']);
    $out = [];
    $sent = 0;
    for ($i = 0; $i < $count; $i++) {
        if (($u['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $sent++;
        if ($sent > $maxFiles) {
            throw new UploadError("{$label}: please attach at most {$maxFiles} files.");
        }
        // temporarily present this entry as a single upload to reuse upload_take()
        $backup = $_FILES[$field];
        $_FILES[$field] = ['name' => $u['name'][$i], 'type' => $u['type'][$i], 'tmp_name' => $u['tmp_name'][$i], 'error' => $u['error'][$i], 'size' => $u['size'][$i]];
        try {
            $f = upload_take($field, ['required' => true] + $rules, $label . ' (' . ($i + 1) . ')');
        } finally {
            $_FILES[$field] = $backup;
        }
        if ($f !== null) {
            $out[] = $f;
        }
    }
    return $out;
}
