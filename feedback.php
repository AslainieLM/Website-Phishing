<?php
include('server.php');

$current_page = 'feedback';
$page_title = 'Feedback — Phishing Website Detector';

include('includes/head.php');
include('includes/header.php');
?>

<section class="shell-page-header">
	<div class="shell-container">
		<h1 class="shell-page-header__title">Share your experience</h1>
		<p class="shell-page-header__subtitle">Your feedback helps us improve the detector for everyone.</p>
	</div>
</section>

<main class="shell-main shell-main--padded">
	<div class="shell-container">
		<div class="shell-feedback-card">
			<?php if ($feedback_message !== '') : ?>
				<div class="shell-alert <?php echo $feedback_class; ?>" role="status">
					<?php echo htmlspecialchars($feedback_message); ?>
				</div>
			<?php endif; ?>
			<form role="form" method="post" action="feedback.php">
				<div class="shell-form-group">
					<label class="shell-form-label">How would you rate your experience?</label>
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
					<label class="shell-form-label" for="comments">Comments</label>
					<textarea class="shell-form-control shell-form-control--visible shell-textarea" name="comments" id="comments" placeholder="Tell us what worked well or what we can improve" maxlength="6000" required></textarea>
				</div>
				<div class="shell-form-grid">
					<div class="shell-form-group">
						<label class="shell-form-label" for="name">Your name</label>
						<input type="text" class="shell-form-control shell-form-control--visible" id="name" name="name" placeholder="Enter your name" required>
					</div>
					<div class="shell-form-group">
						<label class="shell-form-label" for="email">Email</label>
						<input type="email" class="shell-form-control shell-form-control--visible" id="email" name="email" placeholder="you@example.com" required>
					</div>
				</div>
				<div class="shell-form-group shell-form-group--submit">
					<button type="submit" class="shell-btn shell-btn--primary shell-btn--block" name="send">Submit feedback</button>
				</div>
			</form>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>
