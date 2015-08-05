<?PHP
	global $controller;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="core/images/icons/favicon.png">
	<title>The Loneliest Cart</title>

	<meta name="description" content="">
	<meta name="keywords" content="">
	
	<link href="core/styles/bootstrap.min.css" rel="stylesheet">
	<link href="core/styles/custom.css" rel="stylesheet">
	<link href="core/styles/customMedia.css" rel="stylesheet">
	<!--[if lte IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
</head>
<body itemscope itemtype="http://schema.org/Church">

	<header>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<a href="/" class="brand">
<!--
						<img itemprop="logo" src="core/images/icons/logo.png" alt="Logo name">
						<img itemprop="logo" src="core/images/icons/logo2.png" alt="Logo name">
-->
						<img itemprop="logo" src="core/images/icons/logo3.png" alt="Logo name">
						<meta itemprop="name" content="Church name">
					</a>
					<div class="navToggle"></div>
				</div>
				<div class="col-xs-12 col-sm-9">
					<nav>
						<ul>
<!-- 							<li><a href="#">Home</a></li> -->
							<li><a href="#">Nav item 1</a></li>
							<li><a href="#">Nav item 2</a></li>
						</ul>							
					</nav>	
				</div>
			</div>
		</div>
	</header>


<?php $controller->content->select_template(); ?>

	<footer>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					Footer content here
				</div>
			</div>
		</div>
	</footer>

	<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
	<script src="core/includes/bootstrap.min.js"></script>
	<script src="core/includes/javascript.js"></script>
</body>
</html>