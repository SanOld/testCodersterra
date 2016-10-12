<!DOCTYPE html>
<html lang="en">
<head>

	<?php include('head.php'); ?>
</head>
<body class="bg" ng-app="spi">
<div id="page">
	<header class="top-head container-fluid">
		<div class="container">
			<div class="logo p-0 m-t-10 m-b-10 col-lg-6">
				<a href="/"><img src="images/logo.png" alt="logo"></a>
			</div>
			<div class="logo p-0 m-t-20 m-b-15 col-lg-6">
				<a target="_blank" href="api/login" class="pull-right">
					<img src="images/logo2.png" alt="logo">
				</a>
			</div>
		</div>
	</header>

	<div class="pace pace-inactive">
		<div data-progress="99" data-progress-text="100%" style="transform: translate3d(100%, 0px, 0px);" class="pace-progress">
			<div class="pace-progress-inner"></div>
		</div>
		<div class="pace-activity"></div>
	</div>

	<?php echo $content; ?>

</div>
<div class="footer">
	<div class="container">
		<div class="col-lg-12">
			<a target="_blank" href="" class="pull-right m-l-15">
				<img src="images/logo3.png" alt="logo">
			</a>
		</div>
	</div>
</div>

</body>
</html>