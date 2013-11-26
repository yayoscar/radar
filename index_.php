<?php 
session_start(); 
include_once("config.php");

if(isset($_GET["logout"]) && $_GET["logout"]==1)
{
//User clicked logout button, distroy all session variables.
session_destroy();
header('Location: '.$return_url);
}
?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml" xml:lang="en-gb" lang="en-gb" >
<head>
<!-- Call jQuery -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<title>Ajax Facebook Connect With jQuery</title>
 <script>
 function AjaxResponse()
 {
	 var myData = 'connect=1'; //For demo, we will pass a post variable, Check process_facebook.php
	 jQuery.ajax({
	 type: "POST",
	 url: "process_facebook.php",
	 dataType:"html",
	 data:myData,
	 success:function(response){
	 $("#results").html('<fieldset style="padding:20px">'+response+'</fieldset>'); //Result
 },
	 error:function (xhr, ajaxOptions, thrownError){
	 $("#results").html('<fieldset style="padding:20px;color:red;">'+thrownError+'</fieldset>'); //Error
 	}
 });
 }
 
function LodingAnimate() //Show loading Image
{
	$("#LoginButton").hide(); //hide login button once user authorize the application
	$("#results").html('<img src="ajax-loader.gif" /> Please Wait Connecting...'); //show loading image while we process user
}

function ResetAnimate() //Reset User button
{
	$("#LoginButton").show(); //Show login button 
	$("#results").html(''); //reset element html
}

 </script>
</head>
<body>
<?php
if(!isset($_SESSION['logged_in']))
{
?>
    <div id="results">
    </div>
    <div id="LoginButton">
    <div class="fb-login-button" onlogin="javascript:CallAfterLogin();" size="medium" scope="<?php echo $fbPermissions; ?>">Connect With Facebook</div>
    </div>
<?php
}
else
{
	echo 'Hi '. $_SESSION['user_name'].'! You are Logged in to facebook, <a href="?logout=1">Log Out</a>.';
}
?>

<div id="fb-root"></div>
<script type="text/javascript">
window.fbAsyncInit = function() {
FB.init({appId: '<?php echo $appId; ?>',cookie: true,xfbml: true,channelUrl: '<?php echo $return_url; ?>channel.php',oauth: true});};
(function() {var e = document.createElement('script');
e.async = true;e.src = document.location.protocol +'//connect.facebook.net/en_US/all.js';
document.getElementById('fb-root').appendChild(e);}());

function CallAfterLogin(){
		FB.login(function(response) {		
		if (response.status === "connected") 
		{
			LodingAnimate(); //Animate login
			FB.api('/me', function(data) {
			  if(data.email == null)
			  {
					//Facbeook user email is empty, you can check something like this.
					alert("You must allow us to access your email id!"); 
					ResetAnimate();

			  }else{
					AjaxResponse();
			  }
		  });
		 }
	});
}

</script>

</body>
</html>