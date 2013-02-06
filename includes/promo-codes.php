<?php
define( 'BLOCK_LOAD', true );
//define("ED_KEY",  $_SERVER['SERVER_NAME']);

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/RPR_REDEEMER/response-promo-redeemer.php' );
require_once(  $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/RPR_REDEEMER/includes/en_de.php'); // encryption/decryption function
global $wpdb; 
global $post;
global $post_id;
$postID = $_REQUEST['pID'];

echo $post_id;
// parse query string

function check_values($string, $check_val) {
	$str = strtolower($string);
	$array = explode(",",$str);
	$check_val = strtolower($check_val);
	if (in_array($check_val,$array, true)) {
		return true;
	} else {
		return false;
	}
}
global $today;
$today = date('m-d-Y');
function expired($the_date) {
	global $today;
	$thedate = $the_date.' 00:00:00.0';
	$newdate = strtotime($the_date);
	$bool;
	if ($newdate <= strtotime('today')) { $bool = true; }
	else $bool = false;
	return $bool;
}
function active($the_date) {
	global $today;
	$thedate = $the_date.' 00:00:00.0';
	$newdate = strtotime($the_date);
	$bool;
	if ($newdate > strtotime('today')) { $bool = true; }
	else $bool = false;
	return $bool;
}
global $remainder;
function been_twentyfive($the_date, $waiting) {
	global $remainder;
	global $today;
	$wait_days = $waiting/24;
	// set last used date
	$last_date = new DateTime($the_date);
	// set todays date
  	$today_here = new DateTime(date("Y-m-d H:i:s"));
	// activate bool
	$bool;
    $interval = $today_here->diff($last_date);

	$year     = $interval->y;
    $month    = $interval->m;
    $day      = $interval->d;
    $hour     = $interval->h;
    $minute   = $interval->i;
    $second   = $interval->s;
	//echo $minute .' minutes<br>'.$hour.'hours <br>'.$day.' day <br>'.$waiting.' waiting<br>';
    //echo (string)$day +1;
	if((string)$day < $wait_days){
    	if((string)$hour > $waiting){
        	$bool = 1;
        	//echo 'in 1 <br>';
    	} else { 
			//echo 'in 2 <br>';
      		if((string)$hour > 0 && (string)$hour < $waiting){
      			//echo 'in 2.1<br>';
				$bool = 0;
        		$hour_diff = $waiting - (string)$hour;
				$remainder = $hour_diff.' hour(s)';
      		} elseif((string)$hour <= 0){
      			//echo 'in 2.1.1<br>';
      			$bool = 0;
      			$hour_diff = $waiting - 1;
      			$remainder = $hour_diff.' hour(s)';
      		} elseif((string)$minute > 0){
      			//echo 'in 2.2';
				$bool = 0;
        		$hour_diff = 60 - (string)$minutes;
				$remainder = $hour_diff.' minutes';
      		} else {
      			//echo 'in 2.3';
				$bool = 0;
				$remainder = $waiting.' hour(s)';
			}
		}	
   } else { 
   		$bool = 1; 
   		//echo 'in 1.1<br>';
   }
   
   //echo $bool.'<br>';
   
   //$bool = 0;
   return $bool;
}
$the_referer = $_SERVER["HTTP_REFERER"];
//        Who you want to recieve the emails from the form. (Hint: generally you.)
$sendto = $_REQUEST['email'];
$bcc = 'bbielefeldt@thepowertoprovoke.com';
$sendFrom = $_REQUEST['from_email'];
$redURL = $_REQUEST['redURL'];
$table_name = $_REQUEST['tablename'] ;
$ptype = $_REQUEST['ptype'] ;
$end_message = $_REQUEST['end_message'] ;
$start_message = $_REQUEST['start_message'] ;
$is_campaign = $_REQUEST['is_campaign'] ;
$wait = $_REQUEST['wait'] ;
$v_location = $_REQUEST['v_location'];
$v_product = $_REQUEST['v_product'];
$event_array = $_REQUEST['event_array'];
$product_array = $_REQUEST['product_array'];
if ($is_campaign) {
	$api = $_REQUEST['api_k'] ;
	$l = $_REQUEST['l_id'] ;
	$api_k = en_de('decode', $api, ED_KEY);
	$l_id = en_de('decode', $l, ED_KEY);
	include($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/RPR_REDEEMER/includes/campaign_monitor/add/user.php');
}
global $bdate;
$allowed_age = $promo_options['age'];
if (strtolower($allowed_age) != 'any') {
	$bdate = strtotime($_REQUEST['year'].'-'.$_REQUEST['month']."-".$_REQUEST['day']);
	$user_dob = $_REQUEST['year'].'-'.$_REQUEST['month']."-".$_REQUEST['day'];
	$age = (time()-$bdate)/31536000;
	if($age >= $allowed_age) {
		   $user_age = true;
	}
	else {
			$user_age = false;
	}
} else {
	$user_age = true;
}
// --------------------------- Thats it! don't mess with below unless you are really smart! ---------------------------------

//Setting used variables.
$alert = '';
$pass = 0;
$add_pass = 0;

$license = $_REQUEST['license'];

	//        The subject you'll see in your inbox
$subject = 'Promotion portal submission at '.$the_referer;

//        Message for the user when he/she doesn't fill in the form correctly.
$errormessage = 'Oops! There seems to have been a problem. May we suggest...';
$errormessage_end = 'If you are having trouble reading your card please contact customer support as directed below.';

//        Message for the user when he/she fills in the form correctly.
$thanks = "Thank you for your submission your Partner promotion code will be emailed to you go check...";

//        Message for the bot when it fills in in at all.
$honeypot = "You filled in the honeypot! If you're human, try again!";

//        Various messages displayed when the fields are empty.
$emptyname =  'Entering your Name?';
$emptylocation = 'Entering a location code?';
$emptyproduct = 'Entering a product code?';
$emptylicense =  'Entering your Promotion code?';
$emptyemail = 'Entering your Email Address?';


//       Various messages displayed when the fields are incorrectly formatted.
$alertname =  'Entering your Name using only the standard alphabet?';
$alertlicense =  'Entering your Promotion Code using only the standard alphanumeric characters?';
$invalidlicense =  'The Promotion Code '.$license.' is invalid. Make sure you have entered O(s) and Zero(s) correctly. please try again';
$age_restirction = 'Your birth date tells me you are under the age of '.$allowed_age.', if this is incorrect please correct your date of birth';
$alertemail = 'Entering your Email in this format: <i>name@example.com</i>?';
$alertlocation = 'You have entered an invalid location code.';
$alertproduct = 'You have entered an invalid product code.';
$alertactive = 'Your code has not been activated yet.';
$alertexpire = 'Your code is expired.';

//bool for fivedrafts
$license_check = en_de('encode', $license, ED_KEY);
$data_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE our_code = '".$license_check."' AND user_name = 'Not Used'", $license_check ) );
$fivesdrafts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE our_code = '".$license_check."'", $license_check ) );
foreach ( $fivesdrafts as $fivesdraft ) {
			$time = $fivesdraft->time_entered;
			$partner_code = $fivesdraft->partner_code;
			$partner_code = en_de('decode', $partner_code, ED_KEY);
			$user_email = $fivesdraft->user_email;
			$user_name = $fivesdraft->user_name;
			$expiration = $fivesdraft->expiration;
			$partner_expiration = $fivesdraft->partner_expiration;
			$activation = $fivesdraft->activation;
			$value = $fivesdraft->value;
		}
	$newactive = date("m-d-Y",strtotime($activation));
	$newexpire = date("F j, Y",strtotime($expiration));
	$new_p_expire = date("F j, Y",strtotime($partner_expiration));
//echo $data_count;
// Sanitizing the data, kind of done via error messages first. Twice is better!
function clean_var($variable) {
    $variable = strip_tags(stripslashes(trim(rtrim($variable))));
  return $variable;
}

//The first if for honeypot.
if ( empty($_REQUEST['last']) ) {

	// A bunch of if's for all the fields and the error messages.
	
	if ( empty($_REQUEST['name']) ) {
		$pass = 1;
		$alert .= "<li>" . $emptyname . "</li>";
	} elseif ( ereg( "[][{}()*+?\\^$|]", $_REQUEST['name'] ) ) {
		$pass = 1;
		$alert .= "<li>" . $alertname . "</li>";
	}
	if ( empty($_REQUEST['email']) ) {
		$pass = 1;
		$alert .= "<li>" . $emptyemail . "</li>";
	} elseif ( !eregi("^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$", $_REQUEST['email']) ) {
		$pass = 1;
		$alert .= "<li>" . $alertemail . "</li>";
	}
	if (!$user_age) {
		$pass = 1;
		$alert .= "<li>" . $age_restirction . "</li>";
	}
	
	if ( empty($_REQUEST['license']) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $emptylicense . "</li>";
	} elseif ( ereg( "[][{}()*+?.\\^$|]", $_REQUEST['license'] ) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $alertlicense . "</li>";
	} elseif ($data_count == 0 || $data_count > 1) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $invalidlicense . " ...</li>";
	}  elseif ($user_email != "xxxx" || $user_name != 'Not Used') {
		$pass = 1;
		$alert .= "<li>" . $invalidlicense . "  ...</li>";
	} elseif (active($activation)) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $alertactive . " ...</li>";
	  	mail($sendFrom, "Unactive Code Leak!", "The promotion code ".$_REQUEST['license']." has been tried ".$t_host.$t_uri.". ".$_REQUEST['name']." - ".$_REQUEST['email']." - ".$_REQUEST['location']." - ".$_REQUEST['product'] , $header);
	} elseif (expired($expiration)) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $alertexpire . " ...</li>";
	}
	if ( empty($_REQUEST['location']) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $emptylocation . "</li>";
	} elseif ( $v_location == 'true' && !check_values($event_array, $_REQUEST['location'] ) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $alertlocation . "</li>";
	}
	if ( empty($_REQUEST['product']) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $emptyproduct . "</li>";
	} elseif ( $v_product == 'true' && !check_values($product_array, $_REQUEST['product'] ) ) {
		$pass = 1;
	    $add_pass = 1;
		$alert .= "<li>" . $alertproduct . "</li>";
	}
	
	
	//If the user err'd, print the error messages.
	if ( $pass==1 ) {

		//This first line is for ajax/javascript, comment it or delete it if this isn't your cup o' tea.
	echo "<script>$(\".message\").hide(\"slow\").show(\"slow\"); </script>";
	echo "<b>" . $errormessage . "</b>";
	echo "<ul>";
	echo $alert;
	echo "</ul>";
	  if ($add_pass == 1) {
			echo $errormessage_end;
	  }
	 

	// If the user didn't err and there is in fact a message, time to email it.
	} elseif ($pass==0) {
		$time = current_time('mysql');
		$email = $_REQUEST['email'];
		$name = $_REQUEST['name'];
		$p_code = $partner_code;
		$event_location = $_REQUEST['location'];
		$product = $_REQUEST['product'];
		 $lastactive = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_email = '".$email."' ORDER BY time_entered ASC", $email ) );
		  $time_entered;
		  $time_cnt = 0;
		  foreach ( $lastactive as $lastactives ) {
			$time_entered[$time_cnt] = $lastactives->time_entered;
			//echo $time_entered[$time_cnt].' - count = '.$time_cnt.'<br>';
			$time_cnt = $time_cnt+1;
		  }
	  	// ever is if the user has ever registered a code before
	  $ever;
	  	// last entry gets the time for the last entry by that email
	  $time_cnt_ar = count($time_entered);
	  $time_cnt_ar = $time_cnt_ar - 1;
	  $last_entry;
	  if ($time_cnt == 0 || $time_cnt == NULL) { $ever = false; }
	  elseif ($time_cnt > 0 && $time_cnt != NULL) { $ever = true; $last_entry = $time_entered[$time_cnt_ar]; }
	  if (been_twentyfive($last_entry, $wait) || !$ever || $wait < 1) {
		//echo "good to go";
				  $wpdb->update( 
					  $table_name, 
					  array( 
						  'time_entered' => $time,	// string
						  'user_name' => $name,	// integer (number) 
						  'user_email' => $email,
						  'user_dob' => $user_dob,
						  'event_location' => $event_location,
						  'p_o_id' => $product,
						  
					  ), 
					  array( 
						  'our_code' => $license_check,
						  'user_name' => 'Not Used'
					  ), 
					  array( 
						  '%s',	// value1
						  '%s',	// value2
						  '%s',	// value
						  '%s',	// value
						  '%s',	// value
						  '%s'	// value
					  ), 
					  array( '%s' ) 
				); 
				  //Construct the message.
				  if ($is_campaign) {
					$wrap = new CS_REST_Subscribers($l_id, $api_k);
					$check_list = $wrap->get($email);
					
					//echo "Result of GET /api/v3/subscribers/{list id}.{format}?email={email}\n<br />";
					if($check_list->was_successful()) {
					  // echo "Got subscriber <pre>";
					  // var_dump($check_list->response);
						$result = $wrap->unsubscribe($email);
						$result = $wrap->update($email, array(
						  'EmailAddress' => $email,
						  'Name' => $name,
						  'CustomFields' => array(
							  array(
								  'Key' => 'event_location',
								  'Value' => $event_location
							  ),
							  array(
								  'Key' => 'product_code',
								  'Value' => $product
							  ),
							  array(
								  'Key' => 'our_code',
								  'Value' => $license
							  ),
							  array(
								  'Key' => 'partner_code',
								  'Value' => $p_code
							  ),
							  array(
								  'Key' => 'signup_date',
								  'Value' => $time
							  ),
							  array(
								  'Key' => 'dob',
								  'Value' => $user_dob
							  ),
							  array(
								  'Key' => 'expiration',
								  'Value' => $new_p_expire
							  ),
							  array(
								  'Key' => 'value',
								  'Value' => $value
							  ),
							  array(
								  'Key' => 'post_id',
								  'Value' => $postID
							  ),
							  array(
								  'Key' => 'redirect_url',
								  'Value' => $redURL
							  ),
						  ),
						  "Resubscribe" => true,
						  "RestartSubscriptionBasedAutoresponders" => true
					  ));
					} else {
					  //echo 'Failed with code '.$check_list->http_status_code."\n<br /><pre>";
					  // var_dump($check_list->response);
					  $result = $wrap->add(array(
						  'EmailAddress' => $email,
						  'Name' => $name,
						  'CustomFields' => array(
							  array(
								  'Key' => 'event_location',
								  'Value' => $event_location
							  ),
							  array(
								  'Key' => 'product_code',
								  'Value' => $product
							  ),
							  array(
								  'Key' => 'our_code',
								  'Value' => $license
							  ),
							  array(
								  'Key' => 'partner_code',
								  'Value' => $p_code
							  ),
							  array(
								  'Key' => 'signup_date',
								  'Value' => $time
							  ),
							  array(
								  'Key' => 'dob',
								  'Value' => $user_dob
							  ),
							  array(
								  'Key' => 'expiration',
								  'Value' => $new_p_expire
							  ),
							  array(
								  'Key' => 'value',
								  'Value' => $value
							  ),
							  array(
								  'Key' => 'post_id',
								  'Value' => $postID
							  ),
							  array(
								  'Key' => 'redirect_url',
								  'Value' => $redURL
							  ),
						  ),
						  "Resubscribe" => true,
						  "RestartSubscriptionBasedAutoresponders" => true
					  ));
					}
					//echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
					  if($result->was_successful()) {
						  $response = "Subscribed with code ".$result->http_status_code;
					  } else {
						  $response = 'Failed with code '.$result->http_status_code."\n\n ";
						  //var_dump($result->response);
					  }
					  $start_message = str_replace("<br />", "\r", $start_message);
					  $end_message = str_replace("<br />", "\r", $end_message);
					  $start_message = str_replace("<nl />", "\n\n", $start_message);
					  $end_message = str_replace("<nl />", "\n\n", $end_message);
					  $message = $start_message . " ";
					  $message .= $end_message;
					  $instructions = $message;
					  $t_host = $_SERVER['HTTP_HOST'];
						$t_uri = $_SERVER['REQUEST_URI'];
						mail($bcc, "Promoportal Used", "The promotion portal has been used at ".$t_host.$t_uri, $header);
					} else {
						$start_message = str_replace("<br />", "\r", $start_message);
						$end_message = str_replace("<br />", "\r", $end_message);
						$start_message = str_replace("<nl />", "\n\n", $start_message);
						$end_message = str_replace("<nl />", "\n\n", $end_message);
						$message = $start_message . "";
						$message .= ", go to " . $redURL . "\n";
						$message .= "and enter the code, " . $partner_code . $end_message;
						
						$header = 'From:'. $sendFrom . "\r\n";
						//BCC Not working Correctly $headers .= 'Bcc:'. $bcc . "\r\n";
						if ($ptype == 'Connect') {
							$conURL = $_REQUEST['conURL'] ;
							$conNAME = $_REQUEST['conNAME'] ;
							$conTNAME = $_REQUEST['conTNAME'] ;
							$conUSER = $_REQUEST['conUSER'] ;
							$conPASSWORD = $_REQUEST['conPASSWORD'] ;
							$conQSTRING = $_REQUEST['conQSTRING'] ;
						} else if ($ptype == 'Query') {
							$queV1 = $_REQUEST['queV1'] ;
							$queV2 = $_REQUEST['queV2'] ;
							$queV3 = $_REQUEST['queV3'] ;
							// construct query string
							$username = str_replace(" ","_",$name);
							$theQUERY = $redURL.'?'.$queV1.'='.$username.'&'.$queV2.'='.$email.'&'.$queV3.'='.$partner_code;
						}
						//Mail the message - for production
						$t_host = $_SERVER['HTTP_HOST'];
						$t_uri = $_SERVER['REQUEST_URI'];
						mail($sendto, $subject, $message, $header);
						//Added second send mail sys to notify me when it is being used
						mail($bcc, "Promoportal Used", "The promotion portal has been used at ".$t_host.$t_uri, $header);
						//This is for javascript, 
						$instructions = clean_var($_REQUEST['name']) . ", thank you for entering your gift code.\n";
						$instructions .= "The information needed to complete the process is listed below and will be emailed to, " . clean_var($_REQUEST['email']) . "<br><br>";
						if ($ptype == 'Query') {
						$instructions .= "Go to <a href='{$theQUERY}' target='_blank'>" . $redURL . "</a>, ";
						} else {
							$instructions .= "Go to <a href='{$redURL}' target='_blank'>" . $redURL . "</a>, ";
						}
						$instructions .= " and enter the code: ".$partner_code .' '. $end_message;
					}
	  } else { 
					//echo "not time yet"; 
					$instructions = "You must wait ".$remainder." to enter another promotion code!";
	  }
		
		//This is for javascript, 
	    
		
		echo "<script>jQuery(\".message\").hide(\"slow\").show(\"slow\").animate({opacity: 1.0}, 4000); jQuery(':input').clearForm() </script>";
		echo $instructions."<br>";
		//echo $thanks;
		if ($ptype == 'Query') {
			echo '<script type="text/javascript" language="javascript"> 
			window.open("'.$theQUERY.'"); 
			</script>';
		}
		die();
//Echo the email message - for development
		//echo "<br/><br/>" . $message;

	}
	
//If honeypot is filled, trigger the message that bot likely won't see.
} else {
	echo "<script>jQuery(\".message\").hide(\"slow\").show(\"slow\"); </script>";
	echo $honeypot;
}

?>