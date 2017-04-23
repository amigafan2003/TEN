<?php session_start(); //call or creates session??> 
<?php
	include( 'dbconnect.inc.php' );
	$pageTitle = "|  Home";
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
	<script type="text/javascript" src="assets/js/jquery-2.2.3.min.js"></script>
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/js/functions.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

	<script>
		$(document).ready(function(){
		  // Add smooth scrolling to all links
		  $("a").on('click', function(event) {

			// Make sure this.hash has a value before overriding default behavior
			if (this.hash !== "") {
			  // Prevent default anchor click behavior
			  event.preventDefault();

			  // Store hash
			  var hash = this.hash;

			  // Using jQuery's animate() method to add smooth page scroll
			  // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
			  $('html, body').animate({
				scrollTop: $(hash).offset().top
			  }, 800, function(){

				// Add hash (#) to URL when done scrolling (default click behavior)
				window.location.hash = hash;
			  });
			} // End if
		  });
			
			// validate contact form - added by JM 07/04/2017
			$( function () {

				//Validate form
				$( '#contactform' ).validate( {
					rules: {
						name: {
							required: true,
							minlength: 2
						},
						email: {
							required: true,
							email: true
						},
						message: {
							required: true,
							minlength: 10
						},
					},
					messages: {
						name: {
							required: "Come on, you have a name don't you?",
							minlength: "Your name must consist of at least 2 characters"
						},
						email: {
							required: "No email, no message"
						},
						message: {
							required: "Um...yea, you have to write something to send this form.",
							minlength: "Thats all? Really?"
						},
					},


					//Submit form		
					submitHandler: function () {
						$.ajax( {
							type: "POST",
							data: $( '#contactform' ).serialize(),
							url: "process.php",
							//If form submitted successfully sdisable submit button and show success message
							success: function () {
								$( '#contact :input' ).attr( 'disabled', 'disabled' );
								$( '#success' ).fadeIn();
							},
							//If form NOT submitted successfully show error message
							error: function () {
								$( '#contact' ).fadeTo( "slow", 0.15, function () {
									$( '#error' ).fadeIn();
								} );
							}
						} );
					}
				} );
			} );

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
					<div class="content">
						<header>
							<h1>Hi, I’m TEN<br />
											</h1>
							<p>I'm here to service all your RSS feed aggregation requirements.</p>
						</header>
						<p>You'll need to login to your account use my services, so why not do that right away or register for an account if you don't already have one?</p>
						<ul class="actions">
							<li><a href="login.php" class="button big">Login or register</a>
							</li>
						</ul>
					</div>				
					<br><br>
					<span class="image object">
										<img src="images/TEN.png" alt="" />
									</span>

				</section>				
				<br><br>
				<!-- Section -->
				<section id="about">
					<header class="major">
						<h2>About</h2>
					</header>
					<div class="features">
						<article>
							<span class="icon fa-diamond"></span>
							<div class="content">
								<h3>Browse our RSS feeds</h3>
								<p>We've collected all the best RSS feeds out there, so take a look, decide what feeds interest you and then subscribe to them, where they will then show in your My Subscriptions page.</p>
							</div>
						</article>
						<article>
							<span class="icon fa-paper-plane"></span>
							<div class="content">
								<h3>Comment and rate</h3>
								<p>Comment on our feeds, highlight interesting articles to other users or rate our feeds so others can see what might interest them.  Share the knowledge!</p>
							</div>
						</article>
						<article>
							<span class="icon fa-rocket"></span>
							<div class="content">
								<h3>Power to you</h3>
								<p>You can filter feeds by category or rating, only showing the feeds you might be interested in, then you can subscribe to those feeds for your own persistent personalised view.</p>
							</div>
						</article>
						<article>
							<span class="icon fa-signal"></span>
							<div class="content">
								<h3>See what's trending</h3>
								<p>See the feeds that are trending today or were trending yesterday, last week or last month.  See the feeds with the most comments or the most subscribers.</p>
							</div>
						</article>
					</div>
				</section>
				<br><br>
					<!-- Section -->
				<section id="contact">
					<header class="major">
						<h2>Contact</h2>
					</header>
					<!-- left-content -->
					<div class="contact">
						<div class="contact-left">
							<h5 class="custom">Use the form below to send us your comments</h5>
							<!-- form -->
							<form id="contactform" name="contactform" method="post">
								<fieldset>
									<p>
										<label>NAME:</label>
										<input name="name" id="name" type="text" required/>
									</p>
									<p>
										<label>EMAIL:</label>
										<input name="email" id="email" type="text" required/>
									</p>
									<p>
										<label>MESSAGE:</label>
										<textarea name="message" id="message" rows="5" cols="20" required></textarea>
									</p>

									<p><input id="submit" type="submit" name="submit" value="Send Message"/>
									</p>
									<!--Success or fail for email send from AJAX - added JM 07/04/2017-->
									<div id="success">
										<span>Your message was sent succssfully!</span>
									</div>
									<div id="error">
										<span>Something went wrong, try refreshing and submitting the form again.</span>
									</div>
								</fieldset>
							</form>
							<!-- ENDS form -->
						</div>
						<!-- ENDS left-content -->

						<!-- right-content -->
						<div class="contact-right">
							<h4 class="custom">Location map</h4>
							<div><span></span><em></em><img class="map" src="images/map.png" alt="map"/></div>
							<p>Phone: 01253 352352<br/>
							 <br/>
							 Bispham Campus, Ashfield Rd, Blackpool FY2 0HB</p>
						</div>
					</div>
					<br><br><br><br>
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


</body>

</html>
