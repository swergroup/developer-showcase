<?php
/*
Plugin Name: Plugins Showcase
Plugin URI: #
Description: Plugin showcase manager for WordPress plugin developers. 
Author: SWERgroup
Version: 0.3
Author URI: http://swergroup.com
*/

/*
 * Includes code from:
 * https://code.google.com/p/wordpress-plugin-readme-parser/
 */


register_uninstall_hook( __FILE__, 'swer_showcasw_plugin_uninstall' );
function swer_showcasw_plugin_uninstall(){
    
}

class SWER_Showcase_Plugin {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
        require_once( dirname(__FILE__) . '/lib/parse-readme.php');
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
		// load plugin text domain
		add_action( 'init', array( &$this, '_textdomain' ) );
        add_action( 'init', array( &$this, '_register_post_types' ) );  

        add_action( 'add_meta_boxes', array( &$this, '_add_meta_boxes' ));
		add_action( 'admin_enqueue_scripts', array( &$this, '_register_admin_scripts' ) );
        add_filter( 'manage_plugin_posts_columns', array( &$this, 'manage_plugin_posts_columns' ) );
        add_action( 'manage_plugin_posts_custom_column', array( &$this, 'manage_plugin_posts_custom_column' ), 10, 2);
        add_action( 'save_post', array( &$this, '_save_post' ) );
        add_filter( 'the_content', array( &$this, 'the_content') );

        
		#add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
	} // end constructor
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {
	} // end activate
	
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
	} // end deactivate
	

	/**
	 * Loads the plugin text domain for translation
	 */
	public function _textdomain() {
		load_plugin_textdomain( 'swer-showcase-plugins', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function _register_admin_styles() {
		wp_enqueue_style( 'swer-showcase-plugins-admin-styles', plugins_url( 'wp-plugins-showcase/css/admin.css' ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function _register_admin_scripts() {
	    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'swer-showcase-plugins-admin-script', plugins_url( 'wp-plugins-showcase/lib/sparkline.min.js' ), 'jquery');	
	} // end register_admin_scripts
	
	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function _register_plugin_styles() {
		wp_enqueue_style( 'swer-showcase-plugins-plugin-styles', plugins_url( 'wp-plugins-showcase/css/display.css' ) );	
	} // end register_plugin_styles
	
	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function _register_plugin_scripts() {	
		wp_enqueue_script( 'swer-showcase-plugins-plugin-script', plugins_url( 'wp-plugins-showcase/js/display.js' ) );
	
	} // end register_plugin_scripts

    public function _register_post_types() {
      $labels = array(
        'name' => _x('Plugins', 'post type general name', 'swer-showcase-plugins'),
        'singular_name' => _x('Plugin', 'post type singular name', 'swer-showcase-plugins'),
        'add_new' => _x('Add New', 'plugin', 'swer-showcase-plugins'),
        'add_new_item' => __('Add New Plugin', 'swer-showcase-plugins'),
        'edit_item' => __('Edit Plugin', 'swer-showcase-plugins'),
        'new_item' => __('New Plugin', 'swer-showcase-plugins'),
        'all_items' => __('All Plugins', 'swer-showcase-plugins'),
        'view_item' => __('View Plugin', 'swer-showcase-plugins'),
        'search_items' => __('Search Plugins', 'swer-showcase-plugins'),
        'not_found' =>  __('No plugins found', 'swer-showcase-plugins'),
        'not_found_in_trash' => __('No plugins found in Trash', 'swer-showcase-plugins'), 
        'parent_item_colon' => '',
        'menu_name' => __('Showcase', 'swer-showcase-plugins')

      );
      $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'query_var' => true,
        'rewrite' => array( 'slug' => _x( 'wordpress-plugins', 'URL slug', 'swer-showcase-plugins' ) ),
        'capability_type' => 'page',
        'has_archive' => true, 
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
      ); 
      register_post_type('plugin', $args);
    }
    
    
    public function manage_plugin_posts_columns( $post_columns ){
        $post_columns['plugin_info'] = 'Plugin Info';
        $post_columns['plugin_downloads'] = 'Downloads';
        return $post_columns;
    }
    
    public function manage_plugin_posts_custom_column( $column, $post_id ){
        $slug = get_post_meta( $post_id, 'plugin_slug', true );
        $parsed = $this->get_remote_readme_file( $slug );
        $wpinfo = $this->get_plugin_remote_info( $slug );
        switch( $column ):
            case 'plugin_info':
                echo '<strong><a href="http://wordpress.org/extend/plugins/'.$slug.'/">'.$parsed['name'].'</a></strong> <br>';
                echo 'Rating: '.$wpinfo['rating'].' &mdash; Support: '.$wpinfo['support'];
            break;
            
            case 'plugin_downloads':
                echo '<strong>'.$wpinfo['count'].'</strong> <br>';
                echo '<span class="sparkline">'.$this->get_remote_stats( $slug ).'</span>';
            break;
            
        endswitch;
    }
    
    
    
    

    public function _add_meta_boxes(){
        add_meta_box( 'showcase_plugins_readme', "Plugin Info", array(&$this,'metabox_readme'), 'plugin', 'side', 'core' ); 
    }

    public function metabox_readme( $post ){
        $slug = get_post_meta( $post->ID, 'plugin_slug', true );
        wp_nonce_field( plugin_basename( __FILE__ ), 'swer_sp_slug' );
        echo '<label for="swer_sp_slug"><strong>';
        _e("Plugin slug", 'myplugin_textdomain' );
        echo '</strong></label> ';
        echo '<input type="text" id="swer_sp_slug" name="swer_sp_slug" value="'.$slug.'" size="10" />';
        
        if( $slug ):
            echo $this->get_plugin_info_list( $slug );
        endif;
    }

    public function _save_post( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if( 'plugin' == $_POST['post_type'] ):
            update_post_meta( $post_id, 'plugin_slug', $_POST['swer_sp_slug'] );
        endif;        
    }


    private function _parse_readme_file( $content ){
        $wp_readme_parser = new WordPress_Readme_Parser;
	    $new_content = $wp_readme_parser->parse_readme_contents( $content );
        return $new_content;
    }
	
/*--------------------------------------------*
* Core Functions
*---------------------------------------------*/


    public function get_remote_stats( $slug, $days=30){
        $key = '_swer_sp_'.$slug.'_get_remote_statss';
        if ( false === ( $parsed = get_transient( $key ) ) ) {
            $stats = wp_remote_get( 'http://api.wordpress.org/stats/plugin/1.0/downloads.php?slug='.$slug.'&limit='.$days.'&callback=?' );
            if( ! is_wp_error( $stats ) ):
                $out = array();
                $data = json_decode($stats['body'],true);
                foreach( $data as $k=>$count):
                    $out[] = $count;
                endforeach;
                $sparkline = join(',',$out);
            else:
                $sparkline = '';
            endif;
        }
        return $sparkline;
        
    }

     
     
    public function get_remote_readme_file( $slug ){
        $key = '_swer_sp_'.$slug.'_remote_readme_file';
        if ( false === ( $parsed = get_transient( $key ) ) ) {
            $readme = wp_remote_get( 'http://plugins.svn.wordpress.org/'.$slug.'/trunk/readme.txt' );
            if( ! is_wp_error( $readme ) ):
                $parsed = $this->_parse_readme_file($readme['body']);
            else:
                $parsed = false;
            endif;
        }
        return $parsed;
    }
	
	
	public function get_plugin_remote_info( $slug ){
	    $key = '_swer_sp_'.$slug.'_plugin_remote_info';
        if ( false === ( $plugin_remote_info = get_transient( $key ) ) ) {
    	    $res = wp_remote_get( 'http://wordpress.org/extend/plugins/'.$slug.'/' );
            if( ! is_wp_error( $res ) ):
                preg_match( '/content\=\"UserDownloads\:(.*)\"/', $res['body'], $count );
                #preg_match( '/\<meta\ itemprop\=\"RatingValue\" content\=\"(.*)\"\>/', $res['body'], $rateval );
                #preg_match( '/\<meta\ itemprop\=\"RatingCount\" content\=\"(.*)\"\>/', $res['body'], $ratecount );
                preg_match( '/\<span\>(.*)\ out\ of\ (.*)\ stars\<\/span\>/', $res['body'], $rate );
                preg_match( '/\<p\>(.*)\ of\ (.*)\ support\ threads/', $res['body'], $support );
                $plugin_remote_info = array(
                    'count' => $count[1],
                    'rating' => $rate[1].'/'.$rate[2],
                    'support' => $support[1].'/'.$support[2]
                );
    	    endif;
    	}
    	return $plugin_remote_info;
	}
	
	public function get_downloads( $slug ){
	    $res = wp_remote_get( 'http://wordpress.org/extend/plugins/'.$slug.'/' );
        if( ! is_wp_error( $res ) ):
            preg_match( '/content\=\"UserDownloads\:(.*)\"/', $res['body'], $count );
            return $count[1];
        else:
            return 'n/a';
        endif;
	}
	
	public function get_plugin_info_list( $slug ){
	    $key = '_swer_sp_'.$slug.'_get_plugin_info_list';
        if ( false === ( $plugin_info = get_transient( $key ) ) ) {

    	    $readme = $this->get_remote_readme_file( $slug );
    	    $wpinfo = $this->get_plugin_remote_info( $slug );
    	    
    	    $svn_base = 'http://plugins.svn.wordpress.org/';
    	    $svn_link = ($readme['stable_tag']==='trunk') ? $svn_base.$slug.'/trunk/' : $svn_base.$slug.'/tags/'.$readme['stable_tag'].'/';
    	    
            $out = '<ul>';
            $out.= '<li><strong><a href="http://wordpress.org/extend/plugins/'.$slug.'/">'.$readme['name'].'</a></strong></li>';
            $out.= '<li><strong>Stable Tag</strong>: <a href="'.$svn_link.'">'.$readme['stable_tag'].'</a></li>';
            $out.= '<li><strong>Requires</strong> '.$readme['requires_at_least'].' &mdash; <strong>Tested</strong> '.$readme['tested_up_to'].'</li>';
            $out.= '<li><strong>Committers</strong>: '.join(', ', $readme['contributors']).'</li>';
            $out.= '<li><strong>Tags</strong>: '.join(', ', $readme['tags']).'</li>';
            $out.= '<li><strong>Downloads</strong>: '.$wpinfo['count'].'</li>';
            $out.= '<li><strong>Rating</strong>: '.$wpinfo['rating'].'</li>';
            $out.= '<li><strong>Support</strong>: '.$wpinfo['support'].'</li>';
            $out.= '</ul>';
            $plugin_info = $out;
            set_transient( $key, $plugin_info );
        }
        return $plugin_info;
	}
	

} // end class

// TODO: update the instantiation call of your plugin to the name given at the class definition
$plugin_name = new SWER_Showcase_Plugin();






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
