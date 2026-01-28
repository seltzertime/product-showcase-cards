<?php
/**
 * Uninstall Product Showcase Cards Plugin
 *
 * Fired when the plugin is uninstalled.
 *
 * @package Product_Showcase_Cards
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all showcase posts and their meta
$showcases = get_posts(array(
    'post_type' => 'product_showcase',
    'posts_per_page' => -1,
    'post_status' => 'any'
));

foreach ($showcases as $showcase) {
    // Delete all post meta
    delete_post_meta($showcase->ID, '_psc_items_per_row');
    delete_post_meta($showcase->ID, '_psc_bg_color');
    delete_post_meta($showcase->ID, '_psc_text_color');
    delete_post_meta($showcase->ID, '_psc_hover_effect');
    delete_post_meta($showcase->ID, '_psc_title_font');
    delete_post_meta($showcase->ID, '_psc_title_font_weight');
    delete_post_meta($showcase->ID, '_psc_body_font');
    delete_post_meta($showcase->ID, '_psc_body_font_weight');
    delete_post_meta($showcase->ID, '_psc_enable_category_tabs');
    delete_post_meta($showcase->ID, '_psc_tab_color_inactive');
    delete_post_meta($showcase->ID, '_psc_tab_color_hover');
    delete_post_meta($showcase->ID, '_psc_tab_color_active');
    delete_post_meta($showcase->ID, '_psc_tab_underline_hover');
    delete_post_meta($showcase->ID, '_psc_tab_underline_active');
    delete_post_meta($showcase->ID, '_psc_display_categories');
    delete_post_meta($showcase->ID, '_psc_items');

    // Delete the post
    wp_delete_post($showcase->ID, true);
}

// Clear any cached data that has been removed
wp_cache_flush();
