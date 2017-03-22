<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' ); $pageTitle = "|  Feeds";?>
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
	<script type="text/javascript" src="assets/js/functions.js"></script>
	<script type="text/javascript">
		var userid = 0;

		$( document ).ready( function () {
			userid = getUrlParameter( "userid" );

			selectSubscriptions();

			$( document ).on( "click", '.sublink', function ( event ) {
				//alert($(this).attr("rssid"));

				rssid = $( this ).attr( "rssid" );
				action = $( this ).attr( "action" );

				$.ajax( {
					beforeSend: function () {
						$( "#loading" ).show();
					},
					complete: function () {
						$( "#loading" ).hide();
					},
					type: 'GET',
					dataType: "jsonp",
					jsonp: "callback",
					url: "mng_feeds.php?action=" + action + "&user_id=" + userid + "&rss_id=" + rssid,
					success: function ( data ) {

						responseString = "";

						$.each( data, function ( index, item ) {
							// Use item in here
							responseString = item;
						} );

						if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

							//get rest of data after prefix (LOGGEDIN:)
							//the number is the character position to start from, we cut off the prefix
							$( "#messages" ).html( responseString.substring( 8 ) );

							selectSubscriptions();

						}
						if ( responseString.indexOf( "FAIL" ) > -1 ) {

							//get rest of data after prefix (NOTFOUND:)
							//the number is the character position to start from, we cut off the prefix
							$( "#messages" ).html( responseString.substring( 5 ) );

						}

					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						if ( jqXHR.status == 500 ) {
							$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
						} else {
							$( "#messages" ).html( 'Unexpected error.' );
						}
					}
				} );

				return false;
			} );

			$( document ).on( "click", '.rsslink', function ( event ) {
				location.href = "comments.php?userid=" + userid + "&rssid=" + $( this ).attr( "rssid" );
				return false;
			} );

		} );

		function selectSubscriptions() {

			$( "#subcontent" ).html( "" );

			$.ajax( {
				beforeSend: function () {
					$( "#loading" ).show();
				},
				complete: function () {
					$( "#loading" ).hide();
				},
				type: 'GET',
				dataType: "jsonp",
				jsonp: "callback",
				url: "mng_feeds.php?action=select&user_id=" + userid,
				success: function ( data ) {

					responseString = "";

					$.each( data, function ( index, item ) {
						// Use item in here
						responseString = item;
					} );

					$( "#subcontent" ).html( responseString );

				},
				error: function ( jqXHR, textStatus, errorThrown ) {
					if ( jqXHR.status == 500 ) {
						$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
					} else {
						$( "#messages" ).html( 'Unexpected error.' );
					}
				}
			} );



		}
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
						<div id="messages">

						</div>
						<div id="subcontent">

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