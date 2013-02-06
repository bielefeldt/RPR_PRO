<?php
// include plugin styles
global $wpdb;
global $post;
global $post_id;
global $table_name;
include('rpr_list_tables.php');
function promo_add_options_link() {
	add_menu_page('RPR Redemption Portal Options', 'RM Promo Options', 'edit_posts', 'promo-options', 'promo_options_page');
	add_submenu_page('promo-options', 'Used Code', 'Used Codes', 'edit_posts', 'used-codes', 'rpr_used_page');
	add_submenu_page('promo-options', 'Un-Used Code', 'Un-Used Codes', 'edit_posts', 'unused-codes', 'rpr_unused_page');
	add_submenu_page('promo-options', 'Edit Code', 'Edit Codes', 'edit_posts', 'edit-codes', 'rpr_edit_codes');
}
function download_complete($used) {
	global $table_name;
	if ($used == 1) return '<a href="tools.php?page=export&rmcsv='.$table_name.'&used=1" id="csvDownload">Download The Used .CSV file here</a>';
	elseif ($used == 0) return '<a href="tools.php?page=export&rmcsv='.$table_name.'&used=0" id="csvDownload">Download The Un-Used .CSV file here</a>';
	elseif ($used == '2') return '<a href="tools.php?page=export&rmcsv='.$table_name.'" id="csvDownload">Download The WHOLE DATABASE .CSV file here</a>';
}
add_action('admin_menu', 'promo_add_options_link');
function rpr_used_page() { 
	echo '<div class="wrap">
		<h2>RPR USED Promotion Codes</h2>';
	echo download_complete(true);
	global $table_name; 
	rpr_render_list_page('y', $table_name); 
	echo '</div>';
}
function rpr_unused_page() {
	echo '<div class="wrap">
		<h2>RPR UN-USED Promotion Codes</h2>';
	echo download_complete(false);
	global $table_name; 
	rpr_render_list_page('x', $table_name); 
	echo '</div>';
}
function rpr_edit_codes() {
	global $wpdb;
	global $post;
	global $post_id;
	global $table_name;
	function edit_connection($pid, $ids) {
		global $wp_query;
		global $wpdb;
		global $post;
		global $post_id;
		global $table_name;
		$promo_query = sprintf("
        	SELECT * FROM `wp_posts`
				WHERE `post_type` = 'promo'
				AND `post_status` = '%s'", mysql_real_escape_string('publish'));
				 // Perform Query
        $promo_result = mysql_query($promo_query);
		$page_query = sprintf("
        	SELECT * FROM `wp_posts`
				WHERE `post_type` = 'page'
				AND `post_status` = '%s'", mysql_real_escape_string('publish'));
				 // Perform Query
        $page_result = mysql_query($page_query);
		$post_query = sprintf("
        	SELECT * FROM `wp_posts`
				WHERE `post_type` = 'post'
				AND `post_status` = '%s'", mysql_real_escape_string('publish'));
				 // Perform Query
        $post_result = mysql_query($post_query);
        
        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
		
		echo '<form name="code_edit" method="post">';
		
        $the_data = array();
		echo '
			<p><label for="post_id">Select the Promo or Page you would like to move the codes to</label></p><br />
			<select name="post_id" size="10" style="width:200px;height:200px;padding:10px;">';
		echo '
			<optgroup label="Promos Group">';
        while($line = mysql_fetch_array($promo_result)){
            $the_data[] = $line;
			echo '<option value="'.$line['ID'].'" ';
			if ($line['ID'] == $pid) echo 'selected="selected" ';
			echo '>'.$line['post_title'].'</option>';
        }
		echo '
			</optgroup>
			<optgroup label="Pages Group">';
		 while($line = mysql_fetch_array($page_result)){
            $the_data[] = $line;
			echo '<option value="'.$line['ID'].'" ';
			if ($line['ID'] == $pid) echo 'selected="selected" ';
			echo '>'.$line['post_title'].'</option>';
        }
		echo '
			</optgroup>
			<optgroup label="Posts Group">';
		 while($line = mysql_fetch_array($post_result)){
            $the_data[] = $line;
			echo '<option value="'.$line['ID'].'" ';
			if ($line['ID'] == $pid) echo 'selected="selected" ';
			echo '>'.$line['post_title'].'</option>';
        }
		echo '
			</optgroup>';
		
		echo '</select>';
		echo '
			<input type="hidden" name="pid" value="'.$pid.'" />
			<input type="hidden" name="ids" value="'.$ids.'" />
			<input type="hidden" name="save" value="true" />
			<div class="clearfloat" style="margin:10px;"></div>
			<input type="submit" class="button-primary" value="Save Codes" />';
		echo '</form>';
	}
	function edit_value($ids) {
		global $wp_query;
		global $wpdb;
		global $post;
		global $post_id;
		global $table_name;
		
		echo '<form name="code_edit" method="post">';
		
        $the_data = array();
		echo '
			<label for="post_id">Input the value you would like to change your codes to.<br />
			to move the codes to</label><br />
			<input type="text" name="val" />';
		echo '
			<input type="hidden" name="ids" value="'.$ids.'" />
			<input type="hidden" name="save" value="true" />
			<input type="submit" class="button-primary" value="Save Codes" />';
		echo '</form>';
	}
	function edit_expire($ids) {
		global $wp_query;
		global $wpdb;
		global $post;
		global $post_id;
		global $table_name;
		
		echo '<form name="code_edit" method="post">';
		
        $the_data = array();
		
		
		  $form = '<p class="dob forms"><label class="custom-select" for="dob">New Expiration Date: </label><select name="month" class="dropdown">
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
			for ($i = date('Y')+10; $i > 1900; $i--) { $form .= '<option value="'.sprintf("%02s",$i).'">'.sprintf("%02s",$i).'</option>'; }
			$form .= '</select></p>  ';
		
		echo '<label for="post_id">Input the value you would like to change your codes expiration dates to.</label><br />'.$form;
		echo '
			<input type="hidden" name="ids" value="'.$ids.'" />
			<input type="hidden" name="save" value="true" />
			<input type="submit" class="button-primary" value="Save Codes" />';
		echo '</form>';
	}
	echo '<div class="wrap">
		<h2>RPR Promo Code Editor</h2>';
	if(isset($_GET["ids"]) && isset($_GET["pid"]) && !isset($_POST['save']) && !isset($_GET["action"])) {
		
		$ids = $_GET['ids'];
		$pid = $_GET['pid'];
		edit_connection($pid, $ids);
		
	} elseif (isset($_POST['save'])) {
		if (isset($_GET["ids"])) $ids = $_POST['ids']; $IdArray = explode(',', $ids);
		if (isset($_GET["pid"])) $pid = $_GET['pid']; 
		if (isset($_POST['post_id'])) $postid = $_POST['post_id'];
		if (isset($_POST["val"])) $val = $_POST['val'];
		if (isset($_POST["month"])) $exp = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
		foreach ($IdArray as $idset) {
			if (isset($_GET['action'])) {
				switch ($_GET['action']) {
					case 'val':
						$edit_query = "UPDATE $table_name SET value = $val WHERE id = $idset";
						break;
					case 'expire':
						$edit_query = "UPDATE $table_name SET expiration = '".$exp."' WHERE id = $idset";
						break;
					case 'p_expire':
						$edit_query = "UPDATE $table_name SET partner_expiration = '".$exp."' WHERE id = $idset";
						break;
					default:
						break;
				}
			} else {
					$edit_query = "UPDATE $table_name SET post_id = $postid WHERE id = $idset";
			}
			$edit_result = mysql_query($edit_query);
			$affected = mysql_affected_rows();
			//echo $edit_result;
		}
		if ($affected > 0) {
			$domain = get_option('siteurl'); //or home
			$wp_root_path = $domain.'/wp-admin/post.php?post='.$postid.'&action=edit';
			echo '<script type="text/javascript">' . "\n"; 
			echo 'window.location="'.$wp_root_path.'";'; 
			echo '</script>';
		} else {
			rpr_render_list_page($pid, $table_name);
		}
	} elseif (isset($_GET["action"]) ) {
		$action = $_GET["action"];
		if(isset($_GET['promocode'])) {
			$i_id = $_SERVER["REQUEST_URI"];
			$i_id = str_replace("%5B%5D", "[]", $i_id);
			parse_str($i_id);
			$count_array = count($promocode);
			echo $action;
			$ids = '';
			foreach ( $promocode as $the_id ) {
				$ids .= $the_id.',';
			}
		} elseif (isset($_GET["ids"])) {
			$ids = $_GET["ids"];
		}
		//echo $pid;
		switch ($action) {
			case 'edit':
				edit_connection('1', $ids);
				break;
			case 'val':
				edit_value($ids);
				break;
			case 'expire':
			case 'p_expire':
				edit_expire($ids);
				break;
			default:
				$message  = '<div id="message" class="error"><p><strong><div id="icon-edit" class="icon32" style="margin-top: -9px"><br></div>Nothing happening here! Maybe next time ;-)~</strong></p></div>';
				echo $message;
				break;
		}
	} else {
		$message  = '<div id="message" class="error"><p><strong><div id="icon-edit" class="icon32" style="margin-top: -9px"><br></div>Nothing happening here! Maybe next time ;-)~</strong></p></div>';
		echo $message;
	}
	echo '</div>';
}


 function promo_options_page() {

	global $promo_options;

	ob_start(); ?>
	<div class="wrap">
		<h2>RPR Redemption Portal Options</h2>
        <?php echo download_complete(2); ?>
      <div class="grid_6" style="width:33%; margin-left:2%;float:right;">
      	<h2>Steps To Success</h2>
      	<h3>Step 1.</h3>
        <?php if ($promo_options['ctype'] || $promo_options['pages'] || $promo_options['posts']) { ?>	
            <p>Click The 
                <?php 
					$s = 0;
					if ($promo_options['ctype'] && !$promo_options['pages'] && !$promo_options['posts']) { echo '&ldquo;Promos&rdquo;';} 
					elseif (!$promo_options['ctype'] && $promo_options['pages'] && !$promo_options['posts']) { echo '&ldquo;Pages&rdquo;';}
					elseif (!$promo_options['ctype'] && !$promo_options['pages'] && $promo_options['posts']) { echo '&ldquo;Posts&rdquo;';}
					elseif ($promo_options['ctype'] && $promo_options['pages'] && !$promo_options['posts']) { echo '&ldquo;Promos&rdquo; OR &ldquo;Pages&rdquo;'; $s = 1;}
					elseif ($promo_options['ctype'] && !$promo_options['pages'] && $promo_options['posts']) { echo '&ldquo;Promos&rdquo; OR &ldquo;Posts&rdquo;'; $s = 1;}
					elseif (!$promo_options['ctype'] && $promo_options['pages'] && $promo_options['posts']) { echo '&ldquo;Pages&rdquo; OR &ldquo;Posts&rdquo;'; $s = 1;}
					elseif ($promo_options['ctype'] && $promo_options['pages'] && $promo_options['posts']) { echo '&ldquo;Promos&rdquo;, &ldquo;Pages&rdquo; OR &ldquo;Posts&rdquo;'; $s = 1;} ?>
                 Tab<?php
				 	if ($s) { echo 's'; } ?>
                 on the left</p>
            <h3>Step 2.</h3>
            <p>Click &ldquo;Add New&rdquo;</p>
            <h3>Step 3.</h3>
            <p>Add a Title for the Promo &amp; all the content down the right sidebar</p>
            <h3>Step 4.</h3>
            <p>Click the &ldquo;Add Media&rdquo; button ubuve the main content and upload your .csv file</p>
            <h3>Step 5.</h3>
            <p>Copy the url show when the file is finished uploading and then close the lightbox.</p>
            <h3>Step 6.</h3>
            <p>Paste the url into the &ldquo;.CSV file url&rdquo; meta box</p>
            <h3>Step 7.</h3>
            <p>Click Save/Update <br />
                <small>(now your file has been saved but we still need to add it to the database)</small></p>
            <h3>Step 8.</h3>
            <p>You can add more codes at any time by repeating steps 4&mdash;7 after your initial import.<br />
                <small>(Deleting codes is not allowed, a work around is to create a new page post or promo, allocating codes you would like to remove to it and then setting it to private or draft. This will keep the codes in the system so if you have a complaint that a customers code did not work you can allocate a code from the removed codes post back to a specific page post or promo and re issue a code to that customer.)</small></p>
		<div id="message" class="error" <?php if ($promo_options['ht_hide'] == 'true') { echo 'style="display:none;"'; }?> >
			  <div id="hider" style="float:right; padding:10px;"><a href="javascript:jQuery('#message').slideUp()" class="hide_now">Hide this time</a> | <a href="javascript:jQuery('.ht_hide').attr('value','true'), jQuery('#message').slideUp()">Hide for good!</a></div>
            	<h2>Deny external access to csv files uploaded to your site</h2>
                <p>Please add the following to your .htaccess file:</p>
                <code>
                	&lt;FilesMatch &ldquo;\.(csv)$&rdquo;&gt;<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Order Allow,Deny<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#Remove the COMMENT from "Allow" line,<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#Add a COMMENT in fron of "Deny" line<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#and ad your IP address if you would <br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#like to be able to download .csv files <br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#from your machine<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#Allow from 00.000.00.00<br />
				  	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Allow from your.servers.ip.address<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deny from all<br />
                    &lt;/FilesMatch&gt;<br />
                </code>    
            </div>
		<?php if ($promo_options['ht_hide'] == 'false') { ?>
		<?php } else { ?>
			<div class="update-nag">
			  <div id="hider" style="padding:10px;"><a href="javascript:jQuery('.ht_hide').attr('value','false'), jQuery('#message').slideDown()">SHOW .htaccess edits I need to make!</a></div>
            </div>
		<?php } ?>
        <?php } ?>
      </div>
      <!-- end .grid_6 -->
      <div class="grid_10" style="width:65%;float:left;">
      	
        <?php ob_start(); ?>
		<div class="wrap">
            
            
            <form method="post" action="options.php">
            
                <?php settings_fields('promo_settings_group'); ?>
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="postbox-container" class="postbox-container" style="width:48%;">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div id="promo-metabox" class="postbox ">
                                <h3 style="cursor:default;"><span>Response Promotion Redeemer Options</span></h3>
                                <div class="inside">	
                                    <h4>
                                        <input type="hidden" name="promo_settings[ctype]" value="0" />
                                        <input id="ctype_check" style="margin-right:20px;" name="promo_settings[ctype]" type="checkbox" value="1" <?php checked($promo_options['ctype'], 1); ?> /><?php _e('Add &ldquo;Promos&rdquo; custom post type', 'promo_domain'); ?></h4>
                                    <p>
                                        <label class="description" for="promo_settings[enable_c]"><?php _e('By checking the box above you are enabling &ldquo;Promo&rdquo; custom post type.', 'promo_domain'); ?></label>
                                    </p>
                                    <hr />	
                                    <h4>
                                        <input type="hidden" name="promo_settings[pages]" value="0" />
                                        <input id="pages_check" style="margin-right:20px;" name="promo_settings[pages]" type="checkbox" value="1" <?php checked($promo_options['pages'], 1); ?> /><?php _e('Show promotion options to pages', 'promo_domain'); ?></h4>
                                    <p>
                                        <label class="description" for="promo_settings[pages]"><?php _e('By checking the box above you are enabling the the promo options on your pages.', 'promo_domain'); ?></label>
                                    </p>
                                    <hr />	
                                    <h4>
                                        <input type="hidden" name="promo_settings[posts]" value="0" />
                                        <input id="posts_check" style="margin-right:20px;" name="promo_settings[posts]" type="checkbox" value="1" <?php checked($promo_options['posts'], 1); ?> /><?php _e('Show promotion options to posts', 'promo_domain'); ?></h4>
                                    <p>
                                        <label class="description" for="promo_settings[posts]"><?php _e('By checking the box above you are enabling the the promo options on your posts.', 'promo_domain'); ?></label>
                                    </p>
                                    <hr />	
                                    <h4><?php _e('Enter an age restriction here', 'promo_domain'); ?></h4>
                                    <p>
                                       <input id="promo_settings[age]" style="width:98%;" name="promo_settings[age]" type="text" value="<?php if ($promo_options['age']) { echo $promo_options['age']; } else { echo 'any'; } ?>"/>
                                       <small><label class="description" for="promo_settings[age]"><?php _e('If you have an age restriction for your promotions set it here. if it is any age specify any (defalt will be any)', 'promo_domain'); ?></label></small>
                                    </p>
                                    <p>
                                       <input id="promo_settings[wait]" style="width:98%;" name="promo_settings[wait]" type="text" value="<?php if ($promo_options['wait']) { if ($promo_options['wait'] != "") { echo $promo_options['wait']; } else { echo '0'; } } else { echo '0'; } ?>"/>
                                       <small><label class="description" for="promo_settings[wait]"><?php _e('If you would like to add a delay between users entering multiple code enter it below in hours (i.e. "24")', 'promo_domain'); ?></label></small>
                                    </p>
                                    <p class="submit">
									  		<input id="promo_settings[ht_hide]" class="ht_hide" style="width:98%;" name="promo_settings[ht_hide]" type="hidden" value="<?php if ($promo_options['ht_hide']) { echo $promo_options['ht_hide']; } else { echo 'false'; } ?>" /> 
                                            <input type="submit" class="button-primary" value="<?php _e('Save Options', 'promo_domain'); ?>" />
                                        </p>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div id="postbox-container" class="postbox-container" style="width: 48%;margin-left: 4%;">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div id="promo-metabox" class="postbox ">
                                <h3 style="cursor:default;"><span>Response Promotion Redeemer CODE Mail Handling</span></h3>
                                <div class="inside">	
                                    
                                    <h4>
                                        <input type="hidden" name="promo_settings[enable_c]" value="0" />
                                        <input id="cp_check" style="margin-right:20px;" name="promo_settings[enable_c]" type="checkbox" value="1" <?php checked($promo_options['enable_c'], 1); ?> /><?php _e('Enable Campaign Monitor Integration', 'promo_domain'); ?></h4>
                                    <p>
                                        <label class="description" for="promo_settings[enable_c]"><?php _e('By checking the box above you are enabling the option to process your forms through campaign monitor.', 'promo_domain'); ?></label>
                                    </p>
                                    <hr />
                                    <div id="cp_monitor" style="display:none;">
                                        <h4><?php  _e('Campaign Monitor Information', 'promo_domain'); ?></h4>
                                        <p>
                                            <label class="description" for="promo_settings[api_k]"><?php _e('Enter Campaign Monitor API Key here.', 'promo_domain'); ?></label>
                                            <input id="promo_settings[api_k]" style="width:98%;" name="promo_settings[api_k]" type="text" value="<?php echo $promo_options['api_k']; ?>"/>
                                        </p>
                                        <p>
                                            <label class="description" for="promo_settings[l_id]"><?php _e('Enter Campaign Monitor List ID here.', 'promo_domain'); ?></label>
                                            <input id="promo_settings[l_id]" style="width:98%;" name="promo_settings[l_id]" type="text" value="<?php echo $promo_options['l_id']; ?>"/>
                                        </p>
                                        
                                   </div>
                                   <p class="submit">
                                            <input type="submit" class="button-primary" value="<?php _e('Save Options', 'promo_domain'); ?>" />
                                        </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    jQuery('#cp_check').click(function() {
                       if(jQuery('#cp_check').is(':checked')) { 
                        jQuery('#cp_monitor').slideDown();
                       } else { jQuery('#cp_monitor').slideUp(); }
                    });
                    if(jQuery('#cp_check').is(':checked')) { 
                        jQuery('#cp_monitor').slideDown();
                    }
                </script>
            </form>
            
        </div>
        <?php echo ob_get_clean(); ?>
      </div>
      <!-- end .grid_10 -->
      <div class="clear"></div>
	</div>
	<?php
	echo ob_get_clean();
}

function promo_register_settings() {
	// creates our settings in the options table
	register_setting('promo_settings_group', 'promo_settings');
}
add_action('admin_init', 'promo_register_settings'); 
