# Backend build plan

Goal: turn the approved static front-end into a working PHP site with a database, email, file storage,
comments and a small admin area, deployable to the trust's existing shared PHP host.

Work in this order; each step leaves the site deployable.

## 1. PHP routing and shared layout

- Rename `*.html` → `*.php`. Extract the header (logo + nav + Donate button + mobile menu) into
  `inc/header.php` and the footer into `inc/footer.php`; each page sets `$page = 'about'` etc. before
  including the header so the active nav item is marked.
- `inc/config.php` loads `.env` (DB, SMTP, admin credentials, upload path). Provide `.env.example`.
- `inc/db.php` returns a shared PDO connection (utf8mb4, exceptions on).
- `inc/csrf.php`, `inc/mail.php` (PHPMailer wrapper), `inc/upload.php` (validation + safe storage).
- `.htaccess`: force HTTPS, redirect legacy URLs from the old site to the new pages
  (`about-us.php → about.php`, `gallery.php → journey.php#gallery`, `product_details.php → donate.php`,
  `index.html → /`), deny direct access to `inc/`, `uploads/`, `.env`.
- Keep `privacy.php` and `term.php` from the old site; link them from the footer.

## 2. Database schema (MySQL, utf8mb4)

```
applications        id, ref_no (UET-2026-0001), academic_year, status (new|under_review|shortlisted|
                    selected|rejected), submitted_at, ip, consent_declared, parent_name, declared_on
applicants          application_id, name, gender, dob, standard, course, phone, whatsapp, email,
                    address, photo_file_id
family_members      application_id, relation (father|mother|sibling|guardian), name, education,
                    occupation, yearly_income
family_details      application_id, other_income, other_income_detail, house (own|rent), rent_per_month
education           application_id, institution, inst_type, address, phone, hm_name, passed_all,
                    rank, class_strength, awards
marks               application_id, subject, quarterly, half_yearly, annual
funding             application_id, applied_before, received_before, years, total_fees
fee_items           application_id, label, amount
files               id, owner_type (application|donation|contact), owner_id, kind (photo|marksheet|
                    certificate|fee_demand|income_cert|attestation|screenshot), original_name,
                    stored_name, mime, size, uploaded_at
donations           id, name, phone, email, pan, address, amount, txn_ref, paid_on, mode (upi|bank|
                    cheque), list_on_site, receipt_no, receipt_sent_at, status (pending|verified|receipted)
contact_messages    id, name, email, phone, subject, message, created_at, replied_at
comments            id, page (journey|gallery|year:2025…), name, email, body, status (pending|approved|
                    spam), created_at, approved_by
donors_public       id, name, city, photo_file_id, sort_order, visible   (drives donors.php)
sponsorship_years   year, students, amount, highlight, venue, event_date (drives journey/financials)
institutions        id, name, district, kind (school|college), visible (drives home list)
admin_users         id, email, password_hash, role, last_login
audit_log           id, admin_id, action, target, at
```

Ship `schema.sql` plus a `seed.sql` with the known data (5 sponsorship years, 21 schools, 9 colleges,
28 donors with their photo filenames from `assets/donors/`).

## 3. Application form (apply.php → apply_process.php)

- Accept the existing field names from the 4-step form. Validate server-side (required fields, phone
  10 digits, DOB plausible, numeric marks 0–100, totals), re-render the form with errors preserved.
- Uploads: photo (jpg/png ≤2 MB), mark sheet, certificates (≤5), fee demand, income certificate,
  signed attestation (pdf/jpg/png ≤5 MB each). Verify MIME by content, randomise stored names,
  store under `UPLOAD_DIR/applications/<ref_no>/`.
- On success: create `ref_no`, insert in one transaction, generate a PDF copy of the application
  (dompdf or mPDF) and store it as a file record.
- Mail: (a) applicant/parent — confirmation with ref_no and PDF attached (if email given; else send
  the ref_no by WhatsApp link shown on the thank-you page); (b) trust inbox — new-application
  notification with a link to the admin record. Templates in `mail/` using the site colours.
- Thank-you page shows ref_no and next steps. Add a "Check application status" lookup by ref_no + DOB.
- "Add another sibling" / "Add subject" buttons: small JS that clones a row; backend accepts arrays.

## 4. Donations (donate.php)

- Keep the client-side UPI QR; load `VPA` and payee name from config, not hard-coded.
- `donation_receipt.php` stores the "After you pay" form + optional screenshot and mails the trust.
- Admin marks a donation **verified** against the bank statement, then **issues the 80G receipt**:
  sequential `receipt_no` (e.g. 80G/2026-27/0001), PDF with the trust's registration numbers, PAN of
  donor, amount in words, trustee signature image; emailed to the donor and stored.
- If `list_on_site` is ticked and verified, the donor can be added to `donors_public` from admin.

## 5. Contact form (contact.php → form_process.php)

- Store in `contact_messages`, mail the trust, auto-reply to the sender. Subject dropdown drives
  the mail subject line.

## 6. Comments

- Comments live on **Our Journey** (one thread per year card) and optionally on the gallery.
- Form: name, email (not published), comment, honeypot, CSRF. Stored as `pending`; shown only after
  an admin approves. Approved comments render under the year with name and date. Simple spam
  heuristics (links count, rate limit) + optional Akismet.

## 7. Admin area (`/admin`)

- Login (password_hash, session, lockout after 5 failures). Roles: trustee (full), volunteer (read).
- Applications: list with filters (year, status, district, standard), detail view with all sections,
  download of each file, status change with note, export to CSV/Excel for the trustees' meeting.
- Donations: list, verify, issue receipt, resend receipt.
- Comments: approve / spam / delete.
- Content: edit sponsorship years, institutions, public donors (upload portrait), financial PDFs per year.
- Audit log of admin actions.

## 8. Security and ops checklist

- Prepared statements only; `htmlspecialchars` on all output; CSRF on every POST; rate limiting.
- Uploads outside web root; served via `admin/file.php?id=` after auth; never execute uploaded files.
- `.env` not committed; `config.php` fails loudly if missing.
- Daily DB backup (cron or host panel); keep uploads in the backup.
- Minimal PII in logs; applications older than N years archived per trust policy.
- Deploy: `git pull` or FTP of the whole folder; run `schema.sql` once; set `UPLOAD_DIR` and SMTP.

## Suggested first prompt for Claude Code

"Read CLAUDE.md and BACKEND-SPEC.md. Start with step 1: convert the static pages to PHP with shared
header/footer includes and config/db/csrf/mail helpers, without changing any design. Then create
schema.sql and seed.sql for step 2. Stop after each step so I can review."
