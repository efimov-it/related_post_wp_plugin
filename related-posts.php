<?php
/*
Plugin Name: Похожие посты по провайдеру
Description: Плагин для вывода трех случайных постов с совпадающим значением мета поля "provider".
*/

function related_posts_by_provider($content) {
    if (is_single()) { // Проверяем, что просматривается одиночный пост
        $post_id = get_the_ID();
        $provider = get_post_meta($post_id, 'provider', true); // Получаем значение мета поля "provider"
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'orderby' => 'rand', // Случайный порядок
            'meta_query' => array(
                array(
                    'key' => 'provider',
                    'value' => $provider,
                    'compare' => '=',
                ),
            ),
            'post__not_in' => array($post_id), // Исключаем текущий пост
        );

        $related_posts = new WP_Query($args);
        
        if ($related_posts->have_posts()) {
            $content .= '<h3>Похожие посты</h3>';
            $content .= '<ul class="rpp-container">';
            while ($related_posts->have_posts()) {
                $related_posts->the_post();

                $post_thumbnail_url = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'medium') : plugin_dir_url(__FILE__) . 'assets/img/preview.png';

                $content .= '<li class="rpp-container_item">';
                $content .= '<img src="'. $post_thumbnail_url .'" alt="'. get_the_title() .'" width="235" height="176" class="rpp-container_itemImg"/>';
                $content .= '<a class="rpp-container_itemText" href="' . get_permalink() . '">';
                $content .= get_the_title();
                $content .= '</a>';
                $content .= '</li>';
            }
            $content .= '</ul>';
            wp_reset_postdata();
        }
    }
    return $content;
}

add_filter('the_content', 'related_posts_by_provider');

function enqueue_custom_styles() {
    wp_enqueue_style('related-posts-styles', plugin_dir_url(__FILE__) . 'css/main.css');
}

add_action('wp_enqueue_scripts', 'enqueue_custom_styles');
?>