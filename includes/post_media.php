<?php 
//file upload
function post_media() {
		global $post;
		global $wpdb;
		global $post_id;
		global $table_name;
			
		if (isset($_GET["sqlerror"]) && $_GET["sqlerror"] == 'true' && isset($_GET["rmfile"]) && $_GET["rmfile"] = 'true') {
				delete_post_meta($post->ID, 'post_media');
		}
 			wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    		
			$dirNAME = plugin_dir_url( __FILE__ );
			$thefile = get_post_meta($post->ID, 'post_media', true);
			$redURL = get_post_meta( $post->ID, 'redURL', true );
			$ptype = get_post_meta( $post->ID, 'ptype', true );
			
				if ($thefile) {
					$myfileurl = $thefile;
					$fileName = basename($myfileurl);
					echo '<a href="tools.php?page=export&rmcsv='.$table_name.'&post='.$post_id.'" target="_blank" ><img src="'. $dirNAME . 'images/Excel2007Logo.gif" height="40px" style="float:left; margin-right:10px;"></a><div style="font-size:15px; font-weight:bold;padding:10px 0px;">'.$table_name.'.csv</div><div style="clear:both;"></div>';
					echo "<p>Click the <strong style='color:#217F36;'>Excel</strong> icon to download the most current data.</p>";
					
					echo '<p><strong>Current File URL is: <br></strong> <a href="'.$thefile.'">'.$thefile.'</a></p>';
					
					$data_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE post_id = %d", $post_id ) );
					$data_unused = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE user_name = 'Not Used' AND  post_id = %d", $post_id ) );
					$data_used  = $data_count - $data_unused;
					if ($data_count < 1) {
						echo "<p>records found: <strong>{$data_count}</strong></p>";
						echo "<p>click &ldquo;<strong style='color:#298CBA;'>Update</strong>&rdquo; below to populate the promotion codes.</p>";
						echo '<input id="post_media" style="width:95%;" name="post_media" value="'.$thefile.'" />';
					} else {
						echo "<p>records found: <strong>{$data_count}</strong></p>
							<p>records used: <strong>{$data_used}</strong></p>
							<p>records available: <strong>{$data_unused}</strong></p>";
						echo "<p>OR upload another file and add paste the url below to add more codes.</p>";
						echo '<input id="post_media" style="width:95%;" name="post_media" value="" />';
						echo '<p><small>If you are experiencing errors you probably have duplicate codes in the document you are trying to upload or there are codes within the database that match codes in the file you are tring to upload.</small></p>
							<p><small>If you get to a screen with errors on it hit the back button, check to see if the &ldquo;Records Found&rdquo; number has increased if it has you have uploaded more codes minus the duplicates, it is up to you to review your codes and find the ones that are repeatative.</small></p>';
					}
					
				} else {
					$view = '<label for="post_media"><strong>.CSV file url:</strong><br /><input id="post_media" style="width:95%;" name="post_media" value="';
					if( $thefile ) { $view .= $thefile; }
					$view .= '" /></label>';
					echo $view;
					echo 'Please upload a .CSV file with the add media button and thecopy the file url and past it here. Not sure what the .csv should look like... <a href="' . $dirNAME . '/includes/Template.csv" target="_blank" >Download a template here </a>';
				}
			 
} // end post_media
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {

	// Add file extension 'extension' with mime type 'mime/type'
	$existing_mimes['csv'] = 'text/csv';

	return $existing_mimes;
}
