<?php
include('server.php');

$sessData = !empty($_SESSION['sessData']) ? $_SESSION['sessData'] : '';
$statusMsg = '';
$statusMsgType = '';
if (!empty($sessData['status']['msg'])) {
	$statusMsg = $sessData['status']['msg'];
	$statusMsgType = $sessData['status']['type'];
	unset($_SESSION['sessData']['status']);
}

$current_page = 'login';
$page_title = 'Login — Phishing Detection';

include('includes/head.php');
include('includes/header.php');
?>

<main class="shell-auth">
	<div class="shell-container">
		<div class="shell-auth-card">
			<h1 class="shell-auth-card__title">Welcome back</h1>
			<p class="shell-auth-card__subtitle">Sign in to your account</p>
			<form action="" method="POST">
				<?php if (!empty($statusMsg)): ?>
				<div class="shell-alert shell-alert--<?php echo $statusMsgType === 'success' ? 'safe' : 'danger'; ?>">
					<?php echo htmlspecialchars($statusMsg); ?>
				</div>
				<?php endif; ?>
				<div class="shell-form-errors"><?php include('errors.php'); ?></div>
				<div class="shell-form-group">
					<label for="user">Username</label>
					<input type="text" class="shell-form-control" id="user" name="user" placeholder="Username" required>
				</div>
				<div class="shell-form-group">
					<label for="pass">Password</label>
					<input type="password" class="shell-form-control" id="pass" name="pass" placeholder="Password" required>
				</div>
				<button type="submit" name="login" class="shell-btn shell-btn--primary shell-btn--block">Login</button>
				<div class="shell-form-footer">
					<p><input type="checkbox" name="remember"> Remember me &nbsp;·&nbsp; <a href="forgetpass.php">Forgot password?</a></p>
					<p>Not a member yet? <a href="register.php">Sign up</a></p>
				</div>
			</form>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>
