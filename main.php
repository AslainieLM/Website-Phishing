<?php
include('server.php');

$current_page = 'home';
$page_title = 'Phishing Detection — Check URLs';
$url_check_message = '';
$url_check_class = '';

if (isset($_POST['submit']) && isset($_POST['url'])) {
	$db = mysqli_connect('localhost', 'root', 'admin', 'php_project_db');

	if ($db) {
		$url = mysqli_real_escape_string($db, $_POST['url']);
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
		} else {
			$url_check_message = 'URL not in our database — log in to check more';
			$url_check_class = 'shell-alert--warning';
		}
	}
}

include('includes/head.php');
include('includes/header.php');
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
			<form class="shell-check-form" action="" method="POST">
				<input
					class="shell-check-form__input"
					type="url"
					id="url"
					name="url"
					placeholder="Paste URL to check..."
					value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>"
					required
				>
				<button type="submit" name="submit" class="shell-btn shell-btn--primary">Check</button>
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
				<p class="shell-step__text">We look up the URL against our known phishing database.</p>
			</article>
			<article class="shell-step">
				<div class="shell-step__number">3</div>
				<h3 class="shell-step__title">See the result</h3>
				<p class="shell-step__text">Get a clear phishing, safe, or unknown verdict before you click through.</p>
			</article>
		</div>
	</div>
</main>

<section class="shell-feedback">
	<div class="shell-container">
		<div class="shell-feedback-card">
			<h2>Feedback</h2>
			<p>Please provide your feedback below:</p>
			<form role="form" method="post" id="reused_form">
				<div class="shell-form-group">
					<label>How do you rate your overall experience?</label>
					<div class="shell-radio-group">
						<label>
							<input type="radio" name="experience" value="bad" required>
							Bad
						</label>
						<label>
							<input type="radio" name="experience" value="average">
							Average
						</label>
						<label>
							<input type="radio" name="experience" value="good">
							Good
						</label>
					</div>
				</div>
				<div class="shell-form-group">
					<label for="comments">Comments</label>
					<textarea class="shell-form-control shell-textarea" name="comments" id="comments" placeholder="Your comments" maxlength="6000" required></textarea>
				</div>
				<div class="shell-form-grid">
					<div class="shell-form-group">
						<label for="name">Your Name</label>
						<input type="text" class="shell-form-control" id="name" name="name" required>
					</div>
					<div class="shell-form-group">
						<label for="email">Email</label>
						<input type="email" class="shell-form-control" id="email" name="email" required>
					</div>
				</div>
				<div class="shell-form-group" style="margin-top: 1.25rem;">
					<button type="submit" class="shell-btn shell-btn--primary shell-btn--block" name="send">Post Feedback</button>
				</div>
			</form>
		</div>
	</div>
</section>

<?php include('includes/footer.php'); ?>
