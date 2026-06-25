<?php
/**
 * CLI feedback loop for URL check diagnosis.
 * Usage: php scripts/diagnose-url.php <url>
 * Exit 0 = safe, 1 = phishing/warning, 2 = error
 */

require_once __DIR__ . '/../includes/url_check.php';

$url = isset($argv[1]) ? $argv[1] : 'https://facebook.com';

echo "=== URL check diagnose ===\n";
echo "Input: {$url}\n\n";

echo "Normalized: " . normalizeUrlInput($url) . "\n";
echo "DB variants: " . implode(', ', urlLookupVariants($url)) . "\n";

$detector = new PhishingDetector();
$heuristic = $detector->analyzeUrl(normalizeUrlInput($url));
echo "Heuristic suspicious: " . ($heuristic['isSuspicious'] ? 'yes' : 'no') . "\n";
echo "Heuristic score: " . $heuristic['riskScore'] . "\n";

$mlRaw = shell_exec('python ' . escapeshellarg(__DIR__ . '/../index.py') . ' ' . escapeshellarg(normalizeUrlInput($url)) . ' 2>&1');
echo "ML raw output: [" . trim((string) $mlRaw) . "]\n";
echo "ML parsed: ";
$parsed = parseMlResult($mlRaw);
echo var_export($parsed, true) . "\n\n";

include __DIR__ . '/../server.php';
$result = checkUrl($db, $url);

echo "Verdict: {$result['verdict']}\n";
echo "Class: {$result['class']}\n";
echo "Source: {$result['source']}\n";
if ($result['details'] !== '') {
	echo "Details: {$result['details']}\n";
}

$isSafe = $result['class'] === 'shell-alert--safe';
exit($isSafe ? 0 : 1);
