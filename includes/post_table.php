<?php
function render_post_table(){
		if (isset($_GET["sqlerror"]) && $_GET["sqlerror"] == 'true' && !isset($_GET["rmfile"])) {
				$message  = '<div id="message" class="error"><p><strong><a href="'.curPageURL("&rmfile=true").'"><div id="icon-edit" class="icon32" style="margin-top: -9px"><br></div>Click here to remove the file from this post so you can upload a new one</a></strong></p></div>';
				echo $message;
		} 
		global $wpdb;
		global $post;
		global $post_id;
		global $table_name;
		$event_query = sprintf("SELECT * FROM $table_name WHERE `post_id` = %s", mysql_real_escape_string($post_id));
		
		$down_load = '<table width="100%" cellpadding="5" id="data_link">
						<tr bgcolor="#99CCFF">
							<td align="center">
								<a href="javascript:get_ids()" id="csvDownload">Edit Checked Attachment</a>
							</td>
							<td align="center">
								<a href="javascript:get_act_ex()" id="csvDownload">Edit Our Code Exp.</a>
							</td>
							<td align="center">
								<a href="javascript:get_act_p_ex()" id="csvDownload">Edit Partner Code Exp.</a>
							</td>
							<td align="center">
								<a href="javascript:get_ids_val()" id="csvDownload">Edit Checked Value</a>
							</td>
							<td align="center">
								<a href="tools.php?page=export&rmcsv='.$table_name.'&post='.$post_id.'" id="csvDownload">Download The complete .CSV file here</a>
							</td>
						</tr>
					</table>';
		echo $down_load;
		$thead = '<table width="100%" id="data_table">';
		$thead .= '<tr>
						<th align="center" class="no-sort"><input type="checkbox" id="main_check" /></th>
						<th>Activation Date</th>
						<th>Our Exp. &amp; Code</th>
						<th>Partner Exp. &amp; Code</th>
						<th>Value</th>
						<th>Location/Event</th>
						<th>Product ID</th>
						<th>User Name</th>
						<th>User Email</th>
						<th>DOB</th>
					</tr>';
		echo $thead;
		// Perform Query
			$event_result = mysql_query($event_query);
			
			// Check result
			// This shows the actual query sent to MySQL, and the error. Useful for debugging.
			 
			if (!$event_result) {
				$message  = '<div id="message" class="updated fade"><p><strong> You don&rsquo;t have any codes yet, why dont you upload some</strong></p></div>';
				echo $message;
			} else {
			
				// Use result
				// Attempting to print $result won't allow access to information in the resource
				// One of the mysql result functions must be used
				// See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
				function convert_date($the_date) {
					$source = $the_date;
					$date = new DateTime($source);
					return $date->format('D M j, Y'); // 31.07.2012
				}
				while ($event_row = mysql_fetch_assoc($event_result)) {
					$our_code = en_de('decode', $event_row['our_code'], ED_KEY);
					$partner_code = en_de('decode', $event_row['partner_code'], ED_KEY);
					if ($event_row['user_dob'] != 'xxxx') {
						$user_dob = convert_date($event_row['user_dob']);
					} else { $user_dob = $event_row['user_dob']; }
					echo '<tr style="border-bottom:1px solid #ccc;">';
						echo '<td align="center" id="checker"><input type="checkbox" id="sub_check" value="'.$event_row['id'].'" /></td>';
						echo '<td>'.convert_date($event_row['activation']).'</td>';
						echo '<td>'.convert_date($event_row['expiration']).' - '.$our_code.'</td>';
						echo '<td>'.convert_date($event_row['partner_expiration']).' - '.$partner_code.'</td>';
						echo '<td>'.$event_row['value'].'</td>';
						echo '<td>'.$event_row['event_location'].'</td>';
						echo '<td>'.$event_row['p_o_id'].'</td>';
					if ($event_row['user_name'] != 'Not Used') {
						  echo '<td align="center" bgcolor="#99CCFF"><strong>'.$event_row['user_name'].'</strong></td>';
						  echo '<td><strong>'.$event_row['user_email'].'</strong></td>';
						  echo '<td><strong>'.$user_dob.'</strong></td>';
					  } else {
						  echo '<td align="center">'.$event_row['user_name'].'</td>';
						  echo '<td>'.$event_row['user_email'].'</td>';
						  echo '<td>'.$event_row['user_dob'].'</td>';
					  }
					echo '</tr>';
				}
			}
		
		echo '</table>';
		echo '<script src="'.plugin_dir_url( __FILE__ ) . 'tablesort.js"></script>';
		echo "<script>
				new Tablesort(document.getElementById('data_table'));
				var checked_off = document.getElementById('main_check');
				jQuery(checked_off).click(function() {
					//alert('true');
					if (jQuery(checked_off).is(':checked')) {
							jQuery('input#sub_check').attr('checked', true);
						} else if (!jQuery(checked_off).is(':checked')) {
							jQuery('input#sub_check').attr('checked', false);
						}
					
				});
				function get_ids() {
						 var items = jQuery('input#sub_check').map(function(){
						  if (jQuery(this).is(':checked')) {
							  return jQuery(this).val();
						  }
						}).get().join(','); 
						if (items != '') {
						window.location = 'admin.php?page=edit-codes&pid=".$post_id."&ids='+items;
						}
						
					}
				function get_ids_val() {
						 var items = jQuery('input#sub_check').map(function(){
						  if (jQuery(this).is(':checked')) {
							  return jQuery(this).val();
						  }
						}).get().join(','); 
						if (items != '') {
						window.location = 'admin.php?page=edit-codes&action=val&pid=".$post_id."&ids='+items;
						}
						
					}
				function get_act_ex() {
						 var items = jQuery('input#sub_check').map(function(){
						  if (jQuery(this).is(':checked')) {
							  return jQuery(this).val();
						  }
						}).get().join(','); 
						if (items != '') {
						window.location = 'admin.php?page=edit-codes&action=expire&pid=".$post_id."&ids='+items;
						}
						
					}
				function get_act_p_ex() {
						 var items = jQuery('input#sub_check').map(function(){
						  if (jQuery(this).is(':checked')) {
							  return jQuery(this).val();
						  }
						}).get().join(','); 
						if (items != '') {
						window.location = 'admin.php?page=edit-codes&action=p_expire&pid=".$post_id."&ids='+items;
						}
						
					}
			</script>";
		echo '<style>
		table#data_table {
		  background:#fff;
		  max-width:100%;
		  border-spacing:0;
		  width:100%;
		  margin:10px 0;
		  border:1px solid #ddd;
		  border-collapse:separate;
		  *border-collapse:collapsed;
		  -webkit-box-shadow:0 0 4px rgba(0,0,0,0.10);
			 -moz-box-shadow:0 0 4px rgba(0,0,0,0.10);
				  box-shadow:0 0 4px rgba(0,0,0,0.10);
		  }
		  table#data_table th,
		  table#data_table td {
			padding:8px;
			line-height:18px;
			text-align:left;
			border-top:1px solid #ddd;
			}
		  table#data_table th {
			background:#eee;
			background:-webkit-gradient(linear, left top, left bottom, from(#f6f6f6), to(#eee));
			background:-moz-linear-gradient(top, #f6f6f6, #eee);
			text-shadow:0 1px 0 #fff;
			font-weight:bold;
			vertical-align:bottom;
			}
		  table#data_table td {
			vertical-align:top;
			}
		  table#data_table thead:first-child tr th,
		  table#data_table thead:first-child tr td {
			border-top:0;
			}
		  table#data_table tbody + tbody {
			border-top:2px solid #ddd;
			}
		  table#data_table th + th,
		  table#data_table td + td,
		  table#data_table th + td,
		  table#data_table td + th {
			border-left:1px solid #ddd;
			}
		  table#data_table thead:first-child tr:first-child th,
		  table#data_table tbody:first-child tr:first-child th,
		  table#data_table tbody:first-child tr:first-child td {
			border-top:0;
			}
			th.sort-header {
			  cursor:pointer;
			  }
			th.sort-header::-moz-selection,
			th.sort-header::selection {
			  background:transparent;
			  }
			table th.sort-header:after {
			  content:"";
			  float:right;
			  margin-top:7px;
			  border-width:0 4px 4px;
			  border-style:solid;
			  border-color:#404040 transparent;
			  visibility:hidden;
			  }
			table th.sort-header:hover:after {
			  visibility:visible;
			  }
			table th.sort-up:after,
			table th.sort-down:after,
			table th.sort-down:hover:after {
			  visibility:visible;
			  opacity:0.4;
			  }
			table th.sort-up:after {
			  border-bottom:none;
			  border-width:4px 4px 0;
			  }
		</style>'; 
   
}
