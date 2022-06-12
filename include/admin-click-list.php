<?php
/**
 * File: rafax-clickcount/include/admin-click-list.php
 *
 * @package rafax_clickcount
 */
defined( 'ABSPATH' ) || die();
if (!class_exists('WP_List_Table')) {
    require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
 
}
/** * Create a new table class that will extend the WP_List_Table */
class Links_Table_Custom extends WP_List_Table
 
{
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'singular_form',
            'plural' => 'plural_form',
            'ajax' => true
        ));
    }
	
	/**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
	function column_link($item)
    {
		$actions = array(
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'cltd_example')),
        );

        return sprintf(
            '<a target="_blank" href="%s">%s</a>%s',
            $item['link'],$item['link'], $this->row_actions($actions)
        );
    }

	/** 
	* Override the parent columns method. Defines the columns to use in your listing table 
	* * @return Array 
	*/
	function get_columns()
	{
		$columns = [
			'cb' => '<input type="checkbox" />', 
			'id' =>  __('ID', 'rafax-clickcount') ,
			'link' => __('Enlace', 'rafax-clickcount') , 
			'clicks' => __('Clicks', 'rafax-clickcount') , 
			'date_first_click' => __('Fecha primer click', 'rafax-clickcount') , 
			'date_last_click' => __('Fecha ultimo click', 'rafax-clickcount') , 
			  
		];
		return $columns;
	}

	/** 
	* Columns to make sortable. 
	* * @return array 
	*/
	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'id' => array('id',false),
			'link' => array('link',true),
			'clicks' => array('clicks',false) ,
			'date_first_click' => array('date_first_click',false) ,
			'date_last_click' => array('date_last_click',false), 
		);
		return $sortable_columns;
	}

	public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
	

	public function get_hidden_columns()
	{
			// Setup Hidden columns and return them
			return array();
	}

	/** 
	* Returns an associative array containing the bulk action 
	* * @return array */
	function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

	public function process_bulk_action()
	{
		global $wpdb;
        $table = $wpdb->prefix . 'rafax_clickcount';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table WHERE id IN($ids)");
            }
        }
	}
	/** 
	*Text displayed when no record data is available 
	*/
	public function no_items()
	{
		_e('No record found in the database.', 'bx');
	}
	
		

	public function prepare_items()
	{
		global $wpdb;
        $table = $wpdb->prefix . 'rafax_clickcount';

        $per_page = 20; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'link';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $searchpartQuery ='';
        if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
            $searchterm = $_REQUEST['s'];
            $searchpartQuery = " WHERE link LIKE '%$searchterm%'";
        }
        
	
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table $searchpartQuery ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		//$total_items = count($this->items);
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
	}
}




add_action( 'admin_menu', 'rafax_clickcount_menu' );
/**
 * Agrega el menú del plugin al formulario de WordPress
 *
 * @return void
 */
function rafax_clickcount_menu() {
	add_menu_page(
		'Click Count',
		'Click Count',
		'manage_options',
		'rafax_clickcount_menu',
		'custom_list_page',
		'dashicons-feedback',
		75
	);
}

function custom_list_page(){
    
    $class = new Links_Table_Custom;
	$class->prepare_items();
	$message = '';
    if ('delete' === $class->current_action()) {
		$deleted=1;
		if(is_array($_REQUEST['id'])){
			
			$deleted= count($_REQUEST['id']);
		}
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Enlaces eliminados: %d', 'rafax-clickcount'), $deleted) . '</p></div>';
    }
    ?>
<div class="wrap">

    
    <h2><?php _e('Links', 'rafax-clickcount')?> 
    </h2>
    <?php echo $message; ?>
	<form method="post">
        <input type="hidden" name="page" value="rafax_clickcount_menu" />
            <?php $class->search_box('search', 'search_id'); ?>
    </form>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $class->display() ?>
    </form>

</div>
<?php
}

