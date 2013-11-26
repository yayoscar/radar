<?php
/*
 * Facebook Notifications API example using the Facebook PHP SDK (v3.1.1)
 *
 * This example code was written by Aran Reeks
 * A full tutorial can be found here on AnotherWebDevBlog.com here:
 * http://www.anotherwebdevblog.com/facebook-notifications-api-tutorial-php
 *
 * At the time of writing this the Notification API is in public beta and as a result could change, full official documentation of this API can be found here:
 * https://developers.facebook.com/docs/app_notifications/
 * */
 
// APP SPECIFIC SETTINGS - You will need to change the top 2 settings for certain to match your app
$app_id = '469051533130170'; // App ID/API Key can be found here: https://developers.facebook.com/apps
$app_secret = '26020cfe2bc84b28b4e034e6cd7b6c33'; // App secret can be found here: https://developers.facebook.com/apps
//$fb_sdk_location = $_SERVER['DOCUMENT_ROOT'] . '/inc/facebook.php'; // Full path to facebook.php - From the Facebook PHP SDK Files
$fb_sdk_location = $_SERVER['DOCUMENT_ROOT'] . 'radar/inc/facebook.php';
 
$notification_message = 'Un compañero de tu generación se ha registrado - radar-dgeti';
$notification_app_link = '?notification=test'; // The link the notification will go through to, this will be specific to your in Facebook App
// END OF APP SPECIFIC SETTINGS
// This was all you should need to change for this demo to work
 
require($fb_sdk_location); // Bring through the Facebook PHP SDK
$fb = new Facebook(array('appId' => $app_id, 'secret' => $app_secret));
$user = $fb->getUser(); // See if there is a user from a cookie
 
if ($user) {
 /*
 * Facebook user retrieved
 * $user : Holds the Facebook Users unique ID - Required for posting a notification to them
 * */
 try {
 // Try send this user a notification
 $fb_response = $fb->api('/100004549872960/notifications', 'POST',
 array(
 'access_token' => $fb->getAppId() . '|' . $fb->getApiSecret(), // access_token is a combination of the AppID & AppSecret combined
 'href' => $notification_app_link, // Link within your Facebook App to be displayed when a user click on the notification
 'template' => $notification_message, // Message to be displayed within the notification
 )
 );
 if (!$fb_response['success']) {
 // Notification failed to send
 echo '<p><strong>Failed to send notification</strong></p>'."\n";
 echo '<p><pre>' . print_r($fb_response, true) . '</pre></p>'."\n";
 } else {
 // Success!
 echo '<p>Your notification was sent successfully</p>'."\n";
 }
 
} catch (FacebookApiException $e) {
 // Notification failed to send
 echo '<p><pre>' . print_r($e, true) . '</pre></p>';
 $user = NULL;
 }
} else {
 // No Facebook user fetched, show FB login button - Requires Facebook JavaScript SDK (Below)
 echo '<fb:login-button></fb:login-button>'."\n";
}
?>
<div id="fb-root"></div> <!-- Facebook JS SDK will auto create this element if it doesn't exist (you will be notified in your JS Console should this occur) -->
 
<!-- Below is for the Facebook JavaScript SDK -->
<script>
// Load the JS SDK
window.fbAsyncInit = function () {
 FB.init({
 appId : '<?=$fb->getAppID()?>',
 cookie : true,
 xfbml : true,
 oauth : true
 });
 
// Listen for Facebook login / logout events
 FB.Event.subscribe('auth.login', function (response) { window.location.reload(); });
 FB.Event.subscribe('auth.logout', function (response) { window.location.reload(); });
};
 
// Asynchronously load the Facebook JavaScript SDK into the page
(function(){var e=document.createElement('script');e.async=true;e.src=document.location.protocol+'//connect.facebook.net/en_US/all.js';document.getElementById('fb-root').appendChild(e);}());
</script>