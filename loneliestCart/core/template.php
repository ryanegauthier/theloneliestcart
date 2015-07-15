<?PHP
	global $zlcms;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="core/images/icons/favicon.png">
	<title>Title here</title>

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
						<img itemprop="logo" src="core/images/icons/logo.jpg" alt="Logo name">
						<meta itemprop="name" content="Church name">
					</a>
					<div class="navToggle"></div>
				</div>
				<div class="col-xs-12 col-sm-9">
					<nav>
						<ul>
							<li><a href="#">Home</a></li>
							<li><a href="#">Nav item 1</a></li>
							<li><a href="#">Nav item 2</a></li>
						</ul>							
					</nav>	
				</div>
			</div>
		</div>
	</header>

	<section class="carousel slide" id="presentationSlider">
		<div class="carousel-inner">
			<div class="item active" style="background: url(core/images/slideshow/slideshowEx1.jpg);">
				<div class="carousel-caption">
					<h1>Headline</h1>
					<p>Subtitle</p>	
					<a href="#" class="button">Slider Cta</a>
				</div>	
			</div>
			<div class="item" style="background: url(core/images/slideshow/slideshowEx2.jpg);">
				<div class="carousel-caption">
					<h1>Headline 2</h1>
					<p>Subtitle 2</p>	
					<a href="#" class="button">Slider Cta 2</a>
				</div>	
			</div>
		</div>
		
		<ol class="carousel-indicators">
			<li data-target="#presentationSlider" data-slide-to="0" class="active"></li>
			<li data-target="#presentationSlider" data-slide-to="1"></li>
		</ol>		
	</section>

	<!-- 3 column layout -->
	<section class="wrapper1">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-4">
					<h1>Third/full | Left column (h1)</h1>
				</div>
				<div class="col-xs-12 col-sm-4">
					<p>Third/full | Center column (p)</p>
				</div>
				<div class="col-xs-12 col-sm-4">
					<p>Third/full | Right column (p)</p>
				</div>
			</div>
		</div>
	</section>

	<!-- 4 column layout on desktop | 2 row/2 column on mobile | 100% on mobile-->
	<section class="wrapper2">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3">
					<h2>Quarter/half/full | Left column (h2)</h2>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<p>Quarter/half/full | Left Center column (p)</p>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<p>Quarter/half/full | Right Center column (p)</p>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<p>Quarter/half/full | Right column (p)</p>
				</div>
			</div>
		</div>
	</section>

	<!-- 2/3 | 1/3 layout-->
	<section class="wrapper3">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-8">
					<h3>Two-third/full | Left column (h3)</h3>
				</div>
				<div class="col-xs-12 col-sm-4">
					<p>One-third/full | Right column (p)</p>
				</div>

			</div>
		</div>
	</section>

	<!-- 1/3 | 2/3 layout-->
	<section class="wrapper4">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-4">
					<h4>One-third/full | Left column (h4)</h4>
				</div>				
				<div class="col-xs-12 col-sm-8">
					<p>Two-third/full | Right column (p)</p>
				</div>
			</div>
		</div>
	</section>
	
	<!-- 1/4 | 1/2 | 1/4 layout-->
	<section class="wrapper5">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<h5>One-quarter/full | Left column (h5)</h5>
				</div>				
				<div class="col-xs-12 col-sm-6">
					<p>Half/full | Center column (p)</p>
				</div>
				<div class="col-xs-12 col-sm-3">
					<p>One-quarter/full | Right column (p)</p>
				</div>				
			</div>
		</div>
	</section>
	
	<!-- full width -->
	<section class="wrapper6">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>Full width column (h1)</h1>
					<ul>
						<li>List item 1</li>
						<li>List item 2</li>
						<li>List item 3</li>
					</ul>
				</div>							
			</div>
		</div>
	</section>

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