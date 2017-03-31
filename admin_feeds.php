<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' );
$pageTitle = "|  Manage RSS Feeds";

$title = mysqli_query($dbconnect,
		"SELECT `title` 
		 FROM `rss`"
	);

$address = mysqli_query($dbconnect,
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

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

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
						<p class="section-title">
							  <div class="row">
				                <h3></h3>
				            		</div>

				           			<div class="row">

				                <table class="table table-striped table-bordered">
				                  <thead>
				                    <tr>
				                      <th>Feed Name</th>
				                      <th>Feed URL</th>
				                      <th>Category</th>
				                      <th>Options</th>
				                    </tr>
				                  </thead>
				                  <tbody>
				                  <?php
				                  	require 'database.php';
				                   $pdo = Database::connect();
				                   $sql = 'SELECT * FROM customers  ORDER BY id DESC';
				                   foreach ($pdo->query($sql) as $row) {
				                            echo '<tr>';
				                            echo '<td>'. $row['name'] . '</td>';
				                            echo '<td>'. $row['email'] . '</td>';
				                            echo '<td>'. $row['mobile'] . '</td>';
				                            echo '<td width=250>';
				                            echo '<a class="btn" href="read.php?id='.$row['id'].'">Read</a>';
				                            echo ' ';
				                            echo '<a class="btn btn-success" href="update.php?id='.$row['id'].'">Update</a>';
				                            echo ' ';
				                            echo '<a class="btn btn-danger" href="delete.php?id='.$row['id'].'">Delete</a>';
				                            echo '</td>';
				                            echo '</tr>';
				                   }
				                   Database::disconnect();
				                  ?>
				                  </tbody>
				            </table>
				            <p style="style="background-color:"#337ab7;">
				              <a href="create.php" class="btn btn-success">Create</a>
				            </p>
				        </div>
						</p>
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