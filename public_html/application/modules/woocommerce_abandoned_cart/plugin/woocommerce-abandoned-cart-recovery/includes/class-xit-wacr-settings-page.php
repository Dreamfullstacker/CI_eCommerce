<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

class Xit_Wacr_Settings_Page {
    
    /**
     * Contains failed product list table object
     */ 
    private $error_log;
    
    /**
     * Holds all the options
     */ 
    private $options;
    
    /**
     * Holds the Webhook Call URL hook
     */
    public $webhook_call_url;
    
    /**
     * Holds the Checkbox Script URL hook
     */
    public $checkbox_script_url;
    
    /**
     * Holds Validation Code
     */
    public $verification_code;
    
    /** 
     * Checkbox opt-in message
     */
    public $checkbox_optin_message;    
    
    /**
     * Constructor
     */
    public function __construct( 
        $webhook_call_url, 
        $verification_code, 
        $checkbox_script_url,
        $checkbox_optin_message
    ) {
        // Sets some vars
        $this->webhook_call_url = $webhook_call_url;
        $this->verification_code = $verification_code;
        $this->checkbox_script_url = $checkbox_script_url;
        $this->checkbox_optin_message = $checkbox_optin_message;
    
        // Registers a menu
        add_action( 'admin_menu', [ $this, 'on_admin_menu' ] );    
        add_filter( 'set-screen-option', [ $this, 'on_set_screen' ], 10, 3 );        
        
        // Registers options
        add_action( 'admin_init', [ $this, 'on_admin_init' ] );
    }
    
    public function on_set_screen( $status, $option, $value ) {
    	return $value;
    }    
    
    /**
     * Adds a new menu and submenu in the admin dashboard
     */ 
    public function on_admin_menu() {
        global $submenu;
    
    	add_menu_page(
    		__( 'WooCommerce abandoned cart recovery settings', 'xit_wacr' ),
    		__( 'Abandoned Cart', 'xit_wacr' ),
    		'manage_options',
    		'xit_wacr_settings',
    		[ $this, 'abandoned_cart_settings' ],
    		'dashicons-cart',
    		59
    	);
    	
    	// Adds submenu
    	$hook = add_submenu_page(
    	    'xit_wacr_settings',
    		__( 'WooCommerce abandoned cart recovery error logs', 'xit_wacr' ),
    		__( 'Error Logs', 'xit_wacr' ),
    		'manage_options',
    		'xit_wacr_logs',
    		[ $this, 'cart_error_log' ]
    	);    
    
        if ( isset( $submenu[ 'xit_wacr_settings' ][0][0] ) ) {
            $submenu[ 'xit_wacr_settings' ][0][0] = __( 'Settings', 'xit_wacr' );
        }
    
    	add_action( "load-$hook", [ $this, 'on_set_screen_option' ] );
    }
    
    /**
     * Sets screen option for the failed product list
     */ 
    public function on_set_screen_option() {
    
    	$option = 'per_page';
    	$args   = [
    		'label'   => __( 'Logs per page', 'xit_wacr' ),
    		'default' => 10,
    		'option'  => 'wacr_error_log_per_page'
    	];
    
    	add_screen_option( $option, $args );
    	
        require_once dirname( XIT_WACR_PLUGIN_FILE ) . '/includes/class-xit-wacr-error-log-list.php';
        
        // Creates an instance of Xit_Wacr_Error_Log_List
        $this->error_log = new Xit_Wacr_Error_Log_List();    	
    }
    
    /**
     * The main method for displaying list of logs
     */ 
    public function cart_error_log() {
        
    	?>
    	<div class="wrap">
    		<h2><?php _e( 'Error Logs', 'xit_wacr' ); ?></h2>
    		<div id="poststuff">
				<form method="post">
					<?php
					    $this->error_log->prepare_items();
					    $this->error_log->display(); 
					?>
				</form>
    			<div class="clear"></div>
    		</div>
    	</div>
    <?php
    
    }
    
    /**
     * Creates options page
     */ 
    public function abandoned_cart_settings() {
        // Fills in options 
        $this->options = get_option( 'xit_wacr_abandoned_cart_options' );
        
        ?>
        <div class="wrap">
            <h2><?php _e( 'WooCommerce Abandoned Cart Recovery', 'xit_wacr' ); ?></h2>
            <form method="post" action="options.php">
            <?php
                settings_errors();
                settings_fields( 'xit_wacr_abandoned_cart_group' );
                do_settings_sections( 'xit_wacr_abandoned_cart_settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Sets up settings
     */ 
    public function on_admin_init() {
        
        // Registers settings
        register_setting(
            'xit_wacr_abandoned_cart_group',
            'xit_wacr_abandoned_cart_options',
            [ $this, 'sanitize_fields' ]
        );
        
        add_settings_section(
            'xit_wacr_setting_section_id',
            __( 'Settings', 'xit_wacr' ),
            [ $this, 'print_section_info' ],
            'xit_wacr_abandoned_cart_settings'
        );        
        
        // Adds verification code
        add_settings_field(
            'xit_wacr_verification_code',
            __( 'Verification Code', 'xit_wacr' ),
            [ $this, 'verification_code_field' ],
            'xit_wacr_abandoned_cart_settings',
            'xit_wacr_setting_section_id'
        ); 
        
        // Adds server URL
        add_settings_field(
            'xit_wacr_webhook_call_url',
            __( 'Webhook Call URL', 'xit_wacr' ),
            [ $this, 'webhook_call_url_field' ],
            'xit_wacr_abandoned_cart_settings',
            'xit_wacr_setting_section_id'
        );
        
        // Adds checkbox script URL
        add_settings_field(
            'xit_wacr_checkbox_script_url',
            __( 'Checkbox Script URL', 'xit_wacr' ),
            [ $this, 'checkbox_script_url_field' ],
            'xit_wacr_abandoned_cart_settings',
            'xit_wacr_setting_section_id'
        );        
        
        // Add checkbox message
        add_settings_field(
            'xit_wacr_checkbox_msg',
            __( 'Error Message', 'xit_wacr' ),
            [ $this, 'checkbox_message_field' ],
            'xit_wacr_abandoned_cart_settings',
            'xit_wacr_setting_section_id'
        );
    }
    
    public function print_section_info() {
        echo '';
    }
    
    public function verification_code_field() {
        printf(
          '<input type="text" id="xit_wacr_verification_code" class="regular-text" name="xit_wacr_abandoned_cart_options[xit_wacr_verification_code]" value="%1s" required /><p class="description" id="tagline-description">%2s</p>',
          isset( $this->options[ 'xit_wacr_verification_code' ] ) ? esc_attr( $this->options[ 'xit_wacr_verification_code' ] ) : $this->verification_code,
          __( 'The verification code that has been provided you.', 'xit_wacr' )
        );
    }    
    
    public function webhook_call_url_field() {
        printf(
            '<input type="text" id="xit_wacr_webhook_call_url" class="regular-text" name="xit_wacr_abandoned_cart_options[xit_wacr_webhook_call_url]" value="%s" required /><p class="description" id="tagline-description">%2s</p>',
            isset( $this->options[ 'xit_wacr_webhook_call_url' ] ) ? esc_attr( $this->options[ 'xit_wacr_webhook_call_url' ] ) : $this->webhook_call_url,
          __( 'The URL where cart data will be submitted.', 'xit_wacr' )
        );
    }
    
    public function checkbox_script_url_field() {
        printf(
            '<input type="text" id="xit_wacr_checkbox_script_url" class="regular-text" name="xit_wacr_abandoned_cart_options[xit_wacr_checkbox_script_url]" value="%s" required /><p class="description" id="tagline-description">%2s</p>',
            isset( $this->options[ 'xit_wacr_checkbox_script_url' ] ) ? esc_attr( $this->options[ 'xit_wacr_checkbox_script_url' ] ) : $this->checkbox_script_url,
          __( 'The Javascript file for facebook messenger plugin.', 'xit_wacr' )
        );
    }    
    
    public function checkbox_message_field() {
        printf(
            '<input type="text" id="xit_wacr_checkbox_msg" class="regular-text" name="xit_wacr_abandoned_cart_options[xit_wacr_checkbox_msg]" value="%s" required /><p class="description" id="tagline-description">%2s</p>',
            isset($this->options[ 'xit_wacr_checkbox_msg' ]) ? esc_attr( $this->options[ 'xit_wacr_checkbox_msg' ] ) : $this->checkbox_optin_message,
          __( 'The error message to be shown if checkbox is not checked.', 'xit_wacr' )
        );
    }
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize_fields( $input )
    {
        $new_input = array();
        
        if( isset( $input[ 'xit_wacr_verification_code' ] ) && ! empty( $input[ 'xit_wacr_verification_code' ] ) ) {
            $new_input[ 'xit_wacr_verification_code' ] = sanitize_key( $input[ 'xit_wacr_verification_code' ] );
        }  else {
            add_settings_error( 'xit_wacr_settings_error', 'verification-code', __( 'Please provide a valid verification code.', 'xit_wacr' ), 'error' );
        }

        if( isset( $input[ 'xit_wacr_webhook_call_url' ] ) && ! empty( $input[ 'xit_wacr_webhook_call_url' ] ) ) {
            $new_input[ 'xit_wacr_webhook_call_url' ] = esc_url_raw( $input[ 'xit_wacr_webhook_call_url' ] );
        }  else {
            add_settings_error( 'xit_wacr_settings_error', 'webhoo-call-url', __( 'Please provide a valid webhook url.', 'xit_wacr' ), 'error' );
        }    
        
        if( isset( $input[ 'xit_wacr_checkbox_script_url' ] ) && ! empty( $input[ 'xit_wacr_checkbox_script_url' ] ) ) {
            $new_input[ 'xit_wacr_checkbox_script_url' ] = esc_url_raw( $input[ 'xit_wacr_checkbox_script_url' ] );
        }  else {
            add_settings_error( 'xit_wacr_settings_error', 'checkbox-script-url', __( 'Please provide a valid checkbox script url.', 'xit_wacr' ), 'error' );
        }        
        
        if( isset( $input['xit_wacr_checkbox_msg'] ) && ! empty( $input['xit_wacr_checkbox_msg'] ) ) {
            $new_input[ 'xit_wacr_checkbox_msg' ] = sanitize_text_field( $input[ 'xit_wacr_checkbox_msg' ] );
        }  else {
            add_settings_error( 'xit_wacr_settings_error', 'checkbox-msg', __( 'Please provide a checkbox opt-in message.', 'xit_wacr' ), 'error' );
        }

        return $new_input;
    }    
}