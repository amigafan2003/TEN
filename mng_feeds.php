<?php
header('Access-Control-Allow-Origin: *');
include("dbconnect.inc.php");

//Function to get Feed image url  - RS 21/03/2017
function getThumb ($address) {
	
	//Get external RSS feed and input into string 
	$ch = curl_init();  

	curl_setopt($ch,CURLOPT_URL,$address);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	$feed = curl_exec($ch);

	curl_close($ch);
	 
	//Parse xml 
	$thumb = "";
	
	$rss = simplexml_load_string($feed);
	$thumb = $rss->channel->image->url;
	
	//If no thumb found, use RSS.png placeholder
	if ($thumb == "") {
		$thumb = "images/rss.png";
	}
	
	//Return thumb 
	return $thumb;
}

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
								FROM `RSS` WHERE `active` = '1' order by title asc");
	
	//flag to see if match with user (if they are subscribed)
	$matchFlag = false;

	//loop through RSS feeds
	while($rssRow = mysqli_fetch_array($rssResult)) {
			

			//get user linked subscriptions
			$userResult = mysqli_query($dbconnect,
							"SELECT *
							FROM `RSS` 
							INNER JOIN `SUBSCRIPTION`
							ON `SUBSCRIPTION`.`rss_id` = `RSS`.`rss_id`
							WHERE `SUBSCRIPTION`.`user_id`={$userId} AND `RSS`.`active`='1'");

			//this nested loop is for the subscription query
			while($subRow = mysqli_fetch_array($userResult)) {

				//check if the address matches between the two
				//(could check title, however rss_id would be ambiguous
				//as it appears in two tables)
				$firstTableAddress = $rssRow['address'];
				$secondTableAddress = $subRow['address'];

				if($firstTableAddress==$secondTableAddress) {
					$matchFlag = true;
				}
			}

			//Edited to allow filtering on category - added RS 04/04/2017
			$response .= "<div id='subrow' class='subrow " . $rssRow['category'] . "'>";


			//generate link and title for RSS Feed
			
			//Call function to extract feed thum from RSS feed - RS 21/03/2017
		    $response .= "<div style='float:right; width:200px;>'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
		    $response .= "<h2 style='display:inline'><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></h2>";
			
			//if the flag is set, we are subscribed so do unsubscribe link
			//otherwise do subscribe link
			if($matchFlag) {

				$response .= "<div class='sublinkbar'><a href='#' class='sublink' action='unsubscribe' rssid='" . $rssRow['rss_id'] . "' >Unsubscribe</a></div><br />";

			} else {

				$response .= "<div class='sublinkbar'><a href='#' class='sublink' action='subscribe' rssid='" . $rssRow['rss_id'] . "' >Subscribe</a></div><br />";

			}

			$response .= "</div>";
			
			$matchFlag = false;
	}
}


if($action=='subscribe') {

	//For students
	//TO-DO:validation on rss / user id

	//get vars from POST
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];

	//insert query
	$result = mysqli_query($dbconnect,
							"INSERT INTO `SUBSCRIPTION`
							(`user_id`,`rss_id`)
							VALUES
							('{$userId}','{$rssId}')");

	//Prefixes for SUCCESS / FAIL to be checked in jQuery
	if($result) {
		$response .= "SUCCESS:<p>You have subscribed!</p>";	
	} else {
		$response .= "FAIL:<p>Subscription failed, please try again.<p>";
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