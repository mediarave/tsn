<?php

/**
 * This file adds the Landing template to the Education Pro Theme.
 *
 * @author Alok Mishra
 * @package mediaRAVE
 */

/*
Template Name: Spring Invasive Species
*/

?>

<style>
    .site-inner {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
    }

    .siscmenu .wrap {
        width: 100%;
    }

    .siscmenu .menu {
        display: flex;
        justify-content: center
    }

    .siscmenu .menu-item {
        margin-left: 40px;
        font-weight: bold;
        font-size: 1.6rem;
        padding-left: 6px;
        border-left: 4px solid transparent;
    }

    .siscmenu .menu-item:hover {
        color: #7ea63e;
    }

    .siscmenu .current-menu-item {
        border-bottom: 4px solid #b1bd3d;
    }

    @media (max-width: 1024px) {}

    @media (max-width: 768px) {
        .siscmenu .menu-item {
            font-size: 1rem;
        }

        .siscmenu .menu {
            flex-direction: column;
        }

        .siscmenu .current-menu-item {
            border: none;
            border-left: 4px solid #b1bd3d;
        }
    }
</style>

<?php

//* Force full width content layout
add_filter('genesis_site_layout', '__genesis_return_full_width_content');

add_filter('genesis_post_title_text', 'sisc_title');
function sisc_title()
{
    return "Spring Invasive Species Challenge";
}

add_action('genesis_after_header', 'sisc_menu');
function sisc_menu()
{
?>
    <section class="siscmenu flex p-4 bg-white border-b-4 border-green-400">
        <?php wp_nav_menu(array('menu' => 'sisc', 'container_class' => 'wrap')); ?>
    </section>
<?php
}


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
