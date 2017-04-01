<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' );
$pageTitle = "|  Manage RSS Feeds";

$title = mysqli_query( $dbconnect,
	"SELECT `title` 
		 FROM `rss`"
);

$address = mysqli_query( $dbconnect,
	"SELECT `address` 
		 FROM `rss`"
);
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
	<script>
		function confirmChoice( rssId ) {
			response = confirm( "Are you sure you want to delete this RSS Feed?" );
			if ( response == 1 ) {
				window.location = "mng_content.php?action=delete&id=" + rssId;
			} else {
				return false
			}
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

				<?php	include('header.inc.php'); ?>

				<!-- Banner -->
				<section id="banner">
					<div id="main">
							<?php
							if ( isset( $_SESSION[ "u_level" ] ) ) {
								if ( $_SESSION[ "u_level" ] == "admin" ) {
									?>
							<div class="row">

								<table style="overflow-x:auto;" class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Feed Name</th>
											<th>Feed URL</th>
											<th>Category</th>
											<th>Feed active</th>
											<th>Options</th>
										</tr>
									</thead>
									<tbody>
										<?php

										$rssResult = mysqli_query( $dbconnect,
											"SELECT *
													FROM `RSS` order by title asc" );
										while ( $rssRow = mysqli_fetch_array( $rssResult ) ) {
											echo '<tr>';
											echo '<td>' . $rssRow[ 'title' ] . '</td>';
											echo '<td>' . $rssRow[ 'address' ] . '</td>';
											echo '<td>' . $rssRow[ 'category' ] . '</td>';
											if ($rssRow[ 'active' ] == 1) {
												echo '<td>Active</td>';														
											} else {
												echo '<td>Not active</td>';												
											}
											echo '<td style="min-width:240px">';
											echo '<a style="display:inline-block;" class="btn" href="comments.php?rssid=' . $rssRow[ 'rss_id' ] . '">Read</a>';
											echo ' ';
											echo '<a style="display:inline-block;" class="btn btn-success" href="update.php?id=' . $rssRow[ 'rss_id' ] . '">Update</a>';
											echo ' ';
											echo '<a style="display:inline-block;" class="btn btn-danger" href="javascript:confirmChoice(' . $rssRow[ 'rss_id' ] . ')">Delete</a>';
											echo '</td>';
											echo '</tr>';
										}

										?>
									</tbody>
								</table>
								<p style="style=" background-color: "#337ab7;">
									<a href="create.php" class="btn btn-success">Create</a>
								</p>
							</div>
							<?php
								if ( $_SESSION[ 'message' ] != "" ) {
									echo $_SESSION[ 'message' ];
									$_SESSION[ 'message' ] = "";
								}
							?>
							<?php
							} else {
								?> You do not have adminstrator privaleges to view this page.
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