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
								FROM `RSS`
								INNER JOIN `SUBSCRIPTION`
								ON `SUBSCRIPTION`.`rss_id` = `RSS`.`rss_id`
								WHERE `SUBSCRIPTION`.`user_id`={$userId} AND `RSS`.`active`='1' order by title asc");
	
	$rowsRet = mysqli_num_rows($rssResult);
	if ($rowsRet == 0) {
		
		$response .= "<h3>You have no current subscriptions.<h3><br />Click <a href='feeds.php'>here</a> to add some.";
		
	}
	
	//loop through RSS feeds
	while($rssRow = mysqli_fetch_array($rssResult)) {
			//Edited to allow filtering on category - added RS 04/04/2017			
			$response .= "<div class='subrow " . $rssRow['category'] . "'>";

			//generate link and title for RSS Feed
		
			//Call function to extract feed thumb from RSS feed - RS 21/03/2017
		    $response .= "<div style='float:right; width:200px;>'><img style='margin-left:auto; margin-right:auto; display:block;' src='" . getThumb($rssRow['address']) . "'></div>";
			$response .= "<h2 style='display:inline'><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></h2>";

			//Get rating - added by RS 14/04/2017
			//Test if feed is rated
			//get count of rating entires
			$isRated = mysqli_query($dbconnect,
									"SELECT rating_id
									FROM `RATING`
									WHERE `rss_id`={$rssRow['rss_id']}");

			if(mysqli_num_rows($isRated)==0){
				
			} else {

				//get count of rating entires
				$getCount = mysqli_query($dbconnect,
										"SELECT COUNT(rating_id) as 'count'
										FROM `RATING`
										WHERE `rss_id`={$rssRow['rss_id']}");	

				if($getCount) {

					while($countRow = mysqli_fetch_array($getCount)){
						$count = $countRow['count'];
					}

					//get count of rating entires
					$getSum = mysqli_query($dbconnect,
											"SELECT SUM(r_score) as 'sum' 
											FROM `RATING` WHERE `rss_id`={$rssRow['rss_id']}");
					if($getSum){
						while($sumRow = mysqli_fetch_array($getSum)){
							$sum = $sumRow['sum'];

							$rating = ($sum / $count);

							if ($rating > 4.75){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/5stars.png'>";
							} else if ($rating > 4.25){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/4.5stars.png'>";
							} else if ($rating > 3.75){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/4stars.png'>";
							} else if ($rating > 3.25){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/3.5stars.png'>";
							} else if ($rating > 2.75){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/3stars.png'>";
							} else if ($rating > 2.25){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/2.5stars.png'>";
							} else if ($rating > 1.75){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/2stars.png'>";
							} else if ($rating > 1.25){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/1.5stars.png'>";
							} else if ($rating > 0.75){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/1stars.png'>";
							} else if ($rating > 0.25){
								$response.=  "<img style='margin-left:25px' height='25px;' src='images/0.5stars.png'>";
							} else {
								$response.=  "<span style='margin-left:25px'>Nil Rating!</span>";
							}
						} 	
					} else {
						
					}
				} else {
					
				}
			}
		
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