<?php
header('Access-Control-Allow-Origin: *');
include("dbconnect.inc.php");

$action = $_GET['action'];

$callback = $_GET['callback'];

echo $callback . "(";

$response = "";

if($action=="select") {

	//Trending functionality - write entry to TREND table everytime an RSS feed is clicked on
	//get vars
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];

	$result = mysqli_query($dbconnect,
							"INSERT INTO `TREND`
							(`user_id`,`rss_id`,`t_date`)
							VALUES
							({$userId},{$rssId},NOW())");
	//Need to work out some kind of error logging to file where fails are invisible to the user
	//if($result) {
	//	$response.=  "SUCCESS:<p>Comment added</p>";
	//} else {
	//
	//	$response.=  "FAIL:<p>Problem adding comment</p>";
	//}
	
	//TO-DO: Format date / time

	//get vars
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];

	//get all comments
	$result = mysqli_query($dbconnect,
							"SELECT *
							FROM `COMMENT`
							WHERE `rss_id`={$rssId}
							ORDER BY `date_posted` DESC");

	if($result) {
		//prefix for checking
		$response.= "SUCCESS:";
		//loop through comment rows
		while($row = mysqli_fetch_array($result)) {

			//display comment content
			$response.=  "<br><br><div class='comment' comuser='" . $row['user_id'] . "' comid='" . $row['comment_id'] . "'>";
			
			//Get username - added by RS  12/04/2017
			$usernameResult = mysqli_query($dbconnect,
							"SELECT u_username
							FROM `USER`
							WHERE `user_id`={$row['user_id']}");
			
			$usernameRow = mysqli_fetch_assoc($usernameResult);

			$response.= $row['content'];
			$response.= "</div>";
			$response.= "<br><div><a href='profile.php?username=" . $usernameRow['u_username'] .  "'>" . $usernameRow['u_username'] . "</a></div>";
			//display date / time
			$response.=  "<div class='comdate'>";
			$response.=  $row['date_posted'];
			$response.=  "</div>";
						//if comment by current user, show delete and update link
			if($userId==$row['user_id']) {
				$response.=  "<div class='comlinks'>";
				$response.=  "<a href='#' class='updatecom' action='update' comid='" . $row['comment_id'] . "'>Update?</a> | ";
				$response.=  "<a href='#' class='deletecom' action='delete' comid='" . $row['comment_id'] . "''>Delete?</a>";
				$response.=  "</div>";
			}

		}

	} else {
		$response.=  "FAIL:<p>Problem loading comments</p>";
	}

}

if($action=="update") {

	//TO-DO: validation checks
	//get vars
	$commentId = $_GET['comment_id'];
	$commentContent = urldecode($_GET['comment_content']);

	$result = mysqli_query($dbconnect,
							"UPDATE `COMMENT`
							SET `content`='{$commentContent}'
							WHERE `comment_id`={$commentId}");

	if($result) {
		$response.=  "SUCCESS:<p>Comment updated</p>";
	} else {

		$response.=  "FAIL:<p>Problem updating comment</p>";
	}

}

if($action=="insert") {

	//TO-DO: validation checks

	//get vars
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];
	$commentContent = urldecode($_GET['comment_content']);

	$result = mysqli_query($dbconnect,
							"INSERT INTO `COMMENT`
							(`user_id`,`rss_id`,`content`,`date_posted`)
							VALUES
							({$userId},{$rssId},'{$commentContent}',NOW())");
	if($result) {
		$response.=  "SUCCESS:<p>Comment added</p>";
	} else {

		$response.=  "FAIL:<p>Problem adding comment</p>";
	}

}

if($action=="delete") {

	//TO-DO: validation checks

	//get vars
	$commentId = $_GET['comment_id'];

	$result = mysqli_query($dbconnect,
							"DELETE FROM `COMMENT`
							WHERE `comment_id`={$commentId}");

	if($result) {

		$response.=  "SUCCESS:<p>Comment deleted</p>";
	} else {

		$response.=  "FAIL:<p>Problem deleting comment</p>";
	}

}

$array = array("response" => $response);

echo json_encode($array);

echo ")";
