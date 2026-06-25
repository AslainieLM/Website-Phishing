<?php
include('server.php');
require_once 'includes/url_check.php';

$current_page = 'home';
$page_title = 'Phishing Website Detector — Check URLs';
$url_check_message = '';
$url_check_class = '';
$url_check_details = '';
$submitted_url = '';

if (isset($_POST['submit']) && isset($_POST['url'])) {
	$submitted_url = $_POST['url'];
	$result = checkUrl($db, $submitted_url);
	$url_check_message = $result['verdict'];
	$url_check_class = $result['class'];
	$url_check_details = $result['details'];
}

include('includes/head.php');
include('includes/header.php');
?>

<section class="shell-hero">
	<div class="shell-container">
		<h1 class="shell-hero__title">Verify links before you trust them</h1>
		<p class="shell-hero__subtitle">Our database, heuristic rules, and machine learning model work together to spot phishing websites before you click.</p>
	</div>
</section>

<main class="shell-main">
	<div class="shell-container">
		<div class="shell-check-card">
			<label class="shell-check-card__label" for="url">URL Check</label>
			<p class="shell-check-card__hint">Paste any link below. Known URLs are checked instantly; unknown links are scored by heuristic rules and analyzed by our ML model.</p>
			<form class="shell-check-form" action="main.php" method="POST">
				<input
					class="shell-check-form__input"
					type="url"
					id="url"
					name="url"
					placeholder="https://example.com"
					value="<?php echo htmlspecialchars($submitted_url); ?>"
					required
				>
				<button type="submit" name="submit" class="shell-btn shell-btn--primary">Check URL</button>
			</form>
			<?php if ($url_check_message !== '') : ?>
				<div class="shell-alert <?php echo $url_check_class; ?>" role="alert">
					<?php echo htmlspecialchars($url_check_message); ?>
					<?php if ($url_check_details !== '') : ?>
						<p class="shell-alert__detail"><?php echo htmlspecialchars($url_check_details); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>
