<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once dirname( XIT_WACR_PLUGIN_FILE ) . '/includes/class-wp-list-table.php';
}

class Xit_Wacr_Error_Log_List extends WP_List_Table {
    
    /**
     * Holds table name
     */
    protected $table = null;
    
    /** 
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        
        global $wpdb;
        $this->table = "{$wpdb->prefix}xit_wacr_log_failed_data";
    }
    
    /** 
     * Fetches error logs from db
     * 
     * @param int $per_page
     * @param int $page_number
     * 
     * @return mixed
     * @since 1.0.0
     */ 
    public function get_error_logs( $per_page = 10, $page_number = 1 ) {
        global $wpdb;
        
        $sql = "SELECT * FROM {$this->table}";
        
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        
        $sql .= " LIMIT {$per_page}";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        
        return $results;
    }
    
    /**
     * Deletes a error log from db
     * 
     * @param int $id
     * @return void
     * @since 1.0.0
     */ 
    public function delete_error_log( $id ) {
        global $wpdb;
        
        $wpdb->delete( $this->table, [ 'id' => $id ], [ '%d' ] );
    }
    
    /**
     * Fetches total records
     * 
     * @return string|null
     * @since 1.0.0
     */
    public function total_rows() {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        $rows = $wpdb->get_var( $sql );
        
        return $rows;
    }
    
    /** 
     * Displays message when no error logs found.
     */
    public function no_items() {
        _e( 'No logs found', 'xit_wacr' );
    }
    
    /**
     * Renders column names
     * 
     * @param array $item An array of db column names
     */
    public function column_name( $item ) {
        // Creates a nonce
        $delete_nonce = wp_create_nonce( 'xit_wacr_erro_log_nonce' );
        
        $title = '<strong>' . $item['name'] . '</strong>';
        
        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&log_id=%s&_wpnonce=%s">' . __( 'Delete', 'xit_wacr' ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),    
        ];
        
        return $title . $this->row_actions( $actions );
    }
    
    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'status':
                return $item[ $column_name ];
            case 'data':
                return $item[ $column_name ];
            case 'created_at':
              return $item[ $column_name ];
            default:
              return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }
    
    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="delete_log[]" value="%s" />', $item['id']
        );
    }
    
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'status'    => __( 'Status', 'xit_wacr' ),
            'data' => __( 'Failed Data', 'xit_wacr' ),
            'created_at'    => __( 'Created At', 'xit_wacr' )
        ];
        
        return $columns;
    }
    
    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'status' => [ 'status', true ],
            'created_at' => [ 'created_at', true ],
        ];
        
        return $sortable_columns;
    }
    
    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'delete_log' => __( 'Delete', 'xit_wacr' )
        ];
        
        return $actions;
    }
    
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
    
        $this->_column_headers = $this->get_column_info();
        
        /** Process bulk action */
        $this->process_bulk_action();
        
        $per_page     = $this->get_items_per_page( 'wacr_error_log_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = $this->total_rows();
        
        /**
         * We have to provide the total number of items and 
         * number of items to show on a page
         */ 
        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page
        ] );
        
        $this->items = $this->get_error_logs( $per_page, $current_page );
    }
    
    public function process_bulk_action() {
    
        // Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
        
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            
            if ( ! wp_verify_nonce( $nonce, 'xit_wacr_erro_log_nonce' ) ) {
                die( 'Go get a life script kiddies' );
            } else {
                self::delete_error_log( absint( $_GET['log_id'] ) );
                
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }
        }
    
        // If the delete bulk action is triggered
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'delete_log' ) {
        
            $delete_ids = esc_sql( $_POST['delete_log'] );
        
            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                $this->delete_error_log( $id );
            }
        
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }    
}