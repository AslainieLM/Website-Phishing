<?php
if (!isset($page_title)) {
	$page_title = 'Phishing Website Detector';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Check websites for phishing threats using our database and machine learning model">
	<meta name="keywords" content="phishing, URL checker, website safety, phishing detection">
	<title><?php echo htmlspecialchars($page_title); ?></title>
	<link rel="stylesheet" href="./css/shell.css">
</head>
<body class="shell-body">
