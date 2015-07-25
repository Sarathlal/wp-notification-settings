<?php
   /*
   Plugin Name: WordPress Notification Settings
   Plugin URI: http://sarathlal.com/wp-plugins/
   Description: A simple plugin to edit default WordPress Notifications
   Version: 1.0
   Author: Sarathlal N
   Author URI: http://sarathlal.com
   License: GPL2
   */
   
class simpleWPnotificationsettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Notification Settings', 
            'Notifications', 
            'manage_options', 
            'simple-custom-notification-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'simple_custom_notification_value' );
        ?>
        <div class="wrap">
            <h2>WordPress Notification</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'simple_custom_notification_group' );
                do_settings_sections( 'simple-custom-notification-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'simple_custom_notification_group', // Option group
            'simple_custom_notification_value', // Option name 
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'simple_custom_notification_id', // ID 
            'Change Notification Settings!', // Title
            array( $this, 'print_section_info' ), // Callback
            'simple-custom-notification-admin' // Page 
        );  

        add_settings_field(
            'from_email', // ID
            'From Email', // Title 
            array( $this, 'from_email_callback' ), // Callback
            'simple-custom-notification-admin', // Page 
            'simple_custom_notification_id' // Section           
        );      

        add_settings_field(
            'from_name', 
            'From Name', 
            array( $this, 'from_name_callback' ),
            'simple-custom-notification-admin', 
            'simple_custom_notification_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['from_email'] ) )
            $new_input['from_email'] = sanitize_email( $input['from_email'] );

        if( isset( $input['from_name'] ) )
            $new_input['from_name'] = sanitize_text_field( $input['from_name'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Change Notification "From" Email ID & Name';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function from_email_callback()
    {
        printf(
            '<input type="email" id="from_email" name="simple_custom_notification_value[from_email]" value="%s" />', 
            isset( $this->options['from_email'] ) ? esc_attr( $this->options['from_email']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function from_name_callback()
    {
        printf(
            '<input type="text" id="from_name" name="simple_custom_notification_value[from_name]" value="%s" />', 
            isset( $this->options['from_name'] ) ? esc_attr( $this->options['from_name']) : ''
        );
    }
    
    /** 
     * Change the email
     */
    public function notification_from_email()
    {
		$blog_url = get_site_url();
		$output1 = str_replace(array('http://','https://', 'www'), '', $blog_url);
		$output2 = strstr($output1, '/', true);

		$default_from = 'noreply@'. $output2;
		$var_email = get_option( 'simple_custom_notification_value', '' );
		if ($var_email) {
			return $var_email['from_email'];
		} else {
			return $default_from;
		}
			
    }
    
    
    /** 
     * Change the from address
     */
    public function notification_from_name()
    {
		$var_name = get_option( 'simple_custom_notification_value', '' );
		if ($var_name) {
			return $var_name['from_name'];
		} else {
			return 'no-reply';
		}
    }
    
        
}

if( is_admin() )
    $my_settings_page = new simpleWPnotificationsettings();


add_filter('wp_mail_from', array ('simpleWPnotificationsettings', 'notification_from_email'));
add_filter('wp_mail_from_name', array ('simpleWPnotificationsettings', 'notification_from_name'));
