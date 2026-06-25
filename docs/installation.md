# Phishing Website Detector — Installation Guide

This guide covers setting up the full stack on **Windows with XAMPP**: PHP web app, MySQL database, heuristic rules, and Python ML model.

## Prerequisites

| Component | Version |
|-----------|---------|
| XAMPP (Apache + PHP + MySQL) | PHP 7.4+ recommended |
| Python | 3.10+ (3.12–3.14 tested with `requirements-ml.txt`) |
| pip | Latest |

---

## Quick start checklist

1. Start **Apache** and **MySQL** in XAMPP.
2. Import `sql/phishing_db.sql` in phpMyAdmin.
3. Open `http://localhost/PHISHING-MARUHOM/main.php`.
4. Install Python deps: `pip install -r requirements-ml.txt`
5. Ensure `rf_final.pkl` exists (retrain if needed — see [Section 2.3](#23-model-file-rf_finalpkl)).
6. Test: `python index.py "https://example.com"`
7. **Restart Apache** so PHP can find `python` on PATH.
8. Verify: `php scripts/diagnose-url.php "https://example.com"` → `Source: ml`

---

## 1. Web server (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Place this project at:
   ```
   C:\xampp\htdocs\PHISHING-MARUHOM
   ```
3. Open in browser:
   ```
   http://localhost/PHISHING-MARUHOM/main.php
   ```

### Database

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. **Import**:
   ```
   sql/phishing_db.sql
   ```
3. Creates database `phishing_db` with tables `urls` and `user_feedback`.

### PHP database connection

Defaults in `server.php`:

| Setting | Value |
|---------|-------|
| Host | `localhost` |
| User | `root` |
| Password | `admin` |
| Database | `phishing_db` |

Update `server.php` if your MySQL password differs.

---

## 2. Python ML setup

ML analyzes **unknown URLs** — links not found in the `urls` table after www/non-www matching.

### ML components

| File | Role |
|------|------|
| `index.py` | CLI entry — loads model, prints verdict |
| `inputScript.py` | Extracts 30 URL features (network + structure) |
| `rf_final.pkl` | Trained Random Forest model (project root) |
| `includes/url_check.php` | PHP calls Python via `runMlCheck()` |

### 2.1 Install Python

1. Download from [python.org](https://www.python.org/downloads/).
2. Enable **“Add Python to PATH”** during install.
3. Verify:
   ```bash
   python --version
   pip --version
   ```

### 2.2 Install dependencies

From the project folder:

```bash
cd C:\xampp\htdocs\PHISHING-MARUHOM
pip install -r requirements-ml.txt
```

`requirements-ml.txt` includes:

- `joblib`, `scikit-learn`, `numpy`, `scipy` — model load + predict
- `beautifulsoup4`, `lxml`, `requests` — page/HTML features
- `tldextract` — domain parsing
- `python-whois`, `regex` — WHOIS and pattern checks

> **Training only:** `pip install pandas` is needed when running `scripts/train_model.py`, not for day-to-day URL checks.

### 2.3 Model file (`rf_final.pkl`)

The model must live in the project root:

```
PHISHING-MARUHOM/rf_final.pkl
```

**First-time setup or load errors**

If `python index.py` fails with `sklearn.ensemble.forest` or similar pickle errors, the bundled pickle was built with an **old scikit-learn** and cannot load on modern Python. Retrain:

```bash
pip install -r requirements-ml.txt
pip install pandas
python scripts/train_model.py
```

This trains on `datasets/phishcoop.csv` and writes a new `rf_final.pkl` (~97% test accuracy).

### 2.4 Test ML from the command line

```bash
cd C:\xampp\htdocs\PHISHING-MARUHOM
python index.py "https://example.com"
```

**Expected output (single line):**

```
 THIS IS NOT PHISHING URL
```

or

```
 THIS IS PHISHING URL
```

**What changed in `index.py`**

- Removed old CGI `Content-type` header (broke PHP parsing)
- Uses modern `import joblib` (falls back to legacy sklearn import if needed)
- Sets working directory to project root so `rf_final.pkl` loads reliably
- Prints only the verdict line to stdout (errors go to stderr)

**Note:** Feature extraction can take **15–30+ seconds** per URL because `inputScript.py` may fetch pages, check SSL, and query WHOIS.

### 2.5 PHP ↔ Python integration

PHP does not run `index.py` directly by filename. `includes/url_check.php` → `runMlCheck()`:

1. Changes directory to the project root
2. Runs `python index.py "<url>"` (captures stdout + stderr)
3. Parses the **last valid verdict line** via `parseMlResult()`

**Apache must find Python**

1. Add Python to the **system PATH**, then **restart Apache**.
2. Or set environment variable before starting XAMPP:
   ```
   PHISHING_PYTHON=C:\Path\To\python.exe
   ```
   `runMlCheck()` uses `PHISHING_PYTHON` when set, otherwise `python`.

### 2.6 Diagnose URL checks

```bash
php scripts/diagnose-url.php "https://facebook.com"
php scripts/diagnose-url.php "https://example.com"
```

| Field | Good value |
|-------|------------|
| `Source` | `database`, `ml`, or `heuristic` |
| `ML parsed` | `true` or `false` (not `NULL`) |
| `Verdict` | Matches what you see on `main.php` |

For known sites (e.g. Facebook), `Source: database` — ML is skipped.

---

## 3. How detection works

Full pipeline in `includes/url_check.php` → `checkUrl()`:

| Step | Layer | File | When |
|------|-------|------|------|
| 1 | **Normalize** | `url_check.php` | Adds `https://` if missing |
| 2 | **Database** | `phishing_db.urls` | Tries URL with and without `www` |
| 3 | **Heuristics** | `PhishingDetector.php` | Structural rules (IP host, keywords, length, etc.) |
| 4 | **ML** | `index.py` + `rf_final.pkl` | Random Forest on 30 features |

**Verdict rules for unknown URLs**

- **Phishing** if heuristics are suspicious **or** ML returns phishing
- **Safe** if ML returns safe
- **Safe** (with note) if ML is unavailable but heuristics pass — shows *“ML analysis was unavailable…”*

**Examples**

| URL | Typical source | Result |
|-----|----------------|--------|
| `https://facebook.com` | Database (matches `www.facebook.com` row) | Safe, no ML |
| `https://example.com` | ML | Safe or phishing from model |
| `http://192.168.1.1/login` | Heuristics | Phishing + risk signals |

---

## 4. Project structure (key files)

```
PHISHING-MARUHOM/
├── main.php                    # URL check (home)
├── how-it-works.php
├── feedback.php
├── server.php                  # DB connection
├── index.py                    # ML CLI entry (updated)
├── inputScript.py              # Feature extraction (updated for modern libs)
├── rf_final.pkl                # Trained model (required)
├── includes/
│   ├── PhishingDetector.php    # Heuristic rules
│   └── url_check.php           # Full check pipeline + ML bridge
├── datasets/
│   └── phishcoop.csv           # Training data
├── sql/phishing_db.sql
├── requirements-ml.txt         # Python deps for prediction
├── requirements.txt            # Legacy full/training stack
└── scripts/
    ├── train_model.py          # Retrain rf_final.pkl (modern sklearn)
    ├── diagnose-url.php        # Debug a single URL check
    └── test-url-check.php      # PHP regression tests
```

---

## 5. Retrain the ML model

### Recommended (modern Python)

```bash
cd C:\xampp\htdocs\PHISHING-MARUHOM
pip install -r requirements-ml.txt
pip install pandas
python scripts/train_model.py
```

Output:

```
Model saved to ...\rf_final.pkl
Test accuracy: 96.85 %
```

### Legacy (original course code)

```bash
pip install -r requirements.txt
python models/RandomForest.py
```

Copy `final_models/rf_final.pkl` to the project root. Requires older Python/sklearn APIs.

---

## 6. Compatibility fixes (already in codebase)

These updates allow ML to run on current Python/library versions:

| Area | Fix |
|------|-----|
| `index.py` | Modern `joblib`, no CGI header, project-root `chdir` |
| `inputScript.py` | `parse_tld()` for tldextract 3.x+ (`ExtractResult`) |
| `inputScript.py` | `whois_date()` for python-whois datetime (not list) |
| `url_check.php` | `runMlCheck()` uses `shell_exec` from project root |
| `url_check.php` | `parseMlResult()` reads last verdict line from multi-line output |
| `url_check.php` | www/non-www database lookup variants |

---

## 7. Troubleshooting

| Symptom | Likely cause | Fix |
|---------|----------------|-----|
| *“ML analysis was unavailable…”* | Python not installed or not on Apache PATH | `pip install -r requirements-ml.txt`; add Python to PATH; restart Apache |
| `ModuleNotFoundError: sklearn` | Missing scikit-learn | `pip install -r requirements-ml.txt` |
| `No module named 'sklearn.ensemble.forest'` | Old `rf_final.pkl` | `python scripts/train_model.py` |
| `rf_final.pkl not found` | Model missing from root | Retrain or copy file to project root |
| ML works in terminal, not in browser | Apache cannot see `python` | System PATH or `PHISHING_PYTHON`; restart Apache |
| `facebook.com` flagged as phishing | Stale code | Use latest `includes/url_check.php` (www variants + ML parse fix) |
| `TypeError: cannot unpack ExtractResult` | Old `inputScript.py` | Use current `parse_tld()` helper |
| ML very slow | WHOIS/SSL/page fetches per URL | Normal for `inputScript.py`; wait 15–30s |
| SyntaxWarning `\.` in `inputScript.py` | Python 3.14 regex warnings | Harmless; does not affect verdict |

### Quick checks

```bash
python index.py "https://example.com"
php scripts/test-url-check.php
php scripts/diagnose-url.php "https://example.com"
```

Exit code `0` from `test-url-check.php` = PHP helper tests passed.

---

## 8. Running regression tests

```bash
php scripts/test-url-check.php
```

Covers URL normalization, www variants, heuristic pass for `facebook.com`, and ML output parsing (including multi-line stdout).

---

## Support contact

Project footer lists maintainer phone/email for demo and feedback.
