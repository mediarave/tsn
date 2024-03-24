<?php

/**
 * Sponsors template
 *
 * @package The Stewardship Network
 * @author  Alok Mishra <alok@mediarave.com>
 */

/* Template Name: Sponsors */

const LEVEL0_WIDTH = 260;
const REDUCTION_FACTOR = 0.66;

add_action('genesis_entry_content', 'tsn_sponsors');

function tsn_sponsors()
{
    $sponsors = [];

    $slug = get_post_field('post_name', get_post());
    $year = substr($slug, 0, 4);

    $query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'sponsor',
        'orderby' => 'title',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'sponsors',
                'field' => 'slug',
                'terms' => $year,
            ),
        ),
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $sponsor_index = get_field('sponsor_level')['value'];
            $sponsor_label = get_field('sponsor_level')['label'];

            if (!isset($sponsors[$sponsor_index])) {
                $sponsors[$sponsor_index]['label'] = $sponsor_label;
                $sponsors[$sponsor_index]['sponsors'] = [];
            }

            array_push($sponsors[$sponsor_index]['sponsors'], get_the_ID());
        }
        wp_reset_postdata();
        ksort($sponsors);
    }

    // echo "<pre>"; print_r($sponsors); echo "</pre>";

    // get the index and level in full sponsors array
    foreach ($sponsors as $index => $level) {
        $last = count($sponsors) - 1 - $index ? false : true;

        echo sprintf('<section class="mb-4 overflow-hidden level-%s"><h3>%s</h3>', $index, $level['label']);

        // for each sponsor in level's sponsors array
        echo sprintf(
            '<ul class="flex items-center sponsors%s">',
            $last ? ' last' : ''
        );

        // calculate the ideal area for the sponsor logo based on the level, tapering off as the level increases
        $ideal_area = LEVEL0_WIDTH * LEVEL0_WIDTH * (1 - REDUCTION_FACTOR * log($index + 1));

        foreach ($level['sponsors'] as $s) {
            $sponsor = get_post($s);
            $thumbnail_info = wp_get_attachment_image_src(get_post_thumbnail_id($s), 'large');
            $sponsor_site = get_field('sponsor_site', $s);

            if ($thumbnail_info) {
                $sponsor_logo = $thumbnail_info[0]; // $sponsor_logo = get_the_post_thumbnail_url($s, 'large');

                $thumb_width = $thumbnail_info[1];
                $thumb_height = $thumbnail_info[2];

                $thumb_ratio =  $ideal_area / ($thumb_width * $thumb_height);
                $logo_width = round($thumb_width * sqrt($thumb_ratio));
            }

            echo sprintf('<li><a href="%s" target="_blank">', $sponsor_site);
            if ($last || !$thumbnail_info) {
                echo sprintf('<strong>%s</strong>', $sponsor->post_title);
            } else {
                echo sprintf('<img width="%s" src="%s" alt="%s">', $logo_width, $sponsor_logo, $sponsor->post_title);
            }
            echo '</a></li>';
        }

        echo '</ul></section>';
    }
}

genesis();
