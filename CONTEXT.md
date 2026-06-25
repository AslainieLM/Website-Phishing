# Phishing Detector

A web app that lets visitors check whether a URL is phishing. Guests can browse the public shell, register, and log in; Members who sign in get an extended experience on separate pages.

## Language

**Member**:
Anyone who has completed registration and has an account in the system. A Member is not necessarily signed in.
_Avoid_: User, registered user, account holder

**Authenticated User**:
A Member with an active login session — able to access pages beyond the Guest Public Shell (e.g. `loggedin.php`).
_Avoid_: Logged-in member, session user

**Guest Public Shell**:
The unauthenticated UI shared across the home, login, and register pages — common header, footer, and visual style.
_Avoid_: Public pages, landing zone, marketing site

**URL Check**:
The primary guest action on the home page: paste a URL and receive a phishing / not-phishing result from the database lookup.
_Avoid_: Scan, detection run, ML prediction (the home page does not call the Python models today)

**Phishing URL**:
A URL classified as malicious (`type = 1` in the `urls` table). Shown to the user as a red warning.
_Avoid_: Bad link, scam site

**Safe URL**:
A URL present in the database that is not classified as phishing.
_Avoid_: Legitimate URL, clean link

**How it Works**:
A three-step explanatory section on the home page (paste URL → system checks → see result) that replaces the removed image slideshow.
_Avoid_: Carousel, gallery, feature slider

**Image-free Shell**:
The Guest Public Shell uses no photographic assets — only CSS colors and gradients for visual structure.
_Avoid_: Stock photos, background images, image carousel

**Modern Security Theme**:
The visual style for the Guest Public Shell — dark navy header, clean white content areas, a single teal/blue accent, and badge-style alerts for URL Check results.
_Avoid_: Neon effects, dark mode site-wide, retro color scheme

**Feedback Block**:
The feedback form section on the home page — kept in place with all existing fields, restyled to match the Guest Public Shell.
_Avoid_: Feedback page redirect, stripped-down feedback

**Shell Includes**:
Shared PHP partials (`includes/header.php`, `includes/footer.php`, `includes/head.php`) that every Guest Public Shell page includes so navigation and layout stay consistent.
_Avoid_: Duplicated header markup, CSS-only unification

**Home-only URL Check**:
The URL Check form appears only on `main.php`. Login and register pages do not duplicate it.
_Avoid_: URL checker on every page, login-page scan

**Shell Footer**:
The shared footer on Guest Public Shell pages — app name, copyright year (2026), and the original contact phone/email, restyled to match the Modern Security Theme.
_Avoid_: Empty footer, removed contact details

**URL Check Result**:
The badge shown after a URL Check submission. Three outcomes: phishing warning, safe confirmation, or unknown URL prompting login.
_Avoid_: ALL-CAPS raw text, inline font tags

## URL Check Result copy

| Outcome | Message |
|---------|---------|
| Phishing URL | Phishing detected — do not visit this site |
| Safe URL | This URL appears safe |
| Unknown URL | URL not in our database — log in to check more |

## Example dialogue

> **Dev:** Should we restyle the login page too?
> **Expert:** Yes — anything in the Guest Public Shell should match. `loggedin.php` is separate; leave that for now.
