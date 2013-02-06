<?php

function url_promo_metabox() {
		global $post;
		global $wpdb; 
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$v_location = get_post_meta( $post->ID, 'v_location', true );
		$field_location = get_post_meta( $post->ID, 'field_location', true );
		$v_product = get_post_meta( $post->ID, 'v_product', true );
		$field_product = get_post_meta( $post->ID, 'field_product', true );
		$event = get_post_meta( $post->ID, 'event', true );
		$product = get_post_meta( $post->ID, 'product', true );
		$urllink = get_post_meta( $post->ID, 'urllink', true );
		$ptype = get_post_meta( $post->ID, 'ptype', true );
		$from = get_post_meta( $post->ID, 'from', true );
		$redURL = get_post_meta( $post->ID, 'redURL', true );
		$conURL = get_post_meta( $post->ID, 'conURL', true );
		$conNAME = get_post_meta( $post->ID, 'conNAME', true );
		$conTNAME = get_post_meta( $post->ID, 'conTNAME', true );
		$conUSER = get_post_meta( $post->ID, 'conUSER', true );
		$conPASSWORD = get_post_meta( $post->ID, 'conPASSWORD', true );
		$conQSTRING = get_post_meta( $post->ID, 'conQSTRING', true );
		$queV1 = get_post_meta( $post->ID, 'queV1', true );
		$queV2 = get_post_meta( $post->ID, 'queV2', true );
		$queV3 = get_post_meta( $post->ID, 'queV3', true );
		$errors ='<span style="color:green; font-weight:bold;">Good to go!</span>';
		if ( !preg_match( "/http(s?):\/\//", $urllink )) {
			$errors = '<span style="color:red; font-weight:bold;">Url not valid</span>';
			$urllink = '';
		}
		if ( !preg_match( "/http(s?):\/\//", $redURL )) {
			$rederrors = '<span style="color:red; font-weight:bold;">Url not valid</span>';
			$redURL = 'http://';
		}
	 
		// output invlid url message and add the http:// to the input field
		if( $errors ) { echo $errors; } 
		$post_title = get_the_title();?>
       	<script type="text/javascript">
			function setCaretTo(obj, pos){
			 if(obj.selectionStart){
			  obj.focus();
			  obj.setSelectionRange(pos, pos);
			 } else if(obj.createTextRange){
			  var range = obj.createTextRange();
			  range.move('character', pos);
			  range.select();
			 }
			};
			
			function readWrite(t){
			 if(!t.onclick.clicked){
			  t.onclick.clicked=true;
			  t.removeAttribute('readonly', 0);
			  setCaretTo(t, t.value.length);
			  t.blur();
			  t.focus();
			 }
			};
			</script>
		<p>
			<label for="v_location"><strong>Location Code Validation</strong><br />
			<input type="radio" name="v_location" value="true" <?php echo ($v_location == 'true')? 'checked="checked"':''; ?> />Validate Location<br>
			<input type="radio" name="v_location" value="false" <?php echo ($v_location == 'false')? 'checked="checked"':''; ?> />DO NOT Validate Location
		</p>
		<p>
			<label for="field_location"><strong>Location Input type</strong><br />
			<input type="radio" name="field_location" value="text" <?php echo ($field_location == 'text')? 'checked="checked"':''; ?> />Text Box<br>
			<input type="radio" name="field_location" value="select" <?php echo ($field_location == 'select')? 'checked="checked"':''; ?> />Select (dropdown)
		</p>
        <p><label for="event"><strong>Event/Location Codes:</strong><small>(seperated by commas)</small><br />
			<textarea id="event" style="width:98%; height:100px;" name="event" type="text" readonly onclick="readWrite(this);"><?php if( $event ) { echo $event; } ?></textarea></label></p>
		<p>
			<label for="v_product"><strong>Product Code Validation</strong><br />
			<input type="radio" name="v_product" value="true" <?php echo ($v_product == 'true')? 'checked="checked"':''; ?> />Validate Product<br>
			<input type="radio" name="v_product" value="false" <?php echo ($v_product == 'false')? 'checked="checked"':''; ?> />DO NOT Validate Product
		</p>
		<p>
			<label for="field_product"><strong>Product Input type</strong><br />
			<input type="radio" name="field_product" value="text" <?php echo ($field_product == 'text')? 'checked="checked"':''; ?> />Text Box<br>
			<input type="radio" name="field_product" value="select" <?php echo ($field_product == 'select')? 'checked="checked"':''; ?> />Select (dropdown)
		</p>
        <p><label for="product"><strong>Product/Other Codes:</strong><small>(seperated by commas)</small><br />
			<textarea id="product" style="width:98%; height:100px;" name="product" type="text" readonly onclick="readWrite(this);"><?php if( $product ) { echo $product; } ?></textarea></label></p>
        <p><label for="siteurl"><strong>Partner Portal Url:</strong><br />
			<input id="siteurl" style="width:95%;" name="siteurl" value="<?php if( $urllink ) { echo $urllink; } ?>" /></label></p>
		<p><label for="from"><strong>Send Emails From:</strong><br />
			<input id="from" style="width:95%;" name="from" value="<?php if( $from ) { echo $from; } ?>" /></label></p>
		<p><label for="new_partner_type"><strong>Partner Type:</strong><br />
			<?php $partner_type = array('Redirect'/*, 'Connect'*/, 'Query'); ?>
			<select name="new_partner_type" id="new_partner_type">
				<?php foreach($partner_type as $the_type) { ?>
					<?php if( $ptype && $ptype == $the_type ) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
					<option value="<?php echo $the_type; ?>" <?php echo $selected; ?>><?php echo $the_type; ?></option>
				<?php } ?>
			</select></p>
		<div id="redirectURL">
			<p>Please enter the url for the partner promotion redemption portal below.</p>
			<p><label for="redURL"><strong>Partner Redirect Url:</strong><br /><input id="redURL" style="width:95%;" name="redURL" value="<?php if( $redURL ) { echo $redURL; } ?>" /></label></p>
		</div>
		<div id="connectURL" style="display:none;">
			<p>Your have selected the partner type &lsquo;Connect&rsquo;, please enter the Database Host url, db name, db table name, db user, db password, and SQL query for the partner promotion redemption portal below.</p>
			<p><label for="conURL"><strong>Database Connection Url:</strong><br /><input id="conURL" style="width:95%;" name="conURL" value="<?php if( $conURL ) { echo $conURL; } ?>" /></label></p>
			<p><label for="conNAME"><strong>Database Name:</strong><br /><input id="conNAME" style="width:95%;" name="conNAME" value="<?php if( $conNAME ) { echo $conNAME; } ?>" /></label></p>
			<p><label for="conTNAME"><strong>Database Table Name:</strong><br /><input id="conTNAME" style="width:95%;" name="conTNAME" value="<?php if( $conTNAME ) { echo $conTNAME; } ?>" /></label></p>
			<p><label for="conUSER"><strong>Database User:</strong><br /><input id="conUSER" style="width:95%;" name="conUSER" value="<?php if( $conUSER ) { echo $conUSER; } ?>" /></label></p>
			<p><label for="conPASSWORD"><strong>Database Password:</strong><br /><input id="conPASSWORD" style="width:95%;" name="conPASSWORD" value="<?php if( $conPASSWORD ) { echo $conPASSWORD; } ?>" /></label></p>
            <p><label for="conQSTRING"><strong>Database SQL Query:</strong><br /><input id="conQSTRING" style="width:95%;" name="conQSTRING" value="<?php if( $conQSTRING ) { echo $conQSTRING; } ?>" /></label></p>
            <p>Example &ldquo;SELECT a_column FROM table_name WHERE condition_column = &lsquo;unused_variable&rsquo;&rdquo;<br /></p>
		</div>
		<div id="queryURL" style="display:none;">
			<p>Your have selected the partner type &lsquo;Query&rsquo;, please enter the partner Query variables below</p>
			<p><label for="queV1"><strong>Name Variable:</strong><br /><input id="queV1" style="width:95%;" name="queV1" value="<?php if( $queV1 ) { echo $queV1; } ?>" /></label></p>
            <p><label for="queV2"><strong>Email Variable:</strong><br /><input id="queV2" style="width:95%;" name="queV2" value="<?php if( $queV2 ) { echo $queV2; } ?>" /></label></p>
            <p><label for="queV3"><strong>Partner Code Variable:</strong><br /><input id="queV3" style="width:95%;" name="queV3" value="<?php if( $queV3 ) { echo $queV3; } ?>" /></label></p>
		</div>
			<script type="text/javascript">
				var testing = jQuery("select").val();
				function displayVals() {
					  var singleValues = jQuery("select#new_partner_type").val();
					  var multipleValues = jQuery("select#new_partner_type").val() || [];
					  if (singleValues == 'Connect') {
						  jQuery('#connectURL').slideDown();
					  } else if (singleValues != 'Connect') {
						  jQuery('#connectURL').slideUp();
					  } 
					  if (singleValues == 'Query') {
						  jQuery('#queryURL').slideDown();
					  } else if (singleValues != 'Query') {
						  jQuery('#queryURL').slideUp();
					  }
						  
				}
			
				jQuery("select#new_partner_type").change(displayVals);
				displayVals();
			</script>
		<?php 
		if ($ptype) {
			
		} 
		
	}