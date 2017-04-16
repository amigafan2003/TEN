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

	//get all RSS feeds that have are trending today
	$response .= "<div id='rating'>";
	
	$result = mysqli_query($dbconnect,
							  	"SELECT (SUM(r_score) / COUNT(rss_id)) AS 'rating', rss_id 
								FROM `RATING` GROUP BY rss_id ORDER BY rating desc");
	
	while($resultRow = mysqli_fetch_array($result)) {
		$rating = $resultRow['rating'];
		//get all RSS feeds that have a subscription

		$rssId= $resultRow['rss_id'];
		$rssResult = mysqli_query($dbconnect,
									"SELECT *
									FROM `RSS` WHERE `rss_id` = $rssId");

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

			if ($rating > 4.75){
				$response .= "<div id='subrow' class='fivestars'>";
			} else if ($rating > 4.25){
				$response .= "<div id='subrow' class='fourstars'>";
			} else if ($rating > 3.75){
				$response .= "<div id='subrow' class='fourstars'>";
			} else if ($rating > 3.25){
				$response .= "<div id='subrow' class='threestars'>";
			} else if ($rating > 2.75){
				$response .= "<div id='subrow' class='threestars'>";
			} else if ($rating > 2.25){
				$response .= "<div id='subrow' class='twostars'>";
			} else if ($rating > 1.75){
				$response .= "<div id='subrow' class='twostars'>";
			} else if ($rating > 1.25){
				$response .= "<div id='subrow' class='onestars'>";
			} else if ($rating > 0.75){
				$response .= "<div id='subrow' class='onestars'>";
			} else if ($rating > 0.25){
				$response .= "<div id='subrow'  class='onestars'>";
			} else {
				$response .= "<div id='subrow' class='nil'>";
			}			
			
			//generate link and title for RSS Feed
			//Call function to extract feed thumb from RSS feed - RS 21/03/2017
			$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
			$response .= "<h2 style='margin:0px;'><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a>";

			if ($rating > 4.75){
				$response.=  "<img height='25px;' class='starrating' src='images/5stars.png'>";
			} else if ($rating > 4.25){
				$response.=  "<img height='25px;' class='starrating' src='images/4_5stars.png'>";
			} else if ($rating > 3.75){
				$response.=  "<img height='25px;' class='starrating' src='images/4stars.png'>";
			} else if ($rating > 3.25){
				$response.=  "<img height='25px;' class='starrating' src='images/3_5stars.png'>";
			} else if ($rating > 2.75){
				$response.=  "<img height='25px;' class='starrating' src='images/3stars.png'>";
			} else if ($rating > 2.25){
				$response.=  "<img height='25px;' class='starrating' src='images/2_5stars.png'>";
			} else if ($rating > 1.75){
				$response.=  "<img height='25px;' class='starrating' src='images/2stars.png'>";
			} else if ($rating > 1.25){
				$response.=  "<img height='25px;' class='starrating' src='images/1_5stars.png'>";
			} else if ($rating > 0.75){
				$response.=  "<img height='25px;' class='starrating' src='images/1stars.png'>";
			} else if ($rating > 0.25){
				$response.=  "<img height='25px;' class='starrating' src='images/0_5stars.png'>";
			} else {
				$response.=  "<span class='starrating' style='font-weight:100;'>Nil Rating!</span>";
			}
			$response .= "</h2>";
				
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
	
}	$response .= "</div>";


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