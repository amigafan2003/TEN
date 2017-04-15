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
	
	//Test if feed is rated
	//get count of rating entires
	$isRated = mysqli_query($dbconnect,
							"SELECT rating_id
							FROM `RATING`
							WHERE `rss_id`={$rssId}");
	
	if(mysqli_num_rows($isRated)==0){
		$response.= "SUCCESS:<div id='notrated'>Feed is not rated yet.</div>";
	} else {
	
		//get count of rating entires
		$getCount = mysqli_query($dbconnect,
								"SELECT COUNT(rating_id) as 'count'
								FROM `RATING`
								WHERE `rss_id`={$rssId}");	

		if($getCount) {

			while($countRow = mysqli_fetch_array($getCount)){
				$count = $countRow['count'];
			}

			//get count of rating entires
			$getSum = mysqli_query($dbconnect,
									"SELECT SUM(r_score) as 'sum' 
									FROM `RATING` WHERE `rss_id`={$rssId}");
			if($getSum){
				while($sumRow = mysqli_fetch_array($getSum)){
					$sum = $sumRow['sum'];

					$rating = ($sum / $count);

					$response.= "SUCCESS:<span id='star'>";
					if ($rating > 4.75){
						$response.=  "<img height='25px;' src='images/5stars.png'>";
					} else if ($rating > 4.25){
						$response.=  "<img height='25px;' src='images/4.5stars.png'>";
					} else if ($rating > 3.75){
						$response.=  "<img height='25px;' src='images/4stars.png'>";
					} else if ($rating > 3.25){
						$response.=  "<img height='25px;' src='images/3.5stars.png'>";
					} else if ($rating > 2.75){
						$response.=  "<img height='25px;' src='images/3stars.png'>";
					} else if ($rating > 2.25){
						$response.=  "<img height='25px;' src='images/2.5stars.png'>";
					} else if ($rating > 1.75){
						$response.=  "<img height='25px;' src='images/2stars.png'>";
					} else if ($rating > 1.25){
						$response.=  "<img height='25px;' src='images/1.5stars.png'>";
					} else if ($rating > 0.75){
						$response.=  "<img height='25px;' src='images/1stars.png'>";
					} else if ($rating > 0.25){
						$response.=  "<img height='25px;' src='images/0.5stars.png'>";
					} else {
						$response.=  "<span style='font-weight:100;'>Nil Rating!</span>";
					}
				} 	
			} else {
				$response.=  "FAIL:<p>Problem loading ratings</p>";
			}
		} else {
			$response.=  "FAIL:<p>Problem loading ratings</p>";
		}
	}
}


if($action=="insert") {

	//TO-DO: validation checks

	//get vars
	$userId = $_GET['user_id'];
	$rssId = $_GET['rss_id'];
	$ratingContent = urldecode($_GET['rating_content']);

	$result = mysqli_query($dbconnect,
							"INSERT INTO `RATING`
							(`user_id`,`rss_id`,`r_score`)
							VALUES
							({$userId},{$rssId},'{$ratingContent}')");
	if($result) {
		$response.=  "SUCCESS:<p>Rating added</p>";
	} else {

		$response.=  "FAIL:<p>Problem adding rating</p>" . "user id:" . $userId . "rss id:" . $rssId . "rating:" . $ratingContent . "";
	}

}

if($action=="delete") {

	//TO-DO: validation checks

	//get vars
	$ratingId = $_GET['rating_id'];

	$result = mysqli_query($dbconnect,
							"DELETE FROM `RATING`
							WHERE `rating_id`={$ratingId}");

	if($result) {

		$response.=  "SUCCESS:<p>Rating deleted</p>";
	} else {

		$response.=  "FAIL:<p>Rating deleted</p> Rating id: " . $ratingId . "";
	}

}

$array = array("response" => $response);

echo json_encode($array);

echo ")";
