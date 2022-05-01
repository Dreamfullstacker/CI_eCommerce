<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

defined( 'ABSPATH' ) || exit;

require_once dirname( XIT_WACR_PLUGIN_FILE ) . '/includes/class-xit-wacr-install.php';
require_once dirname( XIT_WACR_PLUGIN_FILE ) . '/includes/class-xit-wacr-http-status.php';
require_once dirname( XIT_WACR_PLUGIN_FILE ) . '/includes/class-xit-wacr-settings-page.php';

class Woocommerce_Abandoned_Cart_Recovery {

	/**
	 * Holds single instance of WooCommerce Abandoned Cart Recovery
	 */
	public static $instance = null;

    /**
     * Holds options
     */
    private $options = null;
    
    /**
     * Holds the Webhook Call URL hook
     */
    public $webhook_call_url = "XIT_WACR_WEBHOOK_CALL_URL";
    
    /**
     * Holds the Checkbox Script URL hook
     */
    public $checkbox_script_url = "XIT_WACR_CHECKBOX_SCRIPT_URL";
    
    /**
     * Holds Validation Code
     */
    public $verification_code = "XIT_WACR_VERIFICATION_CODE";
    
    /** 
     * Checkbox opt-in message
     */
    public $checkbox_optin_message;
    
    /**
     * Responsible of single instance of XeroneIt
     * 
     * @return XeroneIt
     */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * XeroneIt Constructor
	 */
	public function __construct() {
        // Sets up options 
        // Should be called before verify_verification_code()
        $this->setup_options();		

		// Objects that need to be created on each page load
		$this->run_on_each_page_load();
		
		// Initializes hooks
		$this->init_hooks();
		
		// Verifies code and builds webhook url
		$this->verify_verification_code( $this->verification_code );
	}
	
    /**
     * Sets up options
     *
     * @since 1.0.0
     */
    public function setup_options() {
		// Populate options
		$this->options = get_option( 'xit_wacr_abandoned_cart_options' );
		
		// Gets URL hook
		if ( ! empty( $this->options[ 'xit_wacr_webhook_call_url' ] ) ) {
		    $this->webhook_call_url = $this->options[ 'xit_wacr_webhook_call_url' ];	
		}
		
		// Gets URL hook
		if ( ! empty( $this->options[ 'xit_wacr_checkbox_script_url' ] ) ) {
		    $this->checkbox_script_url = $this->options[ 'xit_wacr_checkbox_script_url' ];
		}		
		
		// Gets validation code
		if ( ! empty( $this->options[ 'xit_wacr_verification_code' ] ) ) {
		    $this->verification_code = $this->options[ 'xit_wacr_verification_code' ];	
		}
		
		// Checkbox opt-in message
		if ( ! empty( $this->options[ 'xit_wacr_checkbox_msg' ] ) ) {
		    $this->checkbox_optin_message = $this->options[ 'xit_wacr_checkbox_msg' ];
		} else {
		    $this->checkbox_optin_message = __( 'Please click the checkbox to Messenger OPT-IN', 'xit_wacr' );
		}		
    }	
	
	/**
	 * This method responsible for creating objects that are needed
	 * on each page load, option pages for example.
	 */  
	private function run_on_each_page_load() {
	    if ( is_admin() ) {
	        // Adds an option page menu under Settings menu
	        new Xit_Wacr_Settings_Page( 
	            $this->webhook_call_url, 
	            $this->verification_code, 
	            $this->checkbox_script_url,
	            $this->checkbox_optin_message 
	        );
	    }
	}	
	
	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function init_hooks() {
	    // Shows notice if woocommerce is not installed
	    if ( $this->is_request( 'admin' ) ) {
	        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    	    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    		    add_action( 'admin_notices', [ $this, 'disabled_woocommerce_notice' ] );
    		    return;
    		}
	    }	    
	    
		// Installs this plugin
		register_activation_hook( XIT_WACR_PLUGIN_FILE, [ 'Xit_Wacr_Install', 'install' ] );

	    // Inits custom cookie for unique ID
	    if ( ! headers_sent() ) {
    	    if ( ! isset( $_COOKIE['xit_wacr_unique_hash'] ) ) {
                $this->set_hash_in_cookie();   
    	    }
	    }

		// Bootstraps all tasks
		add_action( 'init', [ $this, 'on_init' ], 10, 0 );
		
		// Sets tasks that are evaluated late
		add_action( 'wp', [ $this, 'on_wp' ], 10, 0 );
	}
	
	/**
	 * Produces notice if woocommerce is not installed
	 * 
	 * @return void
	 * @since 1.0.0
	 */
	public function disabled_woocommerce_notice() {
	    ?>
	    
	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( 'WooCommerce plugin must be istalled to make this plugin work.', 'xit_wacr' ); ?></p>
	    </div>
	    
	    <?php
	}	 
	
	/**
	 * Registers hooks
	 * 
	 * @return void
	 * @since 1.0.0
	 */
	public function on_init() {

	    // Setup localisation
	    $this->load_plugin_textdomain();
	    
	    if ( $this->is_request( 'frontend' ) ) {
    	    add_action( 'woocommerce_add_to_cart', [ $this, 'on_add_to_cart' ], 10, 6 );
    	    add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'add_messanger_checkbox_button' ], 10, 0 );
    	    
    	    add_action( 'woocommerce_cart_item_removed', [ $this, 'on_cart_item_removed' ], 10, 2 );
    	    add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'on_cart_item_quantity_update' ], 10, 4 );
    	    
    	    add_action( 'woocommerce_checkout_order_processed', [ $this, 'on_checkout_order_processed' ], 10, 3 );
	    }
    }
    
	/**
	 * Registers hooks
	 *
	 * @return void
	 * @since 1.1.1
	 */	
	public function on_wp() {
	    
	    // Enqueues scripts
	    if ( $this->is_request( 'frontend' ) && is_product() ) {
		    // Adds scripts and styles 
		    add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ], PHP_INT_MAX, 0 );
		}
	}    
    
    /**
     * Makes the plugin ready for translation
     * 
     * @since 1.0.0
     */ 
    public function load_plugin_textdomain() {
		unload_textdomain( 'xit_wacr' );
        load_plugin_textdomain( 'xit_wacr', false, plugin_basename( dirname( XIT_WACR_PLUGIN_FILE ) ) . '/i18n/languages' ); 
    }
    
	/**
	 * Sends data to the remote server
	 * 
     * @param int   $product_id contains the id of the product to add to the cart.
     * @param int   $quantity contains the quantity of the item to add.
     * @param int   $variation_id ID of the variation being added to the cart.
     * @param array $variation attribute values.
     * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @return bool|null
	 * @since 1.0.0
	 */
	public function on_add_to_cart(
		$cart_item_key, 
		$product_id, 
		$quantity, 
		$variation_id, 
		$variation, 
		$cart_item_data
	) {
	    // Gets user data
        $user = $this->get_user();   
	    
        // Gets cart data
        $cart = $this->get_cart_data();
        
        // Gets fb user's unique ref
        $cart_user_unique_ref = ( isset( $_POST['xit-wacr-checkbox-status'] ) && 'checked' == $_POST['xit-wacr-checkbox-status'] ) 
            ? ( isset( $_POST['cart_user_unique_ref'] ) ? sanitize_text_field( $_POST['cart_user_unique_ref'] ) : '0' )
            : '0';
            
        if ( '0' == $cart_user_unique_ref && ( isset( $_COOKIE['xit_wacr_checkbox_status'] ) && 'checked' == $_COOKIE['xit_wacr_checkbox_status'] ) ) {
            $cart_user_unique_ref = $_COOKIE['cart_user_unique_ref'];
        }
        
        $data = [
            'action_type' => 'add',
            'user_id' => $user ? $user->ID : '0',
            'email' => $user ? $user->user_email : '',
            'first_name' => $user ? $user->user_firstname : '',
            'last_name' => $user ? $user->user_lastname : '',
            'mobile' => $user ? get_user_meta( $user->ID, 'billing_phone', true ) : '',
            'product_ids' => $cart['product_ids'],
            'product_list' => $cart['product_list'],
            'verification_code' => $this->verification_code,
            'unique_id' => isset( $_COOKIE['xit_wacr_unique_hash'] ) ? $_COOKIE['xit_wacr_unique_hash'] : '',
            'cart_user_unique_ref' => $cart_user_unique_ref,
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),
        ];
        
        // Builds query parameters
        $fields = json_encode( $data );
    
        // Sends post request to the URL
        $status = $this->send_post_request( $this->webhook_call_url, $fields );
        
        if ( null !== $status && is_int( $status ) ) {
            $this->save_failed_data( $data, $status );
        }
	}

	public function add_messanger_checkbox_button() {
	    $xit_wacr_unique_hash = isset( $_COOKIE['xit_wacr_unique_hash'] ) ? $_COOKIE['xit_wacr_unique_hash'] : '';
	    
	    $str = '<p id="xit-unchecked-car-button-message" style="display: none;">' . $this->checkbox_optin_message;
	    $str .= '<a href="javascript:void(0)" id="xit-skip-fb-checkbox">' . __( ' or skip it', 'xit_wacr') . '</a></p>';
	    $str .= '<input type="hidden" id="xit-wacr-checkbox-status" name="xit-wacr-checkbox-status">';
	    $str .= '<input type="hidden" id="xit-wacr-unique-hash" name="xit-wacr-unique-hash" value="' . $xit_wacr_unique_hash . '">';
	    $str .= '<div style="z-index:9999999;" id="MESSENGER_CHECKBOX_PLUGIN_LOADER_01"></div>';
		echo $str;
	}
	
	/**
	 * Sends post request to remote server with removed cart item
	 * 
	 * @param string
	 * @return void
	 * @since 1.0.0
	 */
	 public function on_cart_item_removed( $cart_item_key, $cart ) {
	    // Gets user data
        $user = $this->get_user();   
        
        // Gets cart data
        $cart = $this->get_cart_data();
    
        // Gets fb user's unique ref
        $cart_user_unique_ref = ( isset( $_POST['xit-wacr-checkbox-status'] ) && 'checked' == $_POST['xit-wacr-checkbox-status'] ) 
            ? ( isset( $_POST['cart_user_unique_ref'] ) ? sanitize_text_field( $_POST['cart_user_unique_ref'] ) : '0' )
            : '0';
            
        if ( '0' == $cart_user_unique_ref && ( isset( $_COOKIE['xit_wacr_checkbox_status'] ) && 'checked' == $_COOKIE['xit_wacr_checkbox_status'] ) ) {
            $cart_user_unique_ref = $_COOKIE['cart_user_unique_ref'];
        }            
    
        $data = [
            'action_type' => 'remove',
            'user_id' => $user ? $user->ID : '0',
            'email' => $user ? $user->user_email : '',
            'first_name' => $user ? $user->user_firstname : '',
            'last_name' => $user ? $user->user_lastname : '',
            'mobile' => $user ? get_user_meta( $user->ID, 'billing_phone', true ) : '',
            'product_ids' => $cart['product_ids'],
            'product_list' => $cart['product_list'],
            'verification_code' => $this->verification_code,
            'unique_id' => isset( $_COOKIE['xit_wacr_unique_hash'] ) ? $_COOKIE['xit_wacr_unique_hash'] : '',
            'cart_user_unique_ref' => $cart_user_unique_ref,
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),            
        ];
    
        // Builds query parameters
        $fields = json_encode( $data );
    
        // Sends post request to the URL
        $status = $this->send_post_request( $this->webhook_call_url, $fields );
        
        if ( null !== $status && is_int( $status ) ) {
            $this->save_failed_data( $data, $status );
        }
	 }
	 
	 
	/**
	 * Send post request while updating cart item quantity
	 * 
	 * @param string $cart_item_key
	 * @param int $quantity
	 * @param int $old_quantity
	 * @param WC_Cart
	 * @return void
	 * @since 1.0.0
	 */
	public function on_cart_item_quantity_update( $cart_item_key, $quantity, $old_quantity, $cart ) {
        // Gets user data
        $user = $this->get_user();   
        
        // Gets cart data
        $cart = $this->get_cart_data();
        
        // Gets fb user's unique ref
        $cart_user_unique_ref = ( isset( $_POST['xit-wacr-checkbox-status'] ) && 'checked' == $_POST['xit-wacr-checkbox-status'] ) 
            ? ( isset( $_POST['cart_user_unique_ref'] ) ? sanitize_text_field( $_POST['cart_user_unique_ref'] ) : '0' )
            : '0';
            
        if ( '0' == $cart_user_unique_ref && ( isset( $_COOKIE['xit_wacr_checkbox_status'] ) && 'checked' == $_COOKIE['xit_wacr_checkbox_status'] ) ) {
            $cart_user_unique_ref = $_COOKIE['cart_user_unique_ref'];
        }            
        
        $data = [
            'action_type' => 'update',
            'user_id' => $user ? $user->ID : '0',
            'email' => $user ? $user->user_email : '',
            'first_name' => $user ? $user->user_firstname : '',
            'last_name' => $user ? $user->user_lastname : '',
            'mobile' => $user ? get_user_meta( $user->ID, 'billing_phone', true ) : '',
            'product_ids' => $cart['product_ids'],
            'product_list' => $cart['product_list'],
            'verification_code' => $this->verification_code,
            'unique_id' => isset( $_COOKIE['xit_wacr_unique_hash'] ) ? $_COOKIE['xit_wacr_unique_hash'] : '',
            'cart_user_unique_ref' => $cart_user_unique_ref,
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),            
        ];
        
        // Builds query parameters
        $fields = json_encode( $data );
    
        // Sends post request to the URL
        $status = $this->send_post_request( $this->webhook_call_url, $fields );
        
        if ( null !== $status && is_int( $status ) ) {
            $this->save_failed_data( $data, $status );
        }        
	}
	
    /**
     * Sends post request on checkout-order-process
     * 
     * @param int $order_id
     * @param array $posted_data
     * @param WC_Order
     * @return null|int
     * @since 1.0.0
     */ 
	public function on_checkout_order_processed( $order_id, $posted_data, $order ) {
	    // Gets user data
        $user = $this->get_user();
        
        // Gets cart data
        $cart = $this->get_cart_data();
        
        $data = $order->get_data();
        $checkout_info = [
            "id" => $data["id"],
            "parent_id" => $data["parent_id"],
            "status" => $data["status"],
            "currency" => $data["currency"],
            "version" => $data["version"],
            "prices_include_tax" => $data["prices_include_tax"],
            "date_created" => ($data["date_created"])->date('Y-m-d H:i:s'),
            "date_modified" => ($data["date_modified"])->date('Y-m-d H:i:s'),
            "discount_total" => $data["discount_total"],
            "coupons" => $order->get_coupon_codes(),
            "discount_tax" => $data["discount_tax"],
            "shipping_total" => $data["shipping_total"],
            "shipping_tax" => $data["shipping_tax"],
            "cart_tax" => $data["cart_tax"],
            "total" => $data["total"],
            "total_tax" => $data["total_tax"],
            "customer_id" => $data["customer_id"],
            "order_key" => $data["order_key"],
            "billing" => $data["billing"],          
            "shipping" => $data["shipping"],
            "payment_method" => $data["payment_method"],
            "payment_method_title" => $data["payment_method_title"],
            "transaction_id" => $data["transaction_id"],
            "customer_ip_address" => $data["customer_ip_address"],
            "customer_user_agent" => $data["customer_user_agent"],
            "created_via" => $data["created_via"],
            "customer_note" => $data["customer_note"],
            "date_completed" => $data["date_completed"],
            "date_paid" => $data["date_paid"],
            "cart_hash" => $data["cart_hash"],
            "number" => $data["number"],
            "order_url" => $order->get_view_order_url(),
        ];
        
        // Gets fb user's unique ref
        $cart_user_unique_ref = ( isset( $_POST['xit-wacr-checkbox-status'] ) && 'checked' == $_POST['xit-wacr-checkbox-status'] ) 
            ? ( isset( $_POST['cart_user_unique_ref'] ) ? sanitize_text_field( $_POST['cart_user_unique_ref'] ) : '0' )
            : '0';
            
        if ( '0' == $cart_user_unique_ref && ( isset( $_COOKIE['xit_wacr_checkbox_status'] ) && 'checked' == $_COOKIE['xit_wacr_checkbox_status'] ) ) {
            $cart_user_unique_ref = $_COOKIE['cart_user_unique_ref'];
        }            
        
        $data = [
            'action_type' => 'checkout',
            'order_id' => $order_id,
            'user_id' => $user ? $user->ID : '0',
            'email' => $user ? $user->user_email : ( isset( $posted_data[ 'billing_email' ] ) ? $posted_data[ 'billing_email' ] : '' ),
            'first_name' => $user ? $user->user_firstname : ( isset( $posted_data[ 'billing_first_name' ] ) ? $posted_data[ 'billing_first_name' ] : '' ),
            'last_name' => $user ? $user->user_lastname : ( isset( $posted_data[ 'billing_last_name' ] ) ? $posted_data[ 'billing_last_name' ] : '' ),
            'mobile' => isset( $posted_data[ 'billing_phone' ] ) ? $posted_data[ 'billing_phone' ] : '',
            'product_ids' => $cart['product_ids'],
            'product_list' => $cart['product_list'],
            'verification_code' => $this->verification_code,
            'unique_id' => isset( $_COOKIE['xit_wacr_unique_hash'] ) ? $_COOKIE['xit_wacr_unique_hash'] : '',
            'cart_user_unique_ref' => $cart_user_unique_ref,
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),
            'checkout_info' => $checkout_info,
        ];
        
        // Builds query parameters
		$fields = json_encode( $data );

		// Sends post request to the URL
        $status = $this->send_post_request( $this->webhook_call_url, $fields );
        
        if ( null !== $status && is_int( $status ) ) {
            return $this->save_failed_data( $data, $status );
        }
        
        if ( isset( $_COOKIE[ 'xit_wacr_unique_hash' ] ) ) {
            $this->set_hash_in_cookie();
        }
	}

	/**
	 * Verifies verification code build webhook url
	 * 
	 * @param string $verification_code The verification code
	 * @return void
	 * @since 1.0.0
	 */ 
	private function verify_verification_code( $verification_code ) {
	    if ( empty( $verification_code ) ) {
            add_action( 'admin_notices', [ $this, 'verify_verification_code_notice' ] );	        
	    }
	    
	    $this->webhook_call_url = $this->trail_slash($this->webhook_call_url) . $verification_code;
	}
	
	/**
	 * Produces notice for invalid verification code
	 * 
	 * @return void
	 * @since 1.0.0
	 */ 
	public function verify_verification_code_notice() {
	    ?>
	    
	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( 'Woocommerce abandoned cart recovery verification code is invalid!', 'xit_wacr' ); ?></p>
	    </div>
	    
	    <?php
	}
	
	/**
	 * Loads scripts and styles
	 * 
	 * @since 1.0.0
	 */ 
	public function load_scripts() {
	    wp_register_script( 'xit-wacr-messenger-checkbox-plugin',  $this->checkbox_script_url, ['jquery'], null, true );
	    wp_register_script( 'xit-wacr-cart-button-handler',  $this->get_asset_url( 'assets/js/cart-button-handler.js' ), ['jquery'], null, true );
	    
	    wp_enqueue_script( 'xit-wacr-messenger-checkbox-plugin' );
	    wp_enqueue_script( 'xit-wacr-cart-button-handler' );
	}
	
	/**
	 * Returns user ID from session
	 * 
	 * @return WP_User|null
	 * @since 1.0.0
	 */ 
	public function get_user() {
        
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            return null;
        }
        
        return $current_user;
	}	
	
	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
	}	
	
	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 * @since 1.0.0
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path Assets path.
	 * @return string
	 * @since 1.0.0
	 */
	private function get_asset_url( $path ) {
		return apply_filters( 'woocommerce_get_asset_url', plugins_url( $path, XIT_WACR_PLUGIN_FILE ), $path );
	}
	
	/**
	 * Prepares cart data
	 * 
	 * @return array
	 * @since 1.0.0
	 */ 
	private function get_cart_data() {
        $product_ids = [];
        $product_list = [];
        $cart_item_keys = [];
        
        if ( ! WC()->cart->is_empty() ) {
            // Gets cart data
            $cart = WC()->cart->get_cart();

            // Prepares products list
            if ( is_array( $cart ) ) {
                foreach ( $cart as $key => $item ) {
                    
                    // Holds each item's prodcut ID
                    $item_product_id = $item[ 'product_id' ];
                    $product = is_object($item['data']) ? $item['data'] : null;
                    
                    // Pushes car item key and product ID
                    array_push( $cart_item_keys, $key );
                    array_push( $product_ids, $item_product_id );
                    
                    // Prepares product list
                    $product_list[ $item_product_id ] = [
                        'product_id' => $item_product_id,
                        'product_name' => $product ? $product->get_name() : '',
                        'price' => $product ? $product->get_price() : '',
                        'quantity' => $item['quantity'],
                        'currency' => get_woocommerce_currency(),
                        'permalink' => get_permalink( $item_product_id ),
        		        'thumbnail' => get_the_post_thumbnail_url( $item_product_id ),
                    ];
                }
            }
            
            return [
                'product_ids' => $product_ids,
                'product_list' => $product_list,
                'cart_item_keys' => $cart_item_keys,
            ];
        }
        
        return [
            'product_ids' => [],
            'product_list' => [],
            'cart_item_keys' => [],            
        ];
	}
	
	/** 
	 * Sends post request
	 * 
	 * @param string    $url The url to send the reqeust
	 * @param array     $fields An associative array of post values
	 * @return int|null
	 * @since 1.0.0
	 */
	private function send_post_request( $url, $fields ) {
	    
	    $send_data = array( "response_raw" => $fields );

	    // Initializes curl session
		$ch = curl_init( $url );

        // Sets curl options
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $send_data );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		// Perform a curl session
		curl_exec( $ch );
		
		$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        // Closes curl session
		curl_close( $ch );
		
		if ( 200 != $status ) {
		    return $status;
		}
	}
	
	/**
	 * Saves failed data in db
	 * 
	 * @param array $data
	 * @param string $status
	 * @return int|bool|null
	 * @since 1.0.0
	 */ 
	private function save_failed_data( $data, $status ) {
	    global $wpdb;
	    
	    $status = Xit_Wacr_Http_Status::status( $status );
	    $data = json_encode( $data );
	    $created_at = date( 'Y-m-d H:i:s' );
	    
	    // Checks if the table exists
	    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}xit_wacr_log_failed_data';" ) ) {
	        return;
	    }
	    
	    $result = $wpdb->query("INSERT INTO {$wpdb->prefix}xit_wacr_log_failed_data (status, data, created_at) VALUES ('{$status}', '{$data}', '{$created_at}')");
	}
	
	/**
	 * Creates a random hash value and sets in cookie
	 * 
	 * @return void
	 * @since 1.0.0
	 */ 
	private function set_hash_in_cookie() {
	    $punc = ",./?\|'\";:]}[{=+-_)(*&^%$#@!~`";
        $random_value = mt_rand() . str_shuffle($punc) . mt_rand() . str_shuffle($punc) . mt_rand() . str_shuffle($punc) . microtime( true );
        setcookie('xit_wacr_unique_hash', md5( $random_value ), time() + (60 * 60 * 24 * 365), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, false, true );
	}
	
	/**
	 * Trail slash to the url
	 * @param string $url The URL to be suffixed with a forward slash
	 * @return string
	 * @since 1.0.0
	 */ 
	private function trail_slash ( $url ) {
	    if ( empty( $url ) ) {
	        return $url;
	    }
	    
	    if ( '/' === substr( $url, -1 ) ) {
	        return $url;
	    }
	    
	    return $url . '/';
	}
}
