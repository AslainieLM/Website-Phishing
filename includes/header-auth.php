<?php
$auth_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<header class="shell-header">
	<div class="shell-container shell-header__inner">
		<a href="loggedin.php" class="shell-logo">
			<span class="shell-logo__accent">Phishing</span> Detection
		</a>
		<nav class="shell-nav" aria-label="Account navigation">
			<ul class="shell-nav__list">
				<li><a href="loggedin.php" class="shell-nav__link shell-nav__link--active">Home</a></li>
				<li><span class="shell-nav__welcome">Welcome, <?php echo htmlspecialchars($auth_username); ?></span></li>
				<li>
					<form class="shell-nav__logout-form" action="" method="POST">
						<button type="submit" name="logout" class="shell-nav__link shell-nav__logout">Logout</button>
					</form>
				</li>
			</ul>
		</nav>
	</div>
</header>
