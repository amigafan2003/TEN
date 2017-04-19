<?php session_start(); //call or creates session??> <?php include( 'dbconnect.inc.php' ); $pageTitle = "|  Feed ".$_GET['rssid']; $user_id = $_SESSION["user_id"];?>
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
	<script type="text/javascript" src="assets/js/jquery-2.2.3.min.js"></script>
	<script type="text/javascript" src="assets/js/functions.js"></script>

	<script type="text/javascript">
		var commentsUpFlag = false;
		rssId = getUrlParameter( "rssid" );
		userId = <?php echo $user_id; ?>;

		//https://api.jquery.com/jQuery.parseXML/

		//FUNCTION TO PARSE RSS FEED --------------------------------------------------------------
		function getFeed( rsscontent ) {

			var xml = rsscontent;
			xmlDoc = $.parseXML( xml ),
				$xml = $( xmlDoc ),
				$channel = $xml.find( "channel" );
			$channel.children().each( function () {

				if ( $( this ).prop( "tagName" ) == "title" ) {
					htmlString = "<div class='rsstitle'>";
					htmlString += "<h1>" + $( this ).text() + "</h1>";
					htmlString += "</div>";
					$( "main" ).append( htmlString );
				}

				if ( $( this ).prop( "tagName" ) == "item" ) {

					//Get article thumbs  - RS 23/03/2017 
					var url = $( this ).find( "media\\:thumbnail, thumbnail" ).attr("url" );
					var imgwidth = $( this ).find( "media\\:thumbnail, thumbnail" ).attr("width" );
					parseInt(imgwidth);
							
					
					htmlString = "<div class='rssitem'>";
					
					//Check if article thumb is empty - if so, set to empty so nothing is displayed - RS 24/03/2017 issue #3
					if ( (url) == undefined) {
						var thumb = "";
					} else {
						//Catch images smaller that current div width to prevent them pixelating - RS 24/03/2017 issue #2
						if (imgwidth > 70) {
							var thumb = "<div width:100%;'><div style='display:inline-block;width:20%'><img style='width:95%;' src='" + (url) + "'></div>";
						} else {
							var thumb = "<div width:100%;'><div style='display:inline-block;width:70px; margin-right:20px;'><img  src='" + (url) + "'></div>"; 
						}
					}
					var title;
					var desc;
					var link;
					
					$( this ).children().each( function () {

					//Build variables for title, description and link values - RS 24/03/2017 issue #1
						if ( $( this ).prop( "tagName" ) == "title" ) {
							title = thumb  + "<div style='display:inline-block;'><h2>" + $( this ).text() + "</h2></div></div>";
						}
						if ( $( this ).prop( "tagName" ) == "description" ) {
							desc = "<span style='margin-left:25px;'>" + $( this ).text() + "</span><br />";
						}
						if ( $( this ).prop( "tagName" ) == "link" ) {
							link = "<a target='_blank' style='margin-left:25px;' href='" + $( this ).text() + "'>Read more</a>";
						}
					} );
					
					//Build htmls string using previous vars for title, desc and link.  Do not inlclude items not found - RS 24/03/2017 issue #1
					if (title == undefined) {
						
					} else {
						htmlString += title;
					}
					
					if (desc == undefined) {
						
					} else {
						htmlString += desc;
					}
					
					if (link == undefined) {
						
					} else {
						htmlString += link;
					}


					htmlString += "</div><br /><br />";

					$( "main" ).append( htmlString );
				}

			} );
		}

		//DOCUMENT READY EVENT HANDLER =========================================================
		$( document ).ready( function () {
			
			// Add smooth scrolling to all links
			$("a").on('click', function(event) {

			// Make sure this.hash has a value before overriding default behavior
			if (this.hash !== "") {
			  // Prevent default anchor click behavior
			  event.preventDefault();

			  // Store hash
			  var hash = this.hash;

			  // Using jQuery's animate() method to add smooth page scroll
			  // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
			  $('html, body').animate({
				scrollTop: $(hash).offset().top
			  }, 800, function(){

				// Add hash (#) to URL when done scrolling (default click behavior)
				window.location.hash = hash;
			  });
			} // End if
		  });
			
			//RSS fEED RETRIEVER --------------------------------------------------------------

			$.ajax( {
				beforeSend: function () {
					$( "#loading" ).show();
				},
				complete: function () {
					$( "#loading" ).hide();
				},
				type: 'GET',
				dataType: "jsonp",
				jsonp: "callback",
				url: "mng_feed.php?rssid=" + rssId,
				success: function ( data ) {

					responseString = "";
					$.each( data, function ( index, item ) {
						// Use item in here
						responseString = item;
					} );

					getFeed( responseString );

				},
				error: function ( jqXHR, textStatus, errorThrown ) {
					if ( jqXHR.status == 500 ) {
						$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
					} else {
						$( "#messages" ).html( 'Unexpected error.' );
					}
				}
			} );

			//COMMENTS CONTAINER HEIGHT ANIMATION -----------------------------------------
			$( "#commentslink" ).click( function () {

				if ( commentsUpFlag ) {

					$( "#commentscontainer" ).animate( {
						bottom: "-400"
					}, 500, function () {
						commentsUpFlag = false;
					} );

				} else {
					$( "#commentscontainer" ).animate( {
						bottom: "0"
					}, 500, function () {
						commentsUpFlag = true;
					} );
				}
				return false;
			} );

			//COMMENT RETRIEVAL -----------------------------------------
			selectComments();

			//CLEAR COMMENT TEXT BOX ------------------------------------------------------
			$( "#clearbtn" ).click( function () {
				$( "#commentbox" ).val( '' );
			} );

			//UPDATE COMMENTS -------------------------------------------------------------
			$( document ).on( "click", '.updatecom', function ( event ) {
				var commentId = $( this ).attr( 'comid' );
				$( '#commentbtn' ).attr( 'mode', 'update' );
				$( '#commentbtn' ).attr( 'commentid', commentId );
				$( '#commentbtn' ).val( 'Edit Comment' );

				var commentContent = "";

				//cycle through comments, if ID matchs, set textarea to have comment content
				$( '.comment' ).each( function () {
					if ( $( this ).attr( 'comid' ) == commentId ) {
						$( '#commentbox' ).val( $( this ).html() );
					}
				} );

			} );

			//INSERT / UPDATE BUTTON CLICK =================================================

			$( document ).on( "click", '#commentbtn', function ( event ) {


				var ajaxString = "";
				var commentContent = $( '#commentbox' ).val();

				if ( commentContent == "" ) {
					alert( "You need to enter more than nothing" );
				} else {
					commentContent = encodeURI( commentContent );
				}

				//INSERT COMMENT - SEND --------------------------------------------------------


				if ( $( this ).attr( 'mode' ) == 'insert' ) {

					ajaxString = "action=insert&user_id=" + userId + "&rss_id=" + rssId + "&comment_content=" + commentContent;

				}


				//UPDATE COMMENTS - SEND -------------------------------------------------------

				if ( $( this ).attr( 'mode' ) == 'update' ) {

					ajaxString = "action=update&comment_id=" + $( this ).attr( 'commentid' ) + "&comment_content=" + commentContent;

				}


				$.ajax( {
					beforeSend: function () {
						$( "#loading" ).show();
					},
					complete: function () {
						$( "#loading" ).hide();
					},
					type: 'GET',
					dataType: "jsonp",
					jsonp: "callback",
					url: "mng_comment.php?" + ajaxString,
					success: function ( data ) {

						responseString = "";

						$.each( data, function ( index, item ) {
							// Use item in here
							responseString = item;
						} );


						if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

							//get rest of data after prefix (SUCCESS:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 8 );
							selectComments();
							$( '#commentbtn' ).attr( 'mode', 'insert' );
							$( '#commentbtn' ).attr( 'commentid', '' );
							$( '#commentbtn' ).val( 'Add Comment' );
						}

						if ( responseString.indexOf( "FAIL" ) > -1 ) {

							//get rest of data after prefix (FAIL:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 5 );

						}

						$( "#messages" ).html( responseString );

					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						if ( jqXHR.status == 500 ) {
							$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
						} else {
							$( "#messages" ).html( 'Unexpected error.' );
						}
					}
				} );


				return false;
			} );


			//DELETE COMMENT ---------------------------------------------------------------
			$( document ).on( "click", '.deletecom', function ( event ) {

				$.ajax( {
					beforeSend: function () {
						$( "#loading" ).show();
					},
					complete: function () {
						$( "#loading" ).hide();
					},
					type: 'GET',
					dataType: "jsonp",
					jsonp: "callback",
					url: "mng_comment.php?action=delete&comment_id=" + $( this ).attr( 'comid' ),
					success: function ( data ) {

						responseString = "";

						$.each( data, function ( index, item ) {
							// Use item in here
							responseString = item;
						} );


						if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

							//get rest of data after prefix (SUCCESS:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 8 );
							selectComments();

						}

						if ( responseString.indexOf( "FAIL" ) > -1 ) {

							//get rest of data after prefix (FAIL:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 5 );

						}

						$( "#messages" ).html( responseString );

					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						if ( jqXHR.status == 500 ) {
							$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
						} else {
							$( "#messages" ).html( 'Unexpected error.' );
						}
					}
				} );

				return false;
			} );
			
			//Feed rating function - added by RS 14/04/2017
			//RATING RETRIEVAL -----------------------------------------
			selectRating();
			
			
			//INSERT RATING BUTTON CLICK =================================================

			$( document ).on( "click", '#ratingbtn', function ( event ) {


				var ajaxString = "";
				var ratingContent = $( '#ratingdropdown' ).val();

				if ( ratingContent == "" ) {
					alert( "You need to enter more than nothing" );
				} else {
					ratingContent = encodeURI( ratingContent );
				}

				//INSERT RATING - SEND --------------------------------------------------------


				if ( $( this ).attr( 'mode' ) == 'insert' ) {

					ajaxString = "action=insert&user_id=" + userId + "&rss_id=" + rssId + "&rating_content=" + ratingContent;

				}


				$.ajax( {
					beforeSend: function () {
						$( "#loading" ).show();
					},
					complete: function () {
						$( "#loading" ).hide();
					},
					type: 'GET',
					dataType: "jsonp",
					jsonp: "callback",
					url: "mng_rating.php?" + ajaxString,
					success: function ( data ) {

						responseString = "";

						$.each( data, function ( index, item ) {
							// Use item in here
							responseString = item;
						} );


						if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

							//get rest of data after prefix (SUCCESS:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 8 );
							selectRating();
							$("#addrating").hide();
							$("#addratinginloop").hide();
						}

						if ( responseString.indexOf( "FAIL" ) > -1 ) {

							//get rest of data after prefix (FAIL:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 5 );

						}

						$( "#messages" ).html( responseString );

					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						if ( jqXHR.status == 500 ) {
							$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
						} else {
							$( "#messages" ).html( 'Unexpected error.' );
						}
					}
				} );


				return false;
			} );


			//DELETE RATING ---------------------------------------------------------------
			$( document ).on( "click", '#deleterating', function ( event ) {

				$.ajax( {
					beforeSend: function () {
						$( "#loading" ).show();
					},
					complete: function () {
						$( "#loading" ).hide();
					},
					type: 'GET',
					dataType: "jsonp",
					jsonp: "callback",
					url: "mng_rating.php?action=delete&rating_id=" + $( this ).attr( 'ratingId' ),
					success: function ( data ) {

						responseString = "";

						$.each( data, function ( index, item ) {
							// Use item in here
							responseString = item;
						} );


						if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

							//get rest of data after prefix (SUCCESS:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 8 );
							selectRating();
							$("#addrating").show();
							$("#addratinginloop").show();
							$("#alreadyrated").hide();
						}

						if ( responseString.indexOf( "FAIL" ) > -1 ) {

							//get rest of data after prefix (FAIL:)
							//the number is the character position to start from, we cut off the prefix
							responseString = responseString.substring( 5 );

						}

						$( "#messages" ).html( responseString );

					},
					error: function ( jqXHR, textStatus, errorThrown ) {
						if ( jqXHR.status == 500 ) {
							$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
						} else {
							$( "#messages" ).html( 'Unexpected error.' );
						}
					}
				} );

				return false;
			} );
			
		} );

		//COMMENT RETRIEVAL FUNCTION ---------------------------------------------------
		function selectComments() {

			$( "#comcontent" ).html( "" );

			$.ajax( {
				beforeSend: function () {
					$( "#loading" ).show();
				},
				complete: function () {
					$( "#loading" ).hide();
					$( "#commentscontainer" ).show();

				},
				type: 'GET',
				dataType: "jsonp",
				jsonp: "callback",
				url: "mng_comment.php?action=select&user_id=" + userId + "&rss_id=" + rssId,
				success: function ( data ) {

					responseString = "";

					$.each( data, function ( index, item ) {
						// Use item in here
						responseString = item;
					} );


					if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

						//get rest of data after prefix (SUCCESS:)
						//the number is the character position to start from, we cut off the prefix
						responseString = responseString.substring( 8 );

					}

					$( "#comcontent" ).html( responseString );

				},
				error: function ( jqXHR, textStatus, errorThrown ) {
					if ( jqXHR.status == 500 ) {
						$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
					} else {
						$( "#messages" ).html( 'Unexpected error.' );
					}
				}
			} );
		}
		
		//FEED RATING RETRIEVAL FUNCTION ---------------------------------------------------
		function selectRating() {

			$( "#rating" ).html( "" );

			$.ajax( {
				beforeSend: function () {
					$( "#loading" ).show();
				},
				complete: function () {
					$( "#loading" ).hide();
					$( "#commentscontainer" ).show();

				},
				type: 'GET',
				dataType: "jsonp",
				jsonp: "callback",
				url: "mng_rating.php?action=select&user_id=" + userId + "&rss_id=" + rssId,
				success: function ( data ) {

					responseString = "";

					$.each( data, function ( index, item ) {
						// Use item in here
						responseString = item;
					} );


					if ( responseString.indexOf( "SUCCESS" ) > -1 ) {

						//get rest of data after prefix (SUCCESS:)
						//the number is the character position to start from, we cut off the prefix
						responseString = responseString.substring( 8 );

					}

					$( "#rating" ).html( responseString );

				},
				error: function ( jqXHR, textStatus, errorThrown ) {
					if ( jqXHR.status == 500 ) {
						$( "#messages" ).html( 'Internal error: ' + jqXHR.responseText );
					} else {
						$( "#messages" ).html( 'Unexpected error retrieving rating.' );
					}
				}
			} );
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

				<?	include('header.inc.php'); ?>

				<!-- Banner -->
				<section id="banner">
					<?php
						if (isset($_SESSION["user_id"])) {
						?>
							<main id="articlespanel">

								<div id="loading">
									<center><img src="images/loading.gif" id="loading" /></center>
								</div>

							</main>
	
							<div id="commentscontainer">
								<a class="skiptoarticles" style="padding-bottom:25px;" href="#articlespanel">Skip to articles</a>
								<div id="messages">

								</div>
								<h3>Feed rating</h3>
								<div id="rating">

								</div><br>
								<?php 	
								$rssId = $_GET['rssid'];
								$userId = $_SESSION[user_id];
								$isRated = mysqli_query($dbconnect,
									"SELECT *
									FROM `RATING`
									WHERE `rss_id`={$rssId} AND `user_id`={$userId}");
								while($rating = mysqli_fetch_array($isRated)) {
									
										$ratingId = $rating['rating_id'];
								}

								if(mysqli_num_rows($isRated) >= 1){
		
								?>
									<span id="alreadyrated">
										<p>You have already rated this feed.</p>
										<input type="button" value="Delete rating" id="deleterating" ratingId="<?php echo $ratingId ?>" mode="delete"/>
									</span>
									<span style="display:none;" id="addratinginloop">
										<select id="ratingdropdown">
											<option value="0">Nil stars!</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option selected="true" value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>									
										</select>
										<input type="button" value="Add Rating" id="ratingbtn" mode="insert"/>
									</span>
											
								<?php } else { ?>
									<span id="addrating">
										<select id="ratingdropdown">
											<option value="0">Nil stars!</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option selected="true" value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>									
										</select>
										<input type="button" value="Add Rating" id="ratingbtn" mode="insert"/>
									</span>
								<?php } ?>
								<br><br>
								<h3>Comments</h3>
								<div id="comcontent">

								</div><br>
								<textarea id="commentbox"></textarea>
								<input type="button" value="Add Comment" id="commentbtn" mode="insert"/>
								<input type="button" value="Clear Comment" id="clearbtn"/>
							</div>
						<?php
						} else {
							?>
							<script>
								$( document ).ready( function () {
									$( "#loading" ).hide();
								});
							</script>
							<p>Please <a href="login.php">login</a> to view this page.</p>
							<?php
						}
					?>
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

				<?	include('nav.inc.php'); ?>

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
