<?php

/**
 * @return array{available: bool, title: string, og_image: string, error: string}
 */
function fetchPagePreview(string $url): array
{
	$empty = [
		'available' => false,
		'title' => '',
		'og_image' => '',
		'error' => '',
	];

	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		$empty['error'] = 'Invalid URL';

		return $empty;
	}

	$html = previewFetchHtml($url);

	if ($html === null || $html === '') {
		$empty['error'] = 'Could not load page content for UI analysis.';

		return $empty;
	}

	$title = previewExtractTitle($html);
	$ogImage = previewExtractOgImage($html, $url);

	return [
		'available' => ($title !== '' || $ogImage !== ''),
		'title' => $title,
		'og_image' => $ogImage,
		'error' => '',
	];
}

function previewFetchHtml(string $url): ?string
{
	if (!function_exists('curl_init')) {
		return null;
	}

	$ch = curl_init($url);
	if ($ch === false) {
		return null;
	}

	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_MAXREDIRS => 3,
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT => 8,
		CURLOPT_USERAGENT => 'PhishingDetector/1.0 (Educational URL Check)',
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
	]);

	$html = curl_exec($ch);
	$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($html === false || $code < 200 || $code >= 400) {
		return null;
	}

	return is_string($html) ? substr($html, 0, 250000) : null;
}

function previewExtractTitle(string $html): string
{
	if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
		return trim(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
	}

	return '';
}

function previewExtractOgImage(string $html, string $baseUrl): string
{
	$patterns = [
		'/property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i',
		'/content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i',
		'/name=["\']twitter:image["\'][^>]*content=["\']([^"\']+)["\']/i',
	];

	foreach ($patterns as $pattern) {
		if (preg_match($pattern, $html, $matches)) {
			return previewResolveUrl(trim($matches[1]), $baseUrl);
		}
	}

	return '';
}

function previewResolveUrl(string $value, string $baseUrl): string
{
	if ($value === '') {
		return '';
	}

	if (preg_match('#^https?://#i', $value)) {
		return $value;
	}

	$parsed = parse_url($baseUrl);
	if ($parsed === false || empty($parsed['scheme']) || empty($parsed['host'])) {
		return $value;
	}

	$origin = $parsed['scheme'] . '://' . $parsed['host'];
	if (isset($parsed['port'])) {
		$origin .= ':' . $parsed['port'];
	}

	if (strpos($value, '//') === 0) {
		return $parsed['scheme'] . ':' . $value;
	}

	if ($value[0] === '/') {
		return $origin . $value;
	}

	return $origin . '/' . $value;
}

function comparePagePreviews(array $submitted, array $trusted): string
{
	$notes = [];

	if (!$submitted['available'] && !$trusted['available']) {
		return 'Page content could not be fetched from either site. URL structure still indicates a lookalike domain.';
	}

	if ($submitted['title'] !== '' && $trusted['title'] !== '') {
		$similarity = 0.0;
		similar_text(strtolower($submitted['title']), strtolower($trusted['title']), $similarity);

		if ($similarity >= 55) {
			$notes[] = 'Page titles look similar — the suspicious site may be copying the real site\'s appearance.';
		} else {
			$notes[] = 'Page titles differ, but the URL domain still looks impersonated.';
		}
	}

	if ($submitted['og_image'] !== '' && $trusted['og_image'] !== '') {
		if ($submitted['og_image'] === $trusted['og_image']) {
			$notes[] = 'Both pages share the same social preview image.';
		} else {
			$notes[] = 'Social preview images were captured for side-by-side comparison.';
		}
	}

	if ($notes === []) {
		return 'Compare the suspicious URL with the trusted link below before you visit any site.';
	}

	return implode(' ', $notes);
}
