<?php
include('server.php');

$current_page = 'how-it-works';
$page_title = 'How It Works — Phishing Website Detector';

include('includes/head.php');
include('includes/header.php');
?>

<section class="shell-page-header">
	<div class="shell-container">
		<h1 class="shell-page-header__title">How It Works</h1>
		<p class="shell-page-header__subtitle">Learn how our database, heuristic rules, and machine learning model analyze suspicious links.</p>
	</div>
</section>

<main class="shell-main shell-main--padded">
	<div class="shell-container">
		<div class="shell-steps">
			<article class="shell-step">
				<div class="shell-step__number">1</div>
				<h2 class="shell-step__title">Paste a URL</h2>
				<p class="shell-step__text">Copy the link you received in an email, message, or search result and paste it into the checker on the home page.</p>
			</article>
			<article class="shell-step">
				<div class="shell-step__number">2</div>
				<h2 class="shell-step__title">We analyze the link</h2>
				<p class="shell-step__text">We check our known URL database first, then run heuristic rules and our machine learning model on unknown links.</p>
			</article>
			<article class="shell-step">
				<div class="shell-step__number">3</div>
				<h2 class="shell-step__title">Read the verdict</h2>
				<p class="shell-step__text">You get a clear safe or phishing result so you can decide whether to visit the site.</p>
			</article>
		</div>
		<p class="shell-cta-text">
			Ready to check a link?
			<a href="main.php">Go to URL Check</a>
		</p>
	</div>
</main>

<?php include('includes/footer.php'); ?>
