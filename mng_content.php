<?php

session_start();

include( "dbconnect.inc.php" );

if ( $_POST[ 'action' ] == "insert" ) {

	//sanitise data for entry
	$title = mysqli_escape_string( $dbconnect,
		$_POST[ 'title' ] );
	$address = mysqli_escape_string( $dbconnect,
		$_POST[ 'address' ] );
	$category = mysqli_escape_string( $dbconnect,
		$_POST[ 'category' ] );
	$active = mysqli_escape_string( $dbconnect,
		$_POST[ 'active' ] );

	//insert query
	$insertSql = "INSERT INTO `RSS`
		(`title`,
			`address`,
			`category`, active)
VALUES
('{$title}',
	'{$address}',
	'{$category}',
	'{$active}')";
	$insertResult = mysqli_query( $dbconnect, $insertSql );

	if ( $insertResult ) {
		$_SESSION[ 'message' ] = "";
		$_SESSION[ 'message' ] = "RSS feed insertion successful.<br /><br /><a class='btn' href='comments.php?rssid=" .mysqli_insert_id( $dbconnect ). "'>Test your feed now!</a>  If it doesn't work, amend it, delete it or mark it as not active else the app will crash!";
		header( "location: create.php?id=" . mysqli_insert_id( $dbconnect ) );
	} else {
		$_SESSION[ 'message' ] = "";
		$_SESSION[ 'message' ] = "Insertion failed!";
		header( "location: create.php" );
	}

} else if ( $_GET[ 'action' ] == "delete" ) { //end insert
	$deleteQuery = "DELETE from `RSS`
	WHERE
	`rss_id`={$_GET['id']}";
	$deleteResult = mysqli_query( $dbconnect, $deleteQuery );

	if ( $deleteResult ) {
		$_SESSION[ 'message' ] = "RSS Feed deleted.";
	} else {
		$_SESSION[ 'message' ] = "Delete failed :-(";
	}
	header( "location: admin_feeds.php" );

} else if ( $_POST[ 'action' ] == "update" ) { //end delete


	//sanitise data for entry
	$rss_id = $_POST[ 'id' ];
	$title = mysqli_escape_string( $dbconnect,
		$_POST[ 'title' ] );
	$address = mysqli_escape_string( $dbconnect,
		$_POST[ 'address' ] );
	$category = mysqli_escape_string( $dbconnect,
		$_POST[ 'category' ] );
	$active = mysqli_escape_string( $dbconnect,
		$_POST[ 'active' ] );
	//update query

	$updateSql = "UPDATE `RSS`
		SET `title`='{$title}',
		`address`='{$address}',
		`category`='{$category}',
		`active`='{$active}'";

	$updateSql .= " WHERE `rss_id`={$rss_id}";
	$updateResult = mysqli_query( $dbconnect, $updateSql );

	if ( $updateResult ) {
		//header("location: detail.php?id=" . $_POST['p_id'])
		$_SESSION[ 'message' ] = "RSS feed updated.<br /><br /><a class='btn' href='comments.php?rssid=" .$rss_id. "'>Test your feed now!</a>  If it doesn't work, amend it, delete it or mark it as not active else the app will crash!";
		header( "location: update.php?id=" . $rss_id );
	} else {
		$_SESSION[ 'message' ] = "RSS feed could not be updated.";
		header( "location: update.php?id=" . $rss_id );
	}

} //end update

?>