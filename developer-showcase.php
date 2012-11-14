<?php
/*
Plugin Name: Developer Showcase
Plugin URI: http://wordpress.org/extend/plugins/developer-showcase/
Description: Plugin showcase manager for WordPress plugin/theme developers. 
Author: SWERgroup
Version: 0.3
Author URI: http://swergroup.com
*/

/*
 * Includes code from:
 * https://github.com/markjaquith/WordPress-Plugin-Readme-Parser
 */


register_uninstall_hook( __FILE__, 'swer_developer_showcase_uninstall' );
function swer_developer_showcase_uninstall(){
    // none yet
}

class SWER_Developer_Showcase {
	 
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
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
        add_filter( 'manage_plugin_posts_columns', array( &$this, 'manage_plugin_posts_columns' ) );
        add_action( 'manage_plugin_posts_custom_column', array( &$this, 'manage_plugin_posts_custom_column' ), 10, 2);
        add_filter( 'manage_theme_posts_columns', array( &$this, 'manage_theme_posts_columns' ) );
        add_action( 'manage_theme_posts_custom_column', array( &$this, 'manage_theme_posts_custom_column' ), 10, 2);
        add_action( 'save_post', array( &$this, '_save_post' ) );
        #add_filter( 'the_content', array( &$this, 'the_content') );
                
		#add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, '_register_admin_scripts' ) );
	} // end constructor
	
	public function activate( $network_wide ) {
        // nothing to setup, yet
	} // end activate

	public function deactivate( $network_wide ) {
        // nothing to setup, yet
	} // end deactivate
	
	public function _textdomain() {
		load_plugin_textdomain( 'swer-developer-showcase', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	public function _register_admin_styles() {
		wp_enqueue_style( 'swer-developer-showcase-admin-styles', plugins_url( 'wp-plugins-showcase/css/admin.css' ) );
	} // end register_admin_styles

	public function _register_admin_scripts() {
	    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'swer-developer-showcase-admin-script', plugins_url( 'wp-plugins-showcase/lib/sparkline.min.js' ), 'jquery');	
	} // end register_admin_scripts
	
	public function _register_plugin_styles() {
		wp_enqueue_style( 'swer-developer-showcase-plugin-styles', plugins_url( 'wp-plugins-showcase/css/display.css' ) );	
	} // end register_plugin_styles
	
	public function _register_plugin_scripts() {	
		wp_enqueue_script( 'swer-developer-showcase-plugin-script', plugins_url( 'wp-plugins-showcase/js/display.js' ) );
	
	} // end register_plugin_scripts

    public function _register_post_types() {
      $p_labels = array(
        'name' => _x('Plugins', 'post type general name', 'swer-developer-showcase'),
        'singular_name' => _x('Plugin', 'post type singular name', 'swer-developer-showcase'),
        'add_new' => _x('Add New', 'plugin', 'swer-developer-showcase'),
        'add_new_item' => __('Add New Plugin', 'swer-developer-showcase'),
        'edit_item' => __('Edit Plugin', 'swer-developer-showcase'),
        'new_item' => __('New Plugin', 'swer-developer-showcase'),
        'all_items' => __('All Plugins', 'swer-developer-showcase'),
        'view_item' => __('View Plugin', 'swer-developer-showcase'),
        'search_items' => __('Search Plugins', 'swer-developer-showcase'),
        'not_found' =>  __('No plugin found', 'swer-developer-showcase'),
        'not_found_in_trash' => __('No plugin found in Trash', 'swer-developer-showcase'), 
        'parent_item_colon' => '',
        'menu_name' => __('WP Plugins', 'swer-developer-showcase')

      );
      $p_args = array(
        'labels' => $p_labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'query_var' => true,
        'rewrite' => array( 'slug' => _x( 'wordpress-plugins', 'URL slug', 'swer-developer-showcase' ) ),
        'capability_type' => 'page',
        'has_archive' => true, 
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
      ); 
      register_post_type( 'plugin', $p_args);
      
      $t_labels = array(
        'name' => _x('Themes', 'post type general name', 'swer-developer-showcase'),
        'singular_name' => _x('Theme', 'post type singular name', 'swer-developer-showcase'),
        'add_new' => _x('Add New', 'theme', 'swer-developer-showcase'),
        'add_new_item' => __('Add New Theme', 'swer-developer-showcase'),
        'edit_item' => __('Edit Theme', 'swer-developer-showcase'),
        'new_item' => __('New Theme', 'swer-developer-showcase'),
        'all_items' => __('All Theme', 'swer-developer-showcase'),
        'view_item' => __('View Theme', 'swer-developer-showcase'),
        'search_items' => __('Search Theme', 'swer-developer-showcase'),
        'not_found' =>  __('No theme found', 'swer-developer-showcase'),
        'not_found_in_trash' => __('No theme found in Trash', 'swer-developer-showcase'), 
        'parent_item_colon' => '',
        'menu_name' => __('WP Themes', 'swer-developer-showcase')

      );
      $t_args = array(
        'labels' => $t_labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'query_var' => true,
        'rewrite' => array( 'slug' => _x( 'wordpress-themes', 'URL slug', 'swer-developer-showcase' ) ),
        'capability_type' => 'page',
        'has_archive' => true, 
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
      ); 
      register_post_type( 'theme', $t_args);
      
      
      
    }
    
    public function admin_menu(){
        # add_submenu_page( 'edit.php?post_type=plugin', 'Code Snippets', 'Code Snippets', 'publish_posts', 'code-snippets', array( &$this, 'admin_menu_display') );
    }
    
    function admin_menu_display(){
        // move to external class
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
                echo '<div class="aligncenter">';
                echo '<strong>'.$wpinfo['count'].'</strong> ';
                echo '<span class="sparkline" data-values="'.$this->get_remote_stats( $slug, 14).'"></span>';
                echo '</div>';
            break;
            
        endswitch;
    }


    public function manage_theme_posts_columns( $post_columns ){
        $post_columns['theme_info'] = 'Theme Info';
        $post_columns['theme_downloads'] = 'Downloads';
        return $post_columns;
    }
    
    public function manage_theme_posts_custom_column( $column, $post_id ){
        $slug = get_post_meta( $post_id, 'theme_slug', true );
        $wpinfo = $this->get_theme_remote_info( $slug );
        switch( $column ):
            case 'theme_info':
                echo '<strong><a href="http://wordpress.org/extend/themes/'.$slug.'/">'.$parsed['name'].'</a></strong> <br>';
                echo 'Rating: '.$wpinfo['rating'].' &mdash; Support: '.$wpinfo['support'];
            break;
            
            case 'theme_downloads':
                echo '<div class="aligncenter">';
                echo '<strong>'.$wpinfo['count'].'</strong> ';
                echo '<span class="sparkline" data-values="'.$this->get_remote_stats( $slug, 14).'"></span>';
                echo '</div>';
            break;
            
        endswitch;
    }



    public function _add_meta_boxes(){
        add_meta_box( 'showcase_plugins_readme', "Plugin Info", array(&$this,'metabox_readme'), 'plugin', 'side', 'core' ); 
        add_meta_box( 'showcase_themes_readme', "Theme Info", array(&$this,'metabox_readme'), 'theme', 'side', 'core' ); 
    }

    public function metabox_readme( $post ){
        wp_nonce_field( plugin_basename( __FILE__ ), 'swer_sp_slug' );
        if( 'plugin' == get_post_type($post) ):
            $slug = get_post_meta( $post->ID, 'plugin_slug', true );
            echo '<label for="swer_sp_slug"><strong>';
            _e("Plugin slug", 'myplugin_textdomain' );
            echo '</strong></label> ';
            echo '<input type="text" id="swer_sp_slug" name="swer_sp_slug" value="'.$slug.'" size="10" />';
            if( $slug ):
                echo $this->get_plugin_info_list( $slug );
            endif;
        elseif( 'theme' == get_post_type($post) ):
            $slug = get_post_meta( $post->ID, 'theme_slug', true );
            echo '<label for="swer_sp_slug"><strong>';
            _e("Theme slug", 'myplugin_textdomain' );
            echo '</strong></label> ';
            echo '<input type="text" id="swer_sp_slug" name="swer_sp_slug" value="'.$slug.'" size="10" />';
            if( $slug ):
                echo $this->get_theme_info_list( $slug );
            endif;
        endif;
    }

    public function _save_post( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if( 'plugin' == $_POST['post_type'] ):
            update_post_meta( $post_id, 'plugin_slug', $_POST['swer_sp_slug'] );
        elseif( 'theme' == $_POST['post_type'] ):
            update_post_meta( $post_id, 'theme_slug', $_POST['swer_sp_slug'] );            
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
        $key = '_swer_sp_'.$slug.'_remote_stats';
        if ( false === ( $sparkline = get_transient( $key ) ) ) {
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
            set_transient( $key, $sparkline, 60*15 );
        }
        return $sparkline;
        
    }

     
     
    public function get_remote_readme_file( $slug ){
        $key = '_swer_sp_'.$slug.'_get_remote_readme_file';
        if ( false === ( $parsed = get_transient( $key ) ) ) {
            $readme = wp_remote_get( 'http://plugins.svn.wordpress.org/'.$slug.'/trunk/readme.txt' );
            if( ! is_wp_error( $readme ) ):
                $parsed = $this->_parse_readme_file($readme['body']);
            else:
                $parsed = false;
            endif;
        }
        set_transient( $key, $parsed, 60*15 );
        return $parsed;
    }
	
	
	public function get_plugin_remote_info( $slug ){
	    $key = '_swer_sp_'.$slug.'_get_plugin_remote_info';
        if ( false === ( $plugin_remote_info = get_transient( $key ) ) ) {
    	    $res = wp_remote_get( 'http://wordpress.org/extend/plugins/'.$slug.'/' );
            if( ! is_wp_error( $res ) ):
                preg_match( '/content\=\"UserDownloads\:(.*)\"/', $res['body'], $count );
                #preg_match( '/\<meta\ itemprop\=\"RatingValue\" content\=\"(.*)\"\>/', $res['body'], $rateval );
                #preg_match( '/\<meta\ itemprop\=\"RatingCount\" content\=\"(.*)\"\>/', $res['body'], $ratecount );
                preg_match( '/\<span\>(.*)\ out\ of\ (.*)\ stars\<\/span\>/', $res['body'], $rate );
                preg_match( '/\<p\>(.*)\ of\ (.*)\ support\ threads/', $res['body'], $support );
                
                $rate_value = (isset($rate[1])) ? $rate[1].'/'.$rate[2] : 'n/a';
                $support_value = (isset($support[1])) ? $support[1].'/'.$support[2] : 'n/a';
                
                $plugin_remote_info = array(
                    'count' => $count[1],
                    'rating' => $rate_value,
                    'support' => $support_value
                );
    	    endif;
            set_transient( $key, $plugin_remote_info, 60*15 );
    	}
    	return $plugin_remote_info;
	}

    // <li><strong>Downloads:</strong> 124,352</li>
    public function get_theme_remote_info( $slug ){
	    $key = '_swer_sp_'.$slug.'_get_theme_remote_info';
        if ( false === ( $theme_remote_info = get_transient( $key ) ) ) {
    	    $res = wp_remote_get( 'http://wordpress.org/extend/themes/'.$slug.'/' );
            if( ! is_wp_error( $res ) ):
                preg_match( '/itemprop\=\"name\">(.*)\<\/h2>/', $res['body'], $name );
                preg_match( '/Downloads\:\<\/strong\>(.*)\<\/li\>/', $res['body'], $count );
                preg_match( '/\<a\ href\=\"(.*)\">Download\ version\ (.*)\<\/a\>/', $res['body'], $version );
                preg_match( '/\<span\>(.*)\ out\ of\ (.*)\ stars\<\/span\>/', $res['body'], $rate );
                preg_match( '/\<p\>(.*)\ of\ (.*)\ support\ threads/', $res['body'], $support );
                $rate_value = (isset($rate[1])) ? $rate[1].'/'.$rate[2] : 'n/a';
                $support_value = (isset($support[1])) ? $support[1].'/'.$support[2] : 'n/a';
                $theme_remote_info = array(
                    'name' => trim($name[1]),
                    'count' => trim($count[1]),
                    'version' => trim($version[2]),
                    'rating' => $rate_value,
                    'support' => $support_value
                );
            endif;
            #set_transient( $key, $theme_remote_info, 60*15 );
        }
        return $theme_remote_info;    
    }


	public function get_plugin_info_list( $slug ){
	    $key = '_swer_sp_'.$slug.'_get_plugin_info_list_';
        if ( false === ( $plugin_info = get_transient( $key ) ) ) {

    	    $readme = $this->get_remote_readme_file( $slug );
    	    $wpinfo = $this->get_plugin_remote_info( $slug );
    	    
    	    $svn_base = 'http://plugins.svn.wordpress.org/';
    	    $svn_link = ($readme['stable_tag']==='trunk') ? $svn_base.$slug.'/trunk/' : $svn_base.$slug.'/tags/'.$readme['stable_tag'].'/';
    	    
            $out = '<ul>';
            $out.= '<li><strong><a href="http://wordpress.org/extend/plugins/'.$slug.'/">'.$readme['name'].'</a></strong></li>';
            $out.= '<li><strong>Stable Tag</strong>: <a href="'.$svn_link.'">'.$readme['stable_tag'].'</a></li>';
            $out.= '<li><strong>Requires</strong> '.$readme['requires_at_least'].' &mdash; <strong>Tested</strong> '.$readme['tested_up_to'].'</li>';
            $out.= '<li><strong>Downloads</strong>: '.$wpinfo['count'].' <span class="sparkline">'.$this->get_remote_stats( $slug ).'</span></li>';
            $out.= '<li><strong>Rating</strong>: '.$wpinfo['rating'].'</li>';
            $out.= '<li><strong>Support</strong>: '.$wpinfo['support'].'</li>';
            $out.= '<li><strong>Committers</strong>: '.join(', ', $readme['contributors']).'</li>';
            $out.= '<li><strong>Tags</strong>: '.join(', ', $readme['tags']).'</li>';
            $out.= '';
            $out.= '</ul>';
            $plugin_info = $out;
            set_transient( $key, $plugin_info, 60*15 );
        }
        return $plugin_info;
	}
	


	public function get_theme_info_list( $slug ){
	    $key = '_swer_sp_'.$slug.'_get_theme_info_list_';
        if ( false === ( $plugin_info = get_transient( $key ) ) ) {

#    	    $readme = $this->get_remote_readme_file( $slug );
    	    $wpinfo = $this->get_theme_remote_info( $slug );
    	    
    	    $svn_base = 'http://themes.svn.wordpress.org/';
    	    $svn_link = $svn_base.$slug.'/'.$readme['version'].'/';
    	    
            $out = '<ul>';
            $out.= '<li><strong><a href="http://wordpress.org/extend/themes/'.$slug.'/">'.$wpinfo['name'].'</a></strong></li>';
            $out.= '<li><strong>Version</strong>: <a href="'.$svn_link.'">'.$wpinfo['version'].'</a></li>';
            $out.= '<li><strong>Downloads</strong>: '.$wpinfo['count'].' <span class="sparkline">'.$this->get_remote_stats( $slug ).'</span></li>';
            $out.= '<li><strong>Rating</strong>: '.$wpinfo['rating'].'</li>';
            $out.= '<li><strong>Support</strong>: '.$wpinfo['support'].'</li>';
            $out.= '';
            $out.= '</ul>';
            $plugin_info = $out;
            set_transient( $key, $plugin_info, 60*15 );
        }
        return $plugin_info;
	}



	
	
	public function the_content( $content ){
	    global $post;
	    if( 'plugin' == get_post_type($post->ID) ):
	        $new_content = $this->_parse_readme_file( strip_tags($content) );
	        return print_r($new_content,true);
	    else:
	        return $content;
	    endif;
	}
	

} // end class

$plugin_name = new SWER_Developer_Showcase();






/*--------------------------------------------*
 * Widget
 *--------------------------------------------*/

class SWER_Plugin_Info_Widget extends WP_Widget {

    
    function SWER_Plugin_Info_Widget(){ 
        $widget_ops = $control_ops = array();
        $this->WP_Widget( 'swer-plugin-info-widget', 'Plugin Info', $widget_ops, $control_ops );		
    }

    function widget(){ 
        $SSP = new SWER_Developer_Showcase;
        $args = array( 'post_type'=>'plugin', 'posts_per_page'=>'1');
        $plugins = new WP_Query( $args );
        if( $plugins->have_posts() ):
            while( $plugins->have_posts() ):
                $plugins->the_post();
                # the_title();
                $slug = get_post_meta( get_the_ID(), 'plugin_slug', true );
                # echo $slug;
                echo $SSP->get_plugin_info_list( $slug );
            endwhile;
        endif;
        wp_reset_postdata();                
    }
    
    function update(){ }
    
    function form(){ }
}


add_action('widgets_init', create_function('', 'return register_widget("SWER_Plugin_Info_Widget");'));
