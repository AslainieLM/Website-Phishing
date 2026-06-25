<?php
include('server.php');

$current_page = 'register';
$page_title = 'Register — Phishing Detection';

include('includes/head.php');
include('includes/header.php');
?>

<main class="shell-auth">
	<div class="shell-container">
		<div class="shell-auth-card shell-auth-card--wide">
			<h1 class="shell-auth-card__title">Create an account</h1>
			<p class="shell-auth-card__subtitle">Join to access extended URL checking</p>
			<form name="reg" action="" method="POST">
				<div class="shell-form-errors"><?php include('errors.php'); ?></div>
				<div class="shell-form-grid">
					<div class="shell-form-group">
						<label for="fname">First name</label>
						<input type="text" class="shell-form-control" id="fname" name="fname" placeholder="First name" required>
					</div>
					<div class="shell-form-group">
						<label for="lname">Last name</label>
						<input type="text" class="shell-form-control" id="lname" name="lname" placeholder="Last name" required>
					</div>
				</div>
				<div class="shell-form-group">
					<label for="user">Username</label>
					<input type="text" class="shell-form-control" id="user" name="user" placeholder="Username" required>
				</div>
				<div class="shell-form-group">
					<label for="email">Email</label>
					<input type="email" class="shell-form-control" id="email" name="email" placeholder="Email" required>
				</div>
				<div class="shell-form-group">
					<label for="passf">Password</label>
					<input type="password" class="shell-form-control" id="passf" name="pass1" placeholder="Password" required minlength="8">
				</div>
				<div class="shell-form-group">
					<label for="passs">Confirm password</label>
					<input type="password" class="shell-form-control" id="passs" name="pass2" placeholder="Confirm password" required minlength="8">
					<span id="verify" class="shell-verify"></span>
				</div>
				<div class="shell-form-group">
					<label for="phone">Phone number</label>
					<div class="shell-form-row">
						<select class="shell-form-control" id="ph" aria-label="Country code">
							<option value="+977">+977</option>
							<option value="+1">+1</option>
							<option value="+44">+44</option>
							<option value="+91">+91</option>
							<option value="+61">+61</option>
						</select>
						<input type="number" class="shell-form-control" id="phone" name="phone" placeholder="Phone number" min="10000000" required>
					</div>
				</div>
				<div class="shell-form-group" style="margin-top: 0.5rem;">
					<button type="submit" class="shell-btn shell-btn--primary shell-btn--block" name="register">Register</button>
				</div>
				<div class="shell-form-footer">
					<p>Already a member? <a href="login.php">Sign in</a></p>
				</div>
			</form>
		</div>
	</div>
</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	$('#passf, #passs').on('keyup', function () {
		if ($('#passf').val() === $('#passs').val()) {
			$('#verify').text('Matching').css('color', '#16a34a');
		} else {
			$('#verify').text('Not matching').css('color', '#dc2626');
		}
	});
</script>

<?php include('includes/footer.php'); ?>
