<?php

/*
  Plugin Name: Post Parents
  Plugin URI: http://leftcurlybracket.com
  Description: Adds a post to page parent relation
  Version: 0.1.0
  Author: Tomasz Gregorczyk
  License: MIT

  /**
 * The plugin class 
 */

class lcb_post_parents {

    function __construct()
    {
        add_action('add_meta_boxes', array($this, 'lcb_meta_boxes'));
    }

    function lcb_meta_boxes()
    {
        global $wp_post_types, $wp_taxonomies;
        foreach ($wp_post_types as $post_type) {
            if ($post_type->name != 'page') {
                add_meta_box('lcb-post-parent', __('Parent', 'lcb-post-parents'), array($this, 'lcb_parent_meta_box'), $post_type->name, 'side', 'default');
            }
        }
    }

    function lcb_parent_meta_box($post)
    {
        wp_dropdown_pages(array(
            'name' => 'parent_id',
            'id' => 'parent_id',
            'echo' => 1,
            'show_option_none' => __('&mdash; Select &mdash;'),
            'option_none_value' => '0',
            'selected' => $post->post_parent)
        );
    }

}

new lcb_post_parents();
