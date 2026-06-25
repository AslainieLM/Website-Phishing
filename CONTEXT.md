# Phishing Website Detector

A web app that lets anyone check whether a URL is phishing. Three public pages share one shell — no accounts, no login.

## Language

**Public Shell**:
The shared UI across all public pages — common header, footer, and visual style. No authenticated area.
_Avoid_: Guest Public Shell (legacy), member area, dashboard

**Home Page**:
`main.php` — hero and URL Check only. No How It Works steps or Feedback form on this page.
_Avoid_: Single-page app, everything on main

**How It Works Page**:
`how-it-works.php` — the three-step explanation (paste URL → analyze → read verdict).
_Avoid_: Anchor section on main, carousel

**Feedback Page**:
`feedback.php` — the full feedback form; submissions stored in `user_feedback`.
_Avoid_: Feedback section on main, JavaScript popup on submit

**URL Check**:
The primary action on the home page: paste a URL and receive a phishing / not-phishing result. **Database first** (with www/non-www variants), **then heuristics + ML** for unknown URLs. ML failures do not count as phishing — only an explicit ML phishing result or heuristic suspicion flags a link.
_Avoid_: Scan, detection run (as a separate product name), login-gated ML, treating ML errors as phishing

**Known URL**:
A URL in the `urls` table, matched exactly or via www/non-www variant (e.g. `https://facebook.com` matches `https://www.facebook.com`).
_Avoid_: Strict string-only lookup, duplicate rows per spelling

**Heuristic Check**:
Structural URL rules that assign a risk score — raw IP host, misplaced `//`, excessive subdomains, long URLs, and suspicious keywords (e.g. login, verify, paypal). A score of 40 or higher is suspicious.
_Avoid_: ML prediction, database lookup, manual review

**Phishing URL**:
A URL classified as malicious — `type = 1` in the `urls` table, a suspicious heuristic score, or a positive ML prediction. Shown to the user as a red warning.
_Avoid_: Bad link, scam site

**Safe URL**:
A URL in the database with a non-phishing type, or an unknown URL that passes heuristic checks and is not explicitly flagged by ML. When ML is unavailable, the verdict relies on database and heuristics only.
_Avoid_: Legitimate URL, clean link, defaulting to phishing on ML error

**How it Works**:
The three-step explanatory content on `how-it-works.php` (paste URL → system checks → see result).
_Avoid_: Carousel, gallery, feature slider, section on main.php

**Image-free Shell**:
The Public Shell uses no photographic assets — only CSS colors and gradients for visual structure.
_Avoid_: Stock photos, background images, image carousel

**Modern Security Theme**:
The visual style for the Public Shell — **deep blue header** (`#1e3a5f`), **electric cyan accent** (`#06b6d4`), warm off-white content areas, and badge-style alerts for URL Check results.
_Avoid_: Legacy navy + teal palette, neon effects, dark mode site-wide

**Feedback Block**:
The feedback form on `feedback.php` — all fields with **high-contrast bordered inputs**: white background, 2px dark border, cyan focus ring, bold labels. Success shown as an inline banner, not a JavaScript popup.
_Avoid_: Feedback on main.php, `alert()` on submit, low-contrast form fields

**Shell Includes**:
Shared PHP partials (`includes/header.php`, `includes/footer.php`, `includes/head.php`) included on every public page.
_Avoid_: Duplicated header markup, CSS-only unification

**Site Navigation**:
Header links to `main.php`, `how-it-works.php`, and `feedback.php`. Active page highlighted in the nav.
_Avoid_: In-page anchors only, Register/Login links

**Shell Footer**:
The shared footer — app name, copyright year (2026), and contact phone/email, restyled to match the Modern Security Theme.
_Avoid_: Empty footer, removed contact details

**Page Header**:
`main.php` uses a full hero (large title + subtitle). `how-it-works.php` and `feedback.php` use a **compact page header** — shorter blue band with page title and one-line subtitle.
_Avoid_: Full hero on every page, bare h1 with no header band

**Copy Tone**:
Educational — clear and professional, slightly explanatory. Good for a school project; mentions database + ML without fear-mongering.
_Avoid_: ALL-CAPS warnings, "log in to check more", stiff imperative headlines

**URL Check Result**:
The badge shown after a URL Check submission. Outcomes: phishing warning, safe confirmation, or invalid URL. Unknown valid URLs are analyzed by ML and return phishing or safe.
_Avoid_: ALL-CAPS raw text, inline font tags, login prompts

## URL Check Result copy

| Outcome | Message |
|---------|---------|
| Phishing URL | Phishing detected — do not visit this site |
| Safe URL | This URL appears safe |
| Invalid URL | Please enter a valid URL |

## Resolved decisions

- **Auth removed (Option A):** No login, register, member dashboard, admin UI, or password reset. Delete associated files, code, and the `registration` table. Database name: `phishing_db`.
- **Unknown URLs (Option A):** Database lookup first; if missing, run `index.py` on `main.php` for all visitors.
- **Feedback:** Form on `feedback.php`; store in `user_feedback`; inline success banner.
- **Visual design:** Deep blue + cyan theme in `shell.css`.
- **Page split (Option A):** Three pages — `main.php` (URL check), `how-it-works.php`, `feedback.php`.
- **Color palette (Option A):** Deep blue header + electric cyan accent; warm off-white backgrounds.
- **Feedback fields (Option A):** White inputs, 2px dark border, cyan focus ring, bold labels.
- **Sub-page headers (Option A):** Compact page header on `how-it-works.php` and `feedback.php`.
- **Copy tone (Option B):** Educational — explain DB + ML clearly; professional, not alarmist.
- **App name (Option B):** **Phishing Website Detector** — used consistently in header, footer, and page title.
- **Database schema (A+C):** Single canonical `sql/phishing_db.sql` with import note; drop `registration` table; all PHP uses `phishing_db`; delete `php_project_db.sql`.
- **Feedback success:** Inline banner on `feedback.php`, styled like URL Check alerts.
- **Footer contact (Option A):** Keep phone 09391520886 and email marjovicalejado123@gmail.com; restyle only.
- **Assets (Option A):** `shell.css` only — drop Bootstrap from `head.php`; delete legacy auth/admin CSS and unused Bootstrap JS/CSS.
