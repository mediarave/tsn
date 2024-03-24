<?php

/**
 * Agenda template
 *
 * @package The Stewardship Network
 * @author  Alok Mishra <alok@mediarave.com>
 */

/* Template Name: Agenda */

add_action('genesis_entry_content', 'tsn_agenda');

function tsn_agenda()
{
    $agenda = [];

    $query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'session',
        'meta_key' => 'time_start',
        'orderby' => 'meta_value',
        'order' => 'ASC'
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $date = get_field('date');
            $time_period = get_field('time_start') . ' - ' . get_field('time_end');

            if (!isset($agenda[$date][$time_period])) {
                $agenda[$date][$time_period] = [];
            }
            array_push($agenda[$date][$time_period], get_the_ID());
        }
        wp_reset_postdata();

        // Sort days from low to high
        ksort($agenda);
    }

    // echo "<pre>"; print_r($agenda); echo "</pre>";

    $room_order = array('Centennial Room', 'Big Ten Room', 'Room 103', 'Room 104', 'Room 105', 'Room 106', 'Red Cedar', 'Michigamme');
    $multirooms = array('Room 103', 'Room 104', 'Room 105', 'Room 106', 'Red Cedar', 'Michigamme');

    foreach ($agenda as $date => $day) {
        echo '<div class="day" id="' . $date . '"><h2 class="date">' . $date . '</h2>';

        foreach ($day as $period => $slot) {
            // Session by room number. For specific sort order.
            $srooms = [];

            foreach ($slot as $session => $s) {
                // Put the ID of the session inside key of room number
                $room = get_field('room', $s);
                $srooms[$room] = $s;

                if (!in_array($room, $multirooms)) {
                    $session_style = 'single';
                    // $time = str_replace("-", "-<br/>", $period);
                } else {
                    $session_style = 'multiple';
                    // $time = $period;
                }
            }

            echo '<section class="period ' . $session_style . '"><div class="time"><h3>' . $period . '</h3></div><div class="slot">';

            // user-defined key sort
            uksort($srooms, function ($k1, $k2) use ($room_order) {
                return (array_search($k1, $room_order) > array_search($k2, $room_order));
            });

            foreach ($srooms as $r => $s) {
                the_session($s);
            }

            // echo "<pre>"; print_r($srooms); echo ", </pre>";

            echo "</div></section>";
        }
        echo "</div>";
    }
}

function the_session($s)
{
    $presenters = get_field('presenters', $s);
    $room = get_field('room', $s);
    $tags = get_field('tags', $s);

    if (empty($room)) {
        echo '<article class="special"><h3 class="title">' . get_field('session_title', $s) . '</h3></article>';
    } else {
        echo '<article class="session">';
        // echo '<h4 class="room">' . $room . '<br/>@ ' . get_field('time_start', $s) . '</h4>';
        echo '<h4 class="room">' . $room . '</h4>';
        if ($presenters) {
            echo '<h3 class="title"><a href="' . get_permalink($s) . '">' . get_field('session_title', $s) . '</a></h3>';
            echo '<div class="presenters-view m-1 p-1">';
            // echo '<a>Presenters</a>';
            echo '<ul class="presenters">';

            foreach ($presenters as $p) {
                // echo '<li><b><a title="' . sanitize_text_field(get_post_field('post_content', $p)) . '">' . get_the_title($p) . '</a></b><br/>' . get_field('organization', $p) . '</li>';
                echo '<li class="bg-lime-100"><b>' . get_the_title($p) . '</b><br/>' . get_field('organization', $p) . '</li>';
            }
            echo '</ul>';

            if ($tags) {
                echo '<h6 class="tags">';
                foreach ($tags as $tag) {
                    echo '<span class="tag" title=' . $tag["label"] . '>' . $tag["value"] . '</span>';
                }
                echo '</h6>';
            }

            echo '</div>';
        } else {
            echo '<h3 class="title">' . get_field('session_title', $s) . '</h3>';
        }
        echo '</article>';
    }
}

genesis();
