<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' );
$pageTitle = "|  Create RSS Feed";

?>
<!DOCTYPE HTML>

<html>

<head>
	<title>TEN</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
	<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
	<link rel="stylesheet" href="assets/css/main.css"/>
	<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</head>

<body>

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Main -->
		<div id="main">
			<div class="inner">

				<!-- Header -->

				<?php	include('header.inc.php'); ?>

				<!-- Banner -->
				<section id="banner">
					<div id="main">

							<?php
							if ( isset( $_SESSION[ "u_level" ] ) ) {
								if ( $_SESSION[ "u_level" ] == "admin" ) {
									?>
							<form class="form-horizontal" action="mng_content.php" method="post" style="width:100%;">
								<div class="control-group">
									<label class="control-label">RSS Feed Title</label>
									<div class="controls">
										<input name="title" type="text" placeholder="title" value="">
									</div>
								</div>
								&nbsp;
								<div class="control-group">
									<label class="control-label">RSS Feed URL </label>
									<div class="controls">
										<input name="address" type="text" placeholder="RSS Feed URL" value="">
									</div>
								</div>
								&nbsp;
								<div class="control-group">
									<label class="control-label">Category</label>
									<div class="controls">
										<select name="category">
											<option value="Business">Business</option>
											<option value="Finance">Finance</option>
											<option value="General">General</option>
											<option value="News">News</option>
											<option value="Science">Science</option>
											<option value="Sport">Sport</option>
											<option value="Technology">Technology</option>
											<option value="Trivia">Trivia</option>
										</select>
									</div>
								</div><br>
								<div class="control-group">
									<label class="control-label">RSS Feed Active</label>
									<div class="controls">
										<input type="radio" name="active" value="1">Active<br>
										<input type="radio" name="active" value="0" checked>Not active<br>
									</div>
								</div>						
								&nbsp; &nbsp;
								<div class="form-actions">
									<button style="color:white; text-decoration:none;" type="submit" class="btn btn-success" value="Update"><a style="color:white;">
										<a style="color:white; text-decoration:none;">Create</a>
									  </button>
								

									<a class="btn" href="admin_feeds.php">Back</a>
								</div>
								<input type="hidden" name="action" value="insert"/>
							</form>

						
						<p>
							<?php
							if ( $_SESSION[ 'message' ] != "" ) {
								echo $_SESSION[ 'message' ];
								$_SESSION[ 'message' ] = "";
							}
							?>
						</p>
						<?php
						} else {
							?> You do not have adminstrator privileges to view this page.
						<?php
						}
						} else {
							?> You need to log in to view this page.
						<?php
						}
						?>

					</div>
				</section>

			</div>
		</div>

		<!-- Sidebar -->
		<div id="sidebar">
			<div class="inner">

				<!-- Search -->
				<section id="search" class="alt">
					<form method="post" action="#">
						<input type="text" name="query" id="query" placeholder="Search"/>
					</form>
				</section>

				<!-- Menu -->

				<?php	include('nav.inc.php'); ?>

				<!-- Footer -->
				<footer id="footer">
					<p class="copyright">&copy; CPC 2017. All rights reserved.</p>
				</footer>

			</div>
		</div>

	</div>

	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

</body>

</html>