<?php

require_once __DIR__ . '/PhishingDetector.php';

/**
 * Ensure the URL has a scheme for parsing and validation.
 */
function normalizeUrlInput($url)
{
	$url = trim($url);
	if ($url === '') {
		return $url;
	}
	if (!preg_match('#^https?://#i', $url)) {
		$url = 'https://' . $url;
	}
	return $url;
}

/**
 * Build common spelling variants for database lookup (with/without www).
 *
 * @return string[]
 */
function urlLookupVariants($url)
{
	$normalized = normalizeUrlInput($url);
	$parsed = parse_url($normalized);

	if ($parsed === false || empty($parsed['host'])) {
		return [$normalized];
	}

	$scheme = isset($parsed['scheme']) ? $parsed['scheme'] : 'https';
	$host = $parsed['host'];
	$path = isset($parsed['path']) ? $parsed['path'] : '';
	$query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
	$port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

	$hosts = [$host];
	if (strpos($host, 'www.') === 0) {
		$hosts[] = substr($host, 4);
	} else {
		$hosts[] = 'www.' . $host;
	}

	$variants = [];
	foreach (array_unique($hosts) as $h) {
		$variants[] = $scheme . '://' . $h . $port . $path . $query;
	}

	return array_unique($variants);
}

/**
 * Parse ML script stdout. Returns true (phishing), false (safe), or null (unavailable).
 */
function parseMlResult($output)
{
	if ($output === null || $output === '') {
		return null;
	}

	$lines = preg_split('/\r\n|\r|\n/', trim($output));
	for ($i = count($lines) - 1; $i >= 0; $i--) {
		$line = trim($lines[$i]);
		if ($line === '') {
			continue;
		}

		if ($line === 'THIS IS PHISHING URL' || $line === ' THIS IS PHISHING URL') {
			return true;
		}

		if ($line === 'THIS IS NOT PHISHING URL' || $line === ' THIS IS NOT PHISHING URL') {
			return false;
		}
	}

	return null;
}

/**
 * Run the Python ML model for a URL.
 *
 * @return bool|null
 */
function runMlCheck($url)
{
	set_time_limit(120);

	$projectRoot = dirname(__DIR__);
	$script = $projectRoot . DIRECTORY_SEPARATOR . 'index.py';
	$python = getenv('PHISHING_PYTHON') ?: 'python';

	if (!is_file($script)) {
		return null;
	}

	$cwd = getcwd();
	chdir($projectRoot);

	$command = escapeshellarg($python) . ' '
		. escapeshellarg($script) . ' '
		. escapeshellarg($url) . ' 2>&1';

	$output = shell_exec($command);

	if ($cwd !== false) {
		chdir($cwd);
	}

	return parseMlResult($output);
}

/**
 * Look up a URL in the database, trying normalized variants.
 *
 * @return array|null Row from urls table, or null if not found.
 */
function lookupUrlInDatabase($db, $url)
{
	if (!$db) {
		return null;
	}

	foreach (urlLookupVariants($url) as $variant) {
		$escaped = mysqli_real_escape_string($db, $variant);
		$query = "SELECT * FROM urls WHERE url='$escaped' LIMIT 1";
		$results = mysqli_query($db, $query);

		if ($results && mysqli_num_rows($results) === 1) {
			return mysqli_fetch_assoc($results);
		}
	}

	return null;
}

/**
 * Full URL check pipeline.
 *
 * @return array{verdict: string, class: string, details: string, source: string}
 */
function checkUrl($db, $url)
{
	$normalized = normalizeUrlInput($url);

	if (!filter_var($normalized, FILTER_VALIDATE_URL)) {
		return [
			'verdict' => 'Please enter a valid URL',
			'class' => 'shell-alert--warning',
			'details' => '',
			'source' => 'validation',
		];
	}

	$row = lookupUrlInDatabase($db, $normalized);

	if ($row !== null) {
		if ($row['type'] == '1') {
			return [
				'verdict' => 'Phishing detected — do not visit this site',
				'class' => 'shell-alert--danger',
				'details' => '',
				'source' => 'database',
			];
		}

		return [
			'verdict' => 'This URL appears safe',
			'class' => 'shell-alert--safe',
			'details' => '',
			'source' => 'database',
		];
	}

	$detector = new PhishingDetector();
	$heuristic = $detector->analyzeUrl($normalized);
	$mlResult = runMlCheck($normalized);

	if ($heuristic['isSuspicious']) {
		return [
			'verdict' => 'Phishing detected — do not visit this site',
			'class' => 'shell-alert--danger',
			'details' => !empty($heuristic['reasons'])
				? 'Risk signals: ' . implode(' ', $heuristic['reasons'])
				: '',
			'source' => 'heuristic',
		];
	}

	if ($mlResult === true) {
		return [
			'verdict' => 'Phishing detected — do not visit this site',
			'class' => 'shell-alert--danger',
			'details' => '',
			'source' => 'ml',
		];
	}

	if ($mlResult === false) {
		return [
			'verdict' => 'This URL appears safe',
			'class' => 'shell-alert--safe',
			'details' => '',
			'source' => 'ml',
		];
	}

	return [
		'verdict' => 'This URL appears safe',
		'class' => 'shell-alert--safe',
		'details' => 'ML analysis was unavailable; result based on database and heuristic checks only.',
		'source' => 'heuristic-only',
	];
}
