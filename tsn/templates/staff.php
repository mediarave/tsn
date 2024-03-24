<?php

/**
 * Template Name: Staff
 * Staff page layout with tiles at top and multiple categories. More info on image hover.
 *
 * @package mediaRAVE
 * @author Alok Mishra <alok@mediarave.com>
 */


function tsn_staff_scripts_styles()
{
    // https://jquerymodal.com/
    // <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.js"></script>
    // <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.css" />

    wp_enqueue_style('jquery-modal-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.css');
    wp_enqueue_script('jquery-modal-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.js', array('jquery'), '0.9.2');
}
add_action('wp_enqueue_scripts', 'tsn_staff_scripts_styles');


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

add_action('genesis_entry_content', 'tsn_staff');
function tsn_staff()
{

    $staff = [];

    $teamterms = get_terms("team");

    foreach ($teamterms as $term) {

        // echo "<pre>"; echo $term->slug; echo "</pre>";

        $members = new WP_Query(array(
            'post_type' => 'staff',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'team',
                    'field'    => 'slug',
                    'terms'    => $term->slug,
                )
            ),
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ));


        if ($members->have_posts()) {

            while ($members->have_posts()) {
                $members->the_post();

                if (!isset($staff[$term->name])) {
                    $staff[$term->name] = [];
                }
                array_push($staff[$term->name], get_the_ID());
            }
            wp_reset_postdata();

            // ksort($team); // sort array by key
        }
    }

    // echo '<li><h3 class="text-6xl"><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h3></li>';

?>


    <?php if (!empty($staff)) {

        // echo "<pre>"; print_r($staff); echo "</pre>";

        foreach ($staff as $t => $team) { ?>

            <section class="team mb-20">
                <h3 class="title font-bold text-4xl text-green-600"><?php echo $t; ?></h3>


                <div class="members flex flex-wrap justify-center md:justify-start">

                    <?php foreach ($team as $m => $member) {

                        // strip invalid characters for anchors
                        // just spaces: /\s+/ , spaces and periods: /[ .]+/ , everything except letters and numbers /[^a-zA-Z\d]/
                        $modal_id = preg_replace('/[^a-zA-Z\d]/', '', ($t . $m));
                    ?>


                        <a href="#<?php echo $modal_id ?>" rel="modal:open">

                            <article class="member flex flex-col justify-end text-center text-white m-2 rounded-lg w-48 h-64 bg-black bg-center bg-no-repeat bg-cover shadow-lg border-green-600 border-2 border-solid" style="background-image: url(<?php echo get_the_post_thumbnail_url($member, 'medium'); ?>);">
                                <h4 class="name bg-green-600 bg-opacity-80 shadow-sm m-0 font-semibold text-lg whitespace-nowrap overflow-hidden text-"><?php echo get_the_title($member); ?></h4>
                                <h5 class="position bg-green-800 bg-opacity-80 m-0 whitespace-nowrap text-base"><?php echo get_field('position', $member); ?></h5>
                            </article>
                        </a>
                        <div id="<?php echo $modal_id ?>" class="modal">
                            <article class="profile container flex-col flex md:flex-row">
                                <aside class="flex-1 my-10 max-w-xs break-words">

                                    <div class="photo border-lime-600 border-2 border-solid">
                                        <img class="w-full" src="<?php echo get_the_post_thumbnail_url($member, 'large'); ?>" alt="">
                                    </div>

                                    <h4 class="font-bold text-3xl text-green-800 mt-4"><?php echo get_the_title($member); ?></h4>

                                    <h5 class="font-bold"><?php echo get_field('position', $member); ?></h5>

                                    <?php if (get_field('email', $member)) { ?>
                                        <h5 class="font-bold"><i class="fa fa-envelope text-lime-600 pr-2"></i><a href="mailto:<?php echo get_field('email', $member); ?>"><?php echo get_field('email', $member); ?></a></h5>
                                    <?php } ?>

                                    <?php if (get_field('phone', $member)) { ?>
                                        <h5><i class="fa fa-phone text-lime-600 pr-2"></i><a href="tel:<?php echo get_field('phone', $member); ?>"><?php echo get_field('phone', $member); ?></a></h5>
                                    <?php } ?>

                                    <?php if (get_field('website', $member)) { ?>
                                        <h5 class="underline"><i class="fa fa-globe text-lime-600 pr-2"></i><a target="_blank" href="<?php echo get_field('website', $member); ?>"><?php echo get_field('website', $member); ?></a></h5>
                                    <?php } ?>

                                </aside>

                                <div class="description flex-1 md:p-10">
                                    <?php echo get_post_field('post_content', $member); ?>
                                </div>
                            </article>
                        </div>


                    <?php } ?>
                </div>

            </section>

    <?php   }
    }

    ?>

    <style>
        .modal {
            max-width: 100%;
        }

        .modal a.close-modal {

            width: 40px;
            height: 40px;

            background-blend-mode: screen;
            border-radius: 20px;
            background-color: #65a30d;

        }

        /* #Michigan1 {
            display: inline-block;
        } */
    </style>

    <script>
        jQuery(document).ready(function($) {
            console.log('Go Team!');

            // $('#Michigan0').click();

        });
    </script>

<?php }





// echo '<ul class="presenters">';
// foreach ($presenters as $p) {
//     echo '<li><b><a title="' . sanitize_text_field(get_post_field('post_content', $p)) . '">' . get_the_title($p) . '</a></b><br/>' . get_field('organization', $p) . '</li>';
// }
// echo '</ul></li>';

// echo "<pre>"; print_r($presenters); echo "</pre>";
// array_push($posters, get_the_ID());


// add_filter('genesis_post_title_text', 'sisc_title');
// function sisc_title()
// {
//     return "Staff";
// }



?>



<?php

/*
add_action('genesis_after_header', 'sisc_menu');
function sisc_menu()
{
?>
    <section class="siscmenu flex p-4 bg-white border-b-4 border-green-400">
        <?php wp_nav_menu(array('menu' => 'sisc', 'container_class' => 'wrap')); ?>
    </section>
<?php
}
*/

//* Remove site header elements
// remove_action('genesis_header', 'genesis_header_markup_open', 5);
// remove_action('genesis_header', 'genesis_do_header');
// remove_action('genesis_header', 'genesis_header_markup_close', 15);

//* Remove navigation
// remove_action('genesis_before_header', 'genesis_do_nav');
// remove_action('genesis_footer', 'genesis_do_subnav', 7);

//* Remove breadcrumbs
// remove_action('genesis_before_loop', 'genesis_do_breadcrumbs');

//* Remove site footer widgets
// remove_action('genesis_before_footer', 'genesis_footer_widget_areas');

//* Remove site footer elements
// remove_action('genesis_footer', 'genesis_footer_markup_open', 5);
// remove_action('genesis_footer', 'genesis_do_footer');
// remove_action('genesis_footer', 'genesis_footer_markup_close', 15);

//* Run the Genesis loop
genesis();
