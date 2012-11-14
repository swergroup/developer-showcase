<?php

/*--------------------------------------------*
 * Plugin Info Widget
 *--------------------------------------------*/

class SWER_Plugin_Info_Widget extends WP_Widget {    

    function SWER_Plugin_Info_Widget(){ 
        $widget_ops = $control_ops = array();
        $this->WP_Widget( 'swer-plugin-info-widget', 'WP Plugin Info', $widget_ops, $control_ops );		
    }

    function widget(){ 
        $SSP = new SWER_Developer_Showcase;
        $selected = get_option( $this->id.'_option' );
        $args = array( 'p'=>$selected, 'post_type'=>'plugin', 'posts_per_page'=>'1');
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
    
    function update(){ 
        if( isset($_POST[ $this->id ]) ):
            update_option( $this->id.'_option', $_POST[ $this->id ] );
        endif;
    }
    
    function form(){ 
        $select_id = $this->id;
        $selected = get_option( $select_id.'_option' );

        $post_type_object = get_post_type_object('plugin');
        $label = $post_type_object->label;
        $posts = get_posts(array('post_type'=> 'plugin', 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
        echo '<select name="'. $select_id .'" id="'.$select_id.'">';
        echo '<option value="">All '.$label.' </option>';
        foreach ($posts as $post) {
            echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
        }
        echo '</select>';
    }
}


add_action('widgets_init', create_function('', 'return register_widget("SWER_Plugin_Info_Widget");'));






/*--------------------------------------------*
 * Theme Info Widget
 *--------------------------------------------*/

class SWER_Theme_Info_Widget extends WP_Widget {    

    function SWER_Theme_Info_Widget(){ 
        $widget_ops = $control_ops = array();
        $this->WP_Widget( 'swer-theme-info-widget', 'WP Theme Info', $widget_ops, $control_ops );		
    }

    function widget(){ 
        $SSP = new SWER_Developer_Showcase;
        $selected = get_option( $this->id.'_option' );
        $args = array( 'p'=>$selected, 'post_type'=>'theme', 'posts_per_page'=>'1');
        $plugins = new WP_Query( $args );
        if( $plugins->have_posts() ):
            while( $plugins->have_posts() ):
                $plugins->the_post();
                # the_title();
                $slug = get_post_meta( get_the_ID(), 'theme_slug', true );
                # echo $slug;
                echo $SSP->get_theme_info_list( $slug );
            endwhile;
        endif;
        wp_reset_postdata();                
    }
    
    function update(){ 
        if( isset($_POST[ $this->id ]) ):
            update_option( $this->id.'_option', $_POST[ $this->id ] );
        endif;
    }
    
    function form(){ 
        $select_id = $this->id;
        $selected = get_option( $select_id.'_option' );

        $post_type_object = get_post_type_object('theme');
        $label = $post_type_object->label;
        $posts = get_posts(array('post_type'=> 'theme', 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
        echo '<select name="'. $select_id .'" id="'.$select_id.'">';
        echo '<option value="">All '.$label.' </option>';
        foreach ($posts as $post) {
            echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
        }
        echo '</select>';
    }
}


add_action('widgets_init', create_function('', 'return register_widget("SWER_Theme_Info_Widget");'));

