<?php
header('Access-Control-Allow-Origin: *');
include("dbconnect.inc.php");

$action = $_GET['action'];

$callback = $_GET['callback'];

echo $callback . "(";

$response = "";

//view existing subscriptions
if($action=='select') {

	//for students
	//TO-DO: Validation checks for user_id
	//TO-DO: Error checking for queries

	//get userid
	$userId = $_GET['user_id'];



	//get all RSS feeds
	$rssResult = mysqli_query($dbconnect,
								"SELECT *
								FROM `RSS`
								INNER JOIN `SUBSCRIPTION`
								ON `SUBSCRIPTION`.`rss_id` = `RSS`.`rss_id`
								WHERE `SUBSCRIPTION`.`user_id`={$userId}");
	
	$rowsRet = mysqli_num_rows($rssResult);
	if ($rowsRet == 0) {
		
		$response .= "<h3>You have no current subscriptions.<h3><br />Click <a href='feeds.php'>here</a> to add some.";
		
	}
	
	//loop through RSS feeds
	while($rssRow = mysqli_fetch_array($rssResult)) {
			
			$response .= "<div class='subrow'>";

			//generate link and title for RSS Feed
			$response .= "<h2 style='display:inline'><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></h2>";


			$response .= "<div class='sublinkbar'><a href='#' class='sublink' action='unsubscribe' rssid='" . $rssRow['rss_id'] . "' >Unsubscribe</a></div><br />";


			$response .= "</div>";
	}
}


if($action=='unsubscribe') {

	//For students
	//TO-DO:validation on rss / user id

	//get vars from POST
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];

	//delete query
	$result = mysqli_query($dbconnect,
							"DELETE FROM `SUBSCRIPTION`
							WHERE  `user_id`='{$userId}'
							AND `rss_id`='{$rssId}'");

	//Prefixes for SUCCESS / FAIL to be checked in jQuery
	if($result) {
		$response .= "SUCCESS:<p>You have unsubscribed!</p>";	
	} else {
		$response .= "FAIL:<p>Unsubscription failed, please try again.<p>";		
	}
}

$array = array("response" => $response);

echo json_encode($array);

echo ")";?>