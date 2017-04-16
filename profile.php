<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' );
$pageTitle = "|  Profile";

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
							if ( isset( $_SESSION[ "user_id" ] ) ) {
									?>
							<div style="margin-left:50px; float:left; display:inline-block;">
								<?php
								$username = $_GET['username'];
								$userResult = mysqli_query( $dbconnect,
									"SELECT * FROM `USER` WHERE u_username = '$username'" );
								$userRow = mysqli_fetch_assoc( $userResult );
								?>
									<!-- Profile image retrieved from $useRow within the database column of u_img_main.-->
									<img src="<?php echo $userRow['u_img_main']; ?>">
								<?php
									echo '<p>Username:<br>' . $userRow[ 'u_username' ] . '</p>';
									echo '<p>First Name:<br>'.$userRow['u_firstname']. '</p>';
									echo '<p>Surname:<br>' .$userRow['u_lastname']. '</p>';
									echo '<p>Email:<br>' .$userRow['u_emailaddress']. '</p>';
									echo '<p>Date of Birth:<br>' .$userRow['u_dob']. '</p>';
								?>
							</div>
							<div style="margin-right:50px; float:right; display:inline-block;">
								<h3>Comments by this user:</h3>
								<?php
								$userComResult = mysqli_query( $dbconnect,
									"SELECT * FROM `COMMENT` WHERE user_id = {$userRow['user_id']}" );
								while ($userComments = mysqli_fetch_array( $userComResult )) {
									//Get RSS feed title
									$rssResult = mysqli_query( $dbconnect,
										"SELECT * FROM `RSS` WHERE rss_id = {$userComments['rss_id']}" );
									while ($rssRow = mysqli_fetch_array( $rssResult )) {
										echo '<p>On feed <a href=comments.php?rssid="' . $rssRow['rss_id'] . '">' . $rssRow['title'] . '</a></p>';
									}									
									echo '<p>' . $userComments['content'] . '</p>';	
									echo '<p>' . $userComments['date_posted'] . '</p><br>';
									
								}
								?>
							</div>
							<p>
							<?php
								if(isset($_SESSION['message'])) {
									if($_SESSION['message'] != "") {
										echo $_SESSION['message'];
										$_SESSION['message']= "";
									}
								}
								?>
							</p>
							<?php
							} else {
								?> You need to log in to view this page.<br><br>
								<?php
								if(isset($_SESSION['message'])) {
									if($_SESSION['message'] != "") {
										echo $_SESSION['message'];
										$_SESSION['message'] = "";
									}
								}
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

				<?php include('nav.inc.php'); ?>

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