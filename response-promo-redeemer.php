<?php
/*
Plugin Name: Response Promo Redeemer PRO(V1)
Plugin URI: http://redeemer.thepowertoprovoke.com
Description: This plugin was developed to streamline the Promotion Code Redemption process for businesses. It will integrate with and external database for cross-domain inclusion of partner promotions, create a querystring redirect to partner sites or generate an email to the person redeeming a promotion code with instructions for the partner site. It allows you to re-skin each page with the apropriate artwork for an individualized partner relationship.
Version: 1.1.0
Author: Bryan Bielefeldt at Response Marketing in New Haven CT
Author URI: http://thepowertoprovoke.com
License: http://www.gnu.org/licenses/gpl-2.0.html


/******************************
* global variables
******************************/

$promo_prefix = 'promo_';
$promo_plugin_name = 'Response Promotion Redeemer';



global $wpdb;
global $post;
global $post_id;
global $rpr_db_version;
global $table_name;
global $promo_options;

// options globals
global $ctype, $pages, $posts, $enable_c, $api_k, $l_id;

//define enqription key
define("ED_KEY",  $_SERVER['SERVER_NAME']);

$rpr_db_version = "1.0";
$promo_plugin_name = 'Response Promotion Redeemer Pro';

// table names

$rpr_db_version = "1.0";

$table_name = $wpdb->prefix . "rpr_codes";

// retrieve our plugin settings from the options table
$promo_options = get_option('promo_settings');

$ctype = $promo_options['ctype'];
$pages = $promo_options['pages'];
$posts = $promo_options['posts'];
$enable_c = $promo_options['enable_c'];
if ($enable_c) {
	$api_k = $promo_options['api_k'];
	$l_id = $promo_options['l_id'];
}

/******************************
* includes
******************************/
include('includes/admin-page.php'); // the plugin options page HTML and save functions
include('includes/url_promo_metabox.php');
include('includes/css_js_metabox.php');
include('includes/post_media.php');
include('includes/short_code_opt.php');
include('includes/front_end_scripts.php'); // this controls all JS / CSS
include('includes/post_table.php');
include('includes/csv-export.php'); // csv export function
if ($ctype) {
	include('includes/promo-ctype.php'); // add cutome content type promo
}
include('includes/form-shortcodes.php'); // add form and shorcode selector to wysiwyg for post tye pormo
include('includes/en_de.php'); // encryption/decryption function
	/******************************
	* install db
	******************************/
	
	// run the install scripts upon plugin activation
	class rpr_promo_table {
		static function rpr_install() {
		   global $wpdb;
		   global $rpr_db_version;
		   global $table_name;
		   
		   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) { 
				$sql = "CREATE TABLE `$table_name` (
				  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
				  `post_id` int(11) NOT NULL,
				  `time_entered` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				  `our_code` VARCHAR(55) NOT NULL,
				  `partner_code` VARCHAR(55) NOT NULL,
				  `value` VARCHAR(20) NOT NULL,
				  `activation` date NOT NULL,
				  `expiration` date NOT NULL,
				  `partner_expiration` date NOT NULL,
				  `event_location` varchar(100) NOT NULL DEFAULT 'xxxx',
				  `p_o_id` varchar(100) NOT NULL DEFAULT 'xxxx',
			  	  `p_o_detail` varchar(100) NOT NULL DEFAULT 'xxxx',
				  `user_email` VARCHAR(55) DEFAULT 'xxxx',
				  `user_name` VARCHAR(55) DEFAULT 'Not Used',
				  `user_dob` varchar(20) NOT NULL DEFAULT 'xxxx',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `our_code` (`our_code`,`partner_code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			 
				add_option("rpr_db_version", $rpr_db_version);
		   }
		}
		
		 static function rpr_install_post($post_ID) {
			 
		   global $wpdb;
		   global $post;
		   global $rpr_db_version;
		   global $table_name;
		   global $post_id;
			$data_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE post_id = %d", $post_id ) );
		   $oldfile = get_post_meta($post->ID, 'post_media', true);
		   //echo $oldfile;
		   $newfile = '';
		   if (isset($_POST['post_media'])) {
			   $newfile = $_POST['post_media'];
			   //echo $newfile;
		   }
		   function fileExists($path){
				return (@fopen($path,"r")==true);
			}
		   //echo $newfile;
		   //echo $oldfile; 
		   if (fileExists($newfile) || $data_count < 1 && fileExists($newfile)) {
			   //print_r($_POST);
			   $myfileurl = $newfile;
			   function to_date($date) {
					$newdate = substr($date,6,2) . "-" . substr($date,0,2) . "-" . substr($date,3,2);
					return $newdate;
				}
			   function dynam_query($item, $input) {
					global $table_name;
					$dupesql = "SELECT 
									$item 
								FROM 
									$table_name 
								WHERE $item = '$input'";
				
					$duperaw = mysql_query($dupesql);
				
					if( mysql_num_rows($duperaw) ) {
						$message  = '
								<script>
									function goBack()
									  {
									  var back = document.referrer+"&sqlerror=true";
									  window.location = back;
									  }
							  </script>
								<strong style="display:block;text-align:center;"> You are trying to add duplicate codes to your site. I wont allow it!<br />
									<a href="javascript:goBack()"><< Click here to go back >></a>
								</strong>';
						wp_die($message);
					} 
					else {
						return;
					}
				}
			   if ($wpdb->get_var("SHOW TABLES LIKE '$table_name';") == $table_name && $myfileurl == true) {
					//echo 'in';
					$ptype = get_post_meta( $post->ID, 'ptype', true );
					
					
					$fileName = basename($myfileurl);
					
					$row = 1;
					if (($handle = fopen($myfileurl, "r")) !== FALSE) {
						//echo 'in 2';
						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							//echo 'in 3';
							$num = count($data);
							$our_code;
							$partner_code;
							$value;
							$activation;
							$expiration;
							$partner_expiration;
							//echo $row;
							if($row > 1) {
								//echo "<p> $num fields in line $row: <br /></p>\n";
								for ($c=0; $c < $num; $c++) {
									//echo $data[$c] . "_".$c."<br />\n";
									if ($c == 0) { $our_code = $data[$c]; dynam_query('our_code', $our_code);}
									else if ($c == 1) { $partner_code = $data[$c]; dynam_query('partner_code', $partner_code);}
									else if ($c == 2) { $value = $data[$c]; }
									else if ($c == 3) { $activation = to_date($data[$c]); }
									else if ($c == 4) { $expiration = to_date($data[$c]); }
									else if ($c == 5) { $partner_expiration = to_date($data[$c]); }
								}
								
							   global $wpdb;
							   global $table_name;	
							   
							   $our_code = en_de('encode', $our_code, ED_KEY);
							   $partner_code = en_de('encode', $partner_code, ED_KEY);
									
							   $rows_affected = $wpdb->insert( $table_name, array('post_id' => $post_id, 'time_entered' => current_time('mysql'), 'our_code' => $our_code, 'partner_code' => $partner_code, 'value' => $value, 'activation' => $activation, 'expiration' => $expiration, 'partner_expiration' => $partner_expiration) );
							}
							$row++;
						}
						fclose($handle);
					}
						
			   }
		   }
		   
		}
	}
	
	register_activation_hook(__FILE__, array('rpr_promo_table', 'rpr_install'));
	add_action( 'edit_post', array('rpr_promo_table', 'rpr_install_post') );
	//custom meta box
	 
	// Hook into WordPress
	add_action( 'admin_init', 'add_promo_metabox' );
	add_action( 'save_post', 'save_promo_url' );
	/******************************
	* add costom meta boxes
	******************************/
	function add_promo_metabox() {
		global $ctype, $pages, $posts, $enable_c, $api_k, $l_id;
		if ($ctype) {
			add_meta_box( 'promo-metabox', __( 'Partner Information' ), 'url_promo_metabox', 'promo', 'side', 'high' );
			add_meta_box( 'post_media', __( 'Add Promo.csv File' ), 'post_media', 'promo', 'side', 'high' );
			add_meta_box( 'short_code_opt', __( 'Form shordcodes' ), 'short_code_opt', 'promo', 'side', 'high' );
			add_meta_box( 'added-css', __( 'Added Page CSS and Javascript' ), 'css_js_metabox', 'promo', 'normal', 'high' );
			add_meta_box( 'list_the_codes', __( 'Promo Codes and Related Users' ), 'list_the_codes', 'promo', 'normal', 'high' );
		}
		if ($pages) {
			add_meta_box( 'promo-metabox', __( 'Partner Information' ), 'url_promo_metabox', 'page', 'side', 'high' );
			add_meta_box( 'post_media', __( 'Add Promo.csv File' ), 'post_media', 'page', 'side', 'high' );
			add_meta_box( 'short_code_opt', __( 'Form shordcodes' ), 'short_code_opt', 'page', 'side', 'high' );
			add_meta_box( 'added-css', __( 'Added Page CSS and Javascript' ), 'css_js_metabox', 'page', 'normal', 'high' );
			add_meta_box( 'list_the_codes', __( 'Promo Codes and Related Users' ), 'list_the_codes', 'page', 'normal', 'high' );
		}
		if ($posts) {
			add_meta_box( 'promo-metabox', __( 'Partner Information' ), 'url_promo_metabox', 'post', 'side', 'high' );
			add_meta_box( 'post_media', __( 'Add Promo.csv File' ), 'post_media', 'post', 'side', 'high' );
			add_meta_box( 'short_code_opt', __( 'Form shordcodes' ), 'short_code_opt', 'post', 'side', 'high' );
			add_meta_box( 'added-css', __( 'Added Page CSS and Javascript' ), 'css_js_metabox', 'post', 'normal', 'high' );
			add_meta_box( 'list_the_codes', __( 'Promo Codes and Related Users' ), 'list_the_codes', 'page', 'normal', 'high' );
		}

	}
function list_the_codes() {
		
	global $wpdb;
 	global $post;
 	global $table_name; 
 	global $post_id;
  	render_post_table($post_id); 
}
function save_promo_url( $post_id ) {
		global $post;  
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
		if( $_POST ) {
			update_post_meta( $post->ID, 'v_location', $_POST['v_location'] );
			update_post_meta( $post->ID, 'field_location', $_POST['field_location'] );
			update_post_meta( $post->ID, 'v_product', $_POST['v_product'] );
			update_post_meta( $post->ID, 'field_product', $_POST['field_product'] );
			update_post_meta( $post->ID, 'event', $_POST['event'] );
			update_post_meta( $post->ID, 'product', $_POST['product'] );
			update_post_meta( $post->ID, 'urllink', $_POST['siteurl'] );
			update_post_meta( $post->ID, 'from', $_POST['from'] );
			update_post_meta( $post->ID, 'ptype', $_POST['new_partner_type'] );
			update_post_meta( $post->ID, 'redURL', $_POST['redURL'] );
			update_post_meta( $post->ID, 'conURL', $_POST['conURL'] );
			update_post_meta( $post->ID, 'conNAME', $_POST['conNAME'] );
			update_post_meta( $post->ID, 'conTNAME', $_POST['conTNAME'] );
			update_post_meta( $post->ID, 'conUSER', $_POST['conUSER'] );
			update_post_meta( $post->ID, 'conPASSWORD', $_POST['conPASSWORD'] );
			update_post_meta( $post->ID, 'conQSTRING', $_POST['conQSTRING'] );
			update_post_meta( $post->ID, 'queV1', $_POST['queV1'] );
			update_post_meta( $post->ID, 'queV2', $_POST['queV2'] );
			update_post_meta( $post->ID, 'queV3', $_POST['queV3'] );
			update_post_meta( $post->ID, 'added-css', $_POST['css'] );
			update_post_meta( $post->ID, 'added-js', $_POST['js'] );
			if (isset($_POST['post_media']) && $_POST['post_media'] !== get_post_meta($post->ID, 'post_media', true) && @fopen($_POST['post_media'],"r")==true) {
			update_post_meta( $post->ID, 'post_media', $_POST['post_media'] );
			}
			
			add_action('save_post', 'register_admin_scripts');
		}
	}
function register_admin_scripts() {
	 	wp_enqueue_style('rpr-styles', plugin_dir_url( __FILE__ ) . 'includes/css/plugin_styles.css');
		wp_register_script('promo_admin_script', plugin_dir_url( __FILE__ ) . 'includes/js/admin.js');
		wp_enqueue_script('promo_admin_script');
	} // end register_scripts
	add_action('admin_enqueue_scripts', 'register_admin_scripts');
	 
	/**
	 * Get and return the values for the URL and description
	 */
	function get_url_desc_box() {
		global $post;
		$v_location = get_post_meta( $post->ID, 'v_location', true );
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
		return array( $urllink, $ptype, $from, $redURL, $conURL, $conNAME, $conTNAME, $conUSER, $conPASSWORD, $conQSTRING, $queV1, $queV2, $queV3);
	}
function curPageURL($add_on) {
				 $pageURL = 'http';
				 $pageURL .= "://";
				 if ($_SERVER["SERVER_PORT"] != "80") {
				  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				 } else {
				  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].$add_on;
				 }
				 return $pageURL;
			}
?>
