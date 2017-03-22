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
		header("location: register.php");	
	} else {
		
		$username = mysqli_escape_string($dbconnect,
			$_POST['r_uname']);
		//hashed password
		
		$password = password_hash($_POST['r_pword1'],PASSWORD_DEFAULT);
		$sal = $_POST['r_sal'];
		$fname = mysqli_escape_string($dbconnect,
			$_POST['r_fname']);
		$sname = mysqli_escape_string($dbconnect,
			$_POST['r_sname']);
		$dob = $_POST['r_dob'];
		$email = mysqli_escape_string($dbconnect,
			$_POST['r_email']);	
		if (isset($checkbox)) {
			$emailSub = "Y";
		} else {
		} $emailSub = "N";
		
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
			  <p style="color:red">User and password do not match.  Please re-enter details.<?php var_dump($hash); ?></p>
			</fieldset>
			<?php	
		}

	} //end validation check	

}//end login routine
