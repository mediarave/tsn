<?php

/**
 * Presenters template
 *
 * @package The Stewardship Network
 * @author  Alok Mishra <alok@mediarave.com>
 */

/* Template Name: Posters */

add_action('genesis_entry_content', 'tsn_posters');

function tsn_posters()
{
    $posters = [];

    $query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'poster',
        'orderby' => 'none',
        'order' => 'ASC'
    ));

    if ($query->have_posts()) {
        echo '<ol>';
        while ($query->have_posts()) {
            $query->the_post();

            $presenters = get_field('presenters', get_the_ID());

            echo '<li><h3><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h3>';

            echo '<ul class="presenters">';
            foreach ($presenters as $p) {
                echo '<li><b><a title="' . sanitize_text_field(get_post_field('post_content', $p)) . '">' . get_the_title($p) . '</a></b><br/>' . get_field('organization', $p) . '</li>';
            }
            echo '</ul></li>';

            // echo "<pre>"; print_r($presenters); echo "</pre>";
            // array_push($posters, get_the_ID());
        }
        echo '</ol>';
        wp_reset_postdata();

        // Sort days from low to high
        ksort($posters);
    }

    // echo "<pre>"; print_r($posters); echo "</pre>";

}

genesis();
