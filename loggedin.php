<?php
include('server.php');

if (!isLoggedIn()) {
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
	exit();
}

if (isset($_POST['logout'])) {
	session_destroy();
	unset($_SESSION['username']);
	header('location: login.php');
	exit();
}

$page_title = 'Dashboard — Phishing Detection';
$url_check_message = '';
$url_check_class = '';
$submitted_url = '';

if (isset($_POST['check']) && isset($_POST['url'])) {
	$submitted_url = $_POST['url'];
	$db = mysqli_connect('localhost', 'root', 'admin', 'php_project_db');

	if ($db) {
		$url = mysqli_real_escape_string($db, $submitted_url);
		$query = "SELECT * FROM urls WHERE url='$url'";
		$results = mysqli_query($db, $query);

		if ($results && mysqli_num_rows($results) === 1) {
			$check_url = mysqli_fetch_assoc($results);
			if ($check_url['type'] == '1') {
				$url_check_message = 'Phishing detected — do not visit this site';
				$url_check_class = 'shell-alert--danger';
			} else {
				$url_check_message = 'This URL appears safe';
				$url_check_class = 'shell-alert--safe';
			}
		} elseif (filter_var($submitted_url, FILTER_VALIDATE_URL) === false) {
			$url_check_message = 'Please enter a valid URL';
			$url_check_class = 'shell-alert--warning';
		} else {
			set_time_limit(0);
			$result = exec('index.py ' . $submitted_url);

			if ($result === ' THIS IS NOT PHISHING URL') {
				$url_check_message = 'This URL appears safe';
				$url_check_class = 'shell-alert--safe';
			} else {
				$url_check_message = 'Phishing detected — do not visit this site';
				$url_check_class = 'shell-alert--danger';
			}
		}
	}
}

include('includes/head.php');
include('includes/header-auth.php');
?>

<section class="shell-hero">
	<div class="shell-container">
		<h1 class="shell-hero__title">Do Not Make a Mistake — Check Every Website</h1>
		<p class="shell-hero__subtitle">Stay alert. Stay safe from phishers.</p>
	</div>
</section>

<main class="shell-main">
	<div class="shell-container">
		<div class="shell-check-card">
			<label class="shell-check-card__label" for="url">URL Check</label>
			<p class="shell-check-card__hint">URLs not in our database are analyzed with the ML model.</p>
			<form class="shell-check-form" action="" method="POST">
				<input
					class="shell-check-form__input"
					type="url"
					id="url"
					name="url"
					placeholder="Paste URL to check..."
					value="<?php echo htmlspecialchars($submitted_url); ?>"
					required
				>
				<button type="submit" name="check" class="shell-btn shell-btn--primary">Check</button>
			</form>
			<?php if ($url_check_message !== '') : ?>
				<div class="shell-alert <?php echo $url_check_class; ?>" role="alert">
					<?php echo htmlspecialchars($url_check_message); ?>
				</div>
			<?php endif; ?>
		</div>

		<h2 class="shell-section-title" style="margin-top: 3.5rem;">How It Works</h2>
		<div class="shell-steps">
			<article class="shell-step">
				<div class="shell-step__number">1</div>
				<h3 class="shell-step__title">Paste a URL</h3>
				<p class="shell-step__text">Copy the link you want to verify and paste it into the checker above.</p>
			</article>
			<article class="shell-step">
				<div class="shell-step__number">2</div>
				<h3 class="shell-step__title">System checks</h3>
				<p class="shell-step__text">We search our database first, then run the ML model for unknown URLs.</p>
			</article>
			<article class="shell-step">
				<div class="shell-step__number">3</div>
				<h3 class="shell-step__title">See the result</h3>
				<p class="shell-step__text">Get a clear phishing or safe verdict before you click through.</p>
			</article>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>
