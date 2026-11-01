# Umalakshmi Educational Trust — website

Redesigned website for **Uma Lakshmi Educational Trust**, a registered charitable trust in Chennai
(founded 11 June 2022) that pays school and college fees for students from low-income families
across Tamil Nadu. Trustees: SP. Nachiappan (Founder & Managing Trustee), N. Subramanian (Financial Trustee — the site owner/developer).

## What is in this folder

- `*.html` — the approved **static front-end** (8 pages). Treat the markup, copy and CSS as the
  design source of truth. The design was reviewed and signed off; do not restyle it.
- `assets/` — all images (logo `uet-logo.png`, photos, donor portraits).
- `BACKEND-SPEC.md` — the backend build plan (PHP + MySQL + mail + uploads + admin). **Read it first.**
- `README.md` — notes from the design hand-off.

Pages: `index` (home), `about`, `journey`, `donors`, `apply` (4-step aid application form),
`financials`, `donate` (dynamic UPI QR), `contact`, plus `privacy` / `terms` placeholders.

## Target stack

- PHP 8.x on shared hosting (the live site at umalakshmitrust.com is already PHP; a `database.inc.php`
  with MySQL credentials and a `form_process.php` exist there — reuse the DB connection pattern).
- MySQL / MariaDB. Use PDO with prepared statements everywhere.
- Mail: PHPMailer over SMTP (credentials in `.env`, never committed).
- No framework required; keep it plain PHP with a small shared `inc/` layer so the host can run it as-is.

## Conventions

- Convert each `.html` into `.php` with shared `inc/header.php` and `inc/footer.php` includes; the
  header must mark the active nav item (desktop pill nav and the mobile menu both use `.active` / `.on`).
- Keep all HTML/CSS exactly as designed; only swap static bits for PHP output.
- Form field `name`s in `apply.html`, `contact.html` and `donate.html` are the contract for the backend — keep them.
- Bilingual labels (English + Tamil) stay as they are.
- Every form: server-side validation, CSRF token, honeypot field, rate limit by IP.
- Uploads go **outside** the web root (or a directory with `.htaccess` deny); serve them only through an
  authenticated admin download script.
- Never log or echo PAN / Aadhaar / income values anywhere except the DB and the admin UI.

## Values that are SAMPLES and must be replaced before go-live

- UPI ID `umalakshmitrust@hdfcbank` (donate page script `VPA`, footer, donate card).
- 12A URN `AAATU1234KE20251`, 80G URN `AAATU1234KF20251` (home, financials, donate).
- Suggested sponsorship amounts ₹1,000 / ₹5,000 / ₹10,000 — trustees to confirm.
- Fifth-function (2026) date/venue and photos; founder's message is final.
- Financial statement PDFs linked under `pdf/financials/…` do not exist yet.

## Done / not done

- Done: full responsive front-end, hamburger menu, client-side UPI QR generator (pure JS, no library).
- Done (step 1, partly): `index`, `about`, `journey`, `donors`, `apply` converted to PHP with `inc/head.php`,
  `inc/header.php` (`$header_band` for the indigo-band pages), `inc/footer.php`, `inc/functions.php`
  (`nav_items()`, `page_href()` fallback to `.html` while pages are still being converted — remove when all
  pages are `.php`). Journey gallery reads `assets/gallery/<year>/`. Nav label for apply is "Scholarship"
  (long label "Apply for Scholarship" in the mobile menu).
- Done (step 3): `apply.php` → `apply_process.php` with `inc/config.php` (.env), `inc/db.php`, `inc/csrf.php`,
  `inc/upload.php`, `inc/mail.php` (PHPMailer, `MAIL_DRIVER=log` for local), `inc/pdf_application.php`
  (FPDF + FPDI, English-only text), templates in `mail/`, schema in `sql/schema.sql`. Attestation replaced by a
  mandatory bonafide-certificate upload (`files.kind = bonafide`); email is mandatory.
- Not done: `financials`, `donate`, `contact`, `privacy`, `terms` conversion; `.htaccess` legacy redirects for
  those; donations, contact form, comments, admin area (steps 4–7); application status lookup.
- Libraries are vendored under `lib/` (no Composer on the host). Deployment steps are in `README.md`.
