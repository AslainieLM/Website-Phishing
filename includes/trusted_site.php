<?php

/**
 * Brand keyword => one or more official registrable domains (and their subdomains).
 *
 * @return array<string, string|string[]>
 */
function getBrandRegistry(): array
{
	return [
		'chatgpt' => 'chatgpt.com',
		'openai' => 'openai.com',
		'paypal' => 'paypal.com',
		'netflix' => 'netflix.com',
		'microsoft' => 'microsoft.com',
		'google' => 'google.com',
		'apple' => 'apple.com',
		'facebook' => 'facebook.com',
		'instagram' => 'instagram.com',
		'amazon' => 'amazon.com',
		'metrobank' => 'metrobank.com.ph',
		'gcash' => 'gcash.com',
		'bdo' => ['bdo.com.ph', 'bdonetworkbank.com.ph'],
		'bpi' => 'bpi.com.ph',
	];
}

/**
 * @return string[]
 */
function getAllOfficialDomains(): array
{
	$domains = [];

	foreach (getBrandRegistry() as $officialDomains) {
		foreach ((array) $officialDomains as $domain) {
			$domains[] = $domain;
		}
	}

	return array_unique($domains);
}

/**
 * @return string[]
 */
function getOfficialHosts(): array
{
	$hosts = [];

	foreach (getAllOfficialDomains() as $domain) {
		$hosts[] = $domain;
		$hosts[] = 'www.' . $domain;
	}

	foreach (getOfficialHostExtraAllowlist() as $extraHosts) {
		foreach ($extraHosts as $host) {
			$hosts[] = $host;
		}
	}

	return array_unique($hosts);
}

/**
 * Extra hosts beyond apex + www that are known legitimate for an official domain.
 *
 * @return array<string, string[]>
 */
function getOfficialHostExtraAllowlist(): array
{
	return [
		'bdonetworkbank.com.ph' => [
			'www.personal.bdonetworkbank.com.ph',
		],
	];
}

/**
 * Official domains where any subdomain is treated as trusted (e.g. google.com).
 *
 * @return string[]
 */
function getSubdomainWildcardDomains(): array
{
	return [
		'chatgpt.com',
		'openai.com',
		'paypal.com',
		'netflix.com',
		'microsoft.com',
		'google.com',
		'apple.com',
		'facebook.com',
		'instagram.com',
		'amazon.com',
	];
}

function trustedNormalizeUrl(string $url): string
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

function hostBelongsToOfficialDomain(string $host, string $officialDomain): bool
{
	$host = strtolower(rtrim($host, '.'));
	$officialDomain = strtolower($officialDomain);

	if ($host === $officialDomain || $host === 'www.' . $officialDomain) {
		return true;
	}

	$extraHosts = getOfficialHostExtraAllowlist()[$officialDomain] ?? [];
	if (in_array($host, $extraHosts, true)) {
		return true;
	}

	if (in_array($officialDomain, getSubdomainWildcardDomains(), true)) {
		return str_ends_with($host, '.' . $officialDomain);
	}

	return false;
}

/**
 * @return array{keyword: string, domain: string}|null
 */
function resolveOfficialDomainForHost(string $host): ?array
{
	$host = strtolower($host);

	foreach (getBrandRegistry() as $keyword => $officialDomains) {
		foreach ((array) $officialDomains as $officialDomain) {
			if (hostBelongsToOfficialDomain($host, $officialDomain)) {
				return [
					'keyword' => $keyword,
					'domain' => $officialDomain,
				];
			}
		}
	}

	return null;
}

function isUrlOnOfficialBrandDomain(string $url): bool
{
	$parsed = parse_url(trustedNormalizeUrl($url));

	if ($parsed === false || empty($parsed['host'])) {
		return false;
	}

	return resolveOfficialDomainForHost($parsed['host']) !== null;
}

function trustedHostMatchesOfficial(string $host): bool
{
	return resolveOfficialDomainForHost($host) !== null;
}

function hostContainsBrandKeyword(string $host, string $keyword): bool
{
	$host = strtolower($host);
	$keyword = strtolower($keyword);

	foreach (explode('.', $host) as $label) {
		if ($label === $keyword) {
			return true;
		}

		if (str_starts_with($label, $keyword) && strlen($label) > strlen($keyword)) {
			$suffix = substr($label, strlen($keyword), 1);
			if ($suffix === '-' || $suffix === '_') {
				return true;
			}
		}
	}

	return strlen($keyword) >= 6 && str_contains($host, $keyword);
}

function trustedIsSuspiciousPath(string $path): bool
{
	$path = $path === '' ? '/' : $path;

	if ($path === '/') {
		return false;
	}

	if (preg_match('/\.(php|asp|aspx|jsp)$/i', $path)) {
		return true;
	}

	if (preg_match('/[a-z0-9]{16,}/i', $path)) {
		return true;
	}

	return substr_count(trim($path, '/'), '/') >= 3;
}

/**
 * Resolve a Trusted Site Link for comparison when the pasted URL looks like a lookalike or path trap.
 *
 * @return array{url: string, reason: string, confirmed_official: bool}|null
 */
function resolveTrustedSiteLink(string $submittedUrl): ?array
{
	$normalized = trustedNormalizeUrl($submittedUrl);
	$parsed = parse_url($normalized);

	if ($parsed === false || empty($parsed['host'])) {
		return null;
	}

	$scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : 'https';
	$host = strtolower($parsed['host']);
	$path = isset($parsed['path']) ? $parsed['path'] : '/';
	$query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
	$port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
	$pathAndQuery = $path . $query;

	$officialMatch = resolveOfficialDomainForHost($host);
	if ($officialMatch !== null) {
		if (trustedIsSuspiciousPath($path)) {
			return [
				'url' => $scheme . '://' . $officialMatch['domain'] . $port . '/',
				'reason' => 'The domain is official, but the URL path looks suspicious. Compare with the trusted homepage.',
				'confirmed_official' => true,
			];
		}

		return null;
	}

	foreach (getBrandRegistry() as $keyword => $officialDomains) {
		if (!hostContainsBrandKeyword($host, $keyword)) {
			continue;
		}

		$primaryDomain = (string) (is_array($officialDomains) ? $officialDomains[0] : $officialDomains);
		$trustedHost = 'www.' . $primaryDomain;

		return [
			'url' => $scheme . '://' . $trustedHost . $port . '/',
			'reason' => 'Domain contains "' . $keyword . '" but is not the official ' . $primaryDomain . '. Compare with the trusted site.',
			'confirmed_official' => true,
		];
	}

	return null;
}

/**
 * @return array<string, mixed>|null
 */
function buildUrlComparison(string $submittedUrl): ?array
{
	$normalized = trustedNormalizeUrl($submittedUrl);
	$trusted = resolveTrustedSiteLink($normalized);

	if ($trusted === null) {
		return null;
	}

	$submittedHost = parse_url($normalized, PHP_URL_HOST);
	$trustedHost = parse_url($trusted['url'], PHP_URL_HOST);

	if ($submittedHost !== false && $trustedHost !== false
		&& strtolower((string) $submittedHost) === strtolower((string) $trustedHost)
		&& $normalized === $trusted['url']) {
		return null;
	}

	require_once __DIR__ . '/page_preview.php';

	$submittedPreview = fetchPagePreview($normalized);
	$trustedPreview = fetchPagePreview($trusted['url']);

	return [
		'submitted_url' => $normalized,
		'trusted_url' => $trusted['url'],
		'reason' => $trusted['reason'],
		'confirmed_official' => $trusted['confirmed_official'],
		'submitted_preview' => $submittedPreview,
		'trusted_preview' => $trustedPreview,
		'ui_analysis' => comparePagePreviews($submittedPreview, $trustedPreview),
	];
}

/**
 * @param array<string, mixed> $result
 * @return array<string, mixed>
 */
function enrichPhishingComparison(array $result, string $url): array
{
	$result['comparison'] = null;

	if (($result['class'] ?? '') !== 'shell-alert--danger') {
		return $result;
	}

	$comparison = buildUrlComparison($url);
	if ($comparison !== null) {
		$result['comparison'] = $comparison;
	}

	return $result;
}
