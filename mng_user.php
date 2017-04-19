<?php session_start(); //call or creates session?
include("dbconnect.inc.php");

if ($_POST['mode']=="register") {  //if registration

$valid = true; //validation
$regOK = true;

if($_POST['r_uname']==""){ //if no username
$valid=false;
}

if($_POST['r_pword1']==""){ //if no password
$valid=false;
}
	
if($_POST['r_pword1']!=$_POST['r_pword2']){ //if passwords don't match
$valid=false;
}

if(!$valid) { //if not valid
	$_SESSION['message']="<span style='color:#FF0004;'>Please enter valid data.</span>";
	
} else if (!$regOK) {
	$_SESSION['message']="<span style='color:#FF0004;'>Invalid registration code</span>";

} else {
	//echo "Valid!";

	$userCheck = mysqli_query($dbconnect,
		"SELECT *
		FROM `USER`
		WHERE `u_username`=
		'{$_POST['r_uname']}'"
		);	
	
	//check if username taken
	if(mysqli_num_rows($userCheck)>0) {
		$_SESSION['message']="<span style='color:#FF0004;'>Username is taken.</span>";
	} else {
		
		$username = mysqli_escape_string($dbconnect,
			$_POST['r_uname']);
		//hashed password
		
		$password = password_hash($_POST['r_pword1'],PASSWORD_DEFAULT);
			
		//try insertion
		$registeruser = @mysqli_query($dbconnect,
			"INSERT INTO `USER`
			(`u_username`,
				`u_password`)
		VALUES
		('{$username}','{$password}')"
		);
		$user_id = mysqli_insert_id($dbconnect);
		
		//if registered
		
		if($registeruser) {
			$_SESSION['message']="<span style='color:green;'><b>Registration successful!</b></span><br /><br />Please <a href='login.php'>login</a>.";

			
		} else {
			
			$_SESSION['message']="<span style='color:#FF0004;'>There has been a user registration error</span>";
			
		} 
	} //end user check
}//end validation check
}//end register routine

if ($_POST['mode']=="login") {  //if login

$valid = true; //validation
if($_POST['l_uname']=="") //if no username
$valid=false;

if($_POST['l_pword']=="") //if no password
$valid = false;

if(!$valid) { //if not valid

	?>

	<fieldset class="input">
	  <p id="login-form-username">
		<label for="modlgn_username">Username</label>
		<input id="modlgn_username" type="text" name="l_uname" class="inputbox" size="18" autocomplete="off">
	  </p>
	  <p id="login-form-password">
		<label for="modlgn_passwd">Password</label>
		<input id="modlgn_passwd" type="password" name="l_pword" class="inputbox" size="18" autocomplete="off">
	  </p>
	  <div class="remember">
		<p id="login-form-remember">
		  <label for="modlgn_remember"><a href="#">Forget Your Password ? </a></label>
		</p>
		<input type="submit" name="Submit" class="button" value="Login">
		<div class="clear"></div>
		<input type="hidden" name="mode" value="login" />
	  </div>
	  <p style="color:red">Please enter a user name AND password</p>
	</fieldset>
	<!--Username:
		<input type="text" name="l_uname" />
		<br />
		Password:
		<input type="password" name="l_pword" />
		<br />
		<input type="submit" value="Log In" />
		<input type="hidden" name="mode" value="login" />
		<p>Please enter details</p>-->
	<?php
	} else {

		$username = $_POST['l_uname'];
		//hashed password
		$password = $_POST['l_pword'];

		$getHash = mysqli_query($dbconnect, 
			"SELECT * 
			FROM `USER`
			WHERE `u_username`='{$username}'"
			);

		$rowHash = mysqli_fetch_array($getHash);

		$hash = $rowHash['u_password']; 

		if(password_verify($password, $hash)) {
			$user = mysqli_query($dbconnect, 
				"SELECT * 
				FROM `USER`
				WHERE `u_username`='{$username}'"
				);
			while($row=mysqli_fetch_array($user)) { 
				$_SESSION['user_id']=$row['user_id'];
				$_SESSION['u_username']=$row['u_username'];
				$_SESSION['u_level']=$row['u_level'];
			}
			?>
			<p>Welcome to the site: <a href="myaccount.php"><?php echo $_SESSION['u_username'];?></a> <br />
			  <br />
			  <a href="logout.php">Logout</a></p>
			<?php
		} else {

				?>
			<fieldset class="input">
			  <p id="login-form-username">
				<label for="modlgn_username">Username</label>
				<input id="modlgn_username" type="text" name="l_uname" class="inputbox" size="18" autocomplete="off">
			  </p>
			  <p id="login-form-password">
				<label for="modlgn_passwd">Password</label>
				<input id="modlgn_passwd" type="password" name="l_pword" class="inputbox" size="18" autocomplete="off">
			  </p>
			  <div class="remember">
				<p id="login-form-remember">
				  <label for="modlgn_remember"><a href="#">Forget Your Password ? </a></label>
				</p>
				<input type="submit" name="Submit" class="button" value="Login">
				<div class="clear"></div>
				<input type="hidden" name="mode" value="login" />
			  </div>
			  <p style="color:red">User and password do not match.  Please re-enter details.</p>
			</fieldset>
			<?php	
		}

	} //end validation check	

}//end login routine

//Update user profile routine
if ( $_POST[ 'action' ] == "update" ) { 
	$avatarMSG = "";
	//sanitise data for entry
	$user_id = $_SESSION[ 'user_id' ];
	$username = mysqli_escape_string( $dbconnect,
		$_POST[ 'username' ] );
	$firstname = mysqli_escape_string( $dbconnect,
		$_POST[ 'firstname' ] );
	$lastname = mysqli_escape_string( $dbconnect,
		$_POST[ 'lastname' ] );
	$emailaddress = mysqli_escape_string( $dbconnect,
		$_POST[ 'emailaddress' ] );
	$dob = mysqli_escape_string( $dbconnect,
		$_POST[ 'dob' ] );
	
	//Avatar code
	define("MAX_SIZE",20971520);
	$errors=0;
	
	//filenames
	$main = $_FILES['avatar']['name'];
	$thumb = $_FILES['avatar']['name'];
	//temp files
	$upMain = $_FILES['avatar']['tmp_name'];
	$upThumb = $_FILES['avatar']['tmp_name'];
	
	//check file extensions
	$mainFile = stripslashes($main);
	$mainExt = strtolower(getExtension($mainFile));
	
	$thumbFile = stripslashes($thumb);
	$thumbExt = strtolower(getExtension($thumbFile));
	
	if(!validExtension($mainExt) || !validExtension($thumbExt)) {
		if(empty($main)) {
			$errors=1; 
		} else {
			$avatarMSG = "<span style='color: red;'>However there was a problem with the avatar image - Unknown image extension</span>";
			$errors=1; 
		}
	} else {
		//filesizes
		$mainSize = filesize($upMain);
		$thumbSize = filesize($upThumb);
		
		if($mainSize>MAX_SIZE || $thumbSize>MAX_SIZE) {
			if(empty($main)) {
				$errors=1;
			} else {
				$avatarMSG['message'] = "<span style='color: red;'>However there was a problem with the avatar image - too big!!</span>";
				$errors=1;
			}
		} else {
			//file type checks for memory images
			switch($mainExt){
				case "jpg" : $mainSrc = imagecreatefromjpeg($upMain); break;
				case "jpeg" : $mainSrc = imagecreatefromjpeg($upMain); break;
				case "png" : $mainSrc = imagecreatefrompng($upMain); break;
				case "gif" : $mainSrc = imagecreatefromgif($upMain); break;
			}
			switch($thumbExt){
				case "jpg" : $thumbSrc = imagecreatefromjpeg($upThumb); break;
				case "jpeg" : $thumbSrc = imagecreatefromjpeg($upThumb); break;
				case "png" : $thumbSrc = imagecreatefrompng($upThumb); break;
				case "gif" : $thumbSrc = imagecreatefromgif($upThumb); break;
			}
			//get uploaded width and height
			list($mainWidth, $mainHeight) = getimagesize($upMain);
			list($thumbWidth, $thumbHeight) = getimagesize($upThumb);
			//main width
			$mainNewWidth = 900; 
			$mainNewHeight = ($mainHeight/$mainWidth)*$mainNewWidth;
			$tmpMain = imagecreatetruecolor($mainNewWidth,$mainNewHeight);
			$thumbNewWidth = 400; 
			$thumbNewHeight = ($thumbHeight/$thumbWidth)*$thumbNewWidth;
			$tmpThumb = imagecreatetruecolor($thumbNewWidth,$thumbNewHeight);
			
			//resave images
			imagecopyresampled($tmpMain,$mainSrc,0,0,0,0,$mainNewWidth,$mainNewHeight,$mainWidth,$mainHeight);
			imagecopyresampled($tmpThumb,$thumbSrc,0,0,0,0,$thumbNewWidth,$thumbNewHeight,$thumbWidth,$thumbHeight);
			
			//create and save images
			switch($mainExt) {
				case "jpg" : imagejpeg($tmpMain,"images/avatars/main/" . $main,100);
				break;
				case "jpeg" : imagejpeg($tmpMain,"images/avatars/main/" . $main,100);
				break;
				case "png" : imagepng($tmpMain,"images/avatars/main/" . $main,0);
				break;
				case "gif" : imagegif($tmpMain,"images/avatars/main/" . $main);
				break;
			}
			switch($thumbExt) {
				case "jpg" : imagejpeg($tmpThumb,"images/avatars/thumb/" . $thumb,100);
				break;
				case "jpeg" : imagejpeg($tmpThumb,"images/avatars/thumb/" . $thumb,100);
				break;
				case "png" : imagepng($tmpThumb,"images/avatars/thumb/" . $thumb,0);
				break;
				case "gif" : imagegif($tmpThumb,"images/avatars/thumb/" . $thumb);
				break;
			}
			
			//free up memory
			imagedestroy($mainSrc); imagedestroy($tmpMain);
			imagedestroy($thumbSrc); imagedestroy($tmpThumb);
			
		}//end file size check
		
	}//end extension check	

		
	if (!$errors){
		$main = "images/avatars/main/" . $main;
		$thumb = "images/avatars/thumb/" . $thumb;
		
		//update query
		$updateSql = "UPDATE `USER`
			SET `u_username`='{$username}',
			`u_firstname`='{$firstname}',
			`u_lastname`='{$lastname}',
			`u_emailaddress`='{$emailaddress}',
			`u_dob`='{$dob}',
			`u_img_main`='{$main}',
			`u_img_thumb`='{$thumb}'";
	} else {
		
		//update query
		$updateSql = "UPDATE `USER`
			SET `u_username`='{$username}',
			`u_firstname`='{$firstname}',
			`u_lastname`='{$lastname}',
			`u_emailaddress`='{$emailaddress}',
			`u_dob`='{$dob}'";
	}
	
	//update query

	$updateSql .= " WHERE `user_id`={$user_id}";
	$updateResult = mysqli_query( $dbconnect, $updateSql );

	if ( $updateResult ) {
		//header("location: detail.php?id=" . $_POST['p_id'])
		$_SESSION[ 'message' ] = "Your profile has been updated.<br><br>" . $avatarMSG;
		header( "location: userupdate.php");
	} else {
		$_SESSION[ 'message' ] = "Your profile could not be updated<br><br>.";
		header( "location: userupdate.php");
	}

} //end update

//Delete user acocunt routine
if ( $_GET[ 'action' ] == "delete" ) { //end insert
	$deleteQuery = "DELETE from `USER`
	WHERE
	`user_id`={$_SESSION['user_id']}";
	$deleteResult = mysqli_query( $dbconnect, $deleteQuery );

	if ( $deleteResult ) {
		session_start();
		session_destroy();
		session_start();
		$_SESSION[ 'message' ] = "Account deleted.";
	} else {
		$_SESSION[ 'message' ] = "Delete failed :-(";
	}
	header( "location: userupdate.php" );

} // end routine

//Code for checking if valid image extension for avatar
	function defaulty($x){
		return strlen($x) ? $x : '<i>Blank</i>';
	}
	function validExtension($ext){
		if($ext == "jpg"  || $ext == "jpeg" || 
			$ext == "png" || $ext == "gif") {
			return true;
		} else {
			return false;
		}
	}

	function getExtension($str){
		//check for dot in string
		$i = strrpos($str,".");
		//if no dot return nothing
		if (!$i) {return "";}
		//what's the extension based on length if string
		$l = strlen($str) - $i;
		//get extension using substring	
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
