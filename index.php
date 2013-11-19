<?php

$mysqli = new mysqli('localhost', 'w3e548bk_multi', 'ucimultitask123', 'w3e548bk_multitask');

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
echo $mysqli->host_info . "\n";

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
		scope => 'read_stream,user_location,email,user_about_me,user_online_presence,friends_online_presence,friends_location,friends_about_me,user_status,read_mailbox'
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
		//var_dump($user_data);
        
		// update php
		
		$insert="INSERT INTO participant VALUES ('','".$user_data['id']."','".$user_data['name']."','".$user_data['location']['name']."','".$user_data['location']['location_id']."')";
		if (!mysqli_query($mysqli, $insert)) {
			die('Error: '.mysqli_error($mysqli));
		}
		else {
			echo 'record added';
		}
		
		// call function to save this to database
		
		// FRIEND DATA *****************************
		$friends = $facebook->api('/me/friends?fields=location','GET');
		$friends_data = $friends['data'];
		// for each friend, get their ID# and location
		foreach ($friends_data as $f) {
			/*
			echo $f['id'].' ';
			echo $f['location']['name'].' ';
			echo 'location id: '.$f['location']['id'].'<br />';
			*/
			// call function to save to friends db, add a new row
			// 
			
		}
		
		// USER POST ACTIVITY **********************
		
		//	Given start and end date, pull all requested data:
		$since = '2013-09-10';
		$until = '2013-11-17';
		
		$request = '/me/posts?since='.$since.'&until='.$until;
		
		$feed_request = $facebook->api($request,'GET');
		$feed = $feed_request['data'];
		///me/posts - get all posts made by that individual (includes posts made on other people's timelines)
		
		foreach ($feed as $f) {
		
		//- photo, video, link, or status update
			echo 'type'.$f['type'].'<br />';
			echo 'status type '.$f['status_type'].'<br />';
		//	- ID of person posting
			echo 'poster id '.$f['from']['id'].'<br />';
		//- device used
			echo 'device '.$f['application']['namespace'];
			
		//- location when posting
			echo 'location '.$f['place']['id'];
			echo $f['place']['name'];
		
		//- created time (timestamp)
			echo 'created time '.$f['created_time'].'<br />';
			echo 'updated time '.$f['updated_time'].'<br />';
		
		//- comments and likes from that post
		//	- timestamp of like/comment
			$likes = Array();
			$likes = $f['likes']['data'];
			
			
			foreach ($likes as $l) {
				// get who liked
				// no timestamp available from Facebook API :( could we get notifications?
				echo 'likes:<br />';
				echo $l['id'].'<br />';
			}
			
			$comments = $f['comments']['data'];
			foreach ($comments as $c) {
				echo 'comments<br/>';
				// id of commenter
				echo $c['from']['id'];
				// created time/time stamp
				echo $c['created_time'];
				// # likes on this comment
				echo $c['like_count'];
				// whether current likes this comment
				echo $c['user_likes'];
				// jsonify/serialize comment array
			}
			
		}
		// MESSAGE ACTIVITY (this includes chats)
		$msg_get = $facebook->api('me/inbox/?since='.$since.'&until='.$until,'GET');
		$conversation = $msg_get['data'];
		
		$new_msg_list = Array();
		
		foreach ($conversation as $m) {
			unset($m['id']);
			unset($m['paging']);
			foreach ($m['to']['data'] as $to => $val) {
				
				unset($m['to']['data'][$to]['name']);
				
			}
			foreach ($m['comments']['data'] as $k => $msgs) {
				unset($m['comments']['data'][$k]['id']);
				unset($m['comments']['data'][$k]['from']['name']);
				unset($m['comments']['data'][$k]['message']);
			}
			
			
			$new_msg_list[] = $m;
		}  
		// save  this array to db
		
		///$likes = $facebook->api('/me/objects/object?since=2013-01-01','GET');
		//var_dump($likes);
		
}
	
// if no active session, show link for logging in and request permissions
else {
?>

	<a href="<?php echo $loginUrl; ?>" target="_parent">Login & Connect</a>

<?php } ?>