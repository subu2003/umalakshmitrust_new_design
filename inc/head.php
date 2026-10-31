<?php
/**
 * Opens the document: <!doctype> through the Google Fonts <link>.
 *
 * Set BEFORE including this file:
 *   $page        string  page id for the active nav item: index|about|journey|donors|apply|financials|donate|contact
 *   $page_title  string  e.g. 'Home'  → <title>Umalakshmi Educational Trust — Home</title>
 *   $page_fonts  string  optional extra Google Fonts family fragment, e.g. '&amp;family=Noto+Sans+Tamil:wght@400;600'
 *
 * The page then emits its own <style> block(s) verbatim from the approved design,
 * and includes inc/header.php (which closes <head>, opens <body> and prints the site header).
 */
require_once __DIR__ . '/functions.php';

$page       = isset($page) ? (string) $page : '';
$page_title = isset($page_title) ? (string) $page_title : '';
$page_fonts = isset($page_fonts) ? (string) $page_fonts : '';
$site_name  = 'Umalakshmi Educational Trust';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($site_name) ?><?= $page_title !== '' ? ' — ' . h($page_title) : '' ?></title>
<link rel="icon" href="assets/uet-logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&amp;family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700<?= $page_fonts ?>&amp;display=swap" rel="stylesheet">
