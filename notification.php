<?php 
    /*
        @name: Facebook Notification Class
        @version: 1.0
        @author: Md. Mahmud Ahsan
        @link: http://mahmudahsan.wordpress.com
        @Description: This class is helpful for facebook application. Using this class object you can send email, send notfication
         to facebook user
    */
    class Notification{
        private $facebook; 
        
        //set the facebook configuration settings here
        private $fbConfig   =   array(
            'api_key'       => '469051533130170',
            'secret_key'    => '26020cfe2bc84b28b4e034e6cd7b6c33'
        );
        
        /* below variables are set for notification email */
        private $mailMessage            =   '';
        private $mailSubject            =   '';
        private $notificationMessage    =   '';
        private $notificationType       =   ''; // Specify whether the notification is a user_to_user one or an app_to_user. (Default value is user_to_user.)
        
        function init(){
            //Tags that work as message: <p>, <br />, <a>, <b>, <i>, <h1>, <hr>, <center> 
            $this->mailMessage          =   "HTML message as email. Like Checkout my app <a href='http://myapp'>My App </a>";
            $this->mailSubject          =   'Email subject';
            $this->notificationMessage  =   'The message you want to send as notfication';
            $this->notificationType     =   'app_to_user'; // you can change it as app_to_user
        }
        
        function __construct(){
            /*//$fb_sdk_location = $_SERVER['DOCUMENT_ROOT'] . 'radar/inc/facebook.php';
			$fb_sdk_location="inc/facebook.php";
			echo $fb_sdk_location;
			include($fb_sdk_location); // you must include the facebook.php file this library is provided by facebook
            
            $this->facebook = new Facebook($this->fbConfig['api_key'], $this->fbConfig['secret_key']);*/
			require_once("./inc/facebook.php");
			include("./class/config.php");
		
			$this->facebook = new Facebook(array(
				'appId'  => $appId,
				'secret' => $appSecret,
				'cookie' => true
			));
			
			
            $this->init();
        }
        
        function notificationStatus(){
            /*----Show Current allocation limits for your application for the specified integration points. ------*/
            echo 'Notification: ' .                         $this->facebook->api_client->admin_getAllocation('notifications_per_day') . "<br />";
            echo 'announcement_notifications_per_week: ' .  $this->facebook->api_client->admin_getAllocation('announcement_notifications_per_week') . "<br />";
            echo 'requests_per_day: ' .                     $this->facebook->api_client->admin_getAllocation('requests_per_day') . "<br />";
            echo 'emails_per_day : ' .                      $this->facebook->api_client->admin_getAllocation('emails_per_day') . "<br />";
            echo 'email_disable_message_location: ' .       $this->facebook->api_client->admin_getAllocation('email_disable_message_location') . "<br />";
        }
        
        function sendEmail($ids){
            /*------- This method send html formatted email to corresponding facebook user ----------*/
            $this->facebook->api_client->notifications_sendEmail($ids, $this->mailSubject, "", $this->mailMessage);
        }
        
        function sendNotification($ids){
            /*------- Sends a notification or request to a set of users. Notifications are items sent by an application to a user's notifications page in response to some sort of user activity within an application ----------*/
            $this->facebook->api_client->notifications_send($ids, $this->notificationMessage, $this->notificationType);
			$this->facebook->api ( array(
				'method' => 'notifications.send'
				,'status' => $_POST['hello']
				,'uid'    => /* user's facebook id */
			) );
        }
    }
    
    /*----------- Examples of Usages ---------------*/
    
    $notifObj=new Notification();
    //$notifObj->notificationStatus(); //using this method you can show current limitation of your facebook application
    
    /*----- Send Email by using this method ---------*/
    //you can send email at most 100 users at a time. And each day your facebook application can use this method 4 times.
    // eg: $notifObj->sendEmailUsingApi('137373777,39344939');
    //$notifObj->sendEmailUsingApi('user_ids');  
    
    /*
      Your application can send a number of notifications or requests to a user in a day based on a number of metrics (or buckets). 
      The number of notifications and the number of requests are determined separately. 
      To get these numbers, call $notifObj->notificationStatus(); 
    */
    // eg: $notifObj->sendNotification('137373777,39344939');
    $notifObj->sendNotification('100004603869179');  
?>       