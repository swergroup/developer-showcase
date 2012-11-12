<?php
/*
Plugin Name: Plugins Showcase
Plugin URI: #
Description: Plugin showcase manager for WordPress plugin developers. 
Author: SWERgroup
Version: 0.1a
Author URI: http://swergroup.com
*/



class SWER_PluginCheck {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
		
		// load plugin text domain
		add_action( 'init', array( $this, 'textdomain' ) );

		// Register admin styles and scripts
		#add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		#add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
	
		// Register site styles and scripts
		#add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
		
	    /*
	     * TODO:
	     * Define the custom functionality for your plugin. The first parameter of the
	     * add_action/add_filter calls are the hooks into which your code should fire.
	     *
	     * The second parameter is the function name located within this class. See the stubs
	     * later in the file.
	     *
	     * For more information: 
	     * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
	     */
	    #add_action( 'TODO', array( $this, 'action_method_name' ) );
	    #add_filter( 'TODO', array( $this, 'filter_method_name' ) );

	} // end constructor
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate
	
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here		
	} // end deactivate
	
	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function uninstall( $network_wide ) {
		// TODO define uninstall functionality here		
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation
	 */
	public function textdomain() {
		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		load_plugin_textdomain( 'plugin-name-locale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'plugin-name-admin-styles', plugins_url( 'plugin-name/css/admin.css' ) );
	
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function register_admin_scripts() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'plugin-name-admin-script', plugins_url( 'plugin-name/js/admin.js' ) );
	
	} // end register_admin_scripts
	
	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'plugin-name-plugin-styles', plugins_url( 'plugin-name/css/display.css' ) );
	
	} // end register_plugin_styles
	
	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'plugin-name-plugin-script', plugins_url( 'plugin-name/js/display.js' ) );
	
	} // end register_plugin_scripts
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
	/**
 	 * Note:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *		  WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *		  Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 */
	function action_method_name() {
    	// TODO define your action method here
	} // end action_method_name
	
	/**
	 * Note:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *		  WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *		  Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 */
	function filter_method_name() {
	    // TODO define your filter method here
	} // end filter_method_name
  
} // end class

// TODO: update the instantiation call of your plugin to the name given at the class definition
$plugin_name = new SWER_PluginCheck();






/*--------------------------------------------*
 * Widget
 *--------------------------------------------*/

class SWER_PlugCheck_Widget extends WP_Widget {

    
    function SWER_PlugCheck_Widget(){ 
        $widget_ops = $control_ops = array();
        $this->WP_Widget( 'swer-plugcheck-widget', 'SWER PlugCheck', $widget_ops, $control_ops );		
    }

    function widget(){ 
        $pages = array( 'page2cat', 'uploadplus', 'gengo', 'paypal-shortcodes' );
        
        echo '</ul>';
        $up = $down = $total = 0;
        foreach( $pages as $page ):        
            $res = wp_remote_get( 'http://wordpress.org/extend/plugins/'.$page.'/' );
            if( ! is_wp_error( $res ) ):
                preg_match( '/content\=\"UserDownloads\:(.*)\"/', $res['body'], $count );
                preg_match( '/itemprop\=\"name\"\>(.*)\<\/h2/', $res['body'], $head );
                echo '<li><a href="http://wordpress.org/extend/plugins/'.$page.'/">'.$head[1].'</a> &mdash; '.$count[1].'</li>';
                $total = $total + $count[1];
                $up++;
            else:
                echo 'n/a';
                $down++;
            endif;
        endforeach;
        echo '<li>Totale: '.$total."</li>";
        echo '</ul>';
    }
    
    function update(){ }
    
    function form(){ }
}


add_action('widgets_init', create_function('', 'return register_widget("SWER_PlugCheck_Widget");'));
