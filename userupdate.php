<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' );
$pageTitle = "|  Update Profile";

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
	$userResult = mysqli_query( $dbconnect,
		"SELECT * FROM `USER` WHERE user_id = $user_id" );

	while ( $row = mysqli_fetch_array( $userResult ) ) {
		$username = $row['u_username'];
		$firstname = $row['u_firstname'];
		$lastname = $row['u_lastname'];
		$emailaddress = $row['u_emailaddress'];
		$dob = $row ['u_dob'];
		$avatar = $row ['u_img_thumb'];
	}
}

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
							<form class="form-horizontal" method="post" enctype="multipart/form-data" action="mng_user.php">
								<div class="control-group">
									<div class="controls">
									  <label class="control-label">User Name</label>
										<input name="username" type="text" placeholder="username" value="<?php echo $username;?>"><br />
										<label class="control-label">First Name</label>
										<input name="firstname" type="text" placeholder="firstname" value="<?php echo $firstname;?>"><br />
										<label class="control-label">Last Name</label>
										<input name="lastname" type="text" placeholder="lastname" value="<?php echo $lastname;?>"><br />
										<label class="control-label">Email Address</label>
										<input name="emailaddress" type="text" placeholder="email" value="<?php echo $emailaddress;?>"><br />
										<label class="control-label">Date of Birth</label>
										<input name="dob" type="date" placeholder="date of birth" value="<?php echo $dob;?>"><br /><br />
										<img src="<?php echo $avatar ?>" width="60" height="60" alt=""/>
									  	<label class="control-label">Avatar image</label>
										<input type="file" name="avatar" />Images 250px by 250px or smaller work best.
										<br />
									</div>
								</div><br>
								&nbsp; &nbsp;
								<div class="form-actions">
									<button style="color:white; text-decoration:none;" type="submit" class="btn btn-success" value="Update"><a style="color:white;">
										<a style="color:white; text-decoration:none;" onclick="Update();">Save Changes</a>
									</button>

									<a class="btn" href="myprofile.php">Back</a>
								</div>
								<input type="hidden" name="action" value="update"/>
								<input type="hidden" name="id" value="<?php echo $user_id;?>"/>
							</form>
						<p>
							<?php
							if(isset($_SESSION['message'])) {
								if( $_SESSION['message'] != "") {
									echo $_SESSION['message'];
									$_SESSION['message'] = "";
								}
							}
							?>
						</p>
						<?php
						} else {
							?> You need to log in to view this page.<br><br>
							<?php
							if(isset($_SESSION['message'])) {
								if($_SESSION['message'] !="") {
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