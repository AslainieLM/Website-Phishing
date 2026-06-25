<?php
if (!isset($current_page)) {
	$current_page = '';
}
?>
<header class="shell-header">
	<div class="shell-container shell-header__inner">
		<a href="main.php" class="shell-logo">
			<span class="shell-logo__accent">Phishing</span> Detection
		</a>
		<nav class="shell-nav" aria-label="Main navigation">
			<ul class="shell-nav__list">
				<li><a href="main.php" class="shell-nav__link<?php echo $current_page === 'home' ? ' shell-nav__link--active' : ''; ?>">Home</a></li>
				<li><a href="register.php" class="shell-nav__link<?php echo $current_page === 'register' ? ' shell-nav__link--active' : ''; ?>">Register</a></li>
				<li><a href="login.php" class="shell-nav__link<?php echo $current_page === 'login' ? ' shell-nav__link--active' : ''; ?>">Login</a></li>
			</ul>
		</nav>
	</div>
</header>
