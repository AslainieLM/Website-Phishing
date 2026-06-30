<?php
if (empty($url_check_comparison)) {
	return;
}

$comparison = $url_check_comparison;
$submittedPreview = $comparison['submitted_preview'];
$trustedPreview = $comparison['trusted_preview'];
?>
<div
	class="shell-compare-modal"
	id="shell-compare-modal"
	data-auto-open="true"
	role="dialog"
	aria-modal="true"
	aria-labelledby="shell-compare-modal-title"
	hidden
>
	<div class="shell-compare-modal__backdrop" data-compare-close></div>
	<div class="shell-compare-modal__panel">
		<header class="shell-compare-modal__header">
			<div>
				<h2 class="shell-compare-modal__title" id="shell-compare-modal-title">Phishing comparison</h2>
				<p class="shell-compare-modal__subtitle"><?php echo htmlspecialchars($comparison['reason']); ?></p>
			</div>
			<button type="button" class="shell-compare-modal__close" data-compare-close aria-label="Close comparison">&times;</button>
		</header>

		<p class="shell-compare-modal__analysis"><?php echo htmlspecialchars($comparison['ui_analysis']); ?></p>

		<div class="shell-compare-modal__grid">
			<section class="shell-compare-card shell-compare-card--danger">
				<h3 class="shell-compare-card__label">Suspicious URL</h3>
				<p class="shell-compare-card__warning" role="alert">
					<strong>Not legitimate.</strong> This website is impersonating a trusted brand. Do not visit it or enter personal information.
				</p>
				<p class="shell-compare-card__url"><?php echo htmlspecialchars($comparison['submitted_url']); ?></p>
				<?php if ($submittedPreview['title'] !== '') : ?>
					<p class="shell-compare-card__meta"><strong>Page title:</strong> <?php echo htmlspecialchars($submittedPreview['title']); ?></p>
				<?php endif; ?>
				<?php if ($submittedPreview['og_image'] !== '') : ?>
					<div class="shell-compare-card__preview">
						<img
							src="<?php echo htmlspecialchars($submittedPreview['og_image']); ?>"
							alt="Social preview image from the suspicious website"
							loading="lazy"
						>
					</div>
				<?php else : ?>
					<div class="shell-compare-card__preview shell-compare-card__preview--empty">
						<p>No page preview available. The URL domain was flagged as impersonation.</p>
					</div>
				<?php endif; ?>
				<?php if ($submittedPreview['error'] !== '') : ?>
					<p class="shell-compare-card__note"><?php echo htmlspecialchars($submittedPreview['error']); ?></p>
				<?php endif; ?>
			</section>

			<section class="shell-compare-card shell-compare-card--safe">
				<h3 class="shell-compare-card__label">Trusted URL</h3>
				<p class="shell-compare-card__url"><?php echo htmlspecialchars($comparison['trusted_url']); ?></p>
				<?php if (!$comparison['confirmed_official']) : ?>
					<p class="shell-compare-card__note">Official site could not be confirmed automatically.</p>
				<?php endif; ?>
				<?php if ($trustedPreview['title'] !== '') : ?>
					<p class="shell-compare-card__meta"><strong>Page title:</strong> <?php echo htmlspecialchars($trustedPreview['title']); ?></p>
				<?php endif; ?>
				<?php if ($trustedPreview['og_image'] !== '') : ?>
					<div class="shell-compare-card__preview">
						<img
							src="<?php echo htmlspecialchars($trustedPreview['og_image']); ?>"
							alt="Social preview image from the trusted website"
							loading="lazy"
						>
					</div>
				<?php else : ?>
					<div class="shell-compare-card__preview shell-compare-card__preview--empty">
						<p>Use the button below to open the legitimate website.</p>
					</div>
				<?php endif; ?>
				<?php if ($trustedPreview['error'] !== '') : ?>
					<p class="shell-compare-card__note"><?php echo htmlspecialchars($trustedPreview['error']); ?></p>
				<?php endif; ?>
				<a
					class="shell-btn shell-btn--primary shell-compare-card__redirect"
					href="<?php echo htmlspecialchars($comparison['trusted_url']); ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					Go to trusted URL
				</a>
			</section>
		</div>

		<footer class="shell-compare-modal__footer">
			<p class="shell-compare-modal__warning">Do not visit the suspicious link. Use the trusted URL if you need the real service.</p>
			<button type="button" class="shell-btn shell-btn--primary" data-compare-close>Close</button>
		</footer>
	</div>
</div>
<script src="./js/comparison-modal.js"></script>
