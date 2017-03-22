<?php session_start(); //call or creates session??> 
<?php
	include( 'dbconnect.inc.php' );
	$pageTitle = "|  Login";
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
				$(document).on("submit","#loginform",function() {

				//random number to make each AJAX call appear unique
				var randNum = Math.floor(Math.random() *10000000);

				$.ajax({
					url: "mng_user.php?rand=" + randNum,
					dataType: 'text',
					type: 'POST',
					data: 'l_uname=' + $("[name='l_uname']").val() + '&l_pword=' + $("[name='l_pword']").val() + '&mode=login',
					beforeSend: function() {
						$('#loginload').html('loading');
			   },
			   complete: function() {
				 $('#loginload').html('');
			   },
			   success:function(result) {
				 $("#loginform").html('').append(result);
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
						<p class="section-title">
							<h2>Log in or register a new account.</h2>
						</p>

						<!-- one col -->

						<div class="col">
							<div class="login-page">
								<?php 
						if(isset($_SESSION['user_id'])) {
					?>
								<p>Welcome to the site:
									<a href="admin.php>">
										<?php echo $_SESSION['u_username'];?>
									</a> <br/><br/>
									<a href="logout.php">Logout</a>
								</p>

								<?php 
						} else { 
					?>

								<div style="margin-right: 60px;" class="contact-left">
									<h5 class="custom">Registered Users</h5>
									<!-- form -->
								<form id="loginform" action="mng_user.php" method="post">
									<fieldset class="input">
									  <p id="login-form-username">
										<label for="modlgn_username">USERNAME:</label>
										<input id="modlgn_username" type="text" name="l_uname" class="inputbox" size="18" autocomplete="off">
									  </p>
									  <p id="login-form-password">
										<label for="modlgn_passwd">PASSWORD:</label>
										<input id="modlgn_passwd" type="password" name="l_pword" class="inputbox" size="18" autocomplete="off">
									  </p>
										  <div class="remember">
											<input style="cursor:pointer;" type="submit" name="Submit" class="button" value="Login">
										  	<p id="login-form-remember">
											  <label for="modlgn_remember"><br /><a href="password.php">Forgotten Your Password?</a></label>
											</p>
											<div class="clear"></div>
											<input type="hidden" name="mode" value="login" />
										  </div>										
									</fieldset>
								</form>

									<!-- ENDS form -->
								</div>

								<div style="margin-left: 60px;" class="contact-left">
									<h5 class="custom">New Customers</h4>
            				<form action="register.php"><br /><p>If you're a new customer, please register with us which will enhance your shopping experience. The registration only takes a few minutes.<br />
            				</p>
            				<div class="button1"> 
								<br /><br/><br /><br /><input style="cursor:pointer;" type="submit" name="Submit" class="button" value="Create an Account">
              				</div>
								<div class="clear"></div>	</form>
						</div>
					<?php 
						}
					?>
          	</div>
          <div class="clear"></div>
        </div>
      
   
	
	
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
										<input type="text" name="query" id="query" placeholder="Search" />
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