<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en-ca">
<head>
<meta charset="utf-8">
<title>The Happiness Center</title>

<script src="scripts/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="scripts/jquery.cycle.lite.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#myslides').cycle({
		fit: 1, pause: 1, timeout: 4000
	});
});
</script>
<!--[if lt IE 9]> <script src="html5shiv.js"></script> <![endif]-->
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php include('header.php'); ?>
<section>
<?php include('navigation.php'); ?>
