<?php session_start(); //call or creates session??> 
<?php
include( 'dbconnect.inc.php' );
$pageTitle = "|  Register";
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
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  			crossorigin="anonymous"></script>
  		<script type="text/javascript">
			  $(document).ready(function() {

			//when loginform submitted (live event)
				$(document).on("submit","#registrationform",function() {

				//random number to make each AJAX call appear unique
				var randNum = Math.floor(Math.random() *10000000);

				$.ajax({
					url: "mng_user.php?rand=" + randNum,
					dataType: 'text',
					type: 'POST',
					data: 'r_uname=' + $("[name='r_uname']").val() + '&r_pword1=' + $("[name='r_pword1']").val() + '&r_pword2=' + $("[name='r_pword2']").val() + '&reg_code=' + '&mode=register',
					beforeSend: function() {
						$('#loginload').html('loading');
			   },
			   complete: function() {
				 $('#loginload').html('');
			   },
			   success:function(result) {
				 $("#registrationform").html('').append(result);
				 if( result.indexOf('match') >=0 || result.indexOf('password') >= 0){
					// Found "uname and pword don't match" or "uname and password must be entered" errors, i.e. not successfully logged in - do not update header login panel
				} else {  //Log in successful - update header panel login
				 window.location.reload();
				}
			   }

			 });
				return false;  //stops form submitting
			});
			  });
		</script>
</head>

<body>

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Main -->
		<div id="main">
			<div class="inner">

				<!-- Header -->

				<?	include('header.inc.php'); ?>

				<!-- Banner -->
				<section id="banner">
					<div id="main">
						<p class="section-title"><h2>Registration - It only takes a second.</h2>
						</p>

						<div class="contact-left">
							<h5 class="custom">YOUR DETAILS:</h5>
							<!-- form -->

							<form id="registrationform" action="mng_user.php" method="post">
								<fieldset class="input">
								  <p id="login-form-username">
									<label>USERNAME:</label>
									<input type="text" name="r_uname" class="inputbox" size="18" autocomplete="off">
								  </p>
								  <p id="login-form-password">
									<label>PASSWORD:</label>
									<input type="password" name="r_pword1" class="inputbox" size="18" autocomplete="off">
									</p>
								  <p id="login-form-password">
									<label>PASSWORD AGAIN:</label>
									<input type="password" name="r_pword2" class="inputbox" size="18" autocomplete="off">
								  </p>
									  <div class="remember">
										<br /><input style="cursor:pointer;" type="submit" name="Submit" class="button" value="Register Now!">
										<div class="clear"></div>
										<input type="hidden" name="mode" value="register" />
									  </div>										
								</fieldset>
								<?php
								if($_SESSION['message']!="") {
									echo '<br />'.$_SESSION['message']; 
									$_SESSION['message']="";
								}
							 ?>
							</form>
							<!-- ENDS form -->
						</div>
						<?php
						if ( ( $_SESSION[ 'message' ] != "" )AND( strpos( $_SESSION[ 'message' ], 'Registration' ) !== false ) ) {
							echo $_SESSION[ 'message' ];
							$_SESSION[ 'message' ] = "";
							?>
						<script>
							document.getElementById( 'registrationform' ).style.display = 'none;';
						</script>
						<?
					}
				 ?>

						<!-- one col -->




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

				<?	include('nav.inc.php'); ?>

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