<?php

/**
 * Template Name: Webcasts
 * Webcasts via cards layout. More info on image hover.
 *
 * @package mediaRAVE
 * @author Alok Mishra <alok@mediarave.com>
 */


function tsn_webcasts_scripts_styles()
{
    wp_enqueue_style('slick-style', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-style-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');

    wp_enqueue_script('slick-script', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'tsn_webcasts_scripts_styles');

//* Force full width content layout
add_filter('genesis_site_layout', '__genesis_return_full_width_content');

?>

<style>
    .site-inner,
    .modal {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;

    }

    .team .member {
        font-family: 'Roboto Condensed', sans-serif;
    }
</style>


<?php

add_action('genesis_entry_content', 'tsn_webcasts');
function tsn_webcasts()
{

    $webcasts = [];

    // https://developer.wordpress.org/reference/classes/wp_query/
    $webcasts_query = new WP_Query(array(
        'post_type' => 'webcast',
        'posts_per_page' => -1,
        // 'tax_query' => array(
        //     array(
        //         'taxonomy' => 'webcast_tag',
        //         'field'    => 'slug',
        //         'terms'    => 'pinned',
        //     )
        // ),
        'orderby' => 'date',
        'meta_key' => 'date',
        'orderby' => 'meta_value',
        'order' => 'DESC'
    ));

    // tsn_debug($webcasts_query->posts);


    if ($webcasts_query->have_posts()) {
        while ($webcasts_query->have_posts()) {
            $webcasts_query->the_post();
            $fields = get_fields();

            // tsn_debug($fields);

            $webcasts[] = [
                'id' => get_the_ID(),
                'title' => $fields['title'],
                'summary' => get_the_excerpt(), // falls back to post_content if no excerpt
                'content' => get_the_content(),
                'date' => $fields['date'],
                'time' => $fields['time'],
                'guests' => $fields['presenters'],
                'registration' => $fields['registration_link'],
                'video' => $fields['video_link'],
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
            ];
        }
    } else {
        echo '<p>No webcasts found!</p>';
    }

    wp_reset_postdata(); // restore original post data

    date_default_timezone_set('America/New_York'); // Set the default time zone to Eastern Time
    $currentDateTime = new DateTime();

    $upcoming_webcasts = [];
    foreach ($webcasts as $webcast) {

        $webcastEndTime = new DateTime($webcast['date']);
        $webcastEndTime->setTime(13, 0, 0); // 1pm ET
        $oneYearAhead = (new DateTime())->add(new DateInterval('P1Y'));

        // upcoming webcasts up to a year from now
        if (($webcastEndTime > $currentDateTime) && ($webcastEndTime <= $oneYearAhead)) {
            array_unshift($upcoming_webcasts, $webcast);
        }
    }

    $past_webcasts = [];
    foreach ($webcasts as $webcast) {
        $webcast_pinned = (in_array('pinned', wp_list_pluck(get_the_terms($webcast['id'], 'webcast_tag'), 'slug')));

        $webcastEndTime = new DateTime($webcast['date']);
        $webcastEndTime->setTime(13, 0, 0); // 1pm ET
        $twoYearsAgo = (new DateTime())->sub(new DateInterval('P2Y'));

        // past webcasts up to 2 years ago or pinned
        if ((($webcastEndTime < $currentDateTime) && ($webcastEndTime >= $twoYearsAgo)) || $webcast_pinned) {
            $past_webcasts[] = $webcast;
        }
    }

    echo '<h2 class="my-8">Upcoming Webcasts</h2><section id="carousel" class="upcoming">';
    display_webcasts($upcoming_webcasts, 'upcoming');
    echo '</section>';

    echo '<h2 class="my-4">Past Webcasts</h2><section class="webcasts grid grid-cols-1 md:grid-cols-3 gap-2 justify-items-center">';
    display_webcasts($past_webcasts, 'past');
    echo '</section>';

    return;
}

function display_webcasts($webcasts, $view_mode = 'past')
{
    foreach ($webcasts as $webcast) {

        $is_upcoming = ($view_mode == 'upcoming');

        $guests = $webcast['guests'];
?>
        <article class="webcast group <?php echo ($is_upcoming ? 'upcoming' : 'past') ?> flex flex-col w-full p-4 border-b-4 border-lime-600 md:bg-stone-100 md:rounded-md" id="webcast-<?php echo $webcast['id']; ?>">
            <header class="webcast-image mb-1">
                <a class="block w-full h-[200px] group-[.upcoming]:h-[300px] bg-no-repeat bg-center bg-cover bg-teal-900" style="background-image: url(<?php echo $webcast['image']; ?>)" href="<?php echo get_permalink($webcast['id']); ?>"></a>
            </header>
            <section class="webcast-content">
                <h3 class="text-lg font-bold line-clamp-2 group-[.past]:min-h-[3.5rem]">
                    <a href="<?php echo get_permalink($webcast['id']); ?>"><?php echo $webcast['title']; ?></a>
                </h3>
                <div class="datetime text-sm font-bold text-lime-600">
                    <?php echo $webcast['date']; ?> @ <?php echo '12pm ET (11am CT, 10am MT, 9am PT)' ?>
                </div>
                <div class="summary line-clamp-5 group-[.past]:min-h-[10rem]">
                    <p class="text-base my-3"><?php echo $webcast['summary']; ?></p>
                </div>
            </section>
            <section class="guests my-2">
                <h3 class="text-base font-bold m-0 self-end">Guests:</h3>
                <?php
                if (!is_countable($guests)) {
                    echo '<span class="guests text-sm">Guests will be announced soon..</span>';
                } else {
                    foreach ($guests as $g) {
                        echo '<span class="guests text-sm">' . get_the_title($g) . '</span>';
                        if ($g != end($guests)) {
                            echo ', ';
                        }
                    }
                }
                ?>
            </section>
            <footer class="mt-auto">

                <?php
                if (!empty($webcast['registration']) && $is_upcoming) {
                ?>
                    <a target="_blank" href="<?php echo $webcast['registration']; ?>" class="button text-sm !px-2 !py-1.5 !pb-1">Register</a>
                <?php
                }
                ?>
                <a href="<?php echo get_permalink($webcast['id']); ?>" class="button text-sm !px-2 !py-1.5 !pb-1">Learn More</a>
            </footer>
        </article>
<?php
    }
}

?>

<script>
    // jQuery(document).ready(function($) {
    //     console.log('Webcasts!');
    // });
</script>

<style>
    .slick-slide {
        height: inherit !important;
    }

    .slick-slider .slick-prev,
    .slick-slider .slick-next {
        width: 30px;
        height: 30px;
        z-index: 1;
        top: 160px;
        padding-top: 2px;
        background-color: transparent !important;
    }

    .slick-slider .slick-prev {
        left: 30px;

    }

    .slick-slider .slick-next {
        right: 40px;
    }

    .slick-slider .slick-prev:before,
    .slick-slider .slick-next:before {
        opacity: .88;
        font-size: 40px;
        color: #d9f99d;
        border-radius: 50%;
        padding: 5px 2px 0;
        background-color: #65a30d;
        /* border: 2px solid #84cc16; */
    }


    /*
        .slick-track {
            display: flex;
            margin-bottom: 10px;

            .slick-slide {
                height: inherit;

                &>div {
                    height: 100%;

                    &>div {
                        height: 100%;
                        margin: 0 auto;
                    }
                }
            }
        }
    */
</style>

<?php

//* Run the Genesis loop
genesis();
