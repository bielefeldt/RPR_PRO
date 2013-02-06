<?php 
	/******************************
	* export data as csv
	******************************/
	define('RM_PLUGIN_SLUG', 'response-promo-redeemer');
	define('RM_PLUGIN_DIR', WP_PLUGIN_URL.'/'.RM_PLUGIN_SLUG.'/');	
    $dirNAME = plugin_dir_url( __FILE__ );
	define('RM_SITE', 'thepowertoprovoke.com');
	if(!function_exists('fn_rm_csv_export')){
		function fn_rm_csv_export(){        
			//ob_clean();
			
			function fn_rm_csv_gen($table_name){
				global $wpdb;
				$req_table = isset($_REQUEST['rmcsv']) ? $_REQUEST['rmcsv'] : '';
				$post_id = isset($_REQUEST['post']) ? $_REQUEST['post'] : '';
				$used = isset($_REQUEST['used']) ? $_REQUEST['used'] : '';
				
				if($req_table){
			
					$field='';
					$getfield ='';
					//echo $post_id;
					if ($post_id && !isset($_REQUEST['used'])) {
						$result = $wpdb->get_results("SELECT * FROM `$req_table` WHERE `post_id` = $post_id");      
						$r1 = mysql_query("SELECT * FROM `$req_table` WHERE `post_id` = $post_id");
					} elseif (!$post_id && !isset($_REQUEST['used'])) {
						$result = $wpdb->get_results("SELECT * FROM `$req_table`");      
						$r1 = mysql_query("SELECT * FROM `$req_table`");
					} elseif ($post_id && isset($_REQUEST['used'])) {
						if ($used == '1') {
							$result = $wpdb->get_results("SELECT * FROM `$req_table` WHERE `post_id` = $post_id AND `user_email` LIKE '%@%'");      
							$r1 = mysql_query("SELECT * FROM `$req_table` WHERE `post_id` = $post_id AND `user_email` LIKE '%@%'");
						} elseif ($used == '0') {
							$result = $wpdb->get_results("SELECT * FROM `$req_table` WHERE `post_id` = $post_id AND `user_name` = 'Not Used'");      
							$r1 = mysql_query("SELECT * FROM `$req_table` WHERE `post_id` = $post_id AND `user_name` = 'Not Used'");
						}
						
					} elseif (!$post_id && isset($_REQUEST['used'])) {
						if ($used == '1') {
							$result = $wpdb->get_results("SELECT * FROM `$req_table` WHERE `user_email` LIKE '%@%'");      
							$r1 = mysql_query("SELECT * FROM `$req_table` WHERE `user_email` LIKE '%@%'");
						} elseif ($used == '0') {
							$result = $wpdb->get_results("SELECT * FROM `$req_table` WHERE `user_name` = 'Not Used'");      
							$r1 = mysql_query("SELECT * FROM `$req_table` WHERE `user_name` = 'Not Used'");
						}
					}
					$fields_num = mysql_num_fields($r1);
					
					for($i=0; $i<$fields_num; $i++){
						$field = mysql_fetch_field($r1);
						$field = (object) $field;         
						$getfield .= $field -> name.',';
					}
			
					$sub = substr_replace($getfield, '', -1);
					$fields = $sub; # GET FIELDS NAME
					$each_field = explode(',', $sub);
					
					$csv_file_name = $req_table.'_'.date('Ymd_His').".csv"; # CSV FILE NAME WILL BE table_name_yyyymmdd_hhmmss.csv
					
					# GET FIELDS VALUES WITH LAST COMMA EXCLUDED
					foreach($result as $row){
						for($s = 0; $s < $fields_num; $s++){
							if($s == 0) $fields .= "\n"; # FORCE NEW LINE IF LOOP COMPLETE
							$ar = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row->$each_field[$s]); # REPLACE NEW LINE WITH TAB
							$ar = str_getcsv ( $ar , ",", "\"" , "\\"); # SEQUENCING DATA IN CSV FORMAT
							if($s == 3) {
								$our_code = en_de('decode', $ar[0], ED_KEY);
								$fields .= $our_code.',';
							} elseif($s == 4) {
								$partner_code = en_de('decode', $ar[0], ED_KEY);
								$fields .= $partner_code.',';
							}  elseif($s != 3 && $s != 4) {
								$fields .= $ar[0].',';
							}
						}			
						$fields = substr_replace($fields, '', -1); # REMOVE EXTRA SPACE AT STRING END
					}
					
					header("Content-type: text/x-csv"); # DECLARING FILE TYPE
					header("Content-Transfer-Encoding: binary");
					header("Content-Disposition: attachment; filename=".$csv_file_name); # EXPORT GENERATED CSV FILE
					header("Pragma: no-cache");
					header("Expires: 0");
			
					echo $fields;
			 	}
			}
			$req_table = isset($_REQUEST['rmcsv']) ? $_REQUEST['rmcsv'] : '';
			if ($req_table){          
				echo fn_rm_csv_gen($req_table);
				exit;
			}
		}
	}
	add_action('init', 'fn_rm_csv_export');
