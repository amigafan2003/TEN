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
	$response .= "<div id='trend' class='today'><h3>Trending today:</h3>";
	$subResult = mysqli_query($dbconnect,
							  	"SELECT COUNT(rss_id) as 'count', rss_id 
								FROM TREND WHERE DATE(t_date) = CURDATE() GROUP BY rss_id order by COUNT(rss_id) desc");
	
	if(mysqli_num_rows($subResult)== 0) {
		
		$response .= "There are no trending feeds today<br><br>";
		
	} else {
	
		while($subCountRow = mysqli_fetch_array($subResult)) {

			//get all RSS feeds that have a subscription

			$sub_rss_id = $subCountRow['rss_id'];
			$count = $subCountRow['count'];
			$rssResult = mysqli_query($dbconnect,
										"SELECT *
										FROM `RSS` WHERE `rss_id` = $sub_rss_id order by title asc");

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

					$response .= "<div id='subrow' class='subrow today'>";


					//generate link and title for RSS Feed
					//Call function to extract feed thumb from RSS feed - RS 21/03/2017
					$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
					$response .= "<h2 style='float:left;' class='feedicon' ><img src='images/trend.png' height='50px'> " . $count . "</h2>";
					$response .= "<h2><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a>";

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
										$response.=  "<span style='starrating'>Nil Rating!</span>";
									}
								} 	
							} else {

							}
						} else {

						}
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
	} 	$response .= "</div>";
	
	//get all RSS feeds that were trending yesterday
	$response .= "<div id='trend' class='yesterday' style='display:none;'><h3>Trending yesterday:</h3>";
	$subResult = mysqli_query($dbconnect,
							  	"SELECT COUNT(rss_id) as 'count', rss_id 
								FROM TREND WHERE DATE(t_date) = CURDATE() -1 GROUP BY rss_id order by COUNT(rss_id) desc");
	
	if(mysqli_num_rows($subResult)== 0) {
		
		$response .= "There are no trending feeds from yesterday<br><br>";
		
	} else {	
	
		while($subCountRow = mysqli_fetch_array($subResult)) {

			//get all RSS feeds that have a subscription

			$sub_rss_id = $subCountRow['rss_id'];
			$count = $subCountRow['count'];
			$rssResult = mysqli_query($dbconnect,
										"SELECT *
										FROM `RSS` WHERE `rss_id` = $sub_rss_id order by title asc");

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
					$response .= "<div id='subrow' class='subrow yesterday'>";

					//generate link and title for RSS Feed
					//Call function to extract feed thumb from RSS feed - RS 21/03/2017
					$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
					$response .= "<h2 style='float:left;' class='feedicon' ><img src='images/trend.png' height='50px'> " . $count . "</h2>";
					$response .= "<h2><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></a>";

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
										$response.=  "<span style='starrating'>Nil Rating!</span>";
									}
								} 	
							} else {

							}
						} else {

						}
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
	} $response .= "</div>";
	
	//get all RSS feeds that were trending in the last week
	$response .= "<div id='trend' class='week' style='display:none;'><h3>Trending in the last week:</h3>";
	$subResult = mysqli_query($dbconnect,
							  	"SELECT COUNT(rss_id) as 'count', rss_id 
								FROM TREND WHERE DATE(t_date) >= CURDATE() -7 GROUP BY rss_id order by COUNT(rss_id) desc");
	
	if(mysqli_num_rows($subResult)== 0) {
		
		$response .= "There are no trending feeds from last week<br><br>";
		
	} else {
		
		while($subCountRow = mysqli_fetch_array($subResult)) {

			//get all RSS feeds that have a subscription

			$sub_rss_id = $subCountRow['rss_id'];
			$count = $subCountRow['count'];
			$rssResult = mysqli_query($dbconnect,
										"SELECT *
										FROM `RSS` WHERE `rss_id` = $sub_rss_id order by title asc");

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

					$response .= "<div id='subrow' class='subrow week'>";

					//generate link and title for RSS Feed
					//Call function to extract feed thumb from RSS feed - RS 21/03/2017
					$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
					$response .= "<h2 style='float:left;' class='feedicon' ><img src='images/trend.png' height='50px'> " . $count . "</h2>";
					$response .= "<h2><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></a>";

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
										$response.=  "<span style='starrating'>Nil Rating!</span>";
									}
								} 	
							} else {

							}
						} else {

						}
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

	//get all RSS feeds that were trending in the last month
	$response .= "<div id='trend' class='month' style='display:none;'><h3>Trending in the last month:</h3>";
	$subResult = mysqli_query($dbconnect,
							  	"SELECT COUNT(rss_id) as 'count', rss_id 
								FROM TREND WHERE DATE(t_date) >= CURDATE() -31 GROUP BY rss_id order by COUNT(rss_id) desc");
	
	if(mysqli_num_rows($subResult)== 0) {
		
		$response .= "There are no trending feeds from last month<br><br>";
		
	} else {		
	
		while($subCountRow = mysqli_fetch_array($subResult)) {

			//get all RSS feeds that have a subscription

			$sub_rss_id = $subCountRow['rss_id'];
			$count = $subCountRow['count'];
			$rssResult = mysqli_query($dbconnect,
										"SELECT *
										FROM `RSS` WHERE `rss_id` = $sub_rss_id order by title asc");

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

					$response .= "<div id='subrow' class='subrow month'>";

					//generate link and title for RSS Feed
					//Call function to extract feed thumb from RSS feed - RS 21/03/2017
					$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
					$response .= "<h2 style='float:left;' class='feedicon' ><img src='images/trend.png' height='50px'> " . $count . "</h2>";
					$response .= "<h2><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></a>";

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
										$response.=  "<span style='starrating'>Nil Rating!</span>";
									}
								} 	
							} else {

							}
						} else {

						}
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
	
//get all time RSS feed trending
	$response .= "<div id='trend' class='all' style='display:none;'><h3>All time trending:</h3>";
	$subResult = mysqli_query($dbconnect,
							  	"SELECT COUNT(rss_id) as 'count', rss_id 
								FROM TREND GROUP BY rss_id order by COUNT(rss_id) desc");
	
	while($subCountRow = mysqli_fetch_array($subResult)) {
		
		//get all RSS feeds that have a subscription
		
		$sub_rss_id = $subCountRow['rss_id'];
		$count = $subCountRow['count'];
		$rssResult = mysqli_query($dbconnect,
									"SELECT *
									FROM `RSS` WHERE `rss_id` = $sub_rss_id order by title asc");

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

				$response .= "<div id='subrow' class='subrow all'>";

					//generate link and title for RSS Feed
					//Call function to extract feed thumb from RSS feed - RS 21/03/2017
					$response .= "<div style='float:right;' class='thumb-container'><img class='feedThumb' src='" . getThumb($rssRow['address']) . "'></div>";
					$response .= "<h2 style='float:left;' class='feedicon' ><img src='images/trend.png' height='50px'> " . $count . "</h2>";
					$response .= "<h2><a href='#' class='rsslink' rssid='" . $rssRow['rss_id'] . "' >" . $rssRow['title'] . "</a></a>";

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
										$response.=  "<span style='starrating'>Nil Rating!</span>";
									}
								} 	
							} else {

							}
						} else {

						}
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