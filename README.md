# Umalakshmi Educational Trust — static preview

Static HTML preview of the redesigned website. Open `index.html` in a browser, or publish the folder with GitHub Pages (Settings → Pages → Deploy from branch → root).

Pages: index · about · journey · donors · apply · financials · donate · contact (plus privacy/terms placeholders).

Responsive: every page adapts to phone width (below 860px) with a hamburger menu that opens a full-screen navigation panel.

Notes for review
- Forms (Apply, Contact, Donate receipt) are visual only; they show a notice on submit. Server-side handling is to be wired up on the live PHP site.
- The Donate page generates a real UPI QR code in the browser from the amount entered. The UPI ID is a SAMPLE (`umalakshmitrust@hdfcbank`) — change `VPA` at the top of the script in `donate.html` before going live.
- 12A / 80G registration numbers are sample values.
- Fonts load from Google Fonts; everything else is local.
