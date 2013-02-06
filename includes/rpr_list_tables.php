<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
 
class RPR_List_Table extends WP_List_Table {

    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'promo code',     //singular name of the listed records
            'plural'    => 'promo codes',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    function column_default($item, $column_name){
        switch($column_name){
            case 'id':
            case 'value':
			case 'activation':
			case 'expiration':
			case 'partner_expiration':
			case 'post_title':
                return $item[$column_name];
				break;
			case 'our_code':
				$our_code = en_de('decode', $item[$column_name], ED_KEY);
				return $our_code;
				break;	
			case 'partner_code':  
				$partner_code = en_de('decode', $item[$column_name], ED_KEY);
				return $partner_code;
				break;
            case 'user_email':
			case 'user_dob':
			case 'p_o_id':
			case 'p_o_detail':
			case 'post_id':
				$post_id = $item[$column_name];
				return $item[$column_name];
				break;
			case 'event_location':
				$default = $item[$column_name];
            	if ($default != 'xxxx') {
            		 return $item[$column_name];
            	} else {
            		return "Un-used";
            	}
				break;
			case 'user_name':
            	$used = $item[$column_name];
            	if ($used != 'Not Used') {
            		 return 'Used by: '.$item[$column_name];
            	} else {
            		return "Un-used";
            	}
				break;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
        
    function column_our_code($item){
        
        //Build row actions
        $actions = array(
            //'edit'      => sprintf('<a href="http://localhost/wp-admin/options-general.php?page=promo-options&ids=">Edit</a>',$_REQUEST['page'],'edit',$item['our_code']),
        );
        
        //Return the title contents
		$our_code = en_de('decode', $item['our_code'], ED_KEY);
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $our_code,
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
	function column_post_title($item){
        
        //Build row actions
        $actions = array(
            //'edit'      => sprintf('<a href="http://localhost/wp-admin/options-general.php?page=promo-options&ids=">Edit</a>',$_REQUEST['page'],'edit',$item['our_code']),
        );
        
        //Return the title contents
        return sprintf('<a href="post.php?post=%2$s&action=edit">%1$s</a>%3$s',
            /*$1%s*/ $item['post_title'],
            /*$2%s*/ $item['post_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'our_code'     => 'Our Code',
            'partner_code'    => 'Partner Code',
            'value'  => 'Value',
			'activation' => 'Activation Date',
			'expiration' => 'Our Code Exp.',
			'partner_expiration' => 'Partner Code Exp.',
            'event_location' => 'Event',
			'p_o_id' => 'Product',
			'post_id' => 'Promo ID',
            'post_title' => 'Promo Title',
			'user_name' => 'Availability'
        );
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
			'our_code'     => array('our_code',false),     //true means its already sorted
            'partner_code'    => array('partner_code',false),
            'value'  => array('value',false),
            'event_location' => array('event_location',false),
			'p_o_id' => array('p_o_id',false),
            'post_title' => array('post_title',false),
			'user_name' => array('user_name',false),
			'activation' => array('activation',false),
			'expiration' => array('expiration',true),
			'partner_expiration' => array('partner_expiration',false)
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
           // 'edit'    => 'Edit Post Connection',
			//'expire'    => 'Edit Expiration Date',
			//'val'	  => 'Edit Value'
        );
        return $actions;
    }
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        $var = $this->current_action();
        //Detect when a bulk action is being triggered...
		switch ($var) {
        case 'edit':
		case 'val':
			break;
		}
        
    }
    
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items($post_id, $table_name) {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 100;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        //$data = $this->rpr_data;
        // Formulate Query
        // This is the best way to perform an SQL query
        // For more examples, see mysql_real_escape_string()
        //$post_id = '4';
		if ($post_id == 'x') {
			$event_query = sprintf("
        	SELECT * FROM $table_name, `wp_posts`
				WHERE `wp_posts`.`ID` = `wp_rpr_codes`.`post_id`
				AND `wp_rpr_codes`.`user_name` = '%s'", mysql_real_escape_string('Not Used'));
		} elseif ($post_id == 'y') {
			$event_query = sprintf("
        	SELECT * FROM $table_name, `wp_posts`
				WHERE `wp_posts`.`ID` = `wp_rpr_codes`.`post_id`
				AND `wp_rpr_codes`.`user_email` LIKE '%s'", mysql_real_escape_string('%@%'));
		} else {
        	$event_query = sprintf("
        	SELECT * FROM $table_name, `wp_posts`
				WHERE `wp_posts`.`ID` = `wp_rpr_codes`.`post_id`
				AND `wp_rpr_codes`.`user_name` = 'Not Used'
				AND `wp_posts`.`ID` = '%s'", mysql_real_escape_string($post_id));
		}
         	
        // Perform Query
        $event_result = mysql_query($event_query);
        
        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        $the_data = array();
        while($line = mysql_fetch_array($event_result, MYSQL_ASSOC)){
            $the_data[] = $line;
        }
        
        $data = $the_data;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'our_code'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}
/***************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function rpr_render_list_page($postid, $table_name){
    //echo $post_id;
	if (!$postid) {$postid='x';}
    //Create an instance of our package class...
    $testListTable = new RPR_List_Table($postid, $table_name);
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items($postid, $table_name);
    
    ?>
    <div class="wrap">
        
        <div id="icon-users" class="icon32"><br/></div>        
        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            <p>The following information is directly correlated with the codes associated to this promotion.</p>
        </div>
        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>
        
    </div>
    <?php
}
