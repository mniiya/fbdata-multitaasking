<?php

// include the relevant facebook php sdk files
require_once("facebook-php-sdk-master/facebook-php-sdk-master/src/facebook.php");

// connecting to FB
$config = array(
      'appId' => '1429015913978309',
      'secret' => '959ea840a291f8764a938bfde31f7bb4',
      'cookie'=> 'false'
  );

// creating fb object
$facebook = new Facebook($config);
var_dump($facebook);
 
// getting logged in user and output their public info
$user = $facebook->getUser();
var_dump($user);
 
/* 
   if ($user) {
	echo '1';
      try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
		var_dump($user_profile);
      } catch (FacebookApiException $e) {

        $user = null;
      }
    }  */
	
// if the user exists/is logged in then set the redirect URL and generate a logout URL
 if ($user) {
echo '1';
	$params = array(
	'next' => 'http://www.melissaniiya.com/uci/uci-multitasking/'
	);

	$logoutUrl = $facebook->getLogoutUrl($params);
	
// otherwise, we need user to log in, and we need to obtain permissions
} else {

	$params = array(
		// here's where we define the permissions we'll need to get from fb
		scope => 'read_stream,user_location,email,user_about_me,user_online_presence,friends_online_presence,friends_location,friends_about_me,user_status'
		//redirect_uri => 'http://www.melissaniiya.com/uci/uci-multitasking/'
	);
	
	$loginUrl = $facebook->getLoginUrl($params);
};

   ?>
   


<?php
// if there is an active session, then access data for the user
if($_SESSION['fb_1429015913978309_access_token']) {
		
		// the GET request you send will depend on what data you want to access
		
		// USER DATA *******************************
        $user_profile = $facebook->api('/me','GET');
		
		$user_data['name'] = $user_profile['name'];
		$user_data['id'] = $user_profile['id'];
		$user_data['location']['name']=$user_profile['location']['name'];
		$user_data['location']['location_id']= $user_profile['location']['id'];
		var_dump($user_data);
        
		// call function to save this to database
		
		// FRIEND DATA *****************************
		$friends = $facebook->api('/me/friends?fields=location','GET');
		$friends_data = $friends['data'];
		// for each friend, get their ID# and location
		foreach ($friends_data as $f) {
			
			echo $f['id'].' ';
			echo $f['location']['name'].' ';
			echo 'location id: '.$f['location']['id'].'<br />';
			
			// call function to save to friends db, add a new row
			// 
			
		}
		
		// USER POST ACTIVITY **********************
		$feed = $facebook->api('/me/posts?since=2013-10-25','GET');
		//var_dump($feed);
		
		
		
		///$likes = $facebook->api('/me/objects/object?since=2013-01-01','GET');
		//var_dump($likes);
		
}
	
// if no active session, show link for logging in and request permissions
else {
?>

	<a href="<?php echo $loginUrl; ?>" target="_parent">Login & Connect</a>

<?php } ?>