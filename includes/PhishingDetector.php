<?php

require_once __DIR__ . '/trusted_site.php';

class PhishingDetector
{
	private $suspiciousKeywords = [
		'login', 'verify', 'secure', 'banking', 'signin', 'account',
		'update', 'paypal', 'netflix', 'microsoft', 'google', 'apple',
	];

	/**
	 * Analyzes a URL and returns a risk report.
	 *
	 * @param string $url The URL to inspect.
	 * @return array Assessment results.
	 */
	public function analyzeUrl($url)
	{
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return [
				'isValid' => false,
				'riskScore' => 100,
				'isSuspicious' => true,
				'reasons' => ['Invalid URL format'],
			];
		}

		$reasons = [];
		$score = 0;
		$parsedUrl = parse_url($url);
		$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
		$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
		$onOfficialDomain = resolveOfficialDomainForHost($host) !== null;

		if (filter_var($host, FILTER_VALIDATE_IP)) {
			$score += 40;
			$reasons[] = 'Domain uses a raw IP address instead of a hostname.';
		}

		if (strrpos($url, '//') > 7) {
			$score += 30;
			$reasons[] = 'Suspicious positioning of "//" symbol.';
		}

		$subdomainCount = count(explode('.', $host)) - 2;
		if (!$onOfficialDomain && $subdomainCount >= 3) {
			$score += 25;
			$reasons[] = "High number of subdomains detected ({$subdomainCount}).";
		}

		if (strlen($url) > 75) {
			$score += 15;
			$reasons[] = 'URL length is exceptionally long (> 75 characters).';
		}

		if (!$onOfficialDomain) {
			foreach ($this->suspiciousKeywords as $keyword) {
				if (stripos($host, $keyword) !== false || stripos($path, $keyword) !== false) {
					if (!preg_match('/' . preg_quote($keyword, '/') . '\.[a-z]{2,}$/i', $host)) {
						$score += 20;
						$reasons[] = "Contains suspicious keyword target: '{$keyword}'.";
						break;
					}
				}
			}
		}

		$finalScore = min($score, 100);

		return [
			'isValid' => true,
			'riskScore' => $finalScore,
			'isSuspicious' => ($finalScore >= 40),
			'reasons' => $reasons,
		];
	}
}
