# Umalakshmi Educational Trust — static preview

Static HTML preview of the redesigned website. Open `index.html` in a browser, or publish the folder with GitHub Pages (Settings → Pages → Deploy from branch → root).

Pages: index · about · journey · donors · apply · financials · donate · contact (plus privacy/terms placeholders).

Responsive: every page adapts to phone width (below 860px) with a hamburger menu that opens a full-screen navigation panel.

Notes for review
- Forms (Apply, Contact, Donate receipt) are visual only; they show a notice on submit. Server-side handling is to be wired up on the live PHP site.
- The Donate page generates a real UPI QR code in the browser from the amount entered. The UPI ID is a SAMPLE (`umalakshmitrust@hdfcbank`) — change `VPA` at the top of the script in `donate.html` before going live.
- 12A / 80G registration numbers are sample values.
- Fonts load from Google Fonts; everything else is local.


## Deploying to cPanel

1. **Files**: upload the whole folder to `public_html` (FTP, File Manager zip, or `git pull`). Keep `inc/`, `lib/`, `mail/`, `sql/` — `.htaccess` blocks browsers from them.
2. **Database**: cPanel → *MySQL® Databases*: create a database and a user, give the user *All privileges*. Then phpMyAdmin → select the database → *Import* → `sql/schema.sql`.
3. **Mailbox**: cPanel → *Email Accounts*: create `noreply@umalakshmitrust.com` (any strong password). SMTP host is usually `mail.umalakshmitrust.com`, port 465 (SSL).
4. **Upload folder**: File Manager → create `/home/<cpanel-user>/uet_uploads` (above `public_html`).
5. **Settings**: copy `.env.example` to `/home/<cpanel-user>/.env` and fill in DB_*, MAIL_*, `UPLOAD_DIR=/home/<cpanel-user>/uet_uploads`. Never upload the local `.env`.
6. **PHP**: cPanel → *Select PHP Version* → 8.1 or newer with `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `openssl` enabled (default). `.user.ini` raises upload limits; if the host ignores it, set `upload_max_filesize=8M`, `post_max_size=64M` in *MultiPHP INI Editor*.
7. **Test**: submit a trial application from `apply.php`; check the two emails and the row in phpMyAdmin → `applications`. If mail fails, set `MAIL_DRIVER=log` temporarily and look in `UPLOAD_DIR/mail-log/`.

## Local development

```bash
cp .env.example .env     # set DB_SOCKET/DB_NAME/DB_USER, UPLOAD_DIR=storage/uploads, MAIL_DRIVER=log
mysql -uroot -e "CREATE DATABASE umalakshmi_dev CHARACTER SET utf8mb4"
mysql -uroot umalakshmi_dev < sql/schema.sql
php -S 127.0.0.1:8765
```
Logged emails appear as `.eml` files under `storage/uploads/mail-log/`.
