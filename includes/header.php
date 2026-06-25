<?php
if (!isset($current_page)) {
	$current_page = '';
}
?>
<header class="shell-header">
	<div class="shell-container shell-header__inner">
		<a href="main.php" class="shell-logo">
			<span class="shell-logo__accent">Phishing Website</span> Detector
		</a>
		<nav class="shell-nav" aria-label="Main navigation">
			<ul class="shell-nav__list">
				<li>
					<a href="main.php" class="shell-nav__link<?php echo $current_page === 'home' ? ' shell-nav__link--active' : ''; ?>">Check URL</a>
				</li>
				<li>
					<a href="how-it-works.php" class="shell-nav__link<?php echo $current_page === 'how-it-works' ? ' shell-nav__link--active' : ''; ?>">How It Works</a>
				</li>
				<li>
					<a href="feedback.php" class="shell-nav__link<?php echo $current_page === 'feedback' ? ' shell-nav__link--active' : ''; ?>">Feedback</a>
				</li>
			</ul>
		</nav>
	</div>
</header>
