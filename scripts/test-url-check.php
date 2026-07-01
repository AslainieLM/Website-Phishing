<?php
/**
 * Regression tests for URL check helpers (no database required).
 * Usage: php scripts/test-url-check.php
 */

require_once __DIR__ . '/../includes/url_check.php';
require_once __DIR__ . '/../includes/PhishingDetector.php';

$failures = 0;

function assert_true($condition, $message)
{
	global $failures;
	if (!$condition) {
		echo "FAIL: {$message}\n";
		$failures++;
		return;
	}
	echo "PASS: {$message}\n";
}

// ML parsing must not treat empty/error output as phishing
assert_true(parseMlResult('') === null, 'empty ML output returns null');
assert_true(parseMlResult("datetime error\n THIS IS NOT PHISHING URL") === false, 'safe line after stderr parsed');
assert_true(parseMlResult('Traceback (most recent call last):') === null, 'traceback returns null');
assert_true(parseMlResult(' THIS IS NOT PHISHING URL') === false, 'safe ML string parsed');
assert_true(parseMlResult(' THIS IS PHISHING URL') === true, 'phishing ML string parsed');

// URL variants must include www form for facebook.com
$variants = urlLookupVariants('https://facebook.com');
assert_true(in_array('https://www.facebook.com', $variants, true), 'facebook.com variant includes www');
assert_true(in_array('https://facebook.com', $variants, true), 'facebook.com variant includes bare host');

$variantsWww = urlLookupVariants('https://www.facebook.com');
assert_true(in_array('https://facebook.com', $variantsWww, true), 'www.facebook.com variant includes bare host');

// Bare domain input gets https prefix
assert_true(normalizeUrlInput('facebook.com') === 'https://facebook.com', 'bare domain normalized');

// Heuristic must not flag legitimate facebook.com
$detector = new PhishingDetector();
$heuristic = $detector->analyzeUrl('https://facebook.com');
assert_true($heuristic['isSuspicious'] === false, 'facebook.com not heuristic-suspicious');

// BDO lookalike subdomain must not be treated as official
$bdoPhish = 'https://www.personal.bdo.com.ph/personal';
assert_true(isUrlOnOfficialBrandDomain($bdoPhish) === false, 'personal.bdo.com.ph is not official');
$lookalike = resolveTrustedSiteLink($bdoPhish);
assert_true($lookalike !== null, 'personal.bdo.com.ph resolves trusted comparison');
assert_true(
	$lookalike['url'] === 'https://www.bdo.com.ph/',
	'personal.bdo.com.ph compares against www.bdo.com.ph'
);

// Legitimate BDO Network Bank personal login stays official
$bdoLegit = 'https://www.personal.bdonetworkbank.com.ph/sso/login';
assert_true(isUrlOnOfficialBrandDomain($bdoLegit) === true, 'personal.bdonetworkbank.com.ph is official');

exit($failures > 0 ? 1 : 0);
