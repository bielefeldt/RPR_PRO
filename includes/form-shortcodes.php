<?php

/******************************
* add shortcodes
******************************/

function promo_form( $atts, $content = null ) {
	//define enqription key
	$wp_wall_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
	global $wpdb;
	global $post;
	global $table_name;
	$v_location = get_post_meta( $post->ID, 'v_location', true );
	$field_location = get_post_meta( $post->ID, 'field_location', true );
	$v_product = get_post_meta( $post->ID, 'v_product', true );
	$field_product = get_post_meta( $post->ID, 'field_product', true );
	$from = get_post_meta( $post->ID, 'from', true );
	$event = get_post_meta( $post->ID, 'event', true );
	$product = get_post_meta( $post->ID, 'product', true );
	$urllink = get_post_meta( $post->ID, 'urllink', true );
	$ptype = get_post_meta( $post->ID, 'ptype', true );
	$from = get_post_meta( $post->ID, 'from', true );
	$redURL = get_post_meta( $post->ID, 'redURL', true );
	$promo_options = get_option('promo_settings');
	$is_campaign = $promo_options['enable_c'];
	$wait = $promo_options['wait'];
	$css = get_post_meta( $post->ID, 'added-css', true );
	$script = get_post_meta( $post->ID, 'added-js', true );
	$user_age = $promo_options['age'];
	if ($is_campaign) {
		$api = en_de('encode', $promo_options['api_k'], ED_KEY);
		$l = en_de('encode', $promo_options['l_id'], ED_KEY);
		$api_k = $api;
		$l_id = $l;
		//echo $api_k . '-' . $l_id; 
	}
	if ($ptype == 'Connect') {
		$conURL = get_post_meta( $post->ID, 'conURL', true );
		$conNAME = get_post_meta( $post->ID, 'conNAME', true );
		$conTNAME = get_post_meta( $post->ID, 'conTNAME', true );
		$conUSER = get_post_meta( $post->ID, 'conUSER', true );
		$conPASSWORD = get_post_meta( $post->ID, 'conPASSWORD', true );
		$conQSTRING = get_post_meta( $post->ID, 'conQSTRING', true );
	} else if ($ptype == 'Query') {
		$queV1 = get_post_meta( $post->ID, 'queV1', true );
		$queV2 = get_post_meta( $post->ID, 'queV2', true );
		$queV3 = get_post_meta( $post->ID, 'queV3', true );
	}
	if ($field_location == 'text') {
		$location_field_type = '
			<p class="location forms">
				<label for="location">Location Code:</label><input type="text" name="location" id="locCode" />
					<small style="padding-left: 10px;font-size: 10px;">Include dashes</small>
			</p>';
	} else {
		$locations = explode(",", $event);
		
		$location_field_type = '
			<p class="dob forms">
				<label class="custom-select" for="location">Location Code:</label>
				<select name="location" class="dropdown">
					<option value="0">Select One</option>';
		
		foreach ($locations as $the_loc) {
			$location_field_type .= '<option value="'.$the_loc.'">'.$the_loc.'</option>';
		}
		$location_field_type .= '</select></p>';
	}
	if ($field_product == 'text') {
		$product_field_type = '
			<p class="product forms">
				<label for="product">Product Code:</label><input type="text" name="product" id="proCode" />
					<small style="padding-left: 10px;font-size: 10px;">Include dashes</small>
			</p>';
	} else {
		$products = explode(",", $product);
		
		$product_field_type = '
			<p class="dob forms">
				<label class="custom-select" for="product">Product Code:</label>
					<select name="product" class="dropdown">
						<option value"0">Select One</option>';
		
		foreach ($products as $the_pro) {
			$product_field_type .= '<option value="'.$the_pro.'">'.$the_pro.'</option>';
		}
		$product_field_type .= '</select></p>';
	}
   extract( shortcode_atts( array(
      'start_message' => 'A gift code has been submitted at '.$_SERVER["HTTP_REFERER"].'.',
	  'end_message' => '',
      // ...etc
      ), $atts ) );
	  $form = '
	  			<style>'. $css .'</style>';
	  $form .= '<div class="message" style="display:none; "><div id="alert" style="padding:10px; border:1px solid #999; background-color:#E7E7E7;"></div></div>';
	  $form .= '<form id="contactform" class="appnitro form"  method="post" action="'.$wp_wall_plugin_url.'/promo-codes.php">';
	  $form .= '<p class="name forms"><label for="name">Name:</label><input type="text" name="name" id="name" value="" /></p>';
	  if (strtolower($user_age) != 'any') {
		  $form .= '<p class="dob forms"><label class="custom-select" for="dob">Date of Birth:</label><select name="month" class="dropdown">
										<option value="01">January</option>
										<option value="02">February</option>
										<option value="03">March</option>
										<option value="04">April</option>
										<option value="05">May</option>
										<option value="06">June</option>
										<option value="07">July</option>
										<option value="08">August</option>
										<option value="09">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
						 </select>
						<select name="day">';
			for ($i = 01; $i < 32; $i++) { $form .= '<option value="'.sprintf("%02s",$i).'">'.sprintf("%02s",$i).'</option>'; }
										
			$form .= '</select>
					  <select name="year">';
			for ($i = date('Y'); $i > 1900; $i--) { $form .= '<option value="'.sprintf("%02s",$i).'">'.sprintf("%02s",$i).'</option>'; }
			$form .= '</select></p>  ';
	  }
	  $form .= '<p class="email forms"><label for="email">E-mail:</label><input type="text" name="email" id="email" /></p>';
	  $form .= '<p class="redeem forms"><label for="license">Redemption Code:</label><input type="text" name="license" id="reCode" /><small style="padding-left: 10px;font-size: 10px;">Case sensitive</small></p>';
  	  $form .= $location_field_type;
	  $form .= $product_field_type;
	  $form .= '<span style="display:none;"><p>Honeypot:</p> <input type="text" name="last" value="" id="last" /></span>';
	  $form .= '
	  	<input type="hidden" name="v_location" value="'.$v_location.'" id="" />
	  	<input type="hidden" name="v_product" value="'.$v_product.'" id="" />
	  	<input type="hidden" name="field_location" value="'.$field_location.'" id="" />
	  	<input type="hidden" name="field_product" value="'.$field_product.'" id="" />
	  	<input type="hidden" name="event_array" value="'.$event.'" id="" />
		<input type="hidden" name="product_array" value="'.$product.'" id="" />
	  	<input type="hidden" name="tablename" value="'.$table_name.'" id="" />
		<input type="hidden" name="from_email" value="'.$from.'" id="" />
		<input type="hidden" name="ptype" value="'.$ptype.'" id="" />
		<input type="hidden" name="redURL" value="'.$redURL.'" id="" />
		<input type="hidden" name="start_message" value="'.esc_attr($start_message).'" id="" />
		<input type="hidden" name="end_message" value="'.esc_attr($end_message).'" id="" />
		<input type="hidden" name="is_campaign" value="'.$is_campaign.'" id="" />
		<input type="hidden" name="wait" value="'.$wait.'" id="" />';
	  if ($is_campaign) {
		$form .= '
				<input type="hidden" name="api_k" value="'.$api_k.'" id="" />
				<input type="hidden" name="l_id" value="'.$l_id.'" id="" />';
	  }
	  if ($ptype == 'Connect') {
		$form .= '
			<input type="hidden" name="conURL" value="'.$conURL.'" id="" />
			<input type="hidden" name="conNAME" value="'.$conNAME.'" id="" />
			<input type="hidden" name="conTNAME" value="'.$conTNAME.'" id="" />
			<input type="hidden" name="conUSER" value="'.$conUSER.'" id="" />
			<input type="hidden" name="conPASSWORD" value="'.$conPASSWORD.'" id="" />
			<input type="hidden" name="conQSTRING" value="'.$conQSTRING.'" id="" />
			<input type="hidden" name="queURL" value="'.$queURL.'" id="" />
			<input type="hidden" name="start_message" value="'.esc_attr($start_message).'" id="" />
			<input type="hidden" name="end_message" value="'.esc_attr($end_message).'" id="" />';
	  } else if ($ptype == 'Query') {
		$form .= '<input type="hidden" name="queV1" value="'.$queV1.'" id="" /><input type="hidden" name="queV2" value="'.$queV2.'" id="" /><input type="hidden" name="queV3" value="'.$queV3.'" id="" />';
	  }
		$form .= '<input type="hidden" name="pID" value="'.$post->ID.'" id="" />
	  ';
	  $form .= '<p class="submit forms">  
                	        <input type="submit" value="Submit" />  
                	    </p>';
	  $form .= '</form><script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script><script type="text/javascript" src="'.$wp_wall_plugin_url.'/js/jquery.form.js"></script><script type="text/javascript" src="'.$wp_wall_plugin_url.'/js/jquery.formfooter.js"></script><script type="text/javascript">'.$script.'</script>';
	  return $form;
}
add_shortcode( 'promo-form', 'promo_form' );
