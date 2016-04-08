<?php
/*
  Plugin Name: LCB Post Parents
  Plugin URI: http://leftcurlybracket.com
  Description: Adds a post to page parent relation
  Version: 0.1.0
  Author: Tomasz Gregorczyk, Dmytro Fominykh
  License: GPL2

  /**
 * The plugin class 
 */

add_action('wp_footer', 'lcb_page_child_posts');

function lcb_page_child_posts()
{
    global $post;
    $query = array(
        'posts_per_page' => 4,
        'post_type' => array('post'),
        'post_status' => 'publish',
        'post_parent' => $post->ID
    );
    $posts = new WP_Query($query);

    $styles = '<style>
      .child-posts{
        background: #D3D3D3;
        padding-bottom: 5px;
        padding-top: 5px;
        position: fixed;
        bottom: 0;
        width: 100%;
      }
      .child-post{
        border: 1px solid #A0A0A0;
        padding: 5px;
        border-radius: 5px;
        min-height: 37px;
      }
      .child-post img{
        margin-right: 10px;
      }
      .child-post .caption{
        line-height: 25px;
      }
    </style>';

    echo $styles;

    if ($posts->have_posts()) {
        $child_posts_output = '<div class="child-posts container-fluid">';
        $child_posts_output .= '<div class="row">';
        while ($posts->have_posts()) {
            $posts->the_post();
            $child_posts_output .= '<div class="col-md-3">';
            $child_posts_output .= '<a href="' . get_permalink() . '">';
            $child_posts_output .= '<div class="child-post clearfix">';

            if (has_post_thumbnail()) {
                $child_posts_output .= get_the_post_thumbnail(get_the_ID(), array(25, 25), array(
                    'class' => 'pull-left',
                    'alt' => esc_attr(get_the_title())
                ));
                $child_posts_output .= '<div class="caption pull-left text-left">' . get_the_title() . '</div>';
            } else {
                $child_posts_output .= '<div class="caption pull-left text-left">' . get_the_title() . '</div>';
            }
            $child_posts_output .= '</div>';
            $child_posts_output .= '</a>';
            $child_posts_output .= '</div>';
        }
        $child_posts_output .= '</div>';
        $child_posts_output .= '</div>';
        echo $child_posts_output;
    }
}

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

add_action('widgets_init', create_function('', 'return register_widget("Lcb_Page_Child_Posts_Widget_Extended");'));

class Lcb_Page_Child_Posts_Widget_Extended extends WP_Widget {

    function __construct()
    {
        parent::WP_Widget(false, $name = __('Lcb Page Child Posts', 'lcb_page_child_posts_widget_plugin'));
    }

    function form($instance)
    {

        if ($instance) {
            $title = esc_attr($instance['title']);
            $textarea = $instance['textarea'];
        } else {
            $title = '';
            $textarea = '';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        $query = array(
            'posts_per_page' => 4,
            'post_type' => array('post'),
            'post_status' => 'publish',
            'post_parent' => $post->ID
        );
        $posts = new WP_Query($query);
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);

        echo $before_widget;

        if ($title) {
            echo $before_title . $title . $after_title;
        }

        $local = dirname(__FILE__) . '/templates/list.php';
        $override = get_template_directory() . '/child-posts.php';
        if (file_exists($override)) {
            include $override;
        } else {
            include $local;
        }

        echo $after_widget;
    }

}
