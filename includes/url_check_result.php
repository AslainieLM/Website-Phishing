<?php
if (!isset($url_check_message) || $url_check_message === '') {
	return;
}
?>
<div class="shell-alert <?php echo $url_check_class; ?>" role="alert">
	<?php echo htmlspecialchars($url_check_message); ?>
	<?php if (!empty($url_check_details)) : ?>
		<p class="shell-alert__detail"><?php echo htmlspecialchars($url_check_details); ?></p>
	<?php endif; ?>
	<?php if (!empty($url_check_comparison)) : ?>
		<p class="shell-alert__detail">
			<button type="button" class="shell-btn shell-btn--ghost shell-btn--sm" id="shell-compare-open">
				View phishing vs trusted comparison
			</button>
		</p>
	<?php endif; ?>
</div>
