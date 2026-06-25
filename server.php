<?php

$db = mysqli_connect('localhost', 'root', 'admin', 'phishing_db');

$feedback_message = '';
$feedback_class = '';

if (isset($_POST['send']) && $db) {
	$rate = mysqli_real_escape_string($db, $_POST['experience']);
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$comment = mysqli_real_escape_string($db, $_POST['comments']);

	$query = "INSERT INTO user_feedback (rate, name, email, comment)
		VALUES('$rate', '$name', '$email', '$comment')";

	if (mysqli_query($db, $query)) {
		$feedback_message = 'Thank you — your feedback has been submitted.';
		$feedback_class = 'shell-alert--safe';
	} else {
		$feedback_message = 'Something went wrong. Please try again.';
		$feedback_class = 'shell-alert--danger';
	}
}

?>
